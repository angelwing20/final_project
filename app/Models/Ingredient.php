<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ingredient extends Model
{
    use HasFactory;

    public function IngredientCategory(){
        return $this->belongsTo(IngredientCategory::class,'ingredient_category_id');
    }
}
