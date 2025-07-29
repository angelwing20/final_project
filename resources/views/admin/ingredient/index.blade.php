@extends('admin.layout.layout')

@section('page_title', 'Ingredient')

@section('content')

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Ingredient</h2>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#refillStockModal">
                    <i class="fa-solid fa-plus"></i> Refill Stock
                </button>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addIngredientModal">
                    <i class="fa-solid fa-plus"></i> Add
                </button>
            </div>
        </div>
    </div>

    {{-- livewire --}}
    @livewire('admin.ingredient-list')

    <!-- Modal for refill stock -->
    <div class="modal fade" id="refillStockModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Refill Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="refill-form" action="{{ route('admin.ingredient.refill_stock') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div id="refill-container">
                            <div class="refill-group mb-3">
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <div class="form-group">
                                            <label class="form-label" for="refill-ingredient-0">Ingredient</label>
                                            <select class="form-select ingredient-select" id="refill-ingredient-0"
                                                name="refills[0][ingredient_id]" required></select>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label" for="refill-quantity-0">Quantity</label>
                                            <input type="number" class="form-control" id="refill-quantity-0"
                                                name="refills[0][quantity]" step="1" min="1"
                                                placeholder="Quantity">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label" for="refill-weight-0">Weight (kg)</label>
                                            <input type="number" class="form-control" id="refill-weight-0"
                                                name="refills[0][weight]" step="0.01" min="0.01" placeholder="Weight"
                                                required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-light text-center rounded p-2 mb-3" role="button" id="add-refill-btn">
                            <i class="fa fa-plus"></i> Add another ingredient
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-warning">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Add Ingredient -->
    <div class="modal fade" id="addIngredientModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Ingredient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="form" action="{{ route('admin.ingredient.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-12">
                                <div class="default-image-wrapper mb-3">
                                    <img id="image-display" src="{{ asset('img/default-image.png') }}"
                                        data-initial-image="{{ asset('img/default-image.png') }}"
                                        onerror="this.onerror=null; this.src='{{ asset('img/default-image.png') }}'">
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
                                    <label for="ingredient_category_id" class="form-label">Ingredient Category</label>
                                    <select class="form-select" name="ingredient_category_id" id="ingredient_category_id"
                                        style="width: 100%" required>
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
                                    <label for="stock_weight" class="form-label">Stock Weight (kg)</label>
                                    <input type="number" class="form-control" name="stock_weight" id="stock_weight"
                                        step="0.01" min="0.01" placeholder="Stock weight">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="alarm_weight" class="form-label">Alarm Weight (kg)</label>
                                    <input type="number" class="form-control" name="alarm_weight" id="alarm_weight"
                                        step="0.01" min="0.01" placeholder="Alarm weight" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="weight_unit" class="form-label">Weight Unit (kg)</label>
                                    <input type="number" class="form-control" name="weight_unit" id="weight_unit"
                                        step="0.01" min="0.01" placeholder="Weight unit" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="price_per_weight_unit" class="form-label">Price Per Weight Unit</label>
                                    <input type="number" class="form-control" name="price_per_weight_unit"
                                        id="price_per_weight_unit" step="0.01" min="0.01"
                                        placeholder="Price per weight unit" required>
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

            $('#refill-form').validate({
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

            $('select[name="refills[0][ingredient_id]"]').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                dropdownParent: $('#refillStockModal .modal-content'),
                placeholder: 'Select ingredient',
                ajax: {
                    url: "{{ route('admin.ingredient.select_search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search_term: params.term,
                            page: params.page
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data.results, function(item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    }
                }
            });

            $('#ingredient_category_id').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                dropdownParent: $('#addIngredientModal .modal-content'),
                placeholder: 'Select ingredient category',

                ajax: {
                    url: "{{ route('admin.ingredient_category.select_search') }}",
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

        let refillIndex = 1;

        $(document).on('click', '.remove-refill-group', function() {
            $(this).closest('.refill-group').remove();
        });

        $('#add-refill-btn').on('click', function() {
            const newGroup = `
                <div class="refill-group mb-3 pb-3 position-relative bg-light p-3 rounded">
                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" class="btn btn-outline-danger remove-refill-group">
                            <i class="fa fa-trash"></i> Remove
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <div class="form-group">
                                <label class="form-label" for="refill-ingredient-${refillIndex}">Ingredient</label>
                                <select class="form-select ingredient-select" id="refill-ingredient-${refillIndex}" name="refills[${refillIndex}][ingredient_id]" required></select>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label" for="refill-quantity-${refillIndex}">Quantity</label>
                                <input type="number" class="form-control" id="refill-quantity-${refillIndex}" name="refills[${refillIndex}][quantity]" step="1" min="1" placeholder="Quantity">
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label" for="refill-weight-${refillIndex}">Weight (kg)</label>
                                <input type="number" class="form-control" id="refill-weight-${refillIndex}" name="refills[${refillIndex}][weight]" step="0.01" min="0.01" placeholder="Weight" required>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#refill-container').append(newGroup);

            $(`select[name="refills[${refillIndex}][ingredient_id]"]`).select2({
                theme: 'bootstrap-5',
                allowClear: true,
                dropdownParent: $('#refillStockModal .modal-content'),
                placeholder: 'Select ingredient',
                ajax: {
                    url: "{{ route('admin.ingredient.select_search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search_term: params.term,
                            page: params.page
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data.results, function(item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    }
                }
            });

            refillIndex++;

        });
    </script>
@endsection
