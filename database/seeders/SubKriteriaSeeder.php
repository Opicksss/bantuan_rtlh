<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubKriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // K1 - Kondisi Atap (Benefit)
            [
                'kode' => 'K1',
                'nama' => 'Rusak Berat',
                'keterangan' => 'Banyak genteng pecah, bocor merata saat hujan, dan kayu rangka mulai lapuk',
                'bobot' => 4,
            ],
            [
                'kode' => 'K1',
                'nama' => 'Rusak Sedang',
                'keterangan' => 'Sebagian genteng retak sehingga rembes di beberapa titik',
                'bobot' => 3,
            ],
            [
                'kode' => 'K1',
                'nama' => 'Rusak Ringan',
                'keterangan' => 'Genteng lengkap dan tidak bocor, namun permukaan sudah usang',
                'bobot' => 2,
            ],
            [
                'kode' => 'K1',
                'nama' => 'Layak/Baik',
                'keterangan' => 'Genteng utuh, terpasang rapi, kokoh, dan tidak bocor',
                'bobot' => 1,
            ],

            // K2 - Kondisi Dinding (Benefit)
            [
                'kode' => 'K2',
                'nama' => 'Rusak Berat',
                'keterangan' => 'Tembok retak besar, lembab, plesteran rontok/rapuh',
                'bobot' => 4,
            ],
            [
                'kode' => 'K2',
                'nama' => 'Rusak Sedang',
                'keterangan' => 'Tembok belum diplester sama sekali',
                'bobot' => 3,
            ],
            [
                'kode' => 'K2',
                'nama' => 'Rusak Ringan',
                'keterangan' => 'Tembok sudah diplester halus, cat mengelupas atau berlumut ringan',
                'bobot' => 2,
            ],
            [
                'kode' => 'K2',
                'nama' => 'Layak/Baik',
                'keterangan' => 'Tembok dengan cat yang sudah bagus',
                'bobot' => 1,
            ],

            // K3 - Kondisi Lantai (Benefit)
            [
                'kode' => 'K3',
                'nama' => 'Rusak Berat',
                'keterangan' => 'Lantai semen hancur/berlubang parah',
                'bobot' => 4,
            ],
            [
                'kode' => 'K3',
                'nama' => 'Rusak Sedang',
                'keterangan' => 'Plesteran semen kasar, retak halus',
                'bobot' => 3,
            ],
            [
                'kode' => 'K3',
                'nama' => 'Rusak Ringan',
                'keterangan' => 'Keramik tegel lama bagus ataupun rusak',
                'bobot' => 2,
            ],
            [
                'kode' => 'K3',
                'nama' => 'Layak/Baik',
                'keterangan' => 'Keramik modern',
                'bobot' => 1,
            ],

            // K4 - Pendapatan Bulanan (Cost)
            [
                'kode' => 'K4',
                'nama' => '< 300.000',
                'keterangan' => 'Penghasilan sangat rendah',
                'bobot' => 1,
            ],
            [
                'kode' => 'K4',
                'nama' => '300-800',
                'keterangan' => 'Penghasilan masih cukup rendah',
                'bobot' => 2,
            ],
            [
                'kode' => 'K4',
                'nama' => '800-1.500',
                'keterangan' => 'Penghasilan menengah',
                'bobot' => 3,
            ],
            [
                'kode' => 'K4',
                'nama' => '> 1.500.000',
                'keterangan' => 'Penghasilan layak',
                'bobot' => 4,
            ],

            // K5 - Jumlah Tanggungan (Benefit)
            [
                'kode' => 'K5',
                'nama' => '> 5 Orang',
                'keterangan' => 'Jumlah tanggungan sangat banyak, beban ekonomi keluarga berat',
                'bobot' => 4,
            ],
            [
                'kode' => 'K5',
                'nama' => '3-4 Orang',
                'keterangan' => 'Jumlah tanggungan banyak, cukup berat',
                'bobot' => 3,
            ],
            [
                'kode' => 'K5',
                'nama' => '2 Orang',
                'keterangan' => 'Jumlah tanggungan sedang, masih terkendali',
                'bobot' => 2,
            ],
            [
                'kode' => 'K5',
                'nama' => '0-1 Orang',
                'keterangan' => 'Jumlah tanggungan sedikit, beban ringan',
                'bobot' => 1,
            ],

            // K6 - Fasilitas Sanitasi MCK (Benefit)
            [
                'kode' => 'K6',
                'nama' => 'Menumpang',
                'keterangan' => 'Tidak memiliki MCK sendiri, masih menumpang tetangga/keluarga',
                'bobot' => 4,
            ],
            [
                'kode' => 'K6',
                'nama' => 'MCK Darurat',
                'keterangan' => 'Memiliki kamar mandi dan toilet sederhana tanpa septic tank atau bak rusak',
                'bobot' => 3,
            ],
            [
                'kode' => 'K6',
                'nama' => 'MCK Permanen Sederhana',
                'keterangan' => 'Memiliki kamar mandi dan toilet pribadi plester semen dengan septic tank',
                'bobot' => 2,
            ],
            [
                'kode' => 'K6',
                'nama' => 'MCK Permanen Layak',
                'keterangan' => 'Memiliki kamar mandi dan toilet pribadi keramik lengkap dengan septic tank',
                'bobot' => 1,
            ],
        ];

        foreach ($data as $item) {

            // Cari ID kriteria berdasarkan kode
            $kriteria = DB::table('kriterias')
                ->where('kode', $item['kode'])
                ->first();

            if (!$kriteria) {
                throw new \Exception(
                    "Kriteria {$item['kode']} tidak ditemukan."
                );
            }

            // Simpan subkriteria
            DB::table('sub_kriterias')->updateOrInsert(
                [
                    'kriteria_id' => $kriteria->id,
                    'nama' => $item['nama'],
                ],
                [
                    'keterangan' => $item['keterangan'],
                    'bobot' => $item['bobot'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
