<?php



namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class SmLeaveRequest extends Model
{

	protected $fillable = [
        'leave_define_id',
        'staff_id',
        'role_id',
        'apply_date',
        'leave_year',
        'type_id',
        'leave_from',
        'leave_to',
        'days',
        'is_half_day',
        'half_session',
        'reason',
        'note',
        'file',
        'approve_status',
        'approver_chain',
        'current_index',
        'approvals_json',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'active_status',
		'company_id',
        'created_by',
        'updated_by',
		'reporting_manager_id',
		'handover_to',
		'emergency_contacts'
    ];

	protected $casts = [
        'apply_date'   => 'date',
        'approved_at'  => 'datetime',
        'rejected_at'  => 'datetime',
        'is_half_day'  => 'boolean',
        'days'         => 'decimal:2',
        'current_index'=> 'integer',
        'leave_year'   => 'integer',
		'leave_from' => 'date',
        'leave_to'   => 'date',	
		'emergency_contacts' => 'array'
    ];

	


    public function leaveType()

	{

	  return $this->belongsTo('App\SmLeaveType', 'type_id');

	}

	public function leaveDefine()
	{
	  return $this->belongsTo('App\SmLeaveDefine', 'leave_define_id', 'id');
	}



	public function staffs()
	{
	  return $this->belongsTo('App\SmStaff', 'staff_id', 'user_id');
	}



	public static function approvedLeave($type_id){
		$user = Auth::user();
		$leaves = SmLeaveRequest::where('role_id', $user->role_id)->where('staff_id', $user->id)->where('leave_define_id', $type_id)->where('approve_status', "A")->get();
		$approved_days = 0;

		foreach($leaves as $leave){

			$start = strtotime($leave->leave_from);
            $end = strtotime($leave->leave_to);
            $days_between = ceil(abs($end - $start) / 86400);
            $days = $days_between + 1;
            $approved_days += $days;
		}
		return $approved_days;

	}



	public static function approvedLeaveModal($type_id, $role_id, $staff_id){

		$leaves = SmLeaveRequest::where('role_id', $role_id)->where('staff_id', $staff_id)->where('leave_define_id', $type_id)->where('approve_status', "A")->get();

		$approved_days = 0;

		foreach($leaves as $leave){

			$start = strtotime($leave->leave_from);

            $end = strtotime($leave->leave_to);



            $days_between = ceil(abs($end - $start) / 86400);

            $days = $days_between + 1;

            $approved_days += $days;

		}

		return $approved_days;

	}

	

    public function chain()
    {
        return $this->hasOne(HrmsApproverChain::class, 'leave_request_id');
    }

    // Translate DB codes to human labels
    public function getApproveStatusLabelAttribute()
    {
        $map = ['P'=>'Pending','A'=>'Approved','R'=>'Rejected'];
        $v = $this->approve_status ?: 'P';
        return $map[$v] ?? $v;
    }

	public function type()
    {
        return $this->belongsTo('App\SmLeaveType', 'type_id');
    }

	public function company()
	{
	return $this->belongsTo(SysCompany::class, 'company_id', 'id');
	}

	public function reportingManager()
	{
	return $this->belongsTo(User::class, 'reporting_manager_id', 'id')
		->withDefault(); // avoids null errors
	}



}

