<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RefillStockHistoryList extends Component
{
    public $refillStockHistories;
    public $totalAmount = 0;
    public $page = 0;
    public $limitDataPerPage = 30;
    public $noMoreData = false;

    public $filter = [
        'date_from' => null,
        'date_to' => null,
        'staff_id' => null,
        'ingredient_id' => null
    ];

    public function loadMore()
    {
        $this->page++;
    }

    public function filterField($fieldName, $value)
    {
        $this->filter[$fieldName] = $value;
        $this->applyFilter();
    }

    public function applyFilter()
    {
        $this->page = 0;
        $this->noMoreData = false;
        $this->refillStockHistories = [];
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
        if (empty($this->filter['date_from']) && empty($this->filter['date_to'])) {
            $this->refillStockHistories = [];
            $this->totalAmount = 0;
            $this->noMoreData = true;
            return view('livewire.admin.refill-stock-history-list');
        }

        $query = DB::table('refill_stock_histories')
            ->join('users', 'refill_stock_histories.staff_id', '=', 'users.id')
            ->join('ingredients', 'refill_stock_histories.ingredient_id', '=', 'ingredients.id')
            ->select(
                'refill_stock_histories.quantity',
                'refill_stock_histories.weight',
                'refill_stock_histories.amount',
                'refill_stock_histories.created_at',

                'users.name as staff_name',
                'ingredients.name as ingredient_name'
            )
            ->orderBy('refill_stock_histories.created_at', 'desc');

        if (isset($this->filter['date_from']) && $this->filter['date_from'] != null) {
            $query = $query->whereDate('refill_stock_histories.created_at', '>=', $this->filter['date_from']);
        }

        if (isset($this->filter['date_to']) && $this->filter['date_to'] != null) {
            $query = $query->whereDate('refill_stock_histories.created_at', '<=', $this->filter['date_to']);
        }

        if (isset($this->filter['staff_id']) && $this->filter['staff_id'] != null) {
            $query = $query->where('refill_stock_histories.staff_id', $this->filter['staff_id']);
        }

        if (isset($this->filter['ingredient_id']) && $this->filter['ingredient_id'] != null) {
            $query = $query->where('refill_stock_histories.ingredient_id', $this->filter['ingredient_id']);
        }

        $records = $query
            ->offset($this->page * $this->limitDataPerPage)
            ->limit($this->limitDataPerPage)
            ->get()
            ->toArray();

        $totalQuery = DB::table('refill_stock_histories');
        if (isset($this->filter['date_from']) && $this->filter['date_from'] != null) {
            $totalQuery = $totalQuery->whereDate('created_at', '>=', $this->filter['date_from']);
        }
        if (isset($this->filter['date_to']) && $this->filter['date_to'] != null) {
            $totalQuery = $totalQuery->whereDate('created_at', '<=', $this->filter['date_to']);
        }
        if (isset($this->filter['staff_id']) && $this->filter['staff_id'] != null) {
            $totalQuery = $totalQuery->where('staff_id', $this->filter['staff_id']);
        }
        if (isset($this->filter['ingredient_id']) && $this->filter['ingredient_id'] != null) {
            $totalQuery = $totalQuery->where('ingredient_id', $this->filter['ingredient_id']);
        }
        $this->totalAmount = $totalQuery->sum('amount');

        if (count($records) < $this->limitDataPerPage || count($records) === 0) {
            $this->noMoreData = true;
        }

        if ($this->page === 0) {
            $this->refillStockHistories = $records;
        } else {
            $this->refillStockHistories = [...$this->refillStockHistories, ...$records];
        }

        return view('livewire.admin.refill-stock-history-list');
    }
}
