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
        // Workflow templates that can be customized
        Schema::create('pum_approval_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Steps within each workflow
        Schema::create('pum_approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('pum_approval_workflows')->cascadeOnDelete();
            $table->integer('order'); // Step order
            $table->string('name'); // Step name (e.g., "Manager Approval")
            $table->enum('approver_type', ['role', 'user', 'organization_head']);
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_required')->default(true);
            $table->timestamps();
            
            $table->index(['workflow_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pum_approval_steps');
        Schema::dropIfExists('pum_approval_workflows');
    }
};
