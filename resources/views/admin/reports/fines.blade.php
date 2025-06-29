@extends('admin.components.main')

@section('title', 'Laporan Denda')
@section('page-title', 'Laporan Denda')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan Denda</h6>
            @if (!$errors->any() && $startDate && $endDate)
                <form action="{{ route('admin.reports.fines.export') }}" method="GET" class="d-inline-block">
                    <input type="hidden" name="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" value="{{ $endDate }}">
                    <input type="hidden" name="status" value="{{ $statusFilter ?? '' }}">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </button>
                </form>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.fines') }}" method="GET" class="row g-3 align-items-center mb-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Tanggal Mulai Rentang</label>
                    <input type="date" class="form-control form-control-sm @error('start_date') is-invalid @enderror"
                        id="start_date" name="start_date" value="{{ $startDate ?? '' }}" required>
                    @error('start_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Tanggal Selesai Rentang</label>
                    <input type="date" class="form-control form-control-sm @error('end_date') is-invalid @enderror"
                        id="end_date" name="end_date" value="{{ $endDate ?? '' }}" required>
                    @error('end_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status Denda</label>
                    <select name="status" class="form-select form-select-sm @error('status') is-invalid @enderror"
                        id="status">
                        @foreach ($filterOptions as $value => $label)
                            <option value="{{ $value }}" {{ ($statusFilter ?? '') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-filter"></i> Tampilkan
                    </button>
                    <a href="{{ route('admin.reports.fines') }}" class="btn btn-secondary btn-sm w-100 mt-1"
                        title="Reset Filter ke Hari Ini (Semua Status)">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
                <div class="col-12 mt-2">
                    <small class="text-muted">
                        *Rentang tanggal berlaku untuk **Tanggal Dibuat** jika Status = 'Semua' atau 'Belum Dibayar'.<br>
                        *Rentang tanggal berlaku untuk **Tanggal Proses** jika Status = 'Lunas/Dibebaskan', 'Lunas', atau
                        'Dibebaskan'.
                    </small>
                </div>
            </form>
            @include('admin.components.flash_messages')
            @if ($errors->any() && !$errors->has('start_date') && !$errors->has('end_date') && !$errors->has('status'))
                @include('admin.components.validation_errors')
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Hasil Laporan Denda
                @if (!$errors->any() && $startDate && $endDate)
                    @php
                        $dateLabel = $statusFilter === '' || $statusFilter === 'Unpaid' ? 'Dibuat' : 'Diproses';
                    @endphp
                    ({{ $dateLabel }}: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM YY') }} -
                    {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM YY') }})
                    @php
                        $statusLabel = $filterOptions[$statusFilter ?? ''] ?? 'Error';
                    @endphp
                    - Status: {{ $statusLabel }}
                    ({{ $fines->count() }} Data)
                @elseif($errors->any())
                    <span class="text-danger">(Filter Tidak Valid)</span>
                @endif
            </h6>
        </div>
        <div class="card-body">
            @if (!$errors->any())
                @if ($fines->isEmpty())
                    <div class="alert alert-info text-center">
                        Tidak ada data denda ditemukan untuk filter yang dipilih.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped datatable" id="dataTableReportFines"
                            width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Tgl Dibuat</th>
                                    <th>Peminjam</th>
                                    <th>Judul Buku</th>
                                    <th>Jumlah (Rp)</th>
                                    <th class="text-center">Status</th>
                                    <th>Tgl Proses</th>
                                    <th>Admin Proses</th>
                                    <th>Catatan</th>
                                    <th class="text-center no-sort">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fines as $index => $fine)
                                    <tr class="align-middle">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $fine->created_at ? $fine->created_at->isoFormat('D MMM YY, HH:mm') : '-' }}
                                        </td>
                                        <td>
                                            {{ $fine->borrowing?->siteUser?->name ?? 'N/A' }}
                                            ({{ $fine->borrowing?->siteUser?->nis ?? 'N/A' }})
                                        </td>
                                        <td>
                                            {{ $fine->borrowing?->bookCopy?->book?->title ?? 'N/A' }}
                                            ({{ $fine->borrowing?->bookCopy?->copy_code ?? 'N/A' }})
                                        </td>
                                        <td class="text-end">{{ number_format($fine->amount, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if ($fine->status)
                                                <span
                                                    class="badge bg-{{ $fine->status->badgeColor() }}">{{ $fine->status->label() }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $fine->payment_date ? $fine->payment_date->isoFormat('D MMM YY, HH:mm') : '-' }}
                                        </td>
                                        <td>{{ $fine->paymentProcessor?->name ?? '-' }}</td>
                                        <td>
                                            <span
                                                title="{{ $fine->notes }}">{{ Str::limit($fine->notes, 50, '...') }}</span>
                                        </td>
                                        <td class="text-center action-column">
                                            <a href="{{ route('admin.fines.show', $fine) }}"
                                                class="btn btn-sm btn-info" title="Lihat Detail Denda"><i
                                                    class="bi bi-eye-fill"></i></a>
                                            <a href="{{ route('admin.borrowings.show', $fine->borrowing_id) }}"
                                                class="btn btn-sm btn-secondary" title="Lihat Detail Peminjaman"><i
                                                    class="bi bi-arrow-up-right-square"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @else
                <div class="alert alert-warning text-center">
                    Silakan perbaiki input filter di atas.
                </div>
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
    </style>
@endsection

@section('script')
    @if (isset($fines) && $fines->count() > 0 && !$errors->any())
        @include('admin.components.datatable_script', ['table_id' => 'dataTableReportFines'])
    @endif
@endsection
