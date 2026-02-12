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
        Schema::create('pum_workflow_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('pum_approval_workflows')->cascadeOnDelete();
            $table->enum('procurement_category', ['barang_baru', 'peremajaan'])->nullable();
            $table->decimal('amount_min', 15, 2)->nullable();
            $table->decimal('amount_max', 15, 2)->nullable();
            $table->integer('priority')->default(0); // Higher priority = checked first
            $table->timestamps();
            
            $table->index(['procurement_category', 'amount_min', 'amount_max'], 'pum_wf_cond_cat_amt_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pum_workflow_conditions');
    }
};
