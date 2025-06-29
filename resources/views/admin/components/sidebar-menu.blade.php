<ul class="nav flex-column sidebar-nav">
    <li class="nav-item">
        <a class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-house-door-fill"></i>
            Dashboard
        </a>
    </li>

    <li class="nav-item mt-2">
        <h6 class="sidebar-heading px-3 mb-1 text-uppercase">Master Buku</h6>
        <ul class="nav flex-column nav-submenu">
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.categories.*') ? 'active' : '' }}"
                    href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-tags-fill"></i> Kategori
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.authors.*') ? 'active' : '' }}"
                    href="{{ route('admin.authors.index') }}">
                    <i class="bi bi-person-fill"></i> Pengarang
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.publishers.*') ? 'active' : '' }}"
                    href="{{ route('admin.publishers.index') }}">
                    <i class="bi bi-building-fill"></i> Penerbit
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.books.*') ? 'active' : '' }}"
                    href="{{ route('admin.books.index') }}">
                    <i class="bi bi-book-half"></i> Manajemen Buku
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item mt-2">
        <h6 class="sidebar-heading px-3 mb-1 text-uppercase">Pengguna</h6>
        <ul class="nav flex-column nav-submenu">
            <li class="nav-item">
                <a class="nav-link {{ Route::is(['admin.site-users.index', 'admin.site-users.create', 'admin.site-users.edit', 'admin.site-users.show', 'admin.site-users.pending']) ? 'active' : '' }}"
                    href="{{ route('admin.site-users.index') }}">
                    <i class="bi bi-people-fill"></i> Manajemen Siswa
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.admin-users.*') ? 'active' : '' }}"
                    href="{{ route('admin.admin-users.index') }}">
                    <i class="bi bi-person-badge-fill"></i> Manajemen Admin
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item mt-2">
        <h6 class="sidebar-heading px-3 mb-1 text-uppercase">Sirkulasi</h6>
        <ul class="nav flex-column nav-submenu">
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.borrowings.*') && !Route::is('admin.borrowings.overdue') ? 'active' : '' }}"
                    href="{{ route('admin.borrowings.index') }}">
                    <i class="bi bi-arrow-repeat"></i> Daftar Semua Peminjaman
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.bookings.*') ? 'active' : '' }}"
                    href="{{ route('admin.bookings.index') }}">
                    <i class="bi bi-journal-bookmark-fill"></i> Manajemen Booking
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.borrowings.overdue') ? 'active' : '' }}"
                    href="{{ route('admin.borrowings.overdue') }}">
                    <i class="bi bi-calendar-x-fill"></i> Buku Lewat Tempo
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.lost-reports.*') ? 'active' : '' }}"
                    href="{{ route('admin.lost-reports.index') }}">
                    <i class="bi bi-exclamation-triangle-fill"></i> Laporan Kehilangan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.fines.*') ? 'active' : '' }}"
                    href="{{ route('admin.fines.index') }}">
                    <i class="bi bi-cash-coin"></i> Manajemen Denda
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item mt-2">
        <h6 class="sidebar-heading px-3 mb-1 text-uppercase">Laporan</h6>
        <ul class="nav flex-column nav-submenu">
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.reports.borrowings') ? 'active' : '' }}"
                    href="{{ route('admin.reports.borrowings') }}">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i> Lap. Peminjaman
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.reports.procurements') ? 'active' : '' }}"
                    href="{{ route('admin.reports.procurements') }}">
                    <i class="bi bi-file-earmark-ruled-fill"></i> Lap. Pengadaan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.reports.lost-books') ? 'active' : '' }}"
                    href="{{ route('admin.reports.lost-books') }}">
                    <i class="bi bi-file-earmark-excel-fill"></i> Lap. Kehilangan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.reports.fines') ? 'active' : '' }}"
                    href="{{ route('admin.reports.fines') }}">
                    <i class="bi bi-file-earmark-medical-fill"></i> Lap. Denda
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item mt-2">
        <h6 class="sidebar-heading px-3 mb-1 text-uppercase">Sistem</h6>
        <ul class="nav flex-column nav-submenu">
            <a class="nav-link {{ Route::is('admin.settings.*') ? 'active' : '' }}"
                href="{{ route('admin.settings.index') }}">
                <i class="bi bi-gear-fill"></i> Pengaturan
            </a>
        </ul>
    </li>
</ul>
