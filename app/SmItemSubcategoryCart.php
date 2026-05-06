<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmItemSubcategoryCart extends Model
{
    protected $table = 'sm_item_subcategory_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'category_name',
        'sub_category_name',
        'company_id',
        'created_by',
        'status',
        'created_at',
        'updated_at'
    ];
}
