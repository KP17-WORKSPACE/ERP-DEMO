<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysSTLItems extends Model
{
    protected $table = 'sys_stl_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'stl_id','pi_no','part_id','part_no','description','amount','status','pi_inv_no','bill_no','awbno','boeno'
    ];

    public function pi_det(){
        return $this->belongsTo('App\SysPurchaseInvoice', 'pi_no', 'id');
    }
    
    public function part_det(){
        return $this->belongsTo('App\SmItem', 'part_id', 'id');
    }
}