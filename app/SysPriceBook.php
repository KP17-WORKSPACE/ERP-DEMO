<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPriceBook extends Model
{
    protected $table = 'sys_price_book';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'pid','currency_id','r_price','e_price','status','created_by','updated_by','created_at','updated_at','company_id'
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
    public function currency(){
        return $this->belongsTo('App\SysCurrencySettings', 'currency_id', 'id');
    }
    public function productname(){
        return $this->belongsTo('App\SmItem', 'pid', 'id');
    }
}