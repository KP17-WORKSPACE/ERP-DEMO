<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseGRN extends Model
{
    protected $table = 'sys_purchase_grn';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','doc_number','po_id','vendors','grn_date','currency','lpo_number','lpo_date','payment_terms','bill_number','bill_date','awbno','boeno','warehouse','reference','narration','status','created_by','updated_by','created_at','updated_at','company_id','deal_id','salesman_name','pi_status','sales_person','shipping_supplier','shipping_name','shipping_email','shipping_contact_no','shipping_address_1','supplier_country','supplier_state','vat_percent','vat_number','supplier_type','purchase_type','ref_company_id','sales_person_name'
    ];
    
    public function productdet(){
		  return $this->belongsTo('App\SmItem', 'id', 'part_no');
    }
    public function createdby(){
      return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function salesperson(){
      return $this->belongsTo('App\SmStaff', 'sales_person', 'user_id');
    }
    public function suppliername(){
		  return $this->belongsTo('App\SysCustSuppl', 'vendors', 'id');
    }
    public function accountname(){
		  return $this->belongsTo('App\SysChartofAccounts', 'vendors', 'id');
    }
    public function suppliertype(){
		  return $this->belongsTo('App\SysSupplierType', 'supplier_type', 'id');
    }
    public function paymentterms(){
		  return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');
    }
    public function currency_name(){
		  return $this->belongsTo('App\SysCurrencySettings', 'currency', 'id');
    }

       public function shippingSupplierName()
{
    return $this->belongsTo('App\SysChartofAccounts', 'shipping_supplier', 'id');
}

    /**
     * All purchase invoices which reference this GRN number
     */
    public function invoices()
    {
        return $this->hasMany('App\SysPurchaseInvoice', 'grn_no', 'doc_number');
    }

    /**
     * Optionally relate by ref_grn_id FK
     */
    public function invoicesById()
    {
        return $this->hasMany('App\SysPurchaseInvoice', 'ref_grn_id', 'id');
    }
}