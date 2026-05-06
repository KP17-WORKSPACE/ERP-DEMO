<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmSupportActivity extends Model
{
    protected $table = 'sys_crm_support_activity';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','support_id','activity_date','activity_from','activity_to','remarks','file','status','created_by','updated_by','created_at','updated_at'
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function supportid(){
        return $this->belongsTo('App\SysCrmSupport', 'support_id', 'id');
    }
}