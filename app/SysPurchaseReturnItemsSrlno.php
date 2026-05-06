<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseReturnItemsSrlno extends Model
{
    protected $table = 'sys_purchase_return_items_srlno';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','prt_id','piv_id','part_no','srl_no','status'
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