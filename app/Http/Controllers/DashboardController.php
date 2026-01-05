<?php

namespace App\Http\Controllers;

use App\Models\PumRequest;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\OrganizationType;
use App\Models\OrganizationUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = ['user' => $user];
        
        // PUM Approval Stats (if user has approve_pum permission)
        if ($user->hasPermission('approve_pum')) {
            $pendingApprovals = PumRequest::with(['requester', 'approvals.step'])
                ->where('status', PumRequest::STATUS_PENDING)
                ->whereHas('approvals', fn($q) => $q->where('status', 'pending'))
                ->get()
                ->filter(fn($req) => $req->canBeApprovedBy($user))
                ->take(5);
            
            $data['pendingApprovals'] = $pendingApprovals;
            $data['pendingApprovalsCount'] = $pendingApprovals->count();
            
            // Add PUM stats for approvers (if they don't have manage_pum permission)
            if (!$user->hasPermission('manage_pum')) {
                $data['pumStats'] = [
                    'total' => PumRequest::count(),
                    'new' => PumRequest::where('status', 'new')->count(),
                    'pending' => PumRequest::where('status', 'pending')->count(),
                    'approved' => PumRequest::where('status', 'approved')->count(),
                    'rejected' => PumRequest::where('status', 'rejected')->count(),
                    'fulfilled' => PumRequest::where('status', 'fulfilled')->count(),
                ];
            }
        }
        
        // PUM Request Stats (if user has manage_pum permission)
        if ($user->hasPermission('manage_pum')) {
            $data['pumStats'] = [
                'total' => PumRequest::count(),
                'new' => PumRequest::where('status', 'new')->count(),
                'pending' => PumRequest::where('status', 'pending')->count(),
                'approved' => PumRequest::where('status', 'approved')->count(),
                'rejected' => PumRequest::where('status', 'rejected')->count(),
                'fulfilled' => PumRequest::where('status', 'fulfilled')->count(),
            ];
            
            $data['recentRequests'] = PumRequest::with('requester')
                ->where('created_by', $user->id)
                ->orWhere('requester_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }
        
        // Admin Stats
        if ($user->hasPermission('manage_users')) {
            $data['userCount'] = User::count();
        }
        
        if ($user->hasPermission('manage_roles')) {
            $data['roleCount'] = Role::count();
        }
        
        if ($user->hasPermission('manage_permissions')) {
            $data['permissionCount'] = Permission::count();
        }
        
        if ($user->hasPermission('manage_organization_types')) {
            $data['orgTypeCount'] = OrganizationType::count();
        }
        
        if ($user->hasPermission('manage_organization_units')) {
            $data['orgUnitCount'] = OrganizationUnit::count();
        }
        
        return view('dashboard', $data);
    }
}
