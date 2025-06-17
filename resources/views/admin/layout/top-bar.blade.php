<nav class="topbar">
    <div class="hamburger-menu" onclick="toggleSidebar()" role="button">
        <div class="line-1"></div>
        <div class="line-2"></div>
        <div class="line-3"></div>
    </div>


    <a class="topbar-title ms-auto" href="{{ route('admin.dashboard') }}">
        <h4 class="mb-0">I Mum Mum 板面专卖店</h4>
    </a>

    <div class="ms-auto">
        <div role="button" data-bs-toggle="dropdown">
            <img src="{{ asset('img/default-avatar-light.png') }}" class="user-image"
                onerror="this.onerror=null; this.src='{{ asset('img/default-avatar-light.png') }}'">
        </div>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="{{ route('admin.profile') }}">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-user-gear"></i> Account Profile
                    </div>
                </a>
            </li>
            <li>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                </form>
                <a class="dropdown-item" href="#" onclick="$('#logout-form').submit();">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </div>
                </a>
            </li>
        </ul>
    </div>
</nav>
