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
     * Requests with approval-type steps that the user is involved in (pending, approved, or already actioned).
     * Shows all statuses EXCEPT 'new' and 'rejected'.
     */
    public function pendingApprovals(Request $request)
    {
        $user = Auth::user();

        $allRequests = PumRequest::with(['requester', 'workflow.steps', 'approvals.step', 'approvals.approver'])
            ->whereIn('status', [PumRequest::STATUS_PENDING, PumRequest::STATUS_APPROVED, PumRequest::STATUS_FULFILLED])
            ->whereHas('approvals', function ($q) use ($user) {
                $q->where(fn($s) => $s->where('status', 'pending')->orWhere('approver_id', $user->id));
            })
            ->search($request->search)
            ->byDateRange($request->date_from, $request->date_to)
            ->get();

        $filtered = $allRequests->map(function ($pumRequest) use ($user) {
            $currentApproval = $pumRequest->getCurrentApproval();
            $isApprovalStep  = $currentApproval
                && $currentApproval->step
                && $currentApproval->step->type !== PumApprovalStep::TYPE_RELEASE
                && $pumRequest->canBeApprovedBy($user);

            $myAction = $pumRequest->approvals->filter(function ($a) use ($user) {
                return (int) $a->approver_id === (int) $user->id
                    && in_array($a->status, ['approved', 'rejected'])
                    && $a->step
                    && $a->step->type !== PumApprovalStep::TYPE_RELEASE;
            })->last();

            $hasActioned = $myAction !== null;

            if (!$isApprovalStep && !$hasActioned) {
                return null;
            }

            $myStatus = $isApprovalStep ? 'pending' : $myAction->status;
            
            $pumRequest->status = $myStatus;
            $pumRequest->status_label = \App\Models\PumRequest::getStatusLabels()[$myStatus] ?? $myStatus;

            return $pumRequest;
        })->filter();

        if ($request->filled('status') && $request->status !== 'all') {
            $filtered = $filtered->where('status', $request->status);
        }

        $filtered = $filtered->values();

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
     * Requests with release-type steps that the user is involved in (pending, approved, or already actioned).
     * Shows all statuses EXCEPT 'new' and 'rejected'.
     */
    public function pendingReleases(Request $request)
    {
        $user = Auth::user();

        $allRequests = PumRequest::with(['requester', 'workflow.steps', 'approvals.step', 'approvals.approver'])
            ->whereIn('status', [PumRequest::STATUS_PENDING, PumRequest::STATUS_APPROVED, PumRequest::STATUS_FULFILLED])
            ->whereHas('approvals', function ($q) use ($user) {
                $q->whereHas('step', fn($s) => $s->where('type', PumApprovalStep::TYPE_RELEASE))
                  ->where(fn($s) => $s->where('status', 'pending')->orWhere('approver_id', $user->id));
            })
            ->search($request->search)
            ->byDateRange($request->date_from, $request->date_to)
            ->get();

        $filtered = $allRequests->map(function ($pumRequest) use ($user) {
            $currentApproval  = $pumRequest->getCurrentApproval();
            $isCurrentRelease = $currentApproval
                && $currentApproval->step
                && $currentApproval->step->type === PumApprovalStep::TYPE_RELEASE
                && $pumRequest->canBeApprovedBy($user);

            $myAction = $pumRequest->approvals->filter(function ($a) use ($user) {
                return (int) $a->approver_id === (int) $user->id
                    && in_array($a->status, ['approved', 'rejected'])
                    && $a->step
                    && $a->step->type === PumApprovalStep::TYPE_RELEASE;
            })->last();

            $hasActionedRelease = $myAction !== null;

            if (!$isCurrentRelease && !$hasActionedRelease) {
                return null;
            }

            $myStatus = $isCurrentRelease ? 'pending' : $myAction->status;
            
            $pumRequest->status = $myStatus;
            $pumRequest->status_label = \App\Models\PumRequest::getStatusLabels()[$myStatus] ?? $myStatus;

            return $pumRequest;
        })->filter();

        if ($request->filled('status') && $request->status !== 'all') {
            $filtered = $filtered->where('status', $request->status);
        }

        $filtered = $filtered->values();

        return response()->json([
            'status' => 'success',
            'data'   => $this->paginate($filtered, $request),
        ]);
    }

    /**
     * POST /api/pum/requests/{id}/approve
     * Approve the current pending step on a PUM request.
     * Body: { "notes": "optional string", "fs_document": "optional file" }
     */
    public function approve(Request $request, PumRequest $pumRequest)
    {
        $currentApproval = $pumRequest->getCurrentApproval();
        if (!$currentApproval) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada step yang perlu di-approve.'], 422);
        }

        $needsFsUpload = $currentApproval->step->is_upload_fs_required ?? false;

        $rules = ['notes' => 'nullable|string|max:500'];
        if ($needsFsUpload) {
            $rules['fs_document'] = 'required|file|mimes:pdf,doc,docx|max:5120';
        }

        $request->validate($rules);

        try {
            if ($needsFsUpload && $request->hasFile('fs_document')) {
                $file = $request->file('fs_document');
                $filename = time() . '_FS_' . $file->getClientOriginalName();
                $path = $file->storeAs('public/pum-attachments', $filename);
                // Also save it inside attachments2 which is intended for added items
                $attachments2 = $pumRequest->attachments2 ?? [];
                $attachments2[] = $filename;
                $pumRequest->update(['attachments2' => $attachments2]);
            }

            $pumRequest->approve(Auth::user(), $request->notes);
            $pumRequest->load(['requester', 'workflow', 'approvals.step', 'approvals.approver']);

            // Notification Hook
            $notificationService = app(\App\Services\NotificationService::class);
            if ($pumRequest->status === \App\Models\PumRequest::STATUS_FULFILLED || $pumRequest->status === \App\Models\PumRequest::STATUS_APPROVED) {
                // If the next step is release, but it moved to approved status, we notify next releasers.
                // Or if it's completely fulfilled, notify requester.
                if ($pumRequest->getCurrentApproval()) {
                    $notificationService->notifyApprovers($pumRequest);
                } else {
                    $notificationService->notifyRequesterApproved($pumRequest);
                }
            } else {
                // Still in pending, notify next approver
                $notificationService->notifyApprovers($pumRequest);
            }

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

            // Notification Hook: Notify Requester about rejection
            app(\App\Services\NotificationService::class)->notifyRequesterRejected($pumRequest, $request->notes);

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
