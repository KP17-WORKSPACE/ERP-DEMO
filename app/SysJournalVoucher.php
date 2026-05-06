<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysJournalVoucher extends Model
{
    protected $table = 'sys_journalvoucher';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'doc_number','doc_date','currency','narration','status','created_by','updated_by','created_at','updated_at','deal_id'
    ];

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }

     public function currency_name(){
		  return $this->belongsTo('App\SysCurrencySettings', 'currency', 'id');
    }
    
}
