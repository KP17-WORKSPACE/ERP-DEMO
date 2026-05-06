<?php



namespace App\Http\Controllers;



use App\Role;

use App\SmStaff;

use App\ApiBaseMethod;

use App\SmStaffAttendence;
use App\SysCompany;
use Illuminate\Http\Request;

use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;



class SmStaffAttendanceController extends Controller

{

    public function __construct()

    {

        $this->middleware('PM');

    }

    

    public function staffAttendance(Request $request){ 

        

        try{

            if(ApiBaseMethod::checkUrl($request->fullUrl())){

                return ApiBaseMethod::sendResponse($roles, null);

            }

                $role_id=1;

                $date= date('m/d/Y');

                $roles = Role::where([['id', '!=',1],['id', '!=',2],['id', '!=',7]])->get();

                $staffs = SmStaff::where([['role_id', '!=',1],['role_id', '!=',2],['role_id', '!=',7]])->where('active_status', 1)->get();

    

            $already_assigned_staffs = [];

            $new_staffs = [];

            $attendance_type = "";

            foreach($staffs as $staff){

                $attendance = SmStaffAttendence::where('staff_id', $staff->id)->where('attendence_date', date('Y-m-d', strtotime($request->attendance_date)))->first();

                if($attendance != ""){

                    $already_assigned_staffs[] = $attendance;

                    $attendance_type =  $attendance->attendence_type;

                }else{

                    $new_staffs[] =  $staff;

                }

            }//end loop for staff

            return view('backEnd.humanResource.staff_attendance', compact('role_id', 'date', 'roles', 'already_assigned_staffs', 'new_staffs', 'attendance_type'));

        }catch (\Exception $e) {

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }



    public function staffAttendanceSearch(Request $request){

 

        $input = $request->all();

        $validator = Validator::make($input, [

            'role' => 'required',

            'attendance_date' => 'required'

        ]);



        if($validator->fails()){

            if(ApiBaseMethod::checkUrl($request->fullUrl())){

                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());

            }

            return redirect()->back()

                ->withErrors($validator)

                ->withInput();

        }



        try{

            $date = $request->attendance_date;

            $roles = Role::where('id', '!=', 3)->where('id', '!=', 2)->get();

            $role_id = $request->role;

            $staffs = SmStaff::where('role_id', $request->role)->where('active_status', 1)->get();

            if($staffs->isEmpty()){

                return redirect('staff-attendance')->with('message-danger', 'No result found');

            }

            $already_assigned_staffs = [];

            $new_staffs = [];

            $attendance_type = "";

            foreach($staffs as $staff){

                $attendance = SmStaffAttendence::where('staff_id', $staff->id)->where('attendence_date', date('Y-m-d', strtotime($request->attendance_date)))->first();

                if($attendance != ""){

                    $already_assigned_staffs[] = $attendance;

                    $attendance_type =  $attendance->attendence_type;

                }else{

                    $new_staffs[] =  $staff;

                }

            }

            if(ApiBaseMethod::checkUrl($request->fullUrl())){

                $data=[];

                $data['role_id']= $role_id;

                $data['date']= $date;

                $data['roles']= $roles->toArray();

                $data['already_assigned_staffs']= $already_assigned_staffs;

                $data['new_staffs']= $new_staffs;

                $data['attendance_type']= $attendance_type;

                return ApiBaseMethod::sendResponse($data, null);

            }

            return view('backEnd.humanResource.staff_attendance', compact('role_id', 'date', 'roles', 'already_assigned_staffs', 'new_staffs', 'attendance_type'));

        }catch (\Exception $e) {

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }



    public function staffAttendanceStore(Request $request){

    	

        try{

            foreach($request->id as $staff){

                $attendance = SmStaffAttendence::where('staff_id', $staff)->where('attendence_date', date('Y-m-d', strtotime($request->date)))->first();

                if($attendance != ""){

                    $attendance->delete();

                }

                $attendance = new SmStaffAttendence();

                $attendance->staff_id = $staff;

                if(isset($request->mark_holiday)){

                    $attendance->attendence_type = "H";

                }else{

                   $attendance->attendence_type = $request->attendance[$staff];

                   $attendance->notes = $request->note[$staff]; 

                }

                $attendance->attendence_date = date('Y-m-d', strtotime($request->date));

                $attendance->save();

            }

            if(ApiBaseMethod::checkUrl($request->fullUrl())){

                return ApiBaseMethod::sendResponse(null, 'Staff attendance been submitted successfully');

            }

            Toastr::success('Operation successful', 'Success');

                return redirect('staff-attendance');

        }catch (\Exception $e) {

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }



    public function staffAttendanceReport(Request $request){

        try{

            $roles = Role::where([['id', '!=', 1],['id', '!=', 2],['id', '!=', 7]])->get();

            if(ApiBaseMethod::checkUrl($request->fullUrl())){

                return ApiBaseMethod::sendResponse($roles, null);

            }

            return view('backEnd.humanResource.staff_attendance_report', compact('roles'));

        }catch (\Exception $e) {

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }



    public function staffAttendanceReportSearch(Request $request){



        $input = $request->all();

        $validator = Validator::make($input, [

            'role' => 'required',

            'month' => 'required',

            'year' => 'required'

        ]);

        if($validator->fails()){

            if(ApiBaseMethod::checkUrl($request->fullUrl())){

                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());

            }

            return redirect()->back()

                ->withErrors($validator)

                ->withInput();

        }

        try{

            $year = $request->year;

            $month = $request->month;;

            $role_id = $request->role;;

            $current_day = date('d');

            $days=cal_days_in_month(CAL_GREGORIAN,$request->month,$request->year);

            $roles = Role::where('id', '!=', 3)->where('id', '!=', 2)->get();

            $staffs = SmStaff::where('role_id', $request->role)->get();

            $attendances = [];

            foreach($staffs as $staff){

                $attendance = SmStaffAttendence::where('staff_id', $staff->id)->where('attendence_date', 'like', $request->year.'-'.$request->month.'%')->get();

                if(count($attendance) != 0){

                    $attendances[] = $attendance;

                }

            }

            if(ApiBaseMethod::checkUrl($request->fullUrl())){

                $data=[];

                $data['attendances']= $attendances;

                $data['days']= $days;

                $data['year']= $year;

                $data['month']= $month;

                $data['current_day']= $current_day;

                $data['roles']= $roles;

                $data['role_id']= $role_id;

                return ApiBaseMethod::sendResponse($data, null);

            }

            return view('backEnd.humanResource.staff_attendance_report', compact('attendances', 'days', 'year', 'month', 'current_day', 'roles', 'role_id'));

        }catch (\Exception $e) {

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }


      private function normalizeClock(?string $t): ?string
    {
        if (!$t) return null;
        $t = trim(preg_replace('/\s+/', ' ', $t));

        // Proper 12h "h:mm AM/PM"
        if (preg_match('/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i', $t, $m)) {
            $h = (int)$m[1];
            $min = $m[2];
            $mer = strtoupper($m[3]);
            if ($h === 12) $h = 0;
            if ($mer === 'PM') $h += 12;
            return sprintf('%02d:%02d', $h, $min);
        }

        // Bad mix like "13:57 PM" -> strip AM/PM, keep 24h
        $stripped = preg_replace('/\s*(AM|PM)\s*$/i', '', $t);
        if (preg_match('/^(\d{1,2}):(\d{2})(:\d{2})?$/', $stripped, $m2)) {
            return sprintf('%02d:%02d', (int)$m2[1], $m2[2]);
        }

        // 24h with/without seconds
        if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $t)) {
            return substr($t, 0, 5);
        }

        // Fallback
        $ts = strtotime($t);
        return $ts !== false ? date('H:i', $ts) : null;
    }

    /**
     * Make a Carbon from date string "Y-m-d" + time "HH:MM". Returns null if invalid.
     */
    private function makeDateTime(?string $dateYmd, ?string $timeHm): ?Carbon
    {
        if (!$dateYmd || !$timeHm) return null;
        try {
            return Carbon::createFromFormat('Y-m-d H:i', $dateYmd.' '.$timeHm);
        } catch (\Throwable $e) {
            return null;
        }
    }

   public function index(Request $req, $id = null)
{
    $user  = Auth::user();
    $staff2 = $user->staff;
    if (!$staff2) {
        abort(403, 'No staff profile linked to this user.');
    }

    $q = SmStaff::with(['roles', 'maincompany', 'departments', 'designations', 'jobDetail'])

                ->where('delete_status', 1)
                ->when(Auth::check() && Auth::user()->role_id != 1, function ($x) {
                    $x->where('role_id', '!=', 1);
                });

            if (session('logged_session_data.company_id') != 1) {
                $q->where('company_id', session('logged_session_data.company_id'));
            }

            if ($req->filled('staff_no')) {
                $term = trim($req->input('staff_no'));
                $q->where(function ($x) use ($term) {
                    $x->where('staff_no', 'like', "%{$term}%")
                        ->orWhere('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", ["%{$term}%"])
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('mobile', 'like', "%{$term}%");
                });
            }

            $staffs = $q->orderBy('id', 'desc')->get();
            $roles = Role::where('active_status', 1)->get();
            $company = SysCompany::select('id', 'company_name')->get();
            $active_id = null;

            if ($id) {

                $firstStaff = SmStaff::with(['roles', 'maincompany', 'departments', 'designations', 'jobDetail'])
                    // ->where('company_id', $companyId)
                    ->where('user_id', $id)
                    ->first();
                $active_id = $id;
                $active_fid = $firstStaff->finger_print_id;
            } else if ($staffs->count() > 0) {

                $firstStaff = $staffs->first();
                $active_id = $firstStaff->user_id;
                $active_fid = $firstStaff->finger_print_id;
            } else {
                $firstStaff = null;
                $active_fid = null;
            }

            
    $staff = $active_fid;
    $activeStaff = SmStaff::where('user_id',$active_id)->first();


    SmStaffAttendanceSyncController::connectAndFetch();

    // ---- CONFIG ----
    $config     = \App\SmStaffAttendanceLeaveConfiguration::where('staff_id', $staff)->first();
    $shiftStart = $config ? $config->shift_start_time : '09:00 AM';
    $shiftEnd   = $config ? $config->shift_end_time   : '06:00 PM';
    $grace      = (int)($config->grace_period ?? 10);
    $weeklyOffs = collect($config && $config->weekly_off_days ? $config->weekly_off_days : []);

    // ---- DATE RANGE ----
    $month    = $req->get('month', date('Y-m'));
    $fromDate = $req->get('from_date');
    $toDate   = $req->get('to_date');
    
    try {
        if ($fromDate && $toDate) {
            $start = Carbon::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
            $end   = Carbon::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
        } else {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
            $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();
        }
    } catch (\Exception $e) {
        $start = Carbon::now()->startOfMonth()->toDateString();
        $end   = Carbon::now()->endOfMonth()->toDateString();
        $month = date('Y-m');
    }

    // ---- FETCH ATTENDANCE ----
    $entries = \App\SmStaffAttendence::with('StaffInfo')
        //->where('staff_id', $staff->user_id)
        ->where('finger_print_id', $active_fid)
        ->whereBetween('attendence_date', [$start, $end])
        ->orderBy('attendence_date', 'asc')
        ->get();

    // ---- AUTO-INJECT SUNDAYS OR MISSING DATES ----
    $period = new \DatePeriod(
        Carbon::parse($start),
        new \DateInterval('P1D'),
        Carbon::parse($end)->addDay()
    );

    $entriesByDate = $entries->keyBy(function ($r) {
        return Carbon::parse($r->attendence_date)->toDateString();
    });

    $completeEntries = collect();

    foreach ($period as $date) {
        $dayName = $date->format('l');
        $dateKey = $date->toDateString();

        if ($entriesByDate->has($dateKey)) {
            $completeEntries->push($entriesByDate->get($dateKey));
        } else {
            $isSunday = strtolower($dayName) === 'sunday';
           $completeEntries->push((object)[
    'attendence_date'  => $dateKey,
    'day_name'         => $dayName,
    'in_time'          => null,
    'out_time'         => null,
    'is_offday'        => $isSunday,
    'attendence_type'  => $isSunday ? 'W' : null,
    'status_label'     => $isSunday ? 'Week Off' : '—',
    'StaffInfo'        => $activeStaff,
    'rectified_hours'  => 0,
    'over_time'        => 0,
]);
        }
    }

    // ---- CALCULATIONS ----
    $entries = $completeEntries->map(function ($item) use ($shiftStart, $shiftEnd, $grace, $weeklyOffs) {
        $date = Carbon::parse($item->attendence_date);
        $day  = $date->format('l');
        $item->day_name = $day;

        // WEEKLY OFF
        $isOff = $item->is_offday ?? false;
        $weekNumber = (int)ceil($date->day / 7);
        if ($weeklyOffs->contains('sunday_all') && $day === 'Sunday') $isOff = true;
        if ($weeklyOffs->contains('friday_all') && $day === 'Friday') $isOff = true;
        if ($weeklyOffs->contains('saturday_all') && $day === 'Saturday') $isOff = true;
        if ($weeklyOffs->contains('1_3_saturday') && $day === 'Saturday' && in_array($weekNumber, [1,3], true)) $isOff = true;
        if ($weeklyOffs->contains('2_4_saturday') && $day === 'Saturday' && in_array($weekNumber, [2,4], true)) $isOff = true;
        $item->is_offday = $isOff;

        // NORMALIZE TIMES
        $shiftStartNorm = $this->normalizeTime($shiftStart);
        $shiftEndNorm   = $this->normalizeTime($shiftEnd);
        $inTimeNorm     = $this->normalizeTime($item->in_time);
        $outTimeNorm    = $this->normalizeTime($item->out_time);

        $shiftStartTime = $shiftStartNorm ? Carbon::createFromFormat('H:i', $shiftStartNorm) : null;
        $shiftEndTime   = $shiftEndNorm   ? Carbon::createFromFormat('H:i', $shiftEndNorm)   : null;
        $checkIn        = $inTimeNorm     ? Carbon::createFromFormat('H:i', $inTimeNorm)     : null;
        $checkOut       = $outTimeNorm    ? Carbon::createFromFormat('H:i', $outTimeNorm)    : null;

        // LATE
        $item->is_late = false;
        $item->late_time = '—';
        if (!$isOff && $checkIn && $shiftStartTime) {
            $limit = $shiftStartTime->copy()->addMinutes($grace);
            if ($checkIn->gt($limit)) {
                $item->is_late = true;
                $mins = $checkIn->diffInMinutes($shiftStartTime);
                $item->late_time = sprintf('%02dh %02dm', intdiv($mins, 60), $mins % 60);
            }
        }


        // EARLY OUT
        $item->is_early_out = false;
        $item->early_out_time = '—';
        if (!$isOff && $checkOut && $shiftEndTime) {
            if ($checkOut->lt($shiftEndTime)) {
                $item->is_early_out = true;
                $mins = $shiftEndTime->diffInMinutes($checkOut);
                $item->early_out_time = sprintf('%02dh %02dm', intdiv($mins, 60), $mins % 60);
            }
        }

        // WORKING TIME
        $item->working_time = '—';
        if ($checkIn && $checkOut) {
            $mins = $checkOut->diffInMinutes($checkIn);
            $item->working_time = sprintf('%02dh %02dm', intdiv($mins, 60), $mins % 60);
        }

        // OVER TIME
        $item->over_time = '—';
        if (!$isOff && $checkOut && $shiftEndTime && $checkOut->gt($shiftEndTime)) {
            $mins = $checkOut->diffInMinutes($shiftEndTime);
            $item->over_time = sprintf('%02dh %02dm', intdiv($mins, 60), $mins % 60);
        }

        // RECTIFIED HOURS
        $item->rectified_hours = is_numeric($item->rectified_hours) ? (float)$item->rectified_hours : 0;

        // STATUS LABEL
        if ($isOff) {
            $item->status_label = 'Week Off';
        } elseif ($item->attendence_type === 'A') {
            $item->status_label = 'Absent';
        } elseif ($item->attendence_type === 'L') {
            $item->status_label = 'On Leave';
        } elseif ($item->attendence_type === 'P') {
            $item->status_label = 'Present';
        } else {
            $item->status_label = ucfirst($item->attendence_type ?: '—');
        }

        return $item;
    });

    // === SUMMARY CALC ===
    $totalDays    = $entries->count();
    $totalWeekOff = $entries->where('is_offday', 1)->count();
    $totalPresent = $entries->where('attendence_type', 'P')->count();
    $totalAbsent  = $entries->where('attendence_type', 'A')->count();
    $totalLeave   = $entries->where('attendence_type', 'L')->count();
    $totalLate    = $entries->where('is_late', 1)->count();

    $expectedWorkingHours = ($totalDays - $totalWeekOff) * 8;
    $actualWorkingHours = 0;

    foreach ($entries as $row) {
        if (!empty($row->in_time) && !empty($row->out_time)) {
            try {
                $in  = Carbon::parse($this->normalizeTime($row->in_time));
                $out = Carbon::parse($this->normalizeTime($row->out_time));
                $mins = $out->diffInMinutes($in, false);
                if ($mins > 0) $actualWorkingHours += $mins / 60;
            } catch (\Exception $e) {}
        }
    }

    $rectifiedHours = floatval($entries->sum('rectified_hours') ?: 0);
    $overTime       = 0;
    $deficiency     = max(0, $expectedWorkingHours - $actualWorkingHours);

    $summary = [
        'total_working_days' => $totalDays,
        'total_week_off'     => $totalWeekOff,
        'total_present'      => $totalPresent,
        'total_absent'       => $totalAbsent,
        'total_leave'        => $totalLeave,
        'total_late'         => $totalLate,
        'expected_hours'     => number_format($expectedWorkingHours, 2),
        'actual_hours'       => number_format($actualWorkingHours, 2),
        'rectified_hours'    => number_format($rectifiedHours, 2),
        'overtime_hours'     => number_format($overTime, 2),
        'deficiency_hours'   => number_format($deficiency, 2),
    ];

    $today = \App\SmStaffAttendence::with('StaffInfo')
        ->where('staff_id', $staff)
        ->whereDate('attendence_date', date('Y-m-d'))
        ->first();

        //return $staff;
        //$activeEmp = SmStaff::select('id','user_id','role_id','finger_print_id','staff_no','full_name','email','mobile','staff_photo')->where('active_status',1)->get();
    $week_off_data = DB::table('sm_staff_job_details')->where('staff_id',$firstStaff->id)->value('week_off');
    $week_off_array = explode(',', $week_off_data);
    $weekly_offs = DB::table('weekly_offs')->wherein('id',$week_off_array)->pluck('name');
    
    return view('backEnd.humanResource.attendance.attendenceList', [
        'employeeView' => true,
        'activeStaff'  => $activeStaff,
        'entries'      => $entries,
        'today'        => $today,
        'month'        => $month,
        'fromDate'     => $fromDate,
        'toDate'       => $toDate,
        'summary'      => $summary,
        'staffs'    => $staffs,
        'roles' => $roles,
        'company' => $company,
        'active_id' => $active_id,
        'weekly_offs' => $weekly_offs,
    ]);
}

public function todays_attendance(Request $request)
{
    try {
        $totalemp = SmStaff::where('active_status',1)->count();
        $totalemp_present = DB::table('sm_staff_attendences')->where('attendence_date',date('Y-m-d'))->groupBy('staff_id')->count();
        
        $attendance_list = DB::table('sm_staff_attendences as att')->select('st.full_name','st.staff_photo','att.attendence_date','att.in_time','att.out_time','att.type_id','ws.start_time')
        ->join('sm_staffs as st','st.finger_print_id','att.finger_print_id')
        ->leftjoin('sm_staff_job_details as jd','jd.staff_id','st.id')
        ->leftjoin('working_shifts as ws','ws.id','jd.shift_id')
        ->where('att.attendence_date',date('Y-m-d'))->wherenull('att.type_id')->get();

        $attendance_list_re = DB::table('sm_staff_attendences as att')->select('st.full_name','st.staff_photo','att.attendence_date','att.in_time','att.out_time','att.type_id','ws.start_time')
        ->join('sm_staffs as st','st.finger_print_id','att.finger_print_id')
        ->leftjoin('sm_staff_job_details as jd','jd.staff_id','st.id')
        ->leftjoin('working_shifts as ws','ws.id','jd.shift_id')
        ->where('att.attendence_date',date('Y-m-d'))->where('att.type_id',2)->get();

        return view('backEnd.humanResource.attendance.attendenceToday', ['totalemp' => $totalemp, 'totalemp_present' => $totalemp_present, 'attendance_list' => $attendance_list, 'attendance_list_re' => $attendance_list_re]);
    } catch (\Throwable $th) {
        return $th;
    }
}

/**
 * Normalize time to H:i (24h)
 */
protected function normalizeTime($time)
{
    if (empty($time)) return null;
    try {
        return Carbon::parse($time)->format('H:i');
    } catch (\Exception $e) {
        return null;
    }
}



    public function details(Request $req, $staff_id)
    {
        $ym = $req->get('month', now()->format('Y-m'));
        $staff = SmStaff::findOrFail($staff_id);
        $records = $this->recordsFor($staff_id, $ym);
        $today = $this->todayFor($staff_id);
        return view('backEnd.humanResource.attendance.partials.details', compact('staff','records','today'))->with('month', $ym);
    }

    protected function recordsFor($staffId, $ym)
    {
        if (!$staffId) return collect();
        $start = Carbon::createFromFormat('Y-m',$ym)->startOfMonth()->toDateString();
        $end   = Carbon::createFromFormat('Y-m',$ym)->endOfMonth()->toDateString();

        return SmStaffAttendence::where('staff_id', $staffId)
            ->whereBetween('attendence_date', [$start,$end])
            ->orderBy('attendence_date','asc')
            ->get();
    }

    protected function todayFor($staffId)
    {
        if (!$staffId) return null;
        return SmStaffAttendence::where('staff_id',$staffId)
            ->whereDate('attendence_date', Carbon::today()->toDateString())
            ->first();
    }

     public function punchIn(Request $request)
    {
        $staffId = Auth::id(); // Current logged-in user
        $today = Carbon::today()->toDateString();

        // Check if already punched in today
        $existing = SmStaffAttendence::where('staff_id', $staffId)
            ->whereDate('attendence_date', $today)
            ->first();

        if ($existing && $existing->in_time) {
            return back()->with('error', 'You already punched in today!');
        }

        // Create or update today's attendance
        SmStaffAttendence::updateOrCreate(
            ['staff_id' => $staffId, 'attendence_date' => $today],
            [
                'attendence_type' => 'P', // Present
                'in_time' => Carbon::now()->format('H:i:s'),
                'created_by' => $staffId,
            ]
        );

        return back()->with('success', 'Punch In recorded successfully!');
    }

    /**
     * 🔴 PUNCH OUT
     */
    public function punchOut(Request $request)
    {
        $staffId = Auth::id();
        $today = Carbon::today()->toDateString();

        $attendance = SmStaffAttendence::where('staff_id', $staffId)
            ->whereDate('attendence_date', $today)
            ->first();

        // If user never punched in
        if (!$attendance) {
            return back()->with('error', 'Please punch in first!');
        }

        // If already punched out
        if (!empty($attendance->out_time)) {
            return back()->with('error', 'You have already punched out today!');
        }

        // Update today's attendance with punch out time
        $attendance->update([
            'out_time' => Carbon::now()->format('H:i:s'),
            'updated_by' => $staffId,
        ]);

        return back()->with('success', 'Punch Out recorded successfully!');
    }


    // attendence rectify

            public function rectify(Request $request)
            {
            $request->validate([
            'staff_id' => 'required|integer',
            'attendence_date' => 'required|date',
            ]);

            $attendance = \App\SmStaffAttendence::updateOrCreate(
            [
                'id' => $request->attendance_id,
                'attendence_date' => $request->attendence_date,
                'staff_id' => $request->staff_id,
            ],
            [
                'attendence_type' => 'P',
                'in_time' => date('H:i:s', strtotime($request->in_time)),
                'out_time' => date('H:i:s', strtotime($request->out_time)),
                'notes' => $request->notes,
                'approval_status' => 'Pending',
                'updated_by' => auth()->id(),
            ]
            );

            return back()->with('success', 'Rectification sent for approval.');
            }    



    public function rectifyList(Request $request)
{
    $user = Auth::user();
    $roleId = $user->role_id;

    // 🧩 Step 1: Get staff IDs whose reporting_manager = current user's role_id
    $teamIds = \App\SmStaff::where('reporting_manager', (string)$roleId)
        ->pluck('id')
        ->toArray();

    // 🧩 Step 2: Build rectification query
    $query = \App\SmStaffAttendence::with('staff')
        ->orderBy('attendence_date', 'desc')
        ->whereIn('staff_id', $teamIds); // ✅ correct usage

    // 🧩 Step 3: Optional status filter
    if ($request->filled('status')) {
        $query->where('approval_status', $request->status);
    }

    $rectifications = $query->paginate(20);

    // 🔸 Remove the first `return $rectifications;` — it stops execution
    return view('backEnd.humanResource.attendance.rectify-list', compact('rectifications'));
}

public function rectifyDetail($id)
{
    $r = \App\SmStaffAttendence::with('staff')->findOrFail($id);
    return view('backEnd.humanResource.attendance.partials.rectify-detail', compact('r'));
}

public function approve($id)
{
    try {
        $user = Auth::user();
        $attendance = \App\SmStaffAttendence::findOrFail($id);

        // Step 1: Manager Approval
        if (empty($attendance->manager_approved_by)) {
            $attendance->manager_approved_by = $user->id;
            $attendance->approval_status = 'Manager Approved';
        }
        // Step 2: HR Approval
        elseif (empty($attendance->hr_approved_by)) {
            $attendance->hr_approved_by = $user->id;
            $attendance->approval_status = 'HR Approved';
            $attendance->attendence_type = 'P';
        }

        $attendance->updated_by = $user->id;
        $attendance->save();

        return redirect()->back()->with('success', 'Rectification approved successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error: '.$e->getMessage());
    }
}





    

}

