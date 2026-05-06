<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysProductType extends Model
{
    protected $table = 'sys_product_type';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','title ','active_status','created_by','updated_by','created_at','updated_at'
    ];

    public function createdby(){
		return $this->belongsTo('App\SmStsffs', 'id', 'created_by');
    }
    public function updatedby(){
		return $this->belongsTo('App\SmStsffs', 'id', 'updated_by');
	}
}
