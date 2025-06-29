@extends('admin.components.main')

@section('title', 'Daftar Peminjaman')
@section('page-title', 'Daftar Semua Peminjaman')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Peminjaman Buku</h6>
            <a href="{{ route('admin.borrowings.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Peminjaman Baru
            </a>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')

            @if ($borrowings->isEmpty())
                <div class="alert alert-info text-center">
                    Belum ada data peminjaman.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped datatable" id="dataTableBorrowings"
                        width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center no-sort" width="1%">No</th>
                                <th>Peminjam</th>
                                <th>Judul Buku</th>
                                <th>Kode Eksemplar</th>
                                <th>Tgl Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Tgl Kembali</th>
                                <th class="text-center">Status</th>
                                <th class="text-center action-column no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($borrowings as $borrowing)
                                <tr class="align-middle">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $borrowing->siteUser?->name ?: 'N/A' }} <br><small class="text-muted">NIS:
                                            {{ $borrowing->siteUser?->nis ?: 'N/A' }}</small></td>
                                    <td>{{ $borrowing->bookCopy?->book?->title ?: 'N/A' }}</td>
                                    <td>{{ $borrowing->bookCopy?->copy_code ?: 'N/A' }}</td>
                                    <td>{{ $borrowing->borrow_date ? $borrowing->borrow_date->isoFormat('D MMM YYYY') : '-' }}
                                    </td>
                                    <td>{{ $borrowing->due_date ? $borrowing->due_date->isoFormat('D MMM YYYY') : '-' }}
                                    </td>
                                    <td>{{ $borrowing->return_date ? $borrowing->return_date->isoFormat('D MMM YYYY') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($borrowing->status)
                                            <span
                                                class="badge bg-{{ $borrowing->status->badgeColor() }}">{{ $borrowing->status->label() }}</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td class="action-column text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.borrowings.show', $borrowing) }}" class="btn btn-info"
                                                title="Detail">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            @if (in_array($borrowing->status, [\App\Enum\BorrowingStatus::Borrowed, \App\Enum\BorrowingStatus::Overdue]))
                                                <button type="button" class="btn btn-success" title="Proses Pengembalian"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#returnModal-{{ $borrowing->id }}">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            @endif
                                            @if (!$borrowing->status->isActive())
                                                <button type="button" class="btn btn-danger" title="Hapus Riwayat"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal-{{ $borrowing->id }}">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @foreach ($borrowings as $borrowing)
                    @if (!$borrowing->status->isActive())
                        <div class="modal fade" id="deleteModal-{{ $borrowing->id }}" tabindex="-1"
                            aria-labelledby="deleteModalLabel-{{ $borrowing->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="deleteModalLabel-{{ $borrowing->id }}">Konfirmasi
                                            Hapus</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus riwayat peminjaman buku
                                        <strong>{{ $borrowing->bookCopy?->book?->title }}</strong> oleh
                                        <strong>{{ $borrowing->siteUser?->name }}</strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('admin.borrowings.destroy', $borrowing) }}" method="POST"
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

                    {{-- Modal Proses Pengembalian (jika aktif) --}}
                    @if (in_array($borrowing->status, [\App\Enum\BorrowingStatus::Borrowed, \App\Enum\BorrowingStatus::Overdue]))
                        <div class="modal fade" id="returnModal-{{ $borrowing->id }}" tabindex="-1"
                            aria-labelledby="returnModalLabel-{{ $borrowing->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.borrowings.return', $borrowing) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="returnModalLabel-{{ $borrowing->id }}">
                                                Konfirmasi Pengembalian</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Anda akan memproses pengembalian untuk:</p>
                                            <ul>
                                                <li>Buku:
                                                    <strong>{{ $borrowing->bookCopy?->book?->title ?? 'N/A' }}</strong>
                                                </li>
                                                <li>Kode Eksemplar:
                                                    <strong>{{ $borrowing->bookCopy?->copy_code ?? 'N/A' }}</strong>
                                                </li>
                                                <li>Peminjam: <strong>{{ $borrowing->siteUser?->name ?? 'N/A' }}</strong>
                                                </li>
                                                <li>Jatuh Tempo:
                                                    <strong>{{ $borrowing->due_date ? $borrowing->due_date->isoFormat('D MMM YYYY') : '-' }}</strong>
                                                </li>
                                            </ul>
                                            <p>Sistem akan menghitung denda secara otomatis jika ada keterlambatan.</p>
                                            <div class="mb-3">
                                                <label for="return_notes-{{ $borrowing->id }}" class="form-label">Catatan
                                                    Pengembalian (Opsional):</label>
                                                <textarea class="form-control @error('return_notes') is-invalid @enderror" id="return_notes-{{ $borrowing->id }}"
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
            text-align: center;
        }

        .action-column .btn .bi {
            vertical-align: middle;
        }
    </style>
@endsection

@section('script')
    @include('admin.components.datatable_script', ['table_id' => 'dataTableBorrowings'])
    <script>
        @foreach ($borrowings as $borrowing)
            @if ($errors->hasBag('return_' . $borrowing->id))
                var returnModal = new bootstrap.Modal(document.getElementById('returnModal-{{ $borrowing->id }}'));
                if (returnModal) {
                    returnModal.show();
                }
            @endif
        @endforeach
    </script>
@endsection
