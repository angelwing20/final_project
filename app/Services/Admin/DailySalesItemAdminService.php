<?php

namespace App\Services\Admin;

use App\Repositories\AddOnIngredientRepository;
use App\Repositories\AddOnRepository;
use App\Repositories\DailySalesRepository;
use App\Repositories\DailySalesItemRepository;
use App\Repositories\IngredientRepository;
use App\Repositories\ProductIngredientRepository;
use App\Repositories\ProductRepository;
use App\Services\Service;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DailySalesItemAdminService extends Service
{
    private $_dailySalesRepository;
    private $_dailySalesItemRepository;
    private $_productRepository;
    private $_addOnRepository;
    private $_productIngredientRepository;
    private $_addOnIngredientRepository;
    private $_ingredientRepository;

    public function __construct(
        DailySalesRepository $dailySalesRepository,
        DailySalesItemRepository $dailySalesItemRepository,
        ProductRepository $productRepository,
        AddOnRepository $addOnRepository,
        ProductIngredientRepository $productIngredientRepository,
        AddOnIngredientRepository $addOnIngredientRepository,
        IngredientRepository $ingredientRepository
    ) {
        $this->_dailySalesRepository = $dailySalesRepository;
        $this->_dailySalesItemRepository = $dailySalesItemRepository;
        $this->_productRepository = $productRepository;
        $this->_addOnRepository = $addOnRepository;
        $this->_productIngredientRepository = $productIngredientRepository;
        $this->_addOnIngredientRepository = $addOnIngredientRepository;
        $this->_ingredientRepository = $ingredientRepository;
    }

    public function getAllProductsAndAddOns()
    {
        try {
            $products = $this->_productRepository->getAll();
            $addons = $this->_addOnRepository->getAll();

            if ($products == null && $addons == null) {
                throw new Exception();
            }

            return [
                'products' => $products,
                'addons' => $addons
            ];
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get products and add-ons.");
            return null;
        }
    }

    public function getByDailySalesId($id)
    {
        try {
            $items = $this->_dailySalesItemRepository->getByDailySalesId($id);

            if (!$items || count($items) === 0) {
                throw new Exception();
            }

            foreach ($items as $item) {
                if ($item->item_type === 'product') {
                    $product = $this->_productRepository->getById($item->item_id);
                    $item->name = $product ? $product->name : 'Unknown Product';
                } else {
                    $addon = $this->_addOnRepository->getById($item->item_id);
                    $item->name = $addon ? $addon->name : 'Unknown Addon';
                }
            }

            return $items;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get daily sales items.");
            return null;
        }
    }

    public function createDailySales(array $data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'products' => 'nullable|array',
                'addons' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $totalQuantity = 0;
            $totalAmount = 0;
            $items = [];

            $hasInsufficientStock = false;

            $ingredientUpdates = [];

            if (!empty($data['products'])) {
                foreach ($data['products'] as $id => $item) {
                    $qty = intval($item['quantity']);

                    if ($qty > 0) {
                        $product = $this->_productRepository->getById($id);
                        if (!$product) continue;

                        $price = $product->price;
                        $amount = $qty * $price;

                        $totalQuantity += $qty;
                        $totalAmount += $amount;

                        $items[] = [
                            'item_type' => 'product',
                            'item_id' => $id,
                            'quantity' => $qty,
                            'price' => $price,
                            'amount' => $amount,
                        ];

                        $productIngredients = $this->_productIngredientRepository->getByProductId($id);

                        foreach ($productIngredients as $pi) {
                            $ingredient = $this->_ingredientRepository->getById($pi->ingredient_id);
                            if ($ingredient) {
                                $requiredWeight = $pi->weight * $qty;

                                if ($ingredient->weight === null || $ingredient->weight < $requiredWeight) {
                                    array_push($this->_errorMessage, "Insufficient stock for ingredient: " . $ingredient->name);
                                    $hasInsufficientStock = true;
                                } else {
                                    $ingredientUpdates[] = [
                                        'id' => $ingredient->id,
                                        'newWeight' => $ingredient->weight - $requiredWeight
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($data['addons'])) {
                foreach ($data['addons'] as $id => $item) {
                    $qty = intval($item['quantity']);

                    if ($qty > 0) {
                        $addon = $this->_addOnRepository->getById($id);
                        if (!$addon) continue;

                        $price = $addon->price;
                        $amount = $qty * $price;

                        $totalQuantity += $qty;
                        $totalAmount += $amount;

                        $items[] = [
                            'item_type' => 'addon',
                            'item_id' => $id,
                            'quantity' => $qty,
                            'price' => $price,
                            'amount' => $amount,
                        ];

                        $addonIngredients = $this->_addOnIngredientRepository->getByAddOnId($id);

                        foreach ($addonIngredients as $ai) {
                            $ingredient = $this->_ingredientRepository->getById($ai->ingredient_id);
                            if ($ingredient) {
                                $requiredWeight = $ai->weight * $qty;

                                if ($ingredient->weight === null || $ingredient->weight < $requiredWeight) {
                                    array_push($this->_errorMessage, "Insufficient stock for ingredient: " . $ingredient->name);
                                    $hasInsufficientStock = true;
                                } else {
                                    $ingredientUpdates[] = [
                                        'id' => $ingredient->id,
                                        'newWeight' => $ingredient->weight - $requiredWeight
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            if ($totalQuantity === 0) {
                array_push($this->_errorMessage, "Please select at least one product or addon.");
                return null;
            }

            if ($hasInsufficientStock) {
                DB::rollBack();
                return null;
            }

            foreach ($ingredientUpdates as $update) {
                $this->_ingredientRepository->updateWeight($update['id'], $update['newWeight']);
            }

            $dailySales = $this->_dailySalesRepository->save([
                'total_quantity' => $totalQuantity,
                'total_amount' => $totalAmount,
                'staff_id' => Auth::id(),
            ]);

            if (!$dailySales) {
                throw new Exception();
            }

            foreach ($items as &$item) {
                $item['daily_sales_id'] = $dailySales->id;
            }

            $this->_dailySalesItemRepository->bulkSave($items);

            DB::commit();
            return $dailySales;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to create daily sales.");
            DB::rollBack();
            return null;
        }
    }

    public function getEditData($id)
    {
        try {
            $dailySales = $this->_dailySalesRepository->getById($id);
            if (!$dailySales) {
                throw new Exception("Daily sales not found.");
            }

            $dailySalesItems = $this->_dailySalesItemRepository->getByDailySalesId($id);
            if (!$dailySalesItems || count($dailySalesItems) === 0) {
                throw new Exception("Daily sales items not found.");
            }

            foreach ($dailySalesItems as $item) {
                if ($item->item_type === 'product') {
                    $product = $this->_productRepository->getById($item->item_id);
                    $item->name = $product ? $product->name : 'Unknown Product';
                } else {
                    $addon = $this->_addOnRepository->getById($item->item_id);
                    $item->name = $addon ? $addon->name : 'Unknown Addon';
                }
            }

            $products = $this->_productRepository->getAll();
            $addons = $this->_addOnRepository->getAll();

            return [
                'dailySales' => $dailySales,
                'dailySalesItems' => $dailySalesItems,
                'products' => $products,
                'addons' => $addons
            ];
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get edit data for daily sales.");
            return null;
        }
    }

    public function updateDailySales($id, array $data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'products' => 'nullable|array',
                'addons' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $this->_errorMessage[] = $error;
                }
                return false;
            }

            $dailySales = $this->_dailySalesRepository->getById($id);
            if (!$dailySales) {
                $this->_errorMessage[] = "Daily Sales not found.";
                return false;
            }

            $oldItems = $this->_dailySalesItemRepository->getByDailySalesId($id);

            foreach ($oldItems as $item) {
                if ($item->item_type === 'product') {
                    $productIngredients = $this->_productIngredientRepository->getByProductId($item->item_id);
                    foreach ($productIngredients as $pi) {
                        $ingredient = $this->_ingredientRepository->getById($pi->ingredient_id);
                        if ($ingredient) {
                            $newWeight = $ingredient->weight + ($pi->weight * $item->quantity);
                            $this->_ingredientRepository->updateWeight($ingredient->id, $newWeight);
                        }
                    }
                } else {
                    $addonIngredients = $this->_addOnIngredientRepository->getByAddOnId($item->item_id);
                    foreach ($addonIngredients as $ai) {
                        $ingredient = $this->_ingredientRepository->getById($ai->ingredient_id);
                        if ($ingredient) {
                            $newWeight = $ingredient->weight + ($ai->weight * $item->quantity);
                            $this->_ingredientRepository->updateWeight($ingredient->id, $newWeight);
                        }
                    }
                }
            }

            $totalQuantity = 0;
            $totalAmount = 0;
            $items = [];
            $ingredientUpdates = [];
            $hasInsufficientStock = false;

            if (!empty($data['products'])) {
                foreach ($data['products'] as $productId => $item) {
                    $qty = intval($item['quantity']);
                    if ($qty > 0) {
                        $product = $this->_productRepository->getById($productId);
                        if (!$product) continue;

                        $price = $product->price;
                        $amount = $qty * $price;

                        $totalQuantity += $qty;
                        $totalAmount += $amount;

                        $items[] = [
                            'item_type' => 'product',
                            'item_id' => $productId,
                            'quantity' => $qty,
                            'price' => $price,
                            'amount' => $amount,
                            'daily_sales_id' => $id,
                        ];

                        $productIngredients = $this->_productIngredientRepository->getByProductId($productId);
                        foreach ($productIngredients as $pi) {
                            $ingredient = $this->_ingredientRepository->getById($pi->ingredient_id);
                            if ($ingredient) {
                                $requiredWeight = $pi->weight * $qty;

                                if ($ingredient->weight === null || $ingredient->weight < $requiredWeight) {
                                    $this->_errorMessage[] = "Insufficient stock for ingredient: " . $ingredient->name;
                                    $hasInsufficientStock = true;
                                } else {
                                    $ingredientUpdates[] = [
                                        'id' => $ingredient->id,
                                        'newWeight' => $ingredient->weight - $requiredWeight
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($data['addons'])) {
                foreach ($data['addons'] as $addonId => $item) {
                    $qty = intval($item['quantity']);
                    if ($qty > 0) {
                        $addon = $this->_addOnRepository->getById($addonId);
                        if (!$addon) continue;

                        $price = $addon->price;
                        $amount = $qty * $price;

                        $totalQuantity += $qty;
                        $totalAmount += $amount;

                        $items[] = [
                            'item_type' => 'addon',
                            'item_id' => $addonId,
                            'quantity' => $qty,
                            'price' => $price,
                            'amount' => $amount,
                            'daily_sales_id' => $id,
                        ];

                        $addonIngredients = $this->_addOnIngredientRepository->getByAddOnId($addonId);
                        foreach ($addonIngredients as $ai) {
                            $ingredient = $this->_ingredientRepository->getById($ai->ingredient_id);
                            if ($ingredient) {
                                $requiredWeight = $ai->weight * $qty;

                                if ($ingredient->weight === null || $ingredient->weight < $requiredWeight) {
                                    $this->_errorMessage[] = "Insufficient stock for ingredient: " . $ingredient->name;
                                    $hasInsufficientStock = true;
                                } else {
                                    $ingredientUpdates[] = [
                                        'id' => $ingredient->id,
                                        'newWeight' => $ingredient->weight - $requiredWeight
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            if ($totalQuantity === 0) {
                $this->_errorMessage[] = "Please select at least one product or addon.";
                return false;
            }

            if ($hasInsufficientStock) {
                DB::rollBack();
                return false;
            }

            foreach ($ingredientUpdates as $update) {
                $this->_ingredientRepository->updateWeight($update['id'], $update['newWeight']);
            }

            $this->_dailySalesRepository->update($id, [
                'total_quantity' => $totalQuantity,
                'total_amount' => $totalAmount,
                'staff_id' => Auth::id(),
            ]);

            $this->_dailySalesItemRepository->deleteByDailySalesId($id);
            $this->_dailySalesItemRepository->bulkSave($items);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            $this->_errorMessage[] = "Fail to update daily sales.";
            return false;
        }
    }
}
