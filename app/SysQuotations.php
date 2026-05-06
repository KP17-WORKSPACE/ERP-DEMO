<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysQuotations extends Model
{
    protected $table = 'sys_quotations';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','doc_number','qt_date','customer','currency','narration','sales_man','customer_ref_no','customer_ref_date','delivery_terms','payment_terms','payment_terms2','quote_validity','vat_type','vat_country','vat_state','vat_percentage','vat_number','status','created_by','updated_by','created_at','updated_at','company_id'
    ];

     public function createdby(){
		   return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
     }
    public function customername(){
		  return $this->belongsTo('App\SysCustSuppl', 'customer', 'id');
    }
    public function salesman(){
		  return $this->belongsTo('App\SmStaff', 'sales_man', 'id');
    }
    public function paymentterms(){
		  return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');
    }
    // public function updatedby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'updated_by');
    // }    
}