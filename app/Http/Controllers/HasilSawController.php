<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\HasilSaw;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilSawController extends Controller
{
    /**
     * Tampilkan perhitungan SAW per tahun (langsung dihitung, belum tentu tersimpan).
     */
    public function index(Request $request)
    {
        $request->validate([
            'tahun' => 'nullable|integer|min:2000|max:2100',
        ]);

        $tahun = (int) $request->input('tahun', now()->year);

        $data = $this->hitung($tahun);

        $tersimpan = HasilSaw::where('tahun', $tahun)->exists();

        return view('hasil-saw.index', array_merge($data, [
            'tahun'     => $tahun,
            'tersimpan' => $tersimpan,
        ]));
    }

    /**
     * Simpan hasil perhitungan ke tabel hasil_saws.
     * Dihitung ulang di server supaya tidak bergantung pada input dari browser.
     */
    public function simpan(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2000|max:2100',
        ]);

        $tahun = (int) $request->tahun;

        $data = $this->hitung($tahun);

        if (empty($data['hasil'])) {
            return back()->with('error', 'Tidak ada data yang bisa disimpan pada tahun ini.');
        }

        try {
            DB::transaction(function () use ($data, $tahun) {
                // timpa hasil lama di tahun yang sama
                HasilSaw::where('tahun', $tahun)->delete();

                $now  = now();
                $rows = [];
                foreach ($data['hasil'] as $row) {
                    $rows[] = [
                        'alternatif_id'    => $row['alternatif']->id,
                        'tahun'            => $tahun,
                        'nilai_preferensi' => $row['v'],
                        'ranking'          => $row['ranking'],
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ];
                }

                HasilSaw::insert($rows);
            });

            return back()->with('success', 'Hasil perhitungan SAW berhasil disimpan.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal menyimpan hasil: ' . $e->getMessage());
        }
    }

    /**
     * Hapus hasil tersimpan pada tahun tertentu.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2000|max:2100',
        ]);

        HasilSaw::where('tahun', (int) $request->tahun)->delete();

        return back()->with('success', 'Hasil tersimpan pada tahun ini berhasil dihapus.');
    }

    /**
     * Inti perhitungan SAW.
     *
     * 1. Matriks keputusan X  : nilai penilaian tiap alternatif x kriteria
     * 2. Normalisasi R        : benefit -> x / max ; cost -> min / x
     * 3. Preferensi V         : sum(w * r), w = bobot dinormalkan (total = 1)
     * 4. Ranking              : urut V terbesar
     */
    private function hitung(int $tahun): array
    {
        $kriteria = Kriteria::orderBy('id')->get();

        // Bobot dinormalkan, jadi aman walau diinput 0.30 atau 30 (persen)
        $totalBobot = (float) $kriteria->sum('bobot');
        $bobot = $kriteria->mapWithKeys(fn ($k) => [
            $k->id => $totalBobot > 0 ? (float) $k->bobot / $totalBobot : 0,
        ]);

        // Rentang tanggal: satu tahun penuh (1 Jan - 31 Des)
        $awal  = Carbon::create($tahun, 1, 1)->toDateString();
        $akhir = Carbon::create($tahun, 12, 31)->toDateString();

        $penilaian = Penilaian::whereBetween('date', [$awal, $akhir])
            ->whereIn('kriteria_id', $kriteria->pluck('id'))
            ->get()
            ->groupBy('alternatif_id');

        // Hanya alternatif Terverifikasi dengan penilaian lengkap di semua kriteria
        $alternatif = Alternatif::where('status', 'Terverifikasi')
            ->whereIn('id', $penilaian->keys())
            ->orderBy('kode')
            ->get()
            ->filter(fn ($a) => $penilaian->get($a->id)->count() === $kriteria->count())
            ->values();

        if ($alternatif->isEmpty() || $kriteria->isEmpty()) {
            return [
                'kriteria'    => $kriteria,
                'bobot'       => $bobot,
                'alternatif'  => $alternatif,
                'matriks'     => [],
                'minmax'      => [],
                'normalisasi' => [],
                'hasil'       => [],
            ];
        }

        // 1. Matriks keputusan
        $matriks = [];
        foreach ($alternatif as $alt) {
            $byKriteria = $penilaian->get($alt->id)->keyBy('kriteria_id');
            foreach ($kriteria as $k) {
                $matriks[$alt->id][$k->id] = (float) $byKriteria->get($k->id)->nilai;
            }
        }

        // Min / max per kriteria
        $minmax = [];
        foreach ($kriteria as $k) {
            $kolom = array_column($matriks, $k->id);
            $minmax[$k->id] = ['min' => min($kolom), 'max' => max($kolom)];
        }

        // 2. Normalisasi
        $normalisasi = [];
        foreach ($alternatif as $alt) {
            foreach ($kriteria as $k) {
                $x = $matriks[$alt->id][$k->id];

                if ($k->sifat === 'benefit') {
                    $max = $minmax[$k->id]['max'];
                    $r = $max > 0 ? $x / $max : 0;
                } else { // cost
                    $min = $minmax[$k->id]['min'];
                    $r = $x > 0 ? $min / $x : 0;
                }

                $normalisasi[$alt->id][$k->id] = $r;
            }
        }

        // 3. Nilai preferensi V
        $hasil = [];
        foreach ($alternatif as $alt) {
            $v = 0;
            foreach ($kriteria as $k) {
                $v += $bobot[$k->id] * $normalisasi[$alt->id][$k->id];
            }
            $hasil[] = [
                'alternatif' => $alt,
                'v'          => round($v, 4),
            ];
        }

        // 4. Ranking (nilai sama -> ranking sama)
        usort($hasil, fn ($a, $b) => $b['v'] <=> $a['v']);

        $rank = 0;
        $prev = null;
        foreach ($hasil as $i => &$row) {
            if ($prev === null || $row['v'] !== $prev) {
                $rank = $i + 1;
            }
            $row['ranking'] = $rank;
            $prev = $row['v'];
        }
        unset($row);

        return compact('kriteria', 'bobot', 'alternatif', 'matriks', 'minmax', 'normalisasi', 'hasil');
    }
}