<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\IngredientCategoryAdminService;


class IngredientCategoryAdminController extends Controller
{
    private $_ingredientCategoryAdminService;

    public function __construct(IngredientCategoryAdminService $ingredientCategoryAdminService)
    {
        $this->_ingredientCategoryAdminService = $ingredientCategoryAdminService;
    }

    public function index()
    {
        return view('admin.ingredient_category.index');
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'name',
        ]);

        $result = $this->_ingredientCategoryAdminService->createIngredientCategory($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_ingredientCategoryAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('admin.ingredient_category.show', $result->id)->with('success', 'Ingredient category added successfully.');
    }

    public function show($id)
    {
        $ingredientCategories = $this->_ingredientCategoryAdminService->getById($id);

        if ($ingredientCategories == null) {
            $errorMessage = implode("<br>", $this->_ingredientCategoryAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return view('admin.ingredient_category.show', compact('ingredientCategories'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'name',
        ]);

        $result = $this->_ingredientCategoryAdminService->update($id, $data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_ingredientCategoryAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return back()->with('success', 'ingredient category detail updated successfully.');
    }

    public function destroy($id)
    {
        $ingredientCategoryAdminService = $this->_ingredientCategoryAdminService->deleteById($id);

        if ($ingredientCategoryAdminService == null) {
            $errorMessage = implode("<br>", $this->_ingredientCategoryAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return redirect()->route('admin.ingredient_category.index')->with('success', 'ingredient category deleted successfully.');
    }

    public function selectOption(Request $request)
    {
        $data = [
            "search_term" => $request->search_term ?? null,
            "page" => $request->page ?? 1,
        ];

        $results = $this->_ingredientCategoryAdminService->getSelectOption($data);
        return $results;
    }
}
