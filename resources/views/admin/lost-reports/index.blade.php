@extends('admin.components.main')

@section('title', 'Laporan Kehilangan')
@section('page-title', 'Laporan Kehilangan Buku')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Laporan Kehilangan</h6>
            <form action="{{ route('admin.lost-reports.index') }}" method="GET" class="float-end d-inline-block ms-3"
                style="max-width: 200px;">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()"
                    aria-label="Filter Status Laporan">
                    <option value="">-- Semua Status --</option>
                    @foreach ($validStatuses as $status)
                        <option value="{{ $status->value }}" {{ $statusFilter == $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')
            @include('admin.components.validation_errors')

            @if ($lostReports->isEmpty())
                <div class="alert alert-info text-center">
                    Tidak ada data laporan kehilangan
                    @if ($statusFilter)
                        untuk status "{{ App\Enum\LostReportStatus::tryFrom($statusFilter)?->label() }}"
                    @endif.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped datatable" id="dataTableLostReports"
                        width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center no-sort" width="1%">ID</th>
                                <th>Pelapor (Siswa)</th>
                                <th>Buku / Eksemplar</th>
                                <th>Tgl Lapor</th>
                                <th class="text-center">Status</th>
                                <th>Admin Verifikasi</th>
                                <th class="text-center action-column no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lostReports as $report)
                                <tr class="align-middle">
                                    <td class="text-center">{{ $report->id }}</td>
                                    <td>
                                        {{ $report->reporter?->name ?? 'N/A' }}<br>
                                        <small class="text-muted">NIS: {{ $report->reporter?->nis ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        {{ $report->bookCopy?->book?->title ?? 'N/A' }}<br>
                                        <small class="text-muted">Kode: {{ $report->bookCopy?->copy_code ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $report->report_date ? $report->report_date->isoFormat('D MMM YYYY, HH:mm') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($report->status)
                                            <span
                                                class="badge bg-{{ $report->status->badgeColor() }}">{{ $report->status->label() }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $report->verifier?->name ?? '-' }}</td>
                                    <td class="action-column text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.lost-reports.show', $report) }}" class="btn btn-info"
                                                title="Detail Laporan">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            {{-- Tombol Verifikasi hanya jika status Reported --}}
                                            @if ($report->status === App\Enum\LostReportStatus::Reported)
                                                <form action="{{ route('admin.lost-reports.verify', $report) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Verifikasi laporan ini?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-primary"
                                                        title="Verifikasi Laporan">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if (in_array($report->status, [App\Enum\LostReportStatus::Reported, App\Enum\LostReportStatus::Verified]))
                                                <button type="button" class="btn btn-success" title="Selesaikan Laporan"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#resolveModal-{{ $report->id }}">
                                                    <i class="bi bi-check2-all"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @foreach ($lostReports as $report)
                    @if (in_array($report->status, [App\Enum\LostReportStatus::Reported, App\Enum\LostReportStatus::Verified]))
                        <div class="modal fade" id="resolveModal-{{ $report->id }}" tabindex="-1"
                            aria-labelledby="resolveModalLabel-{{ $report->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.lost-reports.resolve', $report) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="resolveModalLabel-{{ $report->id }}">
                                                Selesaikan Laporan Kehilangan</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Anda akan menyelesaikan laporan kehilangan untuk:</p>
                                            <ul>
                                                <li>Buku: <strong>{{ $report->bookCopy?->book?->title ?? 'N/A' }}</strong>
                                                    (Kode: {{ $report->bookCopy?->copy_code ?? 'N/A' }})
                                                </li>
                                                <li>Pelapor: <strong>{{ $report->reporter?->name ?? 'N/A' }}</strong></li>
                                            </ul>
                                            <p>Status buku akan diubah menjadi 'Hilang'. Jika terhubung dengan peminjaman
                                                dan ada biaya penggantian di pengaturan sistem, denda akan dibuat/diperbaharui.</p>
                                            <div class="mb-3">
                                                <label for="resolution_notes-{{ $report->id }}"
                                                    class="form-label">Catatan Penyelesaian <span
                                                        class="text-danger">*</span></label>
                                                <textarea class="form-control @error('resolution_notes') is-invalid @enderror"
                                                    id="resolution_notes-{{ $report->id }}" name="resolution_notes" rows="3" required>{{ old('resolution_notes') }}</textarea>
                                                @error('resolution_notes')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-check2-all me-1"></i> Ya, Selesaikan Laporan
                                            </button>
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
    @include('admin.components.datatable_script', ['table_id' => 'dataTableLostReports'])
@endsection
