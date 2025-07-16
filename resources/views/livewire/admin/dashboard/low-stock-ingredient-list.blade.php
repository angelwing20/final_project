<div>
    <h4 class="fw-bold mb-3">Low Stock Ingredients</h4>

    <div class="row g-3">
        @foreach ($lowStockIngredients as $ingredient)
            <div class="col-12">
                <a href="{{ route('admin.ingredient.show', ['id' => $ingredient->id]) }}" class="text-decoration-none">
                    <div class="card card-shadow card-hover border-0 bg-white">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="default-image-wrapper">
                                        <img src="{{ $ingredient->image ? asset('storage/ingredient/' . $ingredient->image) : asset('img/default-image.png') }}"
                                            onerror="this.onerror=null;this.src='{{ asset('img/default-image.png') }}'"
                                            alt="Ingredient Image" class="img-thumbnail"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="fw-bold">
                                        {{ $ingredient->name }}

                                        <div>
                                            Price: {{ $ingredient->price }}
                                        </div>

                                        <div>
                                            <span class="badge rounded-pill text-bg-warning">
                                                {{ $ingredient->ingredient_category_name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-auto">
                                    <div class="d-flex flex-column fw-semibold align-items-end">
                                        <div>
                                            Weight: {{ $ingredient->weight }} kg
                                        </div>

                                        <div>
                                            Alarm Weight: {{ $ingredient->alarm_weight }} kg
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

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
