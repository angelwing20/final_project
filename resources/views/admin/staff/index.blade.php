@extends('admin.layout.layout')

@section('page_title', 'Staff')

@section('content')

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Staff</h2>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                    <i class="fa-solid fa-plus"></i> Add
                </button>
            </div>
        </div>
    </div>

    {{-- livewire --}}
    @livewire('admin.staff-list')

    <!-- Modal for Add Staff -->
    <div class="modal fade" id="addStaffModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="form" action="{{ route('admin.staff.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-12">
                                <div class="default-avatar-wrapper mb-3">
                                    <img id="image-display" src="{{ asset('img/default-avatar-light.png') }}"
                                        data-initial-image="{{ asset('img/default-avatar-light.png') }}"
                                        onerror="this.onerror=null; this.src='{{ asset('img/default-avatar-light.png') }}'">
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
                                        <option value="" selected disabled>Select role</option>
                                        <option value="Superadmin">Superadmin</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        placeholder="Name" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="Email address" required>
                                </div>
                            </div>

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
