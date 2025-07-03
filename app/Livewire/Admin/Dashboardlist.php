<?php
namespace App\Http\Livewire\Admin;
use Livewire\Component;
use App\Models\Ingredient;

class DashboardLowStockAlert extends Component
{
    public $lowStockIngredients;

    public function mount()
    {
        $this->loadLowStock();
    }

    public function loadLowStock()
    {
        $this->lowStockIngredients = Ingredient::whereNotNull('weight')
            ->whereNotNull('alarm_weight')
            ->whereColumn('weight', '<', 'alarm_weight')
            ->get(['id', 'name', 'weight', 'alarm_weight']);
    }

    public function render()
    {
        return view('livewire.admin.dashboard-list', ['lowStockIngredients' => $this->lowStockIngredients]);
    }
}

