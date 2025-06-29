@extends('admin.components.main')

@section('title', 'Edit Siswa')
@section('page-title', 'Edit Siswa: ' . $siteUser->name)

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Siswa</h6>
        </div>
        <div class="card-body">
            {{-- Include flash message partial --}}
            @include('admin.components.flash_messages')
            {{-- Include general validation errors --}}
            @include('admin.components.validation_errors')

            <form action="{{ route('admin.site-users.update', $siteUser) }}" method="POST">
                @method('PUT')
                @include('admin.site-users._form', ['siteUser' => $siteUser])
            </form>
        </div>
    </div>
@endsection
