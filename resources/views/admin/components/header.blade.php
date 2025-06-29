<header class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top p-0 shadow admin-header">
    <div class="container-fluid">

        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" width="30" height="30"
                class="d-inline-block align-text-top me-2">
            SIMPUS Admin
        </a>

        <div class="d-flex align-items-center ms-auto">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle px-3 text-white" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-1"></i>
                    <span>{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm mt-2">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.profile.edit') }}">
                            <i class="bi bi-person-fill me-2"></i>
                            Profil Saya
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item d-flex align-items-center">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <button class="navbar-toggler d-lg-none collapsed border-0" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#sidebarMenuOffcanvas" aria-controls="sidebarMenuOffcanvas" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

    </div>
</header>
