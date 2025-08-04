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

    public function getIngredientConsumptionTableData($dailySalesId)
    {
        $ingredientConsumptionRows = $this->_dailySalesRepository->getIngredientConsumptionByDailySalesId($dailySalesId);

        $ingredients = [];
        $totalIngredientCost = 0;

        foreach ($ingredientConsumptionRows as $ingredientRow) {
            $ingredients[] = [
                'name' => $ingredientRow->ingredient_name,
                'weight' => (float) $ingredientRow->total_weight,
                'amount' => (float) $ingredientRow->total_amount,
                'unit_type' => $ingredientRow->unit_type,
                'weight_unit' => (float) $ingredientRow->weight_unit,
            ];
            $totalIngredientCost += (float) $ingredientRow->total_amount;
        }

        return [
            'ingredients' => $ingredients,
            'total_amount' => $totalIngredientCost
        ];
    }
}
