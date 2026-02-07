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
        Schema::table('pum_approval_steps', function (Blueprint $table) {
            $table->enum('type', ['approval', 'purchasing', 'release'])->default('approval')->after('approver_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pum_approval_steps', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
