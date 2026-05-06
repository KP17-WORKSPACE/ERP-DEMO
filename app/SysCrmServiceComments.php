<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmServiceComments extends Model
{
    protected $table = 'sys_crm_service_comments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'service_id','comments','commentsdoc','status','created_by','updated_by','created_at','updated_at'
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
}