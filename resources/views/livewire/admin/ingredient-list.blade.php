<div>
    <div class="row align-items-center mb-4">
        <div class="col">
            <div class="search-group">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" class="form-control ps-5 py-2" placeholder="搜索名称"
                    wire:keydown.debounce.300ms="search($event.target.value)" wire:model="filter.name">
            </div>
        </div>
        <div class="col-auto">
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fa-solid fa-filter"></i>
            </button>
        </div>
    </div>

    <!-- 过滤模态框 -->
    <div class="modal fade" id="filterModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">过滤</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="关闭"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="filterName" class="form-label">名称</label>
                                <input type="text" id="filterName" class="form-control" placeholder="名称"
                                    wire:model="filter.name">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3" wire:ignore>
                                <label for="filterIngredientCategory" class="form-label">食材分类</label>
                                <select class="form-control" id="filterIngredientCategory" style="width: 100%"></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" wire:click="resetFilter" data-bs-dismiss="modal"
                        onclick="resetFilterForm('#filterModal')">重置</button>
                    <button type="button" class="btn btn-warning" wire:click="applyFilter"
                        data-bs-dismiss="modal">应用</button>
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
                                        <img src="{{ $ingredient->image ? asset('storage/ingredient/' . $ingredient->image) : asset('img/default-image.png') }}"
                                            onerror="this.onerror=null;this.src='{{ asset('img/default-image.png') }}'"
                                            alt="食材图片" style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="fw-bold">
                                        {{ $ingredient->name }}
                                    </div>
                                    <div class="fw-bold">
                                        <span
                                            class="badge rounded-pill text-bg-warning">{{ $ingredient->ingredient_category_name }}</span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="fw-bold">
                                        重量: {{ $ingredient->weight }}
                                    </div>
                                    @if ($ingredient->weight !== null && $ingredient->alarm_weight !== null && $ingredient->weight < $ingredient->alarm_weight)
                                        <div class="badge bg-danger mt-2" style="padding: 5px; font-size: 0.9em;">
                                            low_stock_alert
                                        </div>
                                    @endif
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
                <span class="visually-hidden">加载中...</span>
            </div>
        </div>
    @endif

    @if (empty($ingredients))
        <div class="text-center my-4" wire:loading.remove>
            <div class="text-muted">未找到数据</div>
        </div>
    @endif
</div>
