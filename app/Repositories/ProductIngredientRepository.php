<?php

namespace App\Repositories;

use App\Models\ProductIngredient;

class ProductIngredientRepository extends Repository
{
    protected $_db;

    public function __construct(ProductIngredient $productIngredient)
    {
        $this->_db = $productIngredient;
    }

    public function save($data)
    {
        $model = new ProductIngredient();
        $model->product_id = $data['product_id'];
        $model->ingredient_id = $data['ingredient_id'];
        $model->consumption = $data['consumption'];

        $model->save();
        return $model->fresh();
    }

    public function update($id, $data)
    {
        $model = $this->_db->find($id);
        $model->product_id = $data['product_id'] ?? $model->product_id;
        $model->ingredient_id = $data['ingredient_id'] ?? $model->ingredient_id;
        $model->consumption = $data['consumption'] ?? $model->consumption;

        $model->update();
        return $model;
    }

    public function getByProductId($productId)
    {
        return $this->_db->where('product_id', $productId)->get();
    }
}
