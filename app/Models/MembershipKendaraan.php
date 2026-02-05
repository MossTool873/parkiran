<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipKendaraan extends Model
{
    use SoftDeletes;
    protected $table = 'membership_kendaraan';

    protected $fillable = [
        'membership_id',
        'kendaraan_id',
        'area_parkir_id'
        ];
    public function areaParkir(){
        return $this->belongsTo(AreaParkir::class, 'area_parkir_id');

    }
        public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }
public function kendaraan()
{
    return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
}

}
