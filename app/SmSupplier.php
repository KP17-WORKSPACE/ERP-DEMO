<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmSupplier extends Model
{
    //
    
    public function paymentterms(){
		return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');
	}
}
