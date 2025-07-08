<?php

namespace App\Services\Admin;

use App\Repositories\IngredientCategoriesRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IngredientCategoryAdminService extends Service
{
    private $_ingredientCategoriesRepository;

    public function __construct(IngredientCategoriesRepository $ingredientCategoriesrRepository)
    {
        $this->_ingredientCategoriesRepository = $ingredientCategoriesrRepository;
    }

    public function createIngredientCategory($data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $ingredientCategory = $this->_ingredientCategoriesRepository->save($data);

            DB::commit();
            return $ingredientCategory;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add ingredient category.");
            DB::rollBack();
            return null;
        }
    }

    public function getById($id)
    {
        try {
            $ingredientCategory = $this->_ingredientCategoriesRepository->getById($id);

            return $ingredientCategory;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get ingredient category.");

            return null;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $ingredientCategory = $this->_ingredientCategoriesRepository->update($id, $data);

            DB::commit();
            return $ingredientCategory;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update ingredient category detail.");

            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $ingredientCategory = $this->_ingredientCategoriesRepository->deleteById($id);

            DB::commit();
            return $ingredientCategory;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete ingredient category.");

            DB::rollBack();
            return null;
        }
    }

    public function getSelectOption($data)
    {
        try {
            $data['result_count'] = 50;
            $data['offset'] = ($data['page'] - 1) * $data['result_count'];


            $ingredientCategories = $this->_ingredientCategoriesRepository->getAllBySearchTerm($data);

            $totalCount = $this->_ingredientCategoriesRepository->getTotalCountBySearchTerm($data);

            $results = array(
                "results" => $ingredientCategories->toArray(),
                "pagination" => array(
                    "more" => $totalCount < $data['offset'] + $data['result_count'] ? false : true
                )
            );

            return $results;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Currently the list didnt have this ingredient category.");
            DB::rollBack();

            return null;
        }
    }
}
