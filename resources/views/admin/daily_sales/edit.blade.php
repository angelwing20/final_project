@extends('admin.layout.layout')

@section('page_title', 'Edit Daily Sales Detail')

@section('content')
    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Edit Daily Sales Detail</h2>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <a href="{{ route('admin.daily_sales.show', ['id' => $dailySales->id]) }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <form id="form" action="{{ route('admin.daily_sales.update', ['id' => $dailySales->id]) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="table-responsive">
            <table class="table table-bordered" style="white-space: nowrap">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 40%">Name</th>
                        <th style="width: 20%">Quantity</th>
                        <th style="width: 20%">Price (RM)</th>
                        <th style="width: 20%">Subtotal (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-secondary">
                        <td colspan="4" class="fw-bold">Foods</td>
                    </tr>
                    @foreach ($foods as $food)
                        @php
                            $currentItem = $dailySalesItems
                                ->where('item_type', 'food')
                                ->where('item_id', $food->id)
                                ->first();
                            $quantity = $currentItem ? $currentItem->quantity : 0;
                        @endphp
                        <tr>
                            <td>{{ $food->name }}</td>
                            <td>
                                <input type="number" class="form-control quantity-input"
                                    name="foods[{{ $food->id }}][quantity]" min="0"
                                    value="{{ old('foods.' . $food->id . '.quantity', $quantity) }}" data-type="food"
                                    data-id="{{ $food->id }}" data-old="{{ $quantity }}">
                                <input type="hidden" name="foods[{{ $food->id }}][price]" value="{{ $food->price }}">
                            </td>
                            <td class="text-end">{{ number_format($food->price, 2) }}</td>
                            <td class="text-end subtotal">{{ number_format($quantity * $food->price, 2) }}</td>
                        </tr>
                    @endforeach

                    <tr class="table-secondary">
                        <td colspan="4" class="fw-bold">Add-ons</td>
                    </tr>
                    @foreach ($addons as $addon)
                        @php
                            $currentItem = $dailySalesItems
                                ->where('item_type', 'addon')
                                ->where('item_id', $addon->id)
                                ->first();
                            $quantity = $currentItem ? $currentItem->quantity : 0;
                        @endphp
                        <tr>
                            <td>{{ $addon->name }}</td>
                            <td>
                                <input type="number" class="form-control quantity-input"
                                    name="addons[{{ $addon->id }}][quantity]" min="0"
                                    value="{{ old('addons.' . $addon->id . '.quantity', $quantity) }}" data-type="addon"
                                    data-id="{{ $addon->id }}" data-old="{{ $quantity }}">
                                <input type="hidden" name="addons[{{ $addon->id }}][price]"
                                    value="{{ $addon->price }}">
                            </td>
                            <td class="text-end">{{ number_format($addon->price, 2) }}</td>
                            <td class="text-end subtotal">{{ number_format($quantity * $addon->price, 2) }}</td>
                        </tr>
                    @endforeach

                    <tr class="table-warning fw-bold">
                        <td class="text-end">TOTAL</td>
                        <td class="text-end" id="total-quantity">0</td>
                        <td colspan="2" class="text-end">RM <span id="total-amount">0.00</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4 mb-3">
            <h2 class="fw-bold">Ingredient Consumption Preview</h2>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="ingredient-preview" style="white-space: nowrap">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 40%">Ingredient</th>
                        <th style="width: 20%">Current Stock</th>
                        <th style="width: 20%">Change</th>
                        <th style="width: 20%">After Edit</th>
                    </tr>
                </thead>
                <tbody id="ingredient-preview-body">
                    <tr id="ingredient-loading-row">
                        <td colspan="4" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-warning">Update</button>
        </div>
    </form>
@endsection

@section('script')
    <script>
        $('#form').validate({
            ignore: [],
            errorElement: 'span',
            errorClass: 'invalid-feedback',
            errorPlacement: function(error, element) {
                element.closest('td').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            },
            invalidHandler: function(form, validator) {
                if (validator.numberOfInvalids()) {
                    notifier.show('Error!', 'Please ensure all inputs are correct.', 'warning', '', 4000);
                }
            },
            rules: (function() {
                let dynamicRules = {};
                $('.quantity-input').each(function() {
                    dynamicRules[$(this).attr('name')] = {
                        required: true,
                        digits: true,
                        min: 0
                    };
                });
                return dynamicRules;
            })()
        });

        const EPSILON = 0.00001;
        let ingredientData = @json([
            'foods' => $foods,
            'addons' => $addons,
        ]);

        document.addEventListener('DOMContentLoaded', function() {
            calculateTotals();
            updateIngredientPreview();

            document.querySelectorAll('.quantity-input').forEach(function(input) {
                input.addEventListener('input', function() {
                    if (parseInt(this.value) < 0) this.value = 0;
                    calculateTotals();
                    updateIngredientPreview();
                });
            });
        });

        function calculateTotals() {
            let totalQuantity = 0;
            let totalAmount = 0;

            document.querySelectorAll('.quantity-input').forEach(input => {
                const quantity = parseFloat(input.value) || 0;
                const price = parseFloat(input.closest('tr').querySelector('input[type="hidden"]').value) || 0;
                const subtotalCell = input.closest('tr').querySelector('.subtotal');

                const subtotal = quantity * price;
                subtotalCell.textContent = subtotal.toFixed(2);

                totalQuantity += quantity;
                totalAmount += subtotal;
            });

            document.getElementById('total-quantity').textContent = totalQuantity;
            document.getElementById('total-amount').textContent = totalAmount.toFixed(2);
        }

        function updateIngredientPreview() {
            let ingredientConsumption = {};
            let tableBody = document.querySelector('#ingredient-preview-body');
            tableBody.innerHTML = '';

            document.querySelectorAll('.quantity-input').forEach(function(input) {
                const newQuantity = parseInt(input.value) || 0;
                const oldQuantity = parseInt(input.dataset.old) || 0;
                const quantityDifference = newQuantity - oldQuantity;
                const type = input.dataset.type;
                const id = input.dataset.id;

                if (quantityDifference !== 0) {
                    const item = ingredientData[type + 's'].find(function(i) {
                        return i.id == id;
                    });

                    if (item && item.ingredients) {
                        item.ingredients.forEach(function(i) {
                            const ingredientId = i.ingredient.id;
                            const ingredientName = i.ingredient.name;
                            const unitType = i.ingredient.unit_type;
                            const weightUnit = parseFloat(i.ingredient.weight_unit) || 1;

                            let currentStock = parseFloat(i.ingredient.stock) || 0;
                            if (unitType === 'quantity') {
                                currentStock = currentStock / weightUnit;
                            }

                            let consumption = parseFloat(i.consumption) || 0;
                            let convertedConsumption = consumption;
                            if (unitType === 'quantity') {
                                convertedConsumption = consumption / weightUnit;
                            }

                            if (!ingredientConsumption[ingredientId]) {
                                ingredientConsumption[ingredientId] = {
                                    name: ingredientName,
                                    currentStock: currentStock,
                                    change: 0,
                                    unitType: unitType
                                };
                            }

                            ingredientConsumption[ingredientId].change -= convertedConsumption *
                                quantityDifference;
                        });
                    }
                }
            });

            if (Object.keys(ingredientConsumption).length === 0) {
                tableBody.innerHTML =
                    '<tr><td colspan="4" class="text-center text-muted">No ingredient consumption yet. Adjust quantities above to preview.</td></tr>';
                return;
            }

            const sortedConsumption = Object.values(ingredientConsumption).sort((a, b) => a.name.localeCompare(b.name));

            sortedConsumption.forEach(function(ingredient) {
                const afterEditStock = ingredient.currentStock + ingredient.change;
                const unitLabel = ingredient.unitType === 'quantity' ? ' qty' : ' kg';

                const changeClass = ingredient.change < 0 ? 'text-danger fw-semibold' :
                    (ingredient.change > 0 ? 'text-success fw-semibold' : '');

                let rowClass = '';
                if (afterEditStock < -EPSILON) {
                    rowClass = 'table-danger';
                } else if (Math.abs(afterEditStock) < EPSILON) {
                    rowClass = 'table-warning';
                }

                const afterEditTextClass = afterEditStock < -EPSILON ? 'text-danger fw-bold' : '';

                const row = `
            <tr class="${rowClass}">
                <td>${ingredient.name}</td>
                <td>${removeTrailingZeros(ingredient.currentStock)}${unitLabel}</td>
                <td class="${changeClass}">${ingredient.change > 0 ? '+' : ''}${removeTrailingZeros(ingredient.change)}${unitLabel}</td>
                <td class="${afterEditTextClass}">${removeTrailingZeros(afterEditStock)}${unitLabel}</td>
            </tr>
        `;
                tableBody.innerHTML += row;
            });
        }

        function removeTrailingZeros(value) {
            return value % 1 === 0 ? parseInt(value) : parseFloat(value.toFixed(2));
        }
    </script>
@endsection
