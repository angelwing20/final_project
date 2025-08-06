<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\FoodCategoryAdminService;


class FoodCategoryAdminController extends Controller
{
    private $_foodCategoryAdminService;

    public function __construct(FoodCategoryAdminService $foodCategoryAdminService)
    {
        $this->_foodCategoryAdminService = $foodCategoryAdminService;
    }

    public function index()
    {
        return view('admin.food_category.index');
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'name',
        ]);

        $result = $this->_foodCategoryAdminService->createFoodCategory($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_foodCategoryAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('admin.food_category.show', $result->id)->with('success', 'Food category added successfully.');
    }

    public function show($id)
    {
        $foodCategory = $this->_foodCategoryAdminService->getById($id);

        if ($foodCategory == false) {
            abort(404);
        }

        if ($foodCategory == null) {
            $errorMessage = implode("<br>", $this->_foodCategoryAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return view('admin.food_category.show', compact('foodCategory'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'name',
        ]);

        $result = $this->_foodCategoryAdminService->update($id, $data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_foodCategoryAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return back()->with('success', 'Food category detail updated successfully.');
    }

    public function destroy($id)
    {
        $foodCategoryAdminService = $this->_foodCategoryAdminService->deleteById($id);

        if ($foodCategoryAdminService == null) {
            $errorMessage = implode("<br>", $this->_foodCategoryAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return redirect()->route('admin.food_category.index')->with('success', 'Food category deleted successfully.');
    }

    public function selectOption(Request $request)
    {
        $data = [
            "search_term" => $request->search_term ?? null,
            "page" => $request->page ?? 1,
        ];

        $results = $this->_foodCategoryAdminService->getSelectOption($data);
        return $results;
    }
}
