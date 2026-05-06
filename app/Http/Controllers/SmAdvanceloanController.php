<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\SmStaff;

use App\SmAdvanceloan;
use Auth;
use App\SmHrPayrollGenerate;



class SmAdvanceloanController extends Controller

{

    public function addLoanCreate(){

    	$staffs = SmStaff::where('active_status', 1)->get();

        $loan_lists = SmAdvanceloan::all();



    	return view('backEnd.humanResource.advanceloan.addLoanCreate', compact('staffs', 'loan_lists'));

    }





    public function loanStore(Request $request){

    	

    	$request->validate([

    		'staff' => 'required',

    		'date' => 'required',

    		'amount' => 'required'

    	]);



    	$loan = new SmAdvanceloan();

    	$loan->staff_id = $request->staff;

    	$loan->date = $request->date != ""? date('Y-m-d', strtotime($request->date)):'';

    	$loan->amount = $request->amount;

    	$loan->note = $request->note;

    	$result = $loan->save();

    	if($result){

    		return redirect()->back()->with('message-success', 'Loan has been created successfully');

    	}else{

    		return redirect()->back()->with('message-danger', 'Something went wrong, please try again');

    	}

    }



    public function loanList(){

    	$loan_lists = SmAdvanceloan::all();

    	$loan_lists = $loan_lists->groupBy('staff_id');



    	return view('backEnd.humanResource.advanceloan.loanList', compact('loan_lists'));

    }



    public function loanView($staff_id){

        $staffDetails = SmStaff::find($staff_id);

        $loan_lists = SmAdvanceloan::where('staff_id', $staff_id)->get();

        $deduct_lists = SmHrPayrollGenerate::where('staff_id', $staff_id)->get();

        return view('backEnd.humanResource.advanceloan.loanView', compact('staffDetails', 'loan_lists', 'deduct_lists'));

    }



    public function loanEdit($id){

        $staffs = SmStaff::all();

        $loan_lists = SmAdvanceloan::all();

        $editData = SmAdvanceloan::find($id);



        return view('backEnd.humanResource.advanceloan.addLoanCreate', compact('staffs', 'loan_lists', 'editData'));

    }





    public function loanUpdate(Request $request){

        

        $request->validate([

            'staff' => 'required',

            'date' => 'required',

            'amount' => 'required'

        ]);



        $loan = SmAdvanceloan::find($request->id);



        $loan->staff_id = $request->staff;

        $loan->date = $request->date != ""? date('Y-m-d', strtotime($request->date)):'';

        $loan->amount = $request->amount;

        $loan->note = $request->note;

        $result = $loan->save();

        if($result){

            return redirect('add-loan')->with('message-success', 'Loan has been updated successfully');

        }else{

            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');

        }

    } 



    public function loanDelete($id){

        

       



        $result = SmAdvanceloan::destroy($id);

        if($result){

            return redirect('add-loan')->with('message-success-delete', 'Loan has been deleted successfully');

        }else{

            return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');

        }

    }



     // 👇 Define static loan types here
    private $loanTypes = [
        ['id' => 1, 'name' => 'Salary Advance'],
        ['id' => 2, 'name' => 'Personal Loan'],
        ['id' => 3, 'name' => 'Emergency Loan'],
        ['id' => 4, 'name' => 'Festival Advance'],
    ];

