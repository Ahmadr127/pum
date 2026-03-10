<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PumRequest;
use App\Models\PumApprovalStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * PUM Approval API — Pending lists, Approve, Reject
 *
 * Routes:
 *   GET  /api/pum/requests/pending-approvals  – Requests awaiting approval from me
 *   GET  /api/pum/requests/pending-releases   – Requests awaiting release from me
 *   POST /api/pum/requests/{id}/approve       – Approve current step
 *   POST /api/pum/requests/{id}/reject        – Reject current step
 */
class PumApprovalApiController extends Controller
{
    /**
     * GET /api/pum/requests/pending-approvals
     * Requests with approval-type steps waiting for the current user.
     */
    public function pendingApprovals(Request $request)
    {
        $user = Auth::user();

        $allRequests = PumRequest::with(['requester', 'workflow.steps', 'approvals.step', 'approvals.approver'])
            ->whereIn('status', [PumRequest::STATUS_PENDING, PumRequest::STATUS_APPROVED])
            ->whereHas('approvals', function ($q) use ($user) {
                $q->where(fn($s) => $s->where('status', 'pending')->orWhere('approver_id', $user->id));
            })
            ->search($request->search)
            ->byDateRange($request->date_from, $request->date_to)
            ->get();

        $filtered = $allRequests->filter(function ($pumRequest) use ($user) {
            $currentApproval = $pumRequest->getCurrentApproval();
            $isApprovalStep  = $currentApproval
                && $currentApproval->step
                && $currentApproval->step->type !== PumApprovalStep::TYPE_RELEASE
                && $pumRequest->canBeApprovedBy($user);

            $hasActioned = $pumRequest->approvals->contains(function ($a) use ($user) {
                return (int) $a->approver_id === (int) $user->id
                    && in_array($a->status, ['approved', 'rejected'])
                    && $a->step
                    && $a->step->type !== PumApprovalStep::TYPE_RELEASE;
            });

            return $isApprovalStep || $hasActioned;
        })->values();

        $summary = [
            'pending'  => $filtered->where('status', 'pending')->count(),
            'approved' => $filtered->where('status', 'approved')->count(),
            'rejected' => $filtered->where('status', 'rejected')->count(),
        ];

        return response()->json([
            'status'  => 'success',
            'summary' => $summary,
            'data'    => $this->paginate($filtered, $request),
        ]);
    }

    /**
     * GET /api/pum/requests/pending-releases
     * Requests with release-type steps waiting for the current user.
     */
    public function pendingReleases(Request $request)
    {
        $user = Auth::user();

        $allRequests = PumRequest::with(['requester', 'workflow.steps', 'approvals.step', 'approvals.approver'])
            ->whereIn('status', [PumRequest::STATUS_PENDING, PumRequest::STATUS_APPROVED])
            ->whereHas('approvals', function ($q) use ($user) {
                $q->whereHas('step', fn($s) => $s->where('type', PumApprovalStep::TYPE_RELEASE))
                  ->where(fn($s) => $s->where('status', 'pending')->orWhere('approver_id', $user->id));
            })
            ->search($request->search)
            ->byDateRange($request->date_from, $request->date_to)
            ->get();

        $filtered = $allRequests->filter(function ($pumRequest) use ($user) {
            $currentApproval  = $pumRequest->getCurrentApproval();
            $isCurrentRelease = $currentApproval
                && $currentApproval->step
                && $currentApproval->step->type === PumApprovalStep::TYPE_RELEASE
                && $pumRequest->canBeApprovedBy($user);

            $hasActionedRelease = $pumRequest->approvals->contains(function ($a) use ($user) {
                return (int) $a->approver_id === (int) $user->id
                    && in_array($a->status, ['approved', 'rejected'])
                    && $a->step
                    && $a->step->type === PumApprovalStep::TYPE_RELEASE;
            });

            return $isCurrentRelease || $hasActionedRelease;
        })->values();

        return response()->json([
            'status' => 'success',
            'data'   => $this->paginate($filtered, $request),
        ]);
    }

    /**
     * POST /api/pum/requests/{id}/approve
     * Approve the current pending step on a PUM request.
     * Body: { "notes": "optional string" }
     */
    public function approve(Request $request, PumRequest $pumRequest)
    {
        $request->validate(['notes' => 'nullable|string|max:500']);

        try {
            $pumRequest->approve(Auth::user(), $request->notes);
            $pumRequest->load(['requester', 'workflow', 'approvals.step', 'approvals.approver']);
            return response()->json([
                'status'  => 'success',
                'message' => 'Permintaan berhasil disetujui.',
                'data'    => $pumRequest,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * POST /api/pum/requests/{id}/reject
     * Reject the current pending step on a PUM request.
     * Body: { "notes": "required string" }
     */
    public function reject(Request $request, PumRequest $pumRequest)
    {
        $request->validate(['notes' => 'required|string|max:500']);

        try {
            $pumRequest->reject(Auth::user(), $request->notes);
            $pumRequest->load(['requester', 'workflow', 'approvals.step', 'approvals.approver']);
            return response()->json([
                'status'  => 'success',
                'message' => 'Permintaan berhasil ditolak.',
                'data'    => $pumRequest,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    private function paginate(\Illuminate\Support\Collection $items, Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $page    = $request->get('page', 1);
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }
}
