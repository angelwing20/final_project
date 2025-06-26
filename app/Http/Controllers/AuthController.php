<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    private $_authService;

    public function __construct(AuthService $authService)
    {
        $this->_authService = $authService;
    }

    public function loginPage()
    {
        return view('public.login');
    }

    public function login(Request $request)
    {
        $data = $request->only([
            'email',
            'password',
        ]);

        $result = $this->_authService->login($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_authService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $result = $this->_authService->logout();

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_authService->_errorMessage);
            return back()->with('error', $errorMessage);
        }

        return redirect()->route('login.index');
    }
}
