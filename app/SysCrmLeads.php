<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SysCrmLeads extends Model
{
    protected $table = 'sys_crm_leads';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'code',
        'date',
        'lead_name',
        'cust_id',
        'cust_name',
        'cust_no',
        'company_name',
        'cust_email',
        'cust_designation',
        'address',
        'country',
        'source',
        'source_o',
        'owner',
        'doc',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'company_id',
        'tags',
        'note',
        'isproject',
        'deal_id',
        'deleted_at',
        'lead_update_count',
        'last_updated',
        'follow_up_date',
        'sub_status_comment',
        'sub_status',
        'followup_count'
    ];

    public function lead_code()
    {
        return $this->belongsTo('App\SysCrmLeads', 'id', 'id');
    }
    public function lead_deal_code()
    {
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }

    public function createdby()
    {
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function ownername()
    {
        return $this->belongsTo('App\SmStaff', 'owner', 'user_id');
    }
    public function customername()
    {
        return $this->belongsTo('App\SysCustSuppl', 'cust_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo('App\SysCompany', 'company_id', 'id');
    }

    public function getAgingDays()
    {
        // Fallback in case lead_update_count is null or zero
        $updateCount = $this->lead_update_count > 0 ? $this->lead_update_count : 1;

        // Calculate difference in days between created_at and updated_at
        $createdAt = Carbon::parse($this->created_at);
        $lastUpdatedAt = Carbon::parse($this->last_updated ?? $this->created_at);

        $daysDiff = $lastUpdatedAt->diffInDays($createdAt);

        // Return average aging days
        return round($daysDiff / $updateCount, 2);
    }


    public static $subStatusColors = [
    1  => 'bg-primary',   // Just received, uncontacted
    2  => 'bg-info',      // Sent to Sales
    3  => 'bg-danger',    // Budget Issue
    4  => 'bg-danger',    // Not Interested
    5  => 'bg-secondary', // Wrong Contact
    6  => 'bg-warning',   // Timeline not matching
    7  => 'bg-secondary', // Product/Service mismatch
    8  => 'bg-success',   // Other
    9  => 'bg-warning',   // Waiting for EUD
    10 => 'bg-info',      // Waiting for Vendor Price
    11 => 'bg-primary',   // Quoted - Waiting for Response
    12 => 'bg-success',   // Other
    13 => 'bg-secondary', // No Response
    14 => 'bg-secondary', // Other
];


public function getSubStatusColorAttribute()
{
    return self::$subStatusColors[$this->sub_status] ?? 'bg-dark';
}


    public static $subStatusMap = [
        1 => [ // New
            1 => 'Just received, uncontacted',
        ],
        2 => [ // Qualified
            2 => 'Sent to Sales',
        ],
        0 => [ // Qualified
            2 => 'Sent to Sales',
        ],
        3 => [ // Unqualified
            3 => 'Budget Issue',
            4 => 'Not Interested',
            5 => 'Wrong Contact',
            6 => 'Timeline not matching',
            7 => 'Product/Service mismatch',
            8 => 'Other',
        ],
        4 => [ // Pending Response
            9 => 'Waiting for EUD',
            10 => 'Waiting for Vendor Price',
            11 => 'Quoted - Waiting for Response',
            12 => 'Other',
        ],
        10 => [ // Closed
            13 => 'No Response',
            14 => 'Other',
        ]
    ];

    // Accessor to fetch sub status text
    public function getSubStatusTextAttribute()
    {
        return self::$subStatusMap[$this->status][$this->sub_status] ?? 'Unknown';
    }


    
  public function getStatusInfoAttribute()
    {
        $statusMap = [
            1  => ['label' => 'New',         'color' => 'primary'],   // Blue
            2  => ['label' => 'Qualified',   'color' => 'success'],   // Green
            0  => ['label' => 'Qualified',   'color' => 'success'],   // Green
            3  => ['label' => 'Unqualified', 'color' => 'danger'],    // Red
            4  => ['label' => 'Pending Response', 'color' => 'warning'], // Yellow
            10 => ['label' => 'Closed',      'color' => 'secondary'], // Gray
            5  => ['label' => 'Converted',   'color' => 'info'],      // Teal
        ];

        return $statusMap[$this->status] ?? ['label' => 'Unknown', 'color' => 'dark'];
    }


}