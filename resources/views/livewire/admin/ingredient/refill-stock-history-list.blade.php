<div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Supplier Name</th>
                    <th scope="col">Weight</th>
                    <th scope="col">Created at</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($refillStockHistories as $refillStockHistory)
                    <tr>
                        <td class="fw-bold">{{ $refillStockHistory->supplier_name }}</td>
                        <td>{{ $refillStockHistory->weight }} kg</td>
                        <td>{{ $refillStockHistory->created_at }}</td>
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

        @if (empty($refillStockHistory))
            <div class="text-center my-4" wire:loading.remove>
                <div class="text-muted">No data found</div>
            </div>
        @endif
    </div>
</div>
