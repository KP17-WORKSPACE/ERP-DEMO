<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseAuto extends Model
{
    protected $table = 'sys_purchase_auto';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','cart_id ','deal_id','req_po','req_grn','req_pi','req_pay','req_mode_acc','req_cost','status','company_id','created_by','created_at','po_no','grn_no','pi_no','pay_no','updated_at','po_id','grn_id','pi_id','pay_id'
    ];

}