<div class="sidebar" id="sidebar">
    <!-- Logo Section -->
    <div class="logo-container">
        <img src="{{ asset('img/i_mum_mum_logo.png') }}" alt="Logo" class="logo-image">
    </div>

    <!-- Scrollable Nav Area -->
    <div class="scrollable-area">
        <nav class="sidebar-nav">

            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> 
                Dashboard
            </a>

              <a href="{{ route('admin.inventory.index') }}" class="sidebar-link {{ Request::routeIs('admin.inventory.*') ? 'active' : '' }}">
                <i class="fas fa-boxes-stacked"></i> 
                Inventory
            </a>

            <a href="{{ route('admin.suppliers.index') }}" class="sidebar-link {{ Request::routeIs('admin.suppliers .*') ? 'active' : '' }}">
                <i class="fas fa-truck"></i> 
                Suppliers
            </a>

            <a href="{{ route('admin.product.index') }}" class="sidebar-link {{ Request::routeIs('admin.product.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i> 
                Product
            </a>

            <a href="{{ route('admin.category.category') }}" class="sidebar-link {{ Request::routeIs('admin.category.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i> 
                Product Category
            </a>

            <!-- Category Group -->
            <div class="sidebar-section-group {{ Request::routeIs('admin.staff.*') ? 'active' : '' }}">
                <div class="sidebar-main-item" onclick="toggleSidebarSectionGroup(this)" role="button">
                    <a href="#" class="sidebar-link">
                        <div class="sidebar-link-icon">
                            <i class="fa-solid fa-gear"></i>
                        </div>
                        <div class="sidebar-link-title">Setting</div>
                        <i class="fas fa-angle-down rotate-icon ms-auto"></i>
                    </a>
                </div>

                <div class="sidebar-sub-item">
                    <a href="{{ route('admin.staff.index') }}" class="sidebar-link {{ Request::routeIs('admin.staff.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i> Staff
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <i class="fa-solid fa-angle-left close-sidebar-btn" onclick="toggleSidebar()" role="button"></i>
</div>
