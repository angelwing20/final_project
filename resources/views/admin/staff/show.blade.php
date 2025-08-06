@extends('admin.layout.layout')

@section('page_title', 'Staff Detail')

@section('content')

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Staff Detail</h2>
        </div>

        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>

                @role('Superadmin')
                    <form action="{{ route('admin.staff.destroy', ['id' => $staff->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="deleteConfirmation(event)">
                            <i class="fa-solid fa-trash"></i> Delete
                        </button>
                    </form>

                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editStaffModal">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </button>
                @endrole
            </div>
        </div>
    </div>

    <div class="card card-shadow border-0 bg-white">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-12 mb-3 text-center">
                    <div class="default-avatar-wrapper mb-3">
                        <img src="{{ $staff->image ? asset('storage/profile/' . $staff->image) : asset('img/default-avatar-light.png') }}"
                            onerror="this.onerror=null; this.src='{{ $staff->image ? asset('storage/profile/' . $staff->image) : asset('img/default-avatar-light.png') }}'">
                    </div>
                    @foreach ($staff->roles as $role)
                        <span class="badge rounded-pill text-bg-warning">{{ $role->name }}</span>
                    @endforeach
                </div>

                <div class="col-12">
                    <div class="fw-bold">
                        Name:
                    </div>
                    <div>
                        {{ $staff->name }}
                    </div>
                </div>

                <div class="col-12">
                    <hr class="text-muted">
                </div>

                <div class="col-12">
                    <div class="fw-bold">
                        Email Address:
                    </div>
                    <div>
                        {{ $staff->email }}
                    </div>
                </div>

                <div class="col-12">
                    <hr class="text-muted">
                </div>

                <div class="col col-md-6">
                    <label class="fw-bold text-muted">Password:</label>
                    <div class="fs-5">************</div>
                </div>

                @role('Superadmin')
                    <div class="col-auto">
                        <button class="btn btn-outline-warning text-dark" data-bs-toggle="modal"
                            data-bs-target="#changePasswordModal">
                            <i class="fa-solid fa-lock me-2"></i> Change Password
                        </button>
                    </div>
                @endrole
            </div>
        </div>
    </div>

    <!-- Modal for Edit Staff Detail -->
    <div class="modal fade" id="editStaffModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Staff Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="form" action="{{ route('admin.staff.update', ['id' => $staff->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-12">
                                <div class="default-avatar-wrapper mb-3">
                                    <img id="image-display"
                                        src="{{ $staff->image ? asset('storage/profile/' . $staff->image) : asset('img/default-avatar-light.png') }}"
                                        data-initial-image="{{ asset('img/default-avatar-light.png') }}"
                                        onerror="this.onerror=null;this.src='{{ $staff->image ? asset('storage/profile/' . $staff->image) : asset('img/default-avatar-light.png') }}'"
                                        alt="Staff Image" width="150">
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
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-select" name="role" id="role" required>
                                        <option value="Superadmin"
                                            {{ old('role', $staff->roles->first()?->name === 'Superadmin' ? 'selected' : '') }}>
                                            Superadmin
                                        </option>
                                        <option value="Admin"
                                            {{ old('role', $staff->roles->first()?->name === 'Admin' ? 'selected' : '') }}>
                                            Admin
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ old('name', $staff->name) }}" placeholder="Name" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" id="email"
                                        value="{{ old('email', $staff->email) }}" placeholder="Email address" required>
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

    <!-- Modal for Change Password -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="update-password-form"
                        action="{{ route('admin.staff.update_password', ['id' => $staff->id]) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" id="password"
                                        placeholder="Password" required>
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
                    password: {
                        required: true,
                        minlength: 6,
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
