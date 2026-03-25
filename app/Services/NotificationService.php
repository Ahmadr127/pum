<?php

namespace App\Services;

use App\Models\PumRequest;
use App\Models\User;
use App\Jobs\SendFcmNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Notify current approvers for a PUM request.
     */
    public function notifyApprovers(PumRequest $pumRequest)
    {
        $currentApproval = $pumRequest->getCurrentApproval();
        if (!$currentApproval || !$currentApproval->step) {
            return;
        }

        $approvers = $currentApproval->step->getApprovers($pumRequest->requester);
        
        if ($approvers->isEmpty()) {
            Log::warning("No approvers found for PUM Request {$pumRequest->code} at step {$currentApproval->step->name}");
            return;
        }

        $title = "Persetujuan PUM Baru";
        $body = "Permintaan PUM {$pumRequest->code} dari {$pumRequest->requester->name} sebesar Rp " . number_format($pumRequest->amount, 0, ',', '.') . " menunggu persetujuan Anda.";
        
        // If it's a release step, change the wording
        if ($currentApproval->step->type === \App\Models\PumApprovalStep::TYPE_RELEASE) {
            $title = "Pencairan PUM Baru";
            $body = "Permintaan PUM {$pumRequest->code} menunggu pencairan (release) dari Anda.";
        }

        $this->sendToUsers($approvers, $title, $body, [
            'type' => 'pum_approval_required',
            'pum_id' => (string) $pumRequest->id,
            'code' => $pumRequest->code,
        ]);
    }

    /**
     * Notify requester that their PUM has been approved/fulfilled.
     */
    public function notifyRequesterApproved(PumRequest $pumRequest)
    {
        $requester = $pumRequest->requester;
        $statusLabel = $pumRequest->status === PumRequest::STATUS_FULFILLED ? 'telah dicairkan' : 'telah disetujui';

        $title = "PUM {$pumRequest->status_label}";
        $body = "Permintaan PUM {$pumRequest->code} Anda sebesar Rp " . number_format($pumRequest->amount, 0, ',', '.') . " {$statusLabel}.";

        $this->sendToUsers(collect([$requester]), $title, $body, [
            'type' => 'pum_approved',
            'pum_id' => (string) $pumRequest->id,
            'code' => $pumRequest->code,
        ]);
    }

    /**
     * Notify requester that their PUM has been rejected.
     */
    public function notifyRequesterRejected(PumRequest $pumRequest, string $notes = '')
    {
        $requester = $pumRequest->requester;
        
        $title = "PUM Ditolak";
        $body = "Maaf, permintaan PUM {$pumRequest->code} Anda ditolak." . ($notes ? " Alasan: {$notes}" : "");

        $this->sendToUsers(collect([$requester]), $title, $body, [
            'type' => 'pum_rejected',
            'pum_id' => (string) $pumRequest->id,
            'code' => $pumRequest->code,
        ]);
    }

    /**
     * Internal helper to resolve tokens and dispatch job.
     */
    protected function sendToUsers($users, $title, $body, $data = [])
    {
        $allTokens = [];

        foreach ($users as $user) {
            // Save to database inbox for each user (Phase 8 integration)
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title'   => $title,
                'body'    => $body,
                'data'    => $data,
            ]);

            $tokens = $user->deviceTokens()->pluck('device_token')->toArray();
            $allTokens = array_merge($allTokens, $tokens);
        }

        $allTokens = array_unique($allTokens);

        if (!empty($allTokens)) {
            SendFcmNotification::dispatch($allTokens, $title, $body, $data);
        }
    }
}
