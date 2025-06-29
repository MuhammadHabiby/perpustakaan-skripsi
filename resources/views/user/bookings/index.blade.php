@extends('user.components.main')

@section('title', 'Booking Saya')
@section('page-title', 'Daftar Booking Buku Saya')

@section('content')

    @include('admin.components.flash_messages')
    @include('admin.components.validation_errors')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary"><i class="bi bi-journal-bookmark-fill me-2"></i>Booking Aktif</h6>
        </div>
        <div class="card-body">
            @if ($activeBookings->isEmpty())
                <div class="alert alert-info text-center mb-0">
                    Anda tidak memiliki booking buku yang aktif saat ini. <a href="{{ route('catalog.index') }}"
                        class="alert-link">Cari Buku?</a>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach ($activeBookings as $booking)
                        <div class="list-group-item px-0 py-3">
                            <div class="row g-3 align-items-center">
                                <div class="col-lg-5 col-md-6 d-flex align-items-center">
                                    <img src="{{ $booking->book?->cover_image ? asset('storage/' . $booking->book->cover_image) : asset('assets/images/no-image-book.png') }}"
                                        alt="{{ $booking->book?->title ?? 'Buku' }}"
                                        class="img-thumbnail me-3 d-none d-sm-block" style="max-width: 50px; height: auto;">
                                    <div>
                                        <a href="{{ route('catalog.show', $booking->book?->slug ?? '#') }}"
                                            class="text-decoration-none text-dark fw-semibold d-block">
                                            {{ $booking->book?->title ?? 'Judul Tidak Diketahui' }}
                                        </a>
                                        <small class="text-muted">Eksemplar: {{ $booking->bookCopy?->copy_code ?? 'N/A' }}
                                            (ID Booking: {{ $booking->id }})
                                        </small>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-6">
                                    <small class="d-block text-muted">Dipesan:
                                        {{ $booking->booking_date?->isoFormat('D MMM YY, HH:mm') ?? '-' }}</small>
                                    <small
                                        class="d-block
                                         @if ($booking->is_expired) text-danger fw-bold @elseif($booking->is_expiring_soon) text-warning fw-bold @else text-muted @endif">
                                        Batas Ambil: {{ $booking->expiry_date?->isoFormat('D MMM YY, HH:mm') ?? '-' }}
                                        @if ($booking->is_expired)
                                            <i class="bi bi-exclamation-triangle-fill ms-1"></i> (Kadaluarsa!)
                                        @elseif($booking->is_expiring_soon)
                                            <i class="bi bi-clock-fill ms-1"></i> (Segera Ambil!)
                                        @endif
                                    </small>
                                </div>
                                <div class="col-lg-2 col-md-12 text-lg-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100 w-lg-auto"
                                        title="Batalkan Booking" data-bs-toggle="modal"
                                        data-bs-target="#userCancelModal-{{ $booking->id }}">
                                        <i class="bi bi-x-lg"></i> Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>Riwayat Booking Terdahulu</h6>
        </div>
        <div class="card-body">
            @if ($pastBookings->isEmpty())
                <div class="alert alert-info text-center mb-0">
                    Anda belum memiliki riwayat booking.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover" id="tablePastBookings">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Judul Buku</th>
                                <th scope="col">Tgl Booking</th>
                                <th scope="col">Tgl Kadaluarsa</th>
                                <th scope="col" class="text-center">Status Akhir</th>
                                <th scope="col">Catatan</th> {{-- Tetap tampilkan kolom --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pastBookings as $booking)
                                <tr class="align-middle">
                                    <td>
                                        <a href="{{ route('catalog.show', $booking->book?->slug ?? '#') }}"
                                            class="text-decoration-none text-dark">
                                            {{ $booking->book?->title ?? 'Judul Tidak Diketahui' }}
                                        </a>
                                    </td>
                                    <td>{{ $booking->booking_date?->isoFormat('D MMM YY, HH:mm') ?? '-' }}</td>
                                    <td>{{ $booking->expiry_date?->isoFormat('D MMM YY, HH:mm') ?? '-' }}</td>
                                    <td class="text-center">
                                        @if ($booking->status)
                                            <span
                                                class="badge bg-{{ $booking->status->badgeColor() }}">{{ $booking->status->label() }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            title="{{ $booking->notes }}">{{ Str::limit($booking->notes, 50, '...') }}</span>
                                        @if (!empty($booking->notes))
                                            <button type="button" class="btn btn-xs btn-outline-secondary ms-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#bookingNotesModal-{{ $booking->id }}"
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
                        @if ($pastBookings->total() > 0)
                            <small class="text-muted">
                                Menampilkan {{ $pastBookings->firstItem() }}
                                hingga {{ $pastBookings->lastItem() }}
                                dari {{ $pastBookings->total() }} riwayat
                            </small>
                        @endif
                    </div>
                    <div>
                        {{ $pastBookings->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    @foreach ($activeBookings as $booking)
        <div class="modal fade" id="userCancelModal-{{ $booking->id }}" tabindex="-1"
            aria-labelledby="userCancelModalLabel-{{ $booking->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('user.bookings.cancel', $booking) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="userCancelModalLabel-{{ $booking->id }}">Konfirmasi
                                Pembatalan Booking</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Anda yakin ingin membatalkan booking untuk buku:</p>
                            <p><strong>{{ $booking->book?->title ?? 'N/A' }}</strong>?</p>
                            <p class="text-muted small">Booking ini akan dibatalkan dan tidak bisa dikembalikan.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                            <button type="submit" class="btn btn-danger">Ya, Batalkan Booking Saya</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    @foreach ($pastBookings as $booking)
        @if (!empty($booking->notes))
            <div class="modal fade" id="bookingNotesModal-{{ $booking->id }}" tabindex="-1"
                aria-labelledby="bookingNotesModalLabel-{{ $booking->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="bookingNotesModalLabel-{{ $booking->id }}">
                                <i class="bi bi-chat-left-text me-2"></i>Catatan Booking
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Buku:</strong> {{ $booking->book?->title ?? 'N/A' }}</p>
                            <p><strong>Status Akhir:</strong>
                                @if ($booking->status)
                                    <span
                                        class="badge bg-{{ $booking->status->badgeColor() }}">{{ $booking->status->label() }}</span>
                                @else
                                    -
                                @endif
                            </p>
                            <hr>
                            <p><strong>Catatan:</strong></p>
                            <p style="white-space: pre-wrap;">{!! nl2br(e($booking->notes)) !!}</p>
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
