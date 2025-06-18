@extends('admin.layout.layout') {{-- 假设你 layout 已经放好 --}}

@section('page_title', 'Product')

@section('content')
    <div class="container mt-4">

        {{-- Title + Add Button --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Product Management</h2>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                Add Product
            </button>
        </div>

        {{-- Search Bar + Filter --}}
        <div class="d-flex mb-4">
            <input type="text" name="search" class="form-control me-2" placeholder="Search products...">

            <button class="btn btn-secondary">
                <i class="fa-solid fa-filter"></i>
            </button>
        </div>

        {{-- Product List --}}
        <div class="card">
            <div class="card-body">
                <p>Product list will be displayed here.</p>
            </div>
        </div>

    </div>

    <!-- Modal for Add Product -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
                            <button type="button" class="btn btn-outline-secondary"
                                onclick="document.getElementById('imageInput').click()">Choose</button>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Categories Product</label>
                            <select name="category" class="form-control" id="category" required>
                                <option value="Please choose">Please choose</option>
                                <option value="Category1">Category 1</option>
                                <option value="Category2">Category 2</option>
                                <!-- Add more categories as needed -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Product name</label>
                            <input type="text" name="name" class="form-control" id="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" name="price" class="form-control" id="price" step="0.01"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" class="form-control" id="description" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning text-white"
                            style="background-color: #f4a261;">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection
