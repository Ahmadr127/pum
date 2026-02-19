<?php

namespace App\Http\Controllers;

use App\Models\PumRequest;
use App\Models\PumApprovalWorkflow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class PumRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PumRequest::with(['requester', 'workflow', 'approvals.approver'])
            ->search($request->search)
            ->byStatus($request->status)
            ->byDateRange($request->date_from, $request->date_to)
            ->byRequester($request->requester_id);

        // Filter by amount range
        if ($request->amount_min) {
            $query->where('amount', '>=', $request->amount_min);
        }
        if ($request->amount_max) {
            $query->where('amount', '<=', $request->amount_max);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get users for filter dropdown
        $users = User::orderBy('name')->get();
        
        // Get status options
        $statuses = PumRequest::getStatusLabels();

        return view('pum.requests.index', compact('requests', 'users', 'statuses'));
    }

    /**
     * Display a listing of the resource for current user.
     */
    public function myRequests(Request $request)
    {
        $query = PumRequest::with(['requester', 'workflow', 'approvals.approver'])
            ->where('requester_id', Auth::id())
            ->search($request->search)
            ->byStatus($request->status)
            ->byDateRange($request->date_from, $request->date_to);

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get status options
        $statuses = PumRequest::getStatusLabels();

        return view('pum.requests.myrequest', compact('requests', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        $workflows = PumApprovalWorkflow::active()->with('steps')->get();
        
        return view('pum.requests.create', compact('users', 'workflows'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requester_id'    => 'required|exists:users,id',
            'request_date'    => 'required|date',
            'amount'          => 'required|numeric|min:0',
            'description'     => 'nullable|string|max:1000',
            'no_surat'        => 'nullable|string|max:255',
            'workflow_id'     => 'nullable|exists:pum_approval_workflows,id',
            'submit_for_approval' => 'nullable|boolean',
            'attachments'     => 'nullable|array',
            'attachments.*'   => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            'attachments2'    => 'nullable|array',
            'attachments2.*'  => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            'scanned_pdf'     => 'nullable|file|mimes:pdf|max:10240',
        ]);

        // Check authorization to create for others
        if (!Auth::user()->hasPermission('manage_pum') && $validated['requester_id'] != Auth::id()) {
            abort(403, 'Anda hanya dapat membuat permintaan untuk diri sendiri.');
        }

        // Handle scanned PDF as first attachment
        $attachments = [];
        if ($request->hasFile('scanned_pdf')) {
            $path = $request->file('scanned_pdf')->store('pum-attachments', 'public');
            $attachments[] = $path;
        }

        // Handle additional file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('pum-attachments', 'public');
                $attachments[] = $path;
            }
        }

        $attachments2 = [];
        if ($request->hasFile('attachments2')) {
            foreach ($request->file('attachments2') as $file) {
                $path = $file->store('pum-attachments', 'public');
                $attachments2[] = $path;
            }
        }

        $pumRequest = PumRequest::create([
            'code'         => !empty($validated['no_surat'])
                                ? $validated['no_surat']
                                : PumRequest::generateCode(User::find($validated['requester_id'])),
            'no_surat'     => $validated['no_surat'] ?? null,
            'requester_id' => $validated['requester_id'],
            'request_date' => $validated['request_date'],
            'amount'       => $validated['amount'],
            'description'  => $validated['description'] ?? null,
            'attachments'  => !empty($attachments) ? $attachments : null,
            'attachments2' => !empty($attachments2) ? $attachments2 : null,
            'workflow_id'  => $validated['workflow_id'] ?? null,
            'status'       => PumRequest::STATUS_NEW,
            'created_by'   => Auth::id(),
        ]);

        // Submit for approval if requested
        if ($request->boolean('submit_for_approval')) {
            try {
                $pumRequest->submitForApproval();
                return redirect()
                    ->route('pum-requests.show', $pumRequest)
                    ->with('success', 'Permintaan uang muka berhasil dibuat dan diajukan untuk persetujuan.');
            } catch (\Exception $e) {
                return redirect()
                    ->route('pum-requests.show', $pumRequest)
                    ->with('warning', 'Permintaan dibuat, tetapi gagal mengajukan approval: ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('pum-requests.show', $pumRequest)
            ->with('success', 'Permintaan uang muka berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PumRequest $pumRequest)
    {
        $pumRequest->load([
            'requester', 
            'creator', 
            'workflow.steps', 
            'approvals.step', 
            'approvals.approver'
        ]);

        // Check view permission - Relaxed as per request
        // 1. Has manage_pum permission
        // 2. Is the requester
        // 3. Is an approver (current or past)
        // $isApprover = $pumRequest->approvals->contains('approver_id', Auth::id());
        
        // if (!Auth::user()->hasPermission('manage_pum') && 
        //     $pumRequest->requester_id !== Auth::id() && 
        //     !$isApprover) {
        //     abort(403, 'Anda tidak memiliki hak akses untuk melihat permintaan ini.');
        // }
        
        $canApprove = $pumRequest->canBeApprovedBy(Auth::user());
        
        return view('pum.requests.show', compact('pumRequest', 'canApprove'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PumRequest $pumRequest)
    {
        // Check authorization
        if (!Auth::user()->hasPermission('manage_pum') && $pumRequest->requester_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit permintaan ini.');
        }

        // Admin can edit any status, regular users can only edit 'new' status
        if (!Auth::user()->hasPermission('manage_pum') && $pumRequest->status !== PumRequest::STATUS_NEW) {
            return redirect()
                ->route('pum-requests.show', $pumRequest)
                ->with('error', 'Hanya permintaan dengan status "Baru" yang dapat diedit.');
        }


        $users = User::orderBy('name')->get();
        $workflows = PumApprovalWorkflow::active()->with('steps')->get();
        
        return view('pum.requests.edit', compact('pumRequest', 'users', 'workflows'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PumRequest $pumRequest)
    {
        // Check authorization
        if (!Auth::user()->hasPermission('manage_pum') && $pumRequest->requester_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah permintaan ini.');
        }

        // Admin can update any status, regular users can only update 'new' status
        if (!Auth::user()->hasPermission('manage_pum') && $pumRequest->status !== PumRequest::STATUS_NEW) {
            return redirect()
                ->route('pum-requests.show', $pumRequest)
                ->with('error', 'Hanya permintaan dengan status "Baru" yang dapat diedit.');
        }


        $validated = $request->validate([
            'requester_id' => 'required|exists:users,id',
            'request_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'workflow_id' => 'nullable|exists:pum_approval_workflows,id',
            'submit_for_approval' => 'nullable|boolean',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            'remove_attachments' => 'nullable|array',
            'attachments2' => 'nullable|array',
            'attachments2.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            'remove_attachments2' => 'nullable|array',
        ]);

        // Handle existing attachments removal
        $currentAttachments = $pumRequest->attachments ?? [];
        if ($request->has('remove_attachments')) {
            foreach ($request->remove_attachments as $index) {
                if (isset($currentAttachments[$index])) {
                    Storage::disk('public')->delete($currentAttachments[$index]);
                    unset($currentAttachments[$index]);
                }
            }
            $currentAttachments = array_values($currentAttachments);
        }

        // Handle new file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('pum-attachments', 'public');
                $currentAttachments[] = $path;
            }
        }

        // Handle existing attachments2 removal
        $currentAttachments2 = $pumRequest->attachments2 ?? [];
        if ($request->has('remove_attachments2')) {
            foreach ($request->remove_attachments2 as $index) {
                if (isset($currentAttachments2[$index])) {
                    Storage::disk('public')->delete($currentAttachments2[$index]);
                    unset($currentAttachments2[$index]);
                }
            }
            $currentAttachments2 = array_values($currentAttachments2);
        }

        // Handle new file uploads for attachments2
        if ($request->hasFile('attachments2')) {
            foreach ($request->file('attachments2') as $file) {
                $path = $file->store('pum-attachments', 'public');
                $currentAttachments2[] = $path;
            }
        }

        $pumRequest->update([
            'requester_id' => $validated['requester_id'],
            'request_date' => $validated['request_date'],
            'amount' => $validated['amount'],
            'description' => $validated['description'] ?? null,
            'attachments' => !empty($currentAttachments) ? $currentAttachments : null,
            'attachments2' => !empty($currentAttachments2) ? $currentAttachments2 : null,
            'workflow_id' => $validated['workflow_id'] ?? null,
        ]);

        // Submit for approval if requested
        if ($request->boolean('submit_for_approval')) {
            try {
                $pumRequest->submitForApproval();
                return redirect()
                    ->route('pum-requests.show', $pumRequest)
                    ->with('success', 'Permintaan berhasil diupdate dan diajukan untuk persetujuan.');
            } catch (\Exception $e) {
                return redirect()
                    ->route('pum-requests.show', $pumRequest)
                    ->with('warning', 'Permintaan diupdate, tetapi gagal mengajukan approval: ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('pum-requests.show', $pumRequest)
            ->with('success', 'Permintaan uang muka berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PumRequest $pumRequest)
    {
        // Check authorization
        if (!Auth::user()->hasPermission('manage_pum') && $pumRequest->requester_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus permintaan ini.');
        }

        // Can only delete if status is 'new' or 'rejected'
        if (!in_array($pumRequest->status, [PumRequest::STATUS_NEW, PumRequest::STATUS_REJECTED])) {
            return redirect()
                ->route('pum-requests.index')
                ->with('error', 'Hanya permintaan dengan status "Baru" atau "Ditolak" yang dapat dihapus.');
        }

        $pumRequest->delete();

        return redirect()
            ->route('pum-requests.index')
            ->with('success', 'Permintaan uang muka berhasil dihapus.');
    }

    /**
     * Submit request for approval
     */
    public function submit(PumRequest $pumRequest)
    {
        // Check authorization
        if (!Auth::user()->hasPermission('manage_pum') && $pumRequest->requester_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengajukan permintaan ini.');
        }

        if ($pumRequest->status !== PumRequest::STATUS_NEW) {
            return redirect()
                ->route('pum-requests.show', $pumRequest)
                ->with('error', 'Permintaan sudah diajukan sebelumnya.');
        }

        try {
            $pumRequest->submitForApproval();
            return redirect()
                ->route('pum-requests.show', $pumRequest)
                ->with('success', 'Permintaan berhasil diajukan untuk persetujuan.');
        } catch (\Exception $e) {
            return redirect()
                ->route('pum-requests.show', $pumRequest)
                ->with('error', 'Gagal mengajukan permintaan: ' . $e->getMessage());
        }
    }

    /**
     * Approve the request
     */
    public function approve(Request $request, PumRequest $pumRequest)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
            'fs_form' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        try {
            // Process approval
            $pumRequest->approve(Auth::user(), $request->notes);

            // Handle FS form upload if provided
            if ($request->hasFile('fs_form')) {
                $currentApproval = $pumRequest->approvals()
                    ->where('approver_id', Auth::id())
                    ->where('status', 'approved')
                    ->latest()
                    ->first();

                if ($currentApproval) {
                    $path = $request->file('fs_form')->store('pum-fs-forms', 'public');
                    $currentApproval->update(['fs_form_path' => $path]);
                }
            }



            return redirect()
                ->route('pum-requests.show', $pumRequest)
                ->with('success', 'Permintaan berhasil disetujui.');
        } catch (\Exception $e) {
            return redirect()
                ->route('pum-requests.show', $pumRequest)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Reject the request
     */
    public function reject(Request $request, PumRequest $pumRequest)
    {
        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        try {
            $pumRequest->reject(Auth::user(), $request->notes);
            return redirect()
                ->route('pum-requests.show', $pumRequest)
                ->with('success', 'Permintaan berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()
                ->route('pum-requests.show', $pumRequest)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Mark request as fulfilled
     */
    public function fulfill(PumRequest $pumRequest)
    {
        try {
            $pumRequest->markAsFulfilled();
            return redirect()
                ->route('pum-requests.show', $pumRequest)
                ->with('success', 'Permintaan berhasil ditandai sebagai terpenuhi.');
        } catch (\Exception $e) {
            return redirect()
                ->route('pum-requests.show', $pumRequest)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Export requests to Excel
     */
    public function export(Request $request)
    {
        $query = PumRequest::with(['requester', 'workflow'])
            ->search($request->search)
            ->byStatus($request->status)
            ->byDateRange($request->date_from, $request->date_to)
            ->byRequester($request->requester_id)
            ->orderBy('created_at', 'desc');

        $requests = $query->get();

        // Generate CSV for now (Excel requires additional package)
        $filename = 'pum_requests_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($requests) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'No',
                'Kode',
                'Nama Pengaju',
                'Tanggal',
                'Jumlah Diajukan',
                'Keterangan',
                'Status',
                'Dibuat Pada',
            ]);

            // Data
            foreach ($requests as $index => $req) {
                fputcsv($file, [
                    $index + 1,
                    $req->code,
                    $req->requester->name ?? '-',
                    $req->request_date->format('d/m/Y'),
                    number_format($req->amount, 0, ',', '.'),
                    $req->description ?? '-',
                    $req->status_label,
                    $req->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Print view with QR-based signatures.
     * Generates QR codes as base64 data URIs server-side to avoid broken
     * image errors when the print tab makes unauthenticated image requests.
     */
    public function print(PumRequest $pumRequest)
    {
        $pumRequest->load([
            'requester',
            'creator',
            'workflow.steps',
            'approvals.step',
            'approvals.approver',
        ]);

        // Only approved approvals are shown in signature area
        $signedApprovals = $pumRequest->approvals
            ->where('status', 'approved')
            ->sortBy('step_order')
            ->values();

        // Pre-generate QR codes as base64 data URIs (keyed by user id)
        $qrCodes = [];
        $usersForQr = collect();
        if ($pumRequest->requester) $usersForQr->push($pumRequest->requester);
        foreach ($signedApprovals as $approval) {
            if ($approval->approver) $usersForQr->push($approval->approver);
        }

        foreach ($usersForQr->unique('id') as $user) {
            if (!$user->hasNik()) continue;
            try {
                $result = Builder::create()
                    ->writer(new PngWriter())
                    ->data($user->nik)
                    ->encoding(new Encoding('UTF-8'))
                    ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
                    ->size(180)
                    ->margin(6)
                    ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
                    ->build();
                $qrCodes[$user->id] = 'data:' . $result->getMimeType() . ';base64,' . base64_encode($result->getString());
            } catch (\Exception $e) {
                // Skip QR if generation fails
            }
        }

        return view('pum.requests.show-print', compact('pumRequest', 'signedApprovals', 'qrCodes'));
    }
}
