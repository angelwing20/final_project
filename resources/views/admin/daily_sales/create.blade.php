@extends('admin.layout.layout')

@section('page_title', 'Add Daily Sales')

@section('content')
    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Add Daily Sales</h2>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <a href="{{ route('admin.daily_sales.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <form id="dailySalesForm" action="{{ route('admin.daily_sales.store') }}" method="POST">
        @csrf

        <div class="d-flex align-items-center gap-2 gap-sm-3 mb-3">
            <label for="date" class="fw-bold">Date<span class="text-danger">*</span></label>
            <input type="date" name="date" id="date" class="form-control"
                value="{{ old('date', date('Y-m-d')) }}" required>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" style="white-space: nowrap">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 40%">Name</th>
                        <th style="width: 20%" class="text-end">Quantity<span class="text-danger">*</span></th>
                        <th style="width: 20%" class="text-end">Price (RM)</th>
                        <th style="width: 20%" class="text-end">Subtotal (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-secondary">
                        <td colspan="4" class="fw-bold">Foods</td>
                    </tr>
                    @foreach ($foods as $food)
                        <tr>
                            <td>{{ $food->name }}</td>
                            <td>
                                <input type="number" class="form-control quantity-input"
                                    name="foods[{{ $food->id }}][quantity]" min="0"
                                    value="{{ old('foods.' . $food->id . '.quantity', 0) }}" data-item-type="food"
                                    data-item-id="{{ $food->id }}">
                            </td>
                            <td class="text-end">{{ number_format($food->price, 2) }}</td>
                            <td class="text-end subtotal">0.00</td>
                        </tr>
                    @endforeach

                    <tr class="table-secondary">
                        <td colspan="4" class="fw-bold">Add-ons</td>
                    </tr>
                    @foreach ($addons as $addon)
                        <tr>
                            <td>{{ $addon->name }}</td>
                            <td>
                                <input type="number" class="form-control quantity-input"
                                    name="addons[{{ $addon->id }}][quantity]" min="0"
                                    value="{{ old('addons.' . $addon->id . '.quantity', 0) }}" data-item-type="addon"
                                    data-item-id="{{ $addon->id }}">
                            </td>
                            <td class="text-end">{{ number_format($addon->price, 2) }}</td>
                            <td class="text-end subtotal">0.00</td>
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
                        <th style="width: 15%" class="text-end">Current Stock</th>
                        <th style="width: 15%" class="text-end">Consumption</th>
                        <th style="width: 15%" class="text-end">Remaining Stock</th>
                        <th style="width: 15%" class="text-end">Cost (RM)</th>
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
            <button type="submit" class="btn btn-warning">Submit</button>
        </div>
    </form>
@endsection

@section('script')
    <script>
        $('#dailySalesForm').validate({
            ignore: [],
            errorElement: 'span',
            errorClass: 'invalid-feedback',
            errorPlacement: function(error, element) {
                element.closest('td').append(error);
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
                    notifier.show('Error!', 'Please ensure all inputs are correct.', 'warning', '', 4000);
                }
            },
            rules: {
                @foreach ($foods as $food)
                    "foods[{{ $food->id }}][quantity]": {
                        required: true,
                        digits: true,
                        min: 0
                    },
                @endforeach
                @foreach ($addons as $addon)
                    "addons[{{ $addon->id }}][quantity]": {
                        required: true,
                        digits: true,
                        min: 0
                    },
                @endforeach
            },
        });

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

            document.querySelectorAll('tbody tr').forEach(row => {
                const quantityInput = row.querySelector('.quantity-input');
                const priceCell = row.querySelector('td.text-end');
                const subtotalCell = row.querySelector('.subtotal');

                if (quantityInput && priceCell && subtotalCell) {
                    const quantity = parseFloat(quantityInput.value) || 0;
                    const price = parseFloat(priceCell.textContent.replace(/,/g, '')) || 0;
                    const subtotal = quantity * price;

                    subtotalCell.textContent = subtotal.toFixed(2);
                    totalQuantity += quantity;
                    totalAmount += subtotal;
                }
            });

            document.getElementById('total-quantity').textContent = totalQuantity;
            document.getElementById('total-amount').textContent = totalAmount.toFixed(2);
        }

        function updateIngredientPreview() {
            let ingredientConsumption = {};
            let tableBody = document.querySelector('#ingredient-preview-body');
            let totalCost = 0;

            tableBody.innerHTML = '';

            document.querySelectorAll('.quantity-input').forEach(function(inputElement) {
                let quantity = parseInt(inputElement.value) || 0;
                let itemType = inputElement.dataset.itemType;
                let itemId = inputElement.dataset.itemId;

                if (quantity > 0) {
                    let item = ingredientData[itemType + 's'].find(function(i) {
                        return i.id == itemId;
                    });

                    if (item && item.ingredients) {
                        item.ingredients.forEach(function(recipeIngredient) {
                            let ingredient = recipeIngredient.ingredient;
                            let ingredientId = ingredient.id;
                            let ingredientName = ingredient.name;
                            let unitType = ingredient.unit_type;
                            let weightUnit = parseFloat(ingredient.weight_unit) || 1;
                            let price = parseFloat(ingredient.price) || 0;

                            let currentStockWeight = parseFloat(ingredient.stock) || 0;
                            let consumptionWeight = (parseFloat(recipeIngredient.consumption) || 0) *
                                quantity;

                            let currentStock = unitType === 'quantity' ? currentStockWeight / weightUnit :
                                currentStockWeight;
                            let consumption = unitType === 'quantity' ? consumptionWeight / weightUnit :
                                consumptionWeight;

                            let costPerUnit = unitType === 'weight' ? price / weightUnit : price;
                            let cost = consumption * costPerUnit;

                            if (!ingredientConsumption[ingredientId]) {
                                ingredientConsumption[ingredientId] = {
                                    name: ingredientName,
                                    currentStock: currentStock,
                                    used: 0,
                                    unitType: unitType,
                                    cost: 0
                                };
                            }

                            ingredientConsumption[ingredientId].used += consumption;
                            ingredientConsumption[ingredientId].cost += cost;
                            totalCost += cost;
                        });
                    }
                }
            });

            if (Object.keys(ingredientConsumption).length === 0) {
                tableBody.innerHTML =
                    '<tr><td colspan="5" class="text-center text-muted">No ingredient consumption yet. Adjust quantities above to preview.</td></tr>';
                return;
            }

            let sortedIngredients = Object.values(ingredientConsumption).sort((a, b) => a.name.localeCompare(b.name));
            sortedIngredients.forEach(function(ingredient) {
                let remainingStock = ingredient.currentStock - ingredient.used;
                let rowClass = '';
                let remainingClass = '';
                let unitLabel = ingredient.unitType === 'quantity' ? ' qty' : ' kg';

                let EPSILON = 0.00001;

                if (remainingStock < -EPSILON) {
                    rowClass = 'table-danger';
                    remainingClass = 'text-danger fw-bold';
                } else if (Math.abs(remainingStock) < EPSILON) {
                    rowClass = 'table-warning';
                }

                let row = `
                <tr class="${rowClass}">
                    <td>${ingredient.name}</td>
                    <td class="text-end">${removeTrailingZeros(ingredient.currentStock)}${unitLabel}</td>
                    <td class="text-end">-${removeTrailingZeros(ingredient.used)}${unitLabel}</td>
                    <td class="text-end ${remainingClass}">${removeTrailingZeros(remainingStock)}${unitLabel}</td>
                    <td class="text-end">${ingredient.cost.toFixed(2)}</td>
                </tr>
                `;
                tableBody.innerHTML += row;
            });

            tableBody.innerHTML += `
                <tr class="table-warning fw-bold">
                    <td class="text-end">TOTAL</td>
                    <td colspan="4" class="text-end">RM ${totalCost.toFixed(2)}</td>
                </tr>
            `;
        }


        function removeTrailingZeros(value) {
            return value % 1 === 0 ? parseInt(value) : parseFloat(value.toFixed(2));
        }
    </script>
@endsection
