<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileAdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('admin.account.index', compact('user'));
    }
}
