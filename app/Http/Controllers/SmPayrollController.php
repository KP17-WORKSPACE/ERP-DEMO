<?php



namespace App\Http\Controllers;



use App\Role;

use App\SmStaff;

use App\SmAddIncome;

use App\SmCashIssue;

use App\SmAddExpense;

use App\SmBankAccount;

use App\SmFundTransfer;

use App\SmLeaveRequest;

// use Barryvdh\DomPDF\PDF;

use App\SmChartOfAccount;

use App\SmPaymentMethhod;

use App\SmGeneralSettings;

use App\SmStaffAttendence;

use App\SmHrPayrollGenerate;

use Illuminate\Http\Request;

use App\SmHrPayrollEarnDeduc;

use Illuminate\Support\Facades\DB;

use Brian2694\Toastr\Facades\Toastr;

use Illuminate\Support\Facades\Mail;

use Barryvdh\DomPDF\Facade as PDF;

use Carbon\Carbon;
use Carbon\CarbonPeriod;



class SmPayrollController extends Controller

{

	public function __construct()

	{

		$this->middleware('PM');

	}


	public function index(Request $request)
{
    try {

        /* ---------------------------------------------
         * 1) FILTER INPUTS (YEAR + MONTH)
         * --------------------------------------------- */
        $year  = $request->year  ?? date('Y');
        $month = $request->month ?? date('F');


        /* ---------------------------------------------
         * 2) LOAD PAYROLL CYCLE
         * --------------------------------------------- */
        $cycle = DB::table('sm_hr_payroll_cycles')
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if (!$cycle) {
            Toastr::error("Payroll cycle not created for {$month} {$year}", 'Error');
            return back();
        }


        /* ---------------------------------------------
         * 3) CUSTOM DATE RANGE SUPPORT
         *    If user selects custom from/to → use it
         *    Otherwise use cycle values
         * --------------------------------------------- */
        $from = $request->from_date
            ? Carbon::parse($request->from_date)
            : Carbon::parse($cycle->from_date);

        $to = $request->to_date
            ? Carbon::parse($request->to_date)
            : Carbon::parse($cycle->to_date);

        // Auto-correct if reversed
        if ($from->gt($to)) {
            $temp = $from;
            $from = $to;
            $to = $temp;
        }


        /* ---------------------------------------------
         * 4) TOTAL DAYS (Custom Range)
         * --------------------------------------------- */
        $totalDays = $from->diffInDays($to) + 1;

        /* ---------------------------------------------
         * 5) WORKING DAYS (Mon–Fri)
         * --------------------------------------------- */
        $workingDays = 0;
        $period = CarbonPeriod::create($from, $to);

        foreach ($period as $p) {
            if (!in_array($p->dayOfWeekIso, [6,7])) { // Sat=6, Sun=7
                $workingDays++;
            }
        }


        /* ---------------------------------------------
         * 6) DAY LIST for attendance fetching
         * --------------------------------------------- */
        $dates = [];
        foreach ($period as $p) {
            $dates[] = $p->toDateString();
        }


        /* ---------------------------------------------
         * 7) FETCH STAFFS WITH RELATIONS
         * --------------------------------------------- */
        $staffs = SmStaff::with([
            'departments',
            'company',
            'jobDetail',
            'attendances'
        ])
        ->where('active_status', 1)
        ->orderBy('full_name')
        ->get();


        /* ---------------------------------------------
         * 8) ATTENDANCE + SALARY FOR EACH STAFF
         * --------------------------------------------- */
        foreach ($staffs as $s) {

            // Fetch attendance of date range
            $attMap = SmStaffAttendence::where('staff_id', $s->id)
                ->whereBetween('attendence_date', [$from, $to])
                ->pluck('attendence_type', 'attendence_date');

            // Fill attendance array
            $days = [];
            foreach ($dates as $d) {
                $days[] = $attMap[$d] ?? '-';
            }

            $s->attendance_days = $days;


            /* ----- Attendance Summary ----- */
            $counts = array_count_values($days);
            $P  = $counts['P']  ?? 0;
            $A  = $counts['A']  ?? 0;
            $HD = $counts['HD'] ?? 0;
            $S  = $counts['S']  ?? 0;
            $AL = $counts['AL'] ?? 0;
            $O  = $counts['O']  ?? 0;
            $WO = $counts['WO'] ?? 0;

            $s->present_days  = $P;
            $s->absent_days   = $A;
            $s->half_days     = $HD;
            $s->sick_days     = $S;
            $s->annual_days   = $AL;
            $s->other_days    = $O;
            $s->weekly_off    = $WO;


            /* ----- Salary Calculation ----- */
            $basic      = (float)($s->jobDetail->salary_basic ?? 0);
            $allowances = (float)($s->jobDetail->salary_allowances ?? 0);
            $gross      = (float)($s->jobDetail->salary_gross ?? 0);

            $perDay = $totalDays > 0 ? ($gross / $totalDays) : 0;
            $absentDeduction = $A * $perDay;

            $netPay = $gross - $absentDeduction;

            $s->basic_salary        = $basic;
            $s->allowances          = $allowances;
            $s->gross_salary        = $gross;
            $s->per_day_salary      = round($perDay, 2);
            $s->absent_deduction    = round($absentDeduction, 2);
            $s->net_salary_payable  = round($netPay, 2);

            $s->loan_amount = 0;
        }


        /* ---------------------------------------------
         * 9) FINAL CYCLE OBJECT (for Blade)
         * --------------------------------------------- */
        $cycleObj = (object)[
            'year' => $year,
            'month' => $month,
            'from_date' => $from->toDateString(),
            'to_date' => $to->toDateString(),
            'total_days' => $totalDays,
            'working_days' => $workingDays,
            'weekly_off_days' => ($totalDays - $workingDays),
            'public_holidays' => 0,
            'exchange_rate' => $cycle->exchange_rate ?? 1.00,
        ];

        return view('backEnd.humanResource.payroll.index', [
            'staffs' => $staffs,
            'cycle' => $cycleObj,
            'daysInMonth' => $totalDays
        ]);

    } catch (\Exception $e) {
        \Log::error("Payroll Index Error: " . $e->getMessage());
        Toastr::error("Operation Failed");
        return back();
    }
}



