@extends('admin.layout.layout')

@section('page_title', 'Dashboard')

@section('content')

    <h4 class="fw-bold mb-4">Low Stock Ingredients</h4>

    @livewire('admin.ingredient.low-stock-ingredient-list')

@endsection
