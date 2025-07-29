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

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th style="width: 40%">Name</th>
                    <th style="width: 20%">Quantity</th>
                    <th style="width: 20%">Price</th>
                    <th style="width: 20%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr class="table-secondary">
                    <td colspan="4" class="fw-bold">Products</td>
                </tr>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>
                            <input type="number" class="form-control quantity-input"
                                name="products[{{ $product->id }}][quantity]" min="0" value="0"
                                data-item-type="product" data-item-id="{{ $product->id }}">
                        </td>
                        <td class="text-end">{{ number_format($product->price, 2) }}</td>
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
                                name="addons[{{ $addon->id }}][quantity]" min="0" value="0"
                                data-item-type="addon" data-item-id="{{ $addon->id }}">
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

        <div class="mt-4 mb-3">
            <h2 class="fw-bold">Ingredient Usage Preview</h2>
        </div>

        <table class="table table-bordered" id="ingredient-preview">
            <thead class="table-light">
                <tr>
                    <th>Ingredient</th>
                    <th>Current Stock</th>
                    <th>Used</th>
                    <th>Remaining</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" class="text-center">No ingredient usage yet. Adjust quantities above to preview.</td>
                </tr>
            </tbody>
        </table>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-warning">Submit</button>
        </div>
    </form>
@endsection

@section('script')
    <script>
        let ingredientData = @json([
            'products' => $products,
            'addons' => $addons,
        ]);

        document.querySelectorAll('.quantity-input').forEach(function(inputElement) {
            inputElement.addEventListener('input', function() {
                calculateTotals();
                updateIngredientPreview();
            });
        });

        function calculateTotals() {
            let totalQuantity = 0;
            let totalAmount = 0;

            document.querySelectorAll('tbody tr').forEach(function(rowElement) {
                const quantityInput = rowElement.querySelector('.quantity-input');
                const priceCell = rowElement.querySelector('td.text-end');
                const subtotalCell = rowElement.querySelector('.subtotal');

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
            let ingredientUsage = {};

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
                            let ingredientId = recipeIngredient.ingredient.id;
                            let ingredientName = recipeIngredient.ingredient.name;
                            let currentStock = parseFloat(recipeIngredient.ingredient.stock_weight) || 0;
                            let usedWeight = (parseFloat(recipeIngredient.weight) || 0) * quantity;

                            if (!ingredientUsage[ingredientId]) {
                                ingredientUsage[ingredientId] = {
                                    name: ingredientName,
                                    currentStock: currentStock,
                                    used: 0
                                };
                            }

                            ingredientUsage[ingredientId].used += usedWeight;
                        });
                    }
                }
            });

            let tableBody = document.querySelector('#ingredient-preview tbody');
            tableBody.innerHTML = '';

            if (Object.keys(ingredientUsage).length === 0) {
                tableBody.innerHTML =
                    '<tr><td colspan="4" class="text-center">No ingredient usage yet. Adjust quantities above to preview.</td></tr>';
            } else {
                let sortedIngredients = Object.values(ingredientUsage).sort(function(a, b) {
                    return a.name.localeCompare(b.name);
                });

                sortedIngredients.forEach(function(ingredient) {
                    let remainingStock = ingredient.currentStock - ingredient.used;
                    let rowClass = '';
                    let remainingClass = '';

                    if (remainingStock < 0) {
                        rowClass = 'table-danger';
                        remainingClass = 'text-danger fw-bold';
                    } else if (remainingStock === 0) {
                        rowClass = 'table-warning';
                    }

                    let row = `
                    <tr class="${rowClass}">
                        <td>${ingredient.name}</td>
                        <td>${ingredient.currentStock.toFixed(2)} kg</td>
                        <td>${ingredient.used.toFixed(2)} kg</td>
                        <td class="${remainingClass}">${remainingStock.toFixed(2)} kg</td>
                    </tr>
                `;

                    tableBody.innerHTML += row;
                });
            }
        }
    </script>
@endsection
