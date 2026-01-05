<?php

use Illuminate\Support\Facades\Route;
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

    // PUM (Permintaan Uang Muka) Routes - Management (create, edit, delete)
    Route::middleware('permission:manage_pum')->group(function () {
        Route::get('pum-requests', [PumRequestController::class, 'index'])
            ->name('pum-requests.index');
        Route::get('pum-requests/create', [PumRequestController::class, 'create'])
            ->name('pum-requests.create');
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
        Route::post('pum-requests/{pum_request}/fulfill', [PumRequestController::class, 'fulfill'])
            ->name('pum-requests.fulfill');
        Route::get('pum-requests-export', [PumRequestController::class, 'export'])
            ->name('pum-requests.export');
    });

    // PUM Routes - View detail (for both manage_pum and approve_pum)
    Route::middleware('permission:manage_pum,approve_pum')->group(function () {
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

});
