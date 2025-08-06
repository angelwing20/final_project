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
        $model->unit_type = $data['unit_type'];
        $model->stock = $data['stock'] ?? null;
        $model->min_stock = $data['min_stock'];
        $model->weight_unit = $data['weight_unit'];
        $model->price = $data['price'];


        $model->save();
        return $model->fresh();
    }

    public function update($id, $data)
    {
        $model = $this->_db->find($id);
        $model->ingredient_category_id = $data['ingredient_category_id'] ?? $model->ingredient_category_id;
        $model->image = array_key_exists('image', $data) ? $data['image'] : $model->image;
        $model->name = $data['name'] ?? $model->name;
        $model->unit_type = $data['unit_type'] ?? $model->unit_type;
        $model->stock = array_key_exists('stock', $data) ? $data['stock'] : $model->stock;
        $model->min_stock = $data['min_stock'] ?? $model->min_stock;
        $model->weight_unit = $data['weight_unit'] ?? $model->weight_unit;
        $model->price = $data['price'] ?? $model->price;


        $model->update();
        return $model;
    }

    public function getAllBySearchTerm($data)
    {

        $name = $data['search_term'] ?? '';

        $data = $this->_db->select('id', 'name', 'unit_type', 'weight_unit')
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

    public function getAllBySearchTermAndExcludeFood($data, $exclude_food_id)
    {

        $name = $data['search_term'] ?? '';

        $data = $this->_db->select('id', 'name', 'unit_type', 'weight_unit')
            ->whereNotIn('id', function ($sub) use ($exclude_food_id) {
                $sub->select('ingredient_id')
                    ->from('food_ingredients')
                    ->where('food_id', $exclude_food_id);
            })
            ->where('name', 'LIKE', "%$name%")
            ->skip($data['offset'])->take($data['result_count'])
            ->get();

        if (empty($data)) {
            return null;
        }
        return $data;
    }

    public function getTotalCountBySearchTermAndExcludeFood($data, $exclude_food_id)
    {

        $name = $data['search_term'] ?? '';

        $totalCount = $this->_db
            ->whereNotIn('id', function ($sub) use ($exclude_food_id) {
                $sub->select('ingredient_id')
                    ->from('food_ingredients')
                    ->where('food_id', $exclude_food_id);
            })
            ->where('name', 'LIKE', "%$name%")
            ->count();

        return $totalCount;
    }

    public function getAllBySearchTermAndExcludeAddOn($data, $exclude_add_on_id)
    {

        $name = $data['search_term'] ?? '';

        $data = $this->_db->select('id', 'name', 'unit_type', 'weight_unit')
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

    public function updateStock($id, $newStock)
    {
        $ingredient = $this->getById($id);

        if (!$ingredient) {
            return null;
        }

        $ingredient->stock = max(0, $newStock);
        $ingredient->save();

        return $ingredient->fresh();
    }
}
