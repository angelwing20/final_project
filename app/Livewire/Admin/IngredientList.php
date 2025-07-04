<?php

namespace App\Livewire\Admin;

use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class IngredientList extends Component
{
    public $ingredients;
    public $lowStockIngredients;
    public $page = 0;
    public $limitDataPerPage = 30;
    public $noMoreData = false;

    public $filter = [
        'ingredient_category_id' => null,
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
                'ingredients.description',


                'ingredient_categories.name as ingredient_category_name'
            )
            ->orderBy('ingredients.name', 'asc');

        if (isset($this->filter['name']) && $this->filter['name'] !== null) {
            $query = $query->where('ingredients.name', 'like', '%' . $this->filter['name'] . '%');
        }

        if (isset($this->filter['ingredient_category_id']) && $this->filter['ingredient_category_id'] !== null) {
            $query = $query->where('ingredients.ingredient_category_id', '=', $this->filter['ingredient_category_id']);
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

        $lowStockQuery = DB::table('ingredients')
            ->join('ingredient_categories', 'ingredients.ingredient_category_id', '=', 'ingredient_categories.id')
            ->select(
                'ingredients.id',
                'ingredients.ingredient_category_id',
                'ingredients.image',
                'ingredients.name',
                'ingredients.weight',
                'ingredients.alarm_weight',
                'ingredients.description',
                'ingredient_categories.name as ingredient_category_name'
            )
            ->whereNotNull('ingredients.weight')
            ->whereNotNull('ingredients.alarm_weight')
            ->whereColumn('ingredients.weight', '<', 'ingredients.alarm_weight');

        if (isset($this->filter['name']) && $this->filter['name'] !== null) {
            $lowStockQuery = $lowStockQuery->where('ingredients.name', 'like', '%' . $this->filter['name'] . '%');
        }

        if (isset($this->filter['ingredient_category_id']) && $this->filter['ingredient_category_id'] !== null) {
            $lowStockQuery = $lowStockQuery->where('ingredients.ingredient_category_id', '=', $this->filter['ingredient_category_id']);
        }

        $this->lowStockIngredients = $lowStockQuery->get()->toArray();

        return view('livewire.admin.ingredient-list');
    }
}
