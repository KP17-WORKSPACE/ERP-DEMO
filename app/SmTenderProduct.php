<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmTenderProduct extends Model
{
    public function product(){
    	return $this->belongsTo('App\SmItemReceive', 'product_id', 'product_id');
    }
}
