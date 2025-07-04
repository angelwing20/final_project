@extends('admin.layout.layout')

@section('page_title', 'Dashboard')

@section('content')
    @php
        $lowStockIngredientsCollection = $lowStockIngredients ?? collect();
    @endphp

    <div class="container-fluid">
        <h4 class="fw-bold mb-4">Low Stock Alert</h4>

        @if ($lowStockIngredientsCollection->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Image</th>
                            <th scope="col">Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">Weight</th>
                            <th scope="col">Alarm Weight</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lowStockIngredientsCollection as $ingredient)
                            <tr>
                                <td style="width: 100px;">
                                    <img src="{{ $ingredient->image ? asset('storage/ingredient/' . $ingredient->image) : asset('img/default-image.png') }}"
                                        onerror="this.onerror=null;this.src='{{ asset('img/default-image.png') }}'"
                                        alt="Ingredient Image"
                                        class="img-thumbnail"
                                        style="width: 80px; height: 80px; object-fit: cover;">
                                </td>
                                <td class="fw-bold">{{ $ingredient->name }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $ingredient->ingredientCategory->name ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td>{{ $ingredient->weight }} kg</td>
                                <td>{{ $ingredient->alarm_weight }} kg</td>
                                <td>
                                    @if ($ingredient->weight < $ingredient->alarm_weight)
                                        <span class="badge bg-danger">Low</span>
                                    @else
                                        <span class="badge bg-success">OK</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-success mt-3">
                No low stock items.
            </div>
        @endif
    </div>
@endsection
