<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PumWorkflowCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'procurement_category',
        'amount_min',
        'amount_max',
        'priority',
    ];

    protected $casts = [
        'amount_min' => 'decimal:2',
        'amount_max' => 'decimal:2',
        'priority' => 'integer',
    ];

    /**
     * Get the workflow this condition belongs to
     */
    public function workflow()
    {
        return $this->belongsTo(PumApprovalWorkflow::class, 'workflow_id');
    }

    /**
     * Check if this condition matches the given request parameters
     */
    public function matches($amount, $category): bool
    {
        // Check category match
        if ($this->procurement_category && $this->procurement_category !== $category) {
            return false;
        }

        // Check amount range
        if ($this->amount_min !== null && $amount < $this->amount_min) {
            return false;
        }

        if ($this->amount_max !== null && $amount > $this->amount_max) {
            return false;
        }

        return true;
    }

    /**
     * Scope for ordering by priority
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }
}
