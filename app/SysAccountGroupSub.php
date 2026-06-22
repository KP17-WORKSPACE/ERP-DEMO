<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysAccountGroupSub extends Model
{
    protected $table = 'sys_account_group_sub';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','group_id','title','status','created_by','updated_by','created_at','updated_at','sort_id','group_code','company_id'
    ];

    public function groupid(){
	    return $this->belongsTo('App\SysAccountGroup', 'group_id', 'id');
	}
}