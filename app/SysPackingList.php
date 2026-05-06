<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPackingList extends Model
{
    protected $table = 'sys_packing_list';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'account_id','date','doc_number','refdate','refno','remarks','currancy','status','created_by','updated_by','created_at','updated_at','company_id'
    ];
    
    public function account(){
        return $this->belongsTo('App\SysChartofAccounts', 'account_id', 'id');
    }
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
}