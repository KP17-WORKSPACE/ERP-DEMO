<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPackingListItemsCart extends Model
{
    protected $table = 'sys_packing_list_items_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','cart_id','boxno','part_number','qty','coo','hscode','weight','dimension','status','created_by','updated_by','created_at','updated_at'
    ];    
}