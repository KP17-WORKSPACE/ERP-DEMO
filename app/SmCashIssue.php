<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmCashIssue extends Model
{
    public function staffDetails(){
    	return $this->belongsTo('App\Smstaff', 'staff_id');
    }
}
