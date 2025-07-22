@extends('admin.layout.layout')

@section('page_title', 'Product Category Detail')

@section('content')

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Product Category Detail</h2>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <a href="{{ route('admin.product_category.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
                <form action="{{ route('admin.product_category.destroy', ['id' => $productCategory->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="deleteConfirmation(event)">
                        <i class="fa-solid fa-trash"></i> Delete
                    </button>
                </form>
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editProductCategoryModal">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                </button>
            </div>
        </div>
    </div>

    <div class="card card-shadow border-0 bg-white">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="fw-bold">
                        Name:
                    </div>
                    <div>
                        {{ $productCategory->name }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 mb-3">
        <div class="col">
            <h2 class="fw-bold">Product List</h2>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fa-solid fa-plus"></i> Add
                </button>
            </div>
        </div>
    </div>

    @livewire('admin.product-category.product-list', ['productCategoryId' => $productCategory->id])

    <!-- Modal for Edit Product Category -->
    <div class="modal fade" id="editProductCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Product Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="form"
                        action="{{ route('admin.product_category.update', ['id' => $productCategory->id]) }}"
                        method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ $productCategory->name }}" placeholder="Name" required>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-12">
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-warning">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Add Product -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="add-product-form" action="{{ route('admin.product.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-12">
                                <div class="default-image-wrapper mb-3">
                                    <img id="image-display" src="{{ asset('img/default-image.png') }}"
                                        data-initial-image="{{ asset('img/default-image.png') }}"
                                        onerror="this.onerror=null; this.src='{{ asset('img/default-image.png') }}'">
                                    <input type="file" class="image-input d-none" name="image" id="product-image"
                                        accept=".jpg, .jpeg, .png, .webp" hidden>
                                </div>

                                <div class="text-center mb-3">
                                    <button type="button" class="btn btn-warning" onclick="uploadImage()">Upload</button>
                                    <button type="button" class="btn btn-danger d-none" id="remove-btn"
                                        onclick="removeImage()">Remove</button>
                                </div>
                            </div>

                            <input type="text" name="product_category_id" value="{{ $productCategory->id }}" hidden>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="product-name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="product-name"
                                        placeholder="Name" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="product-price" class="form-label">Price</label>
                                    <input type="number" class="form-control" name="price" id="product-price"
                                        step="0.01" placeholder="Price" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="product-description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" id="product-description" rows="3"
                                        placeholder="Description"></textarea>
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

            $('#add-product-form').validate({
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
            $('#product-image').click();
        }

        function removeImage() {
            $('#product-image').val(null);

            var initialImage = $('#image-display').data('initial-image');
            $('#image-display').attr("src", initialImage);
            $('#remove-btn').addClass('d-none');
        }
    </script>
@endsection
