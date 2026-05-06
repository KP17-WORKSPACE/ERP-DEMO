<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmItemCategoryCart extends Model
{
    protected $table = 'sm_item_category_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'category_name',
        'company_id',
        'created_by',
        'status',
        'created_at',
        'updated_at'
    ];
}
