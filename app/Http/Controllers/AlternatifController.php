<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use Illuminate\Http\Request;

class AlternatifController extends Controller
{
public function index()
    {
        $alternatifs = Alternatif::all();
        return view('alternatif.alternatif', compact('alternatifs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'NIK' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'alamat' => 'nullable|string|max:255',
                'nomor' => 'nullable|numeric|digits_between:10,20',
            ]);

            $lastNumber = Alternatif::count() + 1;
            $validated['kode'] = 'A' . $lastNumber;

            Alternatif::create($validated);
            return redirect()->back()->with('success', 'Alternatif berhasil di tambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan Alternatif. Silakan coba lagi.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Alternatif $alternatif)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Alternatif $alternatif)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'NIK' => 'required|string|max:255',
                'alamat' => 'nullable|string|max:255',
                'nomor' => 'nullable|numeric|digits_between:10,20',
            ]);

            $oldTanggalMasuk = $alternatif->tanggal_masuk;
            $alternatif->update($validated);

            return redirect()->back()->with('success', 'Alternatif berhasil di update');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupdate Alternatif. Silakan coba lagi.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alternatif $alternatif)
    {
        try {
            $deletedNumber = (int) str_replace('A', '', $alternatif->kode);

            // Hapus data
            $alternatif->delete();

            // Ambil semua data setelah kode yang dihapus
            $updateKode = Alternatif::whereRaw('CAST(SUBSTRING(kode, 3) AS UNSIGNED) > ?', [$deletedNumber])
                                             ->orderByRaw('CAST(SUBSTRING(kode, 3) AS UNSIGNED)')
                                             ->get();

            // Update ulang kode-kode setelahnya
            foreach ($updateKode as $item) {
                $currentNumber = (int) str_replace('A', '', $item->kode);
                $newNumber = $currentNumber - 1;
                $item->update(['kode' => 'A' . $newNumber]);
            }

            return redirect()->back()->with('success', 'Alternatif berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus Alternatif. Silakan coba lagi.');
        }
    }
}