   public function index(Request $request)
{
    // ✅ Safely get staff ID (supports both employee and admin login)
    $staffId = optional(Auth::user()->staff)->id ?? Auth::user()->id;

    // ✅ Build query for this staff’s loan requests
    $query = \App\SmAdvanceloan::where('staff_id', $staffId);

    if ($request->get('status')) {
        $query->where('status', $request->get('status'));
    }

    if ($request->get('type_id')) {
        $query->where('type_id', $request->get('type_id'));
    }

    if ($request->get('from')) {
        $query->whereDate('created_at', '>=', $request->get('from'));
    }

    if ($request->get('to')) {
        $query->whereDate('created_at', '<=', $request->get('to'));
    }

    if ($request->get('q')) {
        $q = $request->get('q');
        $query->where(function ($sub) use ($q) {
            $sub->where('id', 'like', "%$q%")
                ->orWhere('purpose', 'like', "%$q%");
        });
    }

    $loans = $query->orderBy('id', 'desc')->paginate(10);

    // ✅ Static loan/advance types
    $loanTypes = collect([
        (object)['id' => 1, 'name' => 'Salary Advance'],
        (object)['id' => 2, 'name' => 'Personal Loan'],
        (object)['id' => 3, 'name' => 'Emergency Loan'],
        (object)['id' => 4, 'name' => 'Festival Advance'],
    ]);

    // ✅ Load selected loan (if opened)
    $selectedLoan = null;
    if ($request->get('active')) {
        $selectedLoan = \App\SmAdvanceloan::find($request->get('active'));
    }

    // ✅ Render employee view (not HR folder)
    return view('backEnd.humanResource.loans.index', compact('loans', 'selectedLoan', 'loanTypes'));
}

   public function show($id)
{
    $loan = \App\SmAdvanceloan::find($id);
    if (!$loan) {
        return response('<p class="text-danger">Record not found.</p>');
    }

    // return partial view
    return view('backEnd.humanResource.loans._details', compact('loan'));
}

    public function create()
    {
        $loanTypes = collect($this->loanTypes);
        return view('backEnd.humanResource.loans.create', compact('loanTypes'));
    }
    

public function store(Request $request)
{
    $this->validate($request, [
        'type_id'         => 'required|integer',
        'amount'          => 'required|numeric|min:1',
        'installments'    => 'required|integer|min:1',
        'repayment_start' => 'required|date_format:Y-m', // accept YYYY-MM
        'repayment_mode'  => 'required|string|max:50',
        'purpose'         => 'required|string',
        'attachment'      => 'nullable|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
    ]);

    $staffId = optional(Auth::user()->staff)->id ?? Auth::user()->id;

    $amount       = (float) $request->input('amount');
    $installments = (int)   $request->input('installments');
    $amountPerMonth = $installments > 0 ? round($amount / $installments, 2) : null;

    // normalize YYYY-MM → YYYY-MM-01 for DATE column
    $repaymentStartDate = $request->input('repayment_start') . '-01';

    $loan = new \App\SmAdvanceloan();
    $loan->staff_id         = $staffId;
    $loan->type_id          = $request->input('type_id');
    $loan->amount           = $amount;
    $loan->installments     = $installments;
    $loan->amount_per_month = $amountPerMonth;
    $loan->repayment_start  = $repaymentStartDate;
    $loan->repayment_mode   = $request->input('repayment_mode');
    $loan->purpose          = $request->input('purpose');
    $loan->note             = $request->input('note');
    $loan->status           = 'Pending';
    $loan->date             = date('Y-m-d');

    if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $fileName = time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $file->move(public_path('uploads/loan_docs'), $fileName);
        $loan->attachment = $fileName;
    }

    $loan->save();

    return redirect()->route('employee.loans.index')
        ->with('success', 'Loan / Advance request submitted successfully.');
}

public function edit($id)
{
    $loan = \App\SmAdvanceloan::findOrFail($id);
    $loanTypes = collect($this->loanTypes);

    // Optional: prevent editing after approval
    if (in_array($loan->status, ['Approved', 'Rejected'])) {
        return redirect()->route('employee.loans.index')
            ->with('error', 'Approved or rejected loans cannot be edited.');
    }

    return view('backEnd.humanResource.loans.create', compact('loan', 'loanTypes'));
}

