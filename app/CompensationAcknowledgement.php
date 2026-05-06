<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompensationAcknowledgement extends Model
{
    protected $table = 'compensation_acknowledgements';
    protected $fillable = [
        'compensation_id',
        'employee_acknowledged',
        'acknowledgement_date',
        'letter_download_count',
        'approval_history_viewed_count',
    ];

    protected $dates = ['acknowledgement_date', 'created_at', 'updated_at'];
    protected $casts = [
        'employee_acknowledged' => 'boolean',
        'letter_download_count' => 'integer',
        'approval_history_viewed_count' => 'integer',
    ];

    // Relationships
    public function compensation()
    {
        return $this->belongsTo('App\CompensationRole', 'compensation_id');
    }
}
