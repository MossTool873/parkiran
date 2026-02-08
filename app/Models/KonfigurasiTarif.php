<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonfigurasiTarif extends Model
{
    use HasFactory;

    protected $table = 'konfigurasi_tarif';

    protected $fillable = [
        'persentase_tarif_perjam_lanjutan',
        'diskon_persen',
        'diskon_sampai',
        'diskon_aktif',
    ];

    protected $casts = [
        'diskon_aktif' => 'boolean',
        'diskon_sampai' => 'date',
    ];

    public function isDiskonBerlaku(): bool
    {
        if (!$this->diskon_aktif) {
            return false;
        }

        if ($this->diskon_sampai === null) {
            return false;
        }

        return now()->toDateString() <= $this->diskon_sampai->toDateString();
    }
}
