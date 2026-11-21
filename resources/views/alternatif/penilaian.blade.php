@extends('layouts.layout')

@section('title')
Pengunggahan Berkas
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="m-0">
                                <a href="{{ route('alternatif.index') }}" class="text-decoration-none">
                                    Management Warga
                                </a> > Ungah Berkas - <strong class="text-uppercase">{{ $alternatif->name }}</strong>
                            </h5>

                            @if ($penilaian->isNotEmpty())
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal"
                                    data-bs-target="#create">Update</button>
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#delete">
                                    Delete
                                </button>
                            </div>
                            @else
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#create">Unggah</button>
                            @endif
                        </div>

                        <table id="dtabel" class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kriteria</th>
                                    <th>Keterangan</th>
                                    <th>Deskripsi</th>
                                    <th>Berkas</th>
                                    <th>Penilaian</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($kriteria as $item)
                                @php
                                $dataPenilaian = $penilaian->get($item->id);

                                $subTerpilih = $item->subKriterias->first(function ($sub) use (
                                $dataPenilaian,
                                ) {
                                return $dataPenilaian !== null &&
                                (float) $sub->bobot === (float) $dataPenilaian->nilai;
                                });
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $subTerpilih->nama ?? '-' }}</td>
                                    <td>{{ $subTerpilih->keterangan ?? '-' }}</td>
                                    <td>
                                        @if ($dataPenilaian && $dataPenilaian->bukti)
                                        @if (in_array(strtolower(pathinfo($dataPenilaian->bukti, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                        <button type="button" class="btn p-0 border-0 bg-transparent"
                                            data-bs-toggle="modal"
                                            data-bs-target="#preview-bukti-{{ $item->id }}"
                                            aria-label="Perbesar bukti {{ $item->nama }}">
                                            <img src="{{ asset('storage/' . $dataPenilaian->bukti) }}"
                                                alt="Bukti {{ $item->nama }}" class="img-thumbnail"
                                                style="width: 100px; height: 100px; object-fit: cover;">
                                        </button>

                                        <div class="modal fade" id="preview-bukti-{{ $item->id }}"
                                            tabindex="-1"
                                            aria-labelledby="preview-bukti-label-{{ $item->id }}"
                                            aria-hidden="true">
                                            <div
                                                class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="preview-bukti-label-{{ $item->id }}">
                                                            Bukti {{ $item->nama }}
                                                        </h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ asset('storage/' . $dataPenilaian->bukti) }}"
                                                            alt="Bukti {{ $item->nama }}"
                                                            class="img-fluid" style="max-height: 80vh;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <a href="{{ asset('storage/' . $dataPenilaian->bukti) }}"
                                            target="_blank">
                                            <span
                                                class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1"
                                                aria-label="Lihat bukti {{ $item->nama }}">
                                                <i class="ri-eye-fill" aria-hidden="true"></i>
                                                <span>Lihat PDF</span>
                                            </span>
                                        </a>
                                        @endif
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>{{ $dataPenilaian->nilai ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
    </div>
</div> <!-- end row -->

<!-- Modal Unggah / Update -->
<div id="create" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="createLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="createLabel">
                    {{ $penilaian->isNotEmpty() ? 'Update Berkas' : 'Unggah Berkas' }}
                    {{ $alternatif->name }}
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('penilaian.store', $alternatif->id) }}" method="POST"
                enctype="multipart/form-data">

                @csrf

                <div class="modal-body">

                    @foreach ($kriteria as $item)
                    @php
                    // Mengambil penilaian berdasarkan kriteria
                    $existingPenilaian = $penilaian->get($item->id);
                    @endphp

                    <div class="mb-1">

                        <label for="sub_kriteria_{{ $item->id }}" class="form-label">
                            {{ $item->nama }}
                        </label>

                        <select id="sub_kriteria_{{ $item->id }}" name="nilai[{{ $item->id }}]"
                            class="form-control" required>

                            <option value="">
                                -- Pilih Subkriteria --
                            </option>

                            @foreach ($item->subKriterias as $sub)
                            <option value="{{ $sub->bobot }}" @selected((string) old('nilai.' . $item->id, $existingPenilaian->nilai ?? '') === (string) $sub->bobot)>

                                {{ $sub->nama }}
                                (Keterangan: {{ $sub->keterangan }})
                            </option>
                            @endforeach

                        </select>

                        <!-- <select id="sub_kriteria_{{ $item->id }}"
                            name="sub_kriteria_id[{{ $item->id }}]"
                            class="form-control" required>

                            <option value="">-- Pilih Subkriteria --</option>

                            @foreach ($item->subKriterias as $sub)
                            <option value="{{ $sub->id }}"
                                @selected(
                                (string) old( 'sub_kriteria_id.' . $item->id,
                                $existingPenilaian->sub_kriteria_id ?? ''
                                ) === (string) $sub->id
                                )>

                                {{ $sub->nama }}
                                (Keterangan: {{ $sub->keterangan }})
                                - Bobot: {{ $sub->bobot }}

                            </option>
                            @endforeach
                        </select> -->

                        @error('sub_kriteria_id.' . $item->id)
                        <small class="text-danger">{{ $message }}</small>
                        @enderror

                        @error('nilai.' . $item->id)
                        <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                    <div class="mb-4">

                        <label for="bukti_{{ $item->id }}" class="form-label small">
                            Berkas {{ $item->nama }}
                        </label>

                        {{-- Menampilkan berkas sebelumnya --}}
                        @if ($existingPenilaian && $existingPenilaian->bukti)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . $existingPenilaian->bukti) }}" target="_blank"
                                class="btn btn-sm btn-outline-info">
                                <i class="ri-eye-fill"></i>
                                Lihat Berkas Sebelumnya
                            </a>
                        </div>
                        @endif

                        <input id="bukti_{{ $item->id }}" type="file" name="bukti[{{ $item->id }}]"
                            class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{-- Wajib hanya jika belum mempunyai berkas --}}
                            @if (!$existingPenilaian || !$existingPenilaian->bukti) required @endif>

                        @if ($existingPenilaian && $existingPenilaian->bukti)
                        <small class="text-muted">
                            Kosongkan jika tidak ingin mengganti berkas sebelumnya.
                        </small>
                        @else
                        <small class="text-muted">
                            Berkas wajib diunggah.
                        </small>
                        @endif

                        @error('bukti.' . $item->id)
                        <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>
                    @endforeach

                </div>

                <div class="modal-footer">

                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        {{ $penilaian->isNotEmpty() ? 'Update' : 'Submit' }}
                    </button>

                    <button type="reset" class="btn btn-secondary waves-effect">
                        Reset
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<!-- Modal Delete -->
<div id="delete" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deleteLabel"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
                <h5 class="modal-title text-danger">
                    Konfirmasi Penghapusan
                </h5>
            </div>
            <div class="modal-body text-center">

                <i class="ri-error-warning-line text-danger" style="font-size: 60px;"></i>

                <h5 class="mt-2">Apakah Anda yakin?</h5>

                <p class="text-muted mb-0">
                    Seluruh data penilaian dan berkas milik
                    <strong>{{ $alternatif->name }}</strong>
                    akan dihapus.
                </p>

                <p class="text-danger small mt-2 mb-0">
                    Data warga tetap tersimpan dan status verifikasi
                    akan dikembalikan.
                </p>

            </div>
            <div class="modal-footer justify-content-center gap-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close
                </button>
                <form action="{{ route('penilaian.destroy', $alternatif->id) }}" method="POST"
                    style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection