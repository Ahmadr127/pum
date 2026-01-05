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
        Schema::create('pum_request_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('pum_requests')->cascadeOnDelete();
            $table->foreignId('step_id')->constrained('pum_approval_steps')->cascadeOnDelete();
            $table->integer('step_order'); // Order at time of creation
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            
            $table->index(['request_id', 'step_order']);
            $table->index(['approver_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pum_request_approvals');
    }
};
