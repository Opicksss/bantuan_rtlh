@extends('layouts.layout')

@section('title')
    Dashboard
@endsection

@section('content')
    @php
        // Link aman: kalau route tidak ada / parameternya kurang, jatuh ke "#" (tidak error)
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
                'satuan' => 'Warga',
                'badge' => $terverifikasi . ' Terverifikasi',
                'badgeClass' => 'bg-subtle-success text-success',
                'ket' => 'Alternatif terdaftar',
                'icon' => 'uim uim-layers-alt',
                'route' => $link('alternatif.index'),
            ],
            [
                'judul' => 'Total Kriteria',
                'nilai' => $totalKriteria,
                'satuan' => 'Kriteria',
                'badge' => $totalSubKriteria . ' Subkriteria',
                'badgeClass' => 'bg-subtle-success text-success',
                'ket' => 'Kriteria penilaian',
                'icon' => 'uim uim-layer-group',
                'route' => $link('kriteria.index'),
            ],
            [
                'judul' => 'Menunggu Verifikasi',
                'nilai' => $pending,
                'satuan' => 'Warga',
                'badge' => 'Pending',
                'badgeClass' => 'bg-subtle-warning text-warning',
                'ket' => 'Perlu diverifikasi petugas',
                'icon' => 'uim uim-check-circle',
                'route' => $link('alternatif.index'),
            ],
            [
                'judul' => 'Belum Dinilai Lengkap',
                'nilai' => $belumDinilai,
                'satuan' => 'Warga',
                'badge' => 'Tahun ' . $tahunIni,
                'badgeClass' => 'bg-subtle-danger text-danger',
                'ket' => 'Terverifikasi, belum ikut SAW',
                'icon' => 'uim uim-document-layout-left',
                // penilaian dibuka per warga, jadi arahkan ke daftar warga
                'route' => $link('alternatif.index'),
            ],
        ];

        $persen = $totalAlternatif > 0 ? round(($terverifikasi / $totalAlternatif) * 100) : 0;
    @endphp

    <div class="page-content">
        <div class="container-fluid">

            <div class="row mb-3">
                <div class="col-12">
                    <h4 class="mb-1">Dashboard Admin</h4>
                    <p class="text-muted mb-0">Ringkasan data sistem pendukung keputusan penerima bantuan (SAW).</p>
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
                                            <span class="text-muted font-size-16">{{ $c['satuan'] }}</span>
                                        </h3>
                                        <p class="text-muted mb-0 text-truncate">
                                            <span class="badge {{ $c['badgeClass'] }} font-size-12 fw-normal me-1">
                                                {{ $c['badge'] }}
                                            </span>
                                        </p>
                                        <p class="text-muted mb-0 text-truncate small mt-1">{{ $c['ket'] }}</p>
                                    </div>
                                    <div class="flex-shrink-0 align-self-start">
                                        <div class="dropdown">
                                            <a class="dropdown-toggle btn-icon border rounded-circle" href="#"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="ri-more-2-fill text-muted font-size-16"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="{{ $c['route'] }}">Lihat Detail</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{-- END ROW --}}

            <div class="row mb-4">
                {{-- ===================== PERINGKAT TERATAS ===================== --}}
                <div class="col-xl-7">
                    <div class="card h-100">
                        <div class="card-header border-0 align-items-center d-flex pb-0">
                            <h4 class="card-title mb-0 flex-grow-1">Peringkat Teratas</h4>
                            <div>
                                @if ($terbaik->count())
                                    <a href="{{ $link('hasil-saw.index', ['tahun' => $terbaik->first()->tahun]) }}"
                                        class="btn btn-soft-primary btn-sm">Lihat Detail</a>
                                @else
                                    <a href="#" class="btn btn-soft-secondary btn-sm disabled">Lihat Detail</a>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            @if ($terbaik->count())
                                <h5 class="mb-3">
                                    Tahun {{ $terbaik->first()->tahun }}
                                </h5>

                                @foreach ($terbaik as $item)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-md flex-shrink-0">
                                            <span class="avatar-title bg-subtle-primary text-primary rounded fs-2">
                                                #{{ $item->ranking }}
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">{{ optional($item->alternatif)->name ?? '-' }}</h6>
                                            <p class="text-muted mb-0">Kode: {{ optional($item->alternatif)->kode ?? '-' }}</p>
                                            <p class="text-muted mb-0">Alamat: {{ optional($item->alternatif)->alamat ?? '-' }}</p>
                                            <p class="text-success mb-0">Nilai: {{ number_format($item->nilai_preferensi, 4) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-warning mb-0">
                                    Belum ada hasil perhitungan SAW yang disimpan.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ===================== STATUS PERIODE ===================== --}}
                <div class="col-xl-5">
                    <div class="card h-100">
                        <div class="card-header border-0 pb-0">
                            <h4 class="card-title mb-0">Status Tahun Berjalan</h4>
                        </div>
                        <div class="card-body">
                            <p class="mb-1 text-muted">Tahun</p>
                            <h5 class="mb-3">{{ $tahunIni }}</h5>

                            <p class="mb-1 text-muted">Hasil SAW</p>
                            @if ($hasilTahunIniTersimpan)
                                <span class="badge bg-success mb-3">Sudah disimpan</span>
                            @else
                                <span class="badge bg-warning text-dark mb-3">Belum disimpan</span>
                            @endif

                            <p class="mb-1 text-muted">Verifikasi warga</p>
                            <div class="progress mb-1" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $persen }}%;" aria-valuenow="{{ $persen }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted d-block mb-4">
                                {{ $terverifikasi }} dari {{ $totalAlternatif }} warga terverifikasi ({{ $persen }}%)
                            </small>

                            <div class="d-grid gap-2">
                                <a href="{{ $link('hasil-saw.index', ['tahun' => $tahunIni]) }}"
                                    class="btn btn-primary">Hitung / Lihat SAW</a>
                                <a href="{{ $link('alternatif.index') }}" class="btn btn-outline-primary">Kelola Penilaian</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- END ROW --}}

        </div> <!-- container-fluid -->
    </div>
@endsection