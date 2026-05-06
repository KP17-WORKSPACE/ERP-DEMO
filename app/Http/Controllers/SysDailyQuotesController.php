<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SysDailyQuotes;
use App\SysHelper;
use App\SysPaymentTerms;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SysDailyQuotesController extends Controller
{

    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request)
    {
        try{
            $dailyquotes = SysDailyQuotes::orderby('date','desc')->get();
            return view('backEnd.humanResource.daily_quotes', compact('dailyquotes'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => "required"
        ]);
        
        try{
            $dailyquote = new SysDailyQuotes();
            $dailyquote->date = SysHelper::normalizeToYmd($request->date);
            $dailyquote->quote = $request->quote;
            $dailyquote->created_by = Auth::user()->id;
            $result = $dailyquote->save();
            if($result){
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function show(Request $request, $id)
    {
        try{
            $editmode = SysDailyQuotes::find($id);
            $dailyquotes = SysDailyQuotes::all();
            return view('backEnd.humanResource.daily_quotes', compact('dailyquotes', 'editmode'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        try{
            $dailyquote = SysDailyQuotes::find($request->id);
            $dailyquote->date = SysHelper::normalizeToYmd($request->date);
            $dailyquote->quote = $request->quote;
            $dailyquote->updated_by = Auth::user()->id;
            $result = $dailyquote->save();
            if($result){
                Toastr::success('Operation successful', 'Success');
                return redirect('daily-quotes');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function destroy(Request $request,$id)
    {
        try{
            $dailyquotes = SysDailyQuotes::destroy($id);
            if($dailyquotes){
                Toastr::success('Operation successful', 'Success');
                return redirect('daily-quotes');
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
