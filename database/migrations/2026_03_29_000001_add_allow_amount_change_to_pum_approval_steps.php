<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pum_approval_steps', function (Blueprint $table) {
            // Whether a releaser on this step is allowed to edit the nominal amount
            $table->boolean('allow_amount_change')->default(false)->after('is_upload_fs_required');
        });

        // Also add release_notes to pum_request_approvals so the changed amount note is searchable
        Schema::table('pum_request_approvals', function (Blueprint $table) {
            $table->decimal('released_amount', 15, 2)->nullable()->after('fs_form_path')
                  ->comment('Nominal yang diubah oleh releaser, null jika tidak diubah');
        });
    }

    public function down(): void
    {
        Schema::table('pum_approval_steps', function (Blueprint $table) {
            $table->dropColumn('allow_amount_change');
        });

        Schema::table('pum_request_approvals', function (Blueprint $table) {
            $table->dropColumn('released_amount');
        });
    }
};
