<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProductCategoryList extends Component
{
    public $productCategories;
    public $page = 0;
    public $limitDataPerPage = 30;
    public $noMoreData = false;

    public $filter = [
        'name' => null
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
        $this->productCategories = [];
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
        $query = DB::table('product_categories')
            ->leftJoin('products', 'product_categories.id', '=', 'products.product_category_id')
            ->select(
                'product_categories.id',
                'product_categories.name',
                DB::raw('COUNT(products.id) as total_product')
            )
            ->groupBy('product_categories.id', 'product_categories.name')
            ->orderBy('product_categories.name', 'asc');

        if (isset($this->filter['name']) && $this->filter['name'] !== null) {
            $query = $query->where('product_categories.name', 'like', '%' . $this->filter['name'] . '%');
        }

        $query = $query
            ->offset($this->page * $this->limitDataPerPage)
            ->limit($this->limitDataPerPage)
            ->get()
            ->toArray();

        if (count($query) < $this->limitDataPerPage || count($query) == 0) {
            $this->noMoreData = true;
        }

        if ($this->page == 0) {
            $this->productCategories = $query;
        } else {
            $this->productCategories = [...$this->productCategories, ...$query];
        }

        return view('livewire.admin.product-category-list');
    }
}
