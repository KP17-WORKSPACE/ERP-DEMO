<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysQuotationsItems extends Model
{
    protected $table = 'sys_quotations_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','qt_id','part_number','tax','qty','unitprice','value','discount','customcharges','taxableamount','vatamount','status'
    ];

    // public function createdby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'created_by');
    // }
    // public function updatedby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'updated_by');
    // }    
}