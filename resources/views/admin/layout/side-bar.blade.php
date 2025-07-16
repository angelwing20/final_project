<div class="sidebar" id="sidebar">

    <div class="logo-container">
        <img src="{{ asset('img/i_mum_mum_logo.png') }}" alt="Logo" class="logo-image">
    </div>

    <div class="scrollable-area">
        <nav class="sidebar-nav">

            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                Dashboard
            </a>

            <a href="{{ route('admin.ingredient.index') }}"
                class="sidebar-link {{ Request::routeIs('admin.ingredient.*') ? 'active' : '' }}">
                <i class="fas fa-boxes-stacked"></i>
                Ingredient
            </a>

            <a href="{{ route('admin.ingredient_category.index') }}"
                class="sidebar-link {{ Request::routeIs('admin.ingredient_category.*') ? 'active' : '' }}">
                <i class="fas fa-boxes-stacked"></i>
                Ingredient Category
            </a>

            <a href="{{ route('admin.product.index') }}"
                class="sidebar-link {{ Request::routeIs('admin.product.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i>
                Product
            </a>

            <a href="{{ route('admin.product_category.index') }}"
                class="sidebar-link {{ Request::routeIs('admin.product_category.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i>
                Product Category
            </a>

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
                    <a href="{{ route('admin.staff.index') }}"
                        class="sidebar-link {{ Request::routeIs('admin.staff.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i> Staff
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <i class="fa-solid fa-angle-left close-sidebar-btn" onclick="toggleSidebar()" role="button"></i>
</div>
