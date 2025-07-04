<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ingredient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'weight',
        'alarm_weight',
        'ingredient_category_id',
        'image', // 添加 image 字段（如果表中存在）
        'description', // 添加 description（如果需要）
    ];

    public function IngredientCategory(){
        return $this->belongsTo(IngredientCategory::class,'ingredient_category_id');
    }
}


