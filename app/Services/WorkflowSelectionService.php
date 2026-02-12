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
     * Get matching workflow based on amount
     */
    public function getMatchingWorkflow($amount): ?PumApprovalWorkflow
    {
        $workflows = PumApprovalWorkflow::with('conditions')
            ->where('is_active', true)
            ->where('is_default', false) // Exclude default workflow
            ->get();

        // Find workflows that match conditions, ordered by priority
        $matchingWorkflows = $workflows->filter(function ($workflow) use ($amount) {
            return $workflow->matchesConditions($amount);
        });

        // Get workflow with highest priority condition
        return $matchingWorkflows->sortByDesc(function ($workflow) {
            return $workflow->conditions->max('priority');
        })->first();
    }


}
