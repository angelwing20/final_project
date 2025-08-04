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
                    <th scope="col" style="width: 10px;">Action</th>
                </tr>
            </thead>

            <tbody>
                @if (!empty($addOnIngredients))
                    @foreach ($addOnIngredients as $addOnIngredient)
                        <tr>
                            <td class="fw-bold">{{ $addOnIngredient->ingredient_name }}</td>
                            <td>
                                @if ($addOnIngredient->ingredient_unit_type === 'weight')
                                    {{ floatval(sprintf('%.2f', $addOnIngredient->consumption)) }} kg
                                @else
                                    {{ $addOnIngredient->consumption / $addOnIngredient->ingredient_weight_unit }}
                                    qty
                                @endif
                            </td>
                            <td>{{ number_format($addOnIngredient->cost, 2) }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form
                                        action="{{ route('admin.add_on.ingredient.destroy', ['id' => $addOnIngredient->id]) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"
                                            onclick="deleteConfirmation(event)">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                    <button class="btn btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editAddOnIngredientModal-{{ $addOnIngredient->id }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="editAddOnIngredientModal-{{ $addOnIngredient->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Add-on Ingredient Detail</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <form id="form-{{ $addOnIngredient->id }}"
                                            action="{{ route('admin.add_on.ingredient.update', ['id' => $addOnIngredient->id]) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group mb-3">
                                                        <label for="consumption-{{ $addOnIngredient->id }}"
                                                            class="form-label">
                                                            {{ $addOnIngredient->ingredient_unit_type === 'weight' ? 'Weight (kg)' : 'Quantity (qty)' }}</label>
                                                        <input type="number" class="form-control" name="consumption"
                                                            id="consumption-{{ $addOnIngredient->id }}"
                                                            step="{{ $addOnIngredient->ingredient_unit_type === 'weight' ? '0.01' : '1' }}"
                                                            min="{{ $addOnIngredient->ingredient_unit_type === 'weight' ? '0.01' : '1' }}"
                                                            value="{{ $addOnIngredient->ingredient_unit_type === 'weight'
                                                                ? $addOnIngredient->consumption
                                                                : $addOnIngredient->consumption / $addOnIngredient->ingredient_weight_unit }}"
                                                            placeholder="{{ $addOnIngredient->ingredient_unit_type === 'weight' ? 'Weight (kg)' : 'Quantity (qty)' }}">
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

        @if (!$noMoreData && !empty($addOnIngredients))
            <div x-intersect.full="$wire.loadMore()"></div>
            <div class="d-flex justify-content-center align-items-center my-4" wire:loading>
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        @endif
    </div>
</div>
