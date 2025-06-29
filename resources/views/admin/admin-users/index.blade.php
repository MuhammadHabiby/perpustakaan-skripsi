@extends('admin.components.main')

@section('title', 'Manajemen Admin')
@section('page-title', 'Manajemen Admin')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Admin</h6>
            <a href="{{ route('admin.admin-users.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Admin
            </a>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')

            @if ($adminUsers->isEmpty())
                <div class="alert alert-info text-center">
                    Belum ada data admin.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover datatable" id="dataTableAdminUsers" width="100%"
                        cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center no-sort" width="1%">No</th>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th class="text-center action-column no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($adminUsers as $user)
                                <tr class="align-middle">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $user->nip }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-center action-column">
                                        {{-- Jangan tampilkan tombol edit/hapus untuk diri sendiri --}}
                                        @if (Auth::guard('admin')->id() !== $user->id)
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.admin-users.edit', $user) }}"
                                                    class="btn btn-warning" title="Edit">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger" title="Hapus"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal-{{ $user->id }}">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic">(Akun Anda)</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @foreach ($adminUsers as $user)
                    {{-- Jangan buat modal hapus untuk diri sendiri --}}
                    @if (Auth::guard('admin')->id() !== $user->id)
                        <div class="modal fade" id="deleteModal-{{ $user->id }}" tabindex="-1"
                            aria-labelledby="deleteModalLabel-{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="deleteModalLabel-{{ $user->id }}">Konfirmasi
                                            Hapus</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus admin: <strong>{{ $user->name }}
                                            ({{ $user->nip }})</strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('admin.admin-users.destroy', $user) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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
    @include('admin.components.datatable_script', ['table_id' => 'dataTableAdminUsers'])
@endsection
