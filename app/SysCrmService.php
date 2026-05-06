<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmService extends Model
{
    protected $table = 'sys_crm_service';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','deal_id','subject','comments','status','created_by','updated_by','created_at','updated_at','assign_user_id','part_number'
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