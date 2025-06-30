@extends('admin/layout/layout')

@section('page_title', 'Account Detail')

@section('content') 
    <!-- Back and Edit Buttons Row -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Back
        </a>

        <a href="#" class="btn btn-warning text-dark">
            <i class="fa-solid fa-pen-to-square me-2"></i> Edit
        </a>
    </div>

    <!-- Main Card -->
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 1140px;">
        <!-- Card Header -->
        <div class="card-header bg-white border-0 px-4 py-4 rounded-top-4 shadow-sm" style="
    display: flex;
    justify-content: center;
    color: black;
">
            <h3 class="mb-0 fw-bold">Account Information</h3>
        </div>

        <!-- Card Body -->
        <div class="card-body p-4 p-md-5">
            <div class="row g-4 align-items-start">
                <!-- Profile Image -->
                <div class="col-12 col-md-4 text-center">
                    @if ($user->image)
                        <img src="{{ asset('storage/profile/' . $user->image) }}"
                             class="rounded-circle shadow border"
                             width="150" height="150" alt="Profile Picture">
                    @else
                        <i class="fa-solid fa-user-circle fa-8x text-secondary"></i>
                    @endif
                    <p class="text-muted mt-3">Profile Picture</p>
                </div>

                <!-- Account Info -->
                <div class="col-12 col-md-8">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="fw-bold text-muted">Full Name</label>
                            <div class="fs-5 fw-semibold">{{ $user->name }}</div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="fw-bold text-muted">Email Address</label>
                            <div class="fs-5 fw-semibold">{{ $user->email }}</div>
                        </div>
                    </div>

                    <hr>

                    <!-- Password Section -->
                        <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="fw-bold text-muted">Password</label>
                                    <div class="fs-5">************</div>
                                </div>
                            <div class="col-md-6 mb-4">
                                <a href="#" class="btn btn-outline-warning text-dark">
                                    <i class="fa-solid fa-lock me-2"></i> Change Password
                                </a>
                            </div>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection
