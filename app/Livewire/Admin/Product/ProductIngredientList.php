<?php

namespace App\Livewire\Admin\Product;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProductIngredientList extends Component
{
    public $productId;
    public $productIngredients = [];
    public $totalCost = 0;
    public $page = 0;
    public $limitDataPerPage = 30;
    public $noMoreData = false;

    public function mount($productId)
    {
        $this->productId = $productId;
        $this->calculateTotalCost();
    }

    public function loadMore()
    {
        $this->page++;
    }

    public function calculateTotalCost()
    {
        $this->totalCost = DB::table('product_ingredients')
            ->join('ingredients', 'product_ingredients.ingredient_id', '=', 'ingredients.id')
            ->where('product_ingredients.product_id', $this->productId)
            ->selectRaw('SUM(product_ingredients.consumption * (ingredients.price / ingredients.weight_unit)) as total')
            ->value('total') ?? 0;
    }

    public function render()
    {
        $query = DB::table('product_ingredients')
            ->join('ingredients', 'product_ingredients.ingredient_id', '=', 'ingredients.id')
            ->select(
                'product_ingredients.id',
                'product_ingredients.product_id',
                'product_ingredients.consumption',

                'ingredients.id as ingredient_id',
                'ingredients.name as ingredient_name',
                'ingredients.unit_type as ingredient_unit_type',
                'ingredients.weight_unit as ingredient_weight_unit',
                DB::raw('(product_ingredients.consumption * (ingredients.price / ingredients.weight_unit)) as cost')
            )
            ->where('product_ingredients.product_id', $this->productId)
            ->orderBy('ingredients.name', 'asc');

        $query = $query
            ->offset($this->page * $this->limitDataPerPage)
            ->limit($this->limitDataPerPage)
            ->get()
            ->toArray();

        if (count($query) < $this->limitDataPerPage || count($query) === 0) {
            $this->noMoreData = true;
        }

        if ($this->page === 0) {
            $this->productIngredients = $query;
        } else {
            $this->productIngredients = [...$this->productIngredients, ...$query];
        }

        return view('livewire.admin.product.product-ingredient-list');
    }
}
