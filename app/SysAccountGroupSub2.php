<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysAccountGroupSub2 extends Model
{
    protected $table = 'sys_account_group_sub2';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','group_id','sub_id','title','status','created_by','updated_by','created_at','updated_at'
    ];

    public function groupid(){
	    return $this->belongsTo('App\SysAccountGroup', 'group_id', 'id');
	}
    public function subid(){
	    return $this->belongsTo('App\SysAccountGroupSub', 'sub_id', 'id');
	}
}