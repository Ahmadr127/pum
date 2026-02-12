<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pum_workflow_conditions', function (Blueprint $table) {
            // Check if column exists before attempting to drop
            if (Schema::hasColumn('pum_workflow_conditions', 'procurement_category')) {
                // Check if index exists using raw SQL
                $indexExists = DB::select("
                    SELECT 1 
                    FROM pg_indexes 
                    WHERE tablename = 'pum_workflow_conditions' 
                    AND indexname = 'pum_workflow_conditions_procurement_category_amount_min_amount_'
                ");
                
                if (!empty($indexExists)) {
                    $table->dropIndex('pum_workflow_conditions_procurement_category_amount_min_amount_');
                }
                
                // Drop the column
                $table->dropColumn('procurement_category');
            }
        });
        
        // Create new index if not exists (outside the closure to avoid conflicts)
        $newIndexExists = DB::select("
            SELECT 1 
            FROM pg_indexes 
            WHERE tablename = 'pum_workflow_conditions' 
            AND indexname = 'pum_wf_conditions_amount_idx'
        ");
        
        if (empty($newIndexExists)) {
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
        $newIndexExists = DB::select("
            SELECT 1 
            FROM pg_indexes 
            WHERE tablename = 'pum_workflow_conditions' 
            AND indexname = 'pum_wf_conditions_amount_idx'
        ");
        
        if (!empty($newIndexExists)) {
            Schema::table('pum_workflow_conditions', function (Blueprint $table) {
                $table->dropIndex('pum_wf_conditions_amount_idx');
            });
        }
        
        Schema::table('pum_workflow_conditions', function (Blueprint $table) {
            // Add the column back if not exists
            if (!Schema::hasColumn('pum_workflow_conditions', 'procurement_category')) {
                $table->enum('procurement_category', ['barang_baru', 'peremajaan'])->nullable()->after('workflow_id');
            }
        });
        
        // Recreate the old index if not exists
        $oldIndexExists = DB::select("
            SELECT 1 
            FROM pg_indexes 
            WHERE tablename = 'pum_workflow_conditions' 
            AND indexname = 'pum_workflow_conditions_procurement_category_amount_min_amount_'
        ");
        
        if (empty($oldIndexExists)) {
            Schema::table('pum_workflow_conditions', function (Blueprint $table) {
                $table->index(['procurement_category', 'amount_min', 'amount_max'], 'pum_workflow_conditions_procurement_category_amount_min_amount_');
            });
        }
    }
};
