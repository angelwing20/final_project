<?php

namespace App\Repositories;

use App\Models\SupplyHistory;

class SupplyHistoryRepository extends Repository
{
    protected $_db;

    public function __construct(SupplyHistory $supplierHistory)
    {
        $this->_db = $supplierHistory;
    }

    public function save($data)
    {
        $model = new SupplyHistory();
        $model->ingredient_id = $data['ingredient_id'];
        $model->supplier_id = $data['supplier_id'];
        $model->weight = $data['weight'];

        $model->save();
        return $model->fresh();
    }

    public function update($id, $data)
    {
        $model = $this->_db->find($id);
        $model->ingredient_id = $data['ingredient_id'] ?? $model->ingredient_id;
        $model->supplier_id = $data['supplier_id'] ?? $model->supplier_id;
        $model->weight = $data['weight'] ?? $model->weight;

        $model->update();
        return $model;
    }
}
