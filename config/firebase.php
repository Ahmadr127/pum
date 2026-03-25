<?php

return [
    'default' => env('FIREBASE_PROJECT', 'app'),

    'projects' => [
        'app' => [
            // Gunakan FIREBASE_CREDENTIALS_JSON (isi JSON string) jika ada,
            // atau baca file dan parse sendiri (lebih reliable daripada pass path ke kreait)
            'credentials' => env('FIREBASE_CREDENTIALS_JSON')
                ? json_decode(env('FIREBASE_CREDENTIALS_JSON'), true)
                : (file_exists(storage_path('app/firebase-auth.json'))
                    ? json_decode(file_get_contents(storage_path('app/firebase-auth.json')), true)
                    : storage_path('app/firebase-auth.json')), // fallback agar error lebih jelas
        ],
    ],
];
