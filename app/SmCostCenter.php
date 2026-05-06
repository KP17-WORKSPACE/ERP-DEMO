<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmCostCenter extends Model
{
    public  static function getCostCenterName($id){

    	$costcenetr = SmCostCenter::find($id);	
    
    	if($costcenetr != ""){
    		$name = $costcenetr->name;
    	}else{
    		$name = "";
    	}
    	return $name;
    }
}
