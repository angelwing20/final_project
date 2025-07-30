@extends('admin.layout.layout')

@section('page_title', 'Refill Stock History')

@section('content')

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Refill Stock History</h2>
        </div>
    </div>

    {{-- livewire --}}
    @livewire('admin.refill-stock-history-list')

@endsection
