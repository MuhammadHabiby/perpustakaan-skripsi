@extends('user.components.main')

@section('title', 'Registrasi Menunggu Persetujuan')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5 text-center">

                        <div class="mb-3">
                            <i class="bi bi-info-circle-fill text-primary" style="font-size: 3rem;"></i>
                        </div>

                        <h3 class="card-title mb-3 fw-bold">Registrasi Anda Berhasil!</h3>

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @else
                            <p class="text-muted">Akun Anda telah berhasil didaftarkan.</p>
                        @endif

                        <p class="text-muted">
                            Akun Anda memerlukan aktivasi oleh admin perpustakaan sebelum dapat digunakan untuk login.
                            Silakan tunggu proses aktivasi atau hubungi petugas perpustakaan jika diperlukan.
                        </p>

                        <hr class="my-4">

                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Kembali ke Halaman Login
                        </a>

                    </div>
                    <div class="card-footer text-center py-3 bg-light">
                        <small class="text-muted">&copy; {{ date('Y') }} {{ config('app.name', 'SIMPerpus') }}</small>
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
