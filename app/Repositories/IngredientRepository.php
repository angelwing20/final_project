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

    public function save($data)
    {
        $model = new Ingredient();
        $model->ingredient_category_id = $data['ingredient_category_id'];
        $model->image = $data['image'] ?? null;
        $model->name = $data['name'];
        $model->weight = $data['weight'] ?? null;
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
        $model->weight = array_key_exists('weight', $data) ? $data['weight'] : $model->weight;
        $model->alarm_weight = $data['alarm_weight'] ?? $model->alarm_weight;
        $model->description = array_key_exists('description', $data) ? $data['description'] : $model->description;


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

    public function getAllBySearchTermAndExcludeProduct($data, $exclude_product_id)
    {

        $name = $data['search_term'] ?? '';

        $data = $this->_db->select('id', 'name')
            ->whereNotIn('id', function ($sub) use ($exclude_product_id) {
                $sub->select('ingredient_id')
                    ->from('product_ingredients')
                    ->where('product_id', $exclude_product_id);
            })
            ->where('name', 'LIKE', "%$name%")
            ->skip($data['offset'])->take($data['result_count'])
            ->get();

        if (empty($data)) {
            return null;
        }
        return $data;
    }

    public function getTotalCountBySearchTermAndExcludeProduct($data, $exclude_product_id)
    {

        $name = $data['search_term'] ?? '';

        $totalCount = $this->_db
            ->whereNotIn('id', function ($sub) use ($exclude_product_id) {
                $sub->select('ingredient_id')
                    ->from('product_ingredients')
                    ->where('product_id', $exclude_product_id);
            })
            ->where('name', 'LIKE', "%$name%")
            ->count();

        return $totalCount;
    }
}