	public function searchStaffPayr(Request $request)

	{

		$request->validate([

			'role_id' => "required",

			'payroll_month' => "required",

			'payroll_year' => "required"



		]);

		try{

			$role_id = $request->role_id;

			$payroll_month = $request->payroll_month;

			$payroll_year = $request->payroll_year;

			$staffs = SmStaff::where('active_status', '=', '1')->where('role_id', '=', $request->role_id)->get();

			$roles = Role::where('active_status', '=', '1')->where('id', '!=', 1)->where('id', '!=', 2)->where('id', '!=', 7)->get();

			return view('backEnd.humanResource.payroll.index', compact('staffs', 'roles', 'payroll_month', 'payroll_year', 'role_id'));

		}catch (\Exception $e) {

		   Toastr::error('Operation Failed', 'Failed');

		   return redirect()->back(); 

		}

	}

	public function generatePayroll(Request $request, $id, $payroll_month, $payroll_year)

	{

		try{

			$staffDetails = SmStaff::find($id);

			$month = date('m', strtotime($payroll_month));

			$attendances = SmStaffAttendence::where('staff_id', $id)->where('attendence_date', 'like', $payroll_year . '-' . $month . '%')->get();

			$p = 0;

			$l = 0;

			$a = 0;

			$f = 0;

			$h = 0;

			foreach ($attendances as $value) {

				if ($value->attendence_type == 'P') {

					$p++;

				} elseif ($value->attendence_type == 'L') {

					$l++;

				} elseif ($value->attendence_type == 'A') {

					$a++;

				} elseif ($value->attendence_type == 'F') {

					$f++;

				} elseif ($value->attendence_type == 'H') {

					$h++;

				}

			}

			$approve_leaves = SmLeaveRequest::where('approve_status', 'A')->where('staff_id', $id)->get();

			// $total_loan = SmAdvanceloan::where('staff_id', $id)->sum('amount');

			// $deduct_lists = SmHrPayrollGenerate::where('staff_id', $id)->get();

			// $total_deduct = 0;

			// foreach($deduct_lists as $deduct_list){

			// 	$deduct_amount = SmHrPayrollEarnDeduc::where('payroll_generate_id', $deduct_list->id)->where('earn_dedc_type', 'D')->first();

			// 	if($deduct_amount != ""){

			// 		$total_deduct = $total_deduct + $deduct_amount->amount;

			// 	}

			// }

	

			// $rest_loan = $total_loan - $total_deduct;

			$unsettled_amount = SmCashIssue::where('is_return', '0')->where('staff_id', $id)->sum('amount');

			return view('backEnd.humanResource.payroll.generatePayroll', compact('staffDetails', 'payroll_month', 'payroll_year', 'p', 'l', 'a', 'f', 'h', 'unsettled_amount'));

		}catch (\Exception $e) {

		   Toastr::error('Operation Failed', 'Failed');

		   return redirect()->back(); 

		}

	}

