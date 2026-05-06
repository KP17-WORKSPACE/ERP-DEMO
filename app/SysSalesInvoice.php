<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysSalesInvoice extends Model
{
    protected $table = 'sys_sales_invoice';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id','ref_qt_id','doc_number','doc_date','customer','currency','printed_invoice_number','lpo_number','lpo_date','payment_terms','payment_terms2','delivery_terms','sales_man','narration','shipping_name','shipping_address','customer_type','sale_type','customer_country','customer_state','end_user_name','contact_person_name','contact_person_email','contact_person_no','status','created_by','updated_by','created_at','updated_at','company_id','dn_id','deal_discount','return_status','deal_id','ref_supplier_id','vat_percent','vat_number','device_serial','shipping_supplier','shipping_contact_no','shipping_email'
    ];

    public function createdby(){
		   return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function salesman(){
		   return $this->belongsTo('App\SmStaff', 'sales_man', 'user_id');
    }
    public function customername(){
		  return $this->belongsTo('App\SysChartofAccounts', 'customer', 'id');
    }
    public function accountname(){
		  return $this->belongsTo('App\SysChartofAccounts', 'customer', 'id');
    }
    public function suppliertype(){
		  return $this->belongsTo('App\SysSupplierType', 'supplier_type', 'id');
    }
    public function paymentterms(){
		  return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');
    }
    public function company(){
        return $this->belongsTo('App\SysCompany', 'company_id', 'id');
    }
    
    public function currency_name(){
		  return $this->belongsTo('App\SysCurrencySettings', 'currency', 'id');
    }
    public function deal_code(){    
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }
    // public function updatedby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'updated_by');
    // }
    
}