<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaParkirDetail extends Model
{
    use SoftDeletes;
    protected $table = 'area_parkir_detail';

    protected $fillable = [
        'area_parkir_id',
        'tipe_kendaraan_id',
        'kapasitas',
        'terisi',
    ];

    public function areaParkir()
    {
        return $this->belongsTo(AreaParkir::class, 'area_parkir_id');
    }

    public function tipeKendaraan()
    {
        return $this->belongsTo(KendaraanTipe::class, 'tipe_kendaraan_id');
    }
}
