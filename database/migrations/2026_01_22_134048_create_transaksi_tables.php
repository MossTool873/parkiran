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
        Schema::create('metode_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_metode');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode');

            $table->foreignId('kendaraan_id')->constrained('kendaraan');

            // MEMBER (nullable)
            $table->foreignId('member_id')->nullable()->constrained('membership');

            $table->dateTime('waktu_masuk');
            $table->dateTime('waktu_keluar')->nullable();
            $table->foreignId('tarif_tipe_kendaraan_id')->constrained('tarif_tipe_kendaraan');
            $table->integer('durasi_menit')->nullable();
            $table->integer('biaya')->nullable();
            $table->integer('biaya_total')->nullable();
            $table->foreignId('metode_pembayaran_id')->nullable()->constrained('metode_pembayaran');
            $table->enum('status', ['masuk', 'keluar'])->default('masuk');
            $table->foreignId('area_parkir_id')->constrained('area_parkir');
            $table->foreignId('membership_id')->constrained('membership');
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
        Schema::dropIfExists('metode_pembayaran');
    }
};
