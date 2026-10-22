<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            
            $table->enum('status_masuk', ['hadir', 'terlambat', 'izin', 'sakit', 'alpa'])->default('hadir');
            $table->text('keterangan_masuk')->nullable();
            
            $table->enum('status_keluar', ['normal', 'cepat', 'izin', 'sakit'])->nullable();
            $table->text('keterangan_keluar')->nullable();
            
            $table->string('keterlambatan')->nullable(); // Format HH:MM
            
            $table->boolean('is_manual')->default(false);
            $table->string('created_by')->nullable();
            
            $table->timestamps();
            
            // Index untuk performa query
            $table->index(['guru_id', 'tanggal']);
            $table->index('tanggal');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('absensi');
    }
};
