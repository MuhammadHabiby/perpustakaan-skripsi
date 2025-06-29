@extends('admin.components.main')

@section('title', 'Detail Laporan Kehilangan')
@section('page-title')
    Detail Laporan Kehilangan #{{ $lost_report->id }}
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Laporan</h6>
                    <div>
                        @if ($lost_report->status === App\Enum\LostReportStatus::Reported)
                            <form action="{{ route('admin.lost-reports.verify', $lost_report) }}" method="POST"
                                class="d-inline ms-1" onsubmit="return confirm('Verifikasi laporan ini?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-primary btn-sm" title="Verifikasi Laporan">
                                    <i class="bi bi-check-circle"></i> Verifikasi
                                </button>
                            </form>
                        @endif
                        @if (in_array($lost_report->status, [App\Enum\LostReportStatus::Reported, App\Enum\LostReportStatus::Verified]))
                            <button type="button" class="btn btn-success btn-sm ms-1" title="Selesaikan Laporan"
                                data-bs-toggle="modal" data-bs-target="#resolveModal-{{ $lost_report->id }}">
                                <i class="bi bi-check2-all"></i> Selesaikan
                            </button>
                        @endif
                        <a href="{{ route('admin.lost-reports.index') }}" class="btn btn-secondary btn-sm ms-1"
                            title="Kembali ke Daftar">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @include('admin.components.flash_messages')
                    @include('admin.components.validation_errors')

                    <dl class="row">
                        <dt class="col-sm-4">ID Laporan</dt>
                        <dd class="col-sm-8">{{ $lost_report->id }}</dd>

                        <dt class="col-sm-4">Status Laporan</dt>
                        <dd class="col-sm-8">
                            @if ($lost_report->status)
                                <span
                                    class="badge fs-6 bg-{{ $lost_report->status->badgeColor() }}">{{ $lost_report->status->label() }}</span>
                            @else
                                -
                            @endif
                        </dd>

                        <dt class="col-sm-4">Tarif Denda Buku Hilang</dt>
                        <dd class="col-sm-8">
                            @if ($lostBookFee > 0)
                                Rp {{ number_format($lostBookFee, 0, ',', '.') }}
                                <small class="text-muted d-block">(Tarif standar sesuai pengaturan sistem jika denda
                                    diterapkan saat penyelesaian)</small>
                            @else
                                - <small class="text-muted d-block">(Tidak ada tarif denda buku hilang di
                                    pengaturan)</small>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Tanggal Dilaporkan</dt>
                        <dd class="col-sm-8">
                            {{ $lost_report->report_date ? $lost_report->report_date->isoFormat('dddd, D MMMM YYYY HH:mm') : '-' }}
                        </dd>

                        @if ($lost_report->verifier)
                            <dt class="col-sm-4">Diverifikasi Oleh</dt>
                            <dd class="col-sm-8">{{ $lost_report->verifier->name }}</dd>
                        @endif

                        @if ($lost_report->status === App\Enum\LostReportStatus::Resolved)
                            <dt class="col-sm-4">Tanggal Diselesaikan</dt>
                            <dd class="col-sm-8">
                                {{ $lost_report->resolution_date ? $lost_report->resolution_date->isoFormat('dddd, D MMMM YYYY HH:mm') : '-' }}
                            </dd>

                            <dt class="col-sm-4">Admin Penyelesaian</dt>
                            <dd class="col-sm-8">{{ $lost_report->verifier?->name ?: '-' }}</dd>

                            <dt class="col-sm-4">Catatan Penyelesaian</dt>
                            <dd class="col-sm-8" style="white-space: pre-wrap;">{!! nl2br(e($lost_report->resolution_notes)) ?: '-' !!}</dd>

                            <dt class="col-sm-4">Denda Tercatat</dt>
                            <dd class="col-sm-8">
                                @if ($lost_report->borrowing?->fine)
                                    Rp {{ number_format($lost_report->borrowing->fine->amount, 0, ',', '.') }}
                                    <span class="ms-2 badge bg-{{ $lost_report->borrowing->fine->status->badgeColor() }}">
                                        {{ $lost_report->borrowing->fine->status->label() }}
                                    </span>
                                    <a href="{{ route('admin.fines.show', $lost_report->borrowing->fine) }}"
                                        class="badge bg-info text-dark text-decoration-none ms-1"
                                        title="Lihat Detail Denda">
                                        <i class="bi bi-eye"></i> Lihat Denda
                                    </a>
                                    @if ($lost_report->borrowing->fine->notes)
                                        <small class="d-block text-muted mt-1"
                                            title="{{ $lost_report->borrowing->fine->notes }}"><em>Catatan Denda:
                                                {{ Str::limit($lost_report->borrowing->fine->notes, 70, '...') }}</em></small>
                                    @endif
                                @else
                                    - (Tidak ada denda tercatat untuk peminjaman ini)
                                @endif
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Buku / Eksemplar</h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Judul Buku</dt>
                        <dd class="col-sm-8">{{ $lost_report->bookCopy?->book?->title ?? '-' }}
                            @if ($lost_report->bookCopy?->book)
                                <a href="{{ route('admin.books.show', $lost_report->bookCopy->book) }}"
                                    class="badge bg-info text-dark text-decoration-none ms-1" title="Lihat Detail Buku"><i
                                        class="bi bi-box-arrow-up-right"></i></a>
                            @endif
                        </dd>
                        <dt class="col-sm-4">Kode Eksemplar</dt>
                        <dd class="col-sm-8">{{ $lost_report->bookCopy?->copy_code ?? '-' }}</dd>
                        <dt class="col-sm-4">Status Eksemplar Saat Ini</dt>
                        <dd class="col-sm-8">
                            @if ($lost_report->bookCopy?->status)
                                <span
                                    class="badge bg-{{ $lost_report->bookCopy->status->badgeColor() }}">{{ $lost_report->bookCopy->status->label() }}</span>
                            @else
                                -
                            @endif
                        </dd>
                        <dt class="col-sm-4">Kondisi Eksemplar</dt>
                        <dd class="col-sm-8">
                            @if ($lost_report->bookCopy?->condition)
                                <span
                                    class="badge bg-{{ $lost_report->bookCopy->condition->badgeColor() }}">{{ $lost_report->bookCopy->condition->label() }}</span>
                            @else
                                -
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            @if ($lost_report->borrowing)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informasi Peminjaman Terkait</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">ID Peminjaman</dt>
                            <dd class="col-sm-8">{{ $lost_report->borrowing->id }}
                                <a href="{{ route('admin.borrowings.show', $lost_report->borrowing) }}"
                                    class="badge bg-info text-dark text-decoration-none ms-1"
                                    title="Lihat Detail Peminjaman"><i class="bi bi-box-arrow-up-right"></i></a>
                            </dd>
                            <dt class="col-sm-4">Tgl Pinjam</dt>
                            <dd class="col-sm-8">
                                {{ $lost_report->borrowing->borrow_date ? $lost_report->borrowing->borrow_date->isoFormat('dddd, D MMMM YYYY HH:mm') : '-' }}
                            </dd>
                            <dt class="col-sm-4">Jatuh Tempo</dt>
                            <dd class="col-sm-8">
                                {{ $lost_report->borrowing->due_date ? $lost_report->borrowing->due_date->isoFormat('dddd, D MMMM YYYY HH:mm') : '-' }}
                            </dd>
                            <dt class="col-sm-4">Status Peminjaman</dt>
                            <dd class="col-sm-8">
                                @if ($lost_report->borrowing->status)
                                    <span
                                        class="badge bg-{{ $lost_report->borrowing->status->badgeColor() }}">{{ $lost_report->borrowing->status->label() }}</span>
                                @else
                                    -
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            @endif

        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Siswa Pelapor</h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">NIS</dt>
                        <dd class="col-sm-8">{{ $lost_report->reporter?->nis ?? '-' }}</dd>
                        <dt class="col-sm-4">Nama</dt>
                        <dd class="col-sm-8">{{ $lost_report->reporter?->name ?? '-' }}</dd>
                        <dt class="col-sm-4">Kelas</dt>
                        <dd class="col-sm-8">{{ $lost_report->reporter?->class ?? '-' }}</dd>
                        <dt class="col-sm-4">Jurusan</dt>
                        <dd class="col-sm-8">{{ $lost_report->reporter?->major ?? '-' }}</dd>
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $lost_report->reporter?->email ?? '-' }}</dd>
                        <dt class="col-sm-4">Telepon</dt>
                        <dd class="col-sm-8">{{ $lost_report->reporter?->phone_number ?? '-' }}</dd>
                    </dl>
                </div>
                @if ($lost_report->reporter)
                    <div class="card-footer text-end">
                        <a href="{{ route('admin.site-users.show', $lost_report->reporter) }}"
                            class="btn btn-sm btn-outline-primary">Lihat Detail Siswa</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if (in_array($lost_report->status, [App\Enum\LostReportStatus::Reported, App\Enum\LostReportStatus::Verified]))
        <div class="modal fade" id="resolveModal-{{ $lost_report->id }}" tabindex="-1"
            aria-labelledby="resolveModalLabel-{{ $lost_report->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.lost-reports.resolve', $lost_report) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="resolveModalLabel-{{ $lost_report->id }}">Selesaikan Laporan
                                Kehilangan</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Anda akan menyelesaikan laporan kehilangan untuk:</p>
                            <ul>
                                <li>Buku: <strong>{{ $lost_report->bookCopy?->book?->title ?? 'N/A' }}</strong> (Kode:
                                    {{ $lost_report->bookCopy?->copy_code ?? 'N/A' }})</li>
                                <li>Pelapor: <strong>{{ $lost_report->reporter?->name ?? 'N/A' }}</strong></li>
                            </ul>
                            <p>Status buku akan diubah menjadi 'Hilang'. Jika terhubung dengan peminjaman dan ada biaya
                                penggantian di pengaturan sistem, denda akan dibuat/diperbaharui.</p>
                            <div class="mb-3">
                                <label for="resolution_notes-show-{{ $lost_report->id }}" class="form-label">Catatan
                                    Penyelesaian <span class="text-danger">*</span></label>
                                {{-- Pesan validasi untuk notes --}}
                                <textarea class="form-control @error('resolution_notes') is-invalid @enderror"
                                    id="resolution_notes-show-{{ $lost_report->id }}" name="resolution_notes" rows="3" required>{{ old('resolution_notes') }}</textarea>
                                @error('resolution_notes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check2-all me-1"></i> Ya, Selesaikan Laporan
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
@endsection
