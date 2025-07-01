<?php

namespace App\Repositories;

use App\Models\IngredientCategory;

class IngredientCategoriesRepository extends Repository
{
    protected $_db;

    public function __construct(IngredientCategory $ingredientCategory)
    {
        $this->_db = $ingredientCategory;
    }

    public function save($data)
    {
        $model = new IngredientCategory();
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

    public function getAllBySearchTerm($data)
    {

        $name = $data['search_term'] ?? '';

        $data = $this->_db->select('id', 'name')
            ->where('name', 'LIKE', "%$name%")
            ->skip($data['offset'])->take($data['result_count'])
            ->get();

        if (empty($data)) {
            return null;
        }
        return $data;
    }

    public function getTotalCountBySearchTerm($data)
    {

        $name = $data['search_term'] ?? '';

        $totalCount = $this->_db
            ->where('name', 'LIKE', "%$name%")
            ->count();

        return $totalCount;
    }
}
