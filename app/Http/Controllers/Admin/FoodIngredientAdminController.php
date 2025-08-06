<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\FoodIngredientAdminService;

class FoodIngredientAdminController extends Controller
{
    private $_foodIngredientAdminService;

    public function __construct(FoodIngredientAdminService $foodIngredientAdminService)
    {
        $this->_foodIngredientAdminService = $foodIngredientAdminService;
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'food_id',
            'ingredient_id',
            'consumption',
        ]);

        $result = $this->_foodIngredientAdminService->createFoodIngredient($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_foodIngredientAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return back()->with('success', 'Food ingredient added successfully.');
    }

    public function destroy($id)
    {
        $result = $this->_foodIngredientAdminService->deleteById($id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_foodIngredientAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return back()->with('success', 'Food ingredient deleted successfully.');
    }
}
