<?php

namespace App\Services;

use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthService extends Service
{
    public function login($data)
    {
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                array_push($this->_errorMessage, $error);
            }
            return null;
        }

        if (!Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ])) {
            array_push($this->_errorMessage, "Invalid email or password.");
            return null;
        }

        return true;
    }

    public function logout()
    {
        try {
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();

            return true;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Logout failed!");
            return null;
        }
    }
}
