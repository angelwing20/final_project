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

        $productSalesItems = $dailySalesItems->where('item_type', 'product');
        $addonSalesItems = $dailySalesItems->where('item_type', 'addon');
    @endphp

    <div class="table-responsive">
        <table class="table table-bordered" style="white-space: nowrap">
            <thead class="table-dark">
                <tr>
                    <th style="width: 40%">Name</th>
                    <th style="width: 20%" class="text-end">Quantity</th>
                    <th style="width: 20%" class="text-end">Price (RM)</th>
                    <th style="width: 20%" class="text-end">Subtotal (RM)</th>
                </tr>
            </thead>
            <tbody>
                @if ($productSalesItems->isNotEmpty())
                    <tr class="table-secondary">
                        <td colspan="4" class="fw-bold">Products</td>
                    </tr>
                    @foreach ($productSalesItems as $salesItem)
                        @php
                            $totalQuantity += $salesItem->quantity;
                            $totalAmount += $salesItem->amount;
                        @endphp
                        <tr>
                            <td>{{ $salesItem->name }}</td>
                            <td class="text-end">{{ $salesItem->quantity }}</td>
                            <td class="text-end">{{ number_format($salesItem->price, 2) }}</td>
                            <td class="text-end">{{ number_format($salesItem->amount, 2) }}</td>
                        </tr>
                    @endforeach
                @endif

                @if ($addonSalesItems->isNotEmpty())
                    <tr class="table-secondary">
                        <td colspan="4" class="fw-bold">Add-ons</td>
                    </tr>
                    @foreach ($addonSalesItems as $salesItem)
                        @php
                            $totalQuantity += $salesItem->quantity;
                            $totalAmount += $salesItem->amount;
                        @endphp
                        <tr>
                            <td>{{ $salesItem->name }}</td>
                            <td class="text-end">{{ $salesItem->quantity }}</td>
                            <td class="text-end">{{ number_format($salesItem->price, 2) }}</td>
                            <td class="text-end">{{ number_format($salesItem->amount, 2) }}</td>
                        </tr>
                    @endforeach
                @endif

                <tr class="table-warning fw-bold">
                    <td class="text-end">TOTAL</td>
                    <td class="text-end">{{ $totalQuantity }}</td>
                    <td colspan="2" class="text-end">RM {{ number_format($totalAmount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-4 mb-3">
        <h2 class="fw-bold">Ingredient Usage</h2>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered" style="white-space: nowrap">
            <thead class="table-dark">
                <tr>
                    <th>Ingredient</th>
                    <th class="text-end">Weight (kg)</th>
                    <th class="text-end">Cost (RM)</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($ingredientUsage['ingredients']))
                    @foreach ($ingredientUsage['ingredients'] as $ingredientData)
                        <tr>
                            <td>{{ $ingredientData['name'] }}</td>
                            <td class="text-end">{{ number_format($ingredientData['weight'], 2) }}</td>
                            <td class="text-end">{{ number_format($ingredientData['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="text-center">No ingredient usage data.</td>
                    </tr>
                @endif

                <tr class="table-warning fw-bold">
                    <td class="text-end">TOTAL</td>
                    <td colspan="2" class="text-end">RM {{ number_format($ingredientUsage['total_amount'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

@endsection
