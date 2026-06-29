<?php

namespace App\Http\Controllers;

use App\Models\PumRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PumApprovalController extends Controller
{
    /**
     * Display pending approvals for current user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get ALL requests where user is/was involved in approval process
        // This includes: pending approvals + already approved by this user
        $query = PumRequest::with(['requester', 'workflow.steps', 'approvals.step', 'approvals.approver'])
            ->where(function ($q) {
                // Include all relevant statuses
                $q->where('status', PumRequest::STATUS_PENDING)
                  ->orWhere('status', PumRequest::STATUS_APPROVED)
                  ->orWhere('status', PumRequest::STATUS_REJECTED)
                  ->orWhere('status', PumRequest::STATUS_FULFILLED);
            })
            ->whereHas('approvals', function ($q) use ($user) {
                // Show requests where:
                // 1. User can approve (pending step)
                // 2. User already approved
                // 3. User already rejected
                $q->where(function ($subQ) use ($user) {
                    $subQ->where('status', 'pending')
                         ->orWhere('approver_id', $user->id);
                });
            });

        // Apply filters
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', "%{$request->search}%")
                  ->orWhereHas('requester', function ($q) use ($request) {
                      $q->where('name', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->date_from) {
            $query->whereDate('request_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('request_date', '<=', $request->date_to);
        }

        // Get all matching requests
        $allRequests = $query->orderBy('created_at', 'desc')->get();
        
        Log::debug('[PumApproval] user=' . $user->id . ' allRequests=' . $allRequests->count());

        // Filter to show only requests where user is eligible to approve OR has already approved (for APPROVAL steps only)
        $requests = $allRequests->filter(function ($pumRequest) use ($user) {
            // Show if user can approve current step AND current step is not a 'release' type
            $currentApproval = $pumRequest->getCurrentApproval();
            $isApprovalStepVisible = $currentApproval 
                && $currentApproval->step
                && $currentApproval->step->type !== \App\Models\PumApprovalStep::TYPE_RELEASE
                && $pumRequest->canBeApprovedBy($user);
            
            // Show if user has already actioned on an 'approval' type step (with null check)
            // Use (int) cast to avoid === strict type mismatch between string DB value and int $user->id
            $hasActionedApproval = $pumRequest->approvals->contains(function ($approval) use ($user) {
                return (int) $approval->approver_id === (int) $user->id
                    && in_array($approval->status, ['approved', 'rejected'])
                    && $approval->step
                    && $approval->step->type !== \App\Models\PumApprovalStep::TYPE_RELEASE;
            });
            
            Log::debug('[PumApproval] req#' . $pumRequest->id . ' status=' . $pumRequest->status
                . ' isVisible=' . ($isApprovalStepVisible ? 'yes' : 'no')
                . ' hasActioned=' . ($hasActionedApproval ? 'yes' : 'no')
                . ' approvals=' . $pumRequest->approvals->pluck('approver_id', 'step_order')->toJson());
            
            return $isApprovalStepVisible || $hasActionedApproval;
        });
        
        Log::debug('[PumApproval] filtered=' . $requests->count() . ' ids=' . $requests->pluck('id')->join(','));

        // Calculate summary counts based on user action status on the full list of eligible requests
        $userRejectedCount = 0;
        $userPendingCount = 0;
        $userApprovedCount = 0;

        foreach ($requests as $r) {
            $userApproval = $r->approvals->where('approver_id', $user->id)->first();
            $hasActioned = $userApproval && in_array($userApproval->status, ['approved', 'rejected']);
            if ($hasActioned) {
                if ($userApproval->status === 'approved') {
                    $userApprovedCount++;
                } else {
                    $userRejectedCount++;
                }
            } else {
                $userPendingCount++;
            }
        }

        $summary = [
            'rejected' => $userRejectedCount,
            'pending' => $userPendingCount,
            'approved' => $userApprovedCount,
        ];

        // Filter the collection by status if requested
        $statusFilter = $request->get('status');
        if (in_array($statusFilter, ['approved', 'rejected', 'pending'])) {
            $requests = $requests->filter(function ($r) use ($user, $statusFilter) {
                $userApproval = $r->approvals->where('approver_id', $user->id)->first();
                $hasActioned = $userApproval && in_array($userApproval->status, ['approved', 'rejected']);
                
                if ($statusFilter === 'approved') {
                    return $hasActioned && $userApproval->status === 'approved';
                } elseif ($statusFilter === 'rejected') {
                    return $hasActioned && $userApproval->status === 'rejected';
                } elseif ($statusFilter === 'pending') {
                    return !$hasActioned;
                }
                return true;
            });
        }

        // Manual pagination for filtered collection
        $page = $request->get('page', 1);
        $perPage = 15;
        $total = $requests->count();
        $paginatedRequests = new \Illuminate\Pagination\LengthAwarePaginator(
            $requests->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('pum.approvals.index', [
            'requests' => $paginatedRequests,
            'summary' => $summary
        ]);
    }
}
