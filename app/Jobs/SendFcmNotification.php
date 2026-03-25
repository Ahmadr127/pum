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
        
        $notification = Notification::create($this->title, $this->body);
        
        // Filter out empty tokens
        $tokens = array_filter($this->tokens);
        
        if (empty($tokens)) {
            return;
        }

        // Send in chunks of 500 (FCM limit for multicast)
        $chunks = array_chunk($tokens, 500);

        foreach ($chunks as $chunk) {
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($this->data);

            try {
                Log::info("Sending FCM Notification to " . count($chunk) . " devices. Title: {$this->title}");
                
                $report = $messaging->sendMulticast($message, $chunk);
                
                Log::info("FCM Send Report: Success: " . $report->successes()->count() . ", Fail: " . $report->failures()->count());

                // Cleanup invalid tokens
                if ($report->hasFailures()) {
                    foreach ($report->failures()->getItems() as $failure) {
                        $reason = $failure->error()->getMessage();
                        $targetToken = $failure->target()->value();
                        
                        // If token is invalid or not registered, delete it
                        if (str_contains($reason, 'invalid-registration-token') || 
                            str_contains($reason, 'registration-token-not-registered')) {
                            Log::warning("Removing invalid FCM Token: {$targetToken}. Reason: {$reason}");
                            UserDeviceToken::where('device_token', $targetToken)->delete();
                        } else {
                            Log::error("FCM Delivery Failed for token {$targetToken}. Reason: {$reason}");
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Failed to send FCM Multicast: " . $e->getMessage());
            }
        }
    }
}
