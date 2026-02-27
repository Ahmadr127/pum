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
        
        // PUM Release Stats (if user has approve_pum_release permission)
        if ($user->hasPermission('approve_pum_release')) {
            // Pending releases = requests where CURRENT ACTIVE step is a release step AND this user can action it
            $allPendingRelease = PumRequest::with(['requester', 'approvals.step', 'approvals.approver'])
                ->whereIn('status', [PumRequest::STATUS_PENDING, PumRequest::STATUS_APPROVED])
                ->whereHas('approvals', function($q) {
                    $q->where('status', 'pending')
                      ->whereHas('step', function($sq) {
                          $sq->where('type', \App\Models\PumApprovalStep::TYPE_RELEASE);
                      });
                })
                ->get()
                ->filter(function($req) use ($user) {
                    // CRITICAL: only include if the CURRENT active step (not just any future step) is a release step
                    $currentApproval = $req->getCurrentApproval();
                    if (!$currentApproval || !$currentApproval->step) return false;
                    if ($currentApproval->step->type !== \App\Models\PumApprovalStep::TYPE_RELEASE) return false;
                    return $req->canBeApprovedBy($user);
                });
            
            $data['pendingReleases'] = $allPendingRelease->take(5);
            $data['pendingReleasesCount'] = $allPendingRelease->count();
            
            // Stats: All requests where this user HAS a release-step approval record (pending or actioned)
            $allReleaseRequests = PumRequest::with(['approvals.step'])
                ->whereIn('status', [
                    PumRequest::STATUS_PENDING,
                    PumRequest::STATUS_APPROVED, 
                    PumRequest::STATUS_FULFILLED,
                    PumRequest::STATUS_REJECTED
                ])
                ->whereHas('approvals', function($q) use ($user) {
                    $q->where('approver_id', $user->id)
                      ->whereHas('step', function($sq) {
                          $sq->where('type', \App\Models\PumApprovalStep::TYPE_RELEASE);
                      });
                })
                ->get();
            
            // Also include requests currently pending release by this user (not yet actioned but user is the releaser)
            $pendingReleaseRequests = $allPendingRelease->reject(function($req) use ($allReleaseRequests) {
                return $allReleaseRequests->contains('id', $req->id);
            });
            
            $myReleaseRequests = $allReleaseRequests->merge($pendingReleaseRequests);
            
            $data['releaseStats'] = [
                'total' => $myReleaseRequests->count(),
                'pending' => $allPendingRelease->count(), // requests waiting for THIS user to release
                'released' => $allReleaseRequests->filter(function($req) use ($user) {
                    // Has an approved release step by this user specifically
                    return $req->approvals
                        ->where('approver_id', $user->id)
                        ->where('status', 'approved')
                        ->filter(fn($appr) => $appr->step && $appr->step->type === \App\Models\PumApprovalStep::TYPE_RELEASE)
                        ->count() > 0;
                })->count(),
            ];
        }

        
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
            
            // Stats for approvers - always set for approve_pum users
            // Count all requests that this user can approve or has approved
            $allApprovalRequests = PumRequest::with(['approvals'])
                ->whereIn('status', [
                    PumRequest::STATUS_PENDING, 
                    PumRequest::STATUS_APPROVED, 
                    PumRequest::STATUS_REJECTED,
                    PumRequest::STATUS_FULFILLED,
                ])
                ->get();
            
            $myApprovalRequests = $allApprovalRequests->filter(function($req) use ($user) {
                // Can approve this request
                if ($req->canBeApprovedBy($user)) return true;
                // Has already approved/rejected this request
                return $req->approvals->where('approver_id', $user->id)->count() > 0;
            });
            
            $data['approvalStats'] = [
                'total' => $myApprovalRequests->count(),
                'pending' => $myApprovalRequests->where('status', 'pending')->count(),
                // Count requests where THIS user has actually approved an approval-type step
                'approved' => $myApprovalRequests->filter(function($req) use ($user) {
                    return $req->approvals
                        ->where('approver_id', $user->id)
                        ->where('status', 'approved')
                        ->filter(fn($appr) => !$appr->step || $appr->step->type !== \App\Models\PumApprovalStep::TYPE_RELEASE)
                        ->count() > 0;
                })->count(),
            ];
        }
        
        // PUM Request Stats (if user has manage_pum OR create_pum permission - for requesters/staff)
        if ($user->hasPermission('manage_pum') || $user->hasPermission('create_pum')) {
            // Show only requests created by or requested by this user
            $myRequests = PumRequest::where(function($query) use ($user) {
                $query->where('created_by', $user->id)
                      ->orWhere('requester_id', $user->id);
            });
            
            $data['myRequestStats'] = [
                'total' => (clone $myRequests)->count(),
                'pending' => (clone $myRequests)->where('status', 'pending')->count(),
                'completed' => (clone $myRequests)->whereIn('status', ['approved', 'fulfilled'])->count(),
                // Additional stats for reference
                'new' => (clone $myRequests)->where('status', 'new')->count(),
                'rejected' => (clone $myRequests)->where('status', 'rejected')->count(),
            ];
            
            $data['recentRequests'] = PumRequest::with('requester')
                ->where(function($query) use ($user) {
                    $query->where('created_by', $user->id)
                          ->orWhere('requester_id', $user->id);
                })
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
