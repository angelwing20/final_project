<?php

namespace App\Services\Admin;

use App\Repositories\ProductRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductAdminService extends Service
{
    private $_productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->_productRepository = $productRepository;
    }

    public function createProduct($data)
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

            $product = $this->_productRepository->save($data);

            DB::commit();
            return $product;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add product .");

            DB::rollBack();
            return null;
        }
    }

    public function getById($id)
    {
        try {
            $supplier = $this->_productRepository->getById($id);

            return $supplier;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get product.");

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

            $product = $this->_productRepository->update($id, $data);

            DB::commit();
            return $product;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update Product Detail.");

            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $product= $this->_productRepository->deleteById($id);

            DB::commit();
            return $product;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete product.");

            DB::rollBack();
            return null;
        }
    }
}
