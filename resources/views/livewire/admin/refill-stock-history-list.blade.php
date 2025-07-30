@php
    use Carbon\Carbon;
@endphp

<div>
    <div class="row align-items-center g-3 mb-4">
        <div class="col">
            <div class="row g-3">
                <div class="col-12 col-sm-6">
                    <div class="search-group">
                        <i class="fa-solid fa-calendar-days"></i>
                        <input type="date" style="padding-left: 40px" class="form-control" placeholder="Date from"
                            wire:change="filterField('date_from',$event.target.value)" wire:model="filter.date_from">
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="search-group">
                        <i class="fa-solid fa-calendar-days"></i>
                        <input type="date" style="padding-left: 40px" class="form-control" placeholder="Date to"
                            wire:change="filterField('date_to',$event.target.value)" wire:model="filter.date_to">
                    </div>
                </div>
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
                        <div class="col-12 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label" for="filterDateFrom">Date From</label>
                                <input type="date" class="form-control" id="filterDateFrom"
                                    wire:model="filter.date_from" placeholder="Date from">
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label" for="filterDateTo">Date To</label>
                                <input type="date" class="form-control" id="filterDateTo" wire:model="filter.date_to"
                                    placeholder="Date to">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group mb-3" wire:ignore>
                                <label class="form-label" for="filterStaffId">Staff</label>
                                <select class="form-control" id="filterStaffId" style="width: 100%"></select>
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

    @if (!empty($filter['date_from']) || !empty($filter['date_to']))
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="fw-bold">Total Amount: <span class="text-success">RM {{ number_format($totalAmount, 2) }}</span>
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" style="white-space: nowrap;">
                <thead class="table-dark">
                    <tr>
                        <th style="width:15%;">Created at</th>
                        <th style="width:15%;">Ingredient</th>
                        <th style="width:15%;">Refill by</th>
                        <th style="width:15%;">Quantity</th>
                        <th style="width:15%;">Weight (kg)</th>
                        <th style="width:15%;">Amount (RM)</th>
                    </tr>
                </thead>

                <tbody>
                    @if (!empty($refillStockHistories))
                        @foreach ($refillStockHistories as $refillStockHistory)
                            <tr>
                                <td>{{ $refillStockHistory->created_at }}</td>
                                <td>{{ $refillStockHistory->ingredient_name }}</td>
                                <td>{{ $refillStockHistory->staff_name }}</td>
                                <td>{{ $refillStockHistory->quantity }}</td>
                                <td>{{ $refillStockHistory->weight }}</td>
                                <td>{{ $refillStockHistory->amount }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center text-muted">No data found.</td>
                        </tr>
                    @endif
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
        </div>
    @else
        <div class="text-center my-4">
            <div class="text-muted">Please select at least one date to view history.</div>
        </div>
    @endif
</div>

@section('scripts')
    <script>
        $(function() {
            $('#filterStaffId').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                dropdownParent: $('#filterModal .modal-content'),
                placeholder: 'Staff',

                ajax: {
                    url: "{{ route('admin.staff.select_search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        var query = {
                            search_term: params.term,
                            page: params.page,
                        }
                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data.results, function(item) {
                                return {
                                    text: item.name,
                                    id: item.id,
                                }
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                }
            }).on('change', function(e) {
                var selectedId = $(this).val();
                @this.set('filter.staff_id', selectedId, false);
            });
        })
    </script>
@endsection
