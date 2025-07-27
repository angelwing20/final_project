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

    public function getAllBySearchTerm($data)
    {

        $name = $data['search_term'] ?? '';

        $data = $this->_db->select('id', 'name')
            ->where('name', 'LIKE', "%$name%")
            ->skip($data['offset'])->take($data['result_count'])
            ->get();

        if (empty($data)) {
            return null;
        }
        return $data;
    }

    public function getTotalCountBySearchTerm($data)
    {

        $name = $data['search_term'] ?? '';

        $totalCount = $this->_db
            ->where('name', 'LIKE', "%$name%")
            ->count();

        return $totalCount;
    }

    public function getByEmail($email)
    {

        $data = $this->_db->where('email', '=', $email)->first();

        if ($data == null) {
            return null;
        }

        return $data;
    }
}
