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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SysAccountGroupSub2Controller extends Controller
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

    public function accountgroupsub2Add(Request $request)
    {
        try {
            $accountgroup = SysAccountGroup::where('status', 1)->get();
            $accountgroupsub = SysAccountGroupSub::where('status', 1)->get();
           $accountgroupsub2 = SysAccountGroupSub2::where('status', 1)->orderBy('group_id', 'asc')->get();


            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($accountgroup, null);
            }
            return view('backEnd.accounts.accountgroupsub2add', compact('accountgroup', 'accountgroupsub', 'accountgroupsub2'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function get_sub_group(Request $request)
    {
        $select_sub_group = SysAccountGroupSub::select('id', 'title')->where('group_id', $request->group_id)->get();
        return response()->json([$select_sub_group]);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        // $validator = Validator::make($input, [
        //     'group_id'=> "required",
        //     'sub_id'=> "required",
        //     'title'=> "required",
        // ]);

        // if ($validator->fails()) {
        //     if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //         return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
        //     }
        //     return redirect()->back()
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        try {
            $accountgroup2 = new SysAccountGroupSub2();

            $groupid = SysAccountGroupSub::select('group_id')->where('id', $request->sub_id)->first();
            $accountgroup2->group_id = $groupid->group_id;

            $accountgroup2->sub_id = $request->sub_id;
            $accountgroup2->title = $request->title;
            $accountgroup2->status = 1;
            $accountgroup2->created_by = Auth::user()->id;

            $results = $accountgroup2->save();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results) {
                    return ApiBaseMethod::sendResponse(null, 'Account Sub Group 2 has been added successfully');
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
            $accountgroup2 = new SysAccountGroupSub2();

            $groupid = SysAccountGroupSub::select('group_id')->where('id', $request->sub_id)->first();
            $accountgroup2->group_id = $groupid->group_id;

            $accountgroup2->sub_id = $request->sub_id;
            $accountgroup2->title = $request->title;
            $accountgroup2->status = 1;
            $accountgroup2->created_by = Auth::user()->id;
            $results = $accountgroup2->save();

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
            $accountgroupsub2 = SysAccountGroupSub2::where('status', 1)->get();
            $editData = SysAccountGroupSub2::find($id);
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['editData'] = $accountgroup->toArray();
                $data['editData'] = $editData->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.accounts.accountgroupsub2add', compact('editData', 'accountgroup', 'accountgroupsub', 'accountgroupsub2'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getEdit(Request $request, $id)
    {
        try {
            // $accountgroup = SysAccountGroup::where('status', 1)->get();
            // $accountgroupsub = SysAccountGroupSub::where('status', 1)->get();
            // $accountgroupsub2 = SysAccountGroupSub2::where('status', 1)->get();
            $editData = SysAccountGroupSub2::find($id);

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
            // if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            //     $data = [];
            //     $data['editData'] = $accountgroup->toArray();
            //     $data['editData'] = $editData->toArray();
            //     return ApiBaseMethod::sendResponse($data, null);
            // }
            // return view('backEnd.accounts.accountgroupsub2add', compact('editData', 'accountgroup', 'accountgroupsub', 'accountgroupsub2'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        // $input = $request->all();
        // $validator = Validator::make($input, [
        //     'group_id'=> "required",
        //     'sub_id'=> "required",
        //     'title'=> "required",
        // ]);

        // if ($validator->fails()) {
        //     if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //         return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
        //     }
        //     return redirect()->back()
        //         ->withErrors($validator)
        //         ->withInput();
        // }
        try {
          
            $accountgroup2 = SysAccountGroupSub2::find($id);

            $groupid = SysAccountGroupSub::select('group_id')->where('id', $request->sub_id)->first();
            $accountgroup2->group_id = $groupid->group_id;

            $accountgroup2->sub_id = $request->sub_id;
            $accountgroup2->title = $request->title;
            $accountgroup2->status = 1;
            $accountgroup2->updated_by = Auth()->user()->id;
            $results = $accountgroup2->update();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results) {
                    return ApiBaseMethod::sendResponse(null, 'Account Sub Group 2 has been updated successfully');
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
            $chartofaccounts = SysChartofAccounts::where('subgroup2', $id)->get();

            if (count($chartofaccounts) > 0) {
                SysAccountGroupSub2::where('id', $id)->update(['status' => 0]);
                Toastr::success('Deleted successful', 'Success');
                return redirect()->back();
            }

            SysAccountGroupSub2::where('id', $id)->delete();


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