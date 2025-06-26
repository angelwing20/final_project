<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;    

    public function productCategory(){
        return $this->belongsTo(ProductCategory::class,'product_category_id');
    }
}
