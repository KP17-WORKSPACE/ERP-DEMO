<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseReturn extends Model
{
    protected $table = 'sys_purchase_return';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'doc_number','doc_date','pi_id','pi_number','currency','narration', 'bill_number','bill_date','supplier_id','supplier_reference','supplier_country','supplier_state','purchase_type', 'status','created_by','updated_by','created_at','updated_at','company_id','shipping_supplier','shipping_name','shipping_email','shipping_contact_no','shipping_address_1','supplier_country','supplier_state','vat_percent','vat_number','supplier_type','purchase_type','ref_company_id'
    ];

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
    
}
