<?php

namespace App\Services\Admin;

use App\Imports\DailySalesImport;
use App\Repositories\ProductRepository;
use App\Repositories\IngredientRepository;
use App\Repositories\ProductIngredientRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ImportDailySalesAdminService extends Service
{
    private $_productRepository;
    private $_ingredientRepository;
    private $_productIngredientRepository;

    public function __construct(
        ProductRepository $productRepository,
        IngredientRepository $ingredientRepository,
        ProductIngredientRepository $productIngredientRepository
    ) {
        $this->_productRepository = $productRepository;
        $this->_ingredientRepository = $ingredientRepository;
        $this->_productIngredientRepository = $productIngredientRepository;
    }

    public function import($data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'excel_file' => 'required|file|mimes:xlsx,csv,txt,text/plain,application/vnd.ms-excel|max:20480',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $import = new DailySalesImport();
            Excel::import($import, $data['excel_file']);

            $rows = $import->rows;
            if (!$rows || $rows->isEmpty()) {
                array_push($this->_errorMessage, "No data found in file.");
                return null;
            }

            foreach ($rows as $row) {
                $productName = trim($row['product_name'] ?? '');
                $quantity = (int)($row['quantity'] ?? 0);

                if (!$productName || $quantity <= 0) {
                    continue;
                }

                $product = $this->_productRepository->getByName($productName);

                if (!$product) {
                    array_push($this->_errorMessage, "Product [$productName] not found.");
                    return null;
                }

                $ingredients = $this->_productIngredientRepository->getByProductId($product->id);

                foreach ($ingredients as $productIngredient) {
                    $ingredient = $this->_ingredientRepository->find($productIngredient->ingredient_id);

                    if (!$ingredient) {
                        array_push($this->_errorMessage, "Ingredient [ID: {$productIngredient->ingredient_id}] not found.");
                        return null;
                    }

                    $weight = $productIngredient->weight * $quantity;

                    if ($ingredient->weight < $weight) {
                        array_push($this->_errorMessage, "Ingredient [{$ingredient->name}] not enough. Required: $weight, Available: {$ingredient->weight}");
                        return null;
                    }

                    $ingredient->weight -= $weight;
                    $this->_ingredientRepository->update($ingredient->id, ['weight' => $ingredient->weight]);
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to upload daily sales file.");

            DB::rollBack();
            return null;
        }
    }
}
