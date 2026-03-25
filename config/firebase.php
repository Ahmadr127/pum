<?php

return [
    'default' => env('FIREBASE_PROJECT', 'app'),

    'projects' => [
        'app' => [
            // Gunakan FIREBASE_CREDENTIALS_JSON (isi JSON string) jika ada,
            // atau fallback ke file path di storage/app/firebase-auth.json
            'credentials' => env('FIREBASE_CREDENTIALS_JSON')
                ? json_decode(env('FIREBASE_CREDENTIALS_JSON'), true)
                : storage_path('app/firebase-auth.json'),
        ],
    ],
];
