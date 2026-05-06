<?php

namespace App\Http\Controllers;

use App\SmClass;
use App\SmRoute;
use App\SmStudent;
use App\SmVehicle;
use App\ApiBaseMethod;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class SmTransportController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }
    
    public function studentTransportReport(Request $request){
    	
        try{
            $classes = SmClass::where('active_status', 1)->get();
            $routes = SmRoute::where('active_status', 1)->get();
            $vehicles = SmVehicle::where('active_status', 1)->get();
            $students = SmStudent::where('active_status', 1)->where('vechile_id', '!=', "")->get();
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['routes'] = $routes->toArray();
                $data['vehicles'] = $vehicles->toArray();
                $data['students'] = $students->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.transport.student_transport_report', compact('classes', 'routes', 'vehicles', 'students'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function studentTransportReportSearch(Request $request){

        try{
            $students = SmStudent::query();
            $students->where('active_status', 1);
            if($request->class != ""){
                $students->where('class_id', $request->class);
            }
             if($request->section != ""){
                $students->where('section_id', $request->section);
            }
            if($request->route != ""){
                $students->where('route_list_id', $request->route);
            }else{
                $students->where('route_list_id', '!=', '');
            }
            if($request->vehicle != ""){
                $students->where('vechile_id', $request->vehicle);
            }else{
                $students->where('vechile_id', '!=', '');
            }
            $students = $students->get();
    
            $classes = SmClass::where('active_status', 1)->get();
            $classes = SmClass::where('active_status', 1)->get();
            $routes = SmRoute::where('active_status', 1)->get();
            $vehicles = SmVehicle::where('active_status', 1)->get();
            $class_id = $request->class;
            $route_id = $request->route;
            $vechile_id = $request->vehicle;
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['routes'] = $routes->toArray();
                $data['vehicles'] = $vehicles->toArray();
                $data['students'] = $students->toArray();
                $data['class_id'] = $class_id;
                $data['route_id'] = $route_id;
                $data['vechile_id'] = $vechile_id;
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.transport.student_transport_report', compact('classes', 'routes', 'vehicles', 'students', 'class_id', 'route_id', 'vechile_id'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}
