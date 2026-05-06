<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmQuoteCSItems extends Model
{
    protected $table = 'sys_crm_quote_cs_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'user_id','deal_id','company_id','currency_id','customer_type','payment_terms','delivery_date','status','created_by','updated_by','created_at','updated_at','description','work_stations','price_per_month','critical_assets','additional_critical_assets','price_per_critical_asset','total_price_per_month'
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function username(){
        return $this->belongsTo('App\SmStaff', 'user_id', 'user_id');
    }
    public function currency(){
        return $this->belongsTo('App\SysCurrency', 'currency_id', 'id');
    }
    public function paymentterms(){
        return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');
    }
    public function company(){
        return $this->belongsTo('App\SysCompany', 'company_id', 'id');
    }
}