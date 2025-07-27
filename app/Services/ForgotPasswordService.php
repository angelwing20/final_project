<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Str;
use App\Mail\PasswordResetRequest;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class ForgotPasswordService extends Service
{
    protected $_userRepository;
    protected $_passwordResetService;

    public function __construct(
        UserRepository $userRepository,
        PasswordResetService $passwordResetService
    ) {
        $this->_userRepository = $userRepository;
        $this->_passwordResetService = $passwordResetService;
    }

    public function forgotPassword($data)
    {
        DB::beginTransaction();
        try {
            $rateLimiterKey = 'forgot_password|' . request()->ip();
            $maxAttempts = 3;
            $decaySeconds = 600;

            if (RateLimiter::tooManyAttempts($rateLimiterKey, $maxAttempts)) {
                $seconds = RateLimiter::availableIn($rateLimiterKey);

                array_push($this->_errorMessage, "Too many attempts. Please try again in " . $seconds . " seconds.");
                return null;
            }

            RateLimiter::increment($rateLimiterKey, $decaySeconds);

            $validator = Validator::make($data, [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }

                return null;
            }

            $user = $this->_userRepository->getByEmail($data['email']);

            if ($user != null) {
                $data['user_id'] = $user->id;
                $data['token'] = Str::random(30);
                $data['expired_minutes'] = $this->_passwordResetService->_expiredMinutes;

                $passwordReset = $this->_passwordResetService->createPasswordResetRequest($data);

                Mail::to($data['email'])->send(new PasswordResetRequest($data));
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Failed to send reset password email.");
            DB::rollBack();
            return null;
        }
    }
}
