<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KendaraanTipe extends Model
{
    use SoftDeletes;
    protected $table = 'kendaraan_tipe';

    protected $fillable = ['tipe_kendaraan'];

        public function tarifTipeKendaraans()
    {
        return $this->hasMany(TarifTipeKendaraan::class, 'tipe_kendaraan_id');
    }
}
