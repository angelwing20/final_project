<?php

namespace App\Repositories;

use App\Models\DailySalesItem;

class DailySalesItemRepository extends Repository
{
    protected $_db;

    public function __construct(DailySalesItem $dailySalesItem)
    {
        $this->_db = $dailySalesItem;
    }

    public function save($data)
    {
        $model = new DailySalesItem();
        $model->daily_sales_id = $data['daily_sales_id'];
        $model->item_type = $data['item_type'];
        $model->item_id = $data['item_id'];
        $model->quantity = $data['quantity'];
        $model->price = $data['price'];
        $model->amount = $data['amount'];

        $model->save();
        return $model->fresh();
    }

    public function getByDailySalesId($id)
    {
        return $this->_db->where('daily_sales_id', $id)->get();
    }

    public function bulkSave(array $dataList)
    {
        $items = [];
        foreach ($dataList as $data) {
            $items[] = [
                'daily_sales_id' => $data['daily_sales_id'],
                'item_type' => $data['item_type'],
                'item_id' => $data['item_id'],
                'quantity' => $data['quantity'],
                'price' => $data['price'],
                'amount' => $data['amount'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $this->_db->insert($items);
    }

    public function deleteByDailySalesId($id)
    {
        return $this->_db->where('daily_sales_id', $id)->delete();
    }
}
