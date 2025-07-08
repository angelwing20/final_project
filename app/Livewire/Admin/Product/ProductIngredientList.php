<?php

namespace App\Livewire\Admin\Product;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProductIngredientList extends Component
{
    public $productId;
    public $productIngredients;
    public $page = 0;
    public $limitDataPerPage = 30;
    public $noMoreData = false;

    public function loadMore()
    {
        $this->page++;
    }

    public function render()
    {
        $query = DB::table('product_ingredients')
            ->join('ingredients', 'product_ingredients.ingredient_id', '=', 'ingredients.id')
            ->select(
                'product_ingredients.id',
                'product_ingredients.product_id',
                'product_ingredients.weight',

                'ingredients.image',
                'ingredients.id as ingredient_id',
                'ingredients.name as ingredient_name'
            )
            ->where('product_ingredients.product_id', '=', $this->productId)
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
