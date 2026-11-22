<?php

namespace App\Http\Controllers;

use App\Models\HasilSaw;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Halaman publik (tanpa login): ranking penerima per tahun.
     */
    public function index(Request $request)
    {
        // Daftar tahun yang sudah punya hasil tersimpan, terbaru di atas
        $tahunOptions = HasilSaw::select('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        // Format yang diterima hanya 4 digit angka, mis. "2026"
        $selected = $request->query('tahun');
        if (!is_string($selected) || !preg_match('/^\d{4}$/', $selected)) {
            $selected = null;
        }

        // Kalau belum memilih, tampilkan tahun terbaru
        $selected = $selected !== null ? (int) $selected : $tahunOptions->first();

        $hasil = collect();

        if ($selected) {
            // Hanya ambil kolom yang perlu ditampilkan (data pribadi warga tidak ikut dimuat)
            $hasil = HasilSaw::with('alternatif:id,name')
                ->where('tahun', $selected)
                ->orderBy('ranking')
                ->orderBy('id')
                ->get();
        }

        return view('home.index', compact('tahunOptions', 'selected', 'hasil'));
    }
}