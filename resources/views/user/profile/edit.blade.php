@extends('user.components.main')

@section('title', 'Profil Saya')
@section('page-title', 'Pengaturan Profil Saya')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Informasi Profil</h6>
                </div>
                <div class="card-body">
                    @include('admin.components.flash_messages')
                    @include('admin.components.validation_errors')

                    <form action="{{ route('user.profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">NIS</label>
                                <input type="text" class="form-control" value="{{ $user->nis }}" readonly disabled>
                                <small class="form-text text-muted">NIS tidak dapat diubah.</small>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kelas</label>
                                <input type="text" class="form-control" value="{{ $user->class ?? '-' }}" readonly
                                    disabled>
                                <small class="form-text text-muted">Kelas/Jurusan diubah oleh admin.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jurusan</label>
                                <input type="text" class="form-control" value="{{ $user->major ?? '-' }}" readonly
                                    disabled>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="text-primary fw-bold mb-3">Informasi Kontak</h6>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Nomor Telepon (WhatsApp)</label>
                            <input type="tel" class="form-control @error('phone_number') is-invalid @enderror"
                                id="phone_number" name="phone_number"
                                value="{{ old('phone_number', $user->phone_number) }}">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h6 class="text-primary fw-bold mb-3">Ubah Password (Opsional)</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" autocomplete="new-password">
                                <small class="form-text text-muted">Min. 8 karakter. Kosongkan jika tidak ingin
                                    mengubah.</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" autocomplete="new-password">
                            </div>
                        </div>


                        <div class="d-flex justify-content-end mt-4">
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