	public function savePayrollData(Request $request)

	{

		$request->validate([

			'net_salary' => "required"

		]);

		try{

			$payrollGenerate = new SmHrPayrollGenerate();

			$payrollGenerate->staff_id = $request->staff_id;

			$payrollGenerate->payroll_month = $request->payroll_month;

			$payrollGenerate->payroll_year = $request->payroll_year;

			$payrollGenerate->basic_salary = $request->basic_salary;

			$payrollGenerate->total_earning = $request->total_earning;

			$payrollGenerate->total_deduction = $request->total_deduction;

			$payrollGenerate->gross_salary = $request->final_gross_salary;

			$payrollGenerate->tax = $request->tax;

			$payrollGenerate->net_salary = $request->net_salary;

			$payrollGenerate->payroll_status = 'G';

			$payrollGenerate->created_by = Auth()->user()->id;

			$result = $payrollGenerate->save();

			$payrollGenerate->toArray();

			if ($result) {

				$earnings = count($request->earningsType);

				for ($i = 0; $i < $earnings; $i++) {

					if (!empty($request->earningsType[$i]) && !empty($request->earningsValue[$i])) {

						$payroll_earn_deducs = new SmHrPayrollEarnDeduc;

						$payroll_earn_deducs->payroll_generate_id = $payrollGenerate->id;

						$payroll_earn_deducs->type_name = $request->earningsType[$i];

						$payroll_earn_deducs->amount = $request->earningsValue[$i];

						$payroll_earn_deducs->earn_dedc_type = 'E';

						$payroll_earn_deducs->created_by = Auth()->user()->id;

						$result = $payroll_earn_deducs->save();

					}

				}

	

				$deductions = count($request->deductionstype);

				for ($i = 0; $i < $deductions; $i++) {

					if (!empty($request->deductionstype[$i]) && !empty($request->deductionsValue[$i])) {

						$payroll_earn_deducs = new SmHrPayrollEarnDeduc;

						$payroll_earn_deducs->payroll_generate_id = $payrollGenerate->id;

						$payroll_earn_deducs->type_name = $request->deductionstype[$i];

						$payroll_earn_deducs->amount = $request->deductionsValue[$i];

						$payroll_earn_deducs->earn_dedc_type = 'D';

						$payroll_earn_deducs->created_by = Auth()->user()->id;

						$result = $payroll_earn_deducs->save();

					}

				}

				Toastr::error('Operation Failed', 'Failed');

				return redirect('payroll');

			} else {

				Toastr::error('Operation Failed', 'Failed');

		   		return redirect()->back();

			}

		}catch (\Exception $e) {

		   Toastr::error('Operation Failed', 'Failed');

		   return redirect()->back(); 

		}

	}



	public function paymentPayroll(Request $request, $id, $role_id)

