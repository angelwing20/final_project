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
                {{-- Product Section --}}
                <tr class="table-secondary">
                    <td colspan="4" class="fw-bold">Products</td>
                </tr>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>
                            <input type="number" class="form-control quantity-input"
                                name="products[{{ $product->id }}][quantity]" min="0" value="0">
                        </td>
                        <td class="text-end">
                            {{ number_format($product->price, 2) }}
                            <input type="hidden" name="products[{{ $product->id }}][price]" value="{{ $product->price }}">
                        </td>
                        <td class="text-end subtotal">0.00</td>
                    </tr>
                @endforeach

                {{-- Add-on Section --}}
                <tr class="table-secondary">
                    <td colspan="4" class="fw-bold">Add-ons</td>
                </tr>
                @foreach ($addons as $addon)
                    <tr>
                        <td>{{ $addon->name }}</td>
                        <td>
                            <input type="number" class="form-control quantity-input"
                                name="addons[{{ $addon->id }}][quantity]" min="0" value="0">
                        </td>
                        <td class="text-end">
                            {{ number_format($addon->price, 2) }}
                            <input type="hidden" name="addons[{{ $addon->id }}][price]" value="{{ $addon->price }}">
                        </td>
                        <td class="text-end subtotal">0.00</td>
                    </tr>
                @endforeach

                {{-- Total Row --}}
                <tr class="table-warning fw-bold">
                    <td class="text-end">TOTAL</td>
                    <td class="text-end" id="total-quantity">0</td>
                    <td colspan="2" class="text-end" id="total-amount">0.00</td>
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
        function calculateTotals() {
            let totalQuantity = 0;
            let totalAmount = 0;

            document.querySelectorAll('tbody tr').forEach(row => {
                const qtyInput = row.querySelector('.quantity-input');
                const priceHidden = row.querySelector('input[type="hidden"]');
                const subtotalCell = row.querySelector('.subtotal');

                if (qtyInput && priceHidden && subtotalCell) {
                    const qty = parseFloat(qtyInput.value) || 0;
                    const price = parseFloat(priceHidden.value) || 0;
                    const subtotal = qty * price;

                    subtotalCell.textContent = subtotal.toFixed(2);

                    totalQuantity += qty;
                    totalAmount += subtotal;
                }
            });

            document.getElementById('total-quantity').textContent = totalQuantity;
            document.getElementById('total-amount').textContent = totalAmount.toFixed(2);
        }

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('quantity-input')) {
                calculateTotals();
            }
        });
    </script>
@endsection
