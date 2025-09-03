<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FoodList extends Component
{
    public $foods;
    public $page = 0;
    public $limitDataPerPage = 15;
    public $noMoreData = false;

    public $filter = [
        'food_category_id' => null,
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
                'food.food_category_id',
                'food.name',
                'food.price',
                'food.description',
                'food.image',
                'food_categories.name as food_category_name',
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
            ->groupBy(
                'food.id',
                'food.food_category_id',
                'food.name',
                'food.price',
                'food.description',
                'food.image',
                'food_categories.name'
            )
            ->orderBy('food_categories.name', 'asc')
            ->orderBy('food.name', 'asc');

        if (isset($this->filter['name']) && $this->filter['name'] !== null) {
            $query->where('food.name', 'like', '%' . $this->filter['name'] . '%');
        }

        if (isset($this->filter['food_category_id']) && $this->filter['food_category_id'] !== null) {
            $query->where('food.food_category_id', '=', $this->filter['food_category_id']);
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

        return view('livewire.admin.food-list');
    }
}
