<?php

namespace App\Services\Admin;

use App\Repositories\AddOnIngredientRepository;
use App\Repositories\IngredientRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AddOnIngredientAdminService extends Service
{
    private $_addOnIngredientRepository;
    private $_ingredientRepository;

    public function __construct(AddOnIngredientRepository $addOnIngredientRepository, IngredientRepository $ingredientRepository)
    {
        $this->_addOnIngredientRepository = $addOnIngredientRepository;
        $this->_ingredientRepository = $ingredientRepository;
    }

    public function createAddOnIngredient($data)
    {
        DB::beginTransaction();

        try {
            $ingredient = $this->_ingredientRepository->getById($data['ingredient_id']);

            if ($ingredient == null) {
                throw new Exception();
            }

            $min = $ingredient->unit_type === 'weight' ? 0.01 : 1;

            $validator = Validator::make($data, [
                'add_on_id' => 'required|exists:add_ons,id',
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

            $addOnIngredient = $this->_addOnIngredientRepository->save($data);

            DB::commit();
            return $addOnIngredient;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add add-on ingredient.");

            DB::rollBack();
            return null;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();

        try {
            $addOnIngredient = $this->_addOnIngredientRepository->getById($id);
            if (!$addOnIngredient) {
                throw new Exception('Add-on Ingredient not found');
            }

            $ingredient = $this->_ingredientRepository->getById($addOnIngredient->ingredient_id);
            if (!$ingredient) {
                throw new Exception('Ingredient not found');
            }

            $min = $ingredient->unit_type === 'weight' ? 0.01 : 1;

            $validator = Validator::make($data, [
                'consumption' => ['required', 'numeric', "min:$min"],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            if ($ingredient->unit_type === 'quantity') {
                $data['consumption'] = $data['consumption'] * $ingredient->weight_unit;
            }

            $addOnIngredient = $this->_addOnIngredientRepository->update($id, $data);

            DB::commit();
            return $addOnIngredient;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update add-on ingredient.");

            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $addOnIngredient = $this->_addOnIngredientRepository->deleteById($id);

            DB::commit();
            return $addOnIngredient;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete add-on ingredient.");

            DB::rollBack();
            return null;
        }
    }
}
