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

    <div class="row g-3">
        @foreach ($staffs as $staff)
            <div class="col-12 col-sm-6 col-md-4">
                <a href="{{ route('admin.staff.show', ['id' => $staff->id]) }}" class="text-decoration-none">
                    <div class="card card-shadow card-hover border-0 bg-white">
                        <div class="card-body">
                            <div class="row g-2 align-items-center flex-column">
                                <div class="col-auto col-sm-12">
                                    <div class="default-avatar-wrapper">
                                        <img src="{{ $staff->image ? asset('storage/profile/' . $staff->image) : asset('img/default-avatar-dark.png') }}"
                                            onerror="this.onerror=null;this.src='{{ asset('img/default-avatar-dark.png') }}'"
                                            alt="Staff Image" style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>
                                </div>

                                <div class="col col-sm-12">
                                    <div class="text-center">
                                        <div class="fw-bold">
                                            {{ $staff->name }}
                                        </div>
                                        <div class="fw-bold">
                                            <span
                                                class="badge rounded-pill text-bg-warning">{{ $staff->role_name }}</span>
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

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="filterRole" class="form-label">Role</label>
                                    <select class="form-select" id="filterRole" wire:model="filter.role">
                                        <option value="">All role</option>
                                        <option value="Superadmin">Superadmin</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                </div>
                            </div>

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

    @if (!$noMoreData)
        <div x-intersect.full="$wire.loadMore()"></div>

        <div class="d-flex justify-content-center align-items-center my-4" wire:loading>
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    @endif

    @if (empty($staffs))
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
