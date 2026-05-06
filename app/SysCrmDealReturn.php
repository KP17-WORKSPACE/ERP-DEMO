<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmDealReturn extends Model
{
    protected $table = 'sys_crm_deal_return';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'deal_id','remarks','status','created_by','updated_by','created_at','updated_at','company_id','collection','return','payable'
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function dealid(){
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }
    public function collectiondet(){
        return $this->belongsTo('App\SysCrmDealReturnCollection', 'deal_id', 'deal_id');
    }
    public function return_det(){
        return $this->belongsTo('App\SysCrmDealReturnSales', 'deal_id', 'deal_id');
    }
    public function payable_det(){
        return $this->belongsTo('App\SysCrmDealReturnPayable', 'deal_id', 'deal_id');
    }
}