<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DailySalesList extends Component
{
    public $dailySales;
    public $page = 0;
    public $limitDataPerPage = 15;
    public $noMoreData = false;

    public $filter = [
        'date_from' => null,
        'date_to' => null,
        'staff_id' => null,
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
        $this->dailySales = [];
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
        $query = DB::table('daily_sales')
            ->join('users', 'daily_sales.staff_id', '=', 'users.id')
            ->select(
                'daily_sales.id',
                'daily_sales.total_quantity',
                'daily_sales.total_amount',
                'daily_sales.created_at',

                'users.name as staff_name'
            )
            ->orderBy('daily_sales.created_at', 'desc');

        if (isset($this->filter['date_from']) && $this->filter['date_from'] != null) {
            $query = $query->whereDate('daily_sales.created_at', '>=', $this->filter['date_from']);
        }

        if (isset($this->filter['date_to']) && $this->filter['date_to'] != null) {
            $query = $query->whereDate('daily_sales.created_at', '<=', $this->filter['date_to']);
        }

        if (isset($this->filter['staff_id']) && $this->filter['staff_id'] != null) {
            $query = $query->where('daily_sales.staff_id', $this->filter['staff_id']);
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
            $this->dailySales = $query;
        } else {
            $this->dailySales = [...$this->dailySales, ...$query];
        }

        return view('livewire.admin.daily-sales-list');
    }
}
