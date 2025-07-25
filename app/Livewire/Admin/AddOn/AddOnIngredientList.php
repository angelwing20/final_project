<?php

namespace App\Livewire\Admin\AddOn;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AddOnIngredientList extends Component
{
    public $addOnId;
    public $addOnIngredients;
    public $page = 0;
    public $limitDataPerPage = 30;
    public $noMoreData = false;

    public function loadMore()
    {
        $this->page++;
    }

    public function render()
    {
        $query = DB::table('add_on_ingredients')
            ->join('ingredients', 'add_on_ingredients.ingredient_id', '=', 'ingredients.id')
            ->select(
                'add_on_ingredients.id',
                'add_on_ingredients.add_on_id',
                'add_on_ingredients.weight',

                'ingredients.image',
                'ingredients.id as ingredient_id',
                'ingredients.name as ingredient_name'
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
