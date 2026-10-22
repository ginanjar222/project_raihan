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
        Schema::table('absensi', function (Blueprint $table) {
            if (!Schema::hasColumn('absensi', 'status_verifikasi')) {
                $table->string('status_verifikasi')->nullable()->after('status_keluar');
            }
            if (!Schema::hasColumn('absensi', 'catatan_verifikasi')) {
                $table->text('catatan_verifikasi')->nullable()->after('status_verifikasi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('absensi', 'status_verifikasi')) $columns[] = 'status_verifikasi';
            if (Schema::hasColumn('absensi', 'catatan_verifikasi')) $columns[] = 'catatan_verifikasi';
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
