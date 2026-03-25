<?php

/*
|--------------------------------------------------------------------------
| Firebase Configuration
|--------------------------------------------------------------------------
| Supports two modes:
|
| 1. JSON String (recommended for CPanel hosting — no file upload needed):
|    Set FIREBASE_CREDENTIALS_JSON in .env to the full JSON content of the
|    service account file (minified, with \n escaped in the private_key).
|
| 2. File Path (default fallback):
|    Upload firebase-auth.json to storage/app/ and set FIREBASE_CREDENTIALS
|    to the absolute path (or leave empty to use storage_path default).
*/

$credentialsJson = env('FIREBASE_CREDENTIALS_JSON');

$credentials = $credentialsJson
    ? json_decode($credentialsJson, true)            // Use JSON string from env
    : env('FIREBASE_CREDENTIALS', storage_path('app/firebase-auth.json')); // Use file path

return [
    'default' => env('FIREBASE_PROJECT', 'app'),

    'projects' => [
        'app' => [
            'credentials' => $credentials,
        ],
    ],
];
