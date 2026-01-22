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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarif_tipe_kendaraan');
    }
};
