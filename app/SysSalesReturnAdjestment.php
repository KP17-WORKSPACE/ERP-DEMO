<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysSalesReturnAdjestment extends Model
{
    protected $table = 'sys_sales_return_adjestment';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','srn_no','dln_no','siv_no','doc_date','total_amount','paid_amount','balance_amount','status','created_by','updated_by','created_at','updated_at','lpo_number','narration'
    ];

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }

    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
}