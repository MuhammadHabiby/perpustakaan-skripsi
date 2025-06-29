@extends('admin.components.main')

@section('title', 'Laporan Pengadaan')
@section('page-title', 'Laporan Pengadaan Buku')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan Pengadaan</h6>
            @if (!$errors->has('start_date') && !$errors->has('end_date') && $startDate && $endDate)
                <form action="{{ route('admin.reports.procurements.export') }}" method="GET" class="d-inline-block">
                    <input type="hidden" name="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" value="{{ $endDate }}">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </button>
                </form>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.procurements') }}" method="GET" class="row g-3 align-items-end mb-3">
                <div class="col-md-5">
                    <label for="start_date" class="form-label">Tanggal Mulai Pengadaan</label>
                    <input type="date" class="form-control form-control-sm @error('start_date') is-invalid @enderror"
                        id="start_date" name="start_date" value="{{ $startDate ?? '' }}" required>
                    @error('start_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-5">
                    <label for="end_date" class="form-label">Tanggal Selesai Pengadaan</label>
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
                    <a href="{{ route('admin.reports.procurements') }}" class="btn btn-secondary btn-sm w-100 mt-1"
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
                Hasil Laporan Pengadaan Eksemplar
                @if (!$errors->has('start_date') && !$errors->has('end_date') && $startDate && $endDate)
                    ({{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM YY') }} -
                    {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM YY') }})
                    ({{ $bookCopies->count() }} Eksemplar Baru)
                @elseif($errors->has('start_date') || $errors->has('end_date'))
                    <span class="text-danger">(Rentang Tanggal Tidak Valid)</span>
                @endif
            </h6>
        </div>
        <div class="card-body">
            @if (!$errors->has('start_date') && !$errors->has('end_date'))
                @if ($bookCopies->isEmpty())
                    <div class="alert alert-info text-center">
                        Tidak ada data pengadaan eksemplar ditemukan untuk rentang tanggal yang dipilih
                        ({{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM YY') }} -
                        {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM YY') }}).
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped datatable"
                            id="dataTableReportProcurements" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Tgl Pengadaan</th>
                                    <th>Kode Eksemplar</th>
                                    <th>Judul Buku</th>
                                    <th>Pengarang</th>
                                    <th>Penerbit</th>
                                    <th>ISBN</th>
                                    <th>Lokasi</th>
                                    <th class="text-center">Kondisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookCopies as $index => $copy)
                                    <tr class="align-middle">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $copy->created_at ? $copy->created_at->isoFormat('D MMM YY, HH:mm') : '-' }}
                                        </td>
                                        <td>{{ $copy->copy_code }}</td>
                                        <td>{{ $copy->book?->title ?? 'N/A' }}</td>
                                        <td>{{ $copy->book?->author?->name ?? 'N/A' }}</td>
                                        <td>{{ $copy->book?->publisher?->name ?? 'N/A' }}</td>
                                        <td>{{ $copy->book?->isbn ?? '-' }}</td>
                                        <td>{{ $copy->book?->location ?? '-' }}</td>
                                        <td class="text-center">
                                            @if ($copy->condition)
                                                <span
                                                    class="badge bg-{{ $copy->condition->badgeColor() }}">{{ $copy->condition->label() }}</span>
                                            @else
                                                -
                                            @endif
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
@endsection

@section('script')
    @if (isset($bookCopies) && $bookCopies->count() > 0 && !$errors->has('start_date') && !$errors->has('end_date'))
        @include('admin.components.datatable_script', ['table_id' => 'dataTableReportProcurements'])
    @endif
@endsection
