<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\OrganizationTypeController;
use App\Http\Controllers\OrganizationUnitController;
use App\Http\Controllers\PumRequestController;
use App\Http\Controllers\PumApprovalWorkflowController;
use App\Http\Controllers\PumApprovalController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will be
| assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// SSO Routes
Route::get('/auth/sso/redirect', function (Request $request) {
    $state = Str::random(40);
    $request->session()->put('sso_state', $state);

    $query = http_build_query([
        'client_id'     => env('SSO_CLIENT_ID'),
        'redirect_uri'  => env('SSO_REDIRECT_URI'),
        'response_type' => 'code',
        'scope'         => '',
        'state'         => $state,
    ]);

    return redirect(env('SSO_BASE_URL') . '/oauth/authorize?' . $query);
})->middleware('web')->name('auth.sso.redirect');

Route::get('/auth/sso/callback', function (Request $request) {
    // Validasi state (CSRF protection) — hanya jika state ada di callback
    if ($request->state && session('sso_state')) {
        abort_if($request->state !== session('sso_state'), 419, 'Invalid SSO state.');
    }

    // Tukar auth code dengan access token
    $tokenResponse = Http::asForm()
        ->withoutVerifying()
        ->post(env('SSO_BASE_URL') . '/oauth/token', [
            'grant_type'    => 'authorization_code',
            'client_id'     => env('SSO_CLIENT_ID'),
            'client_secret' => env('SSO_CLIENT_SECRET'),
            'redirect_uri'  => env('SSO_REDIRECT_URI'),
            'code'          => $request->code,
        ]);

    if (! $tokenResponse->successful()) {
        $body = $tokenResponse->json();
        $errDetail = $body['error_description']
            ?? $body['error']
            ?? $body['message']
            ?? ('HTTP ' . $tokenResponse->status() . ' — ' . json_encode($body));

        \Illuminate\Support\Facades\Log::error('SSO token exchange failed', [
            'status' => $tokenResponse->status(),
            'body'   => $body,
        ]);

        return redirect('/login')->withErrors([
            'sso' => 'Gagal mendapatkan token dari SSO: ' . $errDetail,
        ]);
    }

    $accessToken = $tokenResponse->json('access_token');

    // Ambil data user dari SSO menggunakan access token
    try {
        $ssoUserResponse = Http::withToken($accessToken)
            ->get(env('SSO_BASE_URL') . '/api/user');
        $ssoUser = $ssoUserResponse->json('data') ?? $ssoUserResponse->json();

        if (empty($ssoUser['email'])) {
            $errorBody = $ssoUserResponse->body();
            \Illuminate\Support\Facades\Log::error('SSO User Fetch failed', ['status' => $ssoUserResponse->status(), 'body' => $errorBody]);
            return redirect('/login')->withErrors(['sso' => 'Gagal mengambil data user dari SSO. Detail: ' . $errorBody]);
        }
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('SSO User Fetch Exception', ['message' => $e->getMessage()]);
        return redirect('/login')->withErrors(['sso' => 'Koneksi ke SSO gagal: ' . $e->getMessage()]);
    }

    // Cari user lokal — cari berdasarkan email dulu, lalu username
    $localUser = User::where('email', $ssoUser['email'])->first()
        ?? User::where('username', $ssoUser['username'] ?? '')->first();

    if ($localUser) {
        // Update data user yang sudah ada
        $localUser->update([
            'name'  => $ssoUser['name'],
            'email' => $ssoUser['email'],
            'nik'   => $ssoUser['nik'] ?? $localUser->nik,
        ]);
    } else {
        // Buat user baru jika benar-benar belum ada
        $localUser = User::create([
            'name'     => $ssoUser['name'],
            'email'    => $ssoUser['email'],
            'nik'      => $ssoUser['nik']      ?? null,
            'username' => $ssoUser['username'] ?? null,
            'password' => bcrypt(Str::random(32)),
        ]);
    }

    // Login lokal & regenerate session
    Auth::login($localUser, remember: true);
    $request->session()->regenerate();

    return redirect()->intended('/dashboard');
})->middleware('web')->name('auth.sso.callback');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    

    // User Management routes
    Route::middleware('permission:manage_users')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Role Management routes
    Route::middleware('permission:manage_roles')->group(function () {
        Route::resource('roles', RoleController::class);
    });

    // Permission Management routes
    Route::middleware('permission:manage_permissions')->group(function () {
        Route::resource('permissions', PermissionController::class);
    });

    // Organization Type Management routes
    Route::middleware('permission:manage_organization_types')->group(function () {
        Route::resource('organization-types', OrganizationTypeController::class);
    });

    // Organization Unit Management routes
    Route::middleware('permission:manage_organization_units')->group(function () {
        Route::resource('organization-units', OrganizationUnitController::class);
        
        // Member management routes
        Route::post('organization-units/{organization_unit}/members', [OrganizationUnitController::class, 'addMember'])
            ->name('organization-units.add-member');
        Route::delete('organization-units/{organization_unit}/members/{user}', [OrganizationUnitController::class, 'removeMember'])
            ->name('organization-units.remove-member');
        Route::patch('organization-units/{organization_unit}/head', [OrganizationUnitController::class, 'updateHead'])
            ->name('organization-units.update-head');
    });

    // PUM Routes - Standard User (Create, View Own, Edit Own, Delete Own)
    Route::middleware('permission:create_pum,manage_pum')->group(function () {
        Route::get('my-pum-requests', [PumRequestController::class, 'myRequests'])
            ->name('pum-requests.my-requests');
        Route::get('pum-requests/create', [PumRequestController::class, 'create'])
            ->name('pum-requests.create');
        Route::get('pum-requests/check-duplicate', [PumRequestController::class, 'checkDuplicate'])
            ->name('pum-requests.check-duplicate');
        Route::post('pum-requests', [PumRequestController::class, 'store'])
            ->name('pum-requests.store');
        Route::get('pum-requests/{pum_request}/edit', [PumRequestController::class, 'edit'])
            ->name('pum-requests.edit');
        Route::put('pum-requests/{pum_request}', [PumRequestController::class, 'update'])
            ->name('pum-requests.update');
        Route::patch('pum-requests/{pum_request}', [PumRequestController::class, 'update']);
        Route::delete('pum-requests/{pum_request}', [PumRequestController::class, 'destroy'])
            ->name('pum-requests.destroy');
        Route::post('pum-requests/{pum_request}/submit', [PumRequestController::class, 'submit'])
            ->name('pum-requests.submit');
    });

    // PUM Routes - Management (index, fulfill, export - for admins/managers)
    Route::middleware('permission:manage_pum')->group(function () {
        Route::get('pum-requests', [PumRequestController::class, 'index'])
            ->name('pum-requests.index');
        Route::post('pum-requests/{pum_request}/fulfill', [PumRequestController::class, 'fulfill'])
            ->name('pum-requests.fulfill');
        Route::get('pum-requests-export', [PumRequestController::class, 'export'])
            ->name('pum-requests.export');
    });

    // PUM Routes - View detail (for manage_pum, approve_pum, AND create_pum)
    Route::middleware('permission:manage_pum,approve_pum,create_pum')->group(function () {
        Route::get('pum-requests/{pum_request}', [PumRequestController::class, 'show'])
            ->name('pum-requests.show');
    });

    // PUM Routes - Approval actions (for approvers)
    Route::middleware('permission:approve_pum')->group(function () {
        Route::post('pum-requests/{pum_request}/approve', [PumRequestController::class, 'approve'])
            ->name('pum-requests.approve');
        Route::post('pum-requests/{pum_request}/reject', [PumRequestController::class, 'reject'])
            ->name('pum-requests.reject');
    });

    // PUM print route (accessible to all PUM permission holders)
    Route::middleware('permission:manage_pum,approve_pum,create_pum')->group(function () {
        Route::get('pum-requests/{pum_request}/print', [PumRequestController::class, 'print'])
            ->name('pum-requests.print');
    });

    // User QR Code route (all authenticated users)
    Route::get('users/{user}/qrcode', [UserController::class, 'qrcode'])
        ->name('users.qrcode');

    // PUM Workflow Management Routes
    Route::middleware('permission:manage_pum_workflows')->group(function () {
        Route::resource('pum-workflows', PumApprovalWorkflowController::class);
        Route::post('pum-workflows/{pum_workflow}/set-default', [PumApprovalWorkflowController::class, 'setDefault'])
            ->name('pum-workflows.set-default');
    });

    // PUM Approvals Routes (for approvers)
    Route::middleware('permission:approve_pum')->group(function () {
        Route::get('pum-approvals', [PumApprovalController::class, 'index'])
            ->name('pum-approvals.index');
    });

    // PUM Release Routes (for release users)
    Route::middleware('permission:approve_pum_release')->group(function () {
        Route::get('pum-releases', [\App\Http\Controllers\PumReleaseController::class, 'index'])
            ->name('pum-releases.index');
    });

});
