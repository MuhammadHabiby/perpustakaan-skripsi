@extends('admin.components.main')

@section('title', 'Tambah Siswa Baru')
@section('page-title', 'Tambah Siswa Baru')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Tambah Siswa</h6>
        </div>
        <div class="card-body">
            {{-- Include flash message partial --}}
            @include('admin.components.flash_messages')
            {{-- Include general validation errors --}}
            @include('admin.components.validation_errors')

            <form action="{{ route('admin.site-users.store') }}" method="POST">
                @include('admin.site-users._form')
            </form>
        </div>
    </div>
@endsection
