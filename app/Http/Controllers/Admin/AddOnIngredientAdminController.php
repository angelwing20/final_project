<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AddOnIngredientAdminService;

class AddOnIngredientAdminController extends Controller
{
    private $_addOnIngredientAdminService;

    public function __construct(AddOnIngredientAdminService $addOnIngredientAdminService)
    {
        $this->_addOnIngredientAdminService = $addOnIngredientAdminService;
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'add_on_id',
            'ingredient_id',
            'consumption',
        ]);

        $result = $this->_addOnIngredientAdminService->createAddOnIngredient($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_addOnIngredientAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return back()->with('success', 'Add-on ingredient added successfully.');
    }

    public function destroy($id)
    {
        $result = $this->_addOnIngredientAdminService->deleteById($id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_addOnIngredientAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return back()->with('success', 'Add-on ingredient deleted successfully.');
    }
}
