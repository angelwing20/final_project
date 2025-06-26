<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\ProductCategoryAdminService;


class ProductCategoryAdminController extends Controller
{
    private $_productCategoryAdminService;

    public function __construct(ProductCategoryAdminService $productCategoryAdminService)
    {
        $this->_productCategoryAdminService = $productCategoryAdminService;
    }

    public function index()
    {
        return view('admin.product_category.index');
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'name',
        ]);

        $result = $this->_productCategoryAdminService->createProductCategory($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_productCategoryAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('admin.product_category.show', $result->id)->with('success', 'Product Category added successfully');
    }

    public function show($id)
    {
        $productCategories = $this->_productCategoryAdminService->getById($id);

        if ($productCategories == null) {
            $errorMessage = implode("<br>", $this->_productCategoryAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return view('admin.product_category.show', compact('productCategories'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'name',
        ]);

        $result = $this->_productCategoryAdminService->update($id, $data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_productCategoryAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return back()->with('success', 'ProductCategory detail updated successfully');
    }

    public function destroy($id)
    {
        $productCategoryAdminService = $this->_productCategoryAdminService->deleteById($id);

        if ($productCategoryAdminService == null) {
            $errorMessage = implode("<br>", $this->_productCategoryAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return redirect()->route('admin.product_category.index')->with('success', 'Product Category deleted successfully');
    }

    public function selectOption(Request $request)
    {

        $data = [
            "search_term" => $request->search_term ?? null,
            "page" => $request->page ?? 1,
        ];

        $results = $this->_productCategoryAdminService->getSelectOption($data);
        return $results;
    }
}
