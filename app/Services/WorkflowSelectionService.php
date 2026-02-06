<?php

namespace App\Services;

use App\Models\PumRequest;
use App\Models\PumApprovalWorkflow;
use App\Models\PumRequestApproval;

class WorkflowSelectionService
{
    /**
     * Get the default workflow (Manager Pengaju only)
     */
    public function getDefaultWorkflow(): PumApprovalWorkflow
    {
        return PumApprovalWorkflow::where('is_default', true)
            ->where('is_active', true)
            ->firstOrFail();
    }

    /**
     * Get matching workflow based on amount and category
     */
    public function getMatchingWorkflow($amount, $category): ?PumApprovalWorkflow
    {
        $workflows = PumApprovalWorkflow::with('conditions')
            ->where('is_active', true)
            ->where('is_default', false) // Exclude default workflow
            ->get();

        // Find workflows that match conditions, ordered by priority
        $matchingWorkflows = $workflows->filter(function ($workflow) use ($amount, $category) {
            return $workflow->matchesConditions($amount, $category);
        });

        // Get workflow with highest priority condition
        return $matchingWorkflows->sortByDesc(function ($workflow) {
            return $workflow->conditions->max('priority');
        })->first();
    }

    /**
     * Transition workflow after Manager Pengaju approval
     * This preserves the Manager Pengaju approval and adds new steps
     */
    public function transitionWorkflowAfterManagerApproval(PumRequest $request): void
    {
        // Get the appropriate workflow based on amount and category
        $newWorkflow = $this->getMatchingWorkflow($request->amount, $request->procurement_category);

        if (!$newWorkflow) {
            // No matching workflow, keep current workflow
            return;
        }

        // Update request with new workflow
        $request->update([
            'workflow_id' => $newWorkflow->id,
        ]);

        // Get Manager Pengaju approval (already completed)
        $managerApproval = $request->approvals()
            ->where('status', 'approved')
            ->orderBy('step_order')
            ->first();

        if (!$managerApproval) {
            throw new \Exception('Manager Pengaju approval not found');
        }

        // Delete pending approvals (from old workflow)
        $request->approvals()
            ->where('status', 'pending')
            ->delete();

        // Create new approval steps from the new workflow
        // Skip the first step (Manager Pengaju, already approved)
        $newSteps = $newWorkflow->steps()->where('order', '>', 1)->get();
        
        foreach ($newSteps as $step) {
            PumRequestApproval::create([
                'request_id' => $request->id,
                'step_id' => $step->id,
                'step_order' => $step->order,
                'status' => 'pending',
            ]);
        }

        // Update current step order to the first pending step
        $firstPendingStep = $request->approvals()
            ->where('status', 'pending')
            ->orderBy('step_order')
            ->first();

        if ($firstPendingStep) {
            $request->update([
                'current_step_order' => $firstPendingStep->step_order,
            ]);
        }
    }
}
