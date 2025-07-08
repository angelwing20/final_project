<?php

namespace App\Livewire\Admin\Supplier;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SupplyHistoryList extends Component
{
    public $supplyHistories;
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
            ->join('ingredients', 'supply_histories.ingredient_id', '=', 'ingredients.id')
            ->select(
                'ingredients.image as ingredient_image',
                'ingredients.name as ingredient_name',
                'supply_histories.weight',
                'supply_histories.created_at',
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
            $this->supplyHistories = $query;
        } else {
            $this->supplyHistories = [...$this->supplyHistories, ...$query];
        }

        return view('livewire.admin.supplier.supply-history-list');
    }
}
