<?php

namespace App\Http\Controllers;

use App\Services\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PasswordResetController extends Controller
{
    private $_passwordResetService;

    public function __construct(
        PasswordResetService $passwordResetService
    ) {
        $this->_passwordResetService = $passwordResetService;
    }

    public function index($token, $email)
    {
        $result = $this->_passwordResetService->validateTokenAndEmail($token, $email);

        return view('public.reset_password');
    }

    public function passwordReset(Request $request)
    {
        $data = $request->only([
            'email',
            'token',
            'password',
            'password_confirmation',
        ]);

        $result = $this->_passwordResetService->passwordReset($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_passwordResetService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('login.index')->with('success', "Password has been successfully reset.");
    }
}