public function update(Request $request, $id)
{
    $this->validate($request, [
        'type_id'         => 'required|integer',
        'amount'          => 'required|numeric|min:1',
        'installments'    => 'required|integer|min:1',
        'repayment_start' => 'required|date_format:Y-m',
        'repayment_mode'  => 'required|string|max:50',
        'purpose'         => 'required|string',
        'attachment'      => 'nullable|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
    ]);

    $loan = \App\SmAdvanceloan::findOrFail($id);

    $amount       = (float) $request->input('amount');
    $installments = (int)   $request->input('installments');
    $amountPerMonth = $installments > 0 ? round($amount / $installments, 2) : null;

    $loan->type_id          = $request->input('type_id');
    $loan->amount           = $amount;
    $loan->installments     = $installments;
    $loan->amount_per_month = $amountPerMonth;
    $loan->repayment_start  = $request->input('repayment_start') . '-01';
    $loan->repayment_mode   = $request->input('repayment_mode');
    $loan->purpose          = $request->input('purpose');
    $loan->note             = $request->input('note');

    // Handle file upload (replace old one)
    if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $fileName = time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $file->move(public_path('uploads/loan_docs'), $fileName);

        // Delete old file
        if (!empty($loan->attachment) && file_exists(public_path('uploads/loan_docs/'.$loan->attachment))) {
            @unlink(public_path('uploads/loan_docs/'.$loan->attachment));
        }

        $loan->attachment = $fileName;
    }

    $loan->save();

    return redirect()->route('employee.loans.index')
        ->with('success', 'Loan / Advance request updated successfully.');
}

public function approvalsList()
{
    $auth = Auth::user();
    $query = \App\SmAdvanceloan::query();

    // ✅ If the logged-in user is a Reporting Manager
    if ($auth->role_id == 1) {
        // Find all staff whose reporting_manager field contains this user's ID
        $staffIds = \App\SmStaff::where(function ($q) use ($auth) {
            $q->where('reporting_manager', $auth->role_id)
              ->orWhereRaw("FIND_IN_SET(?, reporting_manager)", [$auth->role_id]);
        })->pluck('id');

        $query->whereIn('staff_id', $staffIds);
    }

    // ✅ If Finance Manager
    elseif ($auth->role_id == 2) {
        $query->where('manager_approval', 'Approved');
    }

    // ✅ If HR
    elseif ($auth->role_id == 3) {
        $query->where('finance_approval', 'Approved');
    }

    // Default order
    $loans = $query->orderBy('id', 'desc')->paginate(10);

    return view('backEnd.humanResource.loans.approvals', compact('loans'));
}



public function showDetail($id)
{
    $loan = \App\SmAdvanceloan::with('staffDetails')->findOrFail($id);
    return view('backEnd.humanResource.loans._approval_detail', compact('loan'));
}


public function approve(Request $request, $id)
{
    $auth = Auth::user();
    $loan = \App\SmAdvanceloan::findOrFail($id);

    $status = $request->input('status', 'Pending'); // Approved or Rejected

    // Manager-level approval
    if ($loan->manager_approval == 'Pending') {
        $loan->manager_approval = $status;
        $loan->approved_by = $auth->id;
    }
    // Finance-level approval
    elseif ($loan->manager_approval == 'Approved' && $loan->finance_approval == 'Pending') {
        $loan->finance_approval = $status;
        $loan->finance_approved_by = $auth->id;
    }
    // HR-level approval
    elseif ($loan->finance_approval == 'Approved' && $loan->hr_approval == 'Pending') {
        $loan->hr_approval = $status;
        $loan->hr_approved_by = $auth->id;
    }

    // ✅ If all three approved → final status Approved
    if ($loan->manager_approval == 'Approved'
        && $loan->finance_approval == 'Approved'
        && $loan->hr_approval == 'Approved') {
        $loan->status = 'Approved';
    }

    // ❌ If any rejected → final status Rejected
    if (in_array('Rejected', [$loan->manager_approval, $loan->finance_approval, $loan->hr_approval])) {
        $loan->status = 'Rejected';
    }

    $loan->save();

    return redirect()->back()->with('success', 'Approval updated successfully.');
}




}

