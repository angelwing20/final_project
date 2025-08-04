<div>
    <div class="row align-items-center mb-4">
        <div class="col">
            <div class="search-group">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" class="form-control ps-5 py-2" placeholder="Search name"
                    wire:keydown.debounce.300ms="search($event.target.value)" wire:model="filter.name">
            </div>
        </div>
        <div class="col-auto">
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fa-solid fa-filter"></i>
            </button>
        </div>
    </div>

    <div class="modal fade" id="filterModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="filterName" class="form-label">Name</label>
                                <input type="text" id="filterName" class="form-control" placeholder="Name"
                                    wire:model="filter.name">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <label for="filterUnitType" class="form-label">Unit Type</label>
                                <select class="form-select" id="filterUnitType" wire:model="filter.unit_type">
                                    <option value="">All unit types</option>
                                    <option value="weight">Weight</option>
                                    <option value="quantity">Quantity</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <label for="filterStockStatus" class="form-label">Stock Status</label>
                                <select class="form-select" id="filterStockStatus" wire:model="filter.stock_status">
                                    <option value="">All stock status</option>
                                    <option value="1">Low stock</option>
                                    <option value="0">Normal stock</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" wire:click="resetFilter" data-bs-dismiss="modal"
                        onclick="resetFilterForm('#filterModal')">Reset</button>
                    <button type="button" class="btn btn-warning" wire:click="applyFilter"
                        data-bs-dismiss="modal">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @foreach ($ingredients as $ingredient)
            <div class="col-12">
                <a href="{{ route('admin.ingredient.show', ['id' => $ingredient->id]) }}" class="text-decoration-none">
                    <div class="card card-shadow card-hover border-0 bg-white">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="default-image-wrapper">
                                        <img class="img-thumbnail"
                                            src="{{ $ingredient->image ? asset('storage/ingredient/' . $ingredient->image) : asset('img/default-image.png') }}"
                                            onerror="this.onerror=null;this.src='{{ asset('img/default-image.png') }}'"
                                            style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="fw-bold">
                                        {{ $ingredient->name }}

                                        <div>
                                            Price: RM {{ $ingredient->price }}
                                            @if ($ingredient->unit_type === 'weight')
                                                / {{ floatval(sprintf('%.2f', $ingredient->weight_unit)) }} kg
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-auto">
                                    <div class="d-flex flex-column align-items-sm-end fw-bold text-center">
                                        <div>
                                            Stock: @if ($ingredient->unit_type === 'weight')
                                                {{ floatval(sprintf('%.2f', $ingredient->stock)) }} kg
                                            @else
                                                {{ $ingredient->stock / $ingredient->weight_unit }} qty
                                            @endif
                                        </div>

                                        @if ($ingredient->stock !== null && $ingredient->min_stock !== null && $ingredient->stock <= $ingredient->min_stock)
                                            <span class="badge bg-danger mt-1">
                                                Low stock
                                            </span>
                                        @endif
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

    @if (empty($ingredients))
        <div class="text-center my-4" wire:loading.remove>
            <div class="text-muted">No data found</div>
        </div>
    @endif
</div>
