<?php

namespace App\Repositories;

use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;

class PasswordResetRepository extends Repository
{
    protected $_db;

    public function __construct(PasswordReset $passwordReset)
    {
        $this->_db = $passwordReset;
    }

    public function save($data)
    {
        $model = new PasswordReset;
        $model->email = $data['email'];
        $model->user_id = $data['user_id'];
        $model->token = Hash::make($data['token']);
        $model->used_at = null;

        $model->save();
        return $model->fresh();
    }

    public function update($data, $id)
    {
        $model = $this->_db->find($id);
        $model->email = $data['email'] ?? $model->email;
        $model->user_id = $data['user_id'] ?? $model->user_id;
        $model->token = ($data['token'] ?? false) ? Hash::make($data['token']) : $model->token;
        $model->used_at = $data['used_at'] ?? $model->used_at;

        $model->update();
        return $model;
    }

    public function getByEmail($email)
    {
        $data =  $this->_db->where('email', '=', $email)
            ->orderBy('created_at', 'desc')->first();

        if ($data == null) {
            return null;
        }

        return $data;
    }
}
