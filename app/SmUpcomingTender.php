<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SmStaff;
use App\SmSupplier;
use App\SmCompititor;
class SmUpcomingTender extends Model
{
    public function compititors()
    {
        return $this->hasMany('App\SmCompititor', 'tender_id', 'id')->orderBy('company_bid_amount', 'asc');
    }
    public function customers(){
        return $this->belongsTo('App\SmStaff', 'customer_id', 'id');
    }
    public static function getCustomerDetails($id){
        $data = SmStaff::select('full_name','current_address')->find($id); 
        return $data;
    }
    public function vendor(){
        return $this->belongsTo('App\SmSupplier', 'vendor_id', 'id');
    }

    public static function getNumberOfCompetitors($id=null){
    	if($id==null){
    		$number_of_competitor = SmCompititor::all()->count();
    		return $number_of_competitor;
    	}else{
    		$number_of_competitor = SmCompititor::where('tender_id',$id)->count();
    		return $number_of_competitor;
    	}

    }
}
