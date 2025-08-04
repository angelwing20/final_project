<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DailySalesAdminService;
use App\Services\Admin\DailySalesItemAdminService;

class DailySalesAdminController extends Controller
{
    private $_dailySalesAdminService;
    private $_dailySalesItemAdminService;

    public function __construct(
        DailySalesAdminService $dailySalesAdminService,
        DailySalesItemAdminService $dailySalesItemAdminService,
    ) {
        $this->_dailySalesAdminService = $dailySalesAdminService;
        $this->_dailySalesItemAdminService = $dailySalesItemAdminService;
    }

    public function index()
    {
        return view('admin.daily_sales.index');
    }

    public function show($id)
    {
        $dailySales = $this->_dailySalesAdminService->getById($id);

        if ($dailySales == false) {
            abort(404);
        }

        $dailySalesItems = $this->_dailySalesItemAdminService->getByDailySalesId($id);

        if ($dailySalesItems == null) {
            $errorMessage = implode("<br>", $this->_dailySalesItemAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        $ingredientConsumption = $this->_dailySalesAdminService->getIngredientConsumptionTableData($id);

        if ($ingredientConsumption == null) {
            $errorMessage = implode("<br>", $this->_dailySalesAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return view('admin.daily_sales.show', compact('dailySales', 'dailySalesItems', 'ingredientConsumption'));
    }
}
