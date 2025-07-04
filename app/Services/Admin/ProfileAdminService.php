<?php

namespace App\Services\Admin;

use App\Repositories\UserRepository;
use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProfileAdminService extends Service
{
    private $_userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->_userRepository = $userRepository;
    }

    public function getProfile()
    {
        try {
            $id = Auth::user()->id;
            $user = $this->_userRepository->getById($id);

            if ($user == null) {
                throw new Exception();
            }
            return $user;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get account profile.");
            return null;
        }
    }

    public function generateFileName()
    {
        return Str::random(5) . Str::uuid() . Str::random(5);
    }

    public function update($data)
    {
        DB::beginTransaction();

        try {
            $id = Auth::user()->id;

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:512000',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $profile = $this->_userRepository->getById($id);

            if ($profile == null) {
                throw new Exception();
            }

            if (!empty($data['image'])) {
                if ($profile['image'] != null && Storage::disk('public')->exists('profile/' . $profile['image'])) {
                    Storage::disk('public')->delete('profile/' . $profile['image']);
                }

                $fileName = $this->generateFileName();
                $fileExtension = $data['image']->extension();
                $fileName = $fileName . '.' . $fileExtension;

                $data['image']->storeAs('profile', $fileName, 'public');
                $data['image'] = $fileName;
            }

            $profile = $this->_userRepository->update($id, $data);

            DB::commit();
            return $profile;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update account profile.");

            DB::rollBack();
            return null;
        }
    }

    public function updatePassword($data)
    {
        DB::beginTransaction();

        try {
            $id = Auth::user()->id;

            $validator = Validator::make($data, [
                'current_password' => 'required|current_password',
                'password' => 'required|confirmed|min:6',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $user = $this->_userRepository->update($id, $data);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update account password.");

            DB::rollBack();
            return null;
        }
    }
}
