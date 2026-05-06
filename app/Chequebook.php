<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chequebook extends Model
{
    use SoftDeletes;

    protected $table = 'chequebooks';

    protected $fillable = [
        'doc_number',
        'bank_id',
        'no_of_cheques',
        'start_no',
        'end_no',
        'attachment',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by',
        'company_id'
    ];

    protected $dates = [
        'deleted_at',
    ];

    /**
     * Relationships
     */

    // Bank relation
    public function bank()
    {
        return $this->belongsTo(SysChartofAccounts::class, 'bank_id');
    }

    // Created By User
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Updated By User
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Deleted By User
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Auto Calculate No of Cheques
     */
    

}