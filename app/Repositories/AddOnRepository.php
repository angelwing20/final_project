<?php

namespace App\Repositories;

use App\Models\AddOn;

class AddOnRepository extends Repository
{
    protected $_db;

    public function __construct(AddOn $addOn)
    {
        $this->_db = $addOn;
    }

    public function save($data)
    {
        $model = new AddOn();
        $model->image = $data['image'] ?? null;
        $model->name = $data['name'];
        $model->price = $data['price'];

        $model->save();
        return $model->fresh();
    }

    public function update($id, $data)
    {
        $model = $this->_db->find($id);
        $model->image = array_key_exists('image', $data) ? $data['image'] : $model->image;
        $model->name = $data['name'] ?? $model->name;
        $model->price = $data['price'] ?? $model->price;

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

    public function getAll()
    {
        return AddOn::with(['ingredients.ingredient'])->get();
    }
}
