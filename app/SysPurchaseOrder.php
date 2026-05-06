<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseOrder extends Model
{
    protected $table = 'sys_purchase_order';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','doc_number','po_date','vendors','currency','narration','delivery_date','payment_terms','payment_terms2','supplier_remarks','shipping_address_1','shipping_address_2','shipping_name','shipping_contact_no','supplier_type','purchase_type','supplier_country','supplier_state','note','status','created_by','updated_by','created_at','updated_at','company_id','grn_status','salesman_name','deal_id','shipping_supplier','shipping_email','contact_person_name','contact_person_email','contact_person_telephone','internal_transfer','bill_number','bill_date','awbno','boeno','reference','sales_person','vat_percent','vat_number','ref_company_id','payment_id','sales_person_name','property_name','property_value'
    ];

    public function createdby(){
      return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function salesperson(){
      return $this->belongsTo('App\SmStaff', 'sales_person', 'user_id');
    }    
    public function suppliername(){
		  return $this->belongsTo('App\SysChartofAccounts', 'vendors', 'id');
    }
    public function accountname(){
		  return $this->belongsTo('App\SysChartofAccounts', 'vendors', 'id');
    }
    public function shippingsupplier(){
		  return $this->belongsTo('App\SysChartofAccounts', 'shipping_supplier', 'id');
    }
    public function suppliertype(){
		  return $this->belongsTo('App\SysSupplierType', 'supplier_type', 'id');
    }
    public function paymentterms(){
		  return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');
    }
    public function productdet(){
		  return $this->belongsTo('App\SmItem', 'id', 'part_number');
    }
    public function currency_name(){
		  return $this->belongsTo('App\SysCurrencySettings', 'currency', 'id');
    }

    
    
}