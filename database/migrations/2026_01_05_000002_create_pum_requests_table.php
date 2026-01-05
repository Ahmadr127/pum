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
        Schema::create('pum_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Format: 00001/ADV/PNJ/01/2026
            $table->foreignId('requester_id')->constrained('users');
            $table->date('request_date');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['new', 'pending', 'approved', 'rejected', 'fulfilled'])->default('new');
            $table->foreignId('workflow_id')->nullable()->constrained('pum_approval_workflows')->nullOnDelete();
            $table->integer('current_step_order')->nullable(); // Current approval step order
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'request_date']);
            $table->index('requester_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pum_requests');
    }
};
