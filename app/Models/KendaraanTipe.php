<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipeKendaraan extends Model
{
    use SoftDeletes;
    protected $table = 'kendaraan_tipe';

    protected $fillable = ['kode','tipe_kendaraan'];

}
