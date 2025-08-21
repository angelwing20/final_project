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

    <div class="row g-3">
        @foreach ($dailySales as $dailySale)
            <div class="col-12">
                <a href="{{ route('admin.daily_sales.show', ['id' => $dailySale->id]) }}" class="text-decoration-none">
                    <div class="card card-shadow card-hover border-0 bg-white">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div>
                                        <h3 class="fw-bold">
                                            {{ Carbon::parse($dailySale->date)->format('d M Y') }}
                                        </h3>

                                        <div class="fw-semibold text-muted">
                                            Total Quantity: {{ $dailySale->total_quantity }}
                                        </div>

                                        <div class="fw-semibold text-muted">
                                            Total Amount: RM {{ $dailySale->total_amount }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-auto">
                                    <div class="col-auto">
                                        <div class="fw-bold text-end text-muted">
                                            PIC: {{ $dailySale->staff_name }}
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

    @if (empty($dailySales))
        <div class="text-center my-4" wire:loading.remove>
            <div class="text-muted">No data found</div>
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
