@extends('admin.layout.layout')

@section('page_title', 'Ingredient Detail')

@section('content')

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Ingredient Detail</h2>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <a href="{{ route('admin.ingredient.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>

                @role('Superadmin')
                    <form action="{{ route('admin.ingredient.destroy', ['id' => $ingredient->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="deleteConfirmation(event)">
                            <i class="fa-solid fa-trash"></i> Delete
                        </button>
                    </form>

                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editIngredientModal">
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
                    <img src="{{ $ingredient->image ? asset('storage/ingredient/' . $ingredient->image) : asset('img/default-image.png') }}"
                        onerror="this.onerror=null;this.src='{{ asset('img/default-image.png') }}'" alt="Ingredient Image"
                        style="width: 200px; height: 200px; object-fit: cover; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); display: block; margin: 0 auto;">
                </div>

                <div class="col-12">
                    <div class="fw-bold">
                        Ingredient Category:
                    </div>
                    <div>
                        <a class="text-decoration-none"
                            href="{{ route('admin.ingredient_category.show', ['id' => $ingredient->ingredient_category_id]) }}"><span
                                class="badge rounded-pill text-bg-warning">{{ $ingredient->ingredientCategory->name }}</span></a>
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
                        {{ $ingredient->name }}
                    </div>
                </div>

                <div class="col-12">
                    <hr class="text-muted">
                </div>

                <div class="col-12">
                    <div class="fw-bold">
                        Stock: @if ($ingredient->stock !== null && $ingredient->min_stock !== null && $ingredient->stock <= $ingredient->min_stock)
                            <span class="badge bg-danger mt-1">
                                Low stock
                            </span>
                        @endif
                    </div>

                    <div>
                        @if ($ingredient->unit_type === 'weight')
                            {{ floatval(sprintf('%.3f', $ingredient->stock)) }} kg
                        @else
                            {{ $ingredient->stock / $ingredient->weight_unit }} qty
                        @endif
                    </div>
                </div>

                <div class="col-12">
                    <hr class="text-muted">
                </div>

                <div class="col-12">
                    <div class="fw-bold">
                        Minimum Stock:
                    </div>

                    <div>
                        @if ($ingredient->unit_type === 'weight')
                            {{ floatval(sprintf('%.3f', $ingredient->min_stock)) }} kg
                        @else
                            {{ $ingredient->min_stock / $ingredient->weight_unit }} qty
                        @endif
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
                        RM {{ $ingredient->price }}
                        @if ($ingredient->unit_type === 'weight')
                            / {{ floatval(sprintf('%.3f', $ingredient->weight_unit)) }} kg
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 mb-3">
        <div class="col">
            <h2 class="fw-bold">Refill Stock History</h2>
        </div>
    </div>

    @livewire('admin.ingredient.refill-stock-history-list', ['ingredientId' => $ingredient->id])

    <!-- Modal for Edit Ingredient Detail -->
    <div class="modal fade" id="editIngredientModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Ingredient Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="form" action="{{ route('admin.ingredient.update', ['id' => $ingredient->id]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-12">
                                <div class="default-image-wrapper mb-3">
                                    <img id="image-display"
                                        src="{{ $ingredient->image ? asset('storage/ingredient/' . $ingredient->image) : asset('img/default-image.png') }}"
                                        data-initial-image="{{ asset('img/default-image.png') }}"
                                        onerror="this.onerror=null;this.src='{{ $ingredient->image ? asset('storage/ingredient/' . $ingredient->image) : asset('img/default-image.png') }}'"
                                        alt="Ingredient Image" width="150">
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
                                    <label for="ingredient_category_id" class="form-label">Ingredient Category<span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="ingredient_category_id" id="ingredient_category_id"
                                        style="width: 100%" required>
                                        <option value="{{ $ingredient->ingredient_category_id }}" selected>
                                            {{ $ingredient->ingredientCategory->name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ old('name', $ingredient->name) }}" placeholder="Name" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="min_stock" class="form-label"></label>
                                    <input type="number" class="form-control" name="min_stock" id="min_stock"
                                        value="{{ old('min_stock', $ingredient->unit_type === 'weight' ? $ingredient->min_stock : $ingredient->min_stock / $ingredient->weight_unit) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="weight_unit" class="form-label">Unit Per Weight (kg)<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="weight_unit" id="weight_unit"
                                        step="0.001" min="0.001"
                                        value="{{ old('weight_unit', $ingredient->weight_unit) }}"
                                        placeholder="Unit per weight (kg)" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="price" class="form-label"></label>
                                    <input type="number" class="form-control" name="price" id="price"
                                        step="0.01" min="0.01" value="{{ old('price', $ingredient->price) }}"
                                        required>
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

            $('#ingredient_category_id').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                dropdownParent: $('#editIngredientModal .modal-content'),
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

            const unitType = "{{ $ingredient->unit_type }}";

            const minStockInput = $('#min_stock');
            const priceInput = $('#price');

            function updateFields() {
                if (unitType === 'weight') {
                    $('label[for="min_stock"]').html(
                        'Minimum Stock (kg)<span class="text-danger">*</span>');
                    minStockInput.attr({
                        min: '0.001',
                        step: '0.001',
                        placeholder: 'Minimum stock (kg)'
                    });

                    $('label[for="price"]').html(
                        'Price per Weight Unit (RM)<span class="text-danger">*</span>');
                    priceInput.attr('placeholder', 'Price per weight unit (RM)');

                } else {
                    $('label[for="min_stock"]').html(
                        'Minimum Stock (qty)<span class="text-danger">*</span>');
                    minStockInput.attr({
                        min: '1',
                        step: '1',
                        placeholder: 'Minimum stock (qty)'
                    });

                    $('label[for="price"]').html(
                        'Price per Quantity (RM)<span class="text-danger">*</span>');
                    priceInput.attr('placeholder', 'Price per quantity (RM)');
                }
            }

            updateFields();
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
