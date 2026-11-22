<?php

namespace App\Http\Controllers;

use App\Models\HasilSaw;
use Illuminate\Http\Request;

class PerangkinganController extends Controller
{
    /**
     * Halaman khusus perangkingan: menampilkan hasil SAW yang SUDAH TERSIMPAN per tahun.
     * (Perhitungan dan penyimpanan dilakukan di halaman Perhitungan SAW.)
     */
    public function index(Request $request)
    {
        $request->validate([
            'tahun' => 'nullable|integer|min:2000|max:2100',
        ]);

        // Daftar tahun yang sudah punya hasil tersimpan, terbaru di atas
        $tahunOptions = HasilSaw::select('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        // Kalau tahun tidak dipilih: pakai tahun terbaru yang punya hasil, atau tahun berjalan
        $tahun = $request->filled('tahun')
            ? (int) $request->input('tahun')
            : (int) ($tahunOptions->first() ?? now()->year);

        $hasil = HasilSaw::with('alternatif')
            ->where('tahun', $tahun)
            ->orderBy('ranking')
            ->orderBy('id')
            ->get();

        // Ringkasan nilai preferensi
        $statistik = [
            'total'     => $hasil->count(),
            'tertinggi' => $hasil->max('nilai_preferensi'),
            'terendah'  => $hasil->min('nilai_preferensi'),
            'rata'      => $hasil->avg('nilai_preferensi'),
        ];

        return view('perangkingan.index', compact('tahun', 'tahunOptions', 'hasil', 'statistik'));
    }
}