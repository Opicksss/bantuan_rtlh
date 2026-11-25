@extends('layouts.layout')

@section('title')
    Warga
@endsection

@section('content')
    <div class="page-content"> 

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="m-0">Management Warga</h5>

                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#create">Create</button>
                            </div>

                            <table id="dtabel" class="table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        <th>Nomor Hp</th>
                                        <th>Status</th>
                                        <th class="no-export">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($alternatifs as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->kode }}</td>
                                            <td>{{ $item->NIK }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->alamat }}</td>
                                            <td>{{ $item->nomor }}</td>
                                            <td>
                                                @if ($item->status === 'Terverifikasi')
                                                    <span class="badge bg-success">Terverifikasi</span>
                                                @else
                                                    <span class="badge bg-danger">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-outline-warning btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#update{{ $item->id }}">Update</button>
                                                    <a href="{{ route('penilaian.index', $item->id) }}" class="btn btn-outline-info btn-sm">Unggah</a>
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#delete{{ $item->id }}">Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- modal update -->
                                        <div id="update{{ $item->id }}" class="modal fade" tabindex="-1" role="dialog"
                                            aria-labelledby="update{{ $item->id }}Label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
                                                        <i class="ri-error-warning-line text-danger" style="font-size: 60px;"></i>
                                                        <p class="mb-4">
                                                            Apakah Anda yakin ingin menghapus Data Warga dengan nama
                                                            <strong style="font-size: 1rem;">{{ ucwords($item->name) }}
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createLabel">Create</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="custom-validation" action="{{ route('alternatif.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label>NIK</label>
                            <input type="text" class="form-control" name="NIK" required
                                placeholder="Masukkan NIK" />
                        </div>
                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" class="form-control" name="name" required
                                placeholder="Masukkan Nama" />
                        </div>

                        <div class="mb-3">
                            <label>Alamat</label>
                            <input type="text" class="form-control" name="alamat" required
                                placeholder="Masukkan alamat" />
                        </div>

                        <div class="mb-3">
                            <label>Nomor Hp</label>
                            <input type="text" class="form-control" name="nomor" required
                                placeholder="Masukkan Nomor Hp" />
                        </div>
                        <div class="mb-0">
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary waves-effect waves-light me-1">
                                    Submit
                                </button>
                                <button type="reset" class="btn btn-secondary waves-effect">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
