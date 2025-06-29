@extends('admin.components.main')

@section('title', 'Detail Siswa')
@section('page-title')
    Detail Siswa: {{ $siteUser->name }}
@endsection

@section('content')
    {{-- Card untuk Detail Siswa --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Siswa</h6>
            <div>
                <a href="{{ route('admin.site-users.edit', $siteUser) }}" class="btn btn-warning btn-sm" title="Edit Siswa">
                    <i class="bi bi-pencil-fill me-1"></i> Edit
                </a>
                <a href="{{ route('admin.site-users.index') }}" class="btn btn-secondary btn-sm" title="Kembali ke Daftar">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">NIS</dt>
                <dd class="col-sm-9">{{ $siteUser->nis }}</dd>

                <dt class="col-sm-3">Nama Lengkap</dt>
                <dd class="col-sm-9">{{ $siteUser->name }}</dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $siteUser->email }}</dd>

                <dt class="col-sm-3">Kelas</dt>
                <dd class="col-sm-9">{{ $siteUser->class ?: '-' }}</dd>

                <dt class="col-sm-3">Jurusan</dt>
                <dd class="col-sm-9">{{ $siteUser->major ?: '-' }}</dd>

                <dt class="col-sm-3">Tanggal Daftar</dt>
                <dd class="col-sm-9">
                    {{ $siteUser->created_at ? $siteUser->created_at->isoFormat('D MMMM YYYY, HH:mm') : '-' }}</dd>

                <dt class="col-sm-3">Terakhir Update</dt>
                <dd class="col-sm-9">
                    {{ $siteUser->updated_at ? $siteUser->updated_at->isoFormat('D MMMM YYYY, HH:mm') : '-' }}</dd>
            </dl>
        </div>
    </div>

    {{-- Card untuk Riwayat Peminjaman --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Peminjaman Siswa</h6>
        </div>
        <div class="card-body">
            @if ($siteUser->borrowings->isEmpty())
                <div class="alert alert-info text-center">
                    Siswa ini belum pernah melakukan peminjaman.
                </div>
            @else
                <div class="table-responsive">
                    {{-- Tabel ini mungkin tidak perlu DataTables jika datanya tidak terlalu banyak per siswa --}}
                    <table class="table table-bordered table-sm table-striped" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Judul Buku</th>
                                <th>Kode Eksemplar</th>
                                <th>Tgl Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Tgl Kembali</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($siteUser->borrowings as $index => $borrowing)
                                <tr class="align-middle">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $borrowing->bookCopy?->book?->title ?: 'N/A' }}</td>
                                    <td>{{ $borrowing->bookCopy?->copy_code ?: 'N/A' }}</td>
                                    <td>{{ $borrowing->borrow_date ? \Carbon\Carbon::parse($borrowing->borrow_date)->isoFormat('D MMM YYYY') : '-' }}
                                    </td>
                                    <td>{{ $borrowing->due_date ? \Carbon\Carbon::parse($borrowing->due_date)->isoFormat('D MMM YYYY') : '-' }}
                                    </td>
                                    <td>{{ $borrowing->return_date ? \Carbon\Carbon::parse($borrowing->return_date)->isoFormat('D MMM YYYY') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($borrowing->status)
                                            <span class="badge bg-{{ $borrowing->status->badgeColor() }}">
                                                {{ $borrowing->status->label() }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Jika ingin pagination untuk riwayat, perlu modifikasi controller --}}
                {{-- {{ $siteUser->borrowings->links() }} --}}
            @endif
        </div>
    </div>
@endsection

@section('css')
    <style>
        dl.row dt {
            margin-bottom: 0.5rem;
        }
    </style>
@endsection

@section('script')
    {{-- Tidak perlu script DataTables untuk tabel riwayat di halaman detail ini --}}
@endsection
