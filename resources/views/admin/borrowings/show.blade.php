@extends('admin.components.main')

@section('title', 'Detail Peminjaman')
@section('page-title')
    Detail Peminjaman: {{ $borrowing->bookCopy?->book?->title ?? 'N/A' }}
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Peminjaman</h6>
                    @if (in_array($borrowing->status, [\App\Enum\BorrowingStatus::Borrowed, \App\Enum\BorrowingStatus::Overdue]))
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                            data-bs-target="#returnModal-{{ $borrowing->id }}">
                            <i class="bi bi-check-circle-fill me-1"></i> Proses Pengembalian
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @include('admin.components.flash_messages')
                    @if ($errors->hasBag('return_' . $borrowing->id))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading">Gagal Memproses Pengembalian:</h6>
                            <ul>
                                @foreach ($errors->{'return_' . $borrowing->id}->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <dl class="row">
                        <dt class="col-sm-4 col-md-3">Status Peminjaman</dt>
                        <dd class="col-sm-8 col-md-9">
                            @if ($borrowing->status)
                                <span
                                    class="badge bg-{{ $borrowing->status->badgeColor() }} fs-6">{{ $borrowing->status->label() }}</span>
                            @else
                                -
                            @endif
                        </dd>
                        <dt class="col-sm-4 col-md-3">Tanggal Pinjam</dt>
                        <dd class="col-sm-8 col-md-9">
                            {{ $borrowing->borrow_date ? $borrowing->borrow_date->isoFormat('dddd, D MMMM YYYY') : '-' }}
                        </dd>
                        <dt class="col-sm-4 col-md-3">Jatuh Tempo</dt>
                        <dd class="col-sm-8 col-md-9">
                            {{ $borrowing->due_date ? $borrowing->due_date->isoFormat('dddd, D MMMM YYYY') : '-' }}</dd>
                        <dt class="col-sm-4 col-md-3">Tanggal Kembali</dt>
                        <dd class="col-sm-8 col-md-9">
                            {{ $borrowing->return_date ? $borrowing->return_date->isoFormat('dddd, D MMMM YYYY') : '-' }}
                        </dd>
                        <dt class="col-sm-4 col-md-3">Admin Peminjam</dt>
                        <dd class="col-sm-8 col-md-9">{{ $borrowing->loanProcessor?->name ?: '-' }}</dd>
                        <dt class="col-sm-4 col-md-3">Admin Pengembali</dt>
                        <dd class="col-sm-8 col-md-9">{{ $borrowing->returnProcessor?->name ?: '-' }}</dd>
                        <dt class="col-sm-4 col-md-3">Denda</dt>
                        <dd class="col-sm-8 col-md-9">
                            @if ($borrowing->fine)
                                Rp {{ number_format($borrowing->fine->amount, 0, ',', '.') }}
                                <span class="ms-2 badge bg-{{ $borrowing->fine->status->badgeColor() }}">
                                    {{ $borrowing->fine->status->label() }}
                                </span>
                                <a href="{{ route('admin.fines.show', $borrowing->fine) }}"
                                    class="badge bg-info ms-1 text-decoration-none" title="Lihat Detail Denda">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                @if ($borrowing->fine->notes)
                                    <small class="d-block text-muted mt-1"><em>Catatan:
                                            {{ $borrowing->fine->notes }}</em></small>
                                @endif
                                @if ($borrowing->fine->status === App\Enum\FineStatus::Paid || $borrowing->fine->status === App\Enum\FineStatus::Waived)
                                    <small class="d-block text-muted">Diproses pada
                                        {{ $borrowing->fine->payment_date?->isoFormat('D MMM YYYY, HH:mm') }}
                                        oleh {{ $borrowing->fine->paymentProcessor?->name ?: 'N/A' }}
                                    </small>
                                @endif
                            @else
                                -
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Buku Yang Dipinjam</h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4 col-md-3">Judul</dt>
                        <dd class="col-sm-8 col-md-9">{{ $borrowing->bookCopy?->book?->title ?: '-' }}</dd>
                        <dt class="col-sm-4 col-md-3">Kode Eksemplar</dt>
                        <dd class="col-sm-8 col-md-9">{{ $borrowing->bookCopy?->copy_code ?: '-' }}</dd>
                        <dt class="col-sm-4 col-md-3">ISBN</dt>
                        <dd class="col-sm-8 col-md-9">{{ $borrowing->bookCopy?->book?->isbn ?: '-' }}</dd>
                        <dt class="col-sm-4 col-md-3">Lokasi Rak</dt>
                        <dd class="col-sm-8 col-md-9">{{ $borrowing->bookCopy?->book?->location ?: '-' }}</dd>
                        <dt class="col-sm-4 col-md-3">Kondisi Eksemplar</dt>
                        <dd class="col-sm-8 col-md-9">
                            @if ($borrowing->bookCopy?->condition)
                                <span class="badge bg-{{ $borrowing->bookCopy->condition->badgeColor() }}">
                                    {{ $borrowing->bookCopy->condition->label() }}
                                </span>
                            @else
                                -
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Siswa Peminjam</h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">NIS</dt>
                        <dd class="col-sm-8">{{ $borrowing->siteUser?->nis ?: '-' }}</dd>
                        <dt class="col-sm-4">Nama</dt>
                        <dd class="col-sm-8">{{ $borrowing->siteUser?->name ?: '-' }}</dd>
                        <dt class="col-sm-4">Kelas</dt>
                        <dd class="col-sm-8">{{ $borrowing->siteUser?->class ?: '-' }}</dd>
                        <dt class="col-sm-4">Jurusan</dt>
                        <dd class="col-sm-8">{{ $borrowing->siteUser?->major ?: '-' }}</dd>
                    </dl>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('admin.site-users.show', $borrowing->siteUser) }}"
                        class="btn btn-sm btn-outline-primary">Lihat Detail Siswa</a>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-start mb-4">
        <a href="{{ route('admin.borrowings.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Peminjaman
        </a>
    </div>

    @if (in_array($borrowing->status, [\App\Enum\BorrowingStatus::Borrowed, \App\Enum\BorrowingStatus::Overdue]))
        <div class="modal fade" id="returnModal-{{ $borrowing->id }}" tabindex="-1"
            aria-labelledby="returnModalLabel-{{ $borrowing->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.borrowings.return', $borrowing) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="returnModalLabel-{{ $borrowing->id }}">Konfirmasi Pengembalian
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Anda akan memproses pengembalian untuk:</p>
                            <ul>
                                <li>Buku: <strong>{{ $borrowing->bookCopy?->book?->title ?? 'N/A' }}</strong></li>
                                <li>Kode Eksemplar: <strong>{{ $borrowing->bookCopy?->copy_code ?? 'N/A' }}</strong></li>
                                <li>Peminjam: <strong>{{ $borrowing->siteUser?->name ?? 'N/A' }}</strong></li>
                                <li>Jatuh Tempo:
                                    <strong>{{ $borrowing->due_date ? $borrowing->due_date->isoFormat('D MMM YYYY') : '-' }}</strong>
                                </li>
                            </ul>
                            <p>Sistem akan menghitung denda secara otomatis jika ada keterlambatan.</p>
                            <div class="mb-3">
                                <label for="return_notes-{{ $borrowing->id }}" class="form-label">Catatan Pengembalian
                                    (Opsional):</label>
                                <textarea class="form-control @error('return_notes', 'return_' . $borrowing->id) is-invalid @enderror"
                                    id="return_notes-{{ $borrowing->id }}" name="return_notes" rows="3">{{ old('return_notes') }}</textarea>
                                @error('return_notes', 'return_' . $borrowing->id)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle-fill me-1"></i> Ya, Proses Pengembalian
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

@endsection

@section('css')
    <style>
        dl.row dt {
            margin-bottom: 0.75rem;
            font-weight: 600;
        }

        dl.row dd {
            margin-bottom: 0.75rem;
        }
    </style>
@endsection

@section('script')
    <script>
        @if ($errors->hasBag('return_' . $borrowing->id))
            var returnModalInstance = document.getElementById('returnModal-{{ $borrowing->id }}');
            if (returnModalInstance) {
                var modal = new bootstrap.Modal(returnModalInstance);
                modal.show();
            }
        @endif
    </script>
@endsection
