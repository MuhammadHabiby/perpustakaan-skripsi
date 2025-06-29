@extends('user.components.main')

@section('title', 'Registrasi Siswa')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mt-5 mb-5">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" width="72" height="72"
                                class="mb-3">
                            <h3 class="card-title mb-1 fw-bold">Registrasi Akun Siswa</h3>
                            <p class="text-muted">Sistem Informasi Perpustakaan</p>
                        </div>

                        @include('admin.components.flash_messages')
                        @include('admin.components.validation_errors')

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="nis" class="form-label">{{ __('NIS') }} <span
                                        class="text-danger">*</span></label>
                                <input id="nis" type="text" class="form-control @error('nis') is-invalid @enderror"
                                    name="nis" value="{{ old('nis') }}" required autocomplete="nis" autofocus>
                                @error('nis')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('Nama Lengkap') }} <span
                                        class="text-danger">*</span></label>
                                <input id="name" type="text"
                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                    value="{{ old('name') }}" required autocomplete="name">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Alamat Email') }} <span
                                        class="text-danger">*</span></label>
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="class" class="form-label">{{ __('Kelas') }}</label>
                                    <input id="class" type="text"
                                        class="form-control @error('class') is-invalid @enderror" name="class"
                                        value="{{ old('class') }}">
                                    @error('class')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="major" class="form-label">{{ __('Jurusan') }}</label>
                                    <input id="major" type="text"
                                        class="form-control @error('major') is-invalid @enderror" name="major"
                                        value="{{ old('major') }}">
                                    @error('major')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">{{ __('Password') }} <span
                                            class="text-danger">*</span></label>
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="new-password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password-confirm" class="form-label">{{ __('Konfirmasi Password') }} <span
                                            class="text-danger">*</span></label>
                                    <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    {{ __('Register') }}
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="mb-0">Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('script')
@endsection
