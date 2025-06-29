@extends('admin.components.main')

@section('title', 'Detail Kategori')
@section('page-title', 'Detail Kategori: ' . $category->name)

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Detail Kategori</h6>
            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning btn-sm" title="Edit Kategori">
                <i class="bi bi-pencil-fill"></i> Edit
            </a>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID Kategori</dt>
                <dd class="col-sm-9">{{ $category->id }}</dd>

                <dt class="col-sm-3">Nama Kategori</dt>
                <dd class="col-sm-9">{{ $category->name }}</dd>

                <dt class="col-sm-3">Slug</dt>
                <dd class="col-sm-9">{{ $category->slug }}</dd>

                <dt class="col-sm-3">Deskripsi</dt>
                <dd class="col-sm-9">
                    {!! nl2br(e($category->description)) ?: '-' !!}
                </dd>

                <dt class="col-sm-3">Tanggal Dibuat</dt>
                <dd class="col-sm-9">
                    {{ $category->created_at ? $category->created_at->isoFormat('D MMMM YYYY, HH:mm:ss') : '-' }}
                </dd>

                <dt class="col-sm-3">Tanggal Diperbarui</dt>
                <dd class="col-sm-9">
                    {{ $category->updated_at ? $category->updated_at->isoFormat('D MMMM YYYY, HH:mm:ss') : '-' }}
                </dd>
            </dl>

            <hr>

            <div class="d-flex justify-content-start">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
@endsection
