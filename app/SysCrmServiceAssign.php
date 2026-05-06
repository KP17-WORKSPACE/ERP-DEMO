<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmServiceAssign extends Model
{
    protected $table = 'sys_crm_service_assign';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'deal_id','user_id','status','created_by','updated_by','created_at','updated_at'
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function dealid(){
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }
    public function userid(){
        return $this->belongsTo('App\SmStaff', 'user_id', 'user_id');
    }
}