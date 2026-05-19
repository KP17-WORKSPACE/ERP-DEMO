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
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'percentages' => 'required|array|min:1',
            'percentages.*' => 'required|numeric|min:0.01|max:100',
            'days' => 'required|array|min:1',
            'days.*' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->validationFailedResponse($request, $validator);
        }

        $scheduleResult = $this->buildPaymentSchedule($request);
        if (isset($scheduleResult['error'])) {
            return redirect()->back()->withErrors(['schedule' => $scheduleResult['error']])->withInput();
        }

        try {
            $paymentterms = new SysPaymentTerms();
            $paymentterms->title = $request->title;
            $paymentterms->payment_schedule = $scheduleResult['schedule'];
            $paymentterms->created_by = Auth::user()->id;
            $result = $paymentterms->save();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse($paymentterms, 'Payment term created successfully');
                }
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }

            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect('payment-terms');
            }

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function show(Request $request, $id)
    {
        return redirect('payment-terms');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'percentages' => 'required|array|min:1',
            'percentages.*' => 'required|numeric|min:0.01|max:100',
            'days' => 'required|array|min:1',
            'days.*' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->validationFailedResponse($request, $validator);
        }

        $scheduleResult = $this->buildPaymentSchedule($request);
        if (isset($scheduleResult['error'])) {
            return redirect()->back()->withErrors(['schedule' => $scheduleResult['error']])->withInput();
        }

        try {
            $paymentterms = SysPaymentTerms::find($request->id ?: $id);
            if (!$paymentterms) {
                Toastr::error('Payment term not found', 'Failed');
                return redirect('payment-terms');
            }

            $paymentterms->title = $request->title;
            $paymentterms->payment_schedule = $scheduleResult['schedule'];
            $paymentterms->updated_by = Auth::user()->id;
            $result = $paymentterms->save();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Payment term has been updated successfully');
                }
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }

            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect('payment-terms');
            }

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
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
                }
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }

            if ($paymentterms) {
                Toastr::success('Operation successful', 'Success');
                return redirect('payment-terms');
            }

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function paymentTermsStoreAjax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

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
                    'title' => $paymentterms->title,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    private function buildPaymentSchedule(Request $request)
    {
        $percentages = $request->input('percentages', []);
        $days = $request->input('days', []);

        if (count($percentages) !== count($days)) {
            return ['error' => 'Invalid payment schedule rows.'];
        }

        $schedule = [];
        $total = 0;

        foreach ($percentages as $index => $percentage) {
            $pct = round((float) $percentage, 2);
            $day = (int) $days[$index];

            if ($pct <= 0 || $pct > 100) {
                return ['error' => 'Each percentage must be between 0.01 and 100.'];
            }

            if ($day < 0) {
                return ['error' => 'Days must be zero or greater.'];
            }

            $schedule[] = [
                'percentage' => $pct,
                'days' => $day,
            ];
            $total += $pct;
        }

        $total = round($total, 2);
        if ($total !== 100.0) {
            return ['error' => 'Total percentage must be exactly 100%. Current total: ' . $total . '%'];
        }

        return [
            'schedule' => $schedule,
        ];
    }

    private function validationFailedResponse(Request $request, $validator)
    {
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
        }

        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }
}