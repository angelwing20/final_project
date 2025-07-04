<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ProfileAdminService;
use Illuminate\Http\Request;

class ProfileAdminController extends Controller
{
    private $_profileAdminService;

    public function __construct(ProfileAdminService $profileAdminService)
    {
        $this->_profileAdminService = $profileAdminService;
    }

    public function index()
    {
        $user = $this->_profileAdminService->getProfile();
        return view('admin.account.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $data = $request->only([
            'name',
            'image',
            'email'
        ]);

        $result = $this->_profileAdminService->update($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_profileAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return back()->with('success', 'Account profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->only([
            'current_password',
            'password',
            'password_confirmation'
        ]);

        $result = $this->_profileAdminService->updatePassword($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_profileAdminService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return back()->with('success', 'Account password updated successfully.');
    }
}
