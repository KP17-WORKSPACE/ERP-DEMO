<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysSTLPayment extends Model
{
    protected $table = 'sys_stl_payment';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'stl_id','payment_req_date','payment_stl_no','payment_stl_ref_no','payment_supplier_id','payment_supplier_name','payment_set_amount','payment_settlement_date','status','created_by','updated_by','created_at','updated_at','company_id'
    ];

    public function stl(){
        return $this->belongsTo('App\SysSTL', 'stl_id', 'id');
    }
}