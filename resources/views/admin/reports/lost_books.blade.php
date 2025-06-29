@extends('admin.components.main')

@section('title', 'Laporan Buku Hilang')
@section('page-title', 'Laporan Buku Hilang')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan Buku Hilang</h6>
            @if (!$errors->has('start_date') && !$errors->has('end_date') && $startDate && $endDate)
                <form action="{{ route('admin.reports.lost-books.export') }}" method="GET" class="d-inline-block">
                    <input type="hidden" name="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" value="{{ $endDate }}">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </button>
                </form>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.lost-books') }}" method="GET" class="row g-3 align-items-end mb-3">
                <div class="col-md-5">
                    <label for="start_date" class="form-label">Tanggal Mulai Penyelesaian</label>
                    <input type="date" class="form-control form-control-sm @error('start_date') is-invalid @enderror"
                        id="start_date" name="start_date" value="{{ $startDate ?? '' }}" required>
                    @error('start_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-5">
                    <label for="end_date" class="form-label">Tanggal Selesai Penyelesaian</label>
                    <input type="date" class="form-control form-control-sm @error('end_date') is-invalid @enderror"
                        id="end_date" name="end_date" value="{{ $endDate ?? '' }}" required>
                    @error('end_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-filter"></i> Tampilkan
                    </button>
                    <a href="{{ route('admin.reports.lost-books') }}" class="btn btn-secondary btn-sm w-100 mt-1"
                        title="Reset Filter ke Hari Ini">
                        <i class="bi bi-arrow-clockwise"></i> Hari Ini
                    </a>
                </div>
            </form>
            @include('admin.components.flash_messages')
            @if ($errors->any() && !$errors->has('start_date') && !$errors->has('end_date'))
                @include('admin.components.validation_errors')
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Hasil Laporan Buku Hilang (Status Resolved)
                @if (!$errors->has('start_date') && !$errors->has('end_date') && $startDate && $endDate)
                    ({{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM YY') }} -
                    {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM YY') }})
                    ({{ $lostReports->count() }} Laporan)
                @elseif($errors->has('start_date') || $errors->has('end_date'))
                    <span class="text-danger">(Rentang Tanggal Tidak Valid)</span>
                @endif
            </h6>
        </div>
        <div class="card-body">
            @if (!$errors->has('start_date') && !$errors->has('end_date'))
                @if ($lostReports->isEmpty())
                    <div class="alert alert-info text-center">
                        Tidak ada data laporan kehilangan yang diselesaikan untuk rentang tanggal yang dipilih.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped datatable"
                            id="dataTableReportLostBooks" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Tgl Selesai</th>
                                    <th>Kode Eksemplar</th>
                                    <th>Judul Buku</th>
                                    <th>Pelapor</th>
                                    <th>Admin Proses</th>
                                    <th>Catatan Penyelesaian</th>
                                    <th class="text-center no-sort">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lostReports as $index => $report)
                                    <tr class="align-middle">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $report->resolution_date ? $report->resolution_date->isoFormat('D MMM YY, HH:mm') : '-' }}
                                        </td>
                                        <td>{{ $report->bookCopy?->copy_code ?? 'N/A' }}</td>
                                        <td>{{ $report->bookCopy?->book?->title ?? 'N/A' }}</td>
                                        <td>
                                            {{ $report->reporter?->name ?? 'N/A' }}
                                            <small class="text-muted">({{ $report->reporter?->nis ?? 'N/A' }})</small>
                                        </td>
                                        <td>{{ $report->verifier?->name ?? '-' }}</td>
                                        <td>
                                            <span
                                                title="{{ $report->resolution_notes }}">{{ Str::limit($report->resolution_notes, 70, '...') }}</span>
                                        </td>
                                        <td class="text-center action-column">
                                            <a href="{{ route('admin.lost-reports.show', $report) }}"
                                                class="btn btn-sm btn-info" title="Lihat Detail Laporan">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @else
                <div class="alert alert-warning text-center">
                    Silakan perbaiki input tanggal pada filter di atas.
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
    @if (isset($lostReports) && $lostReports->count() > 0 && !$errors->has('start_date') && !$errors->has('end_date'))
        @include('admin.components.datatable_script', ['table_id' => 'dataTableReportLostBooks'])
    @endif
@endsection
