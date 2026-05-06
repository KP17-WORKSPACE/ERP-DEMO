<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmItemsCart extends Model
{
    protected $table = 'sm_items_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'item_code', 'part_number','brand','product_type','category_name','subcategory_name','description','vat','uom','coo','hscode','weight','status','created_by','created_at','company_id'
    ];

}