<?php

namespace App\Repositories;

use App\Models\FoodIngredient;

class FoodIngredientRepository extends Repository
{
    protected $_db;

    public function __construct(FoodIngredient $foodIngredient)
    {
        $this->_db = $foodIngredient;
    }

    public function save($data)
    {
        $model = new FoodIngredient();
        $model->food_id = $data['food_id'];
        $model->ingredient_id = $data['ingredient_id'];
        $model->consumption = $data['consumption'];

        $model->save();
        return $model->fresh();
    }

    public function getByFoodId($foodId)
    {
        return $this->_db->where('food_id', $foodId)->get();
    }
}
