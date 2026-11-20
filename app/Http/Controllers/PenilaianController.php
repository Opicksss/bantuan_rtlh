<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PenilaianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $kriteria = Kriteria::with('subKriterias')->orderBy('id', 'asc')->get();

        $penilaian = Penilaian::where('alternatif_id', $id)->get()->keyBy('kriteria_id');

        $alternatif = Alternatif::findOrFail($id);

        return view('alternatif.penilaian', compact('penilaian', 'alternatif', 'kriteria'));
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
    public function store(Request $request, $id)
    {
        try {
            // dd($request->all());
        $request->validate([
            'nilai' => 'required|array',
            'nilai.*' => 'required|integer|min:0',

            'bukti' => 'required|array',
            'bukti.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $kriteria = Kriteria::with('subKriterias')->get();

        foreach ($kriteria as $item) {
            $nilai = $request->input('nilai.' . $item->id);
            $valid = $item->subKriterias->contains('bobot', $nilai);

            if (!$valid) {
                throw ValidationException::withMessages([
                    'nilai.' . $item->id => 'Pilihan subkriteria tidak valid.',
                ]);
            }

            $file = $request->file('bukti.' . $item->id);
            $path = $file->store('bukti-rtlh', 'public');

            Penilaian::updateOrCreate(
                [
                    'alternatif_id' => $id,
                    'kriteria_id' => $item->id,
                ],
                [
                    'nilai' => $nilai,
                    'bukti' => $path,
                    'date' => now()->toDateString(),
                ],
            );
        }

        return redirect()->back()->with('success', 'Kriteria created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan Kriteria. Silakan coba lagi.: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Penilaian $penilaian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penilaian $penilaian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penilaian $penilaian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penilaian $penilaian)
    {
        //
    }
}
