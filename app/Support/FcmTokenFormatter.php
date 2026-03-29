<?php

namespace App\Support;

/**
 * Preview & fingerprint FCM token untuk log/respons API PUM.
 */
class FcmTokenFormatter
{
    public static function preview(string $token): string
    {
        $t = trim($token);
        $len = strlen($t);
        if ($len < 24) {
            return '***';
        }

        return substr($t, 0, 14).'…'.substr($t, -10);
    }

    public static function sha256Prefix(string $token, int $length = 16): string
    {
        return substr(hash('sha256', $token), 0, $length);
    }
}