	{

		try{

			$expense_heads = SmChartOfAccount::where('type', "E")->where('active_status', 1)->get();

			$bank_accounts = SmBankAccount::all();

			$payrollDetails = SmHrPayrollGenerate::find($id);

			$paymentMethods = SmPaymentMethhod::where('id', '!=', '2')->where('id', '!=', '4')->where('id', '!=', '5')->where('id', '!=', '6')->get();

			return view('backEnd.humanResource.payroll.paymentPayroll', compact('payrollDetails', 'paymentMethods', 'role_id', 'bank_accounts', 'expense_heads'));

		}catch (\Exception $e) {

		   Toastr::error('Operation Failed', 'Failed');

		   return redirect()->back(); 

		}

	}



	public function savePayrollPaymentData(Request $request)

	{

		try{

			$payroll_month = $request->payroll_month;

			$payroll_year = $request->payroll_year;

			$payments = SmHrPayrollGenerate::find($request->payroll_generate_id);

			$payments->payment_date = date('Y-m-d', strtotime($request->payment_date));

			$payments->payment_mode = $request->payment_mode;

			$payments->note = $request->note;

			if ($request->payment_mode == 2) {

				$payments->bank_name = $request->cheque_bank_name;

				$payments->cheque_deposite_date = $request->cheque_issue_date != "" ? date('Y-m-d', strtotime($request->cheque_issue_date)) : '';

				$payments->cheque_no = $request->cheque_no;

			} elseif ($request->payment_mode == 3) {

				$payments->account_id = $request->accounts;

				$payments->bank_name = $request->bank_name;

				$payments->cheque_deposite_date = $request->deposite_date != "" ? date('Y-m-d', strtotime($request->deposite_date)) : '';

				$payments->account_name = $request->account_name;

				$payments->account_no = $request->account_no;

			} elseif ($request->payment_mode == 1) {

				$payments->expense_head_id = $request->expense_head;

			}

			$payments->payroll_status = 'P';

			$payments->updated_by = Auth()->user()->id;

			$result = $payments->update();

			$staffs = SmStaff::where('active_status', '=', '1')->where('role_id', '=', $request->role_id)->get();

			$roles = Role::all();

			return view('backEnd.humanResource.payroll.index', compact('staffs', 'roles', 'payroll_month', 'payroll_year'));

		}catch (\Exception $e) {

		   Toastr::error('Operation Failed', 'Failed');

		   return redirect()->back(); 

		}

	}



	public function viewPayslip($id)

	{

		try{

			$schoolDetails = SmGeneralSettings::find(1);

			$payrollDetails = SmHrPayrollGenerate::find($id);

			$payrollEarnDetails = SmHrPayrollEarnDeduc::where('active_status', '=', '1')->where('payroll_generate_id', '=', $id)->where('earn_dedc_type', '=', 'E')->get();

			$payrollDedcDetails = SmHrPayrollEarnDeduc::where('active_status', '=', '1')->where('payroll_generate_id', '=', $id)->where('earn_dedc_type', '=', 'D')->get();

			return view('backEnd.humanResource.payroll.viewPayslip', compact('payrollDetails', 'payrollEarnDetails', 'payrollDedcDetails', 'schoolDetails'));

		}catch (\Exception $e) {

		   Toastr::error('Operation Failed', 'Failed');

		   return redirect()->back(); 

		}

	}



	public function payrollReport(Request $request)

	{

		try{

			$roles = Role::where('active_status', '=', '1')->where('id', '!=', 1)->where('id', '!=', 2)->where('id', '!=', 7)->get();

			return view('backEnd.reports.payroll', compact('roles'));

		}catch (\Exception $e) {

		   Toastr::error('Operation Failed', 'Failed');

		   return redirect()->back(); 

		}

	}



	public function mailPayslip($id)

