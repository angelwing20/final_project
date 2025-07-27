<?php

namespace App\Repositories;

use App\Models\DailySales;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailySalesRepository extends Repository
{
    protected $_db;

    public function __construct(DailySales $dailySales)
    {
        $this->_db = $dailySales;
    }

    public function save($data)
    {
        $model = new DailySales();
        $model->total_quantity = $data['total_quantity'];
        $model->total_amount = $data['total_amount'];
        $model->staff_id = $data['staff_id'];

        $model->save();
        return $model->fresh();
    }

    public function update($id, $data)
    {
        $model = $this->_db->find($id);
        $model->total_quantity = $data['total_quantity'] ?? $model->total_quantity;
        $model->total_amount = $data['total_amount'] ?? $model->total_amount;
        $model->staff_id = $data['staff_id'] ?? $model->staff_id;

        $model->update();
        return $model;
    }

    public function getMonthlyIngredientUsage()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        return DB::table('ingredients as ingredients')
            ->leftJoin('product_ingredients as product_ingredients', 'product_ingredients.ingredient_id', '=', 'ingredients.id')
            ->leftJoin('add_on_ingredients as add_on_ingredients', 'add_on_ingredients.ingredient_id', '=', 'ingredients.id')
            ->leftJoin('daily_sales_items as daily_sales_items', function ($join) {
                $join->on(function ($query) {
                    $query->on('daily_sales_items.item_id', '=', 'product_ingredients.product_id')
                        ->where('daily_sales_items.item_type', '=', 'product');
                })->orOn(function ($query) {
                    $query->on('daily_sales_items.item_id', '=', 'add_on_ingredients.add_on_id')
                        ->where('daily_sales_items.item_type', '=', 'addon');
                });
            })
            ->leftJoin('daily_sales as daily_sales', 'daily_sales.id', '=', 'daily_sales_items.daily_sales_id')
            ->whereBetween('daily_sales.created_at', [$startDate, $endDate])
            ->select(
                'ingredients.name as ingredient_name',
                DB::raw('
                    COALESCE(SUM(CASE WHEN daily_sales_items.item_type = "product"
                                      THEN product_ingredients.weight * daily_sales_items.quantity
                                      ELSE 0 END), 0)
                    +
                    COALESCE(SUM(CASE WHEN daily_sales_items.item_type = "addon"
                                      THEN add_on_ingredients.weight * daily_sales_items.quantity
                                      ELSE 0 END), 0)
                    as total_used
                ')
            )
            ->groupBy('ingredients.name')
            ->orderBy('ingredients.name', 'asc')
            ->get();
    }

    public function getSalesTrendLast7Days()
    {
        return DB::table('daily_sales')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getDashboardStats()
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        $totalRevenue = DB::table('daily_sales')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $lowStockCount = DB::table('ingredients')
            ->whereColumn('weight', '<=', 'alarm_weight')
            ->count();

        return [
            'total_revenue' => $totalRevenue,
            'low_stock_count' => $lowStockCount
        ];
    }

    public function getIngredientUsageByDailySalesId($dailySalesId)
    {
        return DB::table('daily_sales_items as daily_sales_items')
            ->join('daily_sales as daily_sales', 'daily_sales.id', '=', 'daily_sales_items.daily_sales_id')
            ->leftJoin('product_ingredients as product_ingredients', function ($join) {
                $join->on('product_ingredients.product_id', '=', 'daily_sales_items.item_id')
                    ->where('daily_sales_items.item_type', '=', 'product');
            })
            ->leftJoin('add_on_ingredients as add_on_ingredients', function ($join) {
                $join->on('add_on_ingredients.add_on_id', '=', 'daily_sales_items.item_id')
                    ->where('daily_sales_items.item_type', '=', 'addon');
            })
            ->leftJoin('ingredients as ingredients', function ($join) {
                $join->on('ingredients.id', '=', DB::raw('COALESCE(product_ingredients.ingredient_id, add_on_ingredients.ingredient_id)'));
            })
            ->select(
                'ingredients.name as ingredient_name',
                'ingredients.unit_price as ingredient_unit_price',
                DB::raw('SUM((COALESCE(product_ingredients.weight, add_on_ingredients.weight) * daily_sales_items.quantity)) as total_weight'),
                DB::raw('SUM((COALESCE(product_ingredients.weight, add_on_ingredients.weight) * daily_sales_items.quantity) * ingredients.unit_price) as total_amount')
            )
            ->where('daily_sales_items.daily_sales_id', $dailySalesId)
            ->whereNotNull('ingredients.name')
            ->groupBy('ingredients.name', 'ingredients.unit_price')
            ->orderBy('ingredients.name', 'asc')
            ->get();
    }
}
