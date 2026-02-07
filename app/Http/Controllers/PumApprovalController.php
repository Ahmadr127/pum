<?php

namespace App\Http\Controllers;

use App\Models\PumRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                // Include pending requests
                $q->where('status', PumRequest::STATUS_PENDING)
                  // Include approved requests
                  ->orWhere('status', PumRequest::STATUS_APPROVED)
                  // Include rejected requests
                  ->orWhere('status', PumRequest::STATUS_REJECTED)
                  // Include fulfilled requests
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
        $allRequests = $query->get();

        // Filter to show only requests where user is eligible to approve OR has already approved
        $requests = $allRequests->filter(function ($pumRequest) use ($user) {
            // Show if user can approve current step
            if ($pumRequest->canBeApprovedBy($user)) {
                return true;
            }
            
            // Show if user has already approved/rejected this request
            $hasActioned = $pumRequest->approvals()
                ->where('approver_id', $user->id)
                ->whereIn('status', ['approved', 'rejected'])
                ->exists();
            
            return $hasActioned;
        });

        // Calculate summary counts from filtered request
        $summary = [
            'new' => $requests->where('status', 'new')->count(),
            'pending' => $requests->where('status', 'pending')->count(),
            'approved' => $requests->where('status', 'approved')->count(),
            'rejected' => $requests->where('status', 'rejected')->count(),
            'fulfilled' => $requests->where('status', 'fulfilled')->count(),
        ];

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
