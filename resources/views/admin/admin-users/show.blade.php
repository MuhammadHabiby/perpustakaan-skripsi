@extends('admin.components.main')

@section('title', 'Detail Penerbit')
@section('page-title', 'Detail Penerbit: ' . $publisher->name)

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Detail Penerbit</h6>
            <a href="{{ route('admin.publishers.edit', $publisher) }}" class="btn btn-warning btn-sm" title="Edit Penerbit">
                <i class="bi bi-pencil-fill"></i> Edit
            </a>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID Penerbit</dt>
                <dd class="col-sm-9">{{ $publisher->id }}</dd>

                <dt class="col-sm-3">Nama Penerbit</dt>
                <dd class="col-sm-9">{{ $publisher->name }}</dd>

                <dt class="col-sm-3">Alamat</dt>
                <dd class="col-sm-9">
                    {!! nl2br(e($publisher->address)) ?: '-' !!}
                </dd>

                <dt class="col-sm-3">Tanggal Dibuat</dt>
                <dd class="col-sm-9">
                    {{ $publisher->created_at ? $publisher->created_at->isoFormat('D MMMM YYYY, HH:mm:ss') : '-' }}
                </dd>

                <dt class="col-sm-3">Tanggal Diperbarui</dt>
                <dd class="col-sm-9">
                    {{ $publisher->updated_at ? $publisher->updated_at->isoFormat('D MMMM YYYY, HH:mm:ss') : '-' }}
                </dd>
            </dl>

            <hr>

            <div class="d-flex justify-content-start">
                <a href="{{ route('admin.publishers.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
@endsection
