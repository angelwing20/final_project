<div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Image</th>
                    <th scope="col">Ingredient Name</th>
                    <th scope="col">Weight</th>
                    <th scope="col">Created at</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($supplyHistories as $supplyHistory)
                    <tr>
                        <td style="width: 100px;">
                            <img src="{{ $supplyHistory->ingredient_image ? asset('storage/ingredient/' . $supplyHistory->ingredient_image) : asset('img/default-image.png') }}"
                                onerror="this.onerror=null;this.src='{{ asset('img/default-image.png') }}'"
                                alt="Ingredient Image" class="img-thumbnail"
                                style="width: 80px; height: 80px; object-fit: cover;">
                        </td>
                        <td class="fw-bold">{{ $supplyHistory->ingredient_name }}</td>
                        <td>{{ $supplyHistory->weight }} kg</td>
                        <td>{{ $supplyHistory->created_at }}</td>
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

        @if (empty($supplyHistories))
            <div class="text-center my-4" wire:loading.remove>
                <div class="text-muted">No data found</div>
            </div>
        @endif
    </div>
</div>
