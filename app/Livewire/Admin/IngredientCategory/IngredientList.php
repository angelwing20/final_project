<?php

namespace App\Livewire\Admin\IngredientCategory;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class IngredientList extends Component
{
    public $ingredientCategoryId;
    public $ingredients;
    public $page = 0;
    public $limitDataPerPage = 30;
    public $noMoreData = false;

    public $filter = [
        'name' => null,
        'unit_type' => null,
        'stock_status' => null,
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
        $this->ingredients = [];
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
        $query = DB::table('ingredients')
            ->select(
                'id',
                'image',
                'name',
                'unit_type',
                'stock',
                'min_stock',
                'weight_unit',
                'price',
            )
            ->where('ingredient_category_id', '=', $this->ingredientCategoryId)
            ->orderBy('name', 'asc');

        if (isset($this->filter['name']) && $this->filter['name'] !== null) {
            $query = $query->where('name', 'like', '%' . $this->filter['name'] . '%');
        }

        if (isset($this->filter['unit_type']) && $this->filter['unit_type'] !== "") {
            $query = $query->where('unit_type', '=', $this->filter['unit_type']);
        }

        if (isset($this->filter['stock_status']) && $this->filter['stock_status'] !== "") {
            if ($this->filter['stock_status']) {
                $query = $query->whereColumn('stock', '<=', 'min_stock');
            } else {
                $query = $query->whereColumn('stock', '>', 'min_stock');
            }
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
            $this->ingredients = $query;
        } else {
            $this->ingredients = [...$this->ingredients, ...$query];
        }

        return view('livewire.admin.ingredient-category.ingredient-list');
    }
}
