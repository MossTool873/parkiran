<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kendaraan extends Model
{
    use SoftDeletes;
    protected $table = 'kendaraan';
    protected $fillable = [
        'plat_nomor',
        'warna',
        'tipe_kendaraan_id',
        'area_parkir_id'
    ];
    public function areaParkir()
    {
        return $this->belongsTo(AreaParkir::class, 'area_parkir_id');
    }

    public function tipeKendaraan()
    {
        return $this->belongsTo(KendaraanTipe::class, 'tipe_kendaraan_id');
    }

    public function memberships()
    {
        return $this->belongsToMany(
            Membership::class,
            'membership_kendaraan',
            'kendaraan_id',
            'membership_id'
        );
    }
    public function membershipKendaraan()
    {
        return $this->hasOne(MembershipKendaraan::class)
            ->whereHas('membership', function ($q) {
                $q->whereNull('kadaluarsa')
                    ->orWhere('kadaluarsa', '>=', now());
            });
    }
}
