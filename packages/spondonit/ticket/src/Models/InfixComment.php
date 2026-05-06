<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfixComment extends Model
{
	protected $fillable = [
	    'ticket_id', 'user_id', 'comment'
	];
}
