<?php

namespace Modules\Currency\Http\Controllers;

use App\SmCountry;
use App\SmSession;
use App\SmCurrency;
use App\SmLanguage;
use App\SmDateFormat;
use App\SmAcademicYear;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class CurrencyController extends Controller
{
     // manage currency
     public function manageCurrency()
     { 
         try{
            $session_ids = SmSession::where('active_status', 1)->where('school_id', '=', Auth::user()->school_id)->get();
            $currencies = SmCurrency::all();
            $dateFormats = SmDateFormat::where('active_status', 1)->get();
            $languages = SmLanguage::all();
            $countries = SmCountry::select('currency')->groupBy('currency')->get();
            $academic_years = SmAcademicYear::where('school_id', '=', Auth::user()->school_id)->get();
            return view('currency::manageCurrency', compact( 'session_ids', 'dateFormats', 'languages', 'countries', 'currencies', 'academic_years'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
     }
 
     public function storeCurrency(Request $request)
     {
         $request->validate([
             'name' => 'required | max:25',
             'code' => 'required | max:15',
             'symbol' => 'required | max:5', 
         ]);
        
 
         try {
             $s = new SmCurrency();
             $s->name = $request->name;
             $s->code = $request->code;
             $s->symbol = $request->symbol;
             $s->school_id = Auth::user()->school_id;
             $s->save();

             $currencies = SmCurrency::all();    

             Toastr::success('Operation successful', 'Success');
             return view('currency::manageCurrency', compact( 'currencies'));
 
         } catch (\Exception $e) {
 
            Toastr::error('Operation Failed', 'Failed');
             return redirect()->back();
         } 
         
 
     }
 
     public function storeCurrencyUpdate(Request $request)
     {
         $request->validate([
             'name' => 'required | max:25',
             'code' => 'required | max:15',
             'symbol' => 'required | max:5', 
         ]);
        
 
         try {
             $s =SmCurrency::findOrfail($request->id);
             $s->name = $request->name;
             $s->code = $request->code;
             $s->symbol = $request->symbol;
             $s->school_id = Auth::user()->school_id;
             $s->update();
             $currencies = SmCurrency::all();  


             Toastr::success('Operation successful', 'Success');
             return view('currency::manageCurrency', compact( 'currencies'));
 
         } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
         } 
         
 
     }
     public function manageCurrencyEdit($id)
     {
         
         try {
             $editData = SmCurrency::findOrfail($id);
             $currencies = SmCurrency::all();   
             
             return view('currency::manageCurrency', compact('editData','currencies'));
 
         } catch (\Exception $e) {
 
            Toastr::error('Operation Failed', 'Failed');
             return redirect()->back();
         } 
         
 
     }
     public function manageCurrencyDelete($id){
         try {
             $currency = SmCurrency::findOrfail($id);
               $currency->delete();
               Toastr::success('Operation successful', 'Success');
               return redirect('currency/manage-currency');
         } catch (\Exception $e) {
 
             Toastr::error('Operation Failed', 'Failed');
             return redirect()->back();
         } 
     }
}
