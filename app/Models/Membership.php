<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membership extends Model
{
    use SoftDeletes;
    protected $table = 'membership';

    protected $fillable = [
        'nama',
        'membership_tier_id',
        'pembaruan_terakhir',
        'kadaluarsa'
    ];

    public function membershipTier()
    {
        return $this->belongsTo(MembershipTier::class, 'membership_tier_id');
    }

    public function membershipKendaraan()
    {
        return $this->hasMany(MembershipKendaraan::class, 'membership_id');
    }

        public function kendaraans()
    {
        return $this->belongsToMany(
            Kendaraan::class,
            'membership_kendaraan',
            'membership_id',
            'kendaraan_id'
        );
    }
}
