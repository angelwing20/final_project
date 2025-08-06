<?php

namespace App\Livewire\Admin\Food;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FoodIngredientList extends Component
{
    public $foodId;
    public $foodIngredients = [];
    public $totalCost = 0;
    public $page = 0;
    public $limitDataPerPage = 15;
    public $noMoreData = false;

    public function mount($foodId)
    {
        $this->foodId = $foodId;
        $this->calculateTotalCost();
    }

    public function loadMore()
    {
        $this->page++;
    }

    public function calculateTotalCost()
    {
        $this->totalCost = DB::table('food_ingredients')
            ->join('ingredients', 'food_ingredients.ingredient_id', '=', 'ingredients.id')
            ->where('food_ingredients.food_id', $this->foodId)
            ->selectRaw('SUM(food_ingredients.consumption * (ingredients.price / ingredients.weight_unit)) as total')
            ->value('total') ?? 0;
    }

    public function render()
    {
        $query = DB::table('food_ingredients')
            ->join('ingredients', 'food_ingredients.ingredient_id', '=', 'ingredients.id')
            ->select(
                'food_ingredients.id',
                'food_ingredients.food_id',
                'food_ingredients.consumption',

                'ingredients.id as ingredient_id',
                'ingredients.name as ingredient_name',
                'ingredients.unit_type as ingredient_unit_type',
                'ingredients.weight_unit as ingredient_weight_unit',
                DB::raw('(food_ingredients.consumption * (ingredients.price / ingredients.weight_unit)) as cost')
            )
            ->where('food_ingredients.food_id', $this->foodId)
            ->orderBy('ingredients.name', 'asc');

        $query = $query
            ->offset($this->page * $this->limitDataPerPage)
            ->limit($this->limitDataPerPage)
            ->get()
            ->toArray();

        if (count($query) < $this->limitDataPerPage || count($query) === 0) {
            $this->noMoreData = true;
        }

        if ($this->page === 0) {
            $this->foodIngredients = $query;
        } else {
            $this->foodIngredients = [...$this->foodIngredients, ...$query];
        }

        return view('livewire.admin.food.food-ingredient-list');
    }
}
