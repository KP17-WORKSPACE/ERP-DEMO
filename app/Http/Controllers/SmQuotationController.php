<?php

namespace App\Http\Controllers;

use App\SmItem;
use App\SmStaff;
use App\SmSupplier;
use App\SmQuotation;
use App\SmGeneralSettings;
use App\SmQuotationProducts;
use Illuminate\Http\Request;
use App\SmInspectingDepartment;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SmQuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        try{
            $quotations = SmQuotation::all();
            return view('backEnd/quotations/quotations_list', compact('quotations'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try{
            $customers  = SmStaff::where('role_id', 2)->get();
            $vendors    = SmSupplier::all();
            $items      = SmItem::all();
            $quotations = SmQuotation::all();
            $departments = SmInspectingDepartment::all();
            return view('backEnd/quotations/manage_quotations', compact('quotations', 'customers', 'vendors', 'items', 'departments'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function store(Request $request)
    {

        $request->validate([
            'title'         => 'required',
            'number'        => 'required',
            'date'          => 'required',
            // 'vendors'       => 'required|not_in:0',
            'customer'      => 'required',
        ]);


        DB::beginTransaction();
        try {
            $quotation = new SmQuotation();
            $quotation->quotation_type      = $request->quotation_type;
            $quotation->title               = $request->title;
            $quotation->number              = $request->number;
            $quotation->date                = date('Y-m-d', strtotime($request->date));
            $quotation->reference           = $request->reference;
            //customer_details
            $quotation->customer_id         = $request->customer;
            $customer_details               = SmStaff::find($request->customer);
            $quotation->customer_name       = $customer_details->full_name;
            //vendor_details
            $quotation->vendor_id           = $request->vendors;
            $vendor_details                 = SmSupplier::find($request->vendors);
            $quotation->vendor_name         = $vendor_details->company_name;
            //footer note 
            $quotation->private_note        = $request->private_note;
            $quotation->public_note         = $request->public_note;
            $quotation->terms_note          = $request->terms_note;
            $quotation->footer_note         = $request->footer_note;
            $quotation->signature_person    = $request->signature_person;
            $quotation->signature_company   = $request->signature_company;

            if ($request->quotation_type == "equipment") {
                $quotation->discount_amount    = $request->Ediscount;
                $quotation->discount_type      = $request->Ediscount_type;
                $quotation->amount             = $request->Ebid_amount;
            } else {
                $quotation->discount_amount    = $request->discount;
                $quotation->discount_type      = $request->discount_type;
                $quotation->amount             = $request->bid_amount;
            }
            $quotation->created_by    = Auth::user()->id;
            $quotation->save();
            $quotation->toArray();

            $data               = SmQuotation::find($quotation->id);
            $data['note']       = '"' . $data->title . '" has been added.';
            $data['model_name'] = 'SmQuotation';
            $data['old_data']   = $data->toJson();
            $data['new_data']   = '';
            $data['action']     = 'Insert';
            $data['action_id']  = $data->id;
            $result             = SmGeneralSettings::StoreAllActivities($data);

            $i = 0;

            if ($request->quotation_type == "equipment") {

                foreach ($request->Eproducts as $product) {
                    $quotation_product                      = new SmQuotationProducts();
                    $quotation_product->quotation_id        = $quotation->id;
                    $quotation_product->product_id          = $product;
                    $quotation_product->product_model       = $request->Eproduct_model[$i];
                    $quotation_product->qnt                 = $request->Equantity[$i];
                    $quotation_product->unit_price          = $request->Eunit_price[$i];
                    $quotation_product->save();
                    $data               = SmQuotationProducts::find($quotation_product->id);
                    $data['note']       = '"quotation No' . $request->quotation_no . ' & Product Id ' . $data->product_id . '" has been added.';
                    $data['model_name'] = 'SmQuotationProducts';
                    $data['old_data']   = $data->toJson();
                    $data['new_data']   = '';
                    $data['action']     = 'Insert';
                    $data['action_id']  = $data->id;
                    $result             = SmGeneralSettings::StoreAllActivities($data);
                    $i++;
                }
            } else {
                foreach ($request->products as $product) {
                    $quotation_product                  = new SmQuotationProducts();
                    $quotation_product->quotation_id    = $quotation->id;
                    $quotation_product->product_id      = $product;
                    $quotation_product->qnt             = $request->quantity[$i];
                    $quotation_product->unit_price      = $request->unit_price[$i];
                    $result                             = $quotation_product->save();
                    $data               = SmQuotationProducts::find($quotation_product->id);
                    $data['note']       = '"quotation No' . $request->quotation_no . ' & Product Id ' . $data->product_id . '" has been added.';
                    $data['model_name'] = 'SmQuotationProducts';
                    $data['old_data']   = $data->toJson();
                    $data['new_data']   = '';
                    $data['action']     = 'Insert';
                    $data['action_id']  = $data->id;
                    $result             = SmGeneralSettings::StoreAllActivities($data);

                    $i++;
                }
            }

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('quotations');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    //end store method 

    public function show(SmQuotation $smQuotation, $id)
    {
        
        try{
            $quotation = SmQuotation::find($id);
            return view('backEnd/quotations/quotationView', compact('quotation'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

 
    public function edit(SmQuotation $smQuotation, $id)
    {
        try{
            $customers   = SmStaff::where('role_id', 2)->get();
            $vendors     = SmSupplier::all();
            $items       = SmItem::all();
            $edit        = SmQuotation::find($id);
            $quotations  = SmQuotation::all();
            $departments = SmInspectingDepartment::all();
            return view('backEnd/quotations/manage_quotations', compact('quotations', 'edit', 'customers', 'vendors', 'items', 'departments'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function update(Request $request, SmQuotation $smQuotation)
    {
        $input = $request->all();
        if ($request->work_order_mode == "equipment") {
            $validator = Validator::make($input, [
                'title'    => 'required',
                'number'       => 'required',
                'date'   => 'required',
                // 'vendors'        => 'required|not_in:0',
                'customer'        => 'required|not_in:0',
                'quotation_type' => 'required',
            ]);
        } else {
            $validator = Validator::make($input, [
                'title'    => 'required',
                'number'       => 'required',
                'date'   => 'required',
                // 'vendors'        => 'required|not_in:0',
                'customer'        => 'required|not_in:0',
                'quotation_type' => 'required',
            ]);
        }
        if ($validator->fails()) {
            return redirect('post/create')->withErrors($validator)->withInput();
        }


        DB::beginTransaction();
        try {
            $quotation = SmQuotation::find($request->id);
            $quotation->quotation_type      = $request->quotation_type;
            $quotation->title               = $request->title;
            $quotation->number              = $request->number;
            $quotation->date                = date('Y-m-d', strtotime($request->date));
            $quotation->reference           = $request->reference;
            //customer_details
            $quotation->customer_id         = $request->customer;
            $customer_details               = SmStaff::find($request->customer);
            $quotation->customer_name       = $customer_details->full_name;
            //vendor_details
            $quotation->vendor_id           = $request->vendors;
            $vendor_details                 = SmSupplier::find($request->vendors);
            $quotation->vendor_name         = $vendor_details->company_name;
            //footer note 
            $quotation->private_note        = $request->private_note;
            $quotation->public_note         = $request->public_note;
            $quotation->terms_note          = $request->terms_note;
            $quotation->footer_note         = $request->footer_note;
            $quotation->signature_person    = $request->signature_person;
            $quotation->signature_company   = $request->signature_company;

            if ($request->quotation_type == "equipment") {
                $quotation->discount_amount    = $request->Ediscount;
                $quotation->discount_type      = $request->Ediscount_type;
                $quotation->amount             = $request->Ebid_amount;
            } else {
                $quotation->discount_amount    = $request->discount;
                $quotation->discount_type      = $request->discount_type;
                $quotation->amount             = $request->bid_amount;
            }
            $quotation->updated_by    = Auth::user()->id;
            $quotation->save();
            $quotation->toArray();

            $data               = SmQuotation::find($quotation->id);
            $data['note']       = '"' . $data->title . '" has been added.';
            $data['model_name'] = 'SmQuotation';
            $data['old_data']   = $data->toJson();
            $data['new_data']   = '';
            $data['action']     = 'Insert';
            $data['action_id']  = $data->id;
            $result             = SmGeneralSettings::StoreAllActivities($data);

            SmQuotationProducts::where('quotation_id', $quotation->id)->delete();

            $i = 0;

            if ($request->quotation_type == "equipment") {

                foreach ($request->Eproducts as $product) {
                    $quotation_product                      = new SmQuotationProducts();
                    $quotation_product->quotation_id        = $quotation->id;
                    $quotation_product->product_id          = $product;
                    $quotation_product->product_model       = $request->Eproduct_model[$i];
                    $quotation_product->qnt                 = $request->Equantity[$i];
                    $quotation_product->unit_price          = $request->Eunit_price[$i];
                    $quotation_product->save();
                    $data               = SmQuotationProducts::find($quotation_product->id);
                    $data['note']       = '"quotation No' . $request->quotation_no . ' & Product Id ' . $data->product_id . '" has been added.';
                    $data['model_name'] = 'SmQuotationProducts';
                    $data['old_data']   = $data->toJson();
                    $data['new_data']   = '';
                    $data['action']     = 'Insert';
                    $data['action_id']  = $data->id;
                    $result             = SmGeneralSettings::StoreAllActivities($data);
                    $i++;
                }
            } else {
                foreach ($request->products as $product) {
                    $quotation_product                  = new SmQuotationProducts();
                    $quotation_product->quotation_id    = $quotation->id;
                    $quotation_product->product_id      = $product;
                    $quotation_product->qnt             = $request->quantity[$i];
                    $quotation_product->unit_price      = $request->unit_price[$i];
                    $result                             = $quotation_product->save();
                    $data               = SmQuotationProducts::find($quotation_product->id);
                    $data['note']       = '"quotation No' . $request->quotation_no . ' & Product Id ' . $data->product_id . '" has been added.';
                    $data['model_name'] = 'SmQuotationProducts';
                    $data['old_data']   = $data->toJson();
                    $data['new_data']   = '';
                    $data['action']     = 'Insert';
                    $data['action_id']  = $data->id;
                    $result             = SmGeneralSettings::StoreAllActivities($data);

                    $i++;
                }
            }

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('quotations');
            
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function destroy(Request $request, SmQuotation $smQuotation)
    {
        
        try{
            $result = SmQuotation::destroy($request->id);

            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect('quotations');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect('quotations'); 
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function delete($id)
    {
        
        try{
            $result = SmQuotation::destroy($id);

            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect('quotations');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect('quotations'); 
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}
