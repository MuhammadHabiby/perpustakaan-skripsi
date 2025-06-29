@extends('admin.components.main')

@section('title', 'Tambah Penerbit Baru')
@section('page-title', 'Tambah Penerbit Baru')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Tambah Penerbit</h6>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')
            @include('admin.components.validation_errors')

            <form action="{{ route('admin.publishers.store') }}" method="POST">
                @include('admin.publishers._form')
            </form>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('script')
@endsection
