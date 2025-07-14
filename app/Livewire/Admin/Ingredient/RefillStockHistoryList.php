<?php

namespace App\Livewire\Admin\Ingredient;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RefillStockHistoryList extends Component
{
    public $refillStockHistories;
    public $page = 0;
    public $limitDataPerPage = 10;
    public $noMoreData = false;

    public function loadMore()
    {
        $this->page++;
    }

    public function render()
    {
        $query = DB::table('supply_histories')
            ->join('suppliers', 'supply_histories.supplier_id', '=', 'suppliers.id')
            ->select(
                'supply_histories.weight',
                'supply_histories.created_at',

                'suppliers.name as supplier_name',
            )
            ->where('supply_histories.created_at', '>=', Carbon::now()->subMonth())
            ->orderBy('supply_histories.created_at', 'desc');

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
