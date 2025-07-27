<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardAdminService;

class DashboardAdminController extends Controller
{
    private $_dashboardAdminService;

    public function __construct(DashboardAdminService $dashboardAdminService)
    {
        $this->_dashboardAdminService = $dashboardAdminService;
    }

    public function index()
    {
        return view('admin.dashboard.dashboard');
    }

    public function getIngredientUsageData()
    {
        return response()->json($this->_dashboardAdminService->getIngredientChartData());
    }

    public function getSalesTrendData()
    {
        return response()->json($this->_dashboardAdminService->getSalesTrendData());
    }

    public function getDashboardStats()
    {
        return response()->json($this->_dashboardAdminService->getDashboardStats());
    }
}
