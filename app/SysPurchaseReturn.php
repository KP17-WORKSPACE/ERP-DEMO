<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseReturn extends Model
{
    protected $table = 'sys_purchase_return';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','doc_number','doc_date','ref_po_id','ref_grn_id','pi_id','pi_number','pi_date','vendors','currency','awbno','lpo_number','lpo_date','bill_number','bill_date','payment_terms','payment_terms2','supplier_remarks','shipping_address_1','shipping_address_2','shipping_name','shipping_contact_no','supplier_type','purchase_type','supplier_country','supplier_state','note','status','created_by','updated_by','created_at','updated_at','company_id','reference','location','warehouse','grn_no','grn_date','salesman_name','narration','deal_id','return_status','sales_person','shipping_supplier','shipping_name','shipping_email','shipping_contact_no','shipping_address_1','supplier_country','supplier_state','vat_percent','vat_number','supplier_type','purchase_type','ref_company_id','sales_person','deal_id','sales_person_name','debit_note'
    ];

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function salesperson(){
        return $this->belongsTo('App\SmStaff', 'sales_person', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
    public function accountname(){
		  return $this->belongsTo('App\SysChartofAccounts', 'vendors', 'id');
    }
    public function currency_name(){
		  return $this->belongsTo('App\SysCurrencySettings', 'currency', 'id');
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
    public function deal_code(){    
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }
    
}
