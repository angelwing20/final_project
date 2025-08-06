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
                    <th scope="col" style="width: 10px"></th>
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
                                <form
                                    action="{{ route('admin.add_on.ingredient.destroy', ['id' => $addOnIngredient->id]) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="deleteConfirmation(event)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
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
