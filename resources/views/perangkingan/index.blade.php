@extends('layouts.layout')

@section('title')
    Perangkingan
@endsection

@section('content')
    @php
        $badgeClass = fn($rank) => $rank == 1
            ? 'badge bg-success'
            : ($rank == 2
                ? 'badge bg-primary'
                : ($rank == 3
                    ? 'badge bg-warning text-dark'
                    : 'badge bg-secondary'));

        $max = (float) ($statistik['tertinggi'] ?? 0);

        $cards = [
            ['judul' => 'Jumlah Warga', 'nilai' => $statistik['total'], 'icon' => 'uim uim-layers-alt'],
            ['judul' => 'Nilai Tertinggi', 'nilai' => $statistik['total'] ? number_format($statistik['tertinggi'], 4) : '-', 'icon' => 'uim uim-check-circle'],
            ['judul' => 'Nilai Terendah', 'nilai' => $statistik['total'] ? number_format($statistik['terendah'], 4) : '-', 'icon' => 'uim uim-layer-group'],
            ['judul' => 'Rata-rata', 'nilai' => $statistik['total'] ? number_format($statistik['rata'], 4) : '-', 'icon' => 'uim uim-document-layout-left'],
        ];
    @endphp

    <div class="page-content">
        <div class="container-fluid">

            <div class="row mb-3">
                <div class="col-12">
                    <h4 class="mb-1">Perangkingan Penerima Bantuan</h4>
                    <p class="text-muted mb-0">Hasil perhitungan SAW yang sudah disimpan, diurutkan dari nilai preferensi tertinggi.</p>
                </div>
            </div>

            {{-- Filter tahun --}}
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('perangkingan.index') }}" method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label for="tahun" class="form-label">Tahun</label>
                            <select id="tahun" name="tahun" class="form-control">
                                @forelse ($tahunOptions as $t)
                                    <option value="{{ $t }}" {{ (int) $tahun === (int) $t ? 'selected' : '' }}>
                                        {{ $t }}
                                    </option>
                                @empty
                                    <option value="{{ $tahun }}" selected>{{ $tahun }}</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="col-md-9 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Tampilkan</button>
                            <a href="{{ route('hasil-saw.index', ['tahun' => $tahun]) }}" class="btn btn-outline-primary">
                                Buka Perhitungan SAW
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @if ($hasil->isEmpty())
                <div class="card">
                    <div class="card-body text-center text-muted py-5">
                        Belum ada hasil perangkingan yang tersimpan untuk tahun {{ $tahun }}.<br>
                        Hitung dan simpan hasilnya terlebih dahulu di halaman Perhitungan SAW.
                    </div>
                </div>
            @else
                {{-- Ringkasan --}}
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
                                            <h3 class="fs-4 mb-0">{{ $c['nilai'] }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Tabel peringkat --}}
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                            <h5 class="mb-0">Daftar Peringkat Tahun {{ $tahun }}</h5>
                            <input type="search" id="cariNama" class="form-control" style="max-width: 280px;"
                                placeholder="Cari kode / nama / alamat..." autocomplete="off">
                        </div>

                        <div class="table-responsive">
                            <table id="tabelRanking" class="table table-striped table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:110px">Peringkat</th>
                                        <th>Kode</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        <th style="min-width:200px">Nilai Preferensi (V)</th>
                                        <th class="text-center">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($hasil as $row)
                                        @php
                                            $a = $row->alternatif;
                                            $v = (float) $row->nilai_preferensi;
                                            $persen = $max > 0 ? round(($v / $max) * 100) : 0;
                                        @endphp
                                        <tr class="{{ $row->penerima ? 'table-success' : '' }}">
                                            <td class="text-center">
                                                <span class="{{ $badgeClass($row->ranking) }}"
                                                    style="font-size:0.95rem; padding:0.45rem 0.6rem;">
                                                    #{{ $row->ranking }}
                                                </span>
                                            </td>
                                            <td class="cari">{{ optional($a)->kode ?? '-' }}</td>
                                            <td>{{ optional($a)->NIK ?? '-' }}</td>
                                            <td class="cari fw-semibold">{{ optional($a)->name ?? '-' }}</td>
                                            <td class="cari">{{ optional($a)->alamat ?? '-' }}</td>
                                            <td>
                                                <div class="fw-medium mb-1">{{ number_format($v, 4) }}</div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-primary" role="progressbar"
                                                        style="width: {{ $persen }}%;" aria-valuenow="{{ $persen }}"
                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if ($row->penerima)
                                                    <span class="badge bg-success">Menerima</span>
                                                @else
                                                    <span class="badge bg-secondary">Tidak Menerima</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div id="kosongCari" class="alert alert-light text-center mt-3 mb-0 d-none">
                            Data tidak ditemukan.
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- Pencarian sisi browser --}}
    <script>
        (function () {
            var input = document.getElementById('cariNama');
            if (!input) return;

            var rows = document.querySelectorAll('#tabelRanking tbody tr');
            var kosong = document.getElementById('kosongCari');

            input.addEventListener('input', function () {
                var q = input.value.trim().toLowerCase();
                var tampil = 0;

                rows.forEach(function (tr) {
                    var teks = Array.prototype.map.call(tr.querySelectorAll('.cari'), function (td) {
                        return td.textContent;
                    }).join(' ').toLowerCase();

                    var cocok = teks.indexOf(q) !== -1;
                    tr.style.display = cocok ? '' : 'none';
                    if (cocok) tampil++;
                });

                kosong.classList.toggle('d-none', tampil !== 0);
            });
        })();
    </script>
@endsection