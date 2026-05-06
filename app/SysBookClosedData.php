<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysBookClosedData extends Model
{
    protected $table = 'sys_book_closed_data';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','book_id ','book_closed_date','reason','attachment','company_id','status','created_by','created_at','updated_by','updated_at'
    ];

    public function book(){
        return $this->belongsTo('App\SysBookClosed', 'book_id', 'id');
    }

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'id');
    }

    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'id');
    }
}