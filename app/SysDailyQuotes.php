<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysDailyQuotes extends Model
{
    protected $table = 'sys_daily_quotes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','date','quote','status','created_by','updated_by','created_at','updated_at'
    ];

    public function createdby(){
		return $this->belongsTo('App\SmStaff', 'created_by', 'id');
    }
    public function updatedby(){
		return $this->belongsTo('App\SmStaff', 'updated_by', 'id');
	}
}
