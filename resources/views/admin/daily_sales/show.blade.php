@php
    use Carbon\Carbon;
@endphp

@extends('admin.layout.layout')

@section('page_title', 'Daily Sales Detail')

@section('content')

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">
                Daily Sales Detail -
                {{ Carbon::parse($dailySales->created_at)->format('d M Y') }}
                <span class="fs-6 text-muted">
                    {{ Carbon::parse($dailySales->created_at)->format('h:i A') }}
                </span>
            </h2>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <a href="{{ route('admin.daily_sales.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>

                <a href="{{ route('admin.daily_sales.edit', ['id' => $dailySales->id]) }}" class="btn btn-warning">
                    <i class="fa-solid fa-edit"></i> Edit
                </a>
            </div>
        </div>
    </div>

    @php
        $totalQuantity = 0;
        $totalAmount = 0;

        $products = $dailySalesItems->where('item_type', 'product');
        $addons = $dailySalesItems->where('item_type', 'addon');
    @endphp

    <!-- Sales Table -->
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th style="width: 40%">Name</th>
                <th style="width: 20%" class="text-end">Quantity</th>
                <th style="width: 20%" class="text-end">Price</th>
                <th style="width: 20%" class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            @if ($products->count() > 0)
                <tr class="table-secondary">
                    <td colspan="4" class="fw-bold">Products</td>
                </tr>
                @foreach ($products as $item)
                    @php
                        $totalQuantity += $item->quantity;
                        $totalAmount += $item->amount;
                    @endphp
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">{{ number_format($item->price, 2) }}</td>
                        <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            @endif

            @if ($addons->count() > 0)
                <tr class="table-secondary">
                    <td colspan="4" class="fw-bold">Add-ons</td>
                </tr>
                @foreach ($addons as $item)
                    @php
                        $totalQuantity += $item->quantity;
                        $totalAmount += $item->amount;
                    @endphp
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">{{ number_format($item->price, 2) }}</td>
                        <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            @endif

            <tr class="table-warning fw-bold">
                <td class="text-end">TOTAL</td>
                <td class="text-end">{{ $totalQuantity }}</td>
                <td colspan="2" class="text-end">{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="mt-4 mb-3">
        <h2 class="fw-bold">Ingredient Usage</h2>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Ingredient</th>
                <th class="text-end">Total Used (kg)</th>
                <th class="text-end">Amount (RM)</th>
            </tr>
        </thead>
        <tbody>
            @if (count($ingredientUsage['ingredients']) > 0)
                @foreach ($ingredientUsage['ingredients'] as $ingredient)
                    <tr>
                        <td>{{ $ingredient['name'] }}</td>
                        <td class="text-end">{{ number_format($ingredient['weight'], 2) }}</td>
                        <td class="text-end">{{ number_format($ingredient['amount'], 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="text-center">No ingredient usage data.</td>
                </tr>
            @endif

            <tr class="table-warning fw-bold">
                <td colspan="2" class="text-end">Total Ingredient Cost</td>
                <td class="text-end">{{ number_format($ingredientUsage['total_amount'], 2) }}</td>
            </tr>
        </tbody>
    </table>

@endsection
