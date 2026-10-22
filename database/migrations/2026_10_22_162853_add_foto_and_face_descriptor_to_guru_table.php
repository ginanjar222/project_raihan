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
        Schema::table('guru', function (Blueprint $table) {
            if (!Schema::hasColumn('guru', 'foto')) {
                $table->string('foto')->nullable()->after('jabatan');
            }
            if (!Schema::hasColumn('guru', 'face_descriptor')) {
                $table->longText('face_descriptor')->nullable()->after('foto');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('guru', 'foto')) $columns[] = 'foto';
            if (Schema::hasColumn('guru', 'face_descriptor')) $columns[] = 'face_descriptor';
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
