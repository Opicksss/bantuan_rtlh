<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilSaw extends Model
{
    protected $table = 'hasil_saws';

    protected $fillable = [
        'alternatif_id',
        'tahun',
        'nilai_preferensi',
        'ranking',
    ];

    public function alternatif()
    {
        return $this->belongsTo(Alternatif::class);
    }
}