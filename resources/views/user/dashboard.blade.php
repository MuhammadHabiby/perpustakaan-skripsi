@extends('user.components.main')

@section('title', 'Dashboard')

@section('page-title')
    Halo, {{ $user->name }}!
@endsection

@section('content')
    <div class="row">

        <div class="col-lg-3 col-md-6 mb-4">
            <a href="{{ route('user.borrowings.history') }}" class="text-decoration-none">
                <div class="card border-start border-warning border-4 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                    Peminjaman Aktif</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $activeBorrowingsCount }} Buku</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-arrow-up-right-square-fill fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <a href="#" class="text-decoration-none">
                <div class="card border-start border-danger border-4 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                    Lewat Tempo</div>
                                <div
                                    class="h5 mb-0 fw-bold text-gray-800 {{ $overdueBorrowingsCount > 0 ? 'text-danger' : '' }}">
                                    {{ $overdueBorrowingsCount }} Buku
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-calendar-x-fill fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <a href="{{ route('user.bookings.index') }}" class="text-decoration-none">
                <div class="card border-start border-info border-4 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                    Booking Aktif</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $activeBookingsCount }} Buku</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-journal-bookmark-fill fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <a href="{{ route('user.fines.index') }}" class="text-decoration-none">
                <div class="card border-start border-danger border-4 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                    Denda Belum Dibayar</div>
                                <div
                                    class="h5 mb-0 fw-bold text-gray-800 {{ $unpaidFinesAmount > 0 ? 'text-danger' : '' }}">
                                    Rp {{ number_format($unpaidFinesAmount, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-cash-coin fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">Buku yang Sedang Dipinjam</h6>
        </div>
        <div class="card-body">
            @if ($currentBorrowings->isEmpty())
                <div class="alert alert-info text-center mb-0">
                    Anda sedang tidak meminjam buku.
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach ($currentBorrowings as $borrowing)
                        <div
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center me-3 mb-2 mb-md-0">
                                <img src="{{ $borrowing->bookCopy?->book?->cover_image ? asset('/storage/' . $borrowing->bookCopy->book->cover_image) : asset('assets/images/no-image.png') }}"
                                    alt="{{ $borrowing->bookCopy?->book?->title ?? 'Buku' }}"
                                    style="width: 40px; height: 60px; object-fit: cover; margin-right: 15px;">
                                <div>
                                    <h6 class="mb-0">{{ $borrowing->bookCopy?->book?->title ?? 'Judul Tidak Diketahui' }}
                                    </h6>
                                    <small class="text-muted">Kode:
                                        {{ $borrowing->bookCopy?->copy_code ?? 'N/A' }}</small><br>
                                    <small>Dipinjam: {{ $borrowing->borrow_date?->isoFormat('D MMM YY') ?? '-' }}</small>
                                </div>
                            </div>
                            <div class="text-md-end">
                                <small>Jatuh Tempo:</small><br>
                                <strong
                                    class="{{ $borrowing->due_date && \Carbon\Carbon::parse($borrowing->due_date)->startOfDay()->lt(\Carbon\Carbon::today()) ? 'text-danger' : '' }}">
                                    {{ $borrowing->due_date?->isoFormat('dddd, D MMM YY') ?? '-' }}
                                </strong>
                                <a href="#" class="btn btn-sm btn-outline-secondary ms-2"><i
                                        class="bi bi-book"></i></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="card-footer text-center">
            <a href="{{ route('user.borrowings.history') }}">Lihat Semua Riwayat Peminjaman</a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">Booking Aktif</h6>
        </div>
        <div class="card-body">
            @if ($activeBookings->isEmpty())
                <div class="alert alert-info text-center mb-0">
                    Anda tidak memiliki booking buku yang aktif.
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach ($activeBookings as $booking)
                        <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center me-3 mb-2 mb-md-0">
                                <img src="{{ asset('/storage/' . $booking->book->cover_image) }}"
                                    style="width: 40px; height: 60px; object-fit: cover; margin-right: 15px;">
                                <div>
                                    <h6 class="mb-0">{{ $booking->book?->title ?? 'Judul Tidak Diketahui' }}</h6>
                                    <small class="text-muted">Dipesan:
                                        {{ $booking->booking_date?->isoFormat('D MMM YY, HH:mm') ?? '-' }}</small><br>
                                    <small class="{{ $booking->expiry_date < now() ? 'text-danger fw-bold' : '' }}">
                                        Batas Ambil: {{ $booking->expiry_date?->isoFormat('D MMM YY, HH:mm') ?? '-' }}
                                        @if ($booking->expiry_date < now())
                                            (Sudah Lewat)
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-book"></i></a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="card-footer text-center">
            <a href="{{ route('user.bookings.index') }}">Lihat Semua Booking</a>
        </div>
    </div>

@endsection

@section('css')
@endsection

@section('script')
@endsection
