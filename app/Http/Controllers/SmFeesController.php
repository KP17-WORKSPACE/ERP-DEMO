<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use Illuminate\Http\Request;
use Redirect;
use App\SmClass;
use App\SmStudent;
use App\SmFeesCarryForward;
use App\SmFeesAssign;
use App\SmFeesAssignDiscount;
use App\SmFeesMaster;
use App\SmAddIncome;
use App\SmAddExpense;
use App\SmCostCenter;
use App\SmDailyExpense;
use App\SmChartOfAccount;
use DB;
use App\User;
use Auth;
use PDF;
use Validator;

class SmFeesController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function feesForward(Request $request)
    {
        $classes = SmClass::where('active_status', 1)->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($classes, null);
        }
        return view('backEnd.feesCollection.fees_forward', compact('classes'));
    }

    public function feesForwardSearch(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
            'section' => 'required'
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $classes = SmClass::where('active_status', 1)->get();
        $students = SmStudent::where('class_id', $request->class)->where('section_id', $request->section)->get();


        if ($students->count() != 0) {
            foreach ($students as $student) {
                $fees_balance = SmFeesCarryForward::where('student_id', $student->id)->count();
            }

            $class_id = $request->class;

            if ($fees_balance == 0) {

                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    $data = [];
                    $data['classes'] = $classes->toArray();
                    $data['students'] = $students->toArray();
                    $data['class_id'] = $class_id;
                    return ApiBaseMethod::sendResponse($data, null);
                }
                return view('backEnd.feesCollection.fees_forward', compact('classes', 'students', 'class_id'));
            } else {
                $update = "";

                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    $data = [];
                    $data['classes'] = $classes->toArray();
                    $data['students'] = $students->toArray();
                    $data['class_id'] = $class_id;
                    $data['update'] = $update;
                    return ApiBaseMethod::sendResponse($data, null);
                }
                return view('backEnd.feesCollection.fees_forward', compact('classes', 'students', 'update', 'class_id'));
            }
        } else {

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('No result Found');
            }
            return redirect('fees-forward')->with('message-danger', 'No result Found');
        }
    }

    public function feesForwardStore(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->id as $student) {

                if ($request->update == 1) {

                    $fees_forward = SmFeesCarryForward::find($student);
                    $fees_forward->balance = $request->balance[$student];
                    $fees_forward->save();
                } else {
                    $fees_forward = new SmFeesCarryForward();
                    $fees_forward->student_id = $student;
                    $fees_forward->balance = $request->balance[$student];
                    $fees_forward->save();
                }
            }
            DB::commit();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse(null, 'Fees has been forwarded successfully');
            }

            return redirect('fees-forward')->with('message-success', 'Fees has been forwarded successfully');
        } catch (\Exception $e) {
            DB::rollback();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }
    }

    public function collectFees(Request $request)
    {
        $classes = SmClass::where('active_status', 1)->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {

            return ApiBaseMethod::sendResponse($classes, null);
        }
        return view('backEnd.feesCollection.collect_fees', compact('classes'));
    }
    public function collectFeesSearch(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required'
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $students = SmStudent::query();
        $students->where('class_id', $request->class);
        if ($request->section != "") {
            $students->where('section_id', $request->section);
        }
        if ($request->keyword != "") {
            $students->where('full_name', 'like', '%' . $request->keyword . '%')->orWhere('admission_no', $request->keyword)->orWhere('roll_no', $request->keyword)->orWhere('national_id_no', $request->keyword)->orWhere('local_id_no', $request->keyword);
        }
        $students = $students->get();

        if ($students->isEmpty()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('No result found');
            }
            return redirect('collect-fees')->with('message-danger', 'No result found');
        }

        $classes = SmClass::where('active_status', 1)->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['classes'] = $classes->toArray();
            $data['students'] = $students->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.feesCollection.collect_fees', compact('classes', 'students'));
    }



    public function collectFeesStudent(Request $request, $id)
    {

        $student = SmStudent::find($id);
        $fees_assigneds = SmFeesAssign::where('student_id', $id)->orderBy('id', 'desc')->get();
        $fees_discounts = SmFeesAssignDiscount::where('student_id', $id)->get();



        //dd($fees_discounts);


        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['student'] = $student;
            $data['fees_assigneds'] = $fees_assigneds;
            $data['fees_discounts'] = $fees_discounts;
            $data['applied_discount'] = $applied_discount;
            return ApiBaseMethod::sendResponse($data, null);
        }

        return view('backEnd.feesCollection.collect_fees_student_wise', compact('student', 'fees_assigneds', 'fees_discounts', 'applied_discount'));
    }

    public function feesGenerateModal(Request $request, $amount, $student_id, $type)
    {

        $amount = $amount;
        $fees_type_id = $type;
        $student_id = $student_id;
        $discounts = SmFeesAssignDiscount::where('student_id', $student_id)->get();




        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['amount'] = $amount;
            $data['discounts'] = $discounts;
            $data['fees_type_id'] = $fees_type_id;
            $data['student_id'] = $student_id;
            $data['applied_discount'] = $applied_discount;
            return ApiBaseMethod::sendResponse($data, null);
        }

        return view('backEnd.feesCollection.fees_generate_modal', compact('amount', 'discounts', 'fees_type_id', 'student_id', 'applied_discount'));
    }

    public function feesPaymentStore(Request $request)
    {

        $discount_group = explode('-', $request->discount_group);


        $user = Auth::user();


        if ($result) {
            return Redirect::route('fees_collect_student_wise', array('id' => $request->student_id))->with('message-success', 'Fees payment has been collected  successfully');
        } else {
            return Redirect::route('fees_collect_student_wise', array('id' => $request->student_id))->with('message-danger', 'Something went wrong, please try again');
        }
    }


    public function feesPaymentDelete(Request $request)
    {
    }

    public function searchFeesPayment(Request $request)
    {


        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($fees_payments, null);
        }
        return view('backEnd.feesCollection.search_fees_payment', compact('fees_payments'));
    }

    public function feesPaymentSearch(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'payment_id' => 'required'
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $payment = explode('/', $request->payment_id);

        if (!isset($payment[1])) {
            $payment[1] = 0;
        }



        return view('backEnd.feesCollection.search_fees_payment', compact('fees_payments'));
    }

    public function searchFeesDue(Request $request)
    {
        $classes = SmClass::where('active_status', 1)->get();
        $fees_masters = SmFeesMaster::select('fees_group_id')->where('active_status', 1)->distinct('fees_group_id')->get();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['classes'] = $classes->toArray();
            $data['fees_masters'] = $fees_masters->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.feesCollection.search_fees_due', compact('classes', 'fees_masters'));
    }

    public function feesDueSearch(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'fees_group' => 'required',
            'class' => 'required',
            'section' => 'required',
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $fees_group = explode('-', $request->fees_group);

        // $fees_master = SmFeesMaster::select('id', 'amount')->where('fees_group_id', $fees_group[0])->where('fees_type_id', $fees_group[1])->first();

        $fees_master = SmFeesMaster::select('id', 'amount')->where('fees_group_id', $fees_group[0])->where('fees_type_id', $fees_group[1])->first();


        if ($fees_group[0] != 1 && $fees_group[0] != 2) {
            $students = SmStudent::where('class_id', $request->class)->where('section_id', $request->section)->get();
        } else {
            if ($fees_group[0] == 1) {
                $students = SmStudent::where('class_id', $request->class)->where('section_id', $request->section)->where('route_list_id', '!=', '')->get();
            } else {
                $students = SmStudent::where('class_id', $request->class)->where('section_id', $request->section)->where('room_id', '!=', '')->get();
            }
        }

        $fees_dues = [];


        foreach ($students as $student) {

            if ($fees_group[0] != 1 && $fees_group[0] != 2) {
                $fees_master = SmFeesMaster::select('id', 'amount')->where('fees_group_id', $fees_group[0])->where('fees_type_id', $fees_group[1])->first();
                $total_amount = $fees_master->amount;
            } else {
                if ($fees_group[0] == 1) {
                    $total_amount = $student->route->far;
                } else {
                    $total_amount = $student->room->cost_per_bed;
                }
            }


            $fees_assign = SmFeesAssign::where('student_id', $student->id)->where('fees_master_id', $fees_master->id)->first();


            $paid;

            if ($fees_assign != "") {
                if ($total_amount > $paid) {
                    $fees_dues[] = $fees_assign;
                }
            }
        }


        $classes = SmClass::where('active_status', 1)->get();
        $fees_masters = SmFeesMaster::select('fees_group_id')->where('active_status', 1)->distinct('fees_group_id')->get();

        $class_id = $request->class;
        $fees_group_id = $fees_group[1];

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['classes'] = $classes->toArray();
            $data['fees_masters'] = $fees_masters;
            $data['fees_dues'] = $fees_dues;
            $data['class_id'] = $class_id;
            $data['fees_group_id'] = $fees_group_id;
            return ApiBaseMethod::sendResponse($data, null);
        }

        return view('backEnd.feesCollection.search_fees_due', compact('classes', 'fees_masters', 'fees_dues', 'class_id', 'fees_group_id'));
    }

    public function feesStatemnt(Request $request)
    {
        $classes = SmClass::where('active_status', 1)->get();
        $fees_masters = SmFeesMaster::select('fees_group_id')->where('active_status', 1)->distinct('fees_group_id')->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['classes'] = $classes->toArray();
            $data['fees_masters'] = $fees_masters->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.feesCollection.fees_statment', compact('classes', 'fees_masters'));
    }

    public function feesStatementSearch(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'student' => 'required',
            'class' => 'required',
            'section' => 'required',
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        $classes = SmClass::where('active_status', 1)->get();
        $fees_masters = SmFeesMaster::select('fees_group_id')->where('active_status', 1)->distinct('fees_group_id')->get();
        $student = SmStudent::find($request->student);
        $fees_assigneds = SmFeesAssign::where('student_id', $request->student)->get();
        $fees_discounts = SmFeesAssignDiscount::where('student_id', $request->student)->get();



        $class_id = $request->class;

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['classes'] = $classes->toArray();
            $data['fees_masters'] = $fees_masters->toArray();
            $data['fees_assigneds'] = $fees_assigneds->toArray();
            $data['fees_discounts'] = $fees_discounts->toArray();
            $data['applied_discount'] = $applied_discount;
            $data['student'] = $student;
            $data['class_id'] = $class_id;
            return ApiBaseMethod::sendResponse($data, null);
        }

        return view('backEnd.feesCollection.fees_statment', compact('classes', 'fees_masters', 'fees_assigneds', 'fees_discounts', 'applied_discount', 'student', 'class_id'));
    }

    public function balanceFeesReport(Request $request)
    {
        $classes = SmClass::where('active_status', 1)->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($classes, null);
        }
        return view('backEnd.feesCollection.balance_fees_report', compact('classes'));
    }

    public function balanceFeesSearch(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
            'section' => 'required'
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $students = SmStudent::where('class_id', $request->class)->where('section_id', $request->section)->get();
        $balance_students = [];

        $fees_masters = SmFeesMaster::where('active_status', 1)->get();

        foreach ($students as $student) {
            $total_balance = 0;
            $total_discount = 0;
            $total_amount = 0;

            $total_paid = $total_balance + $total_discount;
            if ($total_amount > $total_paid) {
                $balance_students[] = $student;
            }
        }


        $class_id = $request->class;



        $classes = SmClass::where('active_status', 1)->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['classes'] = $classes->toArray();
            $data['balance_students'] = $balance_students;
            $data['class_id'] = $class_id;
            return ApiBaseMethod::sendResponse($data, null);
        }

        return view('backEnd.feesCollection.balance_fees_report', compact('classes', 'balance_students', 'class_id'));
    }

    

    public function costCenterReports(Request $request)
    {
        $cost_centers = SmCostCenter::all();

        $expense_heads = SmChartOfAccount::where('type', 'E')->get();


        return view('backEnd.feesCollection.cost_center_report', compact('cost_centers', 'expense_heads'));
    }

    public function costCenterReportSearch(Request $request)
    {


        $input = $request->all();
        $expense_heads = SmChartOfAccount::where('type', 'E')->get();

        $validator = Validator::make($input, [
            'cost_center' => 'required',
            'date_from' => 'required',
            'date_to' => 'required'
        ]);

        $date_from = date('Y-m-d', strtotime($request->date_from));
        $date_to = date('Y-m-d', strtotime($request->date_to));

        if ($request->cost_center == "all") {

            if ($request->expense_head != "") {
                $all_expenses = SmAddExpense::where([['date', '>=', $date_from], ['date', '<=', $date_to]])->where('expense_head_id', $request->expense_head)->where('active_status', 1)->get();
            } else {
                $all_expenses = SmAddExpense::where([['date', '>=', $date_from], ['date', '<=', $date_to]])->where('active_status', 1)->get();
            }

            $daily_expenses = SmDailyExpense::where([['date', '>=', $date_from], ['date', '<=', $date_to]])->get();
        } else {

            if ($request->expense_head != "") {


                $all_expenses = SmAddExpense::where([['date', '>=', $date_from], ['date', '<=', $date_to], ['cost_center_id', $request->cost_center]])->where('expense_head_id', $request->expense_head)->where('active_status', 1)->get();
            } else {

                $all_expenses = SmAddExpense::where([['date', '>=', $date_from], ['date', '<=', $date_to], ['cost_center_id', $request->cost_center]])->where('active_status', 1)->get();
            }

            $daily_expenses = SmDailyExpense::where([['date', '>=', $date_from], ['date', '<=', $date_to], ['cost_center_id', $request->cost_center]])->get();
        }


        $cost_centers = SmCostCenter::all();


        return view('backEnd.feesCollection.cost_center_report', compact('all_expenses', 'cost_centers', 'daily_expenses', 'expense_heads'));
    }

    public function transactionReport()
    {
        // return "report";
        return view('backEnd.feesCollection.transaction_report');
    }



    public function transactionReportSearch(Request $request)
    {


        $date_from = date('Y-m-d', strtotime($request->date_from));
        $date_to = date('Y-m-d', strtotime($request->date_to));


        $add_incomes = SmAddIncome::where('date', '>=', $date_from)->where('date', '<=', $date_to)->where('active_status', 1)->get();
        $add_expenses = SmAddExpense::where('date', '>=', $date_from)->where('date', '<=', $date_to)->where('active_status', 1)->get();


        return view('backEnd.feesCollection.transaction_report', compact('add_incomes', 'add_expenses'));
    }










    public function studentFineReport(Request $request)
    {
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse(null, null);
        }
        return view('backEnd.reports.student_fine_report');
    }

    public function studentFineReportSearch(Request $request)
    {
        $date_from = date('Y-m-d', strtotime($request->date_from));
        $date_to = date('Y-m-d', strtotime($request->date_to));


        return view('backEnd.reports.student_fine_report', compact('fees_payments'));
    }
}
