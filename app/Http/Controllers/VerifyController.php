<?php

namespace App\Http\Controllers;

use HP;
use App\Envato\Envato;
use App\SmGeneralSettings;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class VerifyController extends Controller
{
	/**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        try{
            $o = Envato::verifyPurchase(HP::set()->purchasecode);
            //dd($o);
            //isset($o['item']['id']) && $o['item']['id'] == "21551447 21834231"
            if(isset($o['item']) && $o['item']['id'] == "22885977" && $o['buyer'] == HP::set()->envatouser){
                return redirect('/');
            }else{
                return view('verifycode');
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storePurchasecode(Request $request, $id)
    {
        try{
            $settings                       =   SmGeneralSettings::find($id);
            $settings->envato_user          =   $request->envatouser;
            $settings->system_purchase_code =   $request->purchasecode;
            $settings->save();
            Toastr::success('Purchase Code Successfully Inserted...', 'Success');
                return redirect('/dashboard/');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

}
