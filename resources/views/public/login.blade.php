<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
</head>
<body class="login-page">

    <div class="login-container">
        <img src="{{ asset('img/i_mum_mum_logo.png') }}" alt="I Mum Mum Logo" class="logo">

        <h2 class="login-title">Admin Login</h2>

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" name="email" id="email" class="form-control" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-login text-white w-100">Login</button>

            <p class="mt-3 text-center">
                Don't have an account?
                <a href="{{ route('admin.register') }}" class="register-link">Register here</a>
            </p>
        </form>
    </div>

</body>
</html>
