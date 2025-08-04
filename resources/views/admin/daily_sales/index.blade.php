@extends('admin.layout.layout')

@section('page_title', 'Daily Sales')

@section('content')

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Daily Sales</h2>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <a href="{{ route('admin.daily_sales.create') }}" class="btn btn-success">
                    <i class="fa-solid fa-plus"></i> Add
                </a>
            </div>
        </div>
    </div>

    {{-- livewire --}}
    @livewire('admin.daily-sales-list')

@endsection
