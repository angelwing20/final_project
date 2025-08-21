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
                {{ Carbon::parse($dailySales->date)->format('d M Y') }}
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

        $foodSalesItems = $dailySalesItems->where('item_type', 'food');
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
                @if ($foodSalesItems->isNotEmpty())
                    <tr class="table-secondary">
                        <td colspan="4" class="fw-bold">Foods</td>
                    </tr>
                    @foreach ($foodSalesItems as $salesItem)
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
        <h2 class="fw-bold">Ingredient Consumption</h2>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered" style="white-space: nowrap">
            <thead class="table-dark">
                <tr>
                    <th style="width: 40%">Ingredient</th>
                    <th style="width: 30%" class="text-end">Consumption</th>
                    <th style="width: 30%" class="text-end">Cost (RM)</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($ingredientConsumption['ingredients']))
                    @foreach ($ingredientConsumption['ingredients'] as $ingredientData)
                        <tr>
                            <td>{{ $ingredientData['name'] }}</td>
                            <td class="text-end">
                                @if ($ingredientData['unit_type'] === 'quantity')
                                    {{ intval(round($ingredientData['weight'] / $ingredientData['weight_unit'])) }} qty
                                @else
                                    {{ floatval(sprintf('%.2f', $ingredientData['weight'])) }} kg
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($ingredientData['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="text-center">No ingredient consumption data.</td>
                    </tr>
                @endif

                <tr class="table-warning fw-bold">
                    <td class="text-end">TOTAL</td>
                    <td colspan="2" class="text-end">RM {{ number_format($ingredientConsumption['total_amount'], 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

@endsection
