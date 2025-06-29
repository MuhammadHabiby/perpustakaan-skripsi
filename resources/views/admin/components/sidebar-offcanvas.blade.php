<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="sidebarMenuOffcanvas"
    aria-labelledby="sidebarMenuOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarMenuOffcanvasLabel">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" width="25" height="25"
                class="d-inline-block align-text-top me-2">
            Menu Admin
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        @include('admin.components.sidebar-menu')
    </div>
</div>
