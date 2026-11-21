<?php

namespace App\Http\Controllers;

use App\Models\HasilSaw;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Halaman publik (tanpa login): ranking penerima per periode 6 bulan.
     */
    public function index(Request $request)
    {
        // Daftar periode yang sudah punya hasil tersimpan, terbaru di atas
        $periodOptions = HasilSaw::select('tahun', 'periode')
            ->distinct()
            ->orderByDesc('tahun')
            ->orderByDesc('periode')
            ->get()
            ->map(fn ($r) => [
                'value' => "{$r->tahun}-{$r->periode}",
                'label' => ($r->periode == 1 ? 'Januari - Juni' : 'Juli - Desember') . ' ' . $r->tahun,
            ]);

        // Format yang diterima hanya "YYYY-1" atau "YYYY-2"
        $selected = $request->query('periode_tahun');
        if (!is_string($selected) || !preg_match('/^\d{4}-[12]$/', $selected)) {
            $selected = null;
        }

        // Kalau belum memilih, tampilkan periode terbaru
        $selected ??= $periodOptions->first()['value'] ?? null;

        $hasil = collect();

        if ($selected) {
            [$tahun, $periode] = explode('-', $selected);

            // Hanya ambil kolom yang perlu ditampilkan (data pribadi warga tidak ikut dimuat)
            $hasil = HasilSaw::with('alternatif:id,name')
                ->where('tahun', (int) $tahun)
                ->where('periode', (int) $periode)
                ->orderBy('ranking')
                ->orderBy('id')
                ->get();
        }

        return view('home.index', compact('periodOptions', 'selected', 'hasil'));
    }
}