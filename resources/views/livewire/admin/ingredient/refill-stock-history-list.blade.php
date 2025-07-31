<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle" style="white-space: nowrap;">
        <thead class="table-dark">
            <tr>
                <th style="width:20%;">Date</th>
                <th style="width:20%;">Refill by</th>
                <th style="width:20%;">Quantity</th>
                <th style="width:20%;">Weight (kg)</th>
                <th style="width:20%;">Amount (RM)</th>
            </tr>
        </thead>

        <tbody>
            @if (!empty($refillStockHistories))
                @foreach ($refillStockHistories as $refillStockHistory)
                    <tr>
                        <td>{{ $refillStockHistory->created_at }}</td>
                        <td>{{ $refillStockHistory->staff_name }}</td>
                        <td>{{ $refillStockHistory->quantity }}</td>
                        <td>{{ $refillStockHistory->weight }}</td>
                        <td>{{ $refillStockHistory->amount }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="text-center text-muted">No data found</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if (!$noMoreData && !empty($refillStockHistories))
        <div x-intersect.full="$wire.loadMore()"></div>
        <div class="d-flex justify-content-center align-items-center my-4" wire:loading>
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    @endif
</div>
