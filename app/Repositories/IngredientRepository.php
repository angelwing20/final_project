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
        $model->unit_price = $data['unit_price'];


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
        $model->unit_price = $data['unit_price'] ?? $model->unit_price;


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

    public function getAllBySearchTermAndExcludeAddOn($data, $exclude_add_on_id)
    {

        $name = $data['search_term'] ?? '';

        $data = $this->_db->select('id', 'name')
            ->whereNotIn('id', function ($sub) use ($exclude_add_on_id) {
                $sub->select('ingredient_id')
                    ->from('add_on_ingredients')
                    ->where('add_on_id', $exclude_add_on_id);
            })
            ->where('name', 'LIKE', "%$name%")
            ->skip($data['offset'])->take($data['result_count'])
            ->get();

        if (empty($data)) {
            return null;
        }
        return $data;
    }

    public function getTotalCountBySearchTermAndExcludeAddOn($data, $exclude_add_on_id)
    {

        $name = $data['search_term'] ?? '';

        $totalCount = $this->_db
            ->whereNotIn('id', function ($sub) use ($exclude_add_on_id) {
                $sub->select('ingredient_id')
                    ->from('add_on_ingredients')
                    ->where('add_on_id', $exclude_add_on_id);
            })
            ->where('name', 'LIKE', "%$name%")
            ->count();

        return $totalCount;
    }

    public function find($id)
    {
        return $this->_db->find($id);
    }

    public function updateWeight($id, $newWeight)
    {
        $ingredient = $this->_db->find($id);
        if (!$ingredient) {
            return null;
        }

        $ingredient->weight = max(0, $newWeight);
        $ingredient->save();

        return $ingredient->fresh();
    }
}
