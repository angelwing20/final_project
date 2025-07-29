<?php

namespace App\Livewire\Admin\Dashboard;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LowStockIngredientList extends Component
{
    public $lowStockIngredients;
    public $page = 0;
    public $limitDataPerPage = 30;
    public $noMoreData = false;

    public function loadMore()
    {
        $this->page++;
    }

    public function render()
    {
        $query = DB::table('ingredients')
            ->join('ingredient_categories', 'ingredients.ingredient_category_id', '=', 'ingredient_categories.id')
            ->select(
                'ingredients.id',
                'ingredients.ingredient_category_id',
                'ingredients.image',
                'ingredients.name',
                'ingredients.stock_weight',
                'ingredients.alarm_weight',
                'ingredients.weight_unit',
                'ingredients.price_per_weight_unit',

                'ingredient_categories.name as ingredient_category_name'
            )
            ->whereColumn('ingredients.stock_weight', '<=', 'ingredients.alarm_weight')
            ->orderBy('ingredients.stock_weight', 'asc');

        $query = $query
            ->offset($this->page * $this->limitDataPerPage)
            ->limit($this->limitDataPerPage)
            ->get()
            ->toArray();

        if (count($query) < $this->limitDataPerPage || count($query) === 0) {
            $this->noMoreData = true;
        }

        if ($this->page === 0) {
            $this->lowStockIngredients = $query;
        } else {
            $this->lowStockIngredients = [...$this->lowStockIngredients, ...$query];
        }

        return view('livewire.admin.dashboard.low-stock-ingredient-list');
    }
}
