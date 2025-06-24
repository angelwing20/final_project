<?php

namespace App\Services\Admin;

use App\Repositories\ProductCategoriesRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductCategoryAdminService extends Service
{
    private $_productCategoriesRepository;

    public function __construct(ProductCategoriesRepository $productCategoriesrRepository)
    {
        $this->_productCategoriesRepository = $productCategoriesrRepository;
    }

    public function createProductCategory($data)
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

            $productCategory = $this->_productCategoriesRepository->save($data);

            DB::commit();
            return $productCategory;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add product .");

            DB::rollBack();
            return null;
        }
    }

    public function getById($id)
    {
        try {
            $productCategory= $this->_productCategoriesRepository->getById($id);

            return $productCategory;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get product category list.");

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

            $productCategory = $this->_productCategoriesRepository->update($id, $data);

            DB::commit();
            return $productCategory;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update Category list.");

            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $productCategory = $this->_productCategoriesRepository->deleteById($id);

            DB::commit();
            return $productCategory;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete Category list.");

            DB::rollBack();
            return null;
        }
    }

    public function getSelectOption($data)
    {
        try {
            $data['result_count'] = 50;
            $data['offset'] = ($data['page'] - 1) * $data['result_count'];
            

            $countries = $this->_productCategoriesRepository->getAllBySearchTerm($data);

            $totalCount = $this->_productCategoriesRepository->getTotalCountBySearchTerm($data);

            $results = array(
                "results" => $countries->toArray(),
                "pagination" => array(
                    "more" => $totalCount < $data['offset'] + $data['result_count'] ? false : true
                )
            );

            // return $results;
            return response(123);
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Currently the list didnt have this product category.");
            DB::rollBack();
            return null;
        }
    }
}
