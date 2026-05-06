<?php



namespace App;



use Illuminate\Database\Eloquent\Model;
use App\SysStates;
use App\SysCountries;



class SysCities extends Model

{

     protected $table = 'sys_cities';

    protected $primaryKey = 'id';

    public $timestamps = false; // No created_at / updated_at columns

    protected $fillable = [
        'name',
        'state_id',
        'state_code',
        'country_id',
        'country_code',
        'flag',
    ];

        public function state()
    {
        return $this->belongsTo(SysStates::class, 'state_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(SysCountries::class, 'country_id', 'id');
    }

    /* ================================
     | Query Scopes (FAST & CLEAN)
     |================================ */

    public function scopeActive($query)
    {
        return $query->where('flag', 1);
    }

    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeByState($query, $stateId)
    {
        return $query->where('state_id', $stateId);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where('name', 'LIKE', $keyword . '%');
    }

}

