<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SsoController;

Route::post('/sso-login', [SsoController::class, 'loginViaToken']);
Route::get('/ping', function() {
    return response()->json(['status' => 'ok']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'username' => $user->username,
            'nik'      => $user->nik,
        ]);
    });
});
