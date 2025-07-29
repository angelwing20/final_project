<?php

namespace App\Livewire\Admin\ProductCategory;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProductList extends Component
{
    public $productCategoryId;
    public $products;
    public $page = 0;
    public $limitDataPerPage = 30;
    public $noMoreData = false;

    public $filter = [
        'name' => null,
    ];

    public function loadMore()
    {
        $this->page++;
    }

    public function search($name)
    {
        $this->filter['name'] = $name;
        $this->applyFilter();
    }

    public function applyFilter()
    {
        $this->page = 0;
        $this->noMoreData = false;
        $this->products = [];
        $this->render();
    }

    public function resetFilter()
    {
        foreach ($this->filter as $key => $value) {
            $this->filter[$key] = null;
        }
        $this->applyFilter();
    }

    public function render()
    {
        $query = DB::table('products')
            ->join('product_categories', 'products.product_category_id', '=', 'product_categories.id')
            ->leftJoin('product_ingredients', 'products.id', '=', 'product_ingredients.product_id')
            ->leftJoin('ingredients', 'product_ingredients.ingredient_id', '=', 'ingredients.id')
            ->select(
                'products.id',
                'products.image',
                'products.name',
                'products.price',
                DB::raw("GROUP_CONCAT(CONCAT(ingredients.name, ' (', product_ingredients.weight, 'kg)') ORDER BY ingredients.name SEPARATOR ', ') as ingredient_details")
            )
            ->where('products.product_category_id', '=', $this->productCategoryId)
            ->groupBy('products.id', 'products.image', 'products.name', 'products.price')
            ->orderBy('products.name', 'asc');

        if (isset($this->filter['name']) && $this->filter['name'] !== null) {
            $query = $query->where('name', 'like', '%' . $this->filter['name'] . '%');
        }

        $query = $query
            ->offset($this->page * $this->limitDataPerPage)
            ->limit($this->limitDataPerPage)
            ->get()
            ->toArray();

        if (count($query) < $this->limitDataPerPage || count($query) === 0) {
            $this->noMoreData = true;
        }

        if ($this->page === 0) {
            $this->products = $query;
        } else {
            $this->products = [...$this->products, ...$query];
        }

        return view('livewire.admin.product-category.product-list');
    }
}
