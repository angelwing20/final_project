<?php

namespace App\Livewire\Admin\Ingredient;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RefillStockHistoryList extends Component
{
    public $ingredientId;
    public $unitType;
    public $refillStockHistories;
    public $page = 0;
    public $limitDataPerPage = 10;
    public $noMoreData = false;

    public function mount($ingredientId)
    {
        $this->ingredientId = $ingredientId;

        $ingredient = DB::table('ingredients')
            ->select('unit_type')
            ->where('id', $ingredientId)
            ->first();

        $this->unitType = $ingredient ? $ingredient->unit_type : null;
    }

    public function loadMore()
    {
        $this->page++;
    }

    public function render()
    {
        $query = DB::table('refill_stock_histories')
            ->join('users', 'refill_stock_histories.staff_id', '=', 'users.id')
            ->select(
                'refill_stock_histories.quantity',
                'refill_stock_histories.weight',
                'refill_stock_histories.amount',
                'refill_stock_histories.created_at',

                'users.name as staff_name',
            )
            ->where('refill_stock_histories.ingredient_id', '=', $this->ingredientId)
            ->orderBy('refill_stock_histories.created_at', 'desc');

        $query = $query
            ->offset($this->page * $this->limitDataPerPage)
            ->limit($this->limitDataPerPage)
            ->get()
            ->toArray();

        if (count($query) < $this->limitDataPerPage || count($query) === 0) {
            $this->noMoreData = true;
        }

        if ($this->page === 0) {
            $this->refillStockHistories = $query;
        } else {
            $this->refillStockHistories = [...$this->refillStockHistories, ...$query];
        }

        return view('livewire.admin.ingredient.refill-stock-history-list');
    }
}
