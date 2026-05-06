<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmDealReturnPayable extends Model
{
    protected $table = 'sys_crm_deal_return_payable';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'ret_id','deal_id','mode_of_pay','remarks','status','created_by','updated_by','created_at','updated_at'
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function dealid(){
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }
}