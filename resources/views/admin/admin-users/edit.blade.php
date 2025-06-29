@extends('admin.components.main')

@section('title', 'Edit Admin')
@section('page-title', 'Edit Admin: ' . $adminUser->name)

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Admin</h6>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')
            @include('admin.components.validation_errors')

            <form action="{{ route('admin.admin-users.update', $adminUser) }}" method="POST">
                @method('PUT')
                @include('admin.admin-users._form', ['adminUser' => $adminUser])
            </form>
        </div>
    </div>
@endsection
