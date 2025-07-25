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

    <form id="form" action="{{ route('admin.daily_sales.store') }}" method="POST">
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
                                data-type="product" data-id="{{ $product->id }}">
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
                                name="addons[{{ $addon->id }}][quantity]" min="0" value="0" data-type="addon"
                                data-id="{{ $addon->id }}">
                        </td>
                        <td class="text-end">{{ number_format($addon->price, 2) }}</td>
                        <td class="text-end subtotal">0.00</td>
                    </tr>
                @endforeach

                <tr class="table-warning fw-bold">
                    <td class="text-end">TOTAL</td>
                    <td class="text-end" id="total-quantity">0</td>
                    <td colspan="2" class="text-end" id="total-amount">0.00</td>
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
                    <th>Current</th>
                    <th>Used</th>
                    <th>Remaining</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4">No ingredient usage yet. Adjust quantities above to preview.</td>
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

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('input', function() {
                calculateTotals();
                updateIngredientPreview();
            });
        });

        function calculateTotals() {
            let totalQuantity = 0;
            let totalAmount = 0;

            document.querySelectorAll('tbody tr').forEach(row => {
                const qtyInput = row.querySelector('.quantity-input');
                const priceCell = row.querySelector('td.text-end');
                const subtotalCell = row.querySelector('.subtotal');

                if (qtyInput && priceCell && subtotalCell) {
                    const qty = parseFloat(qtyInput.value) || 0;
                    const price = parseFloat(priceCell.textContent.replace(/,/g, '')) || 0;
                    const subtotal = qty * price;

                    subtotalCell.textContent = subtotal.toFixed(2);
                    totalQuantity += qty;
                    totalAmount += subtotal;
                }
            });

            document.getElementById('total-quantity').textContent = totalQuantity;
            document.getElementById('total-amount').textContent = totalAmount.toFixed(2);
        }

        function updateIngredientPreview() {
            let usage = {};

            document.querySelectorAll('.quantity-input').forEach(input => {
                let qty = parseInt(input.value) || 0;
                let type = input.dataset.type;
                let id = input.dataset.id;

                if (qty > 0) {
                    let item = ingredientData[type + 's'].find(i => i.id == id);
                    if (item && item.ingredients) {
                        item.ingredients.forEach(i => {
                            let ingId = i.ingredient.id;
                            if (!usage[ingId]) {
                                usage[ingId] = {
                                    name: i.ingredient.name,
                                    current: parseFloat(i.ingredient.weight) || 0, // 强制转数字
                                    used: 0
                                };
                            }
                            usage[ingId].used += (parseFloat(i.weight) || 0) * qty; // 强制转数字
                        });
                    }
                }
            });

            let tbody = document.querySelector('#ingredient-preview tbody');
            tbody.innerHTML = '';

            if (Object.keys(usage).length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="4">No ingredient usage yet. Adjust quantities above to preview.</td></tr>';
            } else {
                Object.values(usage).forEach(ing => {
                    let remaining = ing.current - ing.used;
                    let row = `<tr ${remaining <= 0 ? 'class="table-danger"' : ''}>
                        <td>${ing.name}</td>
                        <td>${ing.current.toFixed(2)} kg</td>
                        <td>${ing.used.toFixed(2)} kg</td>
                        <td>${remaining.toFixed(2)} kg</td>
                    </tr>`;
                    tbody.innerHTML += row;
                });
            }
        }
    </script>
@endsection
