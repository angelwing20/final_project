<?php

namespace App\Services\Admin;

use App\Repositories\DailySalesRepository;
use App\Services\Service;
use Exception;

class DailySalesAdminService extends Service
{
    private $_dailySalesRepository;

    public function __construct(
        DailySalesRepository $dailySalesRepository
    ) {
        $this->_dailySalesRepository = $dailySalesRepository;
    }

    public function getById($id)
    {
        try {
            $dailySales = $this->_dailySalesRepository->getById($id);

            return $dailySales;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get daily sales.");

            return null;
        }
    }
}
