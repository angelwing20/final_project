<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\ProductIngredientAdminService;

class ProductIngredientAdminController extends Controller
{
    private $_productIngredientAdminService;

    public function __construct(ProductIngredientAdminService $productIngredientAdminService)
    {
        $this->_productIngredientAdminService = $productIngredientAdminService;
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'product_id',
            'ingredient_id',
            'weight',
        ]);

        $result = $this->_productIngredientAdminService->createProductIngredient($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_productIngredientAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return back()->with('success', 'Product ingredient added successfully.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'weight',
        ]);

        $result = $this->_productIngredientAdminService->update($id, $data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_productIngredientAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return back()->with('success', 'Product ingredient updated successfully.');
    }

    public function destroy($id)
    {
        $result = $this->_productIngredientAdminService->deleteById($id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_productIngredientAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return back()->with('success', 'Product ingredient deleted successfully.');
    }
}
