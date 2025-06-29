@extends('admin.components.main')

@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori: ' . $category->name)

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Kategori</h6>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')
            @include('admin.components.validation_errors')

            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                @method('PUT')
                @include('admin.categories._form', ['category' => $category])
            </form>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('script')
@endsection
