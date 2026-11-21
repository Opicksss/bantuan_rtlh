<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $kriteria = Kriteria::with('subKriterias')->get();

        $request->validate([
            'nilai' => 'required|array',
            'nilai.*' => 'required|integer|min:0',

            'bukti' => 'nullable|array',
            'bukti.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $penilaianLama = Penilaian::where('alternatif_id', $id)->get()->keyBy('kriteria_id');

        $modeUpdate = $penilaianLama->isNotEmpty();

        foreach ($kriteria as $item) {
            $nilai = $request->input('nilai.' . $item->id);

            $dataLama = $penilaianLama->get($item->id);

            $file = $request->file('bukti.' . $item->id);

            $valid = $item->subKriterias->contains(function ($sub) use ($nilai) {
                return (int) $sub->bobot === (int) $nilai;
            });

            if (!$valid) {
                throw ValidationException::withMessages([
                    'nilai.' . $item->id => 'Pilihan subkriteria tidak valid.',
                ]);
            }

            if ((!$dataLama || !$dataLama->bukti) && !$file) {
                throw ValidationException::withMessages([
                    'bukti.' . $item->id => 'Berkas wajib diunggah.',
                ]);
            }
        }

        $fileBaru = [];
        $fileLama = [];

        try {
            DB::transaction(function () use ($request, $id, $kriteria, $penilaianLama, &$fileBaru, &$fileLama) {
                foreach ($kriteria as $item) {
                    $nilai = $request->input('nilai.' . $item->id);

                    $dataLama = $penilaianLama->get($item->id);

                    $file = $request->file('bukti.' . $item->id);

                    $path = $dataLama->bukti ?? null;

                    if ($file) {
                        $pathBaru = $file->store('bukti-rtlh', 'public');

                        if (!$pathBaru) {
                            throw new \RuntimeException('Gagal mengunggah berkas.');
                        }

                        $fileBaru[] = $pathBaru;

                        if ($dataLama && $dataLama->bukti) {
                            $fileLama[] = $dataLama->bukti;
                        }

                        $path = $pathBaru;
                    }

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

                Alternatif::whereKey($id)->update([
                    'status' => 'Terverifikasi',
                ]);
            });

            foreach ($fileLama as $pathLama) {
                Storage::disk('public')->delete($pathLama);
            }

            return redirect()
                ->back()
                ->with('success', $modeUpdate ? 'Penilaian berhasil diperbarui.' : 'Penilaian berhasil disimpan.');
        } catch (\Throwable $e) {
            foreach ($fileBaru as $pathBaru) {
                Storage::disk('public')->delete($pathBaru);
            }

            report($e);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan penilaian. Silakan coba lagi. error: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        try {
            $alternatif = Alternatif::findOrFail($id);
            $penilaian = Penilaian::where('alternatif_id', $alternatif->id)->get();

            Penilaian::where('alternatif_id', $alternatif->id)->delete();

            foreach ($penilaian as $item) {
                if ($item->bukti) {
                    Storage::disk('public')->delete($item->bukti);
                }
            }

            Alternatif::whereKey($id)->update([
                'status' => 'Pending',
            ]);

            return redirect()->back()->with('success', 'Seluruh penilaian dan berkas berhasil dihapus.');
        } catch (\Exception $e) {
            report($e);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus penilaian. Silakan coba lagi.');
        }
    }
}