	{

		try{

			$schoolDetails = SmGeneralSettings::find(1);

			$payrollDetails = SmHrPayrollGenerate::find($id);

			$saveData['email']  = $payrollDetails->staffDetails->email;

			$payrollEarnDetails = SmHrPayrollEarnDeduc::where('active_status', '=', '1')->where('payroll_generate_id', '=', $id)->where('earn_dedc_type', '=', 'E')->get();

			$payrollDedcDetails = SmHrPayrollEarnDeduc::where('active_status', '=', '1')->where('payroll_generate_id', '=', $id)->where('earn_dedc_type', '=', 'D')->get();

			//return view('backEnd.humanResource.payroll.mail_payslip', compact('schoolDetails', 'payrollDetails', 'payrollEarnDetails', 'payrollDedcDetails'));

			Mail::send('backEnd.humanResource.payroll.mail_payslip', compact('schoolDetails', 'payrollDetails', 'payrollEarnDetails', 'payrollDedcDetails'), function ($message) use ($saveData) {

				$message->to($saveData['email'], 'Tutorials Point')->subject('payslip');

				$message->from('admin@aksobhantraders.com', 'AKS Sobhan Traders');

			});

			Toastr::error('Payslip has been sent successfully', 'Failed');

			return redirect('payroll');

		}catch (\Exception $e) {

		   Toastr::error('Operation Failed', 'Failed');

		   return redirect()->back(); 

		}

	}



	public function searchPayrollReport(Request $request)

