<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysBrand extends Model
{
    protected $table = 'sys_brand';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','title','active_status','created_by','updated_by','created_at','updated_at','company_id'
    ];

    public function createdby(){
		  return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
		  return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
	}
    public function companyid(){
		  return $this->belongsTo('App\SysCompany', 'company_id', 'id');
	}
}