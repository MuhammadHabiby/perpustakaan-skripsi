@extends('admin.components.main')

@section('title', 'Detail Denda')
@section('page-title')
    Detail Denda (ID: {{ $fine->id }})
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Peminjaman</h6>
                    <div>
                        @if ($fine->status === App\Enum\FineStatus::Unpaid)
                            <button type="button" class="btn btn-success btn-sm" title="Tandai Lunas" data-bs-toggle="modal"
                                data-bs-target="#payModal-{{ $fine->id }}">
                                <i class="bi bi-cash-coin"></i> Bayar
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" title="Bebaskan Denda"
                                data-bs-toggle="modal" data-bs-target="#waiveModal-{{ $fine->id }}">
                                <i class="bi bi-slash-circle"></i> Bebaskan
                            </button>
                        @endif
                        <a href="{{ route('admin.fines.index') }}" class="btn btn-secondary btn-sm"
                            title="Kembali ke Daftar Denda">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @include('admin.components.flash_messages')
                    @include('admin.components.validation_errors')

                    <dl class="row">
                        <dt class="col-sm-4">ID Denda</dt>
                        <dd class="col-sm-8">{{ $fine->id }}</dd>

                        <dt class="col-sm-4">Jumlah Denda</dt>
                        <dd class="col-sm-8">Rp {{ number_format($fine->amount, 0, ',', '.') }}</dd>

                        <dt class="col-sm-4">Status Denda</dt>
                        <dd class="col-sm-8">
                            @if ($fine->status)
                                <span
                                    class="badge fs-6 bg-{{ $fine->status->badgeColor() }}">{{ $fine->status->label() }}</span>
                            @else
                                -
                            @endif
                        </dd>

                        @if ($fine->status !== App\Enum\FineStatus::Unpaid)
                            <dt class="col-sm-4">Tanggal Proses</dt>
                            <dd class="col-sm-8">
                                {{ $fine->payment_date ? $fine->payment_date->isoFormat('dddd, D MMMM YYYY HH:mm') : '-' }}
                            </dd>

                            <dt class="col-sm-4">Diproses Oleh Admin</dt>
                            <dd class="col-sm-8">{{ $fine->paymentProcessor?->name ?: '-' }}</dd>
                        @endif

                        <dt class="col-sm-4">Catatan</dt>
                        <dd class="col-sm-8" style="white-space: pre-wrap;">{!! nl2br(e($fine->notes)) ?: '-' !!}</dd>

                        <hr class="my-3">
                        <dt class="col-sm-4">ID Peminjaman</dt>
                        <dd class="col-sm-8">{{ $fine->borrowing?->id ?? '-' }}
                            @if ($fine->borrowing)
                                <a href="{{ route('admin.borrowings.show', $fine->borrowing) }}"
                                    class="badge bg-info ms-1 text-decoration-none" title="Lihat Detail Peminjaman"><i
                                        class="bi bi-box-arrow-up-right"></i></a>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Tgl Pinjam</dt>
                        <dd class="col-sm-8">
                            {{ $fine->borrowing?->borrow_date ? $fine->borrowing->borrow_date->isoFormat('D MMM YYYY') : '-' }}
                        </dd>

                        <dt class="col-sm-4">Jatuh Tempo</dt>
                        <dd class="col-sm-8">
                            {{ $fine->borrowing?->due_date ? $fine->borrowing->due_date->isoFormat('D MMM YYYY') : '-' }}
                        </dd>

                        <dt class="col-sm-4">Tgl Kembali</dt>
                        <dd class="col-sm-8">
                            {{ $fine->borrowing?->return_date ? $fine->borrowing->return_date->isoFormat('D MMM YYYY') : '-' }}
                        </dd>

                        <hr class="my-3">
                        <dt class="col-sm-4">Judul Buku</dt>
                        <dd class="col-sm-8">{{ $fine->borrowing?->bookCopy?->book?->title ?? '-' }}</dd>

                        <dt class="col-sm-4">Kode Eksemplar</dt>
                        <dd class="col-sm-8">{{ $fine->borrowing?->bookCopy?->copy_code ?? '-' }}</dd>

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
                        <dd class="col-sm-8">{{ $fine->borrowing?->siteUser?->nis ?? '-' }}</dd>
                        <dt class="col-sm-4">Nama</dt>
                        <dd class="col-sm-8">{{ $fine->borrowing?->siteUser?->name ?? '-' }}</dd>
                        <dt class="col-sm-4">Kelas</dt>
                        <dd class="col-sm-8">{{ $fine->borrowing?->siteUser?->class ?? '-' }}</dd>
                        <dt class="col-sm-4">Jurusan</dt>
                        <dd class="col-sm-8">{{ $fine->borrowing?->siteUser?->major ?? '-' }}</dd>
                    </dl>
                </div>
                @if ($fine->borrowing?->siteUser)
                    <div class="card-footer text-end">
                        <a href="{{ route('admin.site-users.show', $fine->borrowing->siteUser) }}"
                            class="btn btn-sm btn-outline-primary">Lihat Detail Siswa</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($fine->status === App\Enum\FineStatus::Unpaid)
        <div class="modal fade" id="payModal-{{ $fine->id }}" tabindex="-1"
            aria-labelledby="payModalLabel-{{ $fine->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.fines.pay', $fine) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="payModalLabel-{{ $fine->id }}">Konfirmasi Pembayaran Denda
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Anda akan menandai lunas denda sebesar <strong>Rp
                                    {{ number_format($fine->amount, 0, ',', '.') }}</strong> untuk:</p>
                            <ul>
                                <li>Peminjam: <strong>{{ $fine->borrowing?->siteUser?->name ?? 'N/A' }}</strong></li>
                                <li>Buku: <strong>{{ $fine->borrowing?->bookCopy?->book?->title ?? 'N/A' }}</strong></li>
                            </ul>
                            <div class="mb-3">
                                <label for="payment_notes-pay-{{ $fine->id }}" class="form-label">Catatan Pembayaran
                                    (Opsional):</label>
                                <textarea class="form-control @error('payment_notes', 'pay_' . $fine->id) is-invalid @enderror"
                                    id="payment_notes-pay-{{ $fine->id }}" name="payment_notes" rows="3">{{ old('payment_notes') }}</textarea>
                                @error('payment_notes', 'pay_' . $fine->id)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle-fill me-1"></i>
                                Ya, Tandai Lunas</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="waiveModal-{{ $fine->id }}" tabindex="-1"
            aria-labelledby="waiveModalLabel-{{ $fine->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.fines.waive', $fine) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="waiveModalLabel-{{ $fine->id }}">Konfirmasi Bebaskan
                                Denda</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah Anda yakin ingin membebaskan denda sebesar <strong>Rp
                                    {{ number_format($fine->amount, 0, ',', '.') }}</strong> untuk peminjaman buku
                                <strong>{{ $fine->borrowing?->bookCopy?->book?->title ?? 'N/A' }}</strong> oleh
                                <strong>{{ $fine->borrowing?->siteUser?->name ?? 'N/A' }}</strong>?
                            </p>
                            <div class="mb-3">
                                <label for="waiver_notes-waive-{{ $fine->id }}" class="form-label">Alasan / Catatan
                                    Pembebasan (Opsional):</label>
                                <textarea class="form-control @error('waiver_notes', 'waive_' . $fine->id) is-invalid @enderror"
                                    id="waiver_notes-waive-{{ $fine->id }}" name="waiver_notes" rows="3">{{ old('waiver_notes') }}</textarea>
                                @error('waiver_notes', 'waive_' . $fine->id)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">Ya, Bebaskan Denda</button>
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
        @if ($errors->hasBag('pay_' . $fine->id))
            var payModalInstance = document.getElementById('payModal-{{ $fine->id }}');
            if (payModalInstance) {
                var modal = new bootstrap.Modal(payModalInstance);
                modal.show();
            }
        @endif
        @if ($errors->hasBag('waive_' . $fine->id))
            var waiveModalInstance = document.getElementById('waiveModal-{{ $fine->id }}');
            if (waiveModalInstance) {
                var modal = new bootstrap.Modal(waiveModalInstance);
                modal.show();
            }
        @endif
    </script>
@endsection
