@extends('user.components.main')

@section('title', 'Notifikasi Saya')
@section('page-title', 'Semua Notifikasi')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 fw-bold text-primary"><i class="bi bi-bell-fill me-2"></i>Notifikasi Anda</h6>
            @if (Auth::user()->unreadNotifications()->count() > 0)
                <form action="{{ route('user.notifications.readall') }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Tandai semua notifikasi sebagai sudah dibaca?');">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-check2-all"></i> Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>
        <div class="card-body">
            @include('admin.components.flash_messages')

            @if ($notifications->isEmpty())
                <div class="alert alert-info text-center mb-0">
                    Tidak ada notifikasi untuk Anda saat ini.
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach ($notifications as $notification)
                        <div
                            class="list-group-item list-group-item-action px-0 py-3 {{ $notification->read() ? '' : 'bg-light fw-bold' }}">
                            <div class="row g-2 align-items-center">
                                <div class="col-auto">
                                    @php $icon = $notification->read() ? 'bi-envelope-open' : 'bi-envelope-fill'; @endphp
                                    @isset($notification->data['icon'])
                                        @php $icon = $notification->data['icon']; @endphp
                                    @endisset
                                    <i class="bi {{ $icon }} fs-4 text-primary me-2"></i>
                                </div>
                                <div class="col">
                                    <div class="mb-1">{{ $notification->data['message'] ?? 'Notifikasi baru.' }}</div>
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="col-auto">
                                    @if ($notification->unread())
                                        <form action="{{ route('user.notifications.read', $notification->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                title="Tandai sudah dibaca">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @isset($notification->data['link'])
                                        <a href="{{ $notification->data['link'] }}" class="btn btn-sm btn-outline-info"
                                            title="Lihat Detail">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    @endisset
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            Menampilkan {{ $notifications->firstItem() }}
                            hingga {{ $notifications->lastItem() }}
                            dari {{ $notifications->total() }} hasil
                        </small>
                    </div>
                    <div>
                        {{ $notifications->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('css')
    <style>
        .list-group-item.bg-light {
            background-color: #f8f9fc !important;
        }
    </style>
@endsection

@section('script')
@endsection
