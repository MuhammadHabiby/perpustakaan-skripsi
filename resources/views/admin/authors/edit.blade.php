@extends('admin.components.main')

@section('title', 'Edit Pengarang')
@section('page-title', 'Edit Pengarang: ' . $author->name)

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Pengarang</h6>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')
            @include('admin.components.validation_errors')

            <form action="{{ route('admin.authors.update', $author) }}" method="POST">
                @method('PUT')
                @include('admin.authors._form', ['author' => $author])
            </form>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('script')
@endsection
