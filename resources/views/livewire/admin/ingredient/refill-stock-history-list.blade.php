<div class="table-responsive" style="overflow-x: auto;">
    <table class="table table-bordered table-hover align-middle" style="min-width: 800px; white-space: nowrap;">
        <thead class="table-dark">
            <tr>
                <th style="width:20%;" scope="col">Created at</th>
                <th style="width:20%;" scope="col">Refill by</th>
                <th style="width:20%;" scope="col">Quantity</th>
                <th style="width:20%;" scope="col">Weight (kg)</th>
                <th style="width:20%;" scope="col">Amount (RM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($refillStockHistories as $refillStockHistory)
                <tr>
                    <td>{{ $refillStockHistory->created_at }}</td>
                    <td>{{ $refillStockHistory->staff_name }}</td>
                    <td>{{ $refillStockHistory->quantity }}</td>
                    <td>{{ $refillStockHistory->weight }}</td>
                    <td>{{ $refillStockHistory->amount }}</td>
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
