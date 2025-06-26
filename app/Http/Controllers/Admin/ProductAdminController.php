<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\ProductAdminService;

class ProductAdminController extends Controller
{
    private $_productAdminService;

    public function __construct(ProductAdminService $productAdminService)
    {
        $this->_productAdminService = $productAdminService;
    }

    public function index()
    {
        return view('admin.product.index');
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'product_category_id',
            'name',
            'price',
            'description',
            'image'
        ]);

        $result = $this->_productAdminService->createProduct($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_productAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('admin.product.show', $result->id)->with('success', 'Product added successfully');
    }

    public function show($id)
    {
        $product = $this->_productAdminService->getById($id);

        if ($product == null) {
            $errorMessage = implode("<br>", $this->_productAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return view('admin.product.show', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'product_category_id',
            'name',
            'price',
            'description',
            'image'
        ]);

        $result = $this->_productAdminService->update($id, $data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_productAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return back()->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $result = $this->_productAdminService->deleteById($id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_productAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return redirect()->route('admin.product.index')->with('success', 'Product deleted successfully');
    }
}
