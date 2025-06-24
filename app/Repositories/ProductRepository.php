<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Supplier;

class ProductRepository extends Repository
{
    protected $_db;

    public function __construct(Product $product)
    {
        $this->_db = $product;
    }

    public function save($data)
    {
        $model = new Product();
        $model->name = $data['name'];

        $model->save();
        return $model->fresh();
    }

    public function update($id, $data)
    {
        $model = $this->_db->find($id);
        $model->name = $data['name'] ?? $model->name;

        $model->update();
        return $model;
    }
}
