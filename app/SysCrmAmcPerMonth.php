<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmAmcPerMonth extends Model
{
    protected $table = 'sys_crm_amc_per_month';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','deal_id','amc_amount','amc_date','owner','status','created_by','created_at'
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function dealid(){
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }
    public function ownername(){
        return $this->belongsTo('App\SmStaff', 'owner', 'user_id');
    }
}