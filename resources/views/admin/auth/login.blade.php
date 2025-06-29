<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SIMPUS</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link href="{{ asset('assets/admin/vendor/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            max-width: 450px;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="card shadow-sm login-card">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" width="72" height="72"
                        class="mb-3">
                    <h3 class="card-title mb-1 fw-bold">Admin Login</h3>
                    <p class="text-muted">Sistem Informasi Perpustakaan</p>
                </div>

                <form method="POST" action="#">
                    @csrf

                    <div class="mb-3">
                        <label for="nip" class="form-label">NIP</label>
                        <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip"
                            name="nip" value="{{ old('nip') }}" required autofocus>
                        @error('nip')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Ingat Saya
                        </label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Login</button>
                    </div>

                </form>
            </div>
            <div class="card-footer text-center py-3">
                <small class="text-muted">&copy; {{ date('Y') }} SMKN 1 Sanggau Ledo</small>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/admin/vendor/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
