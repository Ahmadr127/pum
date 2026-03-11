<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SsoController;
use App\Http\Controllers\Api\PumRequestApiController;
use App\Http\Controllers\Api\PumApprovalApiController;
use App\Http\Controllers\Api\PumWorkflowApiController;

/*
|--------------------------------------------------------------------------
| PUM API Routes  (prefix: /api, middleware: api)
|--------------------------------------------------------------------------
| Public:    POST /api/sso-login
| Protected: everything else (auth:sanctum)
*/

Route::post('/sso-login', [SsoController::class, 'loginViaToken']);
Route::get('/ping', fn() => response()->json(['status' => 'ok']));

Route::middleware('auth:sanctum')->group(function () {

    // Current user info
    Route::get('/user', function (Request $request) {
        $user = $request->user()->load('role.permissions');
        return response()->json([
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'username'    => $user->username,
            'nik'         => $user->nik,
            'role'        => $user->role?->name,
            'permissions' => $user->role?->permissions->pluck('name') ?? [],
        ]);
    });

    // ----------------------------------------------------------------
    // PUM Requests + Approvals  (merged prefix to avoid route conflicts)
    // Specific slug routes MUST be declared before /{pumRequest} wildcard
    // ----------------------------------------------------------------
    Route::prefix('pum/requests')->group(function () {
        Route::get('/mine',               [PumRequestApiController::class,  'myRequests']);
        Route::get('/pending-approvals',  [PumApprovalApiController::class, 'pendingApprovals']);
        Route::get('/pending-releases',   [PumApprovalApiController::class, 'pendingReleases']);
        Route::get('/',                   [PumRequestApiController::class,  'index']);

        // Wildcard must come LAST
        Route::get('/{pumRequest}',          [PumRequestApiController::class,  'show']);
        Route::post('/{pumRequest}/submit',  [PumRequestApiController::class,  'submit']);
        Route::post('/{pumRequest}/approve', [PumApprovalApiController::class, 'approve']);
        Route::post('/{pumRequest}/reject',  [PumApprovalApiController::class, 'reject']);
    });

    // ----------------------------------------------------------------
    // PUM Workflows  (PumWorkflowApiController)
    // ----------------------------------------------------------------
    Route::get('/pum/workflows', [PumWorkflowApiController::class, 'index']);
});
