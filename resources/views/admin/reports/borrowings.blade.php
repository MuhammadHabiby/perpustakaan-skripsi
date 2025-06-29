@extends('admin.components.main')

@section('title', 'Laporan Peminjaman')
@section('page-title', 'Laporan Peminjaman Buku')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan Peminjaman</h6>
            @if (!$errors->has('start_date') && !$errors->has('end_date') && $startDate && $endDate)
                <form action="{{ route('admin.reports.borrowings.export') }}" method="GET" class="d-inline-block">
                    <input type="hidden" name="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" value="{{ $endDate }}">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </button>
                </form>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.borrowings') }}" method="GET" class="row g-3 align-items-end mb-3">
                <div class="col-md-5">
                    <label for="start_date" class="form-label">Tanggal Mulai Pinjam</label>
                    <input type="date" class="form-control form-control-sm @error('start_date') is-invalid @enderror"
                        id="start_date" name="start_date" value="{{ $startDate ?? '' }}" required>
                    @error('start_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-5">
                    <label for="end_date" class="form-label">Tanggal Selesai Pinjam</label>
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
                    <a href="{{ route('admin.reports.borrowings') }}" class="btn btn-secondary btn-sm w-100 mt-1"
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
                Hasil Laporan Peminjaman
                @if (!$errors->has('start_date') && !$errors->has('end_date') && $startDate && $endDate)
                    ({{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM YY') }} -
                    {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM YY') }})
                    ({{ $borrowings->count() }} Data)
                @elseif($errors->has('start_date') || $errors->has('end_date'))
                    <span class="text-danger">(Rentang Tanggal Tidak Valid)</span>
                @endif
            </h6>
        </div>
        <div class="card-body">
            @if (!$errors->has('start_date') && !$errors->has('end_date'))
                @if ($borrowings->isEmpty())
                    <div class="alert alert-info text-center">
                        Tidak ada data peminjaman ditemukan untuk rentang tanggal yang dipilih
                        ({{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM YY') }} -
                        {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM YY') }}).
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped datatable"
                            id="dataTableReportBorrowings" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Peminjam</th>
                                    <th>Judul Buku</th>
                                    <th>Kode Eksemplar</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Tgl Kembali</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($borrowings as $index => $borrowing)
                                    <tr class="align-middle">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            {{ $borrowing->siteUser?->name ?? 'N/A' }}
                                            ({{ $borrowing->siteUser?->nis ?? 'N/A' }})
                                        </td>
                                        <td>{{ $borrowing->bookCopy?->book?->title ?? 'N/A' }}</td>
                                        <td>{{ $borrowing->bookCopy?->copy_code ?? 'N/A' }}</td>
                                        <td>{{ $borrowing->borrow_date ? $borrowing->borrow_date->format('d/m/Y') : '-' }}
                                        </td>
                                        <td>{{ $borrowing->due_date ? $borrowing->due_date->format('d/m/Y') : '-' }}</td>
                                        <td>{{ $borrowing->return_date ? $borrowing->return_date->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            @if ($borrowing->status)
                                                <span
                                                    class="badge bg-{{ $borrowing->status->badgeColor() }}">{{ $borrowing->status->label() }}</span>
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
    @if (isset($borrowings) && $borrowings->count() > 0 && !$errors->has('start_date') && !$errors->has('end_date'))
        @include('admin.components.datatable_script', ['table_id' => 'dataTableReportBorrowings'])
    @endif
@endsection
