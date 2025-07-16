<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class IngredientList extends Component
{
    public $ingredients;
    public $page = 0;
    public $limitDataPerPage = 30;
    public $noMoreData = false;

    public $filter = [
        'ingredient_category_id' => null,
        'name' => null,
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
            ->join('ingredient_categories', 'ingredients.ingredient_category_id', '=', 'ingredient_categories.id')
            ->select(
                'ingredients.id',
                'ingredients.ingredient_category_id',
                'ingredients.image',
                'ingredients.name',
                'ingredients.weight',
                'ingredients.alarm_weight',
                'ingredients.price',

                'ingredient_categories.name as ingredient_category_name',
            )
            ->orderBy('ingredients.name', 'asc');

        if (isset($this->filter['ingredient_category_id']) && $this->filter['ingredient_category_id'] !== null) {
            $query = $query->where('ingredients.ingredient_category_id', '=', $this->filter['ingredient_category_id']);
        }

        if (isset($this->filter['name']) && $this->filter['name'] !== null) {
            $query = $query->where('ingredients.name', 'like', '%' . $this->filter['name'] . '%');
        }

        if (isset($this->filter['stock_status']) && $this->filter['stock_status'] !== "") {
            if ($this->filter['stock_status']) {
                $query = $query->whereColumn('ingredients.weight', '<=', 'ingredients.alarm_weight');
            } else {
                $query = $query->whereColumn('ingredients.weight', '>', 'ingredients.alarm_weight');
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

        return view('livewire.admin.ingredient-list');
    }
}
