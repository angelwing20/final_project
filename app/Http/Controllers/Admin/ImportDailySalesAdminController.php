<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ImportDailySalesAdminService;
use Illuminate\Http\Request;

class ImportDailySalesAdminController extends Controller
{
    private $_importDailySalesAdminService;

    public function __construct(ImportDailySalesAdminService $importDailySalesAdminService)
    {
        $this->_importDailySalesAdminService = $importDailySalesAdminService;
    }

    public function importDailySales(Request $request)
    {
        $data = $request->only([
            'excel_file',
        ]);

        $result = $this->_importDailySalesAdminService->import($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_importDailySalesAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return back()->with('success', 'Daily sales file upload successfully.');
    }
}
