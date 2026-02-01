<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetodePembayaran extends Model
{
    use SoftDeletes;

    protected $table = 'metode_pembayaran';
    protected $fillable = ['nama_metode'];
}
