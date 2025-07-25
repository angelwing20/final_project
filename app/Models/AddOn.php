<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddOn extends Model
{
    public function ingredients()
    {
        return $this->hasMany(AddOnIngredient::class);
    }
}
