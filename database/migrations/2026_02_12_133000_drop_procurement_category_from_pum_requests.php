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
        Schema::table('pum_requests', function (Blueprint $table) {
            $table->dropColumn('procurement_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pum_requests', function (Blueprint $table) {
            $table->enum('procurement_category', ['barang_baru', 'peremajaan'])->nullable()->after('amount');
        });
    }
};
