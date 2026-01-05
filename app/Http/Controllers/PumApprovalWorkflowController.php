<?php

namespace App\Http\Controllers;

use App\Models\PumApprovalWorkflow;
use App\Models\PumApprovalStep;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class PumApprovalWorkflowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workflows = PumApprovalWorkflow::with('steps')->get();
        
        return view('pum.workflows.index', compact('workflows'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('display_name')->get();
        $users = User::orderBy('name')->get();
        
        return view('pum.workflows.create', compact('roles', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'steps' => 'required|array|min:1',
            'steps.*.name' => 'required|string|max:255',
            'steps.*.approver_type' => 'required|in:role,user,organization_head',
            'steps.*.role_id' => 'nullable|required_if:steps.*.approver_type,role|exists:roles,id',
            'steps.*.user_id' => 'nullable|required_if:steps.*.approver_type,user|exists:users,id',
            'steps.*.is_required' => 'boolean',
        ]);

        // If setting as default, unset other defaults
        if ($request->boolean('is_default')) {
            PumApprovalWorkflow::where('is_default', true)->update(['is_default' => false]);
        }

        $workflow = PumApprovalWorkflow::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default'),
        ]);

        // Create steps
        foreach ($validated['steps'] as $index => $stepData) {
            PumApprovalStep::create([
                'workflow_id' => $workflow->id,
                'order' => $index + 1,
                'name' => $stepData['name'],
                'approver_type' => $stepData['approver_type'],
                'role_id' => $stepData['approver_type'] === 'role' ? $stepData['role_id'] : null,
                'user_id' => $stepData['approver_type'] === 'user' ? $stepData['user_id'] : null,
                'is_required' => $stepData['is_required'] ?? true,
            ]);
        }

        return redirect()
            ->route('pum-workflows.index')
            ->with('success', 'Workflow approval berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PumApprovalWorkflow $pumWorkflow)
    {
        $pumWorkflow->load('steps.role', 'steps.user');
        
        return view('pum.workflows.show', compact('pumWorkflow'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PumApprovalWorkflow $pumWorkflow)
    {
        $pumWorkflow->load('steps');
        $roles = Role::orderBy('display_name')->get();
        $users = User::orderBy('name')->get();
        
        return view('pum.workflows.edit', compact('pumWorkflow', 'roles', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PumApprovalWorkflow $pumWorkflow)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'steps' => 'required|array|min:1',
            'steps.*.id' => 'nullable|exists:pum_approval_steps,id',
            'steps.*.name' => 'required|string|max:255',
            'steps.*.approver_type' => 'required|in:role,user,organization_head',
            'steps.*.role_id' => 'nullable|required_if:steps.*.approver_type,role|exists:roles,id',
            'steps.*.user_id' => 'nullable|required_if:steps.*.approver_type,user|exists:users,id',
            'steps.*.is_required' => 'boolean',
        ]);

        // If setting as default, unset other defaults
        if ($request->boolean('is_default') && !$pumWorkflow->is_default) {
            PumApprovalWorkflow::where('is_default', true)->update(['is_default' => false]);
        }

        $pumWorkflow->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default'),
        ]);

        // Get existing step IDs
        $existingStepIds = $pumWorkflow->steps->pluck('id')->toArray();
        $submittedStepIds = collect($validated['steps'])
            ->pluck('id')
            ->filter()
            ->toArray();

        // Delete removed steps
        $stepsToDelete = array_diff($existingStepIds, $submittedStepIds);
        PumApprovalStep::whereIn('id', $stepsToDelete)->delete();

        // Update or create steps
        foreach ($validated['steps'] as $index => $stepData) {
            $stepAttributes = [
                'workflow_id' => $pumWorkflow->id,
                'order' => $index + 1,
                'name' => $stepData['name'],
                'approver_type' => $stepData['approver_type'],
                'role_id' => $stepData['approver_type'] === 'role' ? $stepData['role_id'] : null,
                'user_id' => $stepData['approver_type'] === 'user' ? $stepData['user_id'] : null,
                'is_required' => $stepData['is_required'] ?? true,
            ];

            if (!empty($stepData['id'])) {
                PumApprovalStep::where('id', $stepData['id'])->update($stepAttributes);
            } else {
                PumApprovalStep::create($stepAttributes);
            }
        }

        return redirect()
            ->route('pum-workflows.index')
            ->with('success', 'Workflow approval berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PumApprovalWorkflow $pumWorkflow)
    {
        // Check if workflow is being used
        $inUseCount = \App\Models\PumRequest::where('workflow_id', $pumWorkflow->id)->count();
        
        if ($inUseCount > 0) {
            return redirect()
                ->route('pum-workflows.index')
                ->with('error', "Workflow tidak dapat dihapus karena sedang digunakan oleh {$inUseCount} permintaan.");
        }

        $pumWorkflow->delete();

        return redirect()
            ->route('pum-workflows.index')
            ->with('success', 'Workflow approval berhasil dihapus.');
    }

    /**
     * Set workflow as default
     */
    public function setDefault(PumApprovalWorkflow $pumWorkflow)
    {
        $pumWorkflow->setAsDefault();

        return redirect()
            ->route('pum-workflows.index')
            ->with('success', 'Workflow berhasil dijadikan default.');
    }
}
