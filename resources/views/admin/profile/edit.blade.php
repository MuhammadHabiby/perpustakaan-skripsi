@extends('admin.components.main')

@section('title', 'Edit Profil Saya')
@section('page-title', 'Edit Profil Saya')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Profil</h6>
                </div>
                <div class="card-body">
                    @include('admin.components.flash_messages')
                    @include('admin.components.validation_errors')

                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="nip" value="{{ $adminUser->nip }}" readonly
                                disabled>
                            <small class="form-text text-muted">NIP tidak dapat diubah.</small>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $adminUser->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $adminUser->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>
                        <h6 class="text-muted mb-3">Ubah Password (Opsional)</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password">
                                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save-fill me-1"></i> Simpan Perubahan Profil
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('script')
@endsection
