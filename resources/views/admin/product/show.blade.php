@extends('admin.layout.layout')

@section('page_title', 'Product Detail')

@section('content')

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Product Detail</h2>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <a href="{{ route('admin.product.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
                <form action="{{ route('admin.product.destroy', ['id' => $product->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="deleteConfirmation(event)">
                        <i class="fa-solid fa-trash"></i> Delete
                    </button>
                </form>
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editProductModal">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                </button>
            </div>
        </div>
    </div>

    <div class="card card-shadow border-0 bg-white">
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3 text-center">
                    <div>
                        <img src="{{ $product->image ? asset('storage/product/' . $product->image) : asset('img/default-image.png') }}"
                        onerror="this.onerror=null;this.src='{{ asset('img/default-image.png') }}'"
                        alt="Product Image"
                        style="width: 200px; height: 200px; object-fit: cover; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); display: block; margin: 0 auto;">
                    </div>
                </div>
                <div class="col-12">
                    <div class="fw-bold">
                        Product Category :
                    </div>
                    <div>
                        {{ $product->productCategory->name }}
                    </div>
                    <hr class="text-muted">
                </div>
                <div class="col-12">
                    <div class="fw-bold">
                        Name:
                    </div>
                    <div>
                        {{ $product->name }}
                    </div>
                    <hr class="text-muted">
                </div>
                <div class="col-12">
                    <div class="fw-bold">
                        Price:
                    </div>
                    <div>
                        {{ $product->price }}
                    </div>
                    <hr class="text-muted">
                </div>
                <div class="col-12">
                    <div class="fw-bold">
                        Description:
                    </div>
                    <div>
                        {{ $product->description ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Edit Product -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Product Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="form" action="{{ route('admin.product.update', ['id' => $product->id]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-12">
                                <div class="default-image-wrapper mb-3">
                                    <img id="image-display"
                                    src="{{ $product->image ? asset('storage/product/'. $product->image) : asset('img/default-image.png') }}"
                                    data-initial-image="{{ asset('img/default-image.png') }}"
                                    onerror="this.onerror=null;this.src='{{ $product->image ? asset('storage/product/'. $product->image) : asset('img/default-image.png') }}'"
                                    alt="Product Image"
                                    width="150">
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
                                    <label for="product_category_id" class="form-label">Product Category</label>
                                    <select class="form-select" name="product_category_id" id="product_category_id"
                                        style="width: 100%" required>
                                         <option value="{{ $product->product_category_id }}" selected>
                                        {{ $product->productCategory->name }}
                                    </option>
                                    </select>
                                </div>
                            </div>
                             <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ $product->name }}" placeholder="Name" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="text" class="form-control" name="price" id="price"
                                        value="{{ $product->price }}" placeholder="Price" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <input type="text" class="form-control" name="description" id="description"
                                        value="{{ $product->description }}" placeholder="Description">
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

            $('#product_category_id').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                dropdownParent: $('#editProductModal .modal-content'),
                placeholder: 'Select Product Category',
                    
                ajax: {
                    url: "{{ route('admin.product_category.select_search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        var query = {
                            search_term: params.term,
                            page: params.page,
                        }
                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data.results, function(item) {
                                return {
                                    text: item.name,
                                    id: item.id,
                                }
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
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