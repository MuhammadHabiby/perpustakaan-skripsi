@extends('user.components.main')

@section('title', 'Reset Password')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm login-card border-0 mt-5">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" width="72" height="72"
                                class="mb-3">
                            <h3 class="card-title mb-1 fw-bold">{{ __('Reset Password') }}</h3>
                            <p class="text-muted">Masukkan email Anda untuk menerima link reset password.</p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @include('admin.components.flash_messages')

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Alamat Email') }}</label>
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    {{ __('Kirim Link Reset Password') }}
                                </button>
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('login') }}">Kembali ke Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
