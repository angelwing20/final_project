<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\StaffAdminService;
use Illuminate\Http\Request;

class StaffAdminController extends Controller
{
    //
    private $_staffAdminService;

    public function __construct(StaffAdminService $staffAdminService)
    {
        $this->_staffAdminService = $staffAdminService;
    }

    public function index()
    {
        return view("admin.staff.index");
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'image',
            'role',
            'name',
            'email',
            'password'
        ]);

        $result = $this->_staffAdminService->createStaff($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_staffAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('admin.staff.show', $result->id)->with('success', 'Staff added successfully.');
    }

    public function show($id)
    {
        $staff = $this->_staffAdminService->getById($id);

        if ($staff == false) {
            abort(404);
        }

        if ($staff == null) {
            $errorMessage = implode("<br>", $this->_staffAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return view("admin.staff.show", compact("staff"));
    }

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'image',
            'role',
            'name',
            'email',
        ]);

        $result = $this->_staffAdminService->update($id, $data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_staffAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('admin.staff.show', $id)->with('success', 'Staff updated successfully.');
    }

    public function destroy($id)
    {
        $result = $this->_staffAdminService->deleteById($id);

        if ($result == false) {
            $errorMessage = implode("<br>", $this->_staffAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return redirect()->route('admin.staff.index')->with('success', 'Staff deleted successfully.');
    }

    public function updatePassword(Request $request, $id)
    {
        $data = $request->only([
            'password',
        ]);

        $result = $this->_staffAdminService->updatePassword($id, $data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_staffAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return back()->with('success', 'Staff password updated successfully.');
    }

    public function selectOption(Request $request)
    {
        $data = [
            "search_term" => $request->search_term ?? null,
            "page" => $request->page ?? 1,
        ];

        $results = $this->_staffAdminService->getSelectOption($data);
        return $results;
    }
}
