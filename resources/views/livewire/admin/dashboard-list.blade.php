<div>
    @if ($lowStockIngredients->isNotEmpty())
        <div class="alert alert-warning">
            <h4>Low Stock Warning</h4>
            <ul>
                @foreach ($lowStockIngredients as $ingredient)
                    <li>{{ $ingredient->name }} - Current: {{ $ingredient->weight }}kg, Alarm: {{ $ingredient->alarm_weight }}kg</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
