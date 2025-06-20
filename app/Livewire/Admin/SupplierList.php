<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SupplierList extends Component
{
    public $noMoreData = false;
    public $page = 0;
    public $limitDataPerPage = 30;
    public $suppliers;

    public $filter = [
        'name' => null,
        'email' => null,
        'phone' => null,
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
        $query = DB::table('suppliers')
            ->select(
                'id',
                'name',
                'email',
                'phone',
            )
            ->orderBy('name', 'asc');

        if (isset($this->filter['name']) && $this->filter['name'] !== null) {
            $query = $query->where('name', 'like', '%' . $this->filter['name'] . '%');
        }

        if (isset($this->filter['email']) && $this->filter['email'] !== null) {
            $query = $query->where('email', 'like', '%' . $this->filter['email'] . '%');
        }

        if (isset($this->filter['phone']) && $this->filter['phone'] !== null) {
            $query = $query->where('phone', 'like', '%' . $this->filter['phone'] . '%');
        }

        $query = $query
            ->offset($this->page * $this->limitDataPerPage)
            ->limit($this->limitDataPerPage)
            ->get()
            ->toArray();

        if (count($query) < $this->limitDataPerPage || count($query) == 0) {
            $this->noMoreData = true;
        }

        if ($this->page == 0) {
            $this->suppliers = $query;
        } else {
            $this->suppliers = [...$this->suppliers, ...$query];
        }

        return view('livewire.admin.supplier-list');
    }
}
