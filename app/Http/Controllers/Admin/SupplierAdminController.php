<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SupplierAdminService;
use Illuminate\Http\Request;

class SupplierAdminController extends Controller
{
    private $_supplierAdminService;

    public function __construct(SupplierAdminService $supplierAdminService)
    {
        $this->_supplierAdminService = $supplierAdminService;
    }

    public function index()
    {
        return view('admin.supplier.index');
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'name',
            'email',
            'phone',
            'address',
        ]);

        $result = $this->_supplierAdminService->createSupplier($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_supplierAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('admin.supplier.show', $result->id)->with('success', 'Supplier added successfully');
    }

    public function update(Request $request, $id)
    {
        // 暂时模拟更新逻辑
        return redirect()->route('admin.supplier.index')->with('success', 'Supplier updated successfully (模拟)');
    }

    // 删除供应商
    public function destroy($id)
    {

        return redirect()->route('admin.supplier.index')->with('success', 'Supplier deleted successfully (模拟)');
    }
}
