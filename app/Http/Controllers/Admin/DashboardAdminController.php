<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardAdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard.dashboard');
    }
}
