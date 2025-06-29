@extends('admin.components.main')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Konfigurasi Pengaturan Sistem</h6>
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')
            @if (
                $errors->has('settings') &&
                    !$errors->hasAny(array_map(fn($key) => 'settings.' . $key, $settings->pluck('key')->all())))
                @include('admin.components.validation_errors')
            @endif


            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                @if ($settings->isEmpty())
                    <div class="alert alert-warning text-center">
                        Belum ada data pengaturan di database. Silakan jalankan seeder atau tambahkan manual.
                    </div>
                @else
                    @foreach ($settings as $setting)
                        <div class="mb-3 row align-items-center border-bottom pb-3">
                            <label for="setting-{{ $setting->key }}" class="col-md-4 col-form-label">
                                {{ $setting->description ?: Str::title(str_replace('_', ' ', $setting->key)) }}
                                @if ($setting->description)
                                    <br><small
                                        class="text-muted">{{ Str::title(str_replace('_', ' ', $setting->key)) }}</small>
                                @endif
                            </label>
                            <div class="col-md-8">
                                @if (strlen($setting->value) > 100 || str_contains($setting->value, "\n"))
                                    <textarea class="form-control @error('settings.' . $setting->key) is-invalid @enderror" id="setting-{{ $setting->key }}"
                                        name="settings[{{ $setting->key }}]" rows="3">{{ old('settings.' . $setting->key, $setting->value) }}</textarea>
                                @else
                                    <input
                                        type="{{ $setting->key === 'loan_duration' || $setting->key === 'max_loan_books' || $setting->key === 'fine_rate_per_day' || $setting->key === 'booking_expiry_days' ? 'number' : 'text' }}"
                                        class="form-control @error('settings.' . $setting->key) is-invalid @enderror"
                                        id="setting-{{ $setting->key }}" name="settings[{{ $setting->key }}]"
                                        value="{{ old('settings.' . $setting->key, $setting->value) }}">
                                @endif

                                @error('settings.' . $setting->key)
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save-fill me-1"></i> Simpan Pengaturan
                        </button>
                    </div>
                @endif

            </form>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .col-form-label small {
            font-weight: normal;
            font-size: 0.8em;
        }
    </style>
@endsection

@section('script')
@endsection
