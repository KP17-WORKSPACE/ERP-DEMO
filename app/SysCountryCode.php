<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCountryCode extends Model
{
    protected $table = 'sys_country_code';
    protected $primaryKey = 'id';

    protected $fillable = [
        'country_iso','country_iso3 ','calling_code','country_name'
    ];
}
