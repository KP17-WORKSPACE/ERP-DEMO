<?php

namespace App\Http\Controllers;

use App\SmItem;
use App\SmStaff;
use App\SmTender;
use App\SmSupplier;
use App\SmCompititor;
use App\SmItemReceive;
use App\SmExpiredTender;
use App\SmTenderProduct;
use Barryvdh\DomPDF\PDF;
use App\SmUpcomingTender;
use App\SmGeneralSettings;
use App\SmEnlistedSupplier;
use Illuminate\Http\Request;
use App\SmInspectingDepartment;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SmTenderController extends Controller
{
    public function index()
    {
        try{
            $tenders = SmTender::where('stage_status', '!=', 4)->get();
            $items = SmItemReceive::all();
            return view('backEnd.tender.tender', compact('tenders', 'items'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function tenderCompleted()
    {
        try{
            $tenders = SmTender::where('stage_status', 4)->get();
            $items = SmItemReceive::all();
            return view('backEnd.tender.tenderCompleted', compact('tenders', 'items'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function tenderInspection()
    {
        try{
            $tenders = SmTender::where('stage_status', 3)->get();
            $items = SmItemReceive::all();
            return view('backEnd.tender.tenderInspection', compact('tenders', 'items'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function tenderDelivered()
    {
        
        try{
            $tenders = SmTender::where('stage_status', 2)->get();
            $items = SmItemReceive::all();
            return view('backEnd.tender.tenderDelivered', compact('tenders', 'items'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function tenderShipment()
    {
        
        try{
            $tenders = SmTender::where('stage_status', 1)->get();
            $items = SmItemReceive::all();
            return view('backEnd.tender.tenderShipment', compact('tenders', 'items'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function status($id)
    {
        try{
            $singleData = $tender_status = SmTender::find($id);
            $tenders = SmTender::all();
            $items = SmItemReceive::all();
            return view('backEnd.tender.tender_status', compact('tenders', 'items', 'tender_status', 'singleData'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function workOrderUpdate(Request $request)
    {

        try{
            if ($request->status_mode == "Shipment") {
                $input_array = ['shipment_work_order_date', 'shipping_mode', 'shipping_tracking_number', 'shipping_carrier'];
                $stage_status = 1;
            } else if ($request->status_mode == "Delivered") {
                $input_array = ['status_delivery_date', 'status_cr', 'status_destination'];
                $stage_status = 2;
            } else if ($request->status_mode == "InspectionComplete") {
                $input_array = ['inspection_completion_date'];
                $stage_status = 3;
            } else if ($request->status_mode == "Completed") {
                $input_array = ['completion_date', 'cheque_no', 'bank_name', 'amount'];
                $stage_status = 4;
    
                $document_file_1 = "";
                if ($request->file('document_file_1') != "") {
                    $file = $request->file('document_file_1');
                    $document_file_1 = 'doc1-' . md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/student/document/', $document_file_1);
                    $document_file_1 = 'public/uploads/student/document/' . $document_file_1;
    
                }
    
                $document_file_2 = "";
                if ($request->file('document_file_2') != "") {
                    $file = $request->file('document_file_2');
                    $document_file_2 = 'doc2-' . md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/student/document/', $document_file_2);
                    $document_file_2 = 'public/uploads/student/document/' . $document_file_2;
    
                }
    
                $document_file_3 = "";
                if ($request->file('document_file_3') != "") {
                    $file = $request->file('document_file_3');
                    $document_file_3 = 'doc3-' . md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/student/document/', $document_file_3);
                    $document_file_3 = 'public/uploads/student/document/' . $document_file_3;
    
                }
    
                $tender = SmTender::find($request->tender_id);
                $tender->file1 = $document_file_1;
                $tender->file2 = $document_file_2;
                $tender->file3 = $document_file_3;
                $tender->save();
            }
    
            $tender = SmTender::find($request->tender_id);
            foreach ($input_array as $input) {
                if (isset($request->$input)) {
                    if ($input == "shipment_work_order_date" || $input == "status_delivery_date" || $input == "inspection_completion_date" || $input == "completion_date") {
                        $date = date('Y-m-d', strtotime($request->$input));
                        $tender->$input = $date;
                    } else {
                        $tender->$input = $request->$input;
                    }
                }
            }
            $tender->stage_status = $stage_status;
            $tender->save();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function create()
    {
        try{
            $customers = SmStaff::where('role_id', 2)->get();
            $vendors = SmSupplier::all();
            $items = SmItem::all();
            $departments = SmInspectingDepartment::all();
            return view('backEnd.tender.tenderCreate', compact('customers', 'vendors', 'items', 'departments'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function store(Request $request)
    {

        $input = $request->all();

        if ($request->work_order_mode == "equipment") {
            $validator = Validator::make($input, [
                'tender_title' => 'required',
                'tender_no' => 'required',
                'work_order_no' => 'required',
                'work_order_date' => 'required',
                'letter_no' => 'required',
                'delivery_date' => 'required',
                'customer' => 'required|not_in:0',
                'work_order_mode' => 'required',
            ]);

        } else {
            $validator = Validator::make($input, [
                'tender_title' => 'required',
                'tender_no' => 'required',
                'work_order_no' => 'required',
                'work_order_date' => 'required',
                'letter_no' => 'required',
                'delivery_date' => 'required',
                'customer' => 'required|not_in:0',
                'work_order_mode' => 'required',
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try{

        $tender = new SmTender();

        $tender->work_order_mode = $request->work_order_mode;
        $tender->tender_title = $request->tender_title;
        $tender->tender_no = $request->tender_no;
        $tender->work_order_no = $request->work_order_no;
        $tender->work_order_date = date('Y-m-d', strtotime($request->work_order_date));
        $tender->letter_no = $request->letter_no;
        $tender->delivery_date = date('Y-m-d', strtotime($request->delivery_date));
        $tender->customer_id = $request->customer;
        $tender->open_date = date('Y-m-d', strtotime($request->open_date));
      

        if ($request->work_order_mode == "equipment") {
            $tender->discount_amount = $request->Ediscount;
            $tender->discount_type = $request->Ediscount_type;
            $tender->bid_amount = $request->Ebid_amount;
        } else {
            $tender->discount_amount = $request->discount;
            $tender->discount_type = $request->discount_type;
            $tender->bid_amount = $request->bid_amount;

        }
        $tender->description = $request->description;
        $tender->note = $request->note;
        $tender->end_user_name = $request->end_user_name;
        $tender->department_id = $request->inspecting_departments;
        $tender->vendor_id = $request->vendors;
        $tender->created_by = Auth::user()->id;
        $tender->save();
        $tender->toArray();

        $data = SmTender::find($tender->id);
        $data['note'] = '"' . $data->tender_no . '" has been added.';
        $data['model_name'] = 'SmTender';
        $data['old_data'] = $data->toJson();
        $data['new_data'] = '';
        $data['action'] = 'Insert';
        $data['action_id'] = $data->id;
        $result = SmGeneralSettings::StoreAllActivities($data);

        $i = 0;

        if ($request->work_order_mode == "equipment") {

            foreach ($request->Eproducts as $product) {
                $tender_product = new SmTenderProduct();
                $tender_product->tender_id = $tender->id;
                $tender_product->product_id = $product;
                $tender_product->product_model = $request->Eproduct_model[$i];
                $tender_product->qnt = $request->Equantity[$i];
                $tender_product->unit_price = $request->Eunit_price[$i];
                $tender_product->save();

                $data = SmTenderProduct::find($tender_product->id);
                $data['note'] = '"Tender No' . $request->tender_no . ' & Product Id ' . $data->product_id . '" has been added.';
                $data['model_name'] = 'SmTenderProduct';
                $data['old_data'] = $data->toJson();
                $data['new_data'] = '';
                $data['action'] = 'Insert';
                $data['action_id'] = $data->id;
                $result = SmGeneralSettings::StoreAllActivities($data);

                $i++;
            }

        } else {
            foreach ($request->products as $product) {
                $tender_product = new SmTenderProduct();
                $tender_product->tender_id = $tender->id;
                $tender_product->product_id = $product;
                $tender_product->qnt = $request->quantity[$i];
                $tender_product->unit_price = $request->unit_price[$i];
                $result = $tender_product->save();

                $data = SmTenderProduct::find($tender_product->id);
                $data['note'] = '"Tender No' . $request->tender_no . ' & Product Id ' . $data->product_id . '" has been added.';
                $data['model_name'] = 'SmTenderProduct';
                $data['old_data'] = $data->toJson();
                $data['new_data'] = '';
                $data['action'] = 'Insert';
                $data['action_id'] = $data->id;
                $result = SmGeneralSettings::StoreAllActivities($data);

                $i++;
            }
        }
        DB::commit();
        Toastr::success('Operation successful', 'Success');
        return redirect('tender');
          } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try{
            $tender = SmTender::find($id);
            $customers = SmStaff::where('role_id', 2)->get();
            $vendors = SmSupplier::all();
            $items = SmItem::all();
            $departments = SmInspectingDepartment::all();
            return view('backEnd.tender.tenderEdit', compact('tender', 'customers', 'vendors', 'items', 'departments'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function update(Request $request)
    {


        DB::beginTransaction();
        try{

        $newData = $tender = SmTender::find($request->id);
        $tender->tender_title = $request->tender_title;
        $tender->tender_no = $request->tender_no;
        $tender->work_order_no = $request->work_order_no;
        $tender->work_order_date = date('Y-m-d', strtotime($request->work_order_date));
        $tender->letter_no = $request->letter_no;
        $tender->delivery_date = date('Y-m-d', strtotime($request->delivery_date));
        $tender->customer_id = $request->customer;
        $tender->open_date = date('Y-m-d', strtotime($request->open_date));
        


        if ($request->work_order_mode == "equipment") {
            $tender->discount_amount = $request->Ediscount;
            $tender->discount_type = $request->Ediscount_type;
            $tender->bid_amount = $request->Ebid_amount;
        } else {
            $tender->discount_amount = $request->discount;
            $tender->discount_type = $request->discount_type;
            $tender->bid_amount = $request->bid_amount;

        }

        $tender->description = $request->description;
        $tender->department_id = $request->inspecting_departments;
        $tender->vendor_id = $request->vendors;
        $tender->note = $request->note;
        $tender->end_user_name = $request->end_user_name;
        $r = $tender->save();
        // $newData= $tender->toArray();

// store user all activities
        $data = SmTender::find($tender->id);
        $data['note'] = '"Tender No' . $request->tender_no . '" has been updated.';
        $data['model_name'] = 'SmTender';
        $data['old_data'] = $data->toJson();
        $data['new_data'] = $newData->toJson();
        $data['action'] = 'Edit';
        $data['action_id'] = $data->id;
        $result = SmGeneralSettings::StoreAllActivities($data);
// end store user all activities

        SmTenderProduct::where('tender_id', $request->id)->delete();
        $i = 0;

        if ($request->work_order_mode != "equipment") {
            foreach ($request->products as $product) {
                $tender_product = new SmTenderProduct();
                $tender_product->tender_id = $tender->id;
                $tender_product->product_id = $product;
                $tender_product->qnt = $request->quantity[$i];
                $tender_product->unit_price = $request->unit_price[$i];
                $newData = $result = $tender_product->save();

                $i++;
            }
        }else{
            foreach ($request->Eproducts as $product) {
                $tender_product = new SmTenderProduct();
                $tender_product->tender_id = $tender->id;
                $tender_product->product_id = $product;
                $tender_product->product_model = $request->Eproduct_model[$i];
                $tender_product->qnt = $request->Equantity[$i];
                $tender_product->unit_price = $request->Eunit_price[$i];
                $tender_product->save();

                $i++;
            }
        }

        DB::commit();

        Toastr::success('Operation successful', 'Success');
        return redirect('tender');

          } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {

            SmTender::where('id', $id)->delete();

            SmTenderProduct::where('tender_id', $id)->delete();

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('tender');

        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }
    }

    public function tenderView($id)
    {
        
        try{
            $tender = SmTender::find($id);
            return view('backEnd.tender.tenderView', compact('tender'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    /* *********************************** Start Upcoming Tender *********************************** */
    public function UpcomingTender()
    {
        
        try{
            $upcoming_tenders = SmUpcomingTender::where('is_expired', 0)->OrderBy('open_date', 'ASC')->get();
            $upcoming_to_expired = SmUpcomingTender::where('open_date','<',date('Y-m-d', strtotime(date('Y-m-d'))))->where('is_expired',0)->OrderBy('open_date','ASC')->update(['is_expired'=>1]);
            $setting = SmGeneralSettings::find(1);
            $currency_symbol = empty($setting->currency_symbol) ? '$' : $setting->currency_symbol;
            $en_suppliers = SmEnlistedSupplier::where('active_status', 1)->orderBy('company_name', 'ASC')->get();
            $customers = SmStaff::where('role_id', 2)->get();
            return view('backEnd.tender.UpcomingTender', compact('upcoming_tenders', 'currency_symbol', 'customers', 'en_suppliers'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    // UpcomingTenderListPrintView
    public function UpcomingTenderListPrintView(Request $request)
    { 
        
        try{
            $upcoming_tenders = SmUpcomingTender::where('is_expired', 0)->OrderBy('open_date', 'ASC')->get();
            $setting = SmGeneralSettings::find(1);
    
             //return view('backEnd.tender.UpcomingTenderListPrintView', compact('upcoming_tenders','setting'));
            $pdf = PDF::loadView('backEnd.tender.UpcomingTenderListPrintView', [ 'upcoming_tenders' => $upcoming_tenders, 'setting' => $setting]);
              return $pdf->stream('Upcoming-Tender-List.pdf'); 
            // return $pdf->download('Upcoming Tender List.pdf');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function UpcomingTenderPrintView(Request $request, $id = null)
    {
       
        try{
            $compititorList = SmCompititor::where('tender_id', $id)->orderBy('company_bid_amount', 'ASC')->get();
            $upcoming_tender = SmUpcomingTender::find($id);
            $setting = SmGeneralSettings::find(1);
    
            // return view('backEnd.tender.UpcomingTenderPrintView', compact('compititorList','setting','upcoming_tender'));
            $pdf = PDF::loadView('backEnd.tender.UpcomingTenderPrintView', ['compititorList' => $compititorList, 'upcoming_tender' => $upcoming_tender, 'setting' => $setting]);
            return $pdf->download($upcoming_tender->title . '_competitor_list.pdf');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }

    }

    public function UpcomingTenderCreate()
    {
        
        try{
            $customers = SmStaff::where('role_id', 2)->get();
            $vendors = SmSupplier::all();
            return view('backEnd.tender.UpcomingTenderCreate', compact('customers', 'vendors'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function UpcomingTenderCancelled($id)
    {

        
        try{
            $expired_tenders = SmUpcomingTender::find($id);
            $expired_tenders->is_expired = 1;
            $result = $expired_tenders->save();
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect('expired-tenders');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back(); 
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function UpcomingTenderStore(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'customer' => 'required|not_in:0',
            'tender_no' => 'required',
            'open_date' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        
        try{
            $document_file_1 = "";
            if ($request->file('document_file_1') != "") {
                $file = $request->file('document_file_1');
                $document_file_1 = 'Upcoming_Tender_notice_' . date('d_M_Y_h:i:s') . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/tenders/', $document_file_1);
                $document_file_1 = 'public/uploads/tenders/' . $document_file_1;
    
            }
    
            $document_file_2 = "";
            if ($request->file('document_file_2') != "") {
                $file = $request->file('document_file_2');
                $document_file_2 = 'Upcoming_Tender_Specifications_' . date('d_M_Y_h:i:s') . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/tenders/', $document_file_2);
                $document_file_2 = 'public/uploads/tenders/' . $document_file_2;
    
            }
    
            $tender = new SmUpcomingTender();
            $tender->customer = $request->customer;
            $tender->tender_number = $request->tender_no;
            $tender->title = $request->title;
    
            $tender->notice = $document_file_1;
            $tender->specifications = $document_file_2;
    
            $tender->open_date = date('Y-m-d', strtotime($request->open_date));
            $result = $tender->save();
    
            if ($result) {
    
                SmUpcomingTender::where('open_date','<',date('Y-m-d', strtotime(date('Y-m-d'))))->where('is_expired',0)->OrderBy('open_date','ASC')->update(['is_expired'=>1]);
                
                Toastr::success('Operation successful', 'Success');
                return redirect('tender-upcoming');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
    
            return view('backEnd.tender.UpcomingTenderCreate');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function UpcomingTenderEdit($id)
    {
        try{
            $customers = SmStaff::where('role_id', 2)->get();
            $vendors = SmSupplier::all();
            $upcoming_tender = SmUpcomingTender::find($id);
            $upcoming_tenders = SmUpcomingTender::where('open_date', '>=', date('Y-m-d'))->OrderBy('open_date', 'ASC')->get();
            $setting = SmGeneralSettings::find(1);
            $currency_symbol = empty($setting->currency_symbol) ? '$' : $setting->currency_symbol;
            $en_suppliers = SmEnlistedSupplier::where('active_status', 1)->orderBy('company_name', 'ASC')->get();
            $customers = SmStaff::where('role_id', 2)->get();
            return view('backEnd.tender.UpcomingTenderEdit', compact('customers', 'vendors', 'upcoming_tender', 'upcoming_tenders', 'currency_symbol', 'en_suppliers'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function UpcomingTenderUpdate(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'customer' => 'required|not_in:0',
            'tender_no' => 'required',
            'open_date' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        DB::beginTransaction();
        try {

            $tender = SmUpcomingTender::find($request->id);
            $document_file_1 = "";
            if ($request->file('document_file_1') != "") {
                $file = $request->file('document_file_1');
                $document_file_1 = 'Upcoming_Tender_notice_' . date('d_M_Y_h:i:s') . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/tenders/', $document_file_1);
                $document_file_1 = 'public/uploads/tenders/' . $document_file_1;
                $tender->notice = $document_file_1;

            }

            $document_file_2 = "";
            if ($request->file('document_file_2') != "") {
                $file = $request->file('document_file_2');
                $document_file_2 = 'Upcoming_Tender_Specifications_' . date('d_M_Y_h:i:s') . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/tenders/', $document_file_2);
                $document_file_2 = 'public/uploads/tenders/' . $document_file_2;
                $tender->specifications = $document_file_2;

            }
            $tender->customer = $request->customer;
            $tender->tender_number = $request->tender_no;
            $tender->title = $request->title;
            $tender->open_date = date('Y-m-d', strtotime($request->open_date));
            $result = $tender->save();


            SmUpcomingTender::where('open_date','<',date('Y-m-d', strtotime(date('Y-m-d'))))->where('is_expired',0)->OrderBy('open_date','ASC')->update(['is_expired'=>1]);


            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('tender-upcoming');

        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function UpcomingTenderDelete($id)
    {
        DB::beginTransaction();
        try {
            SmUpcomingTender::where('id', $id)->delete();
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    /* *********************************** End Upcoming Tender *********************************** */


    /* *********************************** Start Expired Tender *********************************** */

    public function expiredTender()
    {
        try{
            $upcoming_to_expired = SmUpcomingTender::where('open_date','<',date('Y-m-d', strtotime(date('Y-m-d'))) )->where('is_expired',0)->OrderBy('open_date','ASC')->update(['is_expired'=>1]);
            $expired_tenders = SmUpcomingTender::where('is_expired', 1)->OrderBy('open_date', 'DESC')->get();
            $customers = SmStaff::where('role_id', 2)->get();
            $setting = SmGeneralSettings::find(1);
            $currency_symbol = empty($setting->currency_symbol) ? '$' : $setting->currency_symbol;
            $en_suppliers = SmEnlistedSupplier::where('active_status', 1)->orderBy('company_name', 'ASC')->get();
            return view('backEnd.tender.expiredTender', compact('expired_tenders', 'currency_symbol', 'customers', 'en_suppliers'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function winTender()
    {
        try{
            $win_tenders = SmUpcomingTender::where('is_winner', 1)->get();
            $customers = SmStaff::where('role_id', 2)->get();
            $setting = SmGeneralSettings::find(1);
            $currency_symbol = empty($setting->currency_symbol) ? '$' : $setting->currency_symbol;
            $en_suppliers = SmEnlistedSupplier::where('active_status', 1)->orderBy('company_name', 'ASC')->get();
            return view('backEnd.tender.winTender', compact('win_tenders', 'customers', 'en_suppliers', 'currency_symbol', 'setting'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function expiredTenderCreate()
    {
        try{
            $expaired_tender = SmUpcomingTender::where('open_date', '<', date('Y-m-d'))->get();
            $customers = SmStaff::where('role_id', 2)->get();
            $vendors = SmSupplier::all();
            return view('backEnd.tender.expiredTenderCreate', compact('customers', 'vendors', 'expaired_tender'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function ets(Request $request)
    {
        try{
            dd($request->input());
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function expiredTenderStore(Request $request)
    {
        try{
            $tender = SmUpcomingTender::find($request->id);
            $tender->customer = $request->customer;
            $tender->tender_number = $request->tender_number;
            $tender->tender_result = $request->tender_result;
            $tender->open_date = date('Y-m-d', strtotime($request->open_date));
            if (isset($request->is_winner)) {
                $tender->is_winner = 1;
            } else {
                $tender->is_winner = 0;
            }
            $result = $tender->save();
            $tender_id = $tender->id;
            SmCompititor::where('tender_id', $request->id)->delete();
            if (count($request->suppliers) > 0) {
                $companyList = $request->suppliers;
                $bidAmountList = $request->bid_amount;
                $remarkList = $request->remarks;
    
                for ($i = 0; $i < count($request->suppliers); $i++) {
    
                    $company_info = SmEnlistedSupplier::find($companyList[$i]);
    
                    $compititor = new SmCompititor();
                    $compititor->tender_id = $tender_id;
                    $compititor->company_name = !empty($company_info->company_name) ? $company_info->company_name : '';
                    $compititor->company_id = $companyList[$i];
                    $compititor->company_bid_amount = $bidAmountList[$i];
                    $compititor->remark = $remarkList[$i];
                    $compititor->save();
                }
            }
    
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
    
            return view('backEnd.tender.expiredTenderCreate');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function tenderLowestStore(Request $request)
    {
        try{
            foreach ($request->competitors as $competitor) {
                $compititor
                    = SmCompititor::find($competitor);
                if (isset($request->lowest_bid) && $competitor == $request->lowest_bid) {
                    $compititor->lowest_bid = 1;
                } else {
                    $compititor->lowest_bid = 0;
                }
                $result = $compititor->save();
            }
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function expiredTenderEdit($id)
    {
        
        try{
            $customers = SmStaff::where('role_id', 2)->get();
            $vendors = SmSupplier::all();
            $expired_tender = SmExpiredTender::find($id);
            return view('backEnd.tender.expiredTenderEdit', compact('customers', 'vendors', 'expired_tender'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function expiredTenderUpdate(Request $request)
    {
        DB::beginTransaction();
        try {
            $tender = SmExpiredTender::find($request->id);
            $tender->customer = $request->customer;
            $tender->tender_number = $request->tender_no;
            $tender->tender_result = $request->tender_result;
            $tender->open_date = date('Y-m-d', strtotime($request->open_date));
            $result = $tender->save();
            $tender_id = $tender->id;
            SmCompititor::where('tender_id', $request->id)->delete();
            if (count($request->company_name) > 0) {
                $companyList = $request->company_name;
                $bidAmountList = $request->bid_amount;
                $compititor_id = '';
                for ($i = 0; $i < count($request->company_name); $i++) {
                    $compititor = new SmCompititor();
                    $compititor->tender_id = $tender_id;
                    $compititor->company_name = $companyList[$i];
                    $compititor->company_bid_amount = $bidAmountList[$i];
                    $compititor->save();
                }
            }
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('tender-expired');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function expiredTenderDelete($id)
    {
        DB::beginTransaction();
        try {
            SmExpiredTender::where('id', $id)->delete();
            SmExpiredTender::where('tender_id', $id)->delete();
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('tender-upcoming');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function tenderWorkOrderStatus(Request $request){


        if($request->status == 'on'){
            $status = 1;
        }else{
            $status = 0;
        }

        $tender = SmUpcomingTender::find($request->id);
        $tender->work_order_status = $status;
        $tender->save();

        

        return response()->json($request->id);
    }

    /* *********************************** End Upcoming Tender *********************************** */

}
