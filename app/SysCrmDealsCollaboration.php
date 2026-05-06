<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmDealsCollaboration extends Model
{
    protected $table = 'sys_crm_deals_collaboration';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'deal_id','user_id','status'
    ];
    
    public function userid(){
        return $this->belongsTo('App\SmStaff', 'user_id', 'user_id');
    }
}