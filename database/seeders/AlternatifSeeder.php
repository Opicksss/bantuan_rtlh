<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlternatifSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan dan kode disamakan dengan Tabel 3.4 Nilai Alternatif
        $nama = [
            'A1'  => 'Suhaini',
            'A2'  => 'Suratun',
            'A3'  => 'Suyono',
            'A4'  => 'Hariani',
            'A5'  => 'Santoso',
            'A6'  => 'Miatun',
            'A7'  => 'Samsuri',
            'A8'  => 'Rukmaniyah',
            'A9'  => 'Rukaiyah',
            'A10' => 'Slamet',
        ];

        $dusun = ['Barat', 'Timur', 'Selatan', 'Utara', 'Tengah'];

        $i = 0;
        foreach ($nama as $kode => $name) {
            $i++;

            $item = [
                'kode'   => $kode,
                'NIK'    => '99999999999900' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'name'   => $name,
                'alamat' => 'Dusun ' . $dusun[($i - 1) % count($dusun)] . ', Desa Contoh, Sumenep',
                'nomor'  => '0812345678' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => 'Pending',
            ];

            DB::table('alternatifs')->updateOrInsert(
                ['kode' => $kode],
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}