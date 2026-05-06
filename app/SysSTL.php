<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysSTL extends Model
{
    protected $table = 'sys_stl';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'doc_number','doc_date','bank','exchange_rate','amount_usd','amount_aed','currency','currency_m','owner_name','bank_representative','vendor','payment_type','pi_no','submition_date','narration','partial_remarks','status','created_by','updated_by','created_at','updated_at','company_id','stl_ref_no','processing_date','settlement_date','stl_interest','bank_charges','other_charges','with_amount'
    ];

    public function vendor_name(){
        return $this->belongsTo('App\SysChartofAccounts', 'vendor', 'id');
    }
    public function bank_dept(){
        return $this->belongsTo('App\SysCustSupplSTL', 'bank', 'stl_bank');
    }
    
    public function bank_name(){
        return $this->belongsTo('App\SysChartofAccounts', 'bank', 'id');
    }

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }

    public function currency_name(){
		  return $this->belongsTo('App\SysCurrencySettings', 'currency', 'id');
    }
    public function currency_name_m(){
		  return $this->belongsTo('App\SysCurrencySettings', 'currency_m', 'id');
    }
}