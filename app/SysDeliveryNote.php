<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysDeliveryNote extends Model
{
    protected $table = 'sys_delivery_note';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','ref_si_id','doc_number','doc_date','customer_id','narration', 'currency','salesman','lpo_no','lpo_date','issued_by','received_by','warehouse','driver','vehicleno','paymentterms','invoice_no','invoice_date','status','created_by','updated_by','created_at','updated_at','company_id','supplier_name','deal_id','shipping_name','shipping_address','customer_type','sale_type','customer_country','customer_state','end_user_name','contact_person_name','contact_person_email','contact_person_no','ref_customer_id','vat_percent','vat_number','shipping_supplier','shipping_contact_no','shipping_email'
    ];
    public function accountname(){
		  return $this->belongsTo('App\SysChartofAccounts', 'customer_id', 'id');
    }
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
    public function payment_terms(){
		  return $this->belongsTo('App\SysPaymentTerms', 'paymentterms', 'id');
    }
    public function currency_name(){
		  return $this->belongsTo('App\SysCurrencySettings', 'currency', 'id');
    }
}