<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseReturnAdjestment extends Model
{
    protected $table = 'sys_purchase_return_adjestment';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','pri_no','piv_no','lpo_no','doc_date','total_amount','paid_amount','balance_amount','status','created_by','updated_by','created_at','updated_at'
    ];

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }    
}