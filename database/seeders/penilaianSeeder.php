<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class penilaianSeeder extends Seeder
{
    /**
     * Data dari Tabel 3.4 Nilai Alternatif (K1..K6).
     * Kunci = kode alternatif, isi = [K1, K2, K3, K4, K5, K6].
     */
    public function run(): void
    {
        $data = [
            'A1'  => [3, 3, 3, 2, 3, 2],
            'A2'  => [3, 3, 4, 2, 3, 2],
            'A3'  => [3, 4, 3, 1, 3, 2],
            'A4'  => [3, 3, 4, 1, 2, 3],
            'A5'  => [4, 3, 3, 2, 3, 3],
            'A6'  => [4, 4, 3, 1, 4, 4],
            'A7'  => [3, 3, 3, 2, 3, 1],
            'A8'  => [4, 3, 4, 1, 3, 4],
            'A9'  => [3, 3, 3, 1, 3, 2],
            'A10' => [3, 3, 3, 2, 2, 2],
        ];

        $kriteria   = DB::table('kriterias')->pluck('id', 'kode');   // ['K1' => 1, ...]
        $alternatif = DB::table('alternatifs')->pluck('id', 'kode'); // ['A1' => 1, ...]

        foreach (['K1', 'K2', 'K3', 'K4', 'K5', 'K6'] as $kode) {
            if (!$kriteria->has($kode)) {
                throw new \RuntimeException("Kriteria {$kode} belum ada. Jalankan seeder kriteria dulu.");
            }
        }

        DB::transaction(function () use ($data, $kriteria, $alternatif) {
            foreach ($data as $kodeAlt => $nilaiList) {
                if (!$alternatif->has($kodeAlt)) {
                    throw new \RuntimeException("Alternatif {$kodeAlt} belum ada. Jalankan AlternatifSeeder dulu.");
                }

                $alternatifId = $alternatif[$kodeAlt];

                foreach ($nilaiList as $i => $nilai) {
                    $kriteriaId = $kriteria['K' . ($i + 1)];

                    DB::table('penilaians')->updateOrInsert(
                        [
                            'alternatif_id' => $alternatifId,
                            'kriteria_id'   => $kriteriaId,
                        ],
                        [
                            'nilai'      => $nilai,
                            'bukti'      => null,
                            'date'       => now()->toDateString(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                // penilaian sudah lengkap -> status terverifikasi
                DB::table('alternatifs')
                    ->where('id', $alternatifId)
                    ->update(['status' => 'Terverifikasi', 'updated_at' => now()]);
            }
        });
    }
}