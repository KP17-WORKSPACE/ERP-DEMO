<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysSalesInvoiceCFCharges extends Model
{
    protected $table = 'sys_sales_invoice_cf_charges';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'si_id','si_doc_number','cfc_name','cfc_credit_account','cfc_amount','cfc_cal_amount','cfc_remarks','cfc_currency','cfc_exe_rate','status','created_by','updated_by','created_at','updated_at','date','bill_number'
    ];

    public function cfccreditaccount(){
        return $this->belongsTo('App\SysCustSuppl', 'cfc_credit_account', 'id');
    }
}
