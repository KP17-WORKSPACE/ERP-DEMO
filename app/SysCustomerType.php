<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCustomerType extends Model
{
    protected $table = 'sys_customer_type';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'title','status','created_by','updated_by','created_at','updated_at'
    ];

    // public function countryli(){
	// 	return $this->belongsTo('App\SmCountry', 'country', 'id');
	// }
}