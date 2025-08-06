<?php

namespace App\Repositories;

use App\Models\Food;

class FoodRepository extends Repository
{
    protected $_db;

    public function __construct(Food $food)
    {
        $this->_db = $food;
    }

    public function save($data)
    {
        $model = new Food();
        $model->food_category_id = $data['food_category_id'];
        $model->name = $data['name'];
        $model->price = $data['price'];
        $model->description = $data['description'] ?? null;
        $model->image = $data['image'] ?? null;

        $model->save();
        return $model->fresh();
    }

    public function update($id, $data)
    {
        $model = $this->_db->find($id);
        $model->food_category_id = $data['food_category_id'] ?? $model->food_category_id;
        $model->name = $data['name'] ?? $model->name;
        $model->price = $data['price'] ?? $model->price;
        $model->description = array_key_exists('description', $data) ? $data['description'] : $model->description;
        $model->image = array_key_exists('image', $data) ? $data['image'] : $model->image;

        $model->update();
        return $model;
    }

    public function getByName($name)
    {
        return $this->_db->where('name', $name)->first();
    }

    public function getAll()
    {
        return Food::with(['ingredients.ingredient'])->get();
    }
}
