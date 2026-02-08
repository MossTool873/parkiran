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
        Schema::create('tarif_tipe_kendaraan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipe_kendaraan_id')->constrained('kendaraan_tipe');
            $table->integer('tarif_perjam');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tarif_durasi', function (Blueprint $table) {
            $table->id();

            $table->integer('batas_jam');
            $table->integer('persentase');

            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('konfigurasi_tarif', function (Blueprint $table) {
            $table->id();

            $table->integer('persentase_tarif_perjam_lanjutan');
            $table->integer('diskon_persen')->default(0);
            $table->date('diskon_sampai')->nullable();
            $table->boolean('diskon_aktif')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_tarif');
        Schema::dropIfExists('tarif_tipe_kendaraan');
        Schema::dropIfExists('tarif_durasi');
    }
};
