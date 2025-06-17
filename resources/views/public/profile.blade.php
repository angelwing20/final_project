@extends('admin/layout/layout')

@section('content')
<div class="py-5 px-4" style="background-color: #f8f9fa;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Back Button -->
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>

        <!-- Page Title -->
        <h3 class="fw-bold mb-0">Account Information</h3>

        <!-- Edit Button -->
        <a href="#" class="btn btn-warning text-dark fw-semibold">
            <i class="fa-solid fa-pen-to-square me-1"></i> Edit
        </a>
    </div>

    <div class="text-center mb-4">
        <i class="fa-solid fa-user-circle fa-7x text-secondary"></i>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mx-auto w-100" style="max-width: 1200px;">
        <div class="card-body p-5">
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Name:</label>
                    <div>{{ $user->name }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Email:</label>
                    <div>{{ $user->email }}</div>
                </div>
            </div>
            <hr>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Phone Number:</label>
                    <div>-</div>
                </div>
                <div class="col-md-6 mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <label class="fw-bold mb-0">Password:</label><br>
                        <span>************</span>
                    </div>
                    <a href="#" class="btn btn-warning text-dark fw-semibold">
                        <i class="fa-solid fa-lock me-1"></i> Change
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
