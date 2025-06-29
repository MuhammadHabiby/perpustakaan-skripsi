@extends('user.components.main')

@section('title', 'Login Siswa')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm login-card border-0 mt-5">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" width="72" height="72"
                                class="mb-3">
                            <h3 class="card-title mb-1 fw-bold">Login Siswa</h3>
                            <p class="text-muted">Sistem Informasi Perpustakaan</p>
                        </div>

                        @include('admin.components.flash_messages')

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="nis" class="form-label">{{ __('NIS') }}</label>
                                <input id="nis" type="text" class="form-control @error('nis') is-invalid @enderror"
                                    name="nis" value="{{ old('nis') }}" required autocomplete="nis" autofocus>
                                @error('nis')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('Password') }}</label>
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password" required
                                    autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Ingat Saya') }}
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    {{ __('Login') }}
                                </button>
                            </div>

                            <div class="text-center">
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Lupa Password Anda?') }}
                                    </a>
                                @endif
                                @if (Route::has('register'))
                                    <p class="mt-3 mb-0">Belum punya akun? <a href="{{ route('register') }}">Daftar di
                                            sini</a></p>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
