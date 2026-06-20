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
		'emergency_contacts',
        'leave_application_no',
        'return_to_work_date',
        'leave_category',
        'urgency_level',
        'nature_of_leave',
        'notice_period',
        'availability_during_leave',
        'contact_number_during_leave',
        'email_during_leave',
        'handover_required',
        'handover_employee_id',
        'pending_tasks',
        'client_responsibilities',
        'access_transfer_required',
        'handover_completion_confirmation',
        'manager_verification_of_handover',
        'handover_additional_remarks',
        'leaving_country',
        'destination_country',
        'departure_date',
        'expected_return_date',
        'travel_ticket_file',
        'accommodation_address',
        'emergency_contact_person',
        'emergency_contact_number',
        'emergency_contact_relationship',
        'submitted_at',
        'management_approval_req',
        'declaration_info_confirmed',
        'declaration_handover_confirmed',
        'declaration_policy_agreed',
        'declaration_accepted_at',
        'declaration_accepted_by',
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
		'emergency_contacts' => 'array',
        'return_to_work_date' => 'date',
        'departure_date' => 'date',
        'expected_return_date' => 'date',
        'submitted_at' => 'datetime',
        'declaration_accepted_at' => 'datetime',
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

    public function getApproveStatusLabelAttribute()
    {
        $map = ['D'=>'New','P'=>'Pending','A'=>'Approved','R'=>'Rejected','C'=>'Returned'];
        $v = $this->approve_status ?: 'P';
        return $map[$v] ?? $v;
    }

    public function getApproveStatusBadgeAttribute()
    {
        $map = [
            'D' => 'primary',
            'P' => 'warning',
            'A' => 'success',
            'R' => 'danger',
            'C' => 'info',
        ];
        $v = $this->approve_status ?: 'P';
        return $map[$v] ?? 'secondary';
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

