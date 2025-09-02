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
        $model->date = $data['date'];
        $model->total_quantity = $data['total_quantity'];
        $model->total_amount = $data['total_amount'];
        $model->staff_id = $data['staff_id'];

        $model->save();
        return $model->fresh();
    }

    public function update($id, $data)
    {
        $model = $this->_db->find($id);
        $model->date = $data['date'] ?? $model->date;
        $model->total_quantity = $data['total_quantity'] ?? $model->total_quantity;
        $model->total_amount = $data['total_amount'] ?? $model->total_amount;
        $model->staff_id = $data['staff_id'] ?? $model->staff_id;

        $model->update();
        return $model;
    }

    public function getMonthlyIngredientConsumption()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        return DB::table('ingredients as ingredients')
            ->leftJoin('food_ingredients as food_ingredients', 'food_ingredients.ingredient_id', '=', 'ingredients.id')
            ->leftJoin('add_on_ingredients as add_on_ingredients', 'add_on_ingredients.ingredient_id', '=', 'ingredients.id')
            ->leftJoin('daily_sales_items as daily_sales_items', function ($join) {
                $join->on(function ($query) {
                    $query->on('daily_sales_items.item_id', '=', 'food_ingredients.food_id')
                        ->where('daily_sales_items.item_type', '=', 'food');
                })->orOn(function ($query) {
                    $query->on('daily_sales_items.item_id', '=', 'add_on_ingredients.add_on_id')
                        ->where('daily_sales_items.item_type', '=', 'addon');
                });
            })
            ->leftJoin('daily_sales as daily_sales', 'daily_sales.id', '=', 'daily_sales_items.daily_sales_id')
            ->whereBetween('daily_sales.date', [$startDate, $endDate])
            ->select(
                'ingredients.name as ingredient_name',
                DB::raw('
                    COALESCE(SUM(CASE WHEN daily_sales_items.item_type = "food"
                                      THEN food_ingredients.consumption * daily_sales_items.quantity
                                      ELSE 0 END), 0)
                    +
                    COALESCE(SUM(CASE WHEN daily_sales_items.item_type = "addon"
                                      THEN add_on_ingredients.consumption * daily_sales_items.quantity
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
            ->selectRaw('date, SUM(total_amount) as total')
            ->where('date', '>=', now()->subDays(6)->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getDashboardStats()
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        $totalRevenue = DB::table('daily_sales')
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('total_amount');

        $lowStockCount = DB::table('ingredients')
            ->whereColumn('stock', '<=', 'min_stock')
            ->count();

        $totalRefillAmount = DB::table('refill_stock_histories')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $lastDailySalesUpload = DB::table('daily_sales')->max('date');

        return [
            'total_revenue' => $totalRevenue,
            'low_stock_count' => $lowStockCount,
            'total_refill_amount' => $totalRefillAmount,
            'last_daily_sales_upload' => $lastDailySalesUpload
        ];
    }

    public function getIngredientConsumptionByDailySalesId($dailySalesId)
    {
        return DB::table('daily_sales_items as daily_sales_items')
            ->join('daily_sales as daily_sales', 'daily_sales.id', '=', 'daily_sales_items.daily_sales_id')
            ->leftJoin('food_ingredients as food_ingredients', function ($join) {
                $join->on('food_ingredients.food_id', '=', 'daily_sales_items.item_id')
                    ->where('daily_sales_items.item_type', '=', 'food');
            })
            ->leftJoin('add_on_ingredients as add_on_ingredients', function ($join) {
                $join->on('add_on_ingredients.add_on_id', '=', 'daily_sales_items.item_id')
                    ->where('daily_sales_items.item_type', '=', 'addon');
            })
            ->leftJoin('ingredients as ingredients', function ($join) {
                $join->on('ingredients.id', '=', DB::raw('COALESCE(food_ingredients.ingredient_id, add_on_ingredients.ingredient_id)'));
            })
            ->select(
                'ingredients.name as ingredient_name',
                'ingredients.unit_type',
                'ingredients.weight_unit',
                'ingredients.price',
                DB::raw('SUM((COALESCE(food_ingredients.consumption, add_on_ingredients.consumption) * daily_sales_items.quantity)) as total_weight'),
                DB::raw('SUM(((COALESCE(food_ingredients.consumption, add_on_ingredients.consumption) * daily_sales_items.quantity) / ingredients.weight_unit) * ingredients.price) as total_amount')
            )
            ->where('daily_sales_items.daily_sales_id', $dailySalesId)
            ->whereNotNull('ingredients.name')
            ->groupBy('ingredients.name', 'ingredients.unit_type', 'ingredients.weight_unit', 'ingredients.price')
            ->orderBy('ingredients.name', 'asc')
            ->get();
    }

    public function getByDate($date)
    {
        return DailySales::where('date', $date)->first();
    }
}
