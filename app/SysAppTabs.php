<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysAppTabs extends Model
{
    protected $table = 'sys_app_tabs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','tab_session','tab_name','page_url','status','created_by','updated_by','created_at','updated_at'
    ];


    public static function get_app_tabs(){
    		$item = SysAppTabs::where('tab_session','=', session_id())->get()->unique('page_url');
    		return $item;
    }


    // public function createdby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'created_by');
    // }
    // public function updatedby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'updated_by');
    // }
    
}