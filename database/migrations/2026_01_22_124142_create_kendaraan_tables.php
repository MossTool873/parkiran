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
        Schema::create('kendaraan_tipe', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('tipe_kendaraan');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();
            $table->string('plat_nomor')->unique();
            $table->string('warna');
            $table->foreignId('tipe_kendaraan_id')->constrained('kendaraan_tipe');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
        Schema::dropIfExists('kendaraan_tipe');
    }
};
