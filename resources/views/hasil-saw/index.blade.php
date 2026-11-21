@extends('layouts.layout')

@section('title')
Perhitungan SAW
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        {{-- Filter periode --}}
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hasil-saw.index') }}" method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="tahun" class="form-label">Tahun</label>
                        <input type="number" id="tahun" name="tahun" class="form-control"
                            value="{{ $tahun }}" min="2000" max="2100">
                    </div>
                    <div class="col-md-3">
                        <label for="periode" class="form-label">Periode</label>
                        <select id="periode" name="periode" class="form-control">
                            <option value="1" @selected($periode==1)>Periode 1 (Jan – Jun)</option>
                            <option value="2" @selected($periode==2)>Periode 2 (Jul – Des)</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Hitung</button>

                        @if (count($hasil))
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#simpan">
                            {{ $tersimpan ? 'Simpan Ulang' : 'Simpan Hasil' }}
                        </button>
                        @endif

                        @if ($tersimpan)
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                            data-bs-target="#hapus">
                            Hapus Hasil Tersimpan
                        </button>
                        @endif
                    </div>
                </form>

                @if ($tersimpan)
                <small class="text-success d-block mt-2">
                    Hasil periode ini sudah tersimpan di database.
                </small>
                @endif
            </div>
        </div>

        @if (empty($hasil))
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                Belum ada alternatif yang berstatus <strong>Terverifikasi</strong> dengan penilaian lengkap
                pada periode {{ $periode }} tahun {{ $tahun }}.
            </div>
        </div>
        @else

        {{-- 1. Bobot kriteria --}}
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">1. Kriteria &amp; Bobot</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Kriteria</th>
                                <th>Sifat</th>
                                <th>Bobot</th>
                                <th>Bobot (Normal)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kriteria as $k)
                            <tr>
                                <td>{{ $k->kode }}</td>
                                <td>{{ $k->nama }}</td>
                                <td>
                                    <span class="badge {{ $k->sifat === 'benefit' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($k->sifat) }}
                                    </span>
                                </td>
                                <td>{{ $k->bobot }}</td>
                                <td>{{ number_format($bobot[$k->id], 4) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total</th>
                                <th>{{ number_format($bobot->sum(), 4) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- 2. Matriks keputusan --}}
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">2. Matriks Keputusan (X)</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Alternatif</th>
                                @foreach ($kriteria as $k)
                                <th class="text-center">{{ $k->kode }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alternatif as $alt)
                            <tr>
                                <td>{{ $alt->kode }} - {{ $alt->name }}</td>
                                @foreach ($kriteria as $k)
                                <td class="text-center">{{ rtrim(rtrim(number_format($matriks[$alt->id][$k->id], 2), '0'), '.') }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Max</th>
                                @foreach ($kriteria as $k)
                                <th class="text-center">{{ rtrim(rtrim(number_format($minmax[$k->id]['max'], 2), '0'), '.') }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                <th>Min</th>
                                @foreach ($kriteria as $k)
                                <th class="text-center">{{ rtrim(rtrim(number_format($minmax[$k->id]['min'], 2), '0'), '.') }}</th>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- 3. Normalisasi --}}
        <div class="card">
            <div class="card-body">
                <h5 class="mb-1">3. Matriks Normalisasi (R)</h5>
                <p class="text-muted small mb-3">
                    Benefit: r = x / max &nbsp;|&nbsp; Cost: r = min / x
                </p>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Alternatif</th>
                                @foreach ($kriteria as $k)
                                <th class="text-center">{{ $k->kode }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alternatif as $alt)
                            <tr>
                                <td>{{ $alt->kode }} - {{ $alt->name }}</td>
                                @foreach ($kriteria as $k)
                                <td class="text-center">{{ number_format($normalisasi[$alt->id][$k->id], 4) }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 4. Preferensi & ranking --}}
        <div class="card">
            <div class="card-body">
                <h5 class="mb-1">4. Nilai Preferensi (V) &amp; Ranking</h5>
                <p class="text-muted small mb-3">V = Σ (bobot × r)</p>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">Ranking</th>
                                <th>Kode</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th class="text-center">Nilai V</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hasil as $row)
                            <tr>
                                <td class="text-center">
                                    <span class="badge {{ $row['ranking'] <= 3 ? 'bg-primary' : 'bg-secondary' }}">
                                        {{ $row['ranking'] }}
                                    </span>
                                </td>
                                <td>{{ $row['alternatif']->kode }}</td>
                                <td>{{ $row['alternatif']->NIK }}</td>
                                <td>{{ $row['alternatif']->name }}</td>
                                <td>{{ $row['alternatif']->alamat ?? '-' }}</td>
                                <td class="text-center"><strong>{{ number_format($row['v'], 4) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Simpan -->
<div id="simpan" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('hasil-saw.simpan') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <input type="hidden" name="periode" value="{{ $periode }}">
                <div class="modal-header">
                    <h5 class="modal-title">Simpan Hasil SAW</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Simpan hasil perhitungan periode {{ $periode }} tahun {{ $tahun }}?
                    @if ($tersimpan)
                    <p class="text-danger small mt-2 mb-0">Hasil lama pada periode ini akan ditimpa.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div id="hapus" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('hasil-saw.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <input type="hidden" name="periode" value="{{ $periode }}">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Hapus Hasil Tersimpan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Hasil SAW periode {{ $periode }} tahun {{ $tahun }} yang tersimpan akan dihapus.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection