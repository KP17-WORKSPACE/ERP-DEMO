<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysDeliveryAdviceItems extends Model
{
    protected $table = 'sys_delivery_advice_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'da_id','part_number','qty','unitprice','da_value','remarks','status','created_by','updated_by','created_at','updated_at'
    ];

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }    
}