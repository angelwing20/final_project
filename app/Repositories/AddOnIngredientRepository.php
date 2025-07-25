<?php

namespace App\Repositories;

use App\Models\AddOnIngredient;

class AddOnIngredientRepository extends Repository
{
    protected $_db;

    public function __construct(AddOnIngredient $addOnIngredient)
    {
        $this->_db = $addOnIngredient;
    }

    public function save($data)
    {
        $model = new AddOnIngredient();
        $model->add_on_id = $data['add_on_id'];
        $model->ingredient_id = $data['ingredient_id'];
        $model->weight = $data['weight'];

        $model->save();
        return $model->fresh();
    }

    public function update($id, $data)
    {
        $model = $this->_db->find($id);
        $model->add_on_id = $data['add_on_id'] ?? $model->add_on_id;
        $model->ingredient_id = $data['ingredient_id'] ?? $model->ingredient_id;
        $model->weight = $data['weight'] ?? $model->weight;

        $model->update();
        return $model;
    }

    public function getByAddOnId($addOnId)
    {
        return $this->_db->where('add_on_id', $addOnId)->get();
    }
}
