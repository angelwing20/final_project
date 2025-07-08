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

        return redirect()->route('admin.supplier.show', $result->id)->with('success', 'Supplier added successfully.');
    }

    public function show($id)
    {
        $supplier = $this->_supplierAdminService->getById($id);

        if ($supplier == false) {
            abort(404);
        }

        if ($supplier == null) {
            $errorMessage = implode("<br>", $this->_supplierAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return view('admin.supplier.show', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'name',
            'email',
            'phone',
            'address',
        ]);

        $result = $this->_supplierAdminService->update($id, $data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_supplierAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return back()->with('success', 'Supplier detail updated successfully.');
    }

    public function destroy($id)
    {
        $supplier = $this->_supplierAdminService->deleteById($id);

        if ($supplier == null) {
            $errorMessage = implode("<br>", $this->_supplierAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return redirect()->route('admin.supplier.index')->with('success', 'Supplier deleted successfully.');
    }
}
