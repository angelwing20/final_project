<div class="sidebar" id="sidebar">
    <!-- Logo Section -->
    <div class="logo-container">
        <img src="{{ asset('img/i_mum_mum_logo.png') }}" alt="Logo" class="logo-image">
    </div>

    <!-- Scrollable Nav Area -->
    <div class="scrollable-area">
        <nav class="sidebar-nav">

            <a href="#" class="sidebar-link ">
                <i class="fas fa-chart-line"></i>
                Dashboard
            </a>

            <div class="sidebar-section-group">
                <div class="sidebar-main-item" onclick="toggleSidebarSectionGroup(this)" role="button">
                    <a href="#" class="sidebar-link">
                        <div class="sidebar-link-icon">
                            <i class="fas fa-carrot"></i>
                        </div>
                        <div class="sidebar-link-title">
                            Ingredients
                        </div>
                        <i class="fas fa-angle-down rotate-icon ms-auto"></i>
                    </a>
                </div>

                <div class="sidebar-sub-item">
                    <a href="#1" class="sidebar-link">
                        <i class="far fa-circle"></i> Vegetables
                    </a>
                </div>

                <div class="sidebar-sub-item">
                    <a href="#2" class="sidebar-link">
                        <i class="far fa-dot-circle" style="color: lightgreen;"></i> Fruits
                    </a>
                </div>
            </div>

            <a href="#" class="sidebar-link">
                <i class="fas fa-boxes-stacked"></i>
                Inventory
            </a>

            <a href="#" class="sidebar-link">
                <i class="fas fa-truck"></i>
                Suppliers
            </a>

            <!-- Category Group -->
            <a href="#" class="sidebar-link">
                <i class="fas fa-layer-group"></i>
                Categories
            </a>


        </nav>
    </div>

    <i class="fa-solid fa-angle-left close-sidebar-btn" onclick="toggleSidebar()" role="button"></i>
</div>
