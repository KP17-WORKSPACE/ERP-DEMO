<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysDriver extends Model
{
    protected $table = 'sys_driver';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'driver_name','status','shipping_id'
    ];

    // public function accounttype(){
	//     return $this->belongsTo('App\SysAccountType', 'account_type', 'id');
	// }
}
