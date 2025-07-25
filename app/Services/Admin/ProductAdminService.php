<?php

namespace App\Services\Admin;

use App\Repositories\ProductRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
                'product_category_id' => 'required|exists:product_categories,id',
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0.01',
                'description' => 'nullable|string|max:16777215',
                'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:512000',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            if (isset($data['image']) && !empty($data['image'])) {
                $fileName = $this->generateFileName();
                $fileExtension = $data['image']->extension();
                $fileName = $fileName . '.' . $fileExtension;

                $data['image']->storeAs('product', $fileName, 'public');

                $data['image'] = $fileName;
            }

            $product = $this->_productRepository->save($data);

            DB::commit();
            return $product;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add product.");

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
            $product = $this->_productRepository->getById($id);

            return $product;
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
                'product_category_id' => 'required|exists:product_categories,id',
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0.01',
                'description' => 'nullable|string|max:16777215',
                'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:512000',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $product = $this->_productRepository->getById($id);

            if ($product == null) {
                throw new Exception();
            }

            if (!empty($data['image'])) {
                if ($product['image'] != null && Storage::disk('public')->exists('product/' . $product['image'])) {
                    Storage::disk('public')->delete('product/' . $product['image']);
                }

                $fileName = $this->generateFileName();
                $fileExtension = $data['image']->extension();
                $fileName = $fileName . '.' . $fileExtension;

                $data['image']->storeAs('product', $fileName, 'public');
                $data['image'] = $fileName;
            }

            $product = $this->_productRepository->update($id, $data);

            DB::commit();
            return $product;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update product detail.");

            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $product = $this->_productRepository->deleteById($id);

            DB::commit();
            return $product;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete product.");

            DB::rollBack();
            return null;
        }
    }
}
