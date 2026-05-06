<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WeeklyOff extends Model
{
    protected $table = 'weekly_offs';

    protected $fillable = [
        'company_id',
        'slug',
        'name',
    ];

    public $timestamps = true;
}
