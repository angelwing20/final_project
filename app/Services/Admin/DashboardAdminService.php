<?php

namespace App\Services\Admin;

use App\Repositories\DailySalesRepository;
use App\Services\Service;

class DashboardAdminService extends Service
{
    private $_dailySalesRepository;

    public function __construct(DailySalesRepository $dailySalesRepository)
    {
        $this->_dailySalesRepository = $dailySalesRepository;
    }

    public function getIngredientChartData()
    {
        $rawData = $this->_dailySalesRepository->getMonthlyIngredientUsage();

        return [
            'labels' => $rawData->pluck('ingredient_name'),
            'values' => $rawData->pluck('total_used')->map(fn($v) => (float)$v)
        ];
    }

    public function getSalesTrendData()
    {
        $rawData = $this->_dailySalesRepository->getSalesTrendLast7Days();

        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d M');
            $values[] = $rawData->firstWhere('date', $date)->total ?? 0;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function getDashboardStats()
    {
        $stats = $this->_dailySalesRepository->getDashboardStats();

        return [
            'total_revenue' => (float)($stats['total_revenue'] ?? 0),
            'low_stock_count' => (int)($stats['low_stock_count'] ?? 0),
            'total_refill_amount' => (float)($stats['total_refill_amount'] ?? 0),
            'last_daily_sales_upload' => !empty($stats['last_daily_sales_upload'])
                ? date('d M Y', strtotime($stats['last_daily_sales_upload']))
                : '--'
        ];
    }
}
