<nav class="navbar navbar-expand-lg navbar-dark fixed-top custom-navbar">
    <div class="container-fluid">
        <!-- Sidebar Trigger Button 可放左边（留空也可以）-->
        <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- 店名 Brand -->
        <a class="navbar-brand me-auto" href="#">I Mum Mum 板面专卖店</a>

        <!-- 用户资料区域：在手机时靠右 -->
        <div class="d-flex ms-auto">
            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="images/default-avatar-dark.png" class="user-image"
                    onerror="this.onerror=null; this.src='images/default-avatar-dark.png'">
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    @auth
                        <li><a class="dropdown-item" href="#">账户设置</a></li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}">退出</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </div>
</nav>
