@extends('admin.components.main')

@section('title', 'Manajemen Siswa')
@section('page-title', 'Manajemen Siswa')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Semua Siswa</h6>
            <div>
                <a href="{{ route('admin.site-users.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Siswa Manual
                </a>
            </div>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')

            @if ($siteUsers->isEmpty())
                <div class="alert alert-info text-center">
                    Belum ada data siswa.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover datatable" id="dataTableSiteUsers" width="100%"
                        cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Kelas</th>
                                <th class="text-center action-column">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($siteUsers as $user)
                                <tr class="align-middle">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $user->nis }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->class ?: '-' }}</td>
                                    <td class="text-center action-column">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.site-users.show', $user) }}" class="btn btn-info"
                                                title="Detail">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            <a href="{{ route('admin.site-users.edit', $user) }}" class="btn btn-warning"
                                                title="Edit">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" title="Hapus"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $user->id }}">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @foreach ($siteUsers as $user)
                    <div class="modal fade" id="deleteModal-{{ $user->id }}" tabindex="-1"
                        aria-labelledby="deleteModalLabel-{{ $user->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="deleteModalLabel-{{ $user->id }}">Konfirmasi Hapus
                                    </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus siswa: <strong>{{ $user->name }}
                                        ({{ $user->nis }})</strong>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <form action="{{ route('admin.site-users.destroy', $user) }}" method="POST"
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

@section('script')
    @include('admin.components.datatable_script', ['table_id' => 'dataTableSiteUsers'])
@endsection
