@extends('public.layout.layout')

@section('page_title', 'Forgot Password')

@section('content')

    <h2 class="custom-title">Forgot Password</h2>

    <p class="text-muted mb-4 text-center">Enter your email address and we'll send you a link to reset your password.</p>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @elseif (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form id="form" method="POST" action="{{ route('forgot_password.request') }}">
        @csrf

        <div class="form-group mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                placeholder="Email address" required>
        </div>

        <button type="submit" class="btn btn-custom w-100">Send Reset Link</button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('login.index') }}">Back to Login</a>
    </div>

@endsection

@section('script')
    <script>
        $(function() {
            $('#form').validate({
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
