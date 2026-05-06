<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\ApiBaseMethod;
use App\SmSupplier;
use App\SysAccountGroup;
use App\SysAccountGroupSub;
use App\SysAccountGroupSub2;
use App\SysAccountType;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SysAccountGroupSubController extends Controller
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

    public function accountgroupsubAdd(Request $request)
    {
        try {
            $accountgroup = SysAccountGroup::where('status', 1)->get();
            $accountgroupsub = SysAccountGroupSub::where('status', 1)->orderBy('group_id')->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($accountgroup, null);
            }
            return view('backEnd.accounts.accountgroupsubadd', compact('accountgroup', 'accountgroupsub'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


   



    public function store(Request $request)
    {
        // return $request;
        $input = $request->all();
        $validator = Validator::make($input, [
            'group_id' => "required",
            'title' => "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        try {
            $accountgroup = new SysAccountGroupSub();
            $accountgroup->group_id = $request->group_id;
            $accountgroup->title = $request->title;
            $accountgroup->status = 1;
            $accountgroup->created_by = Auth::user()->id;

            $results = $accountgroup->save();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results) {
                    return ApiBaseMethod::sendResponse(null, 'Account Sub Group has been added successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($results) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function store2(Request $request)
    {

        try {
            $accountgroup = new SysAccountGroupSub();
            $accountgroup->group_id = $request->group_id;
            $accountgroup->title = $request->title;
            $accountgroup->status = 1;
            $accountgroup->created_by = Auth::user()->id;
            $results = $accountgroup->save();

            $ret = SysAccountGroup::all();
            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $accountgroup = SysAccountGroup::where('status', 1)->get();
            $accountgroupsub = SysAccountGroupSub::where('status', 1)->get();
            $editData = SysAccountGroupSub::find($id);
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['editData'] = $accountgroup->toArray();
                $data['editData'] = $editData->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.accounts.accountgroupsubadd', compact('editData', 'accountgroup', 'accountgroupsub'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getEdit(Request $request, $id)
    {
        try {

            $editData = SysAccountGroupSub::find($id);

            if (!$editData) {
                return response()->json([
                    'error' => true,
                    'message' => 'Data not found'
                ], 404);
            }

            return response()->json([
                'error' => false,
                'editData' => $editData
            ]);

            // return view('backEnd.accounts.accountgroupsubadd', compact('editData', 'accountgroup', 'accountgroupsub'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function update(Request $request, $id)
    {
        $input = $request->all();
       
        $validator = Validator::make($input, [
            'group_id' => "required",
            'title' => "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            $accountgroup = SysAccountGroupSub::find($id);
            $accountgroup->group_id = $request->group_id;
            $accountgroup->title = $request->title;
            $accountgroup->status = 1;
            $accountgroup->updated_by = Auth()->user()->id;
            $results = $accountgroup->update();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results) {
                    return ApiBaseMethod::sendResponse(null, 'Account Sub Group has been updated successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($results) {
                    
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();

                } else {
                 
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        } catch (\Exception $e) {
       
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function delete($id)
    {
        try {
            $accountgroupsub2 = SysAccountGroupSub2::where('sub_id', $id)->get();
            $chartofaccounts = SysChartofAccounts::where('subgroup', $id)->get();

            if (count($accountgroupsub2) > 0) {
                Toastr::error('This Group Has Sub Group', 'Failed');
                return redirect()->back();
            }
            if (count($chartofaccounts) > 0) {
                SysAccountGroupSub::where('id', $id)->update(['status' => 0]);
                Toastr::success('Deleted successful', 'Success');
                return redirect()->back();
            }

            SysAccountGroupSub::where('id', $id)->delete();


            Toastr::success('Deleted successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    // public function deleteSupplier(Request $request,$id){

    //     try{
    //         $result = SmSupplier::destroy($id);

    //         if (ApiBaseMethod::checkUrl($request->fullUrl())) {
    //             if ($result) {
    //                 return ApiBaseMethod::sendResponse(null, 'Supplier has been deleted successfully');
    //             } else {
    //                 return ApiBaseMethod::sendError('Something went wrong, please try again.');
    //             }
    //         } else {
    //             if ($result) {
    //                 Toastr::success('Operation successful', 'Success');
    //                 return redirect('suppliers');
    //             } else {
    //                 Toastr::error('Operation Failed', 'Failed');
    //                 return redirect()->back();
    //             }
    //         }
    //     }catch (\Exception $e) {
    //        Toastr::error('Operation Failed', 'Failed');
    //        return redirect()->back(); 
    //     }
    // }
}