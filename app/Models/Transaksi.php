<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use SoftDeletes;
    protected $table = 'transaksi';
    protected $fillable = [
        'kode',
        'kendaraan_id',
        'waktu_masuk',
        'waktu_keluar',
        'tarif_tipe_kendaraan_id',
        'durasi_jam',
        'biaya_total',
        'status',
        'metode_pembayaran_id',
        'area_parkir_id',
        'user_id'
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    public function tarifTipeKendaraan()
    {
        return $this->belongsTo(TarifTipeKendaraan::class, 'tarif_tipe_kendaraan_id');
    }

    public function areaParkir()
    {
        return $this->belongsTo(AreaParkir::class, 'area_parkir_id');
    }

    public function metodePembayaran()
{
    return $this->belongsTo(MetodePembayaran::class);
}

}
