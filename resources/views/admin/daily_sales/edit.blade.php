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
                    @php
                        $currentItem = $dailySalesItems
                            ->where('item_type', 'product')
                            ->where('item_id', $product->id)
                            ->first();
                        $qty = $currentItem ? $currentItem->quantity : 0;
                    @endphp
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>
                            <input type="number" class="form-control quantity-input"
                                name="products[{{ $product->id }}][quantity]" min="0" value="{{ $qty }}"
                                data-type="product" data-id="{{ $product->id }}" data-old="{{ $qty }}">
                        </td>
                        <td class="text-end">
                            {{ number_format($product->price, 2) }}
                            <input type="hidden" name="products[{{ $product->id }}][price]" value="{{ $product->price }}">
                        </td>
                        <td class="text-end subtotal">{{ number_format($qty * $product->price, 2) }}</td>
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
                        $qty = $currentItem ? $currentItem->quantity : 0;
                    @endphp
                    <tr>
                        <td>{{ $addon->name }}</td>
                        <td>
                            <input type="number" class="form-control quantity-input"
                                name="addons[{{ $addon->id }}][quantity]" min="0" value="{{ $qty }}"
                                data-type="addon" data-id="{{ $addon->id }}" data-old="{{ $qty }}">
                        </td>
                        <td class="text-end">
                            {{ number_format($addon->price, 2) }}
                            <input type="hidden" name="addons[{{ $addon->id }}][price]" value="{{ $addon->price }}">
                        </td>
                        <td class="text-end subtotal">{{ number_format($qty * $addon->price, 2) }}</td>
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
                    <th>Change</th>
                    <th>After Edit</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4">No ingredient usage yet. Adjust quantities above to preview.</td>
                </tr>
            </tbody>
        </table>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-warning">Update</button>
        </div>
    </form>
@endsection

@section('script')
    <script>
        let ingredientData = @json([
            'products' => $products,
            'addons' => $addons,
        ]);

        calculateTotals();
        updateIngredientPreview();

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('quantity-input')) {
                if (parseInt(e.target.value) < 0) e.target.value = 0;
                calculateTotals();
                updateIngredientPreview();
            }
        });

        function calculateTotals() {
            let totalQuantity = 0;
            let totalAmount = 0;

            document.querySelectorAll('.quantity-input').forEach(input => {
                const qty = parseFloat(input.value) || 0;
                const price = parseFloat(input.closest('tr').querySelector('input[type="hidden"]').value) || 0;
                const subtotalCell = input.closest('tr').querySelector('.subtotal');

                const subtotal = qty * price;
                subtotalCell.textContent = subtotal.toFixed(2);

                totalQuantity += qty;
                totalAmount += subtotal;
            });

            document.getElementById('total-quantity').textContent = totalQuantity;
            document.getElementById('total-amount').textContent = totalAmount.toFixed(2);
        }

        function updateIngredientPreview() {
            let usage = {};

            document.querySelectorAll('.quantity-input').forEach(input => {
                const newQty = parseInt(input.value) || 0;
                const oldQty = parseInt(input.dataset.old) || 0;
                const diffQty = newQty - oldQty;
                const type = input.dataset.type;
                const id = input.dataset.id;

                if (diffQty !== 0) {
                    const item = ingredientData[type + 's'].find(i => i.id == id);
                    if (item && item.ingredients) {
                        item.ingredients.forEach(i => {
                            const ingId = i.ingredient.id;
                            if (!usage[ingId]) {
                                usage[ingId] = {
                                    name: i.ingredient.name,
                                    current: parseFloat(i.ingredient.weight) || 0,
                                    change: 0
                                };
                            }
                            usage[ingId].change -= (parseFloat(i.weight) || 0) * diffQty;
                        });
                    }
                }
            });

            const tbody = document.querySelector('#ingredient-preview tbody');
            tbody.innerHTML = '';

            if (Object.keys(usage).length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="4">No ingredient usage yet. Adjust quantities above to preview.</td></tr>';
                return;
            }

            const sortedUsage = Object.values(usage).sort((a, b) => a.name.localeCompare(b.name));

            sortedUsage.forEach(ing => {
                const afterEdit = ing.current + ing.change;

                const changeClass = ing.change < 0 ? 'text-danger fw-semibold' : (ing.change > 0 ?
                    'text-success fw-semibold' : '');
                const afterClass = afterEdit < 0 ? 'table-danger fw-semibold' : '';

                const row = `
                <tr class="${afterClass}">
                    <td>${ing.name}</td>
                    <td>${ing.current.toFixed(2)} kg</td>
                    <td class="${changeClass}">${ing.change > 0 ? '+' : ''}${ing.change.toFixed(2)} kg</td>
                    <td>${afterEdit.toFixed(2)} kg</td>
                </tr>
            `;
                tbody.innerHTML += row;
            });
        }
    </script>
@endsection
