@extends('admin.components.main')

@section('title', 'Manajemen Penerbit')
@section('page-title', 'Manajemen Penerbit')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Penerbit</h6>
            <a href="{{ route('admin.publishers.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Penerbit
            </a>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')

            @if ($publishers->isEmpty())
                <div class="alert alert-info text-center">
                    Belum ada data penerbit.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped datatable" id="dataTablePublishers"
                        width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center no-sort" width="1%">No</th>
                                <th>Nama Penerbit</th>
                                <th>Alamat</th>
                                <th class="text-center action-column no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($publishers as $publisher)
                                <tr class="align-middle">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $publisher->name }}</td>
                                    <td>{{ Str::limit($publisher->address, 70, '...') }}</td> {{-- Tingkatkan limit alamat --}}
                                    <td class="action-column text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.publishers.show', $publisher) }}"
                                                class="btn btn-info" title="Detail">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            <a href="{{ route('admin.publishers.edit', $publisher) }}"
                                                class="btn btn-warning" title="Edit">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" title="Hapus"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $publisher->id }}">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @foreach ($publishers as $publisher)
                    <div class="modal fade" id="deleteModal-{{ $publisher->id }}" tabindex="-1"
                        aria-labelledby="deleteModalLabel-{{ $publisher->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="deleteModalLabel-{{ $publisher->id }}">Konfirmasi Hapus
                                    </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus penerbit: <strong>{{ $publisher->name }}</strong>?
                                    Tindakan ini tidak dapat dibatalkan dan mungkin memengaruhi data buku terkait.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <form action="{{ route('admin.publishers.destroy', $publisher) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection

@section('css')
    <style>
        .action-column {
            white-space: nowrap;
            width: 1%;
            text-align: center
        }

        .action-column .btn .bi {
            vertical-align: middle
        }
    </style>
@endsection

@section('script')
    @include('admin.components.datatable_script', ['table_id' => 'dataTablePublishers'])
@endsection
