<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaParkir extends Model
{
    use SoftDeletes;
    protected $table = 'area_parkir';

    protected $fillable = [
        'nama_area',
        'lokasi',
        'total_kapasitas',
    ];

    public function detailKapasitas() {
        return $this->hasMany(AreaParkirDetail::class,'area_parkir_id');
    }
    public function kendaraan()
{
    return $this->hasMany(Kendaraan::class, 'area_parkir_id');
}

}
