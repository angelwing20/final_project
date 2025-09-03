<?php

namespace App\Services\Admin;

use App\Repositories\IngredientRepository;
use App\Repositories\FoodIngredientRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FoodIngredientAdminService extends Service
{
    private $_foodIngredientRepository;
    private $_ingredientRepository;

    public function __construct(FoodIngredientRepository $foodIngredientRepository, IngredientRepository $ingredientRepository)
    {
        $this->_foodIngredientRepository = $foodIngredientRepository;
        $this->_ingredientRepository = $ingredientRepository;
    }

    public function createFoodIngredient($data)
    {
        DB::beginTransaction();

        try {
            $ingredient = $this->_ingredientRepository->getById($data['ingredient_id']);

            if ($ingredient == null) {
                throw new Exception();
            }

            $min = $ingredient->unit_type === 'weight' ? 0.001 : 1;

            $validator = Validator::make($data, [
                'food_id' => 'required|exists:food,id',
                'ingredient_id' => 'required|exists:ingredients,id',
                'consumption' => ['required', 'numeric', "min:$min"],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            if ($ingredient['unit_type'] === 'quantity') {
                $data['consumption'] = $data['consumption'] * $ingredient['weight_unit'];
            }

            $foodIngredient = $this->_foodIngredientRepository->save($data);

            DB::commit();
            return $foodIngredient;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add food ingredient.");

            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $foodIngredient = $this->_foodIngredientRepository->deleteById($id);

            DB::commit();
            return $foodIngredient;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete food ingredient.");

            DB::rollBack();
            return null;
        }
    }
}
