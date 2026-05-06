<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysShipping extends Model
{
    protected $table = 'sys_shipping';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'shipping_name','contact_name','contact_no','address1','address2','status','created_by','updated_by','created_at','updated_at'
    ];

    // public function accounttype(){
	//     return $this->belongsTo('App\SysAccountType', 'account_type', 'id');
	// }
}
