<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class RefillStockHistoryAdminController extends Controller
{
    public function index()
    {
        return view('admin.refill_stock_history.index');
    }
}
