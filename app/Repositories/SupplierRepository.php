<?php

namespace App\Repositories;

use App\Models\Supplier;

class SupplierRepository extends Repository
{
    protected $_db;

    public function __construct(Supplier $supplier)
    {
        $this->_db = $supplier;
    }

    public function save($data)
    {
        $model = new Supplier();
        $model->name = $data['name'];
        $model->email = $data['email'] ?? null;
        $model->phone = $data['phone'] ?? null;
        $model->address = $data['address'] ?? null;

        $model->save();
        return $model->fresh();
    }

    public function update($id, $data)
    {
        $model = $this->_db->find($id);
        $model->name = $data['name'] ?? $model->name;
        $model->email = array_key_exists('email', $data) ? $data['email'] : $model->email;
        $model->phone = array_key_exists('phone', $data) ? $data['phone'] : $model->email;
        $model->address = array_key_exists('address', $data) ? $data['address'] : $model->email;

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
}
