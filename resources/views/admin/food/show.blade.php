@extends('admin.layout.layout')

@section('page_title', 'Food Detail')

@section('content')

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Food Detail</h2>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <a href="{{ route('admin.food.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>

                @role('Superadmin')
                    <form action="{{ route('admin.food.destroy', ['id' => $food->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="deleteConfirmation(event)">
                            <i class="fa-solid fa-trash"></i> Delete
                        </button>
                    </form>

                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editFoodModal">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </button>
                @endrole
            </div>
        </div>
    </div>

    <div class="card card-shadow border-0 bg-white">
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3 text-center">
                    <img src="{{ $food->image ? asset('storage/food/' . $food->image) : asset('img/default-image.png') }}"
                        onerror="this.onerror=null;this.src='{{ asset('img/default-image.png') }}'" alt="Food Image"
                        style="width: 200px; height: 200px; object-fit: cover; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); display: block; margin: 0 auto;">
                </div>

                <div class="col-12">
                    <div class="fw-bold">
                        Food Category:
                    </div>

                    <div>
                        <a class="text-decoration-none"
                            href="{{ route('admin.food_category.show', ['id' => $food->food_category_id]) }}"><span
                                class="badge rounded-pill text-bg-warning">{{ $food->foodCategory->name }}</span></a>
                    </div>
                </div>

                <div class="col-12">
                    <hr class="text-muted">
                </div>

                <div class="col-12">
                    <div class="fw-bold">
                        Name:
                    </div>

                    <div>
                        {{ $food->name }}
                    </div>
                </div>

                <div class="col-12">
                    <hr class="text-muted">
                </div>

                <div class="col-12">
                    <div class="fw-bold">
                        Price:
                    </div>

                    <div>
                        RM {{ $food->price }}
                    </div>
                </div>

                <div class="col-12">
                    <hr class="text-muted">
                </div>

                <div class="col-12">
                    <div class="fw-bold">
                        Description:
                    </div>

                    <div>
                        {{ $food->description ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 mb-3">
        <div class="col">
            <h2 class="fw-bold">Food Ingredient</h2>
        </div>

        @role('Superadmin')
            <div class="col-12 col-md-auto">
                <div class="d-flex gap-2 align-items-center float-end">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addFoodIngredientModal">
                        <i class="fa-solid fa-plus"></i> Add
                    </button>
                </div>
            </div>
        @endrole
    </div>

    @livewire('admin.food.food-ingredient-list', ['foodId' => $food->id])

    <!-- Modal for Edit Food Detail -->
    <div class="modal fade" id="editFoodModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Food Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="form" action="{{ route('admin.food.update', ['id' => $food->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-12">
                                <div class="default-image-wrapper mb-3">
                                    <img id="image-display"
                                        src="{{ $food->image ? asset('storage/food/' . $food->image) : asset('img/default-image.png') }}"
                                        data-initial-image="{{ asset('img/default-image.png') }}"
                                        onerror="this.onerror=null;this.src='{{ $food->image ? asset('storage/food/' . $food->image) : asset('img/default-image.png') }}'"
                                        alt="Food Image" width="150">
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
                                    <label for="food_category_id" class="form-label">Food Category<span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="food_category_id" id="food_category_id"
                                        style="width: 100%" required>
                                        <option value="{{ $food->food_category_id }}" selected>
                                            {{ $food->foodCategory->name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ old('name', $food->name) }}" placeholder="Name" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="price" class="form-label">Price<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="price" id="price"
                                        step="0.01" min="0.01" value="{{ old('price', $food->price) }}"
                                        placeholder="Price" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" name="description" id="description" rows="3" placeholder="Description">{{ old('description', $food->description) }}</textarea>
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

    <!-- Modal for Add Food Ingredient -->
    <div class="modal fade" id="addFoodIngredientModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Food Ingredient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="add-food-ingredient-form" action="{{ route('admin.food.ingredient.store') }}"
                        method="POST">
                        @csrf

                        <div class="row">
                            <input type="text" name="food_id" value="{{ $food->id }}" hidden>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="ingredient_id" class="form-label">Ingredient<span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="ingredient_id" id="ingredient_id"
                                        style="width: 100%" required>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="consumption" class="form-label">Consumption<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="consumption" id="consumption"
                                        value="{{ old('consumption') }}" placeholder="Consumption" required>
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

            $('#add-food-ingredient-form').validate({
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

            $('#food_category_id').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                dropdownParent: $('#editFoodModal .modal-content'),
                placeholder: 'Select food category',

                ajax: {
                    url: "{{ route('admin.food_category.select_search') }}",
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

            $('#ingredient_id').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                dropdownParent: $('#addFoodIngredientModal .modal-content'),
                placeholder: 'Select ingredient',

                ajax: {
                    url: "{{ route('admin.ingredient.select_search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        var query = {
                            search_term: params.term,
                            page: params.page,
                            exclude_food_id: "{{ $food->id }}",
                        }
                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data.results, function(item) {
                                return {
                                    text: item.name,
                                    id: item.id,
                                    data: {
                                        unit_type: item.unit_type,
                                        weight_unit: item.weight_unit
                                    }
                                }
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                }
            }).on('select2:select', function(e) {
                const selected = e.params.data.data;

                const label = selected.unit_type === 'weight' ? 'Weight (kg)' : 'Quantity (qty)';
                $('label[for="consumption"]').text(label);

                if (selected.unit_type === 'weight') {
                    $('#consumption').attr({
                        step: '0.001',
                        min: '0.001',
                        placeholder: 'Weight (kg)'
                    });
                } else {
                    $('#consumption').attr({
                        step: '1',
                        min: '1',
                        placeholder: 'Quantity (qty)'
                    });
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
