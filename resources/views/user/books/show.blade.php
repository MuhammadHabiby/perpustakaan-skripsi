@extends('user.components.main')

@section('title', $book->title)
@section('page-title', 'Detail Buku')

@section('content')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4 text-center text-md-start">
                    <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('assets/images/no-image-book-detail.png') }}"
                        class="img-fluid rounded shadow-sm book-detail-cover" alt="{{ $book->title }}">
                </div>

                <div class="col-md-8">
                    <h1 class="h3 fw-bold text-primary mb-2">{{ $book->title }}</h1>
                    <p class="mb-1">
                        <span class="fw-semibold">Pengarang:</span>
                        {{ $book->author?->name ?? '-' }}
                    </p>
                    <p class="mb-3">
                        <span class="fw-semibold">Penerbit:</span>
                        {{ $book->publisher?->name ?? '-' }} (Tahun: {{ $book->publication_year ?? '-' }})
                    </p>
                    <p class="mb-1">
                        <span class="fw-semibold">Kategori:</span>
                        <span class="badge bg-secondary">{{ $book->category?->name ?? '-' }}</span>
                    </p>
                    <p class="mb-3">
                        <span class="fw-semibold">ISBN:</span> {{ $book->isbn ?? '-' }}
                    </p>
                    <p class="mb-1">
                        <span class="fw-semibold">Lokasi Rak:</span> {{ $book->location ?? '-' }}
                    </p>
                    <p class="mb-3">
                        <span class="fw-semibold">Ketersediaan:</span>
                        @if ($totalCopies > 0)
                            <span class="fw-bold {{ $availableCopiesCount > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $availableCopiesCount }}
                            </span> dari <strong>{{ $totalCopies }}</strong> eksemplar tersedia
                        @else
                            <span class="text-danger fw-bold">Tidak ada eksemplar terdaftar</span>
                        @endif
                    </p>

                    <div class="mt-3 pt-3 border-top">

                        @auth('web')
                            @if ($userStatus === 'borrowing' && $statusDetails)
                                <div class="alert alert-info d-flex align-items-center" role="alert">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <div>
                                        Anda sedang meminjam buku ini. Jatuh tempo pada:
                                        <strong>{{ \Carbon\Carbon::parse($statusDetails)->isoFormat('dddd, D MMMM YYYY') }}</strong>.
                                        <a href="{{ route('user.borrowings.history') }}" class="alert-link">Lihat Riwayat</a>.
                                    </div>
                                </div>
                            @elseif ($userStatus === 'booked' && $statusDetails)
                                <div class="alert alert-info d-flex align-items-center" role="alert">
                                    <i class="bi bi-journal-bookmark-fill me-2"></i>
                                    <div>
                                        Anda sudah memiliki booking aktif untuk buku ini. Batas pengambilan:
                                        <strong>{{ \Carbon\Carbon::parse($statusDetails)->isoFormat('dddd, D MMMM YYYY, HH:mm') }}</strong>.
                                        <a href="{{ route('user.bookings.index') }}" class="alert-link">Lihat Booking Saya</a>.
                                    </div>
                                </div>
                            @elseif ($userStatus === 'unavailable')
                                <div class="alert alert-warning d-flex align-items-center" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div>{{ $statusDetails ?? 'Stok buku ini sedang tidak tersedia.' }}</div>
                                </div>
                            @elseif ($userStatus === 'limit_reached')
                                <div class="alert alert-warning d-flex align-items-center" role="alert">
                                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                                    <div>Anda sudah mencapai batas maksimal booking aktif ({{ $statusDetails }} buku). <a
                                            href="{{ route('user.bookings.index') }}" class="alert-link">Lihat Booking
                                            Saya</a>.</div>
                                </div>
                            @elseif($userStatus === 'inactive')
                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                    <i class="bi bi-exclamation-octagon-fill me-2"></i>
                                    <div>Akun Anda belum aktif, tidak dapat melakukan booking.</div>
                                </div>
                            @endif
                        @endauth

                        <div class="d-grid d-md-block mt-3">
                            @auth('web')
                                @if ($userStatus === 'can_book')
                                    <form action="{{ route('user.bookings.store', $book) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-journal-bookmark-fill me-1"></i> Booking Buku Ini
                                        </button>
                                        <small class="text-muted ms-2 d-block d-md-inline mt-2 mt-md-0">
                                            Batas pengambilan: {{ setting('booking_expiry_days', 2) }} hari.
                                        </small>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-secondary btn-lg" disabled>
                                        <i class="bi bi-journal-x me-1"></i> Tidak Bisa Booking
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}?redirect={{ url()->current() }}" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Login untuk Booking
                                </a>
                            @endauth
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-semibold">Sinopsis</h5>
                    <div class="text-muted synopsis-text">
                        {!! nl2br(e($book->synopsis ?: 'Sinopsis tidak tersedia.')) !!}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Katalog
        </a>
    </div>
@endsection

@section('css')
    <style>
        .book-detail-cover {
            max-height: 450px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
        }

        .synopsis-text {
            line-height: 1.7;
        }

        .alert .bi {
            vertical-align: -0.125em;
            font-size: 1.1em;
        }
    </style>
@endsection

@section('script')
@endsection
