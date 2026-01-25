<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TarifTipeKendaraan extends Model
{
    use SoftDeletes;
    protected $table = 'tarif_tipe_kendaraan';
    protected $fillable = [
        'tipe_kendaraan_id',
        'tarif_perjam'
    ];

    public function tipeKendaraan()
    {
        return $this->belongsTo(KendaraanTipe::class, 'tipe_kendaraan_id');
    }
}
