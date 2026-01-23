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
        ];

        public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }
        public function kendaraan()
    {
        return $this->belongsTo(MembershipTier::class, 'kendaraan_id');
    }
}
