<?php

namespace App\Repositories;

class Repository
{
    public function getAll()
    {
        //
        $data = $this->_db->get();

        if (!$data) {
            return null;
        }

        return $data;
    }

    public function getById($id, $columns = ['*'], $with = [], $withTrashed = false)
    {
        //
        $query = $this->_db->select($columns);

        if ($with) {
            $query->with($with);
        }

        if ($withTrashed) {
            $query->withTrashed();
        }

        $data = $query->find($id);

        if (!$data) {
            return null;
        }

        return $data;
    }

    public function deleteById($id)
    {
        //
        $data = $this->_db->find($id);

        if (!$data) {
            return null;
        }

        $data->delete();

        return $data;
    }
}
