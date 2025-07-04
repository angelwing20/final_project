<?php

namespace App\Repositories;

use App\Models\Ingredient;


class IngredientRepository extends Repository
{
    protected $_db;

    public function __construct(Ingredient $ingredient)
    {
        $this->_db = $ingredient;
    }

    public function getAll()
    {
        return $this->_db->all();
    }

    public function getLowStockIngredients()
    {

       $results = $this->_db
        ->whereRaw('CAST(weight AS DECIMAL(10,2)) < CAST(alarm_weight AS DECIMAL(10,2))')
        ->orderByRaw('CAST(weight AS DECIMAL(10,2)) ASC') // 👈 加这行
        ->with('ingredientCategory')
        ->get();


        return $results;
    }


    public function save($data)
    {
        $model = new Ingredient();
        $model->ingredient_category_id = $data['ingredient_category_id'];
        $model->image = $data['image'] ?? null;
        $model->name = $data['name'];
        $model->weight = $data['weight'];
        $model->alarm_weight = $data['alarm_weight'];
        $model->description = $data['description'] ?? null;


        $model->save();
        return $model->fresh();
    }

    public function update($id, $data)
    {
        $model = $this->_db->find($id);
        $model->ingredient_category_id = $data['ingredient_category_id'] ?? $model->ingredient_category_id;
        $model->image = array_key_exists('image', $data) ? $data['image'] : $model->image;
        $model->name = $data['name'] ?? $model->name;
        $model->weight = $data['weight'] ?? $model->weight;
        $model->alarm_weight = $data['alarm_weight'] ?? $model->alarm_weight;
        $model->description = array_key_exists('description', $data) ? $data['description'] : $model->description;


        $model->update();
        return $model;
    }
}
