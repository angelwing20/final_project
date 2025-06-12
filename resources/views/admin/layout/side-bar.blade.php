<head>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
</head>
<!-- Menu Button for Small Screens -->
<button class="btn btn-outline-primary d-md-none m-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
    <i class="fas fa-bars"></i> Menu
</button>

<div class="sidebar" id="sidebar">
    <br><br>
    <!-- Logo Section -->
    <div class="logo-container">
        <img src="{{ asset('img/i_mum_mum_logo.png') }}" alt="Logo" class="logo-image">
    </div>

    <!-- Scrollable Nav Area -->
    <div class="scrollable-area">
        <nav class="sidebar-nav">
            <!-- Top Links -->
            <a href="#" class="sidebar-link ">
                <i class="fas fa-chart-line"></i>
                Dashboard
            </a>

            <!-- Collapsible Ingredients Section -->
            <a class="sidebar-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#ingredientsCollapse" role="button" aria-expanded="false" aria-controls="ingredientsCollapse">
                <span><i class="fas fa-carrot"></i> Ingredients</span>
                <i class="fas fa-angle-down"></i>
            </a>

            <div class="collapse ps-4" id="ingredientsCollapse">
                <a href="#" class="sidebar-link">
                    <i class="far fa-circle"></i> Vegetables
                </a>
              
                <a href="#" class="sidebar-link">
                    <i class="far fa-dot-circle" style="color: lightgreen;"></i> Fruits
                </a>
            </div>

            <a href="#" class="sidebar-link">
                <i class="fas fa-boxes-stacked"></i>
                Inventory
            </a>

            <a href="#" class="sidebar-link">
                <i class="fas fa-truck"></i>
                Suppliers
            </a>

            <!-- Divider -->
            <div class="sidebar-divider"></div>

            <!-- Category Group -->
            <a href="#" class="sidebar-link">
                <i class="fas fa-layer-group"></i>
                Categories
            </a>

            
        </nav>
    </div>
</div>
