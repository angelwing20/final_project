<?php

namespace App\Services\Admin;

use App\Repositories\FoodRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FoodAdminService extends Service
{
    private $_foodRepository;

    public function __construct(FoodRepository $foodRepository)
    {
        $this->_foodRepository = $foodRepository;
    }

    public function createFood($data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'food_category_id' => 'required|exists:food_categories,id',
                'name' => 'required|string|max:255|unique:food,name',
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

                $data['image']->storeAs('food', $fileName, 'public');

                $data['image'] = $fileName;
            }

            $food = $this->_foodRepository->save($data);

            DB::commit();
            return $food;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add food.");

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
            $food = $this->_foodRepository->getById($id);

            return $food;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get food.");

            return null;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'food_category_id' => 'required|exists:food_categories,id',
                'name' => 'required|string|max:255|unique:food,name,' . $id,
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

            $food = $this->_foodRepository->getById($id);

            if ($food == null) {
                throw new Exception();
            }

            if (!empty($data['image'])) {
                if ($food['image'] != null && Storage::disk('public')->exists('food/' . $food['image'])) {
                    Storage::disk('public')->delete('food/' . $food['image']);
                }

                $fileName = $this->generateFileName();
                $fileExtension = $data['image']->extension();
                $fileName = $fileName . '.' . $fileExtension;

                $data['image']->storeAs('food', $fileName, 'public');
                $data['image'] = $fileName;
            }

            $food = $this->_foodRepository->update($id, $data);

            DB::commit();
            return $food;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update food detail.");

            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $food = $this->_foodRepository->deleteById($id);

            DB::commit();
            return $food;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete food.");

            DB::rollBack();
            return null;
        }
    }
}
