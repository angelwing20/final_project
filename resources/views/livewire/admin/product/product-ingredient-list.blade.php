<div>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="fw-bold">Total Cost: <span class="text-success">RM {{ number_format($totalCost, 2) }}</span></h5>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col" style="white-space: nowrap;">Ingredient</th>
                    <th scope="col" style="white-space: nowrap;">Consumption</th>
                    <th scope="col" style="white-space: nowrap;">Cost (RM)</th>
                    <th scope="col" style="width: 10px">Action</th>
                </tr>
            </thead>

            <tbody>
                @if (!empty($productIngredients))
                    @foreach ($productIngredients as $productIngredient)
                        <tr>
                            <td class="fw-bold">{{ $productIngredient->ingredient_name }}</td>
                            <td>
                                @if ($productIngredient->ingredient_unit_type === 'weight')
                                    {{ floatval(sprintf('%.2f', $productIngredient->consumption)) }} kg
                                @else
                                    {{ $productIngredient->consumption / $productIngredient->ingredient_weight_unit }}
                                    qty
                                @endif
                            </td>
                            <td>{{ number_format($productIngredient->cost, 2) }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form
                                        action="{{ route('admin.product.ingredient.destroy', ['id' => $productIngredient->id]) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"
                                            onclick="deleteConfirmation(event)">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                    <button class="btn btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editProductIngredientModal-{{ $productIngredient->id }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="editProductIngredientModal-{{ $productIngredient->id }}"
                            tabindex="-1">
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
                                                        <label for="consumption-{{ $productIngredient->id }}"
                                                            class="form-label">Consumption
                                                            {{ $productIngredient->ingredient_unit_type === 'weight' ? '(kg)' : '(qty)' }}</label>
                                                        <input type="number" class="form-control" name="consumption"
                                                            id="consumption-{{ $productIngredient->id }}"
                                                            step="{{ $productIngredient->ingredient_unit_type === 'weight' ? '0.01' : '1' }}"
                                                            min="{{ $productIngredient->ingredient_unit_type === 'weight' ? '0.01' : '1' }}"
                                                            value="{{ $productIngredient->ingredient_unit_type === 'weight'
                                                                ? $productIngredient->consumption
                                                                : $productIngredient->consumption / $productIngredient->ingredient_weight_unit }}"
                                                            placeholder="Consumption {{ $productIngredient->ingredient_unit_type === 'weight' ? '(kg)' : '(qty)' }}">
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
                @else
                    <tr>
                        <td colspan="4" class="text-center text-muted">No data found</td>
                    </tr>
                @endif
            </tbody>
        </table>

        @if (!$noMoreData && !empty($productIngredients))
            <div x-intersect.full="$wire.loadMore()"></div>
            <div class="d-flex justify-content-center align-items-center my-4" wire:loading>
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        @endif
    </div>
</div>
