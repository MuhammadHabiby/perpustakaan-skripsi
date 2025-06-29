@extends('admin.components.main')

@section('title', 'Detail Buku')
@section('page-title')
    Detail Buku: {{ $book->title }}
@endsection

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Buku</h6>
            <div>
                <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-warning btn-sm" title="Edit Buku">
                    <i class="bi bi-pencil-fill me-1"></i> Edit Buku
                </a>
                <a href="{{ route('admin.books.index') }}" class="btn btn-secondary btn-sm" title="Kembali ke Daftar">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <dl class="row">
                        <dt class="col-sm-4 col-lg-3">Judul</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $book->title }}</dd>

                        <dt class="col-sm-4 col-lg-3">Pengarang</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $book->author?->name ?: '-' }}</dd>

                        <dt class="col-sm-4 col-lg-3">Penerbit</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $book->publisher?->name ?: '-' }}</dd>

                        <dt class="col-sm-4 col-lg-3">Kategori</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $book->category?->name ?: '-' }}</dd>

                        <dt class="col-sm-4 col-lg-3">ISBN</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $book->isbn ?: '-' }}</dd>

                        <dt class="col-sm-4 col-lg-3">Tahun Terbit</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $book->publication_year ?: '-' }}</dd>

                        <dt class="col-sm-4 col-lg-3">Lokasi Rak</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $book->location ?: '-' }}</dd>

                        <dt class="col-sm-4 col-lg-3">Tanggal Ditambahkan</dt>
                        <dd class="col-sm-8 col-lg-9">
                            {{ $book->created_at ? $book->created_at->isoFormat('D MMMM YYYY, HH:mm') : '-' }}</dd>

                        <dt class="col-sm-4 col-lg-3">Terakhir Diperbarui</dt>
                        <dd class="col-sm-8 col-lg-9">
                            {{ $book->updated_at ? $book->updated_at->isoFormat('D MMMM YYYY, HH:mm') : '-' }}</dd>

                        <dt class="col-sm-12">Sinopsis</dt>
                        <dd class="col-sm-12 mt-1">{!! nl2br(e($book->synopsis)) ?: '-' !!}</dd>
                    </dl>
                </div>
                <div class="col-md-4 text-center text-md-end">
                    <label class="form-label">Gambar Sampul</label>
                    <div>
                        <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('assets/images/no-image.png') }}"
                            alt="Sampul {{ $book->title }}" class="img-thumbnail mb-2"
                            style="max-height: 300px; max-width: 100%;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Eksemplar ({{ $book->copies->count() }} Total)</h6>
        </div>
        <div class="card-body">
            @if ($book->copies->isEmpty())
                <div class="alert alert-info text-center">
                    Belum ada data eksemplar untuk buku ini.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-striped" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No.</th>
                                <th>Kode Eksemplar</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Kondisi</th>
                                <th>Ditambahkan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($book->copies as $index => $copy)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $copy->copy_code }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $copy->status->badgeColor() }}">
                                            {{ $copy->status->label() }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $copy->condition->badgeColor() }}">
                                            {{ $copy->condition->label() }}
                                        </span>
                                    </td>
                                    <td>{{ $copy->created_at ? $copy->created_at->isoFormat('D MMM YYYY, HH:mm') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.books.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Buku
            </a>
        </div>
    </div>

@endsection

@section('css')
    <style>
        dl.row dt {
            margin-bottom: 0.5rem;
        }
    </style>
@endsection
