<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AddOnAdminService;
use Illuminate\Http\Request;

class AddOnAdminController extends Controller
{
    private $_addOnAdminService;

    public function __construct(AddOnAdminService $addOnAdminService)
    {
        $this->_addOnAdminService = $addOnAdminService;
    }

    public function index()
    {
        return view('admin.add_on.index');
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'image',
            'name',
            'price',
        ]);

        $result = $this->_addOnAdminService->createAddOn($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_addOnAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('admin.add_on.show', $result->id)->with('success', 'Add-on added successfully.');
    }

    public function show($id)
    {
        $addOn = $this->_addOnAdminService->getById($id);

        if ($addOn == false) {
            abort(404);
        }

        if ($addOn == null) {
            $errorMessage = implode("<br>", $this->_addOnAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return view('admin.add_on.show', compact('addOn'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'image',
            'name',
            'price',
        ]);

        $result = $this->_addOnAdminService->update($id, $data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_addOnAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return back()->with('success', 'Add-on detail updated successfully.');
    }

    public function destroy($id)
    {
        $addOn = $this->_addOnAdminService->deleteById($id);

        if ($addOn == null) {
            $errorMessage = implode("<br>", $this->_addOnAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return redirect()->route('admin.add_on.index')->with('success', 'Add-on deleted successfully.');
    }

    public function selectOption(Request $request)
    {
        $data = [
            "search_term" => $request->search_term ?? null,
            "page" => $request->page ?? 1,
        ];

        $results = $this->_addOnAdminService->getSelectOption($data);
        return $results;
    }
}
