<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipTier extends Model
{
    use SoftDeletes;

    protected $table = 'membership_tier';
    protected $fillable = ['membership_tier','diskon'];

}
