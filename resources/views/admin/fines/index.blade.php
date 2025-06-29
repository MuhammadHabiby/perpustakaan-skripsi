@extends('admin.components.main')

@section('title', 'Manajemen Denda')
@section('page-title', 'Manajemen Denda')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Denda</h6>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')

            @if ($fines->isEmpty())
                <div class="alert alert-info text-center">
                    Tidak ada data denda.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped datatable" id="dataTableFines" width="100%"
                        cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center no-sort" width="1%">No</th>
                                <th>Peminjam</th>
                                <th>Judul Buku</th>
                                <th>Jumlah Denda (Rp)</th>
                                <th class="text-center">Status</th>
                                <th>Tgl Bayar/Bebas</th>
                                <th>Admin Proses</th>
                                <th>Catatan</th> {{-- Kolom Baru --}}
                                <th class="text-center action-column no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fines as $fine)
                                <tr class="align-middle">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $fine->borrowing?->siteUser?->name ?? 'N/A' }}<br>
                                        <small class="text-muted">NIS:
                                            {{ $fine->borrowing?->siteUser?->nis ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        {{ $fine->borrowing?->bookCopy?->book?->title ?? 'N/A' }}<br>
                                        <small class="text-muted">Kode:
                                            {{ $fine->borrowing?->bookCopy?->copy_code ?? 'N/A' }}</small>
                                    </td>
                                    <td class="text-end">{{ number_format($fine->amount, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if ($fine->status)
                                            <span
                                                class="badge bg-{{ $fine->status->badgeColor() }}">{{ $fine->status->label() }}</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $fine->payment_date ? $fine->payment_date->isoFormat('D MMM H:mm') : '-' }}
                                    </td>
                                    <td>{{ $fine->paymentProcessor?->name ?? '-' }}</td>
                                    <td>
                                        <span title="{{ $fine->notes }}">{{ Str::limit($fine->notes, 50, '...') }}</span>
                                    </td>
                                    <td class="action-column text-center">
                                        @if ($fine->status === App\Enum\FineStatus::Unpaid)
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.fines.show', $fine) }}" class="btn btn-info"
                                                    title="Detail Denda">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                                <button type="button" class="btn btn-success" title="Tandai Lunas"
                                                    data-bs-toggle="modal" data-bs-target="#payModal-{{ $fine->id }}">

                                                    <i class="bi bi-cash-coin"></i> Bayar
                                                </button>
                                            </div>
                                        @elseif($fine->status === App\Enum\FineStatus::Paid || $fine->status === App\Enum\FineStatus::Waived)
                                            <a href="{{ route('admin.fines.show', $fine) }}"
                                                class="btn btn-sm btn-info" title="Lihat Detail"><i
                                                    class="bi bi-eye-fill"></i></a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @foreach ($fines as $fine)
                    @if ($fine->status === App\Enum\FineStatus::Unpaid)
                        <div class="modal fade" id="payModal-{{ $fine->id }}" tabindex="-1"
                            aria-labelledby="payModalLabel-{{ $fine->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.fines.pay', $fine) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="payModalLabel-{{ $fine->id }}">Konfirmasi
                                                Pembayaran Denda</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Anda akan menandai lunas denda sebesar <strong>Rp
                                                    {{ number_format($fine->amount, 0, ',', '.') }}</strong> untuk:</p>
                                            <ul>
                                                <li>Peminjam:
                                                    <strong>{{ $fine->borrowing?->siteUser?->name ?? 'N/A' }}</strong>
                                                </li>
                                                <li>Buku:
                                                    <strong>{{ $fine->borrowing?->bookCopy?->book?->title ?? 'N/A' }}</strong>
                                                </li>
                                            </ul>
                                            <div class="mb-3">
                                                <label for="payment_notes-{{ $fine->id }}" class="form-label">Catatan
                                                    Pembayaran (Opsional):</label>
                                                <textarea class="form-control @error('payment_notes') is-invalid @enderror" id="payment_notes-{{ $fine->id }}"
                                                    name="payment_notes" rows="3">{{ old('payment_notes') }}</textarea>
                                                @error('payment_notes')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-check-circle-fill me-1"></i> Ya, Tandai Lunas
                                            </button>
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
                                            <h1 class="modal-title fs-5" id="waiveModalLabel-{{ $fine->id }}">
                                                Konfirmasi Bebaskan Denda</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin membebaskan denda sebesar <strong>Rp
                                                    {{ number_format($fine->amount, 0, ',', '.') }}</strong> untuk
                                                peminjaman buku
                                                <strong>{{ $fine->borrowing?->bookCopy?->book?->title ?? 'N/A' }}</strong>
                                                oleh <strong>{{ $fine->borrowing?->siteUser?->name ?? 'N/A' }}</strong>?
                                            </p>
                                            <div class="mb-3">
                                                <label for="waiver_notes-{{ $fine->id }}" class="form-label">Alasan /
                                                    Catatan Pembebasan (Opsional):</label>
                                                <textarea class="form-control @error('waiver_notes') is-invalid @enderror" id="waiver_notes-{{ $fine->id }}"
                                                    name="waiver_notes" rows="3">{{ old('waiver_notes') }}</textarea>
                                                @error('waiver_notes')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning">Ya, Bebaskan Denda</button>
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
    @include('admin.components.datatable_script', ['table_id' => 'dataTableFines'])
    <script>
        @foreach ($fines as $fine)
            @if ($errors->hasBag('pay_' . $fine->id))
                var payModal = new bootstrap.Modal(document.getElementById('payModal-{{ $fine->id }}'));
                if (payModal) {
                    payModal.show();
                }
            @endif
            @if ($errors->hasBag('waive_' . $fine->id))
                var waiveModal = new bootstrap.Modal(document.getElementById('waiveModal-{{ $fine->id }}'));
                if (waiveModal) {
                    waiveModal.show();
                }
            @endif
        @endforeach
    </script>
@endsection
