<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProductList extends Component
{
    public $products;
    public $page = 0;
    public $limitDataPerPage = 30;
    public $noMoreData = false;

    public $filter = [
        'product_category_id' => null,
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
            ->select(
                'products.id',
                'products.product_category_id',
                'products.name',
                'products.price',
                'products.description',
                'products.image',

                'product_categories.name as product_category_name'
            )
            ->orderBy('products.name', 'asc');

        if (isset($this->filter['name']) && $this->filter['name'] !== null) {
            $query = $query->where('products.name', 'like', '%' . $this->filter['name'] . '%');
        }

        if (isset($this->filter['product_category_id']) && $this->filter['product_category_id'] !== null) {
            $query = $query->where('products.product_category_id', '=', $this->filter['product_category_id']);
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

        return view('livewire.admin.product-list');
    }
}
