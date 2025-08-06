<?php

namespace App\Livewire\Admin\AddOn;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AddOnIngredientList extends Component
{
    public $addOnId;
    public $addOnIngredients;
    public $totalCost = 0;
    public $page = 0;
    public $limitDataPerPage = 15;
    public $noMoreData = false;

    public function mount($addOnId)
    {
        $this->addOnId = $addOnId;
        $this->calculateTotalCost();
    }

    public function loadMore()
    {
        $this->page++;
    }

    public function calculateTotalCost()
    {
        $this->totalCost = DB::table('add_on_ingredients')
            ->join('ingredients', 'add_on_ingredients.ingredient_id', '=', 'ingredients.id')
            ->where('add_on_ingredients.add_on_id', $this->addOnId)
            ->selectRaw('SUM(add_on_ingredients.consumption * (ingredients.price / ingredients.weight_unit)) as total')
            ->value('total') ?? 0;
    }

    public function render()
    {
        $query = DB::table('add_on_ingredients')
            ->join('ingredients', 'add_on_ingredients.ingredient_id', '=', 'ingredients.id')
            ->select(
                'add_on_ingredients.id',
                'add_on_ingredients.add_on_id',
                'add_on_ingredients.consumption',

                'ingredients.id as ingredient_id',
                'ingredients.name as ingredient_name',
                'ingredients.unit_type as ingredient_unit_type',
                'ingredients.weight_unit as ingredient_weight_unit',
                DB::raw('(add_on_ingredients.consumption * (ingredients.price / ingredients.weight_unit)) as cost')
            )
            ->where('add_on_ingredients.add_on_id', '=', $this->addOnId)
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
            $this->addOnIngredients = $query;
        } else {
            $this->addOnIngredients = [...$this->addOnIngredients, ...$query];
        }

        return view('livewire.admin.add-on.add-on-ingredient-list');
    }
}
