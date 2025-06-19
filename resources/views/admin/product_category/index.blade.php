@extends('admin.layout.layout')

@section('page_title', 'Product Category')

@section('content')

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Product Category</h2>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    Add Product Category
                </button>
            </div>
        </div>
    </div>

    <div class="d-flex mb-4">
        <input type="text" name="search" class="form-control me-2" placeholder="Search product category...">

        <button class="btn btn-secondary">
            <i class="fa-solid fa-filter"></i>
        </button>
    </div>

    {{-- livewire --}}


    <!-- Modal for Add Product Category -->
    <div class="modal fade" id="addProductCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Product Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('admin.product_category.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        placeholder="Name" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-warning">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(function() {

        })
    </script>
@endsection
