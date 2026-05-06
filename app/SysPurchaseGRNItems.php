<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseGRNItems extends Model
{
    protected $table = 'sys_purchase_grn_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','grn_id','po_id','part_no','part_number','hscode','qty','unitprice','value','discount','fright','customcharges','taxableamount','vatamount','status','sort_id','description','pi_qty','pi_status'
    ];
    
    public function productdet(){
		  return $this->belongsTo('App\SmItem', 'id', 'part_no');
    }

    public function createdby(){
      return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function suppliername(){
		  return $this->belongsTo('App\SysCustSuppl', 'vendors', 'id');
    }
    public function suppliertype(){
		  return $this->belongsTo('App\SysSupplierType', 'supplier_type', 'id');
    }
    public function paymentterms(){
		  return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');
    }
    
}