@extends('admin.layout.layout')

@section('page_title', 'Category')

@section('content')
<div class="container mt-4">

    {{-- Title + Add Button --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Category Management</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus"></i>
             Add Category
        </button>
    </div>

    {{-- Search --}}
    <div class="mb-3">
        <input type="text" class="form-control" placeholder="Search Category...">
    </div>

    {{-- Category List --}}
    <div class="card">
        <div class="card-body">
            <p>这里显示 Category 列表 (目前先留空，后续可以加 table)</p>
        </div>
    </div>

</div>

<!-- Modal: Add Category -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.category.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description (optional)</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Category Image</label>
                        <input class="form-control" type="file" id="image" name="image" accept="image/*">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
