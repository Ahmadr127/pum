<?php

namespace App\Http\Controllers;

use App\Models\PumRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PumReleaseController extends Controller
{
    /**
     * Display pending releases for current user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = PumRequest::with(['requester', 'workflow.steps', 'approvals.step', 'approvals.approver'])
            ->where(function ($q) {
                $q->where('status', PumRequest::STATUS_PENDING)
                  ->orWhere('status', PumRequest::STATUS_APPROVED)
                  ->orWhere('status', PumRequest::STATUS_REJECTED)
                  ->orWhere('status', PumRequest::STATUS_FULFILLED);
            })
            ->whereHas('approvals', function ($q) use ($user) {
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

        $allRequests = $query->get();

        $requests = $allRequests->filter(function ($pumRequest) use ($user) {
            $isReleaseStepVisible = false;

            $currentApproval = $pumRequest->getCurrentApproval();
            if ($currentApproval && $currentApproval->step->type === \App\Models\PumApprovalStep::TYPE_RELEASE) {
                if ($pumRequest->canBeApprovedBy($user)) {
                    $isReleaseStepVisible = true;
                }
            }
            
            $hasActionedRelease = $pumRequest->approvals->contains(function ($approval) use ($user) {
                return $approval->approver_id === $user->id 
                    && in_array($approval->status, ['approved', 'rejected'])
                    && $approval->step->type === \App\Models\PumApprovalStep::TYPE_RELEASE;
            });
            
            return $isReleaseStepVisible || $hasActionedRelease;
        });

        $summary = [
            'new' => $requests->where('status', 'new')->count(),
            'pending' => $requests->where('status', 'pending')->count(),
            'approved' => $requests->where('status', 'approved')->count(),
            'rejected' => $requests->where('status', 'rejected')->count(),
            'fulfilled' => $requests->where('status', 'fulfilled')->count(),
        ];

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

        return view('pum.releases.index', [
            'requests' => $paginatedRequests,
            'summary' => $summary
        ]);
    }
}
