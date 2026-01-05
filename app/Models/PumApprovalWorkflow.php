<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PumApprovalWorkflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Get all steps for this workflow
     */
    public function steps()
    {
        return $this->hasMany(PumApprovalStep::class, 'workflow_id')->orderBy('order');
    }

    /**
     * Get the default workflow
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->where('is_active', true)->first()
            ?? static::where('is_active', true)->first();
    }

    /**
     * Set this workflow as default (and unset others)
     */
    public function setAsDefault()
    {
        static::where('is_default', true)->update(['is_default' => false]);
        $this->update(['is_default' => true]);
    }

    /**
     * Scope for active workflows
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get total required steps count
     */
    public function getRequiredStepsCountAttribute()
    {
        return $this->steps()->where('is_required', true)->count();
    }
}
