<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Firebase Project
    |--------------------------------------------------------------------------
    */
    'default' => env('FIREBASE_PROJECT', 'app'),

    'projects' => [
        'app' => [
            /*
            |------------------------------------------------------------------
            | Firebase Service Account Credentials
            |------------------------------------------------------------------
            | Path to the Firebase service account JSON file, relative to the
            | base path of the application. Stored at storage/app/firebase-auth.json
            */
            'credentials' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase-auth.json')),
        ],
    ],
];