	{

		$request->validate([

			'role_id' => "required",

			'payroll_month' => "required",

			'payroll_year' => "required"

		]);

		try{

			$role_id = $request->role_id;

			$payroll_month = $request->payroll_month;

			$payroll_year = $request->payroll_year;



			$query = '';

			if ($request->role_id != "") {

				$query = "AND s.role_id = '$request->role_id'";

			}

			if ($request->payroll_month != "") {

				$query .= "AND pg.payroll_month = '$request->payroll_month'";

			}

			if ($request->payroll_year != "") {

				$query .= "AND pg.payroll_year = '$request->payroll_year'";

			}

			$staffsPayroll = DB::select(DB::raw("SELECT pg.*, s.full_name, r.name, 									d.title 

												FROM sm_hr_payroll_generates pg

												LEFT JOIN sm_staffs s ON pg.staff_id = s.id

												LEFT JOIN roles r ON s.role_id = r.id

												LEFT JOIN sm_designations d ON s.designation_id = d.id

												WHERE pg.active_status AND pg.payroll_status='P'

												$query"));



			$roles = Role::where('active_status', '=', '1')->where('id', '!=', 1)->where('id', '!=', 2)->where('id', '!=', 7)->get();

			return view('backEnd.reports.payroll', compact('staffsPayroll', 'roles', 'payroll_month', 'payroll_year', 'role_id'));

		}catch (\Exception $e) {

		   Toastr::error('Operation Failed', 'Failed');

		   return redirect()->back(); 

		}

	}



	public function printPayrollReport($role_id, $month, $year)

	{

		$query = '';

		if ($role_id != "") {

			$query = "AND s.role_id = '$role_id'";

		}

		if ($month != "") {

			$query .= "AND pg.payroll_month = '$month'";

		}

		if ($year != "") {

			$query .= "AND pg.payroll_year = '$year'";

		}

		$staffsPayroll = DB::select(DB::raw("SELECT pg.*, s.full_name, r.name, 									d.title 

											FROM sm_hr_payroll_generates pg

        									LEFT JOIN sm_staffs s ON pg.staff_id = s.id

        									LEFT JOIN roles r ON s.role_id = r.id

        									LEFT JOIN sm_designations d ON s.designation_id = d.id

        									WHERE pg.active_status AND pg.payroll_status='P'

        									$query"));



		$schoolDetails = SmGeneralSettings::find(1);

		$pdf = PDF::loadView('backEnd.humanResource.printPayrollReport', ['staffsPayroll' => $staffsPayroll, 'schoolDetails' => $schoolDetails]);

		$pdf->setPaper('A4', 'landscape');

		return $pdf->stream('printPayrollReport.pdf');

		// return view('backEnd.humanResource.printPayrollReport', compact('staffsPayroll', 'schoolDetails'));

		

	}



	public function printPayslip($id)

	{

		$schoolDetails = SmGeneralSettings::find(1);

		$payrollDetails = SmHrPayrollGenerate::find($id);

		$payrollEarnDetails = SmHrPayrollEarnDeduc::where('active_status', '=', '1')->where('payroll_generate_id', '=', $id)->where('earn_dedc_type', '=', 'E')->get();

		$payrollDedcDetails = SmHrPayrollEarnDeduc::where('active_status', '=', '1')->where('payroll_generate_id', '=', $id)->where('earn_dedc_type', '=', 'D')->get();

		//return view('backEnd.humanResource.payroll.printPayslip', compact('payrollDetails', 'payrollEarnDetails', 'payrollDedcDetails', 'schoolDetails'));

		$pdf = PDF::loadView('backEnd.humanResource.payroll.printPayslip', ['payrollDetails' => $payrollDetails, 'payrollEarnDetails' => $payrollEarnDetails, 'payrollDedcDetails' => $payrollDedcDetails, 'schoolDetails' => $schoolDetails]);

		$pdf->setPaper('A4', 'landscape');

		return $pdf->stream('competitor-list.pdf');

	}







	public function bankAccountInfo(Request $request)

	{

		$bank_detail = SmBankAccount::find($request->id);

		$income_amount = SmAddIncome::where('payment_method_id', 3)->where('account_id', $request->id)->sum('amount');

		$expense_amount = SmAddExpense::where('payment_method_id', 3)->where('account_id', $request->id)->where('status', 1)->sum('amount');

		$payroll_amount = SmHrPayrollGenerate::where('payment_mode', 3)->where('account_id', $request->id)->sum('net_salary');

		$transfer_amount = SmFundTransfer::where('bank_account_id', $request->id)->sum('amount');

		$total_balance = $bank_detail->opening_balance + $income_amount + $transfer_amount - $expense_amount - $payroll_amount;

		return Response()->json([$bank_detail, $total_balance]);

	}


	public function employeeDetails($id)
{
    $staff = SmStaff::with('company')->findOrFail($id);

    $year  = date('Y');
    $month = date('m');
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    // Fetch attendance
    $attendances = DB::table('sm_staff_attendences')
        ->where('staff_id', $id)
        ->whereYear('attendence_date', $year)
        ->whereMonth('attendence_date', $month)
        ->pluck('attendence_type', 'attendence_date');

    // Build daily map
    $days = [];
    $summary = ['P'=>0,'A'=>0,'L'=>0,'WO'=>0,'H'=>0];
    for ($d=1; $d<=$daysInMonth; $d++) {
        $date = sprintf('%04d-%02d-%02d',$year,$month,$d);
        $type = isset($attendances[$date]) ? $attendances[$date] : '-';
        $days[$d] = $type;
        if (isset($summary[$type])) $summary[$type]++;
    }

    // Salary (placeholder)
    $salary = [
        'basic' => $staff->basic_salary,
        'hra'   => 0,
        'transport' => 0,
        'gross' => $staff->basic_salary,
        'deduction' => 0,
        'net' => $staff->basic_salary,
    ];

    return view('backEnd.humanResource.payroll.employee_details',
                compact('staff','days','summary','salary','month','year','daysInMonth'));
}

public function getAttendance($id)
{
    $month = date('m');
    $year  = date('Y');
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    $att = DB::table('sm_staff_attendences')
        ->where('staff_id', $id)
        ->whereYear('attendence_date', $year)
        ->whereMonth('attendence_date', $month)
        ->pluck('attendence_type', 'attendence_date');

    $html = '<div class="table-responsive"><table class="table table-sm table-bordered text-center"><thead><tr>';
    for ($d=1; $d <= $daysInMonth; $d++) {
        $html .= '<th>'.$d.'</th>';
    }
    $html .= '</tr></thead><tbody><tr>';
    for ($d=1; $d <= $daysInMonth; $d++) {
        $date = sprintf("%04d-%02d-%02d", $year, $month, $d);
        $type = isset($att[$date]) ? $att[$date] : '-';
        $color = $type == 'P' ? 'bg-success text-white'
                : ($type == 'A' ? 'bg-danger text-white'
                : ($type == 'WO' ? 'bg-secondary text-white'
                : ($type == 'H' ? 'bg-info text-white' : '')));
        $html .= '<td class="'.$color.'">'.$type.'</td>';
    }
    $html .= '</tr></tbody></table></div>';

    return response()->json(['html' => $html]);
}




}

