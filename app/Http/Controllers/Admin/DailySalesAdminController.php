<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\DailySalesRepository;
use App\Services\Admin\DailySalesItemAdminService;

class DailySalesAdminController extends Controller
{
    private $_dailySalesRepository;
    private $_dailySalesItemAdminService;

    public function __construct(
        DailySalesRepository $dailySalesRepository,
        DailySalesItemAdminService $dailySalesItemAdminService,
    ) {
        $this->_dailySalesRepository = $dailySalesRepository;
        $this->_dailySalesItemAdminService = $dailySalesItemAdminService;
    }

    public function index()
    {
        return view('admin.daily_sales.index');
    }

    public function show($id)
    {
        $dailySales = $this->_dailySalesRepository->getById($id);

        if ($dailySales == false) {
            abort(404);
        }

        $dailySalesItems = $this->_dailySalesItemAdminService->getByDailySalesId($id);

        if ($dailySalesItems == null) {
            $errorMessage = implode("<br>", $this->_dailySalesItemAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return view('admin.daily_sales.show', compact('dailySales', 'dailySalesItems'));
    }
}
