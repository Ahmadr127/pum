<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Models\UserDeviceToken;

class SendFcmNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tokens;
    protected $title;
    protected $body;
    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct(array|string $tokens, string $title, string $body, array $data = [])
    {
        $this->tokens = is_array($tokens) ? $tokens : [$tokens];
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $messaging = Firebase::messaging();

        // Runtime evidence: token statistics (avoid logging raw tokens).
        $tokensSnapshot = $this->tokens;
        $tokenCount = is_array($tokensSnapshot) ? count($tokensSnapshot) : 0;
        $placeholderExactCount = 0;
        $minLen = null;
        $maxLen = null;
        $tokenHashSamples = [];
        foreach ($tokensSnapshot as $t) {
            if (!is_string($t)) {
                continue;
            }
            if ($t === 'YOUR_FCM_TOKEN_HERE') {
                $placeholderExactCount++;
            }
            $len = strlen($t);
            $minLen = $minLen === null ? $len : min($minLen, $len);
            $maxLen = $maxLen === null ? $len : max($maxLen, $len);
            if (count($tokenHashSamples) < 3 && $t !== '') {
                $tokenHashSamples[] = substr(hash('sha256', $t), 0, 12);
            }
        }

        $projectId = config('firebase.projects.' . config('firebase.default') . '.credentials.project_id') ?? 'unknown';
        Log::info('[FCM DEBUG] Job about to send multicast (pum)', [
            'runId' => 'iter2',
            'hypothesisId' => 'H1_placeholder_or_invalid_tokens_in_db',
            'token_count' => $tokenCount,
            'placeholder_exact_count' => $placeholderExactCount,
            'token_length' => ['min' => $minLen, 'max' => $maxLen],
            'firebase_project_id' => $projectId,
            'title' => $this->title,
            'token_hash_samples_sha256_prefix' => $tokenHashSamples,
        ]);

        $notification = Notification::create($this->title, $this->body);
        
        // Filter out empty tokens
        $tokens = array_filter($this->tokens);
        
        if (empty($tokens)) {
            return;
        }

        // Send in chunks of 500 (FCM limit for multicast)
        $chunks = array_chunk($tokens, 500);
        $firstFailureLogged = false;

        foreach ($chunks as $chunk) {
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($this->data)
                ->withAndroidConfig([
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'pum_notifications',
                    ],
                ])
                ->withApnsConfig([
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ],
                    ],
                ]);

            try {
                Log::info("Sending FCM Notification to " . count($chunk) . " devices. Title: {$this->title}");
                
                $report = $messaging->sendMulticast($message, $chunk);
                
                Log::info("FCM Send Report: Success: " . $report->successes()->count() . ", Fail: " . $report->failures()->count());

                // Cleanup invalid tokens
                if ($report->hasFailures()) {
                    foreach ($report->failures()->getItems() as $failure) {
                        $reason = $failure->error()->getMessage();
                        $targetToken = $failure->target()->value();

                        // Log only the first failure to keep output small.
                        if ($firstFailureLogged === false) {
                            $matchesCleanup =
                                (str_contains($reason, 'invalid-registration-token') ||
                                    str_contains($reason, 'registration-token-not-registered'));
                            $isPlaceholderTarget = $targetToken === 'YOUR_FCM_TOKEN_HERE';
                            Log::info('[FCM DEBUG] First failure reason + cleanup match (pum)', [
                                'runId' => 'iter2',
                                'hypothesisId' => 'H3_cleanup_patterns_mismatch_with_fcm_reason',
                                'fcm_reason' => $reason,
                                'cleanup_condition_matches_current_patterns' => $matchesCleanup,
                                'target_is_placeholder_exact' => $isPlaceholderTarget,
                                'target_sha256_prefix' => substr(hash('sha256', $targetToken), 0, 12),
                            ]);
                            $firstFailureLogged = true;
                        }
                        
                        // If token is invalid or not registered, delete it
                        $reasonLower = strtolower($reason);
                        if (str_contains($reasonLower, 'invalid-registration-token') ||
                            str_contains($reasonLower, 'registration-token-not-registered') ||
                            str_contains($reasonLower, 'not a valid fcm registration token') ||
                            str_contains($reasonLower, 'registration token is not a valid fcm registration token')) {
                            Log::warning("Removing invalid FCM Token (pum). Reason: {$reason}");
                            UserDeviceToken::where('device_token', $targetToken)->delete();
                        } else {
                            Log::error("FCM Delivery Failed for token (masked, pum). Reason: {$reason}");
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Failed to send FCM Multicast: " . $e->getMessage());
            }
        }
    }
}
