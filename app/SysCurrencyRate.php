<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCurrencyRate extends Model
{
    protected $table = 'sys_currency_rate';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','from_currency ','to_currency','rate','from_date','status','created_by','created_at','updated_by','updated_at'
    ];

    
    public function fromcurrency(){
        return $this->belongsTo('App\SysCurrencySettings', 'from_currency', 'id');
    }
    public function tocurrency(){
        return $this->belongsTo('App\SysCurrencySettings', 'to_currency', 'id');
    }

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'id');
    }

    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'id');
    }
}