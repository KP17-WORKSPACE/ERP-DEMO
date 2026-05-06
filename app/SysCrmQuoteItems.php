<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmQuoteItems extends Model
{
    protected $table = 'sys_crm_quote_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'user_id','deal_id','company_id','currency_id','customer_type','product_id','qty','price','discount','vat','cost','status','created_by','updated_by','created_at','updated_at','description','payment_terms','delivery_date','payment_terms_txt','delivery_time','sort_id','quote_id','quote_discount','quote_validity','po_qty','si_qty','dn_qty','product_type','document_number'
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function username(){
        return $this->belongsTo('App\SmStaff', 'user_id', 'user_id');
    }
    public function productname(){
        return $this->belongsTo('App\SmItem', 'product_id', 'id');
    }
    public function currency(){
        return $this->belongsTo('App\SysCurrency', 'currency_id', 'id');
    }
    public function dealname(){
        return $this->belongsTo('App\SysCustSuppl', 'cust_id', 'id');
    }
    public function paymentterms(){
        return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');
    }
    public function company(){
        return $this->belongsTo('App\SysCompany', 'company_id', 'id');
    }
}