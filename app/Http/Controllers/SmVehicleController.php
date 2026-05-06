<?php

namespace App\Http\Controllers;

use App\SmStaff;
use App\SmVehicle;
use App\ApiBaseMethod;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;

class SmVehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        try{
            $drivers = SmStaff::where([['active_status', 1], ['role_id', 9]])->get();
            $assign_vehicles = SmVehicle::all();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['drivers'] = $drivers->toArray();
                $data['assign_vehicles'] = $assign_vehicles->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.transport.vehicle', compact('assign_vehicles', 'drivers'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'vehicle_number' => "required|unique:sm_vehicles,vehicle_no",
            'vehicle_model' => "required",
            'driver_id' => "required"
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try{
            $assign_vehicle = new SmVehicle();
            $assign_vehicle->vehicle_no = $request->vehicle_number;
            $assign_vehicle->vehicle_model = $request->vehicle_model;
            $assign_vehicle->made_year = $request->year_made;
            $assign_vehicle->driver_id = $request->driver_id;
            // $assign_vehicle->driver_license = $request->driver_license;
            // $assign_vehicle->driver_contact = $request->driver_contact;
            $assign_vehicle->note = $request->note;
            $result = $assign_vehicle->save();
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Vehicle has been created successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back(); 
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try{
            //$drivers = SmStaff::where('active_status', 1)->get();
            $drivers = SmStaff::where([['active_status', 1], ['role_id', 9]])->get();
            $assign_vehicle = SmVehicle::find($id);
            $assign_vehicles = SmVehicle::all();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['route'] = $drivers->toArray();
                $data['routes'] = $assign_vehicle;
                $data['routes'] = $assign_vehicles->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.transport.vehicle', compact('assign_vehicle', 'assign_vehicles', 'drivers'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function update(Request $request, $id)
    {
        $input = $request->all();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $validator = Validator::make($input, [
                'vehicle_number' => 'required|unique:sm_vehicles,vehicle_no,' . $id,
                'vehicle_model' => "required",
                'id' => 'required'
            ]);
        } else {
            $validator = Validator::make($input, [
                'vehicle_number' => 'required|unique:sm_vehicles,vehicle_no,' . $id,
                'vehicle_model' => "required"
            ]);
        }
        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try{
            $assign_vehicle = SmVehicle::find($request->id);
            $assign_vehicle->vehicle_no = $request->vehicle_number;
            $assign_vehicle->vehicle_model = $request->vehicle_model;
            $assign_vehicle->made_year = $request->year_made;
            $assign_vehicle->driver_id = $request->driver_id;
            // $assign_vehicle->driver_license = $request->driver_license;
            // $assign_vehicle->driver_contact = $request->driver_contact;
            $assign_vehicle->note = $request->note;
            $result = $assign_vehicle->save();
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Vehicle has been updated successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('vehicle');
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        
        try{
            $assign_vehicle = SmVehicle::destroy($id);
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($assign_vehicle) {
                    return ApiBaseMethod::sendResponse(null, 'Item Category has been deleted successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } else {
                if ($assign_vehicle) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('vehicle');
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back(); 
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}
