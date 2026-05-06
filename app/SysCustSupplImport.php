<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCustSupplImport extends Model
{
    protected $table = 'sys_cust_suppl_import';
    protected $primaryKey = 'id';

    protected $fillable = [
        'account_type','customer_salutation','first_name','last_name','name','customer_name_display','address','address2','contcat_person_salutation','contcat_person_first_name','contcat_person_last_name','designation','contcat_number','mobile','email','sales_person','customer_type','sale_type','vat_country','vat_percentage','vat_number','credit_limit','credit_days','payment_terms','transaction_type','vat_is_fixed','city','zip_code','status','created_by','created_at','company_id','country','state'
    ];

}