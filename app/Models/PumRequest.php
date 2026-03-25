<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class PumRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'no_surat',
        'requester_id',
        'request_date',
        'amount',
        'description',
        'attachments',
        'attachments2',
        'status',
        'workflow_id',
        'current_step_order',
        'created_by',
    ];

    protected $casts = [
        'request_date' => 'date',
        'amount' => 'decimal:2',
        'attachments' => 'array',
        'attachments2' => 'array',
        'requester_id' => 'integer',
        'created_by' => 'integer',
        'workflow_id' => 'integer',
        'current_step_order' => 'integer',
    ];

    const STATUS_NEW = 'new';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_FULFILLED = 'fulfilled';

    /**
     * Status labels in Indonesian
     */
    public static function getStatusLabels()
    {
        return [
            self::STATUS_NEW => 'Baru',
            self::STATUS_PENDING => 'Menunggu Persetujuan',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_FULFILLED => 'Terpenuhi',
        ];
    }

    /**
     * Status colors for UI
     */
    public static function getStatusColors()
    {
        return [
            self::STATUS_NEW => 'yellow',
            self::STATUS_PENDING => 'blue',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_FULFILLED => 'emerald',
        ];
    }

    /**
     * Get the requester
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Get the creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the workflow
     */
    public function workflow()
    {
        return $this->belongsTo(PumApprovalWorkflow::class, 'workflow_id');
    }

    /**
     * Get all approval records
     */
    public function approvals()
    {
        return $this->hasMany(PumRequestApproval::class, 'request_id')->orderBy('step_order');
    }

    /**
     * Generate unique code for new request
     */
    public static function generateCode($user = null)
    {
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        
        // Get Unit Code
        $unitCode = 'PNJ'; // Default fallback
        if ($user && $user->loadMissing('organizationUnit') && $user->organizationUnit) {
            $unitCode = strtoupper($user->organizationUnit->code);
        }
        
        // Get the last code for this month
        $lastRequest = static::withTrashed()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastRequest && preg_match('/^(\d+)\//', $lastRequest->code, $matches)) {
            $sequence = intval($matches[1]) + 1;
        } else {
            $sequence = 1;
        }
        
        return sprintf('%05d/RSAZRA/%s/%s/%s', $sequence, $unitCode, $month, $year);
    }

    /**
     * Get current approval step
     */
    public function getCurrentStep()
    {
        if (!$this->workflow_id || $this->current_step_order === null) {
            return null;
        }

        return PumApprovalStep::where('workflow_id', $this->workflow_id)
            ->where('order', $this->current_step_order)
            ->first();
    }

    /**
     * Get current pending approval
     */
    public function getCurrentApproval()
    {
        return $this->approvals()
            ->where('status', 'pending')
            ->orderBy('step_order')
            ->first();
    }

    /**
     * Check if user can approve this request
     */
    public function canBeApprovedBy(User $user)
    {
        // Cannot approve if status is not pending, new, or approved (for release)
        if (!in_array($this->status, [self::STATUS_NEW, self::STATUS_PENDING, self::STATUS_APPROVED])) {
            return false;
        }

        // Cannot approve own request
        if ($this->requester_id === $user->id || $this->created_by === $user->id) {
            return false;
        }

        $currentApproval = $this->getCurrentApproval();
        if (!$currentApproval) {
            return false;
        }

        // Check if user already approved this request in any previous step
        // Exception: release steps can be actioned by someone who already approved a regular step
        $isCurrentStepRelease = $currentApproval->step 
            && $currentApproval->step->type === \App\Models\PumApprovalStep::TYPE_RELEASE;

        if (!$isCurrentStepRelease) {
            $hasApprovedBefore = $this->approvals()
                ->where('approver_id', $user->id)
                ->where('status', 'approved')
                ->exists();

            if ($hasApprovedBefore) {
                return false; // User cannot approve the same request twice (only for non-release steps)
            }
        }

        // Check if user is eligible for current step
        return $currentApproval->step->canBeApprovedBy($user, $this->requester);
    }

    /**
     * Submit request for approval
     */
    public function submitForApproval()
    {
        $workflow = null;
        
        if ($this->workflow_id) {
            $workflow = $this->workflow;
        } else {
            $workflowService = app(\App\Services\WorkflowSelectionService::class);
            $workflow = $workflowService->getMatchingWorkflow($this->amount);
        }
        
        if (!$workflow) {
            throw new \Exception('Tidak ada workflow approval yang cocok untuk nominal Rp ' . number_format($this->amount, 0, ',', '.'));
        }

        $this->update([
            'workflow_id' => $workflow->id,
            'status' => self::STATUS_PENDING,
            'current_step_order' => 1,
        ]);

        // Create approval records for each step
        foreach ($workflow->steps as $step) {
            PumRequestApproval::create([
                'request_id' => $this->id,
                'step_id' => $step->id,
                'step_order' => $step->order,
                'status' => 'pending',
            ]);
        }

        // AUTO-APPROVE LOGIC
        // If the requester is the designated approver for the current step(s), auto-approve it.
        while ($currentApproval = $this->getCurrentApproval()) {
            $approvers = $currentApproval->step->getApprovers($this->requester);
            
            // If the requester is among the eligible approvers for this step
            if ($approvers->contains('id', $this->requester_id)) {
                // Auto-approve the step
                $currentApproval->update([
                    'approver_id' => $this->requester_id,
                    'status' => 'approved',
                    'notes' => 'Auto-approved by system (Pemohon adalah approver)',
                    'responded_at' => now(),
                ]);
                
                // Move to next step
                $nextApproval = $this->approvals()
                    ->where('step_order', '>', $currentApproval->step_order)
                    ->where('status', 'pending')
                    ->orderBy('step_order')
                    ->first();

                if ($nextApproval) {
                    $updateData = ['current_step_order' => $nextApproval->step_order];
                    if ($currentApproval->step->type === \App\Models\PumApprovalStep::TYPE_APPROVAL && $nextApproval->step->type === \App\Models\PumApprovalStep::TYPE_RELEASE) {
                        $updateData['status'] = self::STATUS_APPROVED;
                    }
                    $this->update($updateData);
                } else {
                    // All steps approved
                    if ($currentApproval->step->type === PumApprovalStep::TYPE_RELEASE) {
                        $this->update([
                            'status' => self::STATUS_FULFILLED,
                            'current_step_order' => null,
                        ]);
                    } else {
                        $this->update([
                            'status' => self::STATUS_APPROVED,
                            'current_step_order' => null,
                        ]);
                    }
                    break;
                }
            } else {
                // The requester is not the approver for this step, stop auto-approving
                break;
            }
        }

        return $this;
    }

    /**
     * Approve current step
     */
    public function approve(User $approver, ?string $notes = null)
    {
        $currentApproval = $this->getCurrentApproval();
        
        if (!$currentApproval) {
            throw new \Exception('Tidak ada approval yang pending');
        }

        if (!$currentApproval->step->canBeApprovedBy($approver, $this->requester)) {
            throw new \Exception('Anda tidak memiliki hak untuk menyetujui permintaan ini');
        }

        // Update current approval
        $currentApproval->update([
            'approver_id' => $approver->id,
            'status' => 'approved',
            'notes' => $notes,
            'responded_at' => now(),
        ]);

        // Check if there's a next step
        $nextApproval = $this->approvals()
            ->where('step_order', '>', $currentApproval->step_order)
            ->where('status', 'pending')
            ->orderBy('step_order')
            ->first();

        if ($nextApproval) {
            // Move to next step
            $updateData = ['current_step_order' => $nextApproval->step_order];
            if ($currentApproval->step->type === \App\Models\PumApprovalStep::TYPE_APPROVAL && $nextApproval->step->type === \App\Models\PumApprovalStep::TYPE_RELEASE) {
                $updateData['status'] = self::STATUS_APPROVED;
            }
            $this->update($updateData);
        } else {
            // All steps approved
            // Check if the last step was a 'release' step
            if ($currentApproval->step->type === PumApprovalStep::TYPE_RELEASE) {
                $this->update([
                    'status' => self::STATUS_FULFILLED,
                    'current_step_order' => null,
                ]);
            } else {
                $this->update([
                    'status' => self::STATUS_APPROVED,
                    'current_step_order' => null,
                ]);
            }
        }

        return $this;
    }

    /**
     * Reject request
     */
    public function reject(User $approver, ?string $notes = null)
    {
        $currentApproval = $this->getCurrentApproval();
        
        if (!$currentApproval) {
            throw new \Exception('Tidak ada approval yang pending');
        }

        if (!$currentApproval->step->canBeApprovedBy($approver, $this->requester)) {
            throw new \Exception('Anda tidak memiliki hak untuk menolak permintaan ini');
        }

        // Update current approval
        $currentApproval->update([
            'approver_id' => $approver->id,
            'status' => 'rejected',
            'notes' => $notes,
            'responded_at' => now(),
        ]);

        // Update request status
        $this->update([
            'status' => self::STATUS_REJECTED,
            'current_step_order' => null,
        ]);

        return $this;
    }

    /**
     * Mark as fulfilled
     */
    public function markAsFulfilled()
    {
        if ($this->status !== self::STATUS_APPROVED) {
            throw new \Exception('Hanya permintaan yang sudah disetujui yang bisa ditandai terpenuhi');
        }

        $this->update(['status' => self::STATUS_FULFILLED]);
        return $this;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return self::getStatusLabels()[$this->status] ?? $this->status;
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        return self::getStatusColors()[$this->status] ?? 'gray';
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope by date range
     */
    public function scopeByDateRange($query, $from, $to)
    {
        if ($from) {
            $query->whereDate('request_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('request_date', '<=', $to);
        }
        return $query;
    }

    /**
     * Scope by requester
     */
    public function scopeByRequester($query, $requesterId)
    {
        if ($requesterId) {
            return $query->where('requester_id', $requesterId);
        }
        return $query;
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('requester', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        return $query;
    }

    /**
     * Scope for requests that need approval from a specific user
     */
    public function scopeNeedsApprovalFrom($query, User $user)
    {
        return $query->whereIn('status', [self::STATUS_NEW, self::STATUS_PENDING, self::STATUS_APPROVED])
            ->whereHas('approvals', function ($q) use ($user) {
                $q->where('status', 'pending')
                  ->where(function ($approvalQuery) use ($user) {
                      // Option 1: Role or User based step
                      $approvalQuery->whereHas('step', function ($sq) use ($user) {
                          $sq->where(function ($ssq) use ($user) {
                              $ssq->where('approver_type', 'role')
                                  ->where('role_id', $user->role_id);
                          })->orWhere(function ($ssq) use ($user) {
                              $ssq->where('approver_type', 'user')
                                  ->where('user_id', $user->id);
                          });
                      })
                      // Option 2: Organization Head based step
                      ->orWhere(function ($approvalQuery2) use ($user) {
                          $approvalQuery2->whereHas('step', function ($sq) {
                              $sq->where('approver_type', 'organization_head');
                          })->whereHas('request.requester.organizationUnit', function ($uq) use ($user) {
                              $uq->where('head_id', $user->id);
                          });
                      });
                  });
            });
    }




}
