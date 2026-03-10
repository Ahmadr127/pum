<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PumApprovalWorkflow;

/**
 * PUM Workflow API — Read-only list of active workflows.
 *
 * Routes:
 *   GET /api/pum/workflows  – List active workflows (for create-request form)
 */
class PumWorkflowApiController extends Controller
{
    /**
     * GET /api/pum/workflows
     * List active workflows with their steps. Used on mobile to populate
     * the workflow picker when creating a new PUM request.
     */
    public function index()
    {
        $workflows = PumApprovalWorkflow::active()->with('steps')->get();

        return response()->json(['status' => 'success', 'data' => $workflows]);
    }
}
