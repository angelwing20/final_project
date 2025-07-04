<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\IngredientAdminService;
use Illuminate\Http\Request;

class IngredientAdminController extends Controller
{
    private $_ingredientAdminService;

    public function __construct(IngredientAdminService $ingredientAdminService)
    {
        $this->_ingredientAdminService = $ingredientAdminService;
    }

    public function index()
    {
        return view('admin.ingredient.index');
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'ingredient_category_id',
            'image',
            'name',
            'weight',
            'alarm_weight',
            'description',
        ]);

        $result = $this->_ingredientAdminService->createIngredient($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_ingredientAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('admin.ingredient.show', $result->id)->with('success', 'Ingredient added successfully.');
    }

    public function show($id)
    {
        $ingredient = $this->_ingredientAdminService->getById($id);

        if ($ingredient == null) {
            $errorMessage = implode("<br>", $this->_ingredientAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return view('admin.ingredient.show', compact('ingredient'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'ingredient_category_id',
            'image',
            'name',
            'alarm_weight',
            'description',
        ]);

        $result = $this->_ingredientAdminService->update($id, $data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_ingredientAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return back()->with('success', 'Ingredient detail updated successfully');
    }

    public function destroy($id)
    {
        $result = $this->_ingredientAdminService->deleteById($id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_ingredientAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return redirect()->route('admin.ingredient.index')->with('success', 'ingredient deleted successfully.');
    }
}
