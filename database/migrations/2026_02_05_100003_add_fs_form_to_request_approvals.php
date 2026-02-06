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
        Schema::table('pum_request_approvals', function (Blueprint $table) {
            $table->string('fs_form_path')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pum_request_approvals', function (Blueprint $table) {
            $table->dropColumn('fs_form_path');
        });
    }
};
