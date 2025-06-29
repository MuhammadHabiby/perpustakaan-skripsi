@extends('admin.components.main')

@section('title', 'Detail Pengarang')
@section('page-title', 'Detail Pengarang: ' . $author->name)

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Detail Pengarang</h6>
            <a href="{{ route('admin.authors.edit', $author) }}" class="btn btn-warning btn-sm" title="Edit Pengarang">
                <i class="bi bi-pencil-fill"></i> Edit
            </a>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID Pengarang</dt>
                <dd class="col-sm-9">{{ $author->id }}</dd>

                <dt class="col-sm-3">Nama Pengarang</dt>
                <dd class="col-sm-9">{{ $author->name }}</dd>

                <dt class="col-sm-3">Bio</dt>
                <dd class="col-sm-9">
                    {!! nl2br(e($author->bio)) ?: '-' !!}
                </dd>

                <dt class="col-sm-3">Tanggal Dibuat</dt>
                <dd class="col-sm-9">
                    {{ $author->created_at ? $author->created_at->isoFormat('D MMMM YYYY, HH:mm:ss') : '-' }}
                </dd>

                <dt class="col-sm-3">Tanggal Diperbarui</dt>
                <dd class="col-sm-9">
                    {{ $author->updated_at ? $author->updated_at->isoFormat('D MMMM YYYY, HH:mm:ss') : '-' }}
                </dd>
            </dl>

            <hr>

            <div class="d-flex justify-content-start">
                <a href="{{ route('admin.authors.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
@endsection
