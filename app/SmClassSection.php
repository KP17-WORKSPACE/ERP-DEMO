<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmClassSection extends Model
{
    public function sectionName(){
    	return $this->belongsTo('App\Smsection', 'section_id', 'id');
    }
}
