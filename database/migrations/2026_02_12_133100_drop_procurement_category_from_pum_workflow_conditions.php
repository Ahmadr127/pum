<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if an index exists (works for both MySQL and PostgreSQL)
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'mysql') {
            $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return !empty($result);
        } elseif ($driver === 'pgsql') {
            $result = DB::select("
                SELECT 1 
                FROM pg_indexes 
                WHERE tablename = ? 
                AND indexname = ?
            ", [$table, $indexName]);
            return !empty($result);
        }
        
        return false;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column exists before attempting to drop
        if (Schema::hasColumn('pum_workflow_conditions', 'procurement_category')) {
            // Check if index exists before dropping
            if ($this->indexExists('pum_workflow_conditions', 'pum_workflow_conditions_procurement_category_amount_min_amount_')) {
                Schema::table('pum_workflow_conditions', function (Blueprint $table) {
                    $table->dropIndex('pum_workflow_conditions_procurement_category_amount_min_amount_');
                });
            }
            
            // Alternative: Check for shorter index name (in case it was created with pum_wf_cond_cat_amt_idx)
            if ($this->indexExists('pum_workflow_conditions', 'pum_wf_cond_cat_amt_idx')) {
                Schema::table('pum_workflow_conditions', function (Blueprint $table) {
                    $table->dropIndex('pum_wf_cond_cat_amt_idx');
                });
            }
            
            // Drop the column
            Schema::table('pum_workflow_conditions', function (Blueprint $table) {
                $table->dropColumn('procurement_category');
            });
        }
        
        // Create new index if not exists
        if (!$this->indexExists('pum_workflow_conditions', 'pum_wf_conditions_amount_idx')) {
            Schema::table('pum_workflow_conditions', function (Blueprint $table) {
                $table->index(['amount_min', 'amount_max'], 'pum_wf_conditions_amount_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new index if exists
        if ($this->indexExists('pum_workflow_conditions', 'pum_wf_conditions_amount_idx')) {
            Schema::table('pum_workflow_conditions', function (Blueprint $table) {
                $table->dropIndex('pum_wf_conditions_amount_idx');
            });
        }
        
        // Add the column back if not exists
        if (!Schema::hasColumn('pum_workflow_conditions', 'procurement_category')) {
            Schema::table('pum_workflow_conditions', function (Blueprint $table) {
                $table->enum('procurement_category', ['barang_baru', 'peremajaan'])->nullable()->after('workflow_id');
            });
        }
        
        // Recreate the old index if not exists
        if (!$this->indexExists('pum_workflow_conditions', 'pum_wf_cond_cat_amt_idx')) {
            Schema::table('pum_workflow_conditions', function (Blueprint $table) {
                $table->index(['procurement_category', 'amount_min', 'amount_max'], 'pum_wf_cond_cat_amt_idx');
            });
        }
    }
};
