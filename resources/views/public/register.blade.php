<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Register | I Mum Mum</title>
    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
</head>

<body class="register-page">

    <div class="register-container">
        <img src="{{ asset('img/i_mum_mum_logo.png') }}" alt="I Mum Mum Logo" class="logo">

        <h2 class="register-title">Admin Registration</h2>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Registration Form --}}
        <form action="{{ route('register.submit') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" id="name" required
                    value="{{ old('name') }}">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" id="email" required
                    value="{{ old('email') }}">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password (min 6 characters)</label>
                <input type="password" name="password" class="form-control" id="password" required>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation"
                    required>
            </div>

            <button type="submit" class="btn btn-register text-white w-100">Register</button>

            <div class="text-center mt-3">
                <a href="{{ route('login.index') }}" class="login-link">Already have an account? Login here</a>
            </div>
        </form>
    </div>

</body>

</html>
