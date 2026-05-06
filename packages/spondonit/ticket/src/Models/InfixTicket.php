<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfixTicket extends Model
{
    protected $fillable = [
	    'user_id', 'category_id', 'ticket_id', 'title', 'priority', 'message', 'status'
	];

	public function category()
	{
	    return $this->belongsTo(InfixCategory::class);
	}


	public function comments()
	{
	    return $this->hasMany(InfixComment::class);
	}
	public function user()
	{
	    return $this->belongsTo(User::class);
	}



}
