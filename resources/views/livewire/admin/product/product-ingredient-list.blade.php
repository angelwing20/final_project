<div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Image</th>
                    <th scope="col">Name</th>
                    <th scope="col">Weight</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productIngredients as $productIngredient)
                    <tr>
                        <td style="width: 100px;">
                            <img src="{{ $productIngredient->image ? asset('storage/ingredient/' . $productIngredient->image) : asset('img/default-image.png') }}"
                                onerror="this.onerror=null;this.src='{{ asset('img/default-image.png') }}'"
                                alt="Ingredient Image" class="img-thumbnail"
                                style="width: 80px; height: 80px; object-fit: cover;">
                        </td>
                        <td class="fw-bold">{{ $productIngredient->ingredient_name }}</td>
                        <td>{{ $productIngredient->weight }} kg</td>
                        <td>
                            <div class="d-flex gap-2">
                                <form
                                    action="{{ route('admin.product.ingredient.destroy', ['id' => $productIngredient->id]) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="deleteConfirmation(event)">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
                                </form>
                                <button class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#editProductIngredientModal-{{ $productIngredient->id }}">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="editProductIngredientModal-{{ $productIngredient->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit Product Ingredient Detail</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <form id="form-{{ $productIngredient->id }}"
                                        action="{{ route('admin.product.ingredient.update', ['id' => $productIngredient->id]) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group mb-3">
                                                    <label for="weight-{{ $productIngredient->id }}"
                                                        class="form-label">Weight (kg)</label>
                                                    <input type="number" class="form-control" name="weight"
                                                        id="weight-{{ $productIngredient->id }}" step="0.01"
                                                        value="{{ $productIngredient->weight }}" placeholder="Weight">
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
                @endforeach
            </tbody>
        </table>

        @if (!$noMoreData)
            <div x-intersect.full="$wire.loadMore()"></div>
            <div class="d-flex justify-content-center align-items-center my-4" wire:loading>
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        @endif

        @if (empty($productIngredients))
            <div class="text-center my-4" wire:loading.remove>
                <div class="text-muted">No data found</div>
            </div>
        @endif
    </div>
</div>
