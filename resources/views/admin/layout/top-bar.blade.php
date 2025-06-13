<nav class="topbar">
    <div class="hamburger-menu" onclick="toggleSidebar()" role="button">
        <div class="line-1"></div>
        <div class="line-2"></div>
        <div class="line-3"></div>
    </div>

    <a class="topbar-title ms-auto" href="">
        <h4 class="mb-0">I Mum Mum 板面专卖店</h4>
    </a>

    <div class="dropdown ms-auto">
        <a href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ asset('img/default-avatar-light.png') }}" class="user-image"
                onerror="this.onerror=null; this.src='{{ asset('img/default-avatar-light.png') }}'">
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="#">Account Profile</a></li>
            <li>
                <form id="logout-form" action="" method="POST" style="display: none;">
                    @csrf
                </form>
                <a class="dropdown-item" href="#"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            </li>
        </ul>
    </div>
</nav>
