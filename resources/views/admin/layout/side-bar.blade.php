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
                Ingredient Stock
            </a>

            <a href="{{ route('admin.ingredient_category.index') }}"
                class="sidebar-link {{ Request::routeIs('admin.ingredient_category.*') ? 'active' : '' }}">
                <i class="fas fa-boxes-stacked"></i>
                Ingredient Category
            </a>

            <a href="{{ route('admin.food.index') }}"
                class="sidebar-link {{ Request::routeIs('admin.food.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i>
                Food Menu
            </a>

            <a href="{{ route('admin.food_category.index') }}"
                class="sidebar-link {{ Request::routeIs('admin.food_category.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i>
                Food Category
            </a>

            <a href="{{ route('admin.add_on.index') }}"
                class="sidebar-link {{ Request::routeIs('admin.add_on.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i>
                Add-on
            </a>

            <a href="{{ route('admin.daily_sales.index') }}"
                class="sidebar-link {{ Request::routeIs('admin.daily_sales.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-days"></i>
                Daily Sales
            </a>

            <a href="{{ route('admin.refill_stock_history.index') }}"
                class="sidebar-link {{ Request::routeIs('admin.refill_stock_history.*') ? 'active' : '' }}">
                <i class="fa-solid fa-table-list"></i>
                Refill Stock History
            </a>

            <a href="{{ route('admin.staff.index') }}"
                class="sidebar-link {{ Request::routeIs('admin.staff.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>
                Staff
            </a>
        </nav>
    </div>

    <i class="fa-solid fa-angle-left close-sidebar-btn" onclick="toggleSidebar()" role="button"></i>
</div>
