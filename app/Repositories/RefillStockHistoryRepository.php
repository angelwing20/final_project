<?php

namespace App\Repositories;

use App\Models\RefillStockHistory;

class RefillStockHistoryRepository extends Repository
{
    protected $_db;

    public function __construct(RefillStockHistory $refillStockHistory)
    {
        $this->_db = $refillStockHistory;
    }

    public function save($data)
    {
        $model = new RefillStockHistory();
        $model->ingredient_id = $data['ingredient_id'];
        $model->staff_id = $data['staff_id'];
        $model->quantity = $data['quantity'];
        $model->weight = $data['weight'];
        $model->amount = $data['amount'];

        $model->save();
        return $model->fresh();
    }
}
