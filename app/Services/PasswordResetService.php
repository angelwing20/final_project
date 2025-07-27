<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Repositories\PasswordResetRepository;

class PasswordResetService extends Service
{
    protected $_userRepository;
    protected $_passwordResetRepository;
    public $_expiredMinutes = 30;
    public $_errorMessage = [];

    public function __construct(
        UserRepository $userRepository,
        PasswordResetRepository $passwordResetRepository
    ) {
        $this->_userRepository = $userRepository;
        $this->_passwordResetRepository = $passwordResetRepository;
    }

    public function createPasswordResetRequest($data)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($data, [
                'token' => 'required',
                'user_id' => 'required',
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $passwordReset = $this->_passwordResetRepository->save($data);

            DB::commit();
            return $passwordReset;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Failed to create password reset request.");
            DB::rollBack();
            return null;
        }
    }

    public function passwordReset($data)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:6',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    return $validator->errors()->all();
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $validateTokenAndEmail = $this->validateTokenAndEmail($data['token'], $data['email']);

            if ($validateTokenAndEmail) {
                $passwordReset = $this->_passwordResetRepository->getByEmail($data['email']);

                $this->_userRepository->update($passwordReset->user_id, ['password' => $data['password']]);

                $data['used_at'] = Carbon::now()->toDateTimeString();
                $this->_passwordResetRepository->update($data, $passwordReset->id);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Failed to reset password.");
            DB::rollBack();
            return null;
        }
    }

    public function validateTokenAndEmail($token, $email)
    {
        $passwordReset = $this->_passwordResetRepository->getByEmail($email);

        if (
            $passwordReset == null ||
            $passwordReset->used_at != null ||
            !Hash::check($token, $passwordReset->token)
        ) {
            abort(404);
        } else if (Carbon::now()->greaterThan($passwordReset->created_at->addMinutes($this->_expiredMinutes))) {
            abort(419);
        }

        return true;
    }
}
