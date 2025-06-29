<header class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" width="30" height="30"
                class="d-inline-block align-text-top me-1">
            {{ config('app.name', 'SIMPerpus') }}
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainUserNavbar"
            aria-controls="mainUserNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainUserNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center">

                @auth('web')
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('catalog.*') ? 'active' : '' }}"
                            href="{{ route('catalog.index') }}">
                            Katalog Buku
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('user.borrowings.history') ? 'active' : '' }}"
                            href="{{ route('user.borrowings.history') }}">
                            Riwayat Pinjam
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('user.bookings.index') ? 'active' : '' }}"
                            href="{{ route('user.bookings.index') }}">
                            Booking Saya
                        </a>
                    </li>
                    <li class="nav-item" id="fcm-button-container" style="display: none;">
                        <button class="btn btn-outline-info btn-sm d-flex align-items-center" id="enable-fcm-button"
                            type="button">
                            <i class="bi bi-bell me-1"></i><span>Aktifkan Notifikasi Browser</span>
                        </button>
                    </li>

                    <li class="nav-item d-none d-lg-block mx-2 border-end" style="height: 20px;"></li>

                    <li class="nav-item dropdown">
                        @php
                            $unreadNotifications = Auth::user()->unreadNotifications()->take(5)->get();
                            $unreadCount = Auth::user()->unreadNotifications()->count();
                        @endphp
                        <a class="nav-link" href="#" id="notificationDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
                            <i class="bi bi-bell-fill position-relative fs-5">
                                @if ($unreadCount > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                        style="font-size: 0.65em;">
                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                        <span class="visually-hidden">unread messages</span>
                                    </span>
                                @endif
                            </i> <span>Notifikasi</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg-end" aria-labelledby="notificationDropdown">
                            <li class="dropdown-header text-center fw-bold">Notifikasi Belum Dibaca</li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            @forelse ($unreadNotifications as $notification)
                                <li>
                                    <a class="dropdown-item d-flex align-items-start small"
                                        href="{{ route('user.notifications.read', $notification->id) }}"
                                        onclick="event.preventDefault(); document.getElementById('mark-as-read-{{ $notification->id }}').submit();">
                                        <form id="mark-as-read-{{ $notification->id }}"
                                            action="{{ route('user.notifications.read', $notification->id) }}"
                                            method="POST" class="d-none">
                                            @csrf
                                            @method('PATCH')
                                        </form>
                                        <i
                                            class="bi {{ $notification->data['icon'] ?? 'bi-info-circle' }} text-primary mt-1 me-2"></i>
                                        <div>
                                            <div>{{ $notification->data['message'] ?? 'Notifikasi baru.' }}</div>
                                            <div class="text-muted" style="font-size: 0.8em;">
                                                {{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            @empty
                                <li><a class="dropdown-item text-center text-muted disabled" href="#">Tidak ada
                                        notifikasi baru</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            @endforelse

                            <li><a class="dropdown-item text-center text-primary fw-bold"
                                    href="{{ route('user.notifications.index') }}">Lihat Semua Notifikasi</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-4 me-1"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item {{ Route::is('user.profile.edit') ? 'active' : '' }}"
                                    href="{{ route('user.profile.edit') }}">
                                    <i class="bi bi-person-lines-fill"></i> Profil Saya</a>
                            </li>
                            <li><a class="dropdown-item {{ Route::is('user.fines.index') ? 'active' : '' }}"
                                    href="{{ route('user.fines.index') }}">
                                    <i class="bi bi-cash-coin me-2"></i> Denda Saya</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <a href="{{ route('logout') }}" class="dropdown-item text-danger"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth

                @guest('web')
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link btn btn-primary btn-sm px-3 me-2 text-white active"
                            href="{{ route('login') }}">Login</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary btn-sm px-3 text-white active"
                                href="{{ route('register') }}">Register</a>
                        </li>
                    @endif
                @endguest
            </ul>
        </div>
    </div>
</header>
