<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlternatifSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode' => 'A001',
                'NIK' => '9999999999990001',
                'name' => 'Suhaini',
                'alamat' => 'Dusun Barat, Desa Contoh, Sumenep',
                'nomor' => '081234567801',
                'status' => 'Belum Verifikasi',
            ],
            [
                'kode' => 'A002',
                'NIK' => '9999999999990002',
                'name' => 'Hariani',
                'alamat' => 'Dusun Timur, Desa Contoh, Sumenep',
                'nomor' => '081234567802',
                'status' => 'Belum Verifikasi',
            ],
            [
                'kode' => 'A003',
                'NIK' => '9999999999990003',
                'name' => 'Samsuri',
                'alamat' => 'Dusun Selatan, Desa Contoh, Sumenep',
                'nomor' => '081234567803',
                'status' => 'Belum Verifikasi',
            ],
            [
                'kode' => 'A004',
                'NIK' => '9999999999990004',
                'name' => 'Miatun',
                'alamat' => 'Dusun Utara, Desa Contoh, Sumenep',
                'nomor' => '081234567804',
                'status' => 'Belum Verifikasi',
            ],
            [
                'kode' => 'A005',
                'NIK' => '9999999999990005',
                'name' => 'Santoso',
                'alamat' => 'Dusun Tengah, Desa Contoh, Sumenep',
                'nomor' => '081234567805',
                'status' => 'Belum Verifikasi',
            ],
        ];

        foreach ($data as $item) {
            DB::table('alternatifs')->updateOrInsert(
                ['kode' => $item['kode']],
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
