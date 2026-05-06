<?php

namespace Modules\Project\Entities;

use Illuminate\Database\Eloquent\Model;

class InfixTeamMember extends Model
{
    protected $table = 'infix_team_member';
    protected $primaryKey = 'id';
    protected $fillable = [];


    public function team()
    {
        return $this->belongsTo('Modules\Project\Entities\InfixTeam', 'infix_team', 'id');
    }
    public function staff()
    {
        return $this->belongsTo('App\SmStaff', 'staff_id', 'id');
    }
}
