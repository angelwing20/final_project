<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class IngredientCategoryList extends Component
{
    public $ingredientCategories;
    public $page = 0;
    public $limitDataPerPage = 15;
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
        $this->ingredientCategories = [];
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
        $query = DB::table('ingredient_categories')
            ->leftJoin('ingredients', 'ingredient_categories.id', '=', 'ingredients.ingredient_category_id')
            ->select(
                'ingredient_categories.id',
                'ingredient_categories.name',
                DB::raw('COUNT(ingredients.id) as total_ingredient')
            )
            ->groupBy('ingredient_categories.id', 'ingredient_categories.name')
            ->orderBy('ingredient_categories.name', 'asc');

        if (isset($this->filter['name']) && $this->filter['name'] !== null) {
            $query = $query->where('ingredient_categories.name', 'like', '%' . $this->filter['name'] . '%');
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
            $this->ingredientCategories = $query;
        } else {
            $this->ingredientCategories = [...$this->ingredientCategories, ...$query];
        }

        return view('livewire.admin.ingredient-category-list');
    }
}
