<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\HasilSaw;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\SubKriteria;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // ---- Statistik utama ----
        $totalKriteria    = Kriteria::count();
        $totalSubKriteria = SubKriteria::count();
        $totalAlternatif  = Alternatif::count();
        $terverifikasi    = Alternatif::where('status', 'Terverifikasi')->count();
        $pending          = Alternatif::where('status', 'Pending')->count();

        // ---- Tahun berjalan (1 Jan - 31 Des) ----
        $tahunIni = now()->year;
        $awal     = Carbon::create($tahunIni, 1, 1)->toDateString();
        $akhir    = Carbon::create($tahunIni, 12, 31)->toDateString();

        // Alternatif yang penilaiannya lengkap (semua kriteria terisi) di tahun berjalan
        $lengkapIds = Penilaian::whereBetween('date', [$awal, $akhir])
            ->select('alternatif_id')
            ->groupBy('alternatif_id')
            ->havingRaw('COUNT(DISTINCT kriteria_id) = ?', [$totalKriteria])
            ->pluck('alternatif_id');

        // Warga terverifikasi yang belum dinilai lengkap -> belum ikut perhitungan SAW
        $belumDinilai = Alternatif::where('status', 'Terverifikasi')
            ->whereNotIn('id', $lengkapIds)
            ->count();

        $hasilTahunIniTersimpan = HasilSaw::where('tahun', $tahunIni)->exists();

        // ---- 3 peringkat teratas dari hasil tersimpan tahun terbaru ----
        $latest = HasilSaw::orderByDesc('tahun')->first();

        $terbaik = collect();
        if ($latest) {
            $terbaik = HasilSaw::with('alternatif')
                ->where('tahun', $latest->tahun)
                ->orderBy('ranking')
                ->orderBy('id')
                ->take(3)
                ->get();
        }

        return view('dashboard.dashboard', compact(
            'totalKriteria',
            'totalSubKriteria',
            'totalAlternatif',
            'terverifikasi',
            'pending',
            'belumDinilai',
            'tahunIni',
            'hasilTahunIniTersimpan',
            'terbaik'
        ));
    }
}