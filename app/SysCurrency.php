<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCurrency extends Model
{
    protected $table = 'sys_currency';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','name ','code','symbol','rate','ex_rate','set_default','status','active_status','created_by','updated_by','created_at','updated_at'
    ];
}
