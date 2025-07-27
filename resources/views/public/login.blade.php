@extends('public.layout.layout')

@section('page_title', 'Login')

@section('content')

    <h2 class="custom-title">Admin Login</h2>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @elseif (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form id="form" method="POST" action="{{ route('login.submit') }}">
        @csrf

        <div class="form-group mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}"
                placeholder="Email address" required>
        </div>

        <div class="form-group mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
        </div>

        <button type="submit" class="btn btn-custom w-100">Login</button>

        <div class="text-center mt-3">
            <a href="{{ route('forgot_password.index') }}">Forgot password?</a>
        </div>
    </form>

@endsection

@section('script')
    <script>
        $(function() {
            $('#form').validate({
                rules: {
                    email: {
                        required: true,
                        email: true,
                    },
                    password: {
                        required: true,
                        minlength: 6,
                    },
                },
                messages: {
                    email: {
                        required: "Please enter your email address.",
                        email: "Please enter a valid email address.",
                    },
                    password: {
                        required: "Please enter your password.",
                        minlength: "Your password must be at least 6 characters long.",
                    },
                },
                errorElement: 'span',
                errorClass: 'invalid-feedback',
                errorPlacement: function(error, element) {
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
            });
        })
    </script>
@endsection
