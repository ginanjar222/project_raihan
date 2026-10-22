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
        Schema::create('absensi_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guru_id')->nullable(); // <-- dibuat nullable agar tidak error jika guru_id kosong
            $table->unsignedBigInteger('absensi_id')->nullable(); // foto tetap tersimpan meski absensi gagal
            $table->string('uid'); // UID kartu RFID
            $table->string('photo_path');
            $table->enum('capture_type', ['masuk', 'keluar', 'failed_attempt'])->default('masuk');
            $table->text('capture_context')->nullable(); // info tambahan saat foto diambil
            $table->timestamp('captured_at');
            $table->timestamps();

            // relasi dengan tabel guru & absensi
            $table->foreign('guru_id')->references('id')->on('guru')->onDelete('set null');
            $table->foreign('absensi_id')->references('id')->on('absensi')->onDelete('set null');

            // index untuk optimasi pencarian
            $table->index(['guru_id', 'captured_at']);
            $table->index(['uid', 'captured_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_photos');
    }
};
