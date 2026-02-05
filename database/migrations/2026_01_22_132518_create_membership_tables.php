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
        Schema::create('membership_tier', function (Blueprint $table) {
            $table->id();
            $table->string('membership_tier');
            $table->float('diskon');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('membership', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('membership_tier_id')->constrained('membership_tier');
            $table->dateTime('pembaruan_terakhir');
            $table->dateTime('kadaluarsa');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('membership_kendaraan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')->constrained('membership');
            $table->foreignId('kendaraan_id')->constrained('kendaraan');
            $table->foreignId('area_parkir_id')->nullable()->constrained('area_parkir');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_kendaraan');
        Schema::dropIfExists('membership');
        Schema::dropIfExists('membership_tier');
    }
};
