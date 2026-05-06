<?php

namespace App\Http\Controllers;

use App\SmExpenseHead;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class SmExpenseHeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $expense_heads = SmExpenseHead::all();
            return view('backEnd.accounts.expense_head', compact('expense_heads'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => "required|unique:sm_expense_heads,name",
        ]);

        try{
            $expense_head = new SmExpenseHead();
            $expense_head->name = $request->name;
            $expense_head->description = $request->description;
            $result = $expense_head->save();
            if($result){
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back(); 
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
 
    public function show($id)
    {
        try{
            $expense_head = SmExpenseHead::find($id);
            $expense_heads = SmExpenseHead::all();
            return view('backEnd.accounts.expense_head', compact('expense_heads', 'expense_head'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => "required|unique:sm_expense_heads,name,".$request->id,
        ]);
        
        try{
            $expense_head = SmExpenseHead::find($request->id);
            $expense_head->name = $request->name;
            $expense_head->description = $request->description;
            $result = $expense_head->save();
            if($result){
                Toastr::success('Operation successful', 'Success');
                return redirect('expense-head');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function destroy($id)
    {
        
        try{
            $expense_head = SmExpenseHead::destroy($id);
            if($expense_head){
                Toastr::success('Operation successful', 'Success');
                return redirect('expense-head');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}
