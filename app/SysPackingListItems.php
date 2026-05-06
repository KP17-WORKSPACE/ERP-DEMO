<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPackingListItems extends Model
{
    protected $table = 'sys_packing_list_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','packing_list_id','boxno','part_number','qty','coo','hscode','weight','dimension','status','created_by','updated_by','created_at','updated_at'
    ];
    
    public function product(){
        return $this->belongsTo('App\SmItem', 'part_number', 'id');
  }
}