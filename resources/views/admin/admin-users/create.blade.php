@extends('admin.components.main')

@section('title', 'Tambah Admin Baru')
@section('page-title', 'Tambah Admin Baru')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Tambah Admin</h6>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')
            @include('admin.components.validation_errors')

            <form action="{{ route('admin.admin-users.store') }}" method="POST">
                @include('admin.admin-users._form')
            </form>
        </div>
    </div>
@endsection
