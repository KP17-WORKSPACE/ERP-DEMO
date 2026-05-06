<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysSalesReturn extends Model
{
    protected $table = 'sys_sales_return';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','ref_qt_id','doc_number','doc_date','customer','currency','printed_invoice_number','lpo_number','lpo_date','payment_terms','payment_terms2','delivery_terms','sales_man','narration','shipping_name','shipping_address','customer_type','sale_type','customer_country','customer_state','end_user_name','contact_person_name','contact_person_email','contact_person_no','status','created_by','updated_by','created_at','updated_at','company_id','dn_doc_number','dn_doc_date','si_doc_number','si_doc_date','attachment','credit_note','ref_supplier_id'
    ];

    public function accountname(){
        return $this->belongsTo('App\SysChartofAccounts', 'customer', 'id');
    }
    public function paymentterms(){
		  return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');
    }
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
    public function currency_name(){
        return $this->belongsTo('App\SysCurrencySettings', 'currency', 'id');
    }    
    public function salesman_name(){
        return $this->belongsTo('App\SmStaff', 'sales_man', 'user_id'); 
    }
}
