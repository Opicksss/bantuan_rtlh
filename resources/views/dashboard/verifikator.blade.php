@extends('layouts.layout')

@section('title')
    Dashboard Survey
@endsection

@section('content')
    @php
        // Link aman: route tidak ada / parameter kurang -> "#" (tidak error)
        $link = function ($name, $params = []) {
            try {
                return \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : '#';
            } catch (\Throwable $e) {
                return '#';
            }
        };

        $cards = [
            [
                'judul' => 'Total Warga',
                'nilai' => $totalAlternatif,
                'badge' => $totalKriteria . ' Kriteria',
                'badgeClass' => 'bg-subtle-primary text-primary',
                'ket' => 'Warga yang perlu dinilai',
                'icon' => 'uim uim-layers-alt',
            ],
            [
                'judul' => 'Sudah Lengkap',
                'nilai' => $lengkap,
                'badge' => 'Selesai',
                'badgeClass' => 'bg-subtle-success text-success',
                'ket' => 'Semua kriteria terisi',
                'icon' => 'uim uim-check-circle',
            ],
            [
                'judul' => 'Dinilai Sebagian',
                'nilai' => $sebagian,
                'badge' => 'Lanjutkan',
                'badgeClass' => 'bg-subtle-warning text-warning',
                'ket' => 'Masih ada kriteria kosong',
                'icon' => 'uim uim-document-layout-left',
            ],
            [
                'judul' => 'Belum Dinilai',
                'nilai' => $belumMulai,
                'badge' => 'Belum mulai',
                'badgeClass' => 'bg-subtle-danger text-danger',
                'ket' => 'Belum ada nilai sama sekali',
                'icon' => 'uim uim-layer-group',
            ],
        ];
    @endphp

    <div class="page-content">
        <div class="container-fluid">

            <div class="row mb-3">
                <div class="col-12">
                    <h4 class="mb-1">Dashboard Survey</h4>
                    <p class="text-muted mb-0">
                        Input nilai warga untuk tahun {{ $tahunIni }}.
                    </p>
                </div>
            </div>

            {{-- ===================== STATISTIK ===================== --}}
            <div class="row">
                @foreach ($cards as $c)
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-md flex-shrink-0">
                                        <span class="avatar-title bg-subtle-primary text-primary rounded fs-2">
                                            <i class="{{ $c['icon'] }}"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden ms-4">
                                        <p class="text-muted text-truncate font-size-15 mb-2">{{ $c['judul'] }}</p>
                                        <h3 class="fs-4 flex-grow-1 mb-3">{{ $c['nilai'] }}
                                            <span class="text-muted font-size-16">Warga</span>
                                        </h3>
                                        <p class="text-muted mb-0 text-truncate">
                                            <span class="badge {{ $c['badgeClass'] }} font-size-12 fw-normal me-1">
                                                {{ $c['badge'] }}
                                            </span>
                                        </p>
                                        <p class="text-muted mb-0 text-truncate small mt-1">{{ $c['ket'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{-- END ROW --}}

            <div class="row mb-4">
                {{-- ===================== DAFTAR PERLU DINILAI ===================== --}}
                <div class="col-xl-8">
                    <div class="card h-100">
                        <div class="card-header border-0 align-items-center d-flex pb-0">
                            <h4 class="card-title mb-0 flex-grow-1">Warga yang Perlu Dinilai</h4>
                            <a href="{{ $link('alternatif.index') }}" class="btn btn-soft-primary btn-sm">Lihat Semua</a>
                        </div>

                        <div class="card-body">
                            @if ($perluDinilai->count())
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Kode</th>
                                                <th>Nama</th>
                                                <th>Alamat</th>
                                                <th class="text-center" style="width:140px">Progres</th>
                                                <th class="text-center" style="width:130px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($perluDinilai as $w)
                                                <tr>
                                                    <td>{{ $w->kode }}</td>
                                                    <td class="fw-semibold">{{ $w->name }}</td>
                                                    <td>{{ $w->alamat ?? '-' }}</td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge {{ $w->terisi == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                                            {{ $w->terisi }}/{{ $totalKriteria }} kriteria
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ $link('penilaian.index', ['id' => $w->id]) }}"
                                                            class="btn btn-primary btn-sm">Input Nilai</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if ($belumLengkap > $perluDinilai->count())
                                    <p class="small text-muted mt-3 mb-0">
                                        Menampilkan {{ $perluDinilai->count() }} dari {{ $belumLengkap }} warga yang
                                        belum lengkap.
                                    </p>
                                @endif
                            @else
                                <div class="alert alert-success mb-0">
                                    Semua warga sudah dinilai lengkap pada tahun ini.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ===================== PROGRES PERIODE ===================== --}}
                <div class="col-xl-4">
                    <div class="card h-100">
                        <div class="card-header border-0 pb-0">
                            <h4 class="card-title mb-0">Progres Penilaian</h4>
                        </div>
                        <div class="card-body">
                            <p class="mb-1 text-muted">Tahun</p>
                            <h5 class="mb-3">{{ $tahunIni }}</h5>

                            <p class="mb-1 text-muted">Penilaian selesai</p>
                            <div class="progress mb-1" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $persen }}%;" aria-valuenow="{{ $persen }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted d-block mb-4">
                                {{ $lengkap }} dari {{ $totalAlternatif }} warga lengkap ({{ $persen }}%)
                            </small>

                            <div class="alert alert-info small mb-4">
                                Warga baru ikut perhitungan SAW setelah semua kriteria
                                ({{ $totalKriteria }}) terisi pada tahun ini.
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ $link('alternatif.index') }}" class="btn btn-primary">Daftar Warga</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- END ROW --}}

        </div> <!-- container-fluid -->
    </div>
@endsection
