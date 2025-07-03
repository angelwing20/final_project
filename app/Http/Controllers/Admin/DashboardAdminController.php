<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ingredient;

class DashboardAdminController extends Controller
{
    //
    public function index()
    {
        $lowStockIngredients = Ingredient::whereNotNull('weight')
            ->whereNotNull('alarm_weight')
            ->whereColumn('weight', '<', 'alarm_weight')
            ->get(['id', 'name', 'weight', 'alarm_weight']);
        return view('admin.dashboard.dashboard', compact('lowStockIngredients'));
    }
}
