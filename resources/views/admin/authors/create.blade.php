@extends('admin.components.main')

@section('title', 'Tambah Pengarang Baru')
@section('page-title', 'Tambah Pengarang Baru')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Tambah Pengarang</h6>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')
            @include('admin.components.validation_errors')

            <form action="{{ route('admin.authors.store') }}" method="POST">
                @include('admin.authors._form')
            </form>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('script')
@endsection
