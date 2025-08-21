<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\DailySalesItemAdminService;

class DailySalesItemAdminController extends Controller
{
    private $_dailySalesItemAdminService;

    public function __construct(DailySalesItemAdminService $dailySalesItemAdminService)
    {
        $this->_dailySalesItemAdminService = $dailySalesItemAdminService;
    }

    public function create()
    {
        $data = $this->_dailySalesItemAdminService->getAllFoodsAndAddOns();

        if ($data == null) {
            $errorMessage = implode("<br>", $this->_dailySalesItemAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return view('admin.daily_sales.create', [
            'foods' => $data['foods'],
            'addons' => $data['addons'],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->only(['date', 'foods', 'addons']);

        $result = $this->_dailySalesItemAdminService->createDailySales($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_dailySalesItemAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('admin.daily_sales.show', $result->id)->with('success', 'Daily sales added successfully.');
    }

    public function edit($id)
    {
        $data = $this->_dailySalesItemAdminService->getEditData($id);

        if ($data == null) {
            $errorMessage = implode("<br>", $this->_dailySalesItemAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return view('admin.daily_sales.edit', [
            'dailySales' => $data['dailySales'],
            'dailySalesItems' => $data['dailySalesItems'],
            'foods' => $data['foods'],
            'addons' => $data['addons'],
            'ingredientMap' => $data['ingredientMap'],
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->only(['date', 'foods', 'addons']);

        $result = $this->_dailySalesItemAdminService->updateDailySales($id, $data);

        if ($result === false) {
            $errorMessage = implode("<br>", $this->_dailySalesItemAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('admin.daily_sales.show', ['id' => $id])
            ->with('success', 'Daily sales detail updated successfully.');
    }
}
