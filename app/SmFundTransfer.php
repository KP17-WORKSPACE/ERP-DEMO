<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmFundTransfer extends Model
{
    public function bankDeatil(){
    	return $this->belongsTo('App\SmBankAccount', 'bank_account_id');
    }
}
