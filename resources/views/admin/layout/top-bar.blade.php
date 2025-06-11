<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I Mum Mum 板面专卖店 - @yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
    background-color: #FAF3E0; /* 浅米色背景 */
    }
        .navbar {
            background-color: #5C4033;
        }

        /* 品牌文字与链接颜色 */
        .navbar-brand {
            color: #FFFFFF;
        }
        .navbar-brand:hover {
            color: #D8CAB1;
        }

        .nav-link {
            color: #FFFFFF !important;
        }
        .nav-link:hover {
            background-color: #D8CAB1;
            color: #5C4033 !important;
        }

        /* 下拉菜单 */
        .dropdown-menu {
            background-color: #5C4033;
        }
        .dropdown-item {
            color: #FFFFFF;
        }
        .dropdown-item:hover {
            background-color: #D8CAB1;
            color: #5C4033;
        }

    </style>
</head>
<body>
    <!-- 顶部导航栏 -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('inventory.index') }}">I Mum Mum 板面专卖店</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('inventory.index') }}">库存管理</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('inventory.low') }}">低库存</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">订单录入</a> <!-- 待实现路由 -->
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            用户
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#">账户设置</a></li>
                            <li><a class="dropdown-item" href="{{ route('logout') }}">退出</a></li> <!-- 需认证后实现 -->
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- 内容区域 -->
    <div class="container mt-5 pt-5">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>