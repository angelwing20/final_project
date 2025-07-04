<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\IngredientAdminService;
use Illuminate\Support\Facades\Log;

class DashboardAdminController extends Controller
{
    private $_ingredientAdminService;

    public function __construct(IngredientAdminService $ingredientAdminService)
    {
        $this->_ingredientAdminService = $ingredientAdminService;
    }

    public function index()
    {
        $lowStockIngredients = $this->_ingredientAdminService->getLowStockIngredients();

        Log::info("\uD83D\uDD0D Dashboard 拿到数量：" . $lowStockIngredients->count());

        if ($lowStockIngredients === null) {
            Log::warning('Low stock ingredients not loaded: ', $this->_ingredientAdminService->_errorMessage ?? []);
            $lowStockIngredients = collect(); // 默认空集合
        }

        return view('admin.dashboard.dashboard', compact('lowStockIngredients'));
    }
}
