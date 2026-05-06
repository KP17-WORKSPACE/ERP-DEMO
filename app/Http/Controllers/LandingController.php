<?php

namespace App\Http\Controllers;

class LandingController extends Controller
{
    
    public function index(){
        try {
          return view('frontEnd.landing.index');            
        } catch (\Exception $e) {
            return redirect()->back()->with('error',$e);
        }
    }

}
