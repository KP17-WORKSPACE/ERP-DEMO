<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmAmcAsign extends Model
{
    protected $table = 'sys_crm_amc_asign';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'amc_id','user_id','status'
    ];
    
    public function userid(){
        return $this->belongsTo('App\SmStaff', 'user_id', 'user_id');
    }
}