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
     * @return array Debug data for API response
     */
    public function notifyApprovers(PumRequest $pumRequest)
    {
        // Ensure requester and their organization unit are loaded for Org Head resolution
        $pumRequest->loadMissing('requester.organizationUnit');
        
        $currentApproval = $pumRequest->getCurrentApproval();
        if (!$currentApproval || !$currentApproval->step) {
            Log::info("[NotificationService] No pending approval step found for PUM {$pumRequest->code}. Skipping notification.");
            return ['device_count' => 0, 'device_tokens' => [], 'message' => 'No pending approval step.'];
        }

        $approvers = $currentApproval->step->getApprovers($pumRequest->requester);
        
        if ($approvers->isEmpty()) {
            Log::warning("[NotificationService] No approvers found for PUM Request {$pumRequest->code} at step {$currentApproval->step->name} (Type: {$currentApproval->step->approver_type})");
            return ['device_count' => 0, 'device_tokens' => [], 'message' => 'No approvers found for this step.'];
        }

        Log::info("[NotificationService] Found " . $approvers->count() . " potential approvers for PUM {$pumRequest->code} at step {$currentApproval->step->name}");

        $title = "Persetujuan PUM Baru";
        $body = "Anda perlu approve pengajuan uang muka {$pumRequest->code} dari {$pumRequest->requester->name}";
        
        // If it's a release step, change the wording
        if ($currentApproval->step->type === \App\Models\PumApprovalStep::TYPE_RELEASE) {
            $title = "Pencairan PUM Baru";
            $body = "Anda perlu release pengajuan uang muka {$pumRequest->code} dari {$pumRequest->requester->name}";
        }

        return $this->notifyUsers($approvers, $title, $body, [
            'type' => 'pum_approval_required',
            'pum_id' => (string) $pumRequest->id,
            'code' => $pumRequest->code,
        ]);
    }

    /**
     * Notify requester that their PUM has been approved/fulfilled.
     * @return array Debug data for API response
     */
    public function notifyRequesterApproved(PumRequest $pumRequest)
    {
        $requester = $pumRequest->requester;
        $statusLabel = $pumRequest->status === PumRequest::STATUS_FULFILLED ? 'telah dicairkan' : 'telah disetujui';

        $title = "PUM {$pumRequest->status_label}";
        $body = "Permintaan PUM {$pumRequest->code} Anda sebesar Rp " . number_format($pumRequest->amount, 0, ',', '.') . " {$statusLabel}.";

        return $this->notifyUsers(collect([$requester]), $title, $body, [
            'type' => 'pum_approved',
            'pum_id' => (string) $pumRequest->id,
            'code' => $pumRequest->code,
        ]);
    }

    /**
     * Notify requester that their PUM has been rejected.
     * @return array Debug data for API response
     */
    public function notifyRequesterRejected(PumRequest $pumRequest, string $notes = '')
    {
        $requester = $pumRequest->requester;
        
        $title = "PUM Ditolak";
        $body = "Maaf, permintaan PUM {$pumRequest->code} Anda ditolak." . ($notes ? " Alasan: {$notes}" : "");

        return $this->notifyUsers(collect([$requester]), $title, $body, [
            'type' => 'pum_rejected',
            'pum_id' => (string) $pumRequest->id,
            'code' => $pumRequest->code,
        ]);
    }

    /**
     * Notify specific users with a custom message.
     * @return array Debug data for API response
     */
    public function notifyUsers($users, $title, $body, $data = [])
    {
        $allTokens = [];
        $allDeviceTypeCounts = [];
        $targetUserIds = [];
        $perUserTokenDebug = [];

        foreach ($users as $user) {
            $targetUserIds[] = $user->id;

            // Save to database inbox for each user (Phase 8 integration)
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title'   => $title,
                'body'    => $body,
                'data'    => $data,
            ]);

            $tokenRecords = $user->deviceTokens()->get(['device_token', 'device_type']);
            $tokens = $tokenRecords->pluck('device_token')->toArray();
            
            if (empty($tokens)) {
                Log::warning("[FCM DEBUG] User #{$user->id} ({$user->name}) has NO registered device tokens. They will not receive push notification.");
            } else {
                Log::info("[FCM DEBUG] User #{$user->id} has " . count($tokens) . " token(s): " . implode(', ', $tokens));
                $allTokens = array_merge($allTokens, $tokens);
            }

            $lengths = [];
            foreach ($tokens as $t) {
                if (!is_string($t)) {
                    continue;
                }
                $lengths[] = strlen($t);
            }
            $perUserTokenDebug[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'token_count' => count($tokens),
                'token_lengths' => $lengths,
                'tokens' => $tokens, // Include raw tokens per user
            ];

            foreach ($tokenRecords->groupBy('device_type') as $deviceType => $group) {
                $key = $deviceType ?? 'unknown';
                $allDeviceTypeCounts[$key] = ($allDeviceTypeCounts[$key] ?? 0) + $group->count();
            }
        }

        $allTokens = array_unique($allTokens);
        
        $debugInfo = [
            'device_count'    => count($allTokens),
            'target_user_ids' => $targetUserIds,
            'device_tokens'   => array_values($allTokens),
            'title'           => $title,
            'body'            => $body,
        ];

        if (!empty($allTokens)) {
            Log::info('[FCM DEBUG] Dispatching FCM notification (pum)', [
                'runId' => 'iter5_raw_tokens',
                'target_user_ids' => $targetUserIds,
                'per_user_token_debug' => $perUserTokenDebug,
                'token_count_total' => count($allTokens),
                'device_tokens_all' => array_values($allTokens), // Log all raw tokens
                'title' => $title,
            ]);
            SendFcmNotification::dispatch($allTokens, $title, $body, $data);
            $debugInfo['message'] = "Dispatched to " . count($allTokens) . " devices.";
        } else {
            Log::warning("[FCM DEBUG] No valid tokens found for target users. No FCM sent.");
            $debugInfo['message'] = "No valid tokens found for target users. No FCM sent.";
        }

        return $debugInfo;
    }
}
