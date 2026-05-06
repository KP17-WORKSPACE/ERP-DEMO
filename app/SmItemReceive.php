<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmItemReceive extends Model
{
    public function supplier(){
    	return $this->belongsTo('App\SmSupplier', 'supplier_id', 'id');
    }

    public function productDetail(){
    	return $this->belongsTo('App\SmItem', 'product_id', 'id');
    }

    
}
