<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaffAdminController extends Controller
{
    //
    public function index()
    {
        return view("admin.staff.index");
    }

    public function show($id)
    {
        return view("admin.staff.show");
    }
}
