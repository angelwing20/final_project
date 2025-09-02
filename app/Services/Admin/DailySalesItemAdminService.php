<?php

namespace App\Services\Admin;

use App\Repositories\AddOnIngredientRepository;
use App\Repositories\AddOnRepository;
use App\Repositories\DailySalesRepository;
use App\Repositories\DailySalesItemRepository;
use App\Repositories\IngredientRepository;
use App\Repositories\FoodIngredientRepository;
use App\Repositories\FoodRepository;
use App\Services\Service;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DailySalesItemAdminService extends Service
{
    private $_dailySalesRepository;
    private $_dailySalesItemRepository;
    private $_foodRepository;
    private $_addOnRepository;
    private $_foodIngredientRepository;
    private $_addOnIngredientRepository;
    private $_ingredientRepository;

    public function __construct(
        DailySalesRepository $dailySalesRepository,
        DailySalesItemRepository $dailySalesItemRepository,
        FoodRepository $foodRepository,
        AddOnRepository $addOnRepository,
        FoodIngredientRepository $foodIngredientRepository,
        AddOnIngredientRepository $addOnIngredientRepository,
        IngredientRepository $ingredientRepository
    ) {
        $this->_dailySalesRepository = $dailySalesRepository;
        $this->_dailySalesItemRepository = $dailySalesItemRepository;
        $this->_foodRepository = $foodRepository;
        $this->_addOnRepository = $addOnRepository;
        $this->_foodIngredientRepository = $foodIngredientRepository;
        $this->_addOnIngredientRepository = $addOnIngredientRepository;
        $this->_ingredientRepository = $ingredientRepository;
    }

    public function getAllFoodsAndAddOns()
    {
        try {
            $foods = $this->_foodRepository->getAll();
            $addons = $this->_addOnRepository->getAll();

            if ($foods == null && $addons == null) {
                throw new Exception();
            }

            return [
                'foods' => $foods,
                'addons' => $addons
            ];
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get foods and add-ons.");
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
                if ($item->item_type === 'food') {
                    $food = $this->_foodRepository->getById($item->item_id);
                    $item->name = $food ? $food->name : 'Unknown Food';
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
                'date' => 'required|date',
                'foods' => 'nullable|array',
                'addons' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $exists = $this->_dailySalesRepository->getByDate($data['date']);
            if ($exists) {
                array_push($this->_errorMessage, "Daily sales for this date already exists.");
                DB::rollBack();
                return null;
            }

            $totalQuantity = 0;
            $totalAmount = 0;
            $items = [];

            $hasInsufficientStock = false;
            $ingredientUpdates = [];

            if (!empty($data['foods'])) {
                foreach ($data['foods'] as $id => $item) {
                    $quantity = intval($item['quantity']);

                    if ($quantity > 0) {
                        $food = $this->_foodRepository->getById($id);
                        if (!$food) {
                            continue;
                        }

                        $price = $food->price;
                        $amount = $quantity * $price;

                        $totalQuantity += $quantity;
                        $totalAmount += $amount;

                        $items[] = [
                            'item_type' => 'food',
                            'item_id' => $id,
                            'quantity' => $quantity,
                            'price' => $price,
                            'amount' => $amount,
                        ];

                        $foodIngredients = $this->_foodIngredientRepository->getByFoodId($id);
                        foreach ($foodIngredients as $foodIngredient) {
                            $ingredientId = $foodIngredient->ingredient_id;
                            $requiredWeight = $foodIngredient->consumption * $quantity;

                            if (!isset($ingredientUpdates[$ingredientId])) {
                                $ingredient = $this->_ingredientRepository->getById($ingredientId);
                                $ingredientUpdates[$ingredientId] = [
                                    'id' => $ingredientId,
                                    'name' => $ingredient->name,
                                    'currentWeight' => $ingredient->stock,
                                    'consume' => 0
                                ];
                            }

                            $ingredientUpdates[$ingredientId]['consume'] += $requiredWeight;
                        }
                    }
                }
            }

            if (!empty($data['addons'])) {
                foreach ($data['addons'] as $id => $item) {
                    $quantity = intval($item['quantity']);

                    if ($quantity > 0) {
                        $addon = $this->_addOnRepository->getById($id);
                        if (!$addon) {
                            continue;
                        }

                        $price = $addon->price;
                        $amount = $quantity * $price;

                        $totalQuantity += $quantity;
                        $totalAmount += $amount;

                        $items[] = [
                            'item_type' => 'addon',
                            'item_id' => $id,
                            'quantity' => $quantity,
                            'price' => $price,
                            'amount' => $amount,
                        ];

                        $addonIngredients = $this->_addOnIngredientRepository->getByAddOnId($id);
                        foreach ($addonIngredients as $addonIngredient) {
                            $ingredientId = $addonIngredient->ingredient_id;
                            $requiredWeight = $addonIngredient->consumption * $quantity;

                            if (!isset($ingredientUpdates[$ingredientId])) {
                                $ingredient = $this->_ingredientRepository->getById($ingredientId);
                                $ingredientUpdates[$ingredientId] = [
                                    'id' => $ingredientId,
                                    'name' => $ingredient->name,
                                    'currentWeight' => $ingredient->stock,
                                    'consume' => 0
                                ];
                            }

                            $ingredientUpdates[$ingredientId]['consume'] += $requiredWeight;
                        }
                    }
                }
            }

            if ($totalQuantity === 0) {
                array_push($this->_errorMessage, "Please select at least one food or addon.");
                return null;
            }

            foreach ($ingredientUpdates as $update) {
                if ($update['currentWeight'] === null || $update['currentWeight'] < $update['consume']) {
                    array_push($this->_errorMessage, "Insufficient stock for ingredient: " . $update['name']);
                    $hasInsufficientStock = true;
                }
            }

            if ($hasInsufficientStock) {
                DB::rollBack();
                return null;
            }

            foreach ($ingredientUpdates as $update) {
                $newWeight = $update['currentWeight'] - $update['consume'];
                $this->_ingredientRepository->updateStock($update['id'], $newWeight);
            }

            $dailySales = $this->_dailySalesRepository->save([
                'date' => $data['date'],
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
                if ($item->item_type === 'food') {
                    $food = $this->_foodRepository->getById($item->item_id);
                    $item->name = $food ? $food->name : 'Unknown Food';
                } else {
                    $addon = $this->_addOnRepository->getById($item->item_id);
                    $item->name = $addon ? $addon->name : 'Unknown Addon';
                }
            }

            $foods = $this->_foodRepository->getAll();
            $addons = $this->_addOnRepository->getAll();

            $ingredientMap = [
                'food' => [],
                'addon' => []
            ];

            foreach ($foods as $food) {
                $ingredients = $this->_foodIngredientRepository->getByFoodId($food->id);
                $ingredientMap['food'][$food->id] = [];

                foreach ($ingredients as $pi) {
                    $ingredient = $this->_ingredientRepository->getById($pi->ingredient_id);
                    if ($ingredient) {
                        $ingredientMap['food'][$food->id][] = [
                            'name' => $ingredient->name,
                            'consumption' => $pi->consumption,
                            'remaining' => $ingredient->stock
                        ];
                    }
                }
            }

            foreach ($addons as $addon) {
                $ingredients = $this->_addOnIngredientRepository->getByAddOnId($addon->id);
                $ingredientMap['addon'][$addon->id] = [];

                foreach ($ingredients as $ai) {
                    $ingredient = $this->_ingredientRepository->getById($ai->ingredient_id);
                    if ($ingredient) {
                        $ingredientMap['addon'][$addon->id][] = [
                            'name' => $ingredient->name,
                            'consumption' => $ai->consumption,
                            'remaining' => $ingredient->stock
                        ];
                    }
                }
            }

            return [
                'dailySales' => $dailySales,
                'dailySalesItems' => $dailySalesItems,
                'foods' => $foods,
                'addons' => $addons,
                'ingredientMap' => $ingredientMap
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
                'date' => 'required|date',
                'foods' => 'nullable|array',
                'addons' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return false;
            }

            $dailySales = $this->_dailySalesRepository->getById($id);

            if (!$dailySales) {
                array_push($this->_errorMessage, "Daily Sales not found.");
                return false;
            }

            $exists = $this->_dailySalesRepository->getByDate($data['date']);
            if ($exists && $exists->id != $id) {
                array_push($this->_errorMessage, "Daily sales for this date already exists.");
                DB::rollBack();
                return false;
            }

            $oldItems = $this->_dailySalesItemRepository->getByDailySalesId($id);

            $oldQuantities = [
                'food' => [],
                'addon' => []
            ];
            foreach ($oldItems as $item) {
                $oldQuantities[$item->item_type][$item->item_id] = $item->quantity;
            }

            $totalQuantity = 0;
            $totalAmount = 0;
            $items = [];
            $hasInsufficientStock = false;

            if (!empty($data['foods'])) {
                foreach ($data['foods'] as $foodId => $item) {
                    $newQty = intval($item['quantity']);
                    $oldQty = $oldQuantities['food'][$foodId] ?? 0;
                    $diff = $newQty - $oldQty;

                    if ($newQty > 0) {
                        $food = $this->_foodRepository->getById($foodId);
                        if (!$food) continue;

                        $price = $food->price;
                        $amount = $newQty * $price;

                        $totalQuantity += $newQty;
                        $totalAmount += $amount;

                        $items[] = [
                            'item_type' => 'food',
                            'item_id' => $foodId,
                            'quantity' => $newQty,
                            'price' => $price,
                            'amount' => $amount,
                            'daily_sales_id' => $id,
                        ];
                    }

                    if ($diff !== 0) {
                        $foodIngredients = $this->_foodIngredientRepository->getByFoodId($foodId);
                        foreach ($foodIngredients as $pi) {
                            $ingredient = $this->_ingredientRepository->getById($pi->ingredient_id);
                            if ($ingredient) {
                                $changeWeight = $pi->consumption * $diff;

                                if ($changeWeight > 0) {
                                    if ($ingredient->stock < $changeWeight) {
                                        array_push($this->_errorMessage, "Insufficient stock for ingredient: " . $ingredient->name);
                                        $hasInsufficientStock = true;
                                    } else {
                                        $this->_ingredientRepository->updateStock($ingredient->id, $ingredient->stock - $changeWeight);
                                    }
                                } elseif ($changeWeight < 0) {
                                    $this->_ingredientRepository->updateStock($ingredient->id, $ingredient->stock + abs($changeWeight));
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($data['addons'])) {
                foreach ($data['addons'] as $addonId => $item) {
                    $newQty = intval($item['quantity']);
                    $oldQty = $oldQuantities['addon'][$addonId] ?? 0;
                    $diff = $newQty - $oldQty;

                    if ($newQty > 0) {
                        $addon = $this->_addOnRepository->getById($addonId);
                        if (!$addon) continue;

                        $price = $addon->price;
                        $amount = $newQty * $price;

                        $totalQuantity += $newQty;
                        $totalAmount += $amount;

                        $items[] = [
                            'item_type' => 'addon',
                            'item_id' => $addonId,
                            'quantity' => $newQty,
                            'price' => $price,
                            'amount' => $amount,
                            'daily_sales_id' => $id,
                        ];
                    }

                    if ($diff !== 0) {
                        $addonIngredients = $this->_addOnIngredientRepository->getByAddOnId($addonId);
                        foreach ($addonIngredients as $ai) {
                            $ingredient = $this->_ingredientRepository->getById($ai->ingredient_id);
                            if ($ingredient) {
                                $changeWeight = $ai->consumption * $diff;

                                if ($changeWeight > 0) {
                                    if ($ingredient->stock < $changeWeight) {
                                        array_push($this->_errorMessage, "Insufficient stock for ingredient: " . $ingredient->name);
                                        $hasInsufficientStock = true;
                                    } else {
                                        $this->_ingredientRepository->updateStock($ingredient->id, $ingredient->stock - $changeWeight);
                                    }
                                } elseif ($changeWeight < 0) {
                                    $this->_ingredientRepository->updateStock($ingredient->id, $ingredient->stock + abs($changeWeight));
                                }
                            }
                        }
                    }
                }
            }

            if ($totalQuantity === 0) {
                array_push($this->_errorMessage, "Total quantity cannot be zero.");
                return false;
            }

            if ($hasInsufficientStock) {
                DB::rollBack();
                return false;
            }

            $this->_dailySalesRepository->update($id, [
                'date' => $data['date'],
                'total_quantity' => $totalQuantity,
                'total_amount' => $totalAmount,
                'staff_id' => Auth::id(),
            ]);

            $this->_dailySalesItemRepository->deleteByDailySalesId($id);

            $this->_dailySalesItemRepository->bulkSave($items);

            DB::commit();
            return true;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update daily sales.");
            DB::rollBack();
            return false;
        }
    }
}
