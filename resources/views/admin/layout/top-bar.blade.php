<nav class="navbar navbar-expand-lg navbar-dark fixed-top custom-navbar">
    <div class="container-fluid">
        <!-- Sidebar Trigger Button -->
        <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- 品牌 -->
        <a class="navbar-brand me-auto" href="#">I Mum Mum 板面专卖店</a> 

        <!-- 用户头像 dropdown -->
        <div class="dropdown ms-auto">
            <a href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ asset('img/default-avatar-dark.png') }}" class="user-image" onerror="this.onerror=null; this.src='img/default-avatar-dark.png'">
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="#">账户设置</a></li>
                <li>
                    <form id="logout-form" action="" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">退出</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
