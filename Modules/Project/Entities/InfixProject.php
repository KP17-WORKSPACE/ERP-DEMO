<?php

namespace Modules\Project\Entities;

use Illuminate\Database\Eloquent\Model;

class InfixProject extends Model
{
    protected $table = 'infix_project';
    protected $primaryKey = 'id';
    protected $fillable = [];

    public function category()
    {
        return $this->belongsTo('Modules\Project\Entities\InfixProjectCategory', 'category_id', 'id');
    }
    public function staff()
    {
        return $this->belongsTo('App\SmStaff', 'customer_id', 'id');
    }
}
