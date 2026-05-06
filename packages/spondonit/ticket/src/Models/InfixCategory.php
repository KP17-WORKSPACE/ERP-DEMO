<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfixCategory extends Model
{
    
	public function tickets()
	{
	    return $this->hasMany(InfixTicket::class);
	}


}
