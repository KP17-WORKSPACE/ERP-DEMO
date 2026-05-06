<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCustSupplForm extends Model
{
    protected $table = 'sys_cust_suppl_form';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id','group','catid','account_type','customer_salutation','first_name','designation','last_name','name','customer_name_display','code','address','address2','contcat_person','contcat_number','mobile','email','sales_person','vat_type','purchase_type','customer_type','sale_type','supplier_type','vat_country','vat_state','vat_percentage','vat_number','credit_limit','credit_days','payment_terms','customer_documents','status','created_by','updated_by','created_at','updated_at','type','company_id','vat_is_fixed','city','zip_code','transaction_type'
    ];
    
    public function salesperson(){
      return $this->belongsTo('App\SmStaff', 'sales_person', 'user_id');
    }
    public function paymentterms(){
		  return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');
	  }
    public function vatcountry(){
		  return $this->belongsTo('App\SysCountries', 'vat_country', 'id');
	  }
    public function vatstate(){
		  return $this->belongsTo('App\SysStates', 'vat_state', 'id');
	  }
    public function vattype(){
		  return $this->belongsTo('App\SysVatType', 'vat_type', 'id');
	  }

    public function purchasetype(){
		  return $this->belongsTo('App\SysPurchaseType', 'purchase_type', 'id');
	  }
    public function customertype(){
		  return $this->belongsTo('App\SysCustomerType', 'customer_type', 'id');
	  }
    public function saletype(){
		  return $this->belongsTo('App\SysSaleType', 'sale_type', 'id');
	  }
    public function suppliertype(){
		  return $this->belongsTo('App\SysSupplierType', 'supplier_type', 'id');
	  }

    public function createdby(){
      return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }

  

}
