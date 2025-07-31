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
        $files = $request->file('excel_file');

        if (!$files) {
            return back()->with('error', 'Please upload at least one file.');
        }

        $files = is_array($files) ? $files : [$files];

        $successFiles = [];
        $failedFiles = [];

        foreach ($files as $file) {
            $result = $this->_importDailySalesAdminService->import($file);

            if ($result) {
                $successFiles[] = $file->getClientOriginalName();
            } else {
                $failedFiles[] = [
                    'file' => $file->getClientOriginalName(),
                    'errors' => $this->_importDailySalesAdminService->_errorMessage
                ];
            }

            $this->_importDailySalesAdminService->_errorMessage = [];
        }

        return back()->with([
            'successFiles' => $successFiles,
            'failedFiles' => $failedFiles
        ]);
    }
}
