<?php

namespace App\Services\Admin;

use App\Repositories\AddOnIngredientRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AddOnIngredientAdminService extends Service
{
    private $_addOnIngredientRepository;

    public function __construct(AddOnIngredientRepository $addOnIngredientRepository)
    {
        $this->_addOnIngredientRepository = $addOnIngredientRepository;
    }

    public function createAddOnIngredient($data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'add_on_id' => 'required|exists:add_ons,id',
                'ingredient_id' => 'required|exists:ingredients,id',
                'weight' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
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
            $validator = Validator::make($data, [
                'weight' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
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
