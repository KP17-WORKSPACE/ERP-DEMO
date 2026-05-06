<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysStockOutSerialNo extends Model
{
    protected $table = 'sys_stock_out_serial_no';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'out_id','serial_no','status'
    ];

    // public function countryli(){
	// 	return $this->belongsTo('App\SmCountry', 'country', 'id');
	// }
}