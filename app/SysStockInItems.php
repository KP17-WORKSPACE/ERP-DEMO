<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysStockInItems extends Model
{
    protected $table = 'sys_stock_in_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','stock_in_id','part_number','part_number_txt','description','qty','unitprice','value','status','created_by','updated_by','created_at','updated_at','refid','serialno','narration'
    ];   
    public function productdet(){
        return $this->belongsTo('App\SmItem', 'part_number', 'id');
    } 
}