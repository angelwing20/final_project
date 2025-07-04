<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository extends Repository
{
    protected $_db;

    public function __construct(User $user)
    {
        $this->_db = $user;
    }

    public function save($data)
    {
        $model = new User();
        $model->name = $data['name'];
        $model->email = $data['email'];
        $model->image = $data['image'] ?? null;
        $model->password = Hash::make($data['password']);

        $model->save();
        return $model->fresh();
    }

    public function update($id, $data)
    {
        $model = $this->_db->find($id);
        $model->name = $data['name'] ?? $model->name;
        $model->email = $data['email'] ?? $model->email;
        $model->image = array_key_exists('image', $data) ? $data['image'] : $model->image;
        $model->password = !empty($data['password']) ? Hash::make($data['password']) : $model->password;

        $model->update();
        return $model;
    }
}
