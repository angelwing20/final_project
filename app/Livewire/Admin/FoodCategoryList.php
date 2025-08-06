<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FoodCategoryList extends Component
{
    public $foodCategories;
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
        $this->foodCategories = [];
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
        $query = DB::table('food_categories')
            ->leftJoin('food', 'food_categories.id', '=', 'food.food_category_id')
            ->select(
                'food_categories.id',
                'food_categories.name',
                DB::raw('COUNT(food.id) as total_food')
            )
            ->groupBy('food_categories.id', 'food_categories.name')
            ->orderBy('food_categories.name', 'asc');

        if (isset($this->filter['name']) && $this->filter['name'] !== null) {
            $query = $query->where('food_categories.name', 'like', '%' . $this->filter['name'] . '%');
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
            $this->foodCategories = $query;
        } else {
            $this->foodCategories = [...$this->foodCategories, ...$query];
        }

        return view('livewire.admin.food-category-list');
    }
}
