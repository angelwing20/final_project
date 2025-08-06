<?php

namespace App\Services\Admin;

use App\Repositories\FoodCategoriesRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FoodCategoryAdminService extends Service
{
    private $_foodCategoriesRepository;

    public function __construct(FoodCategoriesRepository $foodCategoriesRepository)
    {
        $this->_foodCategoriesRepository = $foodCategoriesRepository;
    }

    public function createFoodCategory($data)
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

            $foodCategory = $this->_foodCategoriesRepository->save($data);

            DB::commit();
            return $foodCategory;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add food category.");
            DB::rollBack();
            return null;
        }
    }

    public function getById($id)
    {
        try {
            $foodCategory = $this->_foodCategoriesRepository->getById($id);

            return $foodCategory;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get food category.");

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

            $foodCategory = $this->_foodCategoriesRepository->update($id, $data);

            DB::commit();
            return $foodCategory;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update food category detail.");

            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $foodCategory = $this->_foodCategoriesRepository->deleteById($id);

            DB::commit();
            return $foodCategory;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete food category.");

            DB::rollBack();
            return null;
        }
    }

    public function getSelectOption($data)
    {
        try {
            $data['result_count'] = 50;
            $data['offset'] = ($data['page'] - 1) * $data['result_count'];


            $foodCategories = $this->_foodCategoriesRepository->getAllBySearchTerm($data);

            $totalCount = $this->_foodCategoriesRepository->getTotalCountBySearchTerm($data);

            $results = array(
                "results" => $foodCategories->toArray(),
                "pagination" => array(
                    "more" => $totalCount < $data['offset'] + $data['result_count'] ? false : true
                )
            );

            return $results;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Currently the list didnt have this food category.");
            DB::rollBack();

            return null;
        }
    }
}
