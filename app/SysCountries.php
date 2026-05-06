<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SysStates;

class SysCountries extends Model
{
    protected $table = 'sys_countries';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','name','iso3','numeric_code','iso2','phonecode','currency','currency_name','currency_symbol','region','flag'
    ];

      public function states()
    {
        return $this->hasMany(SysStates::class, 'country_id', 'id');
    }

    public function cities()
    {
        return $this->hasMany(SysCities::class, 'country_id', 'id');
    }
}
