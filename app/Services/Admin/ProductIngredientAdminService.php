<?php

namespace App\Services\Admin;

use App\Repositories\IngredientRepository;
use App\Repositories\ProductIngredientRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductIngredientAdminService extends Service
{
    private $_productIngredientRepository;
    private $_ingredientRepository;

    public function __construct(ProductIngredientRepository $productIngredientRepository, IngredientRepository $ingredientRepository)
    {
        $this->_productIngredientRepository = $productIngredientRepository;
        $this->_ingredientRepository = $ingredientRepository;
    }

    public function createProductIngredient($data)
    {
        DB::beginTransaction();

        try {
            $ingredient = $this->_ingredientRepository->getById($data['ingredient_id']);

            if ($ingredient == null) {
                throw new Exception();
            }

            $min = $ingredient->unit_type === 'weight' ? 0.01 : 1;

            $validator = Validator::make($data, [
                'product_id' => 'required|exists:products,id',
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

            $productIngredient = $this->_productIngredientRepository->save($data);

            DB::commit();
            return $productIngredient;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add product ingredient.");

            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $productIngredient = $this->_productIngredientRepository->deleteById($id);

            DB::commit();
            return $productIngredient;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete product ingredient.");

            DB::rollBack();
            return null;
        }
    }
}
