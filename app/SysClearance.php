<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysClearance extends Model
{
    protected $table = 'sys_clearance';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','invoice_no','invoice_date','free_zone_bill_no','goods_description','bill_to','bill_to_address','ship_to','ship_to_address','status','company_id','created_by','created_at', 'currency','doc_no','box_type','box_qty','cbm','exit_point','destination','deal_id'
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