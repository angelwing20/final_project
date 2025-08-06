<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\FoodAdminService;

class FoodAdminController extends Controller
{
    private $_foodAdminService;

    public function __construct(FoodAdminService $foodAdminService)
    {
        $this->_foodAdminService = $foodAdminService;
    }

    public function index()
    {
        return view('admin.food.index');
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'food_category_id',
            'name',
            'price',
            'description',
            'image'
        ]);

        $result = $this->_foodAdminService->createFood($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_foodAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('admin.food.show', $result->id)->with('success', 'Food added successfully.');
    }

    public function show($id)
    {
        $food = $this->_foodAdminService->getById($id);

        if ($food == false) {
            abort(404);
        }

        if ($food == null) {
            $errorMessage = implode("<br>", $this->_foodAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return view('admin.food.show', compact('food'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'food_category_id',
            'name',
            'price',
            'description',
            'image'
        ]);

        $result = $this->_foodAdminService->update($id, $data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_foodAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return back()->with('success', 'Food updated successfully');
    }

    public function destroy($id)
    {
        $result = $this->_foodAdminService->deleteById($id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_foodAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return redirect()->route('admin.food.index')->with('success', 'Food deleted successfully.');
    }
}
