@extends('user.components.main')

@section('title', 'Denda Saya')
@section('page-title', 'Rincian Denda Saya')

@section('content')

    <div class="alert {{ $totalUnpaidFines > 0 ? 'alert-danger' : 'alert-success' }} d-flex align-items-center"
        role="alert">
        <i class="bi {{ $totalUnpaidFines > 0 ? 'bi-cash-coin' : 'bi-check-circle-fill' }} fs-4 me-3"></i>
        <div>
            @if ($totalUnpaidFines > 0)
                Total denda yang **belum Anda bayar** saat ini adalah:
                <strong class="fs-5">Rp {{ number_format($totalUnpaidFines, 0, ',', '.') }}</strong>.
                <br><small>Segera selesaikan pembayaran di petugas perpustakaan.</small>
            @else
                Anda tidak memiliki tanggungan denda saat ini. Terima kasih!
            @endif
        </div>
    </div>


    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary"><i class="bi bi-list-ol me-2"></i>Daftar Semua Denda Tercatat</h6>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')

            @if ($fines->isEmpty())
                <div class="alert alert-info text-center mb-0">
                    Anda tidak memiliki catatan denda.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover" id="tableFines">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Tgl Denda Dibuat</th>
                                <th scope="col">Buku</th>
                                <th scope="col">Jumlah (Rp)</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col">Tgl Proses</th>
                                <th scope="col">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fines as $fine)
                                <tr class="align-middle">
                                    <td>{{ $fine->created_at?->isoFormat('D MMM YY, HH:mm') ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('catalog.show', $fine->borrowing?->bookCopy?->book?->slug ?? '#') }}"
                                            class="text-decoration-none text-dark d-block"
                                            title="{{ $fine->borrowing?->bookCopy?->book?->title ?? 'N/A' }}">
                                            {{ Str::limit($fine->borrowing?->bookCopy?->book?->title ?? 'Judul Tidak Diketahui', 40, '...') }}
                                        </a>
                                        <small class="text-muted">Eksemplar:
                                            {{ $fine->borrowing?->bookCopy?->copy_code ?? 'N/A' }}</small>
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($fine->amount, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if ($fine->status)
                                            <span
                                                class="badge bg-{{ $fine->status->badgeColor() }}">{{ $fine->status->label() }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $fine->payment_date?->isoFormat('D MMM YY, HH:mm') ?? '-' }}</td>
                                    <td>
                                        <span title="{{ $fine->notes }}">{{ Str::limit($fine->notes, 40, '...') }}</span>
                                        @if (!empty($fine->notes))
                                            <button type="button" class="btn btn-xs btn-outline-secondary ms-1"
                                                data-bs-toggle="modal" data-bs-target="#fineNotesModal-{{ $fine->id }}"
                                                title="Lihat Catatan Lengkap">
                                                <i class="bi bi-chat-left-text"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            Menampilkan {{ $fines->firstItem() }}
                            hingga {{ $fines->lastItem() }}
                            dari {{ $fines->total() }} hasil
                        </small>
                    </div>
                    <div>
                        {{ $fines->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    @foreach ($fines as $fine)
        @if (!empty($fine->notes))
            <div class="modal fade" id="fineNotesModal-{{ $fine->id }}" tabindex="-1"
                aria-labelledby="fineNotesModalLabel-{{ $fine->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="fineNotesModalLabel-{{ $fine->id }}">
                                <i class="bi bi-chat-left-text me-2"></i>Catatan Denda
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Buku:</strong> {{ $fine->borrowing?->bookCopy?->book?->title ?? 'N/A' }}
                                ({{ $fine->borrowing?->bookCopy?->copy_code ?? 'N/A' }})</p>
                            <p><strong>Jumlah Denda:</strong> Rp {{ number_format($fine->amount, 0, ',', '.') }}</p>
                            <p><strong>Status:</strong>
                                @if ($fine->status)
                                    <span
                                        class="badge bg-{{ $fine->status->badgeColor() }}">{{ $fine->status->label() }}</span>
                                @else
                                    -
                                @endif
                            </p>
                            <hr>
                            <p><strong>Catatan:</strong></p>
                            <p style="white-space: pre-wrap;">{!! nl2br(e($fine->notes)) !!}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

@endsection

@section('css')
    <style>
        .btn-xs {
            --bs-btn-padding-y: .1rem;
            --bs-btn-padding-x: .3rem;
            --bs-btn-font-size: .75rem;
        }
    </style>
@endsection

@section('script')
@endsection
