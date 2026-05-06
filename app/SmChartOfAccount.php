<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmChartOfAccount extends Model
{
    public static function getHeadChartOfAccount($id=null){

    	if($id != ""){
    		$data = SmChartOfAccount::find($id);
    		$name = !empty($data)? $data->head:''; 
    	}else{
    		$name ="";
    	}
    	return $name;
    }
}
