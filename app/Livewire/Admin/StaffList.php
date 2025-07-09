<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StaffList extends Component
{
    public $staffs;
    public $page = 0;
    public $limitDataPerPage = 30;
    public $noMoreData = false;

    public $filter = [
        'role' => null,
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
        $this->staffs = [];
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
        $query = DB::table('users')
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', '=', \App\Models\User::class);
            })
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select(
                'users.id',
                'users.image',
                'users.name',

                'roles.name as role_name'
            )
            ->where('users.id', '!=', Auth::id())
            ->orderBy('users.name', 'asc');

        if (isset($this->filter['role']) && $this->filter['role'] !== "") {
            $query = $query->where('roles.name', '=', $this->filter['role']);
        }

        if (isset($this->filter['name']) && $this->filter['name'] !== null) {
            $query = $query->where('users.name', 'like', '%' . $this->filter['name'] . '%');
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
            $this->staffs = $query;
        } else {
            $this->staffs = [...$this->staffs, ...$query];
        }

        return view('livewire.admin.staff-list');
    }
}
