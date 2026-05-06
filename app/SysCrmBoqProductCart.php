<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmBoqProductCart extends Model
{
    protected $table = 'product_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','user_id','cart_id','product_id','description','qty','price','discount','status','created_at','updated_at'
    ];
}