@extends('admin.components.main')

@section('title', 'Tambah Kategori Baru')
@section('page-title', 'Tambah Kategori Baru')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Tambah Kategori</h6>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')
            @include('admin.components.validation_errors')

            <form action="{{ route('admin.categories.store') }}" method="POST">
                @include('admin.categories._form')
            </form>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('script')
@endsection
