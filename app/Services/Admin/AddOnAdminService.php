<?php

namespace App\Services\Admin;

use App\Repositories\AddOnRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AddOnAdminService extends Service
{
    private $_addOnRepository;

    public function __construct(AddOnRepository $addOnRepository)
    {
        $this->_addOnRepository = $addOnRepository;
    }

    public function createAddOn($data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:512000',
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $validator->errors()->all();
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            if (isset($data['image']) && !empty($data['image'])) {
                $fileName = $this->generateFileName();
                $fileExtension = $data['image']->extension();
                $fileName = $fileName . '.' . $fileExtension;

                $data['image']->storeAs('add_on', $fileName, 'public');

                $data['image'] = $fileName;
            }

            $addOn = $this->_addOnRepository->save($data);

            DB::commit();
            return $addOn;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add add-on.");
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
            $addOn = $this->_addOnRepository->getById($id);

            return $addOn;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get add-on.");

            return null;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:512000',
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $addOn = $this->_addOnRepository->getById($id);

            if ($addOn == null) {
                throw new Exception();
            }

            if (!empty($data['image'])) {
                if ($addOn['image'] != null && Storage::disk('public')->exists('add_on/' . $addOn['image'])) {
                    Storage::disk('public')->delete('add_on/' . $addOn['image']);
                }

                $fileName = $this->generateFileName();
                $fileExtension = $data['image']->extension();
                $fileName = $fileName . '.' . $fileExtension;

                $data['image']->storeAs('add_on', $fileName, 'public');
                $data['image'] = $fileName;
            }

            $addOn = $this->_addOnRepository->update($id, $data);

            DB::commit();
            return $addOn;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update add-on detail.");

            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $addOn = $this->_addOnRepository->deleteById($id);

            DB::commit();
            return $addOn;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete add-on.");

            DB::rollBack();
            return null;
        }
    }

    public function getSelectOption($data)
    {
        try {
            $data['result_count'] = 50;
            $data['offset'] = ($data['page'] - 1) * $data['result_count'];


            $addOns = $this->_addOnRepository->getAllBySearchTerm($data);

            $totalCount = $this->_addOnRepository->getTotalCountBySearchTerm($data);

            $results = array(
                "results" => $addOns->toArray(),
                "pagination" => array(
                    "more" => $totalCount < $data['offset'] + $data['result_count'] ? false : true
                )
            );

            return $results;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Currently the list didnt have this add-on.");
            DB::rollBack();

            return null;
        }
    }
}
