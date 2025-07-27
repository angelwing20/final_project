@extends('public.layout.layout')

@section('page_title', 'Reset Password')

@section('content')

    <h2 class="custom-title">Reset Password</h2>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @elseif (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form id="form" method="POST" action="{{ route('reset_password.request') }}">
        @csrf

        <input type="hidden" name="token" value="{{ Request::route('token') }}">
        <input type="hidden" name="email" value="{{ Request::route('email') }}">

        <div class="form-group mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
        </div>

        <div class="form-group mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation"
                placeholder="Confirm password" required>
        </div>

        <button type="submit" class="btn btn-custom w-100">Submit</button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('login.index') }}">Back to Login</a>
    </div>

@endsection

@section('script')
    <script>
        $(function() {
            $('#form').validate({
                rules: {
                    'password': {
                        required: true,
                        minlength: 6
                    },
                    'password_confirmation': {
                        required: true,
                        equalTo: "#password"
                    }
                },
                messages: {
                    'password_confirmation': {
                        equalTo: "Password not match."
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            })
        });
    </script>
@endsection
