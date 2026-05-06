<?php



namespace App;



use Illuminate\Database\Eloquent\Model;
use App\SysCountries;
use App\SysCities;



class SysStates extends Model

{

    protected $table = 'sys_states';

    protected $primaryKey = 'id';



    protected $fillable = [

        'id','name','country_id','country_code','fips_code','iso2','flag'

    ];

     public function country()
    {
        return $this->belongsTo(SysCountries::class, 'country_id', 'id');
    }

    public function cities()
    {
        return $this->hasMany(SysCities::class, 'state_id', 'id');
    }

}

