@extends('admin.layout.layout')

@section('page_title', 'Ingredient Category Detail')

@section('content')

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Ingredient Category Detail</h2>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <a href="{{ route('admin.ingredient_category.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>

                @role('Superadmin')
                    <form action="{{ route('admin.ingredient_category.destroy', ['id' => $ingredientCategory->id]) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="deleteConfirmation(event)">
                            <i class="fa-solid fa-trash"></i> Delete
                        </button>
                    </form>

                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editIngredientCategoryModal">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </button>
                @endrole
            </div>
        </div>
    </div>

    <div class="card card-shadow border-0 bg-white">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="fw-bold">Name:</div>
                    <div>{{ $ingredientCategory->name }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 mb-3">
        <div class="col">
            <h2 class="fw-bold">Ingredient Stock</h2>
        </div>

        @role('Superadmin')
            <div class="col-12 col-md-auto">
                <div class="d-flex gap-2 align-items-center float-end">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addIngredientModal">
                        <i class="fa-solid fa-plus"></i> Add
                    </button>
                </div>
            </div>
        @endrole
    </div>

    @livewire('admin.ingredient-category.ingredient-list', ['ingredientCategoryId' => $ingredientCategory->id])

    <!-- Modal for Edit Ingredient Category -->
    <div class="modal fade" id="editIngredientCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Ingredient Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="form"
                        action="{{ route('admin.ingredient_category.update', ['id' => $ingredientCategory->id]) }}"
                        method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ old('name', $ingredientCategory->name) }}" placeholder="Name" required>
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

    <!-- Modal for Add Ingredient Stock -->
    <div class="modal fade" id="addIngredientModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Ingredient Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="add-ingredient-form" action="{{ route('admin.ingredient.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-12">
                                <div class="default-image-wrapper mb-3">
                                    <img id="image-display" src="{{ asset('img/default-image.png') }}"
                                        data-initial-image="{{ asset('img/default-image.png') }}"
                                        onerror="this.onerror=null; this.src='{{ asset('img/default-image.png') }}'">
                                    <input type="file" class="image-input d-none" name="image" id="ingredient-image"
                                        accept=".jpg, .jpeg, .png, .webp" hidden>
                                </div>

                                <div class="text-center mb-3">
                                    <button type="button" class="btn btn-warning" onclick="uploadImage()">Upload</button>
                                    <button type="button" class="btn btn-danger d-none" id="remove-btn"
                                        onclick="removeImage()">Remove</button>
                                </div>
                            </div>

                            <input type="text" name="ingredient_category_id" value="{{ $ingredientCategory->id }}"
                                hidden>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="ingredient-name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="ingredient-name"
                                        value="{{ old('name') }}" placeholder="Name" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="ingredient-unit_type" class="form-label">Unit Type</label>
                                    <select class="form-select" name="unit_type" id="ingredient-unit_type" required>
                                        <option value="weight" {{ old('unit_type') === 'weight' ? 'selected' : '' }}>
                                            Weight (kg)</option>
                                        <option value="quantity" {{ old('unit_type') === 'quantity' ? 'selected' : '' }}>
                                            Quantity (qty)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="ingredient-stock" class="form-label"></label>
                                    <input type="number" class="form-control" name="stock" id="ingredient-stock"
                                        value="{{ old('stock') }}">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="ingredient-min_stock" class="form-label"></label>
                                    <input type="number" class="form-control" name="min_stock"
                                        id="ingredient-min_stock" value="{{ old('min_stock') }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="ingredient-weight_unit" class="form-label">Weight Unit (kg)</label>
                                    <input type="number" class="form-control" name="weight_unit"
                                        id="ingredient-weight_unit" step="0.01" min="0.01"
                                        value="{{ old('weight_unit') }}" placeholder="Weight unit (kg)" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="ingredient-price" class="form-label"></label>
                                    <input type="number" class="form-control" name="price" id="ingredient-price"
                                        min="0.01" step="0.01" value="{{ old('price') }}" required>
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
            });

            $('#add-ingredient-form').validate({
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
            });

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

            const unitTypeSelect = $('#ingredient-unit_type');
            const stockInput = $('#ingredient-stock');
            const minStockInput = $('#ingredient-min_stock');
            const priceInput = $('#ingredient-price');

            function updateFields() {
                const selectedType = unitTypeSelect.val();

                if (selectedType === 'weight') {
                    $('label[for="ingredient-stock"]').text('Current Stock (kg)');
                    stockInput.attr({
                        min: '0.01',
                        step: '0.01',
                        placeholder: 'Current stock (kg)'
                    });

                    $('label[for="ingredient-min_stock"]').text('Minimum Stock (kg)');
                    minStockInput.attr({
                        min: '0.01',
                        step: '0.01',
                        placeholder: 'Minimum stock (kg)'
                    });

                    $('label[for="ingredient-price"]').text('Price per Weight Unit (RM)');
                    priceInput.attr('placeholder', 'Price per weight unit (RM)');

                } else {
                    $('label[for="ingredient-stock"]').text('Current Stock (qty)');
                    stockInput.attr({
                        min: '1',
                        step: '1',
                        placeholder: 'Current stock (qty)'
                    });

                    $('label[for="ingredient-min_stock"]').text('Minimum Stock (qty)');
                    minStockInput.attr({
                        min: '1',
                        step: '1',
                        placeholder: 'Minimum stock (qty)'
                    });

                    $('label[for="ingredient-price"]').text('Price per Quantity (RM)');
                    priceInput.attr('placeholder', 'Price per quantity (RM)');
                }

                if (!stockInput.val()) {
                    stockInput.val('');
                }
                if (!minStockInput.val()) {
                    minStockInput.val('');
                }
            }

            updateFields();

            unitTypeSelect.on('change', updateFields);
        });

        function uploadImage() {
            $('#ingredient-image').click();
        }

        function removeImage() {
            $('#ingredient-image').val(null);
            var initialImage = $('#image-display').data('initial-image');
            $('#image-display').attr("src", initialImage);
            $('#remove-btn').addClass('d-none');
        }
    </script>
@endsection
