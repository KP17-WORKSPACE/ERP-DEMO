<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SysPaymentTerms;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SysPaymentTermsController extends Controller
{

    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request)
    {
        try {
            $paymentterms = SysPaymentTerms::where('active_status', 1)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($paymentterms, null);
            }
            return view('backEnd.humanResource.payment_terms', compact('paymentterms'));
        } catch (\Exception $e) {
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

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $paymentterms = new SysPaymentTerms();
            $paymentterms->title = $request->title;
            $paymentterms->created_by = Auth::user()->id;
            $result = $paymentterms->save();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
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
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $editmode = SysPaymentTerms::find($id);
            $paymentterms = SysPaymentTerms::all();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['paymentterms'] = $paymentterms->toArray();
                $data['editmode'] = $editmode->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.humanResource.payment_terms', compact('paymentterms', 'editmode'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => "required"
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
            $paymentterms = SysPaymentTerms::find($request->id);
            $paymentterms->title = $request->title;
            $paymentterms->updated_by = Auth::user()->id;
            $result = $paymentterms->save();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Designation has been updated successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } else {
                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('payment-terms');
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

    public function destroy(Request $request, $id)
    {
        try {
            $paymentterms = SysPaymentTerms::destroy($id);
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($paymentterms) {
                    return ApiBaseMethod::sendResponse(null, 'Payment Terms has been deleted successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } else {
                if ($paymentterms) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('payment-terms');
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


    public function paymentTermsStoreAjax(Request $request)
    {
       
        try {
            $paymentterms = new SysPaymentTerms();
            $paymentterms->title = $request->title;
            $paymentterms->created_by = Auth::id();
            $paymentterms->save();

            return response()->json([
                'status' => true,
                'message' => 'Payment term created successfully',
                'data' => [
                    'id' => $paymentterms->id,
                    'title' => $paymentterms->title
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

}
