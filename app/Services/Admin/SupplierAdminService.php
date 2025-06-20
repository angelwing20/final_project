<?php

namespace App\Services\Admin;

use Exception;
use App\Services\Service;
use App\Repositories\SupplierRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SupplierAdminService extends Service
{
    private $_supplierRepository;

    public function __construct(SupplierRepository $supplierRepository)
    {
        $this->_supplierRepository = $supplierRepository;
    }

    public function createSupplier($data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255|unique:suppliers,email',
                'phone' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:16777215',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $supplier = $this->_supplierRepository->save($data);

            DB::commit();
            return $supplier;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add supplier.");

            DB::rollBack();
            return null;
        }
    }

    public function getById($id)
    {
        try {
            $supplier = $this->_supplierRepository->getById($id);

            return $supplier;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get supplier.");

            return null;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255|unique:suppliers,email,' . $id,
                'phone' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:16777215',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $supplier = $this->_supplierRepository->update($id, $data);

            DB::commit();
            return $supplier;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update supplier.");

            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $supplier = $this->_supplierRepository->deleteById($id);

            DB::commit();
            return $supplier;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get supplier.");

            DB::rollBack();
            return null;
        }
    }
}
