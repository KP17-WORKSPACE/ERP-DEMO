<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseDlnLicenseKey extends Model
{
    protected $table = 'sys_purchase_dln_license_key';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','cart_id','dn_id','item_id','license_key','exp_date','license_qty','status','created_by','created_at','updated_by','updated_at','company_id','grn_id'
    ];    
}