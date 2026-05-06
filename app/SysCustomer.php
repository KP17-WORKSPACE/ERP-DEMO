<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCustomer extends Model
{
    protected $table = 'sys_customer';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','customer_name','customer_code','contcat_person','mobile','address','email','vat_number','sales_person_name','credit_limit','credit_days','payment_terms','accountant_name','accountant_email','accountant_number','status','created_by','updated_by','created_at','updated_at'
    ];
    
    public function paymentterms(){
		return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');
	}
}
