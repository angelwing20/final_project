<?php

namespace App\Services\Admin;

use App\Imports\DailySalesImport;
use App\Repositories\AddOnIngredientRepository;
use App\Repositories\AddOnRepository;
use App\Repositories\DailySalesItemRepository;
use App\Repositories\DailySalesRepository;
use App\Repositories\ProductRepository;
use App\Repositories\IngredientRepository;
use App\Repositories\ProductIngredientRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ImportDailySalesAdminService extends Service
{
    private $_productRepository;
    private $_ingredientRepository;
    private $_productIngredientRepository;
    private $_dailySalesRepository;
    private $_dailySalesItemRepository;
    private $_addOnRepository;
    private $_addOnIngredientRepository;

    public function __construct(
        ProductRepository $productRepository,
        IngredientRepository $ingredientRepository,
        ProductIngredientRepository $productIngredientRepository,
        DailySalesRepository $dailySalesRepository,
        DailySalesItemRepository $dailySalesItemRepository,
        AddOnRepository $addOnRepository,
        AddOnIngredientRepository $addOnIngredientRepository
    ) {
        $this->_productRepository = $productRepository;
        $this->_ingredientRepository = $ingredientRepository;
        $this->_productIngredientRepository = $productIngredientRepository;
        $this->_dailySalesRepository = $dailySalesRepository;
        $this->_dailySalesItemRepository = $dailySalesItemRepository;
        $this->_addOnRepository = $addOnRepository;
        $this->_addOnIngredientRepository = $addOnIngredientRepository;
    }

    public function import($file)
    {
        try {
            $validator = Validator::make(['excel_file' => $file], [
                'excel_file' => 'required|file|mimes:xlsx,csv,txt,text/plain,application/vnd.ms-excel|max:20480',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return false;
            }

            $import = new DailySalesImport();
            Excel::import($import, $file);
            $rows = $import->rows;

            if (!$rows || $rows->isEmpty()) {
                array_push($this->_errorMessage, "No data found in file.");
                return false;
            }

            $validatedItems = [];
            $totalQty = 0;
            $totalAmount = 0;

            foreach ($rows as $index => $row) {
                $itemName = trim($row['item_name'] ?? '');
                $itemType = strtolower(trim($row['item_type'] ?? ''));
                $quantity = (int)($row['quantity'] ?? 0);

                if (!$itemName || !$itemType || $quantity <= 0) {
                    array_push($this->_errorMessage, "Row " . ($index + 1) . " is invalid. [item_name, item_type, quantity required]");
                    continue;
                }

                if (!in_array($itemType, ['product', 'addon'])) {
                    array_push($this->_errorMessage, "Invalid item type [$itemType] for [$itemName] (row " . ($index + 1) . ")");
                    continue;
                }

                $item = $itemType === 'product'
                    ? $this->_productRepository->getByName($itemName)
                    : $this->_addOnRepository->getByName($itemName);

                if (!$item || empty($item->id)) {
                    array_push($this->_errorMessage, "Item [$itemName] with type [$itemType] not found (row " . ($index + 1) . ").");
                    continue;
                }

                $ingredients = $itemType === 'product'
                    ? $this->_productIngredientRepository->getByProductId($item->id)
                    : $this->_addOnIngredientRepository->getByAddOnId($item->id);

                $price = $item->price;
                $amount = $price * $quantity;

                foreach ($ingredients as $ingredientLink) {
                    $ingredient = $this->_ingredientRepository->getById($ingredientLink->ingredient_id);
                    $weightNeeded = $ingredientLink->weight * $quantity;

                    if ($ingredient->stock_weight < $weightNeeded) {
                        array_push($this->_errorMessage, "Ingredient [{$ingredient->name}] stock not enough (row " . ($index + 1) . ").");
                        continue 2;
                    }
                }

                $validatedItems[] = compact('item', 'itemType', 'quantity', 'price', 'amount', 'ingredients');
                $totalQty += $quantity;
                $totalAmount += $amount;
            }

            if (!empty($this->_errorMessage)) {
                return false;
            }

            DB::beginTransaction();
            try {
                $dailySales = $this->_dailySalesRepository->save([
                    'total_quantity' => $totalQty,
                    'total_amount' => $totalAmount,
                    'staff_id' => Auth::id()
                ]);

                foreach ($validatedItems as $data) {
                    $this->_dailySalesItemRepository->save([
                        'daily_sales_id' => $dailySales->id,
                        'item_type' => $data['itemType'],
                        'item_id' => $data['item']->id,
                        'quantity' => $data['quantity'],
                        'price' => $data['price'],
                        'amount' => $data['amount'],
                    ]);

                    foreach ($data['ingredients'] as $ingredientLink) {
                        $ingredient = $this->_ingredientRepository->getById($ingredientLink->ingredient_id);
                        $ingredient->stock_weight -= $ingredientLink->weight * $data['quantity'];
                        $this->_ingredientRepository->update($ingredient->id, ['stock_weight' => $ingredient->stock_weight]);
                    }
                }

                DB::commit();
                return true;
            } catch (Exception $e) {
                DB::rollBack();
                array_push($this->_errorMessage, "Transaction failed: " . $e->getMessage());
                return false;
            }
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to upload daily sales file: " . $e->getMessage());
            return false;
        }
    }
}
