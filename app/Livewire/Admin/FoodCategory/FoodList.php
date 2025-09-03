<?php

namespace App\Livewire\Admin\FoodCategory;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FoodList extends Component
{
    public $foodCategoryId;
    public $foods;
    public $page = 0;
    public $limitDataPerPage = 15;
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
        $this->foods = [];
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
        $query = DB::table('food')
            ->join('food_categories', 'food.food_category_id', '=', 'food_categories.id')
            ->leftJoin('food_ingredients', 'food.id', '=', 'food_ingredients.food_id')
            ->leftJoin('ingredients', 'food_ingredients.ingredient_id', '=', 'ingredients.id')
            ->select(
                'food.id',
                'food.image',
                'food.name',
                'food.price',
                DB::raw("
                    GROUP_CONCAT(
                        CONCAT(
                            ingredients.name, ' (',
                            CASE
                                WHEN ingredients.unit_type = 'quantity'
                                THEN TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM FORMAT(food_ingredients.consumption / ingredients.weight_unit, 3)))
                                ELSE TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM FORMAT(food_ingredients.consumption, 3)))
                            END,
                            ' ',
                            CASE
                                WHEN ingredients.unit_type = 'quantity' THEN 'qty' ELSE 'kg'
                            END,
                            ')'
                        )
                        ORDER BY ingredients.name SEPARATOR ', '
                    ) as ingredient_details
                ")
            )
            ->where('food.food_category_id', '=', $this->foodCategoryId)
            ->groupBy('food.id', 'food.image', 'food.name', 'food.price')
            ->orderBy('food.name', 'asc');

        if (isset($this->filter['name']) && $this->filter['name'] !== null) {
            $query = $query->where('food.name', 'like', '%' . $this->filter['name'] . '%');
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
            $this->foods = $query;
        } else {
            $this->foods = [...$this->foods, ...$query];
        }

        return view('livewire.admin.food-category.food-list');
    }
}
