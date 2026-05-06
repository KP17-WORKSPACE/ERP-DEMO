<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmInvestment extends Model
{
    public function staffDetail(){
    	return $this->belongsTo('App\SmStaff', 'staff_id');
    }
}
