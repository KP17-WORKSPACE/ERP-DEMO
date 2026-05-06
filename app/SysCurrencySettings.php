<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCurrencySettings extends Model
{
    protected $table = 'sys_currency';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','name ','code','symbol','rate','set_default','status','active_status','created_by','updated_by','created_at','updated_at','r_code','p_code'
    ];

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'id');
    }

    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'id');
    }
}