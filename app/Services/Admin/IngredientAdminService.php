<?php

namespace App\Services\Admin;

use App\Repositories\IngredientRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/**
 * IngredientAdminService Class
 * This service handles CRUD operations for ingredients, including file uploads and low stock management.
 */
class IngredientAdminService extends Service
{
   
    private $_ingredientRepository;


    public function __construct(IngredientRepository $ingredientRepository)
    {
        $this->_ingredientRepository = $ingredientRepository;
    }

    public function getAll()
    {
        try {
            return $this->_ingredientRepository->getAll();
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to retrieve all ingredients.");
            return null;
        }
    }

    public function getLowStockIngredients()
    {
        try {
            return $this->_ingredientRepository->getLowStockIngredients();
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to retrieve low stock ingredients.");
            return null;
        }
    }

    public function createIngredient($data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'ingredient_category_id' => 'required|exists:ingredient_categories,id',
                'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:512000',
                'name' => 'required|string|max:255',
                'weight' => 'required|numeric',
                'alarm_weight' => 'required|numeric',
                'description' => 'nullable|string|max:16777215',
            ]);

            if ($validator->fails()) {
                // 移除 dd() 用于调试，改为记录错误
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
                'weight' => 'required|numeric',
                'alarm_weight' => 'required|numeric',
                'description' => 'nullable|string|max:16777215',
            ]);

            if ($validator->fails()) {
                // 移除 dd() 用于调试，改为记录错误
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
}
