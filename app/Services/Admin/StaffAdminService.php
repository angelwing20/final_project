<?php

namespace App\Services\Admin;

use App\Repositories\UserRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class StaffAdminService extends Service
{
    private $_userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->_userRepository = $userRepository;
    }

    public function createStaff($data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:512000',
                'role' => 'required|in:Admin,Superadmin',
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:6'
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

                $data['image']->storeAs('profile', $fileName, 'public');

                $data['image'] = $fileName;
            }

            $staff = $this->_userRepository->save($data);
            $staff->assignRole($data['role']);

            DB::commit();
            return $staff;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add staff.");

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
            $staff = $this->_userRepository->getById($id);

            if ($staff) {
                $staff->load('roles');
            }

            return $staff;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get staff.");

            return null;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:512000',
                'role' => 'required|in:Admin,Superadmin',
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $staff = $this->_userRepository->getById($id);

            if ($staff == null) {
                throw new Exception();
            }

            if (!empty($data['image'])) {
                if ($staff['image'] != null && Storage::disk('public')->exists('profile/' . $staff['image'])) {
                    Storage::disk('public')->delete('profile/' . $staff['image']);
                }

                $fileName = $this->generateFileName();
                $fileExtension = $data['image']->extension();
                $fileName = $fileName . '.' . $fileExtension;

                $data['image']->storeAs('profile', $fileName, 'public');
                $data['image'] = $fileName;
            }

            $staff = $this->_userRepository->update($id, $data);

            $staff->syncRoles([$data['role']]);

            DB::commit();
            return $staff;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update staff detail.");

            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $staff = $this->_userRepository->deleteById($id);

            DB::commit();
            return $staff;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete staff.");

            DB::rollBack();
            return null;
        }
    }

    public function updatePassword($id, $data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $staff = $this->_userRepository->update($id, $data);

            DB::commit();
            return $staff;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update staff password.");

            DB::rollBack();
            return null;
        }
    }

    public function getSelectOption($data)
    {
        try {
            $data['result_count'] = 50;
            $data['offset'] = ($data['page'] - 1) * $data['result_count'];

            $staffs = $this->_userRepository->getAllBySearchTerm($data);

            $totalCount = $this->_userRepository->getTotalCountBySearchTerm($data);

            $results = array(
                "results" => $staffs->toArray(),
                "pagination" => array(
                    "more" => $totalCount < $data['offset'] + $data['result_count'] ? false : true
                )
            );

            return $results;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Currently the list didnt have this staff.");
            DB::rollBack();

            return null;
        }
    }
}
