@extends('admin.components.main')

@section('title', 'Detail Booking')
@section('page-title')
    Detail Booking ID: {{ $booking->id }}
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Booking & Buku</h6>
                    <div>
                        @if ($booking->status === App\Enum\BookingStatus::Active)
                            <button type="button" class="btn btn-success btn-sm" title="Konversi ke Peminjaman"
                                data-bs-toggle="modal" data-bs-target="#convertModal-{{ $booking->id }}">
                                <i class="bi bi-check2-square me-1"></i> Konversi
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" title="Batalkan Booking"
                                data-bs-toggle="modal" data-bs-target="#cancelModal-{{ $booking->id }}">
                                <i class="bi bi-x-lg me-1"></i> Batal
                            </button>
                        @endif
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary btn-sm"
                            title="Kembali ke Daftar Booking">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @include('admin.components.flash_messages')
                    @include('admin.components.validation_errors')

                    <dl class="row">
                        <dt class="col-sm-4">ID Booking</dt>
                        <dd class="col-sm-8">{{ $booking->id }}</dd>

                        <dt class="col-sm-4">Status Booking</dt>
                        <dd class="col-sm-8">
                            @if ($booking->status)
                                <span
                                    class="badge fs-6 bg-{{ $booking->status->badgeColor() }}">{{ $booking->status->label() }}</span>
                                @if ($booking->status == App\Enum\BookingStatus::Active && $booking->expiry_date < now())
                                    <i class="bi bi-clock-history text-danger ms-1"
                                        title="Sudah Melewati Batas Pengambilan"></i>
                                @endif
                            @else
                                -
                            @endif
                        </dd>

                        <dt class="col-sm-4">Tanggal Booking</dt>
                        <dd class="col-sm-8">
                            {{ $booking->booking_date ? $booking->booking_date->isoFormat('dddd, D MMMM YYYY HH:mm') : '-' }}
                        </dd>

                        <dt class="col-sm-4">Batas Pengambilan</dt>
                        <dd
                            class="col-sm-8 {{ $booking->status == App\Enum\BookingStatus::Active && $booking->expiry_date < now() ? 'text-danger fw-bold' : '' }}">
                            {{ $booking->expiry_date ? $booking->expiry_date->isoFormat('dddd, D MMMM YYYY HH:mm') : '-' }}
                        </dd>

                        <dt class="col-sm-4">Catatan Booking</dt>
                        <dd class="col-sm-8" style="white-space: pre-wrap;">{!! nl2br(e($booking->notes)) ?: '-' !!}</dd>


                        <hr class="my-3">
                        <dt class="col-sm-4">Judul Buku</dt>
                        <dd class="col-sm-8">{{ $booking->book?->title ?? '-' }}
                            @if ($booking->book)
                                <a href="{{ route('admin.books.show', $booking->book) }}"
                                    class="badge bg-light text-dark text-decoration-none ms-1" title="Lihat Detail Buku"><i
                                        class="bi bi-box-arrow-up-right"></i></a>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Pengarang</dt>
                        <dd class="col-sm-8">{{ $booking->book?->author?->name ?? '-' }}</dd>

                        <dt class="col-sm-4">Penerbit</dt>
                        <dd class="col-sm-8">{{ $booking->book?->publisher?->name ?? '-' }}</dd>

                        <dt class="col-sm-4">Kategori</dt>
                        <dd class="col-sm-8">{{ $booking->book?->category?->name ?? '-' }}</dd>

                        <dt class="col-sm-4">ISBN</dt>
                        <dd class="col-sm-8">{{ $booking->book?->isbn ?? '-' }}</dd>

                        <dt class="col-sm-4">Lokasi Rak</dt>
                        <dd class="col-sm-8">{{ $booking->book?->location ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Siswa Pemesan</h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        {{-- Detail siswa --}}
                        <dt class="col-sm-4">NIS</dt>
                        <dd class="col-sm-8">{{ $booking->siteUser?->nis ?? '-' }}</dd>
                        <dt class="col-sm-4">Nama</dt>
                        <dd class="col-sm-8">{{ $booking->siteUser?->name ?? '-' }}</dd>
                        <dt class="col-sm-4">Kelas</dt>
                        <dd class="col-sm-8">{{ $booking->siteUser?->class ?? '-' }}</dd>
                        <dt class="col-sm-4">Jurusan</dt>
                        <dd class="col-sm-8">{{ $booking->siteUser?->major ?? '-' }}</dd>
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $booking->siteUser?->email ?? '-' }}</dd>
                    </dl>
                </div>
                @if ($booking->siteUser)
                    <div class="card-footer text-end">
                        <a href="{{ route('admin.site-users.show', $booking->siteUser) }}"
                            class="btn btn-sm btn-outline-primary">Lihat Detail Siswa</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($booking->status === App\Enum\BookingStatus::Active)
        <div class="modal fade" id="convertModal-{{ $booking->id }}" tabindex="-1"
            aria-labelledby="convertModalLabel-{{ $booking->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.bookings.convert', $booking) }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="convertModalLabel-{{ $booking->id }}">
                                Konversi Booking ke Peminjaman</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Anda akan mengonversi booking ini menjadi peminjaman:</p>
                            <ul>
                                <li>Buku: <strong>{{ $booking->book?->title ?? 'N/A' }}</strong></li>
                                <li>Pemesan: <strong>{{ $booking->siteUser?->name ?? 'N/A' }}</strong></li>
                                <li>Eksemplar:
                                    <strong>{{ $booking->bookCopy?->copy_code ?? 'N/A (Harap Cek!)' }}</strong>
                                </li>
                            </ul>

                            {{-- Opsional: Tambahkan Textarea Notes Admin --}}
                            <div class="mb-3">
                                <label for="admin_notes-convert-{{ $booking->id }}" class="form-label">Catatan Konversi
                                    (Opsional):</label>
                                <textarea class="form-control @error('admin_notes', 'convert_' . $booking->id) is-invalid @enderror"
                                    id="admin_notes-convert-{{ $booking->id }}" name="admin_notes" rows="2">{{ old('admin_notes') }}</textarea>
                                @error('admin_notes', 'convert_' . $booking->id)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <p><small>Pastikan Anda memberikan eksemplar yang benar kepada siswa.</small>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-square me-1"></i> Konversi Jadi Peminjaman
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="cancelModal-{{ $booking->id }}" tabindex="-1"
            aria-labelledby="cancelModalLabel-{{ $booking->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="cancelModalLabel-{{ $booking->id }}">Konfirmasi Pembatalan
                                Booking</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Anda yakin ingin membatalkan booking untuk:</p>
                            <ul>
                                <li>Buku: <strong>{{ $booking->book?->title ?? 'N/A' }}</strong></li>
                                <li>Pemesan: <strong>{{ $booking->siteUser?->name ?? 'N/A' }}</strong></li>
                            </ul>
                            <div class="mb-3">
                                <label for="admin_notes-cancel-show-{{ $booking->id }}" class="form-label">Alasan /
                                    Catatan Pembatalan (Opsional):</label>
                                <textarea class="form-control @error('admin_notes') is-invalid @enderror"
                                    id="admin_notes-cancel-show-{{ $booking->id }}" name="admin_notes" rows="2">{{ old('admin_notes') }}</textarea>
                                @error('admin_notes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                            <button type="submit" class="btn btn-danger">Ya, Batalkan Booking</button>
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
