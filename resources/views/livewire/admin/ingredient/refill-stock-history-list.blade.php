<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle" style="white-space: nowrap;">
        <thead class="table-dark">
            <tr>
                <th>Date</th>
                <th>Refill by</th>
                <th>Quantity</th>
                @if ($unitType === 'weight')
                    <th>Weight (kg)</th>
                @endif
                <th>Amount (RM)</th>
            </tr>
        </thead>

        <tbody>
            @if (!empty($refillStockHistories))
                @foreach ($refillStockHistories as $refillStockHistory)
                    <tr>
                        <td>{{ $refillStockHistory->created_at }}</td>
                        <td>{{ $refillStockHistory->staff_name }}</td>
                        <td>{{ $refillStockHistory->quantity }}</td>
                        @if ($unitType === 'weight')
                            <td>{{ floatval(sprintf('%.2f', $refillStockHistory->weight)) }}</td>
                        @endif
                        <td>{{ $refillStockHistory->amount }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="{{ $unitType === 'weight' ? 5 : 4 }}" class="text-center text-muted">No data found</td>
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
