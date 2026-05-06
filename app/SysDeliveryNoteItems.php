<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysDeliveryNoteItems extends Model
{
    protected $table = 'sys_delivery_note_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','ref_si_id','dn_id','part_number','serial_no','qty','unitprice','value','discount','taxableamount','vatamount','tax','status','created_by','updated_by','created_at','updated_at','is_deal_aditional','description','sort_id','refid'
    ];

    public function product(){
        return $this->belongsTo('App\SmItem', 'part_number', 'id');
    }
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }    
}