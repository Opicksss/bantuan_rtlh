<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $kriterias = [
            [
                'kode' => 'K1',
                'nama' => 'Kondisi Atap',
                'sifat' => 'benefit',
                'bobot' => 20,
            ],
            [
                'kode' => 'K2',
                'nama' => 'Kondisi Dinding',
                'sifat' => 'benefit',
                'bobot' => 20,
            ],
            [
                'kode' => 'K3',
                'nama' => 'Kondisi Lantai',
                'sifat' => 'benefit',
                'bobot' => 20,
            ],
            [
                'kode' => 'K4',
                'nama' => 'Pendapatan Bulanan',
                'sifat' => 'cost',
                'bobot' => 15,
            ],
            [
                'kode' => 'K5',
                'nama' => 'Jumlah Tanggungan',
                'sifat' => 'benefit',
                'bobot' => 10,
            ],
            [
                'kode' => 'K6',
                'nama' => 'Fasilitas Sanitasi (MCK)',
                'sifat' => 'benefit',
                'bobot' => 15,
            ],
        ];

        foreach ($kriterias as $kriteria) {
            DB::table('kriterias')->updateOrInsert(
                ['kode' => $kriteria['kode']],
                array_merge($kriteria, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
