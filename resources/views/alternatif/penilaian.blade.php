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
                                        Alternatif
                                    </a> > Ungah Berkas - <strong class="text-uppercase">{{ $alternatif->name }}</strong>
                                </h5>

                                @if ($penilaian->isNotEmpty())
                                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal"
                                        data-bs-target="#create">Update</button>
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
                                        <th>Berkas</th>
                                        <th>Penilaian</th>
                                        <th class="no-export">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($kriteria as $item)
                                        @php
                                            $dataPenilaian = $penilaian->get($item->id);
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->nama }}</td>
                                            <td>{{ $item->subKriteria->keterangan ?? '-' }}</td>
                                            <td>
                                                @if ($dataPenilaian && $dataPenilaian->bukti)
                                                    @if (in_array(strtolower(pathinfo($dataPenilaian->bukti, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                                        <a href="{{ asset('storage/' . $dataPenilaian->bukti) }}"
                                                            target="_blank">
                                                            <img src="{{ asset('storage/' . $dataPenilaian->bukti) }}"
                                                                alt="Bukti {{ $item->nama }}" class="img-thumbnail"
                                                                style="width: 100px; height: 100px; object-fit: cover;">
                                                        </a>
                                                    @else
                                                        <a href="{{ asset('storage/' . $dataPenilaian->bukti) }}"
                                                            target="_blank">
                                                            Lihat Bukti
                                                        </a>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $dataPenilaian->nilai ?? '-' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-outline-warning btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#update{{ $item->id }}">Update</button>
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#delete{{ $item->id }}">Delete</button>
                                        </tr>
                                        <!-- modal update -->
                                        <div id="update{{ $item->id }}" class="modal fade" tabindex="-1" role="dialog"
                                            aria-labelledby="update{{ $item->id }}Label" aria-hidden="true"
                                            data-bs-backdrop="static" data-bs-keyboard="false">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="update{{ $item->id }}Label">Update
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form class="custom-validation"
                                                            action="{{ route('alternatif.update', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="mb-3">
                                                                <label>NIK</label>
                                                                <input type="text" class="form-control" name="NIK"
                                                                    required placeholder="Masukkan NIK"
                                                                    value="{{ $item->NIK }}" />
                                                            </div>
                                                            <div class="mb-3">
                                                                <label>Name</label>
                                                                <input type="text" class="form-control" name="name"
                                                                    required placeholder="Masukkan Nama"
                                                                    value="{{ $item->name }}" />
                                                            </div>

                                                            <div class="mb-3">
                                                                <label>Alamat</label>
                                                                <input type="text" class="form-control" name="alamat"
                                                                    required placeholder="Masukkan alamat"
                                                                    value="{{ $item->alamat }}" />
                                                            </div>
                                                            <div class="mb-3">
                                                                <label>Nomor Hp</label>
                                                                <input type="tel" class="form-control" name="nomor"
                                                                    required placeholder="Masukkan Nomor Hp"
                                                                    value="{{ $item->nomor }}" />
                                                            </div>

                                                            <div class="mb-0">
                                                                <div class="d-flex justify-content-end">
                                                                    <button type="submit"
                                                                        class="btn btn-primary waves-effect waves-light me-1">
                                                                        Submit
                                                                    </button>
                                                                    <button type="reset"
                                                                        class="btn btn-secondary waves-effect">
                                                                        Reset
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div><!-- /.modal-content -->
                                            </div><!-- /.modal-dialog -->
                                        </div>
                                        <!-- /modal update -->
                                        <!-- modal delete -->
                                        <div id="delete{{ $item->id }}" class="modal fade" tabindex="-1"
                                            role="dialog" aria-labelledby="delete{{ $item->id }}Label"
                                            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <!-- Modal Header -->
                                                    <div class="modal-header justify-content-center">
                                                        <h5 class="modal-title text-danger">
                                                            Konfirmasi Penghapusan
                                                        </h5>
                                                    </div>

                                                    <!-- Modal Body -->
                                                    <div class="modal-body text-center">
                                                        <p class="mb-4">
                                                            Apakah Anda yakin ingin menghapus Data Warga dengan nama
                                                            <strong style="font-size: 1rem;">{{ ucwords($item->nama) }}
                                                                ?</strong>
                                                            Tindakan ini tidak dapat dibatalkan.
                                                        </p>
                                                        <div class="d-flex justify-content-center">
                                                            <i class="bi bi-exclamation-circle-fill text-warning"
                                                                style="font-size: 3rem;"></i>
                                                        </div>
                                                    </div>

                                                    <!-- Modal Footer -->
                                                    <div class="modal-footer justify-content-center gap-3">
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            data-bs-dismiss="modal">Close
                                                        </button>
                                                        <form action="{{ route('alternatif.destroy', $item->id) }}"
                                                            method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger">Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div><!-- /.modal-dialog -->
                                        </div>
                                        <!-- /modal delete -->
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div>
        </div>
    </div> <!-- end row -->




    <!-- sample modal content -->

    <div id="create" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="createLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createLabel">Unggah Berkas {{ $alternatif->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('penilaian.store', $alternatif->id) }}" method="POST"
                        enctype="multipart/form-data">

                        @csrf

                        @foreach ($kriteria as $item)
                            @php
                                $existingPenilaian = $penilaian->get($item->id);
                            @endphp
                            <div class="mb-1">
                                <label for="sub_kriteria_{{ $item->id }}" class="form-label">
                                    {{ $item->nama }}
                                </label>
                                <select id="sub_kriteria_{{ $item->id }}"
                                    name="nilai[{{ $item->id }}]" class="form-control" required>
                                    <option value="">-- Pilih Kriteria --</option>
                                    @foreach ($item->subKriterias as $sub)
                                        <option value="{{ $sub->bobot }}"
                                            {{ old('nilai.' . $item->id) == $sub->bobot || (!old('nilai.' . $item->id) && ($existingPenilaian->sub_kriteria_id ?? null) == $sub->id) ? 'selected' : '' }}>
                                            {{ $sub->nama }} (Keterangan: {{ $sub->keterangan }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="bukti_{{ $item->id }}" class="form-label small">Berkas</label>
                                <input id="bukti_{{ $item->id }}" type="file" name="bukti[{{ $item->id }}]"
                                    class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                            </div>
                        @endforeach
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                    <button type="reset" class="btn btn-secondary waves-effect">Reset</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
