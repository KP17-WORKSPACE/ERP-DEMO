<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmProductPartnumber extends Model
{
    public function productReceivedDetail(){
    	return $this->belongsTo('App\SmItemReceive', 'product_receive_id', 'id');
    }

    public function supplierDetail(){
    	return $this->belongsTo('App\SmSupplier', 'supplier_id', 'id');
    }
}
