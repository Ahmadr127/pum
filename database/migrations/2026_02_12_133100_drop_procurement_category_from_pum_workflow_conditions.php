<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pum_workflow_conditions', function (Blueprint $table) {
            // Drop old index that includes procurement_category
            $table->dropIndex('pum_workflow_conditions_procurement_category_amount_min_amount_');
            
            // Drop the column
            $table->dropColumn('procurement_category');
            
            // Create new index without procurement_category
            $table->index(['amount_min', 'amount_max'], 'pum_wf_conditions_amount_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pum_workflow_conditions', function (Blueprint $table) {
            // Drop the new index
            $table->dropIndex('pum_wf_conditions_amount_idx');
            
            // Add the column back
            $table->enum('procurement_category', ['barang_baru', 'peremajaan'])->nullable()->after('workflow_id');
            
            // Recreate the old index
            $table->index(['procurement_category', 'amount_min', 'amount_max'], 'pum_workflow_conditions_procurement_category_amount_min_amount_');
        });
    }
};
