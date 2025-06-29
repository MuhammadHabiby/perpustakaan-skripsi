@extends('admin.components.main')

@section('title', 'Buku Lewat Tempo')
@section('page-title', 'Daftar Buku Lewat Tempo')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Peminjaman Lewat Tempo</h6>
            <a href="{{ route('admin.borrowings.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-list-ul me-1"></i> Lihat Semua Peminjaman
            </a>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')
            @include('admin.components.validation_errors')

            @if ($overdueBorrowings->isEmpty())
                <div class="alert alert-info text-center">
                    Tidak ada buku yang sedang lewat tempo saat ini.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped datatable" id="dataTableOverdue"
                        width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center no-sort" width="1%">No</th>
                                <th>Peminjam</th>
                                <th>Judul Buku</th>
                                <th>Kode Eksemplar</th>
                                <th>Tgl Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th class="text-center">Terlambat (Hari)</th>
                                <th class="text-center action-column no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($overdueBorrowings as $borrowing)
                                <tr class="align-middle">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $borrowing->siteUser?->name ?? 'N/A' }} <br><small class="text-muted">NIS:
                                            {{ $borrowing->siteUser?->nis ?? 'N/A' }}</small></td>
                                    <td>{{ $borrowing->bookCopy?->book?->title ?? 'N/A' }}</td>
                                    <td>{{ $borrowing->bookCopy?->copy_code ?? 'N/A' }}</td>
                                    <td>{{ $borrowing->borrow_date ? $borrowing->borrow_date->isoFormat('D MM YYYY') : '-' }}
                                    </td>
                                    <td class="text-danger fw-bold">
                                        {{ $borrowing->due_date ? $borrowing->due_date->isoFormat('D MM YYYY') : '-' }}
                                    </td>
                                    <td class="text-center text-danger fw-bold">{{ $borrowing->days_overdue ?? 'N/A' }}</td>
                                    <td class="action-column text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.borrowings.show', $borrowing) }}" class="btn btn-info"
                                                title="Detail">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            <button type="button" class="btn btn-success" title="Proses Pengembalian"
                                                data-bs-toggle="modal" data-bs-target="#returnModal-{{ $borrowing->id }}">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @foreach ($overdueBorrowings as $borrowing)
                    <div class="modal fade" id="returnModal-{{ $borrowing->id }}" tabindex="-1"
                        aria-labelledby="returnModalLabel-{{ $borrowing->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('admin.borrowings.return', $borrowing) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="returnModalLabel-{{ $borrowing->id }}">Konfirmasi
                                            Pengembalian</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Anda akan memproses pengembalian untuk:</p>
                                        <ul>
                                            <li>Buku: <strong>{{ $borrowing->bookCopy?->book?->title ?? 'N/A' }}</strong>
                                            </li>
                                            <li>Kode Eksemplar:
                                                <strong>{{ $borrowing->bookCopy?->copy_code ?? 'N/A' }}</strong>
                                            </li>
                                            <li>Peminjam: <strong>{{ $borrowing->siteUser?->name ?? 'N/A' }}</strong></li>
                                            <li>Jatuh Tempo: <strong
                                                    class="text-danger">{{ $borrowing->due_date ? $borrowing->due_date->isoFormat('D MM YYYY') : '-' }}</strong>
                                            </li>
                                            <li>Terlambat: <strong
                                                    class="text-danger">{{ $borrowing->days_overdue ?? 'N/A' }}
                                                    Hari</strong></li>
                                        </ul>
                                        <p>Sistem akan menghitung denda secara otomatis.</p>
                                        <div class="mb-3">
                                            <label for="return_notes-ovd-{{ $borrowing->id }}" class="form-label">Catatan
                                                Pengembalian (Opsional):</label>
                                            <textarea class="form-control @error('return_notes') is-invalid @enderror" id="return_notes-ovd-{{ $borrowing->id }}"
                                                name="return_notes" rows="3">{{ old('return_notes') }}</textarea>
                                            @error('return_notes')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle-fill me-1"></i> Ya, Proses Pengembalian
                                        </button>
                                    </div>
                                </div>
                            </form>
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
            text-align: center;
        }

        .action-column .btn .bi {
            vertical-align: middle;
        }
    </style>
@endsection

@section('script')
    @include('admin.components.datatable_script', ['table_id' => 'dataTableOverdue'])
@endsection
