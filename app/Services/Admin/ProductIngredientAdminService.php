<?php

namespace App\Services\Admin;

use App\Repositories\ProductIngredientRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductIngredientAdminService extends Service
{
    private $_productIngredientRepository;

    public function __construct(ProductIngredientRepository $productIngredientRepository)
    {
        $this->_productIngredientRepository = $productIngredientRepository;
    }

    public function createProductIngredient($data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'product_id' => 'required|exists:products,id',
                'ingredient_id' => 'required|exists:ingredients,id',
                'weight' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
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

    public function update($id, $data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'weight' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $product = $this->_productIngredientRepository->update($id, $data);

            DB::commit();
            return $product;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update product ingredient.");

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
