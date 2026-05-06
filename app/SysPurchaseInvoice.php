<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseInvoice extends Model
{
    protected $table = 'sys_purchase_invoice';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','ref_po_id','ref_grn_id','doc_number','pi_date','vendors','currency','awbno','boeno','lpo_number','lpo_date','bill_number','bill_date','payment_terms','payment_terms2','supplier_remarks','shipping_address_1','shipping_address_2','shipping_name','shipping_contact_no','supplier_type','purchase_type','supplier_country','supplier_state','note','status','created_by','updated_by','created_at','updated_at','company_id','reference','location','warehouse','grn_no','grn_date','salesman_name','narration','deal_id','return_status','sales_person','vat_percent','vat_number','shipping_supplier','shipping_email'
    ];

    public function createdby(){
      return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function suppliername(){
		  return $this->belongsTo('App\SysCustSuppl', 'vendors', 'id');
    }
    public function salesperson(){
      return $this->belongsTo('App\SmStaff', 'sales_person', 'user_id');
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
    public function deal_code(){    
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }

    public function shippingSupplierName()
{
    return $this->belongsTo('App\SysChartofAccounts', 'shipping_supplier', 'id');
}

    /**
     * Link the invoice to its GRN by matching grn_no to sys_purchase_grn.doc_number
     */
    public function grn()
    {
        return $this->belongsTo('App\SysPurchaseGRN', 'grn_no', 'doc_number');
    }

    /**
     * Alias for backwards compatibility if field ref_grn_id used as foreign key
     */
    public function grnById()
    {
        return $this->belongsTo('App\SysPurchaseGRN', 'ref_grn_id', 'id');
    }

    // public function updatedby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'updated_by');
    // }    
}