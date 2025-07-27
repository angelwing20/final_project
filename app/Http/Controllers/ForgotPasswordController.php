<?php

namespace App\Http\Controllers;

use App\Services\ForgotPasswordService;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    private $_forgotPasswordService;

    public function __construct(
        ForgotPasswordService $forgotPasswordService
    ) {
        $this->_forgotPasswordService = $forgotPasswordService;
    }

    public function index()
    {
        return view('public.forgot_password');
    }

    public function forgotPassword(Request $request)
    {
        $data = $request->only([
            'email',
        ]);

        $result = $this->_forgotPasswordService->forgotPassword($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_forgotPasswordService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('login.index')->with('success', "A password reset link has been sent to your email. Please check your inbox.");
    }
}
