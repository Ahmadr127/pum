<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PumRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * PUM Request API — Read, Create, Submit
 *
 * Routes:
 *   GET  /api/pum/requests              – All requests (filterable, admin)
 *   GET  /api/pum/requests/mine         – Own requests
 *   GET  /api/pum/requests/{id}         – Single request detail
 *   POST /api/pum/requests/{id}/submit  – Submit to workflow
 */
class PumRequestApiController extends Controller
{
    /**
     * GET /api/pum/requests
     * All PUM Requests. Supports ?search=, ?status=, ?date_from=, ?date_to=, ?requester_id=, ?per_page=
     */
    public function index(Request $request)
    {
        $query = PumRequest::with(['requester', 'workflow', 'approvals.approver', 'approvals.step'])
            ->search($request->search)
            ->byStatus($request->status)
            ->byDateRange($request->date_from, $request->date_to)
            ->byRequester($request->requester_id);

        if ($request->amount_min) $query->where('amount', '>=', $request->amount_min);
        if ($request->amount_max) $query->where('amount', '<=', $request->amount_max);

        $requests = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json(['status' => 'success', 'data' => $requests]);
    }

    /**
     * GET /api/pum/requests/mine
     * PUM requests belonging to the authenticated user.
     */
    public function myRequests(Request $request)
    {
        $requests = PumRequest::with(['requester', 'workflow', 'approvals.approver', 'approvals.step'])
            ->where('requester_id', Auth::id())
            ->search($request->search)
            ->byStatus($request->status)
            ->byDateRange($request->date_from, $request->date_to)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json(['status' => 'success', 'data' => $requests]);
    }

    /**
     * GET /api/pum/requests/{id}
     * Detail of a single PUM Request including full approval history.
     */
    public function show(PumRequest $pumRequest)
    {
        $pumRequest->load(['requester', 'creator', 'workflow.steps', 'approvals.step', 'approvals.approver']);

        $user            = Auth::user();
        $currentApproval = $pumRequest->getCurrentApproval();
        $currentStep     = null;

        if ($currentApproval?->step) {
            $currentStep = [
                'id'                  => $currentApproval->id,
                'step_name'           => $currentApproval->step->name ?? '-',
                'step_type'           => $currentApproval->step->type,
                'approver_type'       => $currentApproval->step->approver_type,
                'status'              => $currentApproval->status,
                'needs_fs_upload'     => $currentApproval->step->is_upload_fs_required ?? false,
                'allow_amount_change' => $currentApproval->step->allow_amount_change ?? false,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'           => $pumRequest->id,
                'code'         => $pumRequest->code,
                'no_surat'     => $pumRequest->no_surat,
                'requester'    => $pumRequest->requester,
                'request_date' => $pumRequest->request_date,
                'amount'       => $pumRequest->amount,
                'description'  => $pumRequest->description,
                'status'       => $pumRequest->status,
                'status_label' => $pumRequest->status_label,
                'workflow'     => $pumRequest->workflow,
                'approvals'    => $pumRequest->approvals->map(fn($a) => [
                    'id'           => $a->id,
                    'step_order'   => $a->step_order,
                    'step_type'    => $a->step->type ?? null,
                    'step_name'    => $a->step->name ?? '-',
                    'approver'     => $a->approver,
                    'status'       => $a->status,
                    'notes'        => $a->notes,
                    'responded_at' => $a->responded_at,
                ]),
                'current_step'   => $currentStep,
                'can_approve'    => $pumRequest->canBeApprovedBy($user),
                'can_submit'     => $pumRequest->status === PumRequest::STATUS_NEW
                                    && ($user->hasPermission('manage_pum') || $pumRequest->requester_id === $user->id),
                'attachments'    => $pumRequest->attachments,
                'attachments2'   => $pumRequest->attachments2,
                'print_url'      => route('pum-requests.print', $pumRequest),
                'created_at'     => $pumRequest->created_at,
            ],
        ]);
    }

    /**
     * POST /api/pum/requests/{id}/submit
     * Submit a PUM request into the approval workflow.
     */
    public function submit(PumRequest $pumRequest)
    {
        $user = Auth::user();

        if (!$user->hasPermission('manage_pum') && $pumRequest->requester_id !== $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Tidak memiliki hak akses.'], 403);
        }

        if ($pumRequest->status !== PumRequest::STATUS_NEW) {
            return response()->json(['status' => 'error', 'message' => 'Permintaan sudah diajukan sebelumnya.'], 422);
        }

        try {
            $pumRequest->submitForApproval();
            $pumRequest->load(['requester', 'workflow', 'approvals.step', 'approvals.approver']);

            // Notification Hook
            $notificationService = app(\App\Services\NotificationService::class);
            if ($pumRequest->status === \App\Models\PumRequest::STATUS_FULFILLED || $pumRequest->status === \App\Models\PumRequest::STATUS_APPROVED) {
                if ($pumRequest->getCurrentApproval()) {
                    $notificationService->notifyApprovers($pumRequest);
                } else {
                    $notificationService->notifyRequesterApproved($pumRequest);
                }
            } else {
                $notificationService->notifyApprovers($pumRequest);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Permintaan berhasil diajukan untuk persetujuan.',
                'data'    => $pumRequest,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function downloadAttachment($filename)
    {
        // Prevent directory traversal
        $filename = basename($filename);
        $path = storage_path('app/public/pum-attachments/' . $filename);
        
        if (!file_exists($path)) {
            return response()->json(['status' => 'error', 'message' => 'File tidak ditemukan.'], 404);
        }

        return response()->file($path);
    }
}
