<?php

namespace App\Services\Admin;

use App\Models\Ingredient;
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
            $min = (isset($data['unit_type']) && $data['unit_type'] === 'quantity') ? 1 : 0.001;

            $validator = Validator::make($data, [
                'ingredient_category_id' => 'required|exists:ingredient_categories,id',
                'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:512000',
                'name' => 'required|string|max:255|unique:ingredients,name',
                'unit_type' => 'required|in:weight,quantity',
                'stock' => ['nullable', 'numeric', "min:$min"],
                'min_stock' => ['required', 'numeric', "min:$min"],
                'weight_unit' => 'required|numeric|min:0.001',
                'price' => 'required|numeric|min:0.01',
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

            if ($data['unit_type'] === 'quantity') {
                if (!empty($data['stock'])) {
                    $data['stock'] = $data['stock'] * $data['weight_unit'];
                } else {
                    $data['stock'] = 0;
                }
                $data['min_stock'] = $data['min_stock'] * $data['weight_unit'];
            } else {
                $data['stock'] = $data['stock'] ?? 0;
            }

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
            $ingredient = $this->_ingredientRepository->getById($id);

            if ($ingredient == null) {
                throw new Exception();
            }

            $min = $ingredient->unit_type === 'weight' ? 0.001 : 1;

            $validator = Validator::make($data, [
                'ingredient_category_id' => 'required|exists:ingredient_categories,id',
                'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:512000',
                'name' => 'required|string|max:255|unique:ingredients,name,' . $id,
                'min_stock' => ['required', 'numeric', "min:$min"],
                'weight_unit' => 'required|numeric|min:0.001',
                'price' => 'required|numeric|min:0.01',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
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

            if ($ingredient['unit_type'] === 'quantity') {
                if ($data['weight_unit'] != $ingredient['weight_unit']) {

                    $originalQty = $ingredient['stock'] / $ingredient['weight_unit'];
                    $originalMinQty = $ingredient['min_stock'] / $ingredient['weight_unit'];

                    $data['stock'] = $originalQty * $data['weight_unit'];
                    $data['min_stock'] = $originalMinQty * $data['weight_unit'];
                } else {
                    $data['min_stock'] = $data['min_stock'] * $data['weight_unit'];
                }
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
                $ingredient = $this->_ingredientRepository->getById($refill['ingredient_id']);

                $rules = [
                    'ingredient_id' => 'required|exists:ingredients,id',
                    'quantity' => 'required|integer|min:1',
                    'weight' => $ingredient->unit_type === 'weight'
                        ? 'required|numeric|min:0.001'
                        : 'nullable|numeric|min:0.001',
                ];

                $validator = Validator::make($refill, $rules);

                if ($validator->fails()) {
                    foreach ($validator->errors()->all() as $error) {
                        array_push($this->_errorMessage, $error);
                    }
                    return null;
                }

                if ($ingredient->unit_type === 'quantity') {
                    $totalWeight = $refill['quantity'] * $ingredient->weight_unit;
                } else {
                    $totalWeight = $refill['quantity'] * $refill['weight'];
                }

                $newWeight = $ingredient->stock + $totalWeight;

                $amount = ($totalWeight / $ingredient->weight_unit) * $ingredient->price;

                $this->_refillStockHistoryRepository->save([
                    'ingredient_id' => $refill['ingredient_id'],
                    'staff_id' => Auth::id(),
                    'quantity' => $refill['quantity'],
                    'weight' => $totalWeight,
                    'amount' => $amount
                ]);

                $this->_ingredientRepository->update($refill['ingredient_id'], [
                    'stock' => $newWeight
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

            if ($data['exclude_food_id'] == null && $data['exclude_add_on_id'] == null) {
                $ingredients = $this->_ingredientRepository->getAllBySearchTerm($data);
                $totalCount = $this->_ingredientRepository->getTotalCountBySearchTerm($data);
            } elseif ($data['exclude_food_id'] != null) {
                $ingredients = $this->_ingredientRepository->getAllBySearchTermAndExcludeFood($data, $data['exclude_food_id']);
                $totalCount = $this->_ingredientRepository->getTotalCountBySearchTermAndExcludeFood($data, $data['exclude_food_id']);
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
