<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysClearanceItems extends Model
{
    protected $table = 'sys_clearance_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','clearance_id','pid','partno','description','coo','hscode','weight','qty','price','totalprice','status','created_by','created_at'
    ];

     public function createdby(){
		   return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
     }
    /*public function customername(){
		  return $this->belongsTo('App\SmSupplier', 'customer', 'id');
    }
    public function suppliertype(){
		  return $this->belongsTo('App\SysSupplierType', 'supplier_type', 'id');
    }
    public function paymentterms(){
		  return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');
    }*/
    // public function updatedby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'updated_by');
    // }
    
}