<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmIndustry extends Model
{
    //

    public function activities()
{
    return $this->hasMany('App\SmBusinessActivity', 'industry_id');
}

}
