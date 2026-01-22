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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kendaraan_id')->constrained('kendaraan');
            $table->dateTime('waktu_masuk');
            $table->dateTime('waktu_keluar');
            $table->foreignId('tarif_tipe_kendaraan_id')->constrained('tarif_tipe_kendaraan');
            $table->integer('durasi_jam');
            $table->integer('biaya_total');
            $table->enum('status', ['masuk', 'keluar'])->default('masuk');
            $table->foreignId('area_parkir_id')->constrained('area_parkir');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
