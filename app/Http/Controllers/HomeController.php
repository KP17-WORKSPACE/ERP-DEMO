<?php

namespace App\Http\Controllers;

use App\Role;
use App\SmToDo;
use App\SmStaff;
use App\SmTender;
use App\SmHoliday;
use App\SmItemSell;
use App\SmAddIncome;
use App\SmQuotation;
use App\SmAddExpense;
use App\SmBankAccount;
use App\SmItemReceive;
use App\SmNoticeBoard;
use App\SmLeaveRequest;
use App\SmUpcomingTender;
use App\SmHrPayrollGenerate;
use App\SmRolePermission;
use App\SysHelper;
use App\User;
// tender
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Modules\Project\Entities\InfixProject;
use Spondonit\Invoice\Models\InfixInvoice;

//end tender

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('PM');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function dashboard()
    {
        try{
            $role_id = Session::get('role_id');
            if (Auth::user()->role_id == 3) {
                return redirect()->route('user.dashboard');
            } else {
                return redirect('admin-dashboard');
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    // for display dashboard

    public function index()
    {
        try{
            return redirect('crm-dashboard');
            
            $user_id = Auth()->user()->id;
            $peoples = Role::where('id', '!=', 1)->get();
            //$totalStudents = SmStudent::where('active_status', 1)->get();
            // $totalTeachers = SmStaff::where('active_status', 1)->where('role_id', 4)->get();
            // //$totalParents = SmParent::all();
            $totalStaffs = SmStaff::where('active_status', 1)->where('role_id', '!=', 1)->where('role_id', '!=', 4)->get();
            $toDoLists = SmToDo::where('complete_status', 'P')->where('created_by', $user_id)->orderBy('date', 'DESC')->take(5)->get();
            $toDoListsCompleteds = SmToDo::where('complete_status', 'C')->where('created_by', $user_id)->get();
            $notices = SmNoticeBoard::select('*')->where('active_status', 1)->get();
            // for current month
            $m_add_incomes = SmAddIncome::where('active_status', 1)->where('date', 'like', date('Y-m-') . '%')->sum('amount');
            // $m_fees_payments = SmFeesPayment::where('active_status', 1)->where('payment_date', 'like', date('Y-m-').'%')->sum('amount');
            $m_item_sells = SmItemSell::where('active_status', 1)->where('sell_date', 'like', date('Y-m-') . '%')->sum('total_paid');
            $m_total_income = $m_add_incomes  + $m_item_sells;
            $m_add_expenses = SmAddExpense::where('active_status', 1)->where('date', 'like', date('Y-m-') . '%')->sum('amount');
            $m_item_receives = SmItemReceive::where('active_status', 1)->where('received_date', 'like', date('Y-m-') . '%')->sum('total_paid');
            $m_payroll_payments = SmHrPayrollGenerate::where('active_status', 1)->where('payroll_status', 'P')->where('created_at', 'like', date('Y-m-') . '%')->sum('net_salary');
            $m_total_expense = $m_add_expenses + $m_item_receives + $m_payroll_payments;
            // for current year
            $y_add_incomes = SmAddIncome::where('active_status', 1)->where('date', 'like', date('Y-') . '%')->sum('amount');
            // $y_fees_payments = SmFeesPayment::where('active_status', 1)->where('payment_date', 'like', date('Y-').'%')->sum('amount');
            $y_item_sells = SmItemSell::where('active_status', 1)->where('sell_date', 'like', date('Y-') . '%')->sum('total_paid');
            $y_total_income = $y_add_incomes  + $y_item_sells;
            $y_add_expenses = SmAddExpense::where('active_status', 1)->where('date', 'like', date('Y-') . '%')->sum('amount');
            $y_item_receives = SmItemReceive::where('active_status', 1)->where('received_date', 'like', date('Y-') . '%')->sum('total_paid');
            $y_payroll_payments = SmHrPayrollGenerate::where('active_status', 1)->where('payroll_status', 'P')->where('created_at', 'like', date('Y-') . '%')->sum('net_salary');
            $y_total_expense = $y_add_expenses + $y_item_receives + $y_payroll_payments;
            $holidays = SmHoliday::where('active_status', 1)->get();
            // new for tender
            SmUpcomingTender::where('open_date', '<', date('Y-m-d', strtotime(date('Y-m-d'))))->where('is_expired', 0)->OrderBy('open_date', 'ASC')->update(['is_expired' => 1]);
            $complete_tenders = SmTender::where('active_status', 1)->get();
            $tenders = SmTender::all();
            $upcoming_tenders = SmUpcomingTender::where('is_expired', 0)->orderBy('open_date', 'ASC')->take(5)->get();
            $upcoming_tender_count = SmUpcomingTender::where('is_expired', 0)->orderBy('open_date', 'ASC')->get();
            $apply_leaves = SmLeaveRequest::where('active_status', 1)->take(5)->get();
            // end tender
            $tenderCompleted = SmTender::where('stage_status', 4)->get();
            $tenderInspection = SmTender::where([['shipment_work_order_date', '!=', NULL], ['status_delivery_date', '!=', NULL], ['inspection_completion_date', '!=', NULL], ['completion_date', NULL]])->get();
            $tenderDelivered = SmTender::where([['shipment_work_order_date', '!=', NULL], ['status_delivery_date', '!=', NULL],  ['inspection_completion_date', NULL],       ['completion_date', NULL]])->get();
            $tenderShipment = SmTender::where([['shipment_work_order_date', '!=', NULL], ['status_delivery_date', NULL],         ['inspection_completion_date', NULL],       ['completion_date', NULL]])->get();
            $bank_accounts = SmBankAccount::all()->take(2);
            $m_win_tenders = SmUpcomingTender::where('is_winner', 1)->where('updated_at', 'like', date('Y-m-') . '%')->count();
            $t_win_tenders = SmUpcomingTender::where('is_winner', 1)->count();
            $m_work_orders = SmTender::where('updated_at', 'like', date('Y-m-') . '%')->count();
            $nextY = date('Y') + 1;
            $nextYearAprilDate = $nextY . '-06-30';
            $CurrentYearAprilDate = date('Y') . '-07-01';
            $to = date('Y-m-d', strtotime($nextYearAprilDate));
            $from = date('Y-m-d', strtotime($CurrentYearAprilDate));
            $quotations = SmQuotation::all()->count();
            $total_invoices = InfixInvoice::totalInvoice();
            $y_work_orders = SmTender::whereBetween('open_date', [$from, $to])->count();
            $y_win_tenders = SmUpcomingTender::where('open_date', '>=', $from)->where('open_date', '<=', $to)->where('is_winner', 1)->count();
            $complete_project = InfixProject::where('is_complete', '=', 1)->get()->count();
            $incomplete_project = InfixProject::where('is_complete', '=', 0)->get()->count();
            $projects = InfixProject::where('active_status', 1)->where('is_complete', '=', 0)->orderBy('due_date', 'asc')->take(5)->get();
            
            return view('backEnd/dashboard', compact(
                'totalStaffs',
                'toDoLists',
                'notices',
                'toDoListsCompleteds',
                'm_total_income',
                'm_total_expense',
                'y_total_income',
                'y_total_expense',
                'holidays',
                'peoples',
                'complete_tenders',
                'tenders',
                'upcoming_tenders',
                'apply_leaves',
                'tenderCompleted',
                'bank_accounts',
                'm_win_tenders',
                'y_win_tenders',
                't_win_tenders',
                'm_work_orders',
                'y_work_orders',
                'upcoming_tender_count',
                'complete_project',
                'incomplete_project',
                'total_invoices',
                'quotations',
                'projects'
            ));
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function saveToDoData(Request $request)
    {
        try{
            $toDolists = new SmToDo();
            $toDolists->todo_title = $request->todo_title;
            $toDolists->date = date('Y-m-d', strtotime($request->date));
            $toDolists->created_by = Auth()->user()->id;
            $results = $toDolists->save();
            if ($results) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function viewToDo($id)
    {
        try{
            $toDolists = SmToDo::where('id', $id)->first();
            return view('backEnd.dashboard.viewToDo', compact('toDolists'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function editToDo($id)
    {
        try{
            $editData = SmToDo::find($id);
            return view('backEnd.dashboard.editToDo', compact('editData', 'id'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function updateToDo(Request $request)
    {
        try{
            $to_do_id = $request->to_do_id;
            $toDolists = SmToDo::find($to_do_id);
            $toDolists->todo_title = $request->todo_title;
            $toDolists->date = date('Y-m-d', strtotime($request->date));
            $toDolists->complete_status = $request->complete_status;
            $toDolists->updated_by = Auth()->user()->id;
            $results = $toDolists->update();
            if ($results) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function manageToDoList(Request $request)
    {
        try{
            $to_do = SmToDo::all();
            return view('backEnd.dashboard.toDoListView', compact('toDolists'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function removeToDo(Request $request)
    {
        $to_do = SmToDo::find($request->id);
        $to_do->complete_status = "C";
        $to_do->save();
        $html = "";
        return response()->json('html');
    }

    public function getToDoList(Request $request)
    {
        $to_do_list = SmToDo::where('complete_status', 'C')->get();
        $datas = [];
        foreach ($to_do_list as $to_do) {
            $datas[] = array(
                'title' => $to_do->todo_title,
                'date' => date('jS M, Y', strtotime($to_do->date))
            );
        }
        return response()->json($datas);
    }

    public function viewNotice($id)
    {
        try{
            $notice = SmNoticeBoard::find($id);
            return view('backEnd.dashboard.view_notice', compact('notice'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function updatePassowrd()
    {
        try{
            return view('backEnd.update_password');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function updatePassowrdStore(Request $request)
    {
        $request->validate([
            'current_password' => "required",
            'new_password'  => "required|same:confirm_password|min:6|different:current_password",
            'confirm_password'  => 'required|min:6'
        ]);
        try{
            $user = Auth::user();
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $result = $user->save();
                if ($result) {
                    Toastr::success('Password has been changed successfully', 'Success');
                    return redirect()->back();
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            } else {
                Toastr::error('You have entered a wrong current password', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function passwordexp()
    {
        try{
            return view('backEnd.update_exp_password');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function updatePassowrdStore2(Request $request)
    {
        $request->validate([
            'current_password' => "required",
            'new_password'  => "required|same:confirm_password|min:6|different:current_password",
            'confirm_password'  => 'required|min:6'
        ]);
        try{
            $user = Auth::user();
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $result = $user->save();
                if ($result) {
                    Toastr::success('Password has been changed successfully', 'Success');
                    return redirect('crm-dashboard');
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            } else {
                Toastr::error('You have entered a wrong current password', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}
