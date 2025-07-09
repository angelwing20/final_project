@extends('admin/layout/layout')

@section('page_title', 'Account Profile')

@section('content')

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Account Profile</h2>
        </div>

        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>

                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                </button>
            </div>
        </div>
    </div>

    <div class="card card-shadow border-0">
        <div class="card-body p-3 p-md-4">
            <div class="row g-4 align-items-center">

                <div class="col-12">
                    <div class="text-center">
                        <div class="default-avatar-wrapper mb-3">
                            <img src="{{ $user->image ? asset('storage/profile/' . $user->image) : asset('img/default-avatar-light.png') }}"
                                onerror="this.onerror=null; this.src='{{ $user->image ? asset('storage/profile/' . $user->image) : asset('img/default-avatar-light.png') }}'">
                        </div>
                        <span class="badge rounded-pill text-bg-warning">{{ Auth::user()->getRoleNames()->first() }}</span>
                    </div>
                </div>

                <div class="col-12">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-md-6">
                            <label class="fw-bold text-muted">Name</label>
                            <div class="fs-5 fw-semibold">{{ $user->name }}</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="fw-bold text-muted">Email Address</label>
                            <div class="fs-5 fw-semibold">{{ $user->email }}</div>
                        </div>

                        <div class="col-12">
                            <hr>
                        </div>

                        <div class="col col-md-6">
                            <label class="fw-bold text-muted">Password</label>
                            <div class="fs-5">************</div>
                        </div>

                        <div class="col-auto">
                            <button class="btn btn-outline-warning text-dark" data-bs-toggle="modal"
                                data-bs-target="#changePasswordModal">
                                <i class="fa-solid fa-lock me-2"></i> Change Password
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Account Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="form" action="{{ route('admin.account.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-12">

                                <div class="default-avatar-wrapper mb-3">
                                    <img id="image-display"
                                        src="{{ $user->image ? asset('storage/profile/' . $user->image) : asset('img/default-avatar-light.png') }}"
                                        data-initial-image="{{ asset('img/default-avatar-light.png') }}"
                                        onerror="this.onerror=null;this.src='{{ $user->image ? asset('storage/profile/' . $user->image) : asset('img/default-avatar-light.png') }}'"
                                        alt="Profile Image" width="150">
                                    <input type="file" class="image-input d-none" name="image" id="image"
                                        accept=".jpg, .jpeg, .png, .webp" hidden>
                                </div>

                                <div class="text-center mb-3">
                                    <button type="button" class="btn btn-warning" onclick="uploadImage()">Upload</button>
                                    <button type="button" class="btn btn-danger d-none" id="remove-btn"
                                        onclick="removeImage()">Remove</button>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ $user->name }}" placeholder="Name" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" id="email"
                                        value="{{ $user->email }}" placeholder="Email address" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-warning">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="update-password-form" action="{{ route('admin.account.update_password') }}"
                        method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" name="current_password"
                                        id="current_password" placeholder="Current password" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="password" id="password"
                                        placeholder="New password" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        id="password_confirmation" placeholder="Confirm password" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-warning">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('#form').validate({
                ignore: [],
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
                invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        notifier.show('Error!', 'Please ensure all inputs are correct.', 'warning', '',
                            4000);
                    }
                },
            })

            $('#update-password-form').validate({
                rules: {
                    current_password: {
                        required: true,
                    },
                    password: {
                        required: true,
                        minlength: 6,
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: '#password'
                    }
                },
                messages: {
                    'password_confirmation': {
                        equalTo: "Password not match."
                    }
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
                invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        notifier.show('Error!', 'Please ensure all inputs are correct.', 'warning', '',
                            4000);
                    }
                },
            })

            $('.image-input').change(function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        $('#image-display').attr("src", event.target.result);
                        $('#remove-btn').removeClass('d-none');
                    };
                    reader.readAsDataURL(file);
                } else {
                    var initialImage = $('#image-display').data('initial-image');
                    $('#image-display').attr("src", initialImage);
                    $('#remove-btn').addClass('d-none');
                }
            });
        })

        function uploadImage() {
            $('#image').click();
        }

        function removeImage() {
            $('#image').val(null);

            var initialImage = $('#image-display').data('initial-image');
            $('#image-display').attr("src", initialImage);
            $('#remove-btn').addClass('d-none');
        }
    </script>
@endsection
