<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmBusinessActivity extends Model
{
    //

    protected $table = 'sm_business_activities';

    protected $fillable = ['name'];

    public function industry()
{
    return $this->belongsTo('App\SmIndustry', 'industry_id');
}


}
