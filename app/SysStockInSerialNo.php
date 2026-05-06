<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysStockInSerialNo extends Model
{
    protected $table = 'sys_stock_in_serial_no';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'in_id','serial_no','status'
    ];

    // public function countryli(){
	// 	return $this->belongsTo('App\SmCountry', 'country', 'id');
	// }
}