@extends('layouts.home.layout')
@section('title', 'Home')

@section('content')
    {{-- ===================== HERO ===================== --}}
    <section class="py-xxl-10 pb-0" id="home">
        <!-- <div class="bg-holder bg-size"
            style="background-image:url({{ asset('home/assets/img/gallery/hero-header-bg.png') }});background-position:top center;background-size:cover;">
        </div> -->
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-5 col-xl-6 col-xxl-7 order-0 order-md-1 text-end">
                    <img class="pt-7 pt-md-0 w-100" src="{{ asset('home/assets/img/illustrations/hero.png') }}"
                        alt="hero-header" />
                </div>
                <div class="col-md-75 col-xl-6 col-xxl-5 text-md-start text-center py-8">
                    <h1 class="fw-normal fs-6 fs-xxl-7">Informasi Penerima Bantuan</h1>
                    <h1 class="fw-bolder fs-6 fs-xxl-7 mb-2">Transparan &amp; Terbuka</h1>
                    <p class="fs-1 mb-5">
                        Warga dapat melihat peringkat calon penerima bantuan setiap tahun tanpa perlu login.
                        Peringkat dihitung dari penilaian kriteria yang sama untuk semua warga.
                    </p>
                    <a class="btn btn-primary px-5" href="#ranking">Lihat Peringkat</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== ALUR PENILAIAN ===================== --}}
    <section class="py-7" id="alur">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 text-center mb-3">
                    <h5 class="text-danger">ALUR PENILAIAN</h5>
                    <h2>Bagaimana peringkat ditentukan?</h2>
                </div>
            </div>
            <div class="row h-100 justify-content-center">
                @php
                    $alur = [
                        [
                            'judul' => '1. Pendataan & Verifikasi',
                            'isi' => 'Data warga dikumpulkan beserta bukti pendukung, lalu diverifikasi oleh petugas.',
                        ],
                        [
                            'judul' => '2. Penilaian Kriteria',
                            'isi' => 'Setiap warga dinilai pada kriteria yang sama dengan bobot yang telah ditetapkan.',
                        ],
                        [
                            'judul' => '3. Perhitungan & Peringkat',
                            'isi' => 'Nilai dihitung dengan metode SAW, semakin tinggi nilai preferensi semakin tinggi peringkatnya.',
                        ],
                    ];
                @endphp
                @foreach ($alur as $item)
                    <div class="col-md-4 pt-4 px-md-2 px-lg-3">
                        <div class="card h-100 px-lg-4 card-span">
                            <div class="card-body d-flex flex-column justify-content-center text-center py-5">
                                <h5 class="mb-3">{{ $item['judul'] }}</h5>
                                <p class="mb-0">{{ $item['isi'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== RANKING ===================== --}}
    <section id="ranking">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-12 mb-6 text-center">
                    <h5 class="text-danger">Peringkat Penerima Bantuan</h5>
                    <h2>Lihat daftar peringkat berdasarkan tahun</h2>
                </div>

                <div class="main-card mx-auto col-lg-12 col-md-10 col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            @php
                                $badgeClass = fn($rank) => $rank == 1
                                    ? 'badge bg-success'
                                    : ($rank == 2
                                        ? 'badge bg-primary'
                                        : ($rank == 3
                                            ? 'badge bg-warning text-dark'
                                            : 'badge bg-secondary'));
                            @endphp

                            {{-- Pilih tahun --}}
                            <form method="GET" action="{{ route('home') }}#ranking" class="mb-3">
                                <div class="row g-2">
                                    <div class="col-sm-8">
                                        <label for="tahun" class="form-label visually-hidden">Pilih Tahun</label>
                                        <select name="tahun" id="tahun" class="form-select" required>
                                            <option value="">-- Pilih Tahun --</option>
                                            @foreach ($tahunOptions as $t)
                                                <option value="{{ $t }}"
                                                    {{ (string) $selected === (string) $t ? 'selected' : '' }}>
                                                    {{ $t }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4 d-grid">
                                        <button type="submit" class="btn btn-primary">Lihat Peringkat</button>
                                    </div>
                                </div>
                            </form>

                            @if ($hasil->count())
                                {{-- Cari nama (di halaman ini saja) --}}
                                <div class="mb-3">
                                    <input type="search" id="cariNama" class="form-control"
                                        placeholder="Cari nama warga..." autocomplete="off">
                                </div>

                                <div class="table-responsive">
                                    <div style="max-height: 420px; overflow-y: auto;">
                                        <table id="tabelRanking"
                                            class="table table-striped table-hover align-middle text-center mb-0"
                                            style="width:100%;">
                                            <thead class="table-light" style="position: sticky; top: 0; z-index: 2;">
                                                <tr>
                                                    <th style="width:110px">Peringkat</th>
                                                    <th>Nama</th>
                                                    <th style="width:200px">Nilai Preferensi</th>
                                                    <th style="width:120px">Tahun</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($hasil as $row)
                                                    @php
                                                        $rank = $row->ranking ?? $loop->iteration;
                                                        $nama = optional($row->alternatif)->name ?? '-';
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <span class="{{ $badgeClass($rank) }}"
                                                                style="font-size:0.95rem; padding:0.45rem 0.6rem;">
                                                                #{{ $rank }}
                                                            </span>
                                                        </td>
                                                        <td class="nama fw-semibold">{{ mb_convert_case($nama, MB_CASE_TITLE, 'UTF-8') }}</td>
                                                        <td>
                                                            <div class="small text-muted">Skor</div>
                                                            <div class="fw-medium">
                                                                {{ number_format($row->nilai_preferensi ?? 0, 4) }}
                                                            </div>
                                                        </td>
                                                        <td>{{ $row->tahun ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div id="kosongCari" class="alert alert-light text-center mt-3 mb-0 d-none">
                                    Nama tidak ditemukan pada tahun ini.
                                </div>

                                <p class="small text-muted mt-3 mb-0">
                                    Total {{ $hasil->count() }} warga pada tahun ini.
                                </p>
                            @else
                                <div class="alert alert-warning text-center mb-0">
                                    <div class="fw-semibold mb-1">Data belum tersedia</div>
                                    <div class="small text-muted">
                                        Peringkat untuk tahun yang dipilih belum dipublikasikan.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== KONTAK ===================== --}}
    <section id="kontak" class="py-7">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 text-center">
                    <h5 class="text-danger">BUTUH BANTUAN?</h5>
                    <h2>Hubungi petugas</h2>
                    <p class="mt-3">
                        Jika ada pertanyaan atau keberatan terkait data Anda, silakan hubungi petugas
                        di kantor desa pada jam pelayanan.
                    </p>
                    {{-- TODO: ganti dengan alamat / jam layanan / nomor kontak yang sebenarnya --}}
                    <p class="mb-1"><i class="fas fa-map-marker-alt text-warning me-2"></i>Alamat kantor desa</p>
                    <p class="mb-1"><i class="fas fa-clock text-warning me-2"></i>Senin - Jumat : 08.00 - 15.00 WIB</p>
                    <p><i class="fas fa-phone-alt text-warning me-2"></i>Nomor kontak petugas</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== FOOTER ===================== --}}
    <section class="py-0 bg-1000">
        <div class="container">
            <div class="row justify-content-md-between justify-content-evenly py-4">
                <div class="col-12 col-sm-8 col-md-6 col-lg-auto text-center text-md-start">
                    <p class="fs--1 my-2 fw-bold text-200">
                        All rights Reserved &copy; {{ date('Y') }}
                    </p>
                </div>
                <div class="col-12 col-sm-8 col-md-6">
                    <ul class="list-inline text-center text-md-end my-2">
                        <li class="list-inline-item"><a class="text-200" href="{{ route('home') }}">Home</a></li>
                        <li class="list-inline-item"><a class="text-200" href="#alur">Alur</a></li>
                        <li class="list-inline-item"><a class="text-200" href="#ranking">Peringkat</a></li>
                        <li class="list-inline-item"><a class="text-200" href="#kontak">Kontak</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Pencarian nama sisi browser --}}
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
                    var nama = tr.querySelector('.nama').textContent.toLowerCase();
                    var cocok = nama.indexOf(q) !== -1;
                    tr.style.display = cocok ? '' : 'none';
                    if (cocok) tampil++;
                });

                kosong.classList.toggle('d-none', tampil !== 0);
            });
        })();
    </script>
@endsection