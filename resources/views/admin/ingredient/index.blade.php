@extends('admin.layout.layout')

@section('page_title', 'Ingredient Stock')

@section('content')

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Ingredient Stock</h2>
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

                                    <div class="col-6 quantity-col">
                                        <div class="form-group">
                                            <label class="form-label" for="refill-quantity-0">Quantity</label>
                                            <input type="number" class="form-control" id="refill-quantity-0"
                                                name="refills[0][quantity]" step="1" min="1" value="1"
                                                placeholder="Quantity" required>
                                        </div>
                                    </div>

                                    <div class="col-6 weight-col">
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

    <!-- Modal for Add Ingredient Stock -->
    <div class="modal fade" id="addIngredientModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Ingredient Stock</h5>
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
                                        style="width: 100%" required></select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ old('name') }}" placeholder="Name" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="unit_type" class="form-label">Unit Type</label>
                                    <select class="form-select" name="unit_type" id="unit_type" required>
                                        <option value="weight" {{ old('unit_type') === 'weight' ? 'selected' : '' }}>Weight
                                            (kg)</option>
                                        <option value="quantity" {{ old('unit_type') === 'quantity' ? 'selected' : '' }}>
                                            Quantity (qty)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="stock" class="form-label"></label>
                                    <input type="number" class="form-control" name="stock" id="stock"
                                        value="{{ old('stock') }}">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="min_stock" class="form-label"></label>
                                    <input type="number" class="form-control" name="min_stock" id="min_stock"
                                        value="{{ old('min_stock') }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="weight_unit" class="form-label">Weight Unit (kg)</label>
                                    <input type="number" class="form-control" name="weight_unit" id="weight_unit"
                                        step="0.01" min="0.01" value="{{ old('weight_unit') }}"
                                        placeholder="Weight unit (kg)" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="price" class="form-label"></label>
                                    <input type="number" class="form-control" name="price" id="price"
                                        value="{{ old('price') }}" step="0.01" min="0.01" required>
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
            $('#form, #refill-form').each(function() {
                $(this).validate({
                    ignore: [],
                    errorElement: 'span',
                    errorClass: 'invalid-feedback',
                    errorPlacement: function(error, element) {
                        element.closest('.form-group').append(error);
                    },
                    highlight: function(element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                    },
                    invalidHandler: function(form, validator) {
                        if (validator.numberOfInvalids()) {
                            notifier.show('Error!', 'Please ensure all inputs are correct.',
                                'warning', '', 4000);
                        }
                    },
                });
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

            const unitTypeSelect = $('#unit_type');
            const stockInput = $('#stock');
            const minStockInput = $('#min_stock');
            const priceInput = $('#price');

            function updateFields() {
                const type = unitTypeSelect.val();

                if (type === 'weight') {
                    $('label[for="stock"]').text('Current Stock (kg)');
                    stockInput.attr({
                        min: '0.01',
                        step: '0.01',
                        placeholder: 'Current stock (kg)'
                    });

                    $('label[for="min_stock"]').text('Minimum Stock (kg)');
                    minStockInput.attr({
                        min: '0.01',
                        step: '0.01',
                        placeholder: 'Minimum stock (kg)'
                    });

                    $('label[for="price"]').text('Price per Weight Unit (RM)');
                    priceInput.attr('placeholder', 'Price per weight unit (RM)');

                } else {
                    $('label[for="stock"]').text('Current Stock (qty)');
                    stockInput.attr({
                        min: '1',
                        step: '1',
                        placeholder: 'Current stock (qty)'
                    });

                    $('label[for="min_stock"]').text('Minimum Stock (qty)');
                    minStockInput.attr({
                        min: '1',
                        step: '1',
                        placeholder: 'Minimum stock (qty)'
                    });

                    $('label[for="price"]').text('Price per Quantity (RM)');
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

            initIngredientSelect($('select[name="refills[0][ingredient_id]"]'));
            let refillIndex = 1;

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
                        <div class="col-6 quantity-col">
                            <div class="form-group">
                                <label class="form-label" for="refill-quantity-${refillIndex}">Quantity</label>
                                <input type="number" class="form-control" id="refill-quantity-${refillIndex}" name="refills[${refillIndex}][quantity]" step="1" min="1" value="1" placeholder="Quantity" required>
                            </div>
                        </div>
                        <div class="col-6 weight-col">
                            <div class="form-group">
                                <label class="form-label" for="refill-weight-${refillIndex}">Weight (kg)</label>
                                <input type="number" class="form-control" id="refill-weight-${refillIndex}" name="refills[${refillIndex}][weight]" step="0.01" min="0.01" placeholder="Weight" required>
                            </div>
                        </div>
                    </div>
                </div>`;
                $('#refill-container').append(newGroup);
                initIngredientSelect($(`select[name="refills[${refillIndex}][ingredient_id]"]`));
                refillIndex++;
            });

            $(document).on('click', '.remove-refill-group', function() {
                $(this).closest('.refill-group').remove();
            });

            function initIngredientSelect($element) {
                $element.select2({
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
                                        id: item.id,
                                        data: {
                                            unit_type: item.unit_type,
                                            weight_unit: item.weight_unit
                                        }
                                    };
                                }),
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        }
                    }
                });

                $element.on('select2:select', function(e) {
                    var selectedData = e.params.data.data;
                    var row = $(this).closest(".refill-group");
                    var weightCol = row.find('.weight-col');
                    var quantityCol = row.find('.quantity-col');
                    var weightInput = weightCol.find('input[name^="refills"][name$="[weight]"]');

                    if (selectedData.unit_type === 'quantity') {
                        weightCol.addClass('d-none');
                        quantityCol.removeClass('col-6').addClass('col-12');
                        weightInput.prop('required', false).val('');
                    } else {
                        weightCol.removeClass('d-none');
                        quantityCol.removeClass('col-12').addClass('col-6');
                        weightInput.prop('required', true);
                    }
                });
            }
        });

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
