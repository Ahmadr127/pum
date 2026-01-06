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
            
            // Stats for approvers - show requests they need to approve
            if (!$user->hasPermission('manage_pum')) {
                // Count all requests that this user can approve or has approved
                $allApprovalRequests = PumRequest::with(['approvals'])
                    ->whereIn('status', [
                        PumRequest::STATUS_PENDING, 
                        PumRequest::STATUS_APPROVED, 
                        PumRequest::STATUS_REJECTED
                    ])
                    ->get();
                
                $myApprovalRequests = $allApprovalRequests->filter(function($req) use ($user) {
                    // Can approve this request
                    if ($req->canBeApprovedBy($user)) return true;
                    // Has already approved/rejected this request
                    return $req->approvals->where('approver_id', $user->id)->count() > 0;
                });
                
                $data['pumStats'] = [
                    'total' => $myApprovalRequests->count(),
                    'pending' => $myApprovalRequests->where('status', 'pending')->count(),
                    'approved' => $myApprovalRequests->where('status', 'approved')->count(),
                ];
            }
        }
        
        // PUM Request Stats (if user has manage_pum permission - for requesters/staff)
        if ($user->hasPermission('manage_pum')) {
            // Show only requests created by or requested by this user
            $myRequests = PumRequest::where('created_by', $user->id)
                ->orWhere('requester_id', $user->id);
            
            $data['pumStats'] = [
                'total' => (clone $myRequests)->count(),
                'new' => (clone $myRequests)->where('status', 'new')->count(),
                'pending' => (clone $myRequests)->where('status', 'pending')->count(),
                'approved' => (clone $myRequests)->where('status', 'approved')->count(),
                'rejected' => (clone $myRequests)->where('status', 'rejected')->count(),
                'fulfilled' => (clone $myRequests)->where('status', 'fulfilled')->count(),
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
