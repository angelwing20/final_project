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
        @foreach ($foods as $food)
            <div class="col-12">
                <a href="{{ route('admin.food.show', ['id' => $food->id]) }}" class="text-decoration-none">
                    <div class="card card-shadow card-hover border-0 bg-white">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="default-image-wrapper">
                                        <img class="img-thumbnail"
                                            src="{{ $food->image ? asset('storage/food/' . $food->image) : asset('img/default-image.png') }}"
                                            onerror="this.onerror=null;this.src='{{ asset('img/default-image.png') }}'"
                                            style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="fw-bold">
                                        {{ $food->name }}

                                        <div>
                                            Price: RM {{ $food->price }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="d-flex flex-column gap-1">
                                        <div class="fw-bold">
                                            Ingredients:
                                        </div>

                                        <div class="scrollable-ingredients">
                                            <div class="ingredient-list">
                                                @foreach (explode(',', $food->ingredient_details) as $ingredient)
                                                    <span
                                                        class="badge bg-custom flex-shrink-0">{{ trim($ingredient) }}</span>
                                                @endforeach
                                            </div>
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

    @if (empty($foods))
        <div class="text-center my-4" wire:loading.remove>
            <div class="text-muted">No data found</div>
        </div>
    @endif
</div>

@section('scripts')
    <script>
        $(function() {

        })
    </script>
@endsection
