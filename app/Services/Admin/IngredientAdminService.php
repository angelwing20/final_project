<?php

namespace App\Services\Admin;

use App\Repositories\IngredientRepository;
use App\Repositories\RefillStockHistoryRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class IngredientAdminService extends Service
{
    private $_ingredientRepository;
    private $_refillStockHistoryRepository;

    public function __construct(IngredientRepository $ingredientRepository, RefillStockHistoryRepository $refillStockHistoryRepository)
    {
        $this->_ingredientRepository = $ingredientRepository;
        $this->_refillStockHistoryRepository = $refillStockHistoryRepository;
    }

    public function createIngredient($data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'ingredient_category_id' => 'required|exists:ingredient_categories,id',
                'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:512000',
                'name' => 'required|string|max:255',
                'stock_weight' => 'nullable|numeric|min:0',
                'alarm_weight' => 'required|numeric|min:0.01',
                'weight_unit' => 'required|numeric|min:0.01',
                'price_per_weight_unit' => 'required|numeric|min:0.01',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            if (isset($data['image']) && !empty($data['image'])) {
                $fileName = $this->generateFileName();
                $fileExtension = $data['image']->extension();
                $fileName = $fileName . '.' . $fileExtension;

                $data['image']->storeAs('ingredient', $fileName, 'public');

                $data['image'] = $fileName;
            }

            $data['stock_weight'] = $data['stock_weight'] ?? 0;

            $ingredient = $this->_ingredientRepository->save($data);

            DB::commit();
            return $ingredient;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add ingredient.");

            DB::rollBack();
            return null;
        }
    }

    public function generateFileName()
    {
        return Str::random(5) . Str::uuid() . Str::random(5);
    }

    public function getById($id)
    {
        try {
            $ingredient = $this->_ingredientRepository->getById($id);

            return $ingredient;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get ingredient.");

            return null;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'ingredient_category_id' => 'required|exists:ingredient_categories,id',
                'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:512000',
                'name' => 'required|string|max:255',
                'alarm_weight' => 'required|numeric|min:0.01',
                'weight_unit' => 'required|numeric|min:0.01',
                'price_per_weight_unit' => 'required|numeric|min:0.01',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $ingredient = $this->_ingredientRepository->getById($id);

            if ($ingredient == null) {
                throw new Exception();
            }

            if (!empty($data['image'])) {
                if ($ingredient['image'] != null && Storage::disk('public')->exists('ingredient/' . $ingredient['image'])) {
                    Storage::disk('public')->delete('ingredient/' . $ingredient['image']);
                }

                $fileName = $this->generateFileName();
                $fileExtension = $data['image']->extension();
                $fileName = $fileName . '.' . $fileExtension;

                $data['image']->storeAs('ingredient', $fileName, 'public');
                $data['image'] = $fileName;
            }

            $ingredient = $this->_ingredientRepository->update($id, $data);

            DB::commit();
            return $ingredient;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update ingredient detail.");

            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $ingredient = $this->_ingredientRepository->deleteById($id);

            DB::commit();
            return $ingredient;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete ingredient.");

            DB::rollBack();
            return null;
        }
    }

    public function bulkRefillStock(array $refills)
    {
        DB::beginTransaction();

        try {
            foreach ($refills as $refill) {
                $validator = Validator::make($refill, [
                    'ingredient_id' => 'required|exists:ingredients,id',
                    'weight' => 'required|numeric|min:0.01',
                    'quantity' => 'nullable|integer|min:1',
                ]);

                if ($validator->fails()) {
                    foreach ($validator->errors()->all() as $error) {
                        array_push($this->_errorMessage, $error);
                    }
                    return null;
                }

                $refill['quantity'] = $refill['quantity'] ?? 1;

                $ingredient = $this->_ingredientRepository->getById($refill['ingredient_id']);

                $totalWeight = $refill['weight'] * $refill['quantity'];
                $newWeight = $ingredient->stock_weight + $totalWeight;

                $amount = ($totalWeight / $ingredient->weight_unit) * $ingredient->price_per_weight_unit;

                $this->_refillStockHistoryRepository->save([
                    'ingredient_id' => $refill['ingredient_id'],
                    'staff_id' => Auth::id(),
                    'quantity' => $refill['quantity'],
                    'weight' => $totalWeight,
                    'amount' => $amount
                ]);

                $this->_ingredientRepository->update($refill['ingredient_id'], [
                    'stock_weight' => $newWeight
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to refill stock.");

            DB::rollBack();
            return null;
        }
    }

    public function getSelectOption($data)
    {
        try {
            $data['result_count'] = 50;
            $data['offset'] = ($data['page'] - 1) * $data['result_count'];

            if ($data['exclude_product_id'] == null && $data['exclude_add_on_id'] == null) {
                $ingredients = $this->_ingredientRepository->getAllBySearchTerm($data);
                $totalCount = $this->_ingredientRepository->getTotalCountBySearchTerm($data);
            } elseif ($data['exclude_product_id'] != null) {
                $ingredients = $this->_ingredientRepository->getAllBySearchTermAndExcludeProduct($data, $data['exclude_product_id']);
                $totalCount = $this->_ingredientRepository->getTotalCountBySearchTermAndExcludeProduct($data, $data['exclude_product_id']);
            } else {
                $ingredients = $this->_ingredientRepository->getAllBySearchTermAndExcludeAddOn($data, $data['exclude_add_on_id']);
                $totalCount = $this->_ingredientRepository->getTotalCountBySearchTermAndExcludeAddOn($data, $data['exclude_add_on_id']);
            }

            $results = array(
                "results" => $ingredients->toArray(),
                "pagination" => array(
                    "more" => $totalCount < $data['offset'] + $data['result_count'] ? false : true
                )
            );

            return $results;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Currently the list didnt have this ingredient.");
            DB::rollBack();

            return null;
        }
    }
}
