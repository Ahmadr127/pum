<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PumApprovalStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'order',
        'name',
        'approver_type',
        'role_id',
        'user_id',
        'is_required',
        'is_upload_fs_required',
        'type', // Added type
    ];

    const TYPE_APPROVAL = 'approval';
    const TYPE_RELEASE = 'release';

    protected $casts = [
        'is_required' => 'boolean',
        'is_upload_fs_required' => 'boolean',
    ];

    const TYPE_ROLE = 'role';
    const TYPE_USER = 'user';
    const TYPE_ORGANIZATION_HEAD = 'organization_head';

    /**
     * Get the workflow this step belongs to
     */
    public function workflow()
    {
        return $this->belongsTo(PumApprovalWorkflow::class, 'workflow_id');
    }

    /**
     * Get the role for this step (if type is role)
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the specific user for this step (if type is user)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the approver(s) for this step based on the requester
     * Returns a collection of users who can approve
     */
    public function getApprovers(?User $requester = null)
    {
        switch ($this->approver_type) {
            case self::TYPE_ROLE:
                return User::where('role_id', $this->role_id)->get();
            
            case self::TYPE_USER:
                return $this->user ? collect([$this->user]) : collect();
            
            case self::TYPE_ORGANIZATION_HEAD:
                if ($requester && $requester->organizationUnit) {
                    $head = $requester->organizationUnit->head;
                    return $head ? collect([$head]) : collect();
                }
                return collect();
            
            default:
                return collect();
        }
    }

    /**
     * Check if a user can approve this step
     */
    public function canBeApprovedBy(User $user, ?User $requester = null)
    {
        return $this->getApprovers($requester)->contains('id', $user->id);
    }

    /**
     * Get human-readable approver description
     */
    public function getApproverDescriptionAttribute()
    {
        switch ($this->approver_type) {
            case self::TYPE_ROLE:
                return $this->role ? "Role: {$this->role->display_name}" : 'Role: (tidak ditemukan)';
            
            case self::TYPE_USER:
                return $this->user ? "User: {$this->user->name}" : 'User: (tidak ditemukan)';
            
            case self::TYPE_ORGANIZATION_HEAD:
                return 'Kepala Unit Organisasi';
            
            default:
                return 'Tidak diketahui';
        }
    }
}
