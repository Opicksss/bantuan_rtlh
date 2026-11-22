<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VerifikatorDashboardController extends Controller
{
    public function dashboard()
    {
        $totalKriteria   = Kriteria::count();
        $totalAlternatif = Alternatif::count();

        // ---- Periode berjalan (1 = Jan-Jun, 2 = Jul-Des) ----
        $tahunIni   = now()->year;
        $periodeIni = now()->month <= 6 ? 1 : 2;

        if ($periodeIni === 1) {
            $awal  = Carbon::create($tahunIni, 1, 1)->toDateString();
            $akhir = Carbon::create($tahunIni, 6, 30)->toDateString();
        } else {
            $awal  = Carbon::create($tahunIni, 7, 1)->toDateString();
            $akhir = Carbon::create($tahunIni, 12, 31)->toDateString();
        }

        // Jumlah kriteria yang sudah dinilai per warga pada periode berjalan
        // hasil: [alternatif_id => jumlah_kriteria_terisi]
        $terisi = Penilaian::whereBetween('date', [$awal, $akhir])
            ->select('alternatif_id', DB::raw('COUNT(DISTINCT kriteria_id) as jml'))
            ->groupBy('alternatif_id')
            ->pluck('jml', 'alternatif_id');

        $lengkap = $totalKriteria > 0
            ? $terisi->filter(fn ($j) => $j >= $totalKriteria)->count()
            : 0;

        $sebagian = $terisi->filter(fn ($j) => $j > 0 && $j < $totalKriteria)->count();

        $belumMulai = max($totalAlternatif - $lengkap - $sebagian, 0);

        $belumLengkap = $totalAlternatif - $lengkap;

        $persen = $totalAlternatif > 0 ? round(($lengkap / $totalAlternatif) * 100) : 0;

        // Daftar warga yang masih perlu dinilai (maks. 10, urut sesuai pendaftaran)
        $perluDinilai = Alternatif::orderBy('id')
            ->get()
            ->filter(fn ($a) => ($terisi[$a->id] ?? 0) < $totalKriteria)
            ->take(10)
            ->map(function ($a) use ($terisi) {
                $a->terisi = (int) ($terisi[$a->id] ?? 0);

                return $a;
            })
            ->values();

        return view('dashboard.verifikator', compact(
            'totalKriteria',
            'totalAlternatif',
            'lengkap',
            'sebagian',
            'belumMulai',
            'belumLengkap',
            'persen',
            'tahunIni',
            'periodeIni',
            'perluDinilai'
        ));
    }
}