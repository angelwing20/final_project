<div>
    <h4 class="fw-bold mb-4">Low Stock Ingredients</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Image</th>
                    <th scope="col">Name</th>
                    <th scope="col">Category</th>
                    <th scope="col">Weight</th>
                    <th scope="col">Alarm Weight</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lowStockIngredients as $ingredient)
                    <tr>
                        <td style="width: 100px;">
                            <img src="{{ $ingredient->image ? asset('storage/ingredient/' . $ingredient->image) : asset('img/default-image.png') }}"
                                onerror="this.onerror=null;this.src='{{ asset('img/default-image.png') }}'"
                                alt="Ingredient Image" class="img-thumbnail"
                                style="width: 80px; height: 80px; object-fit: cover;">
                        </td>
                        <td class="fw-bold">{{ $ingredient->name }}</td>
                        <td>
                            <span class="badge rounded-pill text-bg-warning">
                                {{ $ingredient->ingredient_category_name }}
                            </span>
                        </td>
                        <td>{{ $ingredient->weight }} kg</td>
                        <td>{{ $ingredient->alarm_weight }} kg</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if (!$noMoreData)
            <div x-intersect.full="$wire.loadMore()"></div>
            <div class="d-flex justify-content-center align-items-center my-4" wire:loading>
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        @endif

        @if (empty($lowStockIngredients))
            <div class="text-center my-4" wire:loading.remove>
                <div class="text-muted">No data found</div>
            </div>
        @endif
    </div>
</div>
