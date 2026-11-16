<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alternatif extends Model
{
        protected $table = 'alternatifs';

    protected $fillable = ['kode','NIK', 'name', 'alamat', 'nomor'];

    protected $guarded = ['id'];

    // public function rekaps()
    // {
    //     return $this->hasMany(Rekap::class);
    // }
    // public function hasilTopsis()
    // {
    //     return $this->hasMany(hasilTopsis::class);
    // }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // protected static function booted()
    // {
    //     static::created(function ($kurir) {
    //         $kurir->update([
    //             'kurir_id' => 'KRR' . str_pad($kurir->id, 6, '0', STR_PAD_LEFT)
    //         ]);
    //     });
    // }
}
