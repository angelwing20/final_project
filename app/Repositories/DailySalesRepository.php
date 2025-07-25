<?php

namespace App\Repositories;

use App\Models\DailySales;


class DailySalesRepository extends Repository
{
    protected $_db;

    public function __construct(DailySales $dailySales)
    {
        $this->_db = $dailySales;
    }

    public function save($data)
    {
        $model = new DailySales();
        $model->total_quantity = $data['total_quantity'];
        $model->total_amount = $data['total_amount'];
        $model->staff_id = $data['staff_id'];

        $model->save();
        return $model->fresh();
    }

    public function update($id, $data)
    {
        $model = $this->_db->find($id);
        $model->total_quantity = $data['total_quantity'] ?? $model->total_quantity;
        $model->total_amount = $data['total_amount'] ?? $model->total_amount;
        $model->staff_id = $data['staff_id'] ?? $model->staff_id;

        $model->update();
        return $model;
    }
}
