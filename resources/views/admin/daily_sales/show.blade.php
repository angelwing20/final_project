@php
    use Carbon\Carbon;
@endphp

@extends('admin.layout.layout')

@section('page_title', 'Daily Sales Detail')

@section('content')

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Daily Sales Detail - {{ Carbon::parse($dailySales->created_at)->format('d M Y') }}
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

@endsection
