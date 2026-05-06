<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPaymentTerms extends Model
{
    protected $table = 'sys_payment_terms';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','title ','active_status','created_by','updated_by','created_at','updated_at'
    ];
    
    public static function getPaymentTermsName($id){
    	if(!empty($id)){
    		$item = SysPaymentTerms::find($id);
    		return @$item->title;
    	}else{
    		return 'NA';
    	}
    }

    public function createdby(){
		return $this->belongsTo('App\SmStaff', 'created_by', 'id');
    }
    public function updatedby(){
		return $this->belongsTo('App\SmStaff', 'updated_by', 'id');
	}
}
