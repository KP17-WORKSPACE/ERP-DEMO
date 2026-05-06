<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class SmHumanDepartment extends Model

{

    //

    public function designations()
{
    return $this->hasMany(SmDesignation::class, 'department_id', 'id');
}


}

