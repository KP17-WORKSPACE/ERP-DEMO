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
use App\SysChartofAccounts;
use App\SysCompany;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCrmQuoteItems;
use App\SysCurrencySettings;
use App\SysCustomer;
use App\SysCustomerType;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysHelper;
use App\SysPaymentTerms;
use App\SysProformaInvoice;
use App\SysProformaInvoiceItems;
use App\SysQuotations;
use App\SysQuotationsItems;
use App\SysSaleType;
use App\SysStates;
use App\SysVatType;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use App\SysBrand;
use App\SmDesignation;
use App\SysCrmQuoteCart;
use App\SysCrmQuoteCharges;
use App\SysCrmDealTrack;
use App\SysCrmDealTrackTemp;
use App\SysCrmDealsComments;
use App\SysCrmSupport;
use App\SysCrmEndUser;
use App\SysCrmQuoteCartEdit;
use App\ReserveStock;


class SysProformaInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id = null)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $ret = SysProformaInvoiceController::generate();
            //return $ret;
            //if($ret != "OK" ) { return $ret; }
            $query = SysProformaInvoice::select();

            $query->wherein('company_id', $company_id)->orderby('doc_number', 'desc');
            //$query->wherein('created_by',$r[1]);

            $quotations = $query->get();

            $selectedInv = [];
            $createData = [];
            $active_id = $id;
            $action = null;
            $poAction = null;
            $editData = null;



            if ($request->has('proforma_action')) {
                $poAction = $request->input('proforma_action');

                if ($poAction === 'add') {
                    $action = 'add';
                    $createData = $this->add();
                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->getQuoteEdit($active_id);

                }
            } else {
                if ($id) {
                    $active_id = $id;
                    $record = SysProformaInvoice::find($active_id)->get();
                    $selectedInv = $this->get_print_data($active_id);
                } else {
                    $firstRecord = $quotations->first();
                    if ($firstRecord) {
                        $active_id = $quotations->first()->id;
                        $selectedInv = $this->get_print_data($active_id);
                    }
                }
            }







            return view('backEnd/proformainvoice/proforma_invoice_list', compact('quotations', 'active_id', 'selectedInv', 'createData', 'action', 'poAction', 'editData'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getDetails($id)
    {

        $data = $this->get_print_data($id);

        if (!empty($data) && is_array($data)) {
            return view('backEnd/proformainvoice/proforma_invoice_details', $data);
        } else {
            return response("Error loading details!", 404);
        }
    }

    public function get_print_data($id)
    {
        try {

            $address = "";
            $address2 = "";
            $city = "";
            $state = "";
            $country = "";
            $contact_name = "";
            $email = "";
            $tel = "";
            $ship_company_name = "";
            $delivery_city = "";
            $delivery_zip_code = "";
            $delivery_country = "";
            $delivery_state = "";
            $mobile = "";
            $ship_mobile = "";
            $ship_tel = "";

            $pfi = SysProformaInvoice::find($id);
            if (!empty($pfi)) {

                $quotation = SysCrmDeals::where('id', $pfi->deal_id)->first();

                //return $quotation->customername;
                //return $quotation;
                $quotationitems = SysCrmQuoteItems::where('deal_id', $quotation->id)->where('quote_id', $quotation->quote_id)->orderby('sort_id', 'ASC')->get();

                $company = SysCompany::find($pfi->company_id);
                $pfi_item = SysProformaInvoiceItems::where('profo_id', '=', $pfi->id)->where('deal_id', $quotation->id)->where('quote_id', $quotation->quote_id)->get();
                $sup_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_cust_suppl.id', $pfi->customer)->first();

                if (!empty($sup_email)) {
                    $add = SysCustSupplAddressbook::where('cust_suppl_id', $sup_email->id)->first();
                }

                $contact_name = $sup_email->customer_salutation . ' ' . $sup_email->first_name . ' ' . $sup_email->last_name;
                $email = $sup_email->email;
                $tel = $sup_email->contcat_number;
                $vat_number = $sup_email->vat_number;
                $mobile = $sup_email->mobile;
                if (!empty($add)) {
                    $address = $add->address;
                    $address2 = $add->address2;
                    $city = $add->city;

                    try {
                        $state = $add->statename->name;
                    } catch (\Throwable $th) {
                        $state = 3391;
                        //throw $th;
                    }

                    $country = $add->countryname->name;
                }
                if ($pfi->deal_id != 0 && $pfi->deal_id != "") {
                    $deal_details = SysCrmDeals::where('id', $pfi->deal_id)->first();

                    if (isset($deal_details)) {
                        if ($deal_details->delivery_company != "") {
                            $ship_company_name = $deal_details->delivery_company;
                        } else {
                            $ship_company_name = $deal_details->customername->name;
                        }

                        if ($deal_details->delivery_address1 != "") {
                            $ship_address1 = $deal_details->delivery_address1;
                        } else {
                            $ship_address1 = $deal_details->address;
                        }
                        if ($deal_details->delivery_address1 != "" && $deal_details->delivery_address2 != "") {
                            $ship_address2 = $deal_details->delivery_address2;
                        } else {
                            $ship_address2 = "";
                        }
                        if ($deal_details->delivery_address1 != "" && $deal_details->delivery_city != "") {
                            $delivery_city = $deal_details->delivery_city;
                        } else {
                            $delivery_city = $add->city;
                        }
                        if ($deal_details->delivery_zip_code != "") {
                            $delivery_zip_code = $deal_details->delivery_zip_code;
                        } else {
                            $delivery_zip_code = "";
                        }
                        if ($deal_details->delivery_country != "") {
                            $delivery_country = $deal_details->country->name;
                        } else {
                            $delivery_country = $add->countryname->name;
                        }


                        try {
                            if ($deal_details->delivery_state != "") {
                                $delivery_state = $deal_details->state->name;
                            } else {
                                $delivery_state = $add->statename->name;
                            }
                        } catch (\Throwable $th) {
                            $delivery_state = 3391;
                        }


                        if ($deal_details->delivery_name != "") {
                            $ship_contact_name = $deal_details->delivery_name;
                        } else {
                            $ship_contact_name = $deal_details->cust_name;
                        }
             
                        if ($deal_details->delivery_number != "") {
                            $ship_tel = $deal_details->delivery_number;
                        }
                        $ship_mobile = $deal_details->cust_no;

                        if ($deal_details->delivery_email != "") {
                            $ship_email = $deal_details->delivery_email;
                        } else {
                            $ship_email = $deal_details->cust_email;
                        }
                    }
                } else {
                    $ship_company_name = "";
                    $ship_contact_name = $contact_name;
                    $ship_email = $email;
                    $ship_tel = $tel;
                    $ship_address1 = $add->city;
                    $ship_address2 = "";
                    $delivery_city = $add->city;
                    $delivery_zip_code = "";
                    $delivery_country = $add->countryname->name;
                    $delivery_state = $add->statename->name;
                }

                $data = [
                    'pfi' => $pfi,
                    'company' => $company,
                    'pfi_item' => $pfi_item,
                    'quotation' => $quotation,
                    'quotationitems' => $quotationitems,
                    'email' => $email,
                    'tel' => $tel,
                    'address' => $address,
                    'address2' => $address2,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'contact_name' => $contact_name,

                    'ship_company_name' => $ship_company_name,
                    'ship_address1' => $ship_address1,
                    'ship_address2' => $ship_address2,
                    'delivery_city' => $delivery_city,
                    'delivery_zip_code' => $delivery_zip_code,
                    'delivery_country' => $delivery_country,
                    'delivery_state' => $delivery_state,

                    'ship_contact_name' => $ship_contact_name,
                    'ship_tel' => $ship_tel,
                    'ship_email' => $ship_email,
                    'vat_number' => $vat_number,
                    'mobile' => $mobile,
                    'ship_mobile' => $ship_mobile,
                ];

                return $data;
            } else {
                return "error!!";
                //return view('web.syscom_credit_application_form');
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function generate()
    {
        try {
            DB::beginTransaction();
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $pfids = SysProformaInvoice::select('deal_id')->pluck('deal_id');
            $deals_ids = SysCrmDeals::select('id')->where('stage', 4)->whereNull('pfi_id')->wherenotin('id', $pfids)->get();

            if (count($deals_ids) > 0) {
                foreach ($deals_ids as $dealid) {
                    $deals = DB::table('sys_crm_deals as d')->select('d.*', 'i.*', 'c.*')
                        ->join('sys_crm_quote_items as i', 'i.deal_id', 'd.id')
                        ->join('sys_cust_suppl as c', 'c.id', 'd.cust_id')
                        //->join('sys_crm_deal_track as t','t.deal_id','d.id')
                        ->where('d.id', $dealid->id)->wherein('d.company_id', $company_id)->limit(1)->get();

                    if (count($deals) > 0) {
                        foreach ($deals as $data) {

                            try {
                                $doc_number = SysHelper::get_new_code('sys_proforma_invoice', 'PF', 'doc_number');
                                $pfi_id = DB::table('sys_proforma_invoice')->insertGetId([
                                    'ref_qt_id' => $data->quote_id,
                                    'doc_number' => $doc_number,
                                    'doc_date' => $data->date,
                                    'customer' => $data->cust_id,
                                    'currency' => $data->deal_currency,
                                    //'lpo_number' => $data->lpo_number,
                                    //'lpo_date' => $data->lpo_date,
                                    'payment_terms' => $data->payment_terms,
                                    'payment_terms2' => $data->payment_terms_txt,
                                    'delivery_terms' => $data->delivery_time,
                                    'sales_man' => $data->owner,
                                    'narration' => $data->deal_name,
                                    'shipping_name' => $data->name,
                                    'shipping_address' => $data->address,
                                    'customer_type' => $data->account_type,
                                    'sale_type' => $data->sale_type,
                                    'customer_country' => $data->vat_country,
                                    'customer_state' => $data->vat_state,
                                    //'end_user_name' => $data->end_user_name,
                                    'contact_person_name' => $data->cust_name,
                                    'contact_person_email' => $data->cust_email,
                                    'contact_person_no' => $data->cust_no,
                                    'deal_id' => $data->deal_id,
                                    'reference_no' => $data->code,
                                    'reference_date' => $data->date,
                                    'company_id' => $data->company_id,
                                    'created_by' => Auth::user()->id,
                                    'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                                    'status' => 1,
                                ]);

                                $deal_items = SysCrmQuoteItems::where('deal_id', $data->deal_id)->get();
                                if (count($deal_items) > 0) {
                                    foreach ($deal_items as $items) {
                                        $value = $items->price * $items->qty;
                                        $taxableamount = ($items->price * $items->qty) - $items->discount;
                                        $vatamount = $taxableamount * $data->vat / 100;
                                        DB::table('sys_proforma_invoice_items')->insert([
                                            'profo_id' => $pfi_id,
                                            'deal_id' => $data->deal_id,
                                            'quote_id' => $items->quote_id,
                                            'part_number' => $items->product_id,
                                            'tax' => $items->vat,
                                            'qty' => $items->qty,
                                            'unitprice' => $items->price,
                                            'value' => $value,
                                            'discount' => $items->discount,
                                            'taxableamount' => $taxableamount,
                                            'vatamount' => $vatamount,
                                            'status' => 1,
                                            'created_by' => Auth::user()->id,
                                        ]);
                                    }
                                }
                                DB::table('sys_crm_deals')->where('id', $dealid->id)->update(['pfi_id' => $pfi_id]);
                            } catch (\Throwable $th) {
                            }
                        }
                    }
                }
            }
            DB::commit();
            return "OK";
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }



    public static function re_generate($deal_id, $reference_no, $reference_date)
    {
        try {
            DB::beginTransaction();
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];



            $deals = DB::table('sys_crm_deals as d')->select('d.*', 'i.*', 'c.*')
                ->join('sys_crm_quote_items as i', 'i.deal_id', 'd.id')
                ->join('sys_cust_suppl as c', 'c.id', 'd.cust_id')
                //->join('sys_crm_deal_track as t','t.deal_id','d.id')
                ->where('d.id', $deal_id)->get();



            $reference_date = SysHelper::normalizeToYmd($reference_date);




            $check_pfi = SysProformaInvoice::where('deal_id', $deal_id)->first();
            if ($reference_no == "" && isset($check_pfi)) {
                $reference_no = $check_pfi->reference_no;
            }
            if ($reference_date == "" && isset($check_pfi)) {
                $reference_date = $check_pfi->reference_date;
            }

            if ($reference_date == "") {
                $reference_date = Carbon::now('+04:00')->format('Y-m-d');
            }

            if (count($deals) > 0) {

                $deal = $deals->first(); // take one deal row
                $deal_items = SysCrmQuoteItems::where('deal_id', $deal->deal_id)->get();


                if (isset($check_pfi)) {

                    $pfi_id = $check_pfi->id;
                    DB::table('sys_proforma_invoice')->where('id', $check_pfi->id)->update([
                        'ref_qt_id' => $deal->quote_id,
                        'doc_date' => $deal->date,
                        'customer' => $deal->cust_id,
                        'currency' => $deal->deal_currency,
                        'payment_terms' => $deal->payment_terms,
                        'payment_terms2' => $deal->payment_terms_txt,
                        'delivery_terms' => $deal->delivery_time,
                        'sales_man' => $deal->owner,
                        'narration' => $deal->deal_name,
                        'shipping_name' => $deal->name,
                        'shipping_address' => $deal->address,
                        'customer_type' => $deal->account_type,
                        'sale_type' => $deal->sale_type,
                        'customer_country' => $deal->vat_country,
                        'customer_state' => $deal->vat_state,
                        'contact_person_name' => $deal->cust_name,
                        'contact_person_email' => $deal->cust_email,
                        'contact_person_no' => $deal->cust_no,
                        'deal_id' => $deal->deal_id,
                        'reference_no' => $reference_no,
                        'reference_date' => $reference_date,
                        'company_id' => session('logged_session_data.company_id'),
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                        'status' => 1,
                    ]);
                } else {
                    $doc_number = SysHelper::get_new_code('sys_proforma_invoice', 'PF', 'doc_number');
                    $pfi_id = DB::table('sys_proforma_invoice')->insertGetId([
                        'ref_qt_id' => $deal->quote_id,
                        'doc_number' => $doc_number,
                        'doc_date' => $deal->date,
                        'customer' => $deal->cust_id,
                        'currency' => $deal->deal_currency,
                        //'lpo_number' => $deal->lpo_number,
                        //'lpo_date' => $data->lpo_date,
                        'payment_terms' => $deal->payment_terms,
                        'payment_terms2' => $deal->payment_terms_txt,
                        'delivery_terms' => $deal->delivery_time,
                        'sales_man' => $deal->owner,
                        'narration' => $deal->deal_name,
                        'shipping_name' => $deal->name,
                        'shipping_address' => $deal->address,
                        'customer_type' => $deal->account_type,
                        'sale_type' => $deal->sale_type,
                        'customer_country' => $deal->vat_country,
                        'customer_state' => $deal->vat_state,
                        //'end_user_name' => $deal->end_user_name,
                        'contact_person_name' => $deal->cust_name,
                        'contact_person_email' => $deal->cust_email,
                        'contact_person_no' => $deal->cust_no,
                        'deal_id' => $deal->deal_id,
                        'reference_no' => $reference_no,
                        'reference_date' => $reference_date,
                        'company_id' => session('logged_session_data.company_id'),
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                        'status' => 1,
                    ]);
                }




                DB::table('sys_proforma_invoice_items')->where('profo_id', $pfi_id)->delete();

                if (count($deal_items) > 0) {
                    foreach ($deal_items as $items) {
                        $value = $items->price * $items->qty;
                        $taxableamount = ($items->price * $items->qty) - $items->discount;
                        $vatamount = $taxableamount * $deal->vat / 100;
                        DB::table('sys_proforma_invoice_items')->insert([
                            'profo_id' => $pfi_id,
                            'deal_id' => $deal->deal_id,
                            'quote_id' => $items->quote_id,
                            'part_number' => $items->product_id,
                            'tax' => $items->vat,
                            'qty' => $items->qty,
                            'unitprice' => $items->price,
                            'value' => $value,
                            'discount' => $items->discount,
                            'taxableamount' => $taxableamount,
                            'vatamount' => $vatamount,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                        ]);
                    }
                }
                DB::table('sys_crm_deals')->where('id', $deal_id)->update(['pfi_id' => $pfi_id]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public static function re_generate_deal($deal_id, $reference_no, $reference_date)
    {
        try {

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];



            $deals = DB::table('sys_crm_deals as d')->select('d.*', 'i.*', 'c.*')
                ->join('sys_crm_quote_items as i', 'i.deal_id', 'd.id')
                ->join('sys_cust_suppl as c', 'c.id', 'd.cust_id')
                //->join('sys_crm_deal_track as t','t.deal_id','d.id')
                ->where('d.id', $deal_id)->get();



            $reference_date = SysHelper::normalizeToYmd($reference_date);




            $check_pfi = SysProformaInvoice::where('deal_id', $deal_id)->first();
            if ($reference_no == "" && isset($check_pfi)) {
                $reference_no = $check_pfi->reference_no;
            }
            if ($reference_date == "" && isset($check_pfi)) {
                $reference_date = $check_pfi->reference_date;
            }

            if ($reference_date == "") {
                $reference_date = Carbon::now('+04:00')->format('Y-m-d');
            }

            if (count($deals) > 0) {

                $deal = $deals->first(); // take one deal row
                $deal_items = SysCrmQuoteItems::where('deal_id', $deal->deal_id)->get();


                if (isset($check_pfi)) {


                    $pfi_id = $check_pfi->id;
                    DB::table('sys_proforma_invoice')->where('id', $check_pfi->id)->update([
                        'ref_qt_id' => $deal->quote_id,
                        'doc_date' => $deal->date,
                        'customer' => $deal->cust_id,
                        'currency' => $deal->deal_currency,
                        'payment_terms' => $deal->payment_terms,
                        'payment_terms2' => $deal->payment_terms_txt,
                        'delivery_terms' => $deal->delivery_time,
                        'sales_man' => $deal->owner,
                        'narration' => $deal->deal_name,
                        'shipping_name' => $deal->name,
                        'shipping_address' => $deal->address,
                        'customer_type' => $deal->account_type,
                        'sale_type' => $deal->sale_type,
                        'customer_country' => $deal->vat_country,
                        'customer_state' => $deal->vat_state,
                        'contact_person_name' => $deal->cust_name,
                        'contact_person_email' => $deal->cust_email,
                        'contact_person_no' => $deal->cust_no,
                        'deal_id' => $deal->deal_id,
                        'reference_no' => $reference_no,
                        'reference_date' => $reference_date,
                        'company_id' => session('logged_session_data.company_id'),
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                        'status' => 1,
                    ]);
                } else {

                    $doc_number = SysHelper::get_new_code('sys_proforma_invoice', 'PF', 'doc_number');
                    $pfi_id = DB::table('sys_proforma_invoice')->insertGetId([
                        'ref_qt_id' => $deal->quote_id,
                        'doc_number' => $doc_number,
                        'doc_date' => $deal->date,
                        'customer' => $deal->cust_id,
                        'currency' => $deal->deal_currency,
                        //'lpo_number' => $deal->lpo_number,
                        //'lpo_date' => $data->lpo_date,
                        'payment_terms' => $deal->payment_terms,
                        'payment_terms2' => $deal->payment_terms_txt,
                        'delivery_terms' => $deal->delivery_time,
                        'sales_man' => $deal->owner,
                        'narration' => $deal->deal_name,
                        'shipping_name' => $deal->name,
                        'shipping_address' => $deal->address,
                        'customer_type' => $deal->account_type,
                        'sale_type' => $deal->sale_type,
                        'customer_country' => $deal->vat_country,
                        'customer_state' => $deal->vat_state,
                        //'end_user_name' => $deal->end_user_name,
                        'contact_person_name' => $deal->cust_name,
                        'contact_person_email' => $deal->cust_email,
                        'contact_person_no' => $deal->cust_no,
                        'deal_id' => $deal->deal_id,
                        'reference_no' => $reference_no,
                        'reference_date' => $reference_date,
                        'company_id' => session('logged_session_data.company_id'),
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                        'status' => 1,
                    ]);
                }





                DB::table('sys_proforma_invoice_items')->where('profo_id', $pfi_id)->delete();

                if (count($deal_items) > 0) {
                    foreach ($deal_items as $items) {
                        $value = $items->price * $items->qty;
                        $taxableamount = ($items->price * $items->qty) - $items->discount;
                        $vatamount = $taxableamount * $deal->vat / 100;
                        DB::table('sys_proforma_invoice_items')->insert([
                            'profo_id' => $pfi_id,
                            'deal_id' => $deal->deal_id,
                            'quote_id' => $items->quote_id,
                            'part_number' => $items->product_id,
                            'tax' => $items->vat,
                            'qty' => $items->qty,
                            'unitprice' => $items->price,
                            'value' => $value,
                            'discount' => $items->discount,
                            'taxableamount' => $taxableamount,
                            'vatamount' => $vatamount,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                        ]);
                    }
                }
                DB::table('sys_crm_deals')->where('id', $deal_id)->update(['pfi_id' => $pfi_id]);
            }


            return $pfi_id;
        } catch (\Throwable $th) {

            return $th;
        }
    }


    // public static function re_generate($deal_id, $reference_no, $reference_date)
    // {
    //     try {
    //         DB::beginTransaction();
    //         $r = SysHelper::get_data_by_role();
    //         $company_id = $r[0];

    //         $deals = DB::table('sys_crm_deals as d')->select('d.*', 'i.*', 'c.*')
    //             ->join('sys_crm_quote_items as i', 'i.deal_id', 'd.id')
    //             ->join('sys_cust_suppl as c', 'c.id', 'd.cust_id')
    //             //->join('sys_crm_deal_track as t','t.deal_id','d.id')
    //             ->where('d.id', $deal_id)->wherein('d.company_id', $company_id)->get();

    //         $check_pfi = SysProformaInvoice::where('deal_id', $deal_id)->first();
    //         if ($reference_no == "") {
    //             $reference_no = $check_pfi->reference_no;
    //         }
    //         if ($reference_date == "") {
    //             $reference_date = $check_pfi->reference_date;
    //         }

    //         if (count($deals) > 0) {
    //             foreach ($deals as $data) {

    //                 if (isset($check_pfi)) {
    //                     $pfi_id = $check_pfi->id;
    //                     DB::table('sys_proforma_invoice')->where('id', $check_pfi->id)->update([
    //                         'ref_qt_id' => $data->quote_id,
    //                         'doc_date' => $data->date,
    //                         'customer' => $data->cust_id,
    //                         'currency' => $data->deal_currency,
    //                         //'lpo_number' => $data->lpo_number,
    //                         //'lpo_date' => $data->lpo_date,
    //                         'payment_terms' => $data->payment_terms,
    //                         'payment_terms2' => $data->payment_terms_txt,
    //                         'delivery_terms' => $data->delivery_time,
    //                         'sales_man' => $data->owner,
    //                         'narration' => $data->deal_name,
    //                         'shipping_name' => $data->name,
    //                         'shipping_address' => $data->address,
    //                         'customer_type' => $data->account_type,
    //                         'sale_type' => $data->sale_type,
    //                         'customer_country' => $data->vat_country,
    //                         'customer_state' => $data->vat_state,
    //                         //'end_user_name' => $data->end_user_name,
    //                         'contact_person_name' => $data->cust_name,
    //                         'contact_person_email' => $data->cust_email,
    //                         'contact_person_no' => $data->cust_no,
    //                         'deal_id' => $data->deal_id,
    //                         'reference_no' => $reference_no,
    //                         'reference_date' => $reference_date,
    //                         'company_id' => session('logged_session_data.company_id'),
    //                         'created_by' => Auth::user()->id,
    //                         'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
    //                         'status' => 1,
    //                     ]);
    //                 } else {
    //                     $doc_number = SysHelper::get_new_code('sys_proforma_invoice', 'PF', 'doc_number');
    //                     $pfi_id = DB::table('sys_proforma_invoice')->insertGetId([
    //                         'ref_qt_id' => $data->quote_id,
    //                         'doc_number' => $doc_number,
    //                         'doc_date' => $data->date,
    //                         'customer' => $data->cust_id,
    //                         'currency' => $data->deal_currency,
    //                         //'lpo_number' => $data->lpo_number,
    //                         //'lpo_date' => $data->lpo_date,
    //                         'payment_terms' => $data->payment_terms,
    //                         'payment_terms2' => $data->payment_terms_txt,
    //                         'delivery_terms' => $data->delivery_time,
    //                         'sales_man' => $data->owner,
    //                         'narration' => $data->deal_name,
    //                         'shipping_name' => $data->name,
    //                         'shipping_address' => $data->address,
    //                         'customer_type' => $data->account_type,
    //                         'sale_type' => $data->sale_type,
    //                         'customer_country' => $data->vat_country,
    //                         'customer_state' => $data->vat_state,
    //                         //'end_user_name' => $data->end_user_name,
    //                         'contact_person_name' => $data->cust_name,
    //                         'contact_person_email' => $data->cust_email,
    //                         'contact_person_no' => $data->cust_no,
    //                         'deal_id' => $data->deal_id,
    //                         'reference_no' => $reference_no,
    //                         'reference_date' => $reference_date,
    //                         'company_id' => session('logged_session_data.company_id'),
    //                         'created_by' => Auth::user()->id,
    //                         'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
    //                         'status' => 1,
    //                     ]);
    //                 }
    //                 $deal_items = SysCrmQuoteItems::where('deal_id', $data->deal_id)->get();

    //                 DB::table('sys_proforma_invoice_items')->where('profo_id', $pfi_id, )->delete();

    //                 if (count($deal_items) > 0) {
    //                     foreach ($deal_items as $items) {
    //                         $value = $items->price * $items->qty;
    //                         $taxableamount = ($items->price * $items->qty) - $items->discount;
    //                         $vatamount = $taxableamount * $data->vat / 100;
    //                         DB::table('sys_proforma_invoice_items')->insert([
    //                             'profo_id' => $pfi_id,
    //                             'deal_id' => $data->deal_id,
    //                             'quote_id' => $items->quote_id,
    //                             'part_number' => $items->product_id,
    //                             'tax' => $items->vat,
    //                             'qty' => $items->qty,
    //                             'unitprice' => $items->price,
    //                             'value' => $value,
    //                             'discount' => $items->discount,
    //                             'taxableamount' => $taxableamount,
    //                             'vatamount' => $vatamount,
    //                             'status' => 1,
    //                             'created_by' => Auth::user()->id,
    //                         ]);
    //                     }
    //                 }
    //                 DB::table('sys_crm_deals')->where('id', $deal_id)->update(['pfi_id' => $pfi_id]);
    //             }
    //         }
    //         DB::commit();
    //     } catch (\Throwable $th) {
    //         DB::rollBack();
    //         return $th;
    //     }
    // }

    public function download($id)
    {
        try {

            $address = "";
            $address2 = "";
            $city = "";
            $state = "";
            $country = "";
            $contact_name = "";
            $email = "";
            $tel = "";
            $ship_company_name = "";
            $delivery_city = "";
            $delivery_zip_code = "";
            $delivery_country = "";
            $delivery_state = "";

            $pfi = SysProformaInvoice::find($id);
            if (!empty($pfi)) {

                $quotation = SysCrmDeals::where('id', $pfi->deal_id)->first();
                //return $quotation->customername;
                //return $quotation;
                $quotationitems = SysCrmQuoteItems::where('deal_id', $quotation->id)->where('quote_id', $quotation->quote_id)->orderby('sort_id', 'ASC')->get();
                $company = SysCompany::find($pfi->company_id);
                $pfi_item = SysProformaInvoiceItems::where('profo_id', '=', $pfi->id)->get();
                $sup_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_cust_suppl.id', $pfi->customer)->first();

                if (!empty($sup_email)) {
                    $add = SysCustSupplAddressbook::where('cust_suppl_id', $sup_email->id)->first();
                }

                $contact_name = $sup_email->customer_salutation . ' ' . $sup_email->first_name . ' ' . $sup_email->last_name;
                $email = $sup_email->email;
                $tel = $sup_email->contcat_number;
                $vat_number = $sup_email->vat_number;
                if (!empty($add)) {
                    $address = $add->address;
                    $address2 = $add->address2;
                    $city = $add->city;

                    try {
                        $state = $add->statename->name;
                    } catch (\Throwable $th) {
                        $state = 3391;
                        //throw $th;
                    }

                    $country = $add->countryname->name;
                }
                if ($pfi->deal_id != 0 && $pfi->deal_id != "") {
                    $deal_details = SysCrmDeals::where('id', $pfi->deal_id)->first();

                    if (isset($deal_details)) {
                        if ($deal_details->delivery_company != "") {
                            $ship_company_name = $deal_details->delivery_company;
                        } else {
                            $ship_company_name = $deal_details->customername->name;
                        }

                        if ($deal_details->delivery_address1 != "") {
                            $ship_address1 = $deal_details->delivery_address1;
                        } else {
                            $ship_address1 = $deal_details->address;
                        }
                        if ($deal_details->delivery_address1 != "" && $deal_details->delivery_address2 != "") {
                            $ship_address2 = $deal_details->delivery_address2;
                        } else {
                            $ship_address2 = "";
                        }
                        if ($deal_details->delivery_address1 != "" && $deal_details->delivery_city != "") {
                            $delivery_city = $deal_details->delivery_city;
                        } else {
                            $delivery_city = $add->city;
                        }
                        if ($deal_details->delivery_zip_code != "") {
                            $delivery_zip_code = $deal_details->delivery_zip_code;
                        } else {
                            $delivery_zip_code = "";
                        }
                        if ($deal_details->delivery_country != "") {
                            $delivery_country = $deal_details->country->name;
                        } else {
                            $delivery_country = $add->countryname->name;
                        }


                        try {
                            if ($deal_details->delivery_state != "") {
                                $delivery_state = $deal_details->state->name;
                            } else {
                                $delivery_state = $add->statename->name;
                            }
                        } catch (\Throwable $th) {
                            $delivery_state = 3391;
                        }


                        if ($deal_details->delivery_name != "") {
                            $ship_contact_name = $deal_details->delivery_name;
                        } else {
                            $ship_contact_name = $deal_details->cust_name;
                        }
                        if ($deal_details->delivery_number != "") {
                            $ship_tel = $deal_details->delivery_number;
                        } else {
                            $ship_tel = $deal_details->cust_no;
                        }
                        if ($deal_details->delivery_email != "") {
                            $ship_email = $deal_details->delivery_email;
                        } else {
                            $ship_email = $deal_details->cust_email;
                        }
                    }
                } else {
                    $ship_company_name = "";
                    $ship_contact_name = $contact_name;
                    $ship_email = $email;
                    $ship_tel = $tel;
                    $ship_address1 = $add->city;
                    $ship_address2 = "";
                    $delivery_city = $add->city;
                    $delivery_zip_code = "";
                    $delivery_country = $add->countryname->name;
                    $delivery_state = $add->statename->name;
                }

                $data = [
                    'pfi' => $pfi,
                    'company' => $company,
                    'pfi_item' => $pfi_item,
                    'quotation' => $quotation,
                    'quotationitems' => $quotationitems,
                    'email' => $email,
                    'tel' => $tel,
                    'address' => $address,
                    'address2' => $address2,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'contact_name' => $contact_name,

                    'ship_company_name' => $ship_company_name,
                    'ship_address1' => $ship_address1,
                    'ship_address2' => $ship_address2,
                    'delivery_city' => $delivery_city,
                    'delivery_zip_code' => $delivery_zip_code,
                    'delivery_country' => $delivery_country,
                    'delivery_state' => $delivery_state,

                    'ship_contact_name' => $ship_contact_name,
                    'ship_tel' => $ship_tel,
                    'ship_email' => $ship_email,
                    'vat_number' => $vat_number,
                ];
                // return view('backEnd.pdf_print.pfi_pdf', $data);
                $pdf = PDF::loadView('backEnd.pdf_print.pfi_pdf', $data);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($pfi->doc_number . '-' . $pfi->customername->name . ".pdf");
            } else {
                return "error!!";
                //return view('web.syscom_credit_application_form');
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }



    public function create()
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency = SysCurrencySettings::all();
            $items = SysHelper::get_product_list($company_id);
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $sales_man = SmStaff::select('user_id', 'full_name')->orderby('full_name', 'asc')->wherein('company_id', $company_id)->get();

            $customer = SysHelper::get_customer_list($company_id);

            $items = SmItem::where('status', 1)->orderby('part_number', 'asc')->wherein('company_id', $company_id)->get();
            $departments = SmInspectingDepartment::all();
            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();
            $vattype = SysVatType::all();
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();
            $customertype = SysCustomerType::orderby('title', 'asc')->get();
            $saletype = SysSaleType::orderby('title', 'asc')->get();
            return compact('sales_man', 'items', 'departments', 'currency', 'items', 'company', 'customer', 'countries', 'states', 'vattype', 'paymentterms', 'customertype', 'saletype');

            // return view('backEnd.proformainvoice.manage_proforma_invoice', compact('sales_man', 'items', 'departments', 'currency', 'items', 'company', 'customer', 'countries', 'states', 'vattype', 'paymentterms', 'customertype', 'saletype'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function quotationpending(Request $request)
    {
        try {
            $cust_id = SysCustSuppl::select('sys_cust_suppl.id')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')
                ->where('sys_chartofaccounts.id', $request->id)->first();
            $ret = SysCrmDeals::select('sys_crm_deals.id', 'sys_crm_deals.code', 'sys_crm_deals.deal_name')->where('cust_id', $cust_id->id)->where('stage', 4)->where('company_id', session('logged_session_data.company_id'))->get();
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                return json_encode(array('data' => 'ERROR'));
            }
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    function quotationpendingitemlist(Request $request)
    {
        try {
            $ret = SysCrmQuoteItems::select('sys_crm_quote_items.*', 'sm_items.part_number', 'sm_items.description', 'sys_crm_deals.note', 'sys_crm_deals.cust_name', 'sys_crm_deals.date', 'sys_cust_suppl.address', 'sys_cust_suppl.vat_country', 'sys_cust_suppl.vat_state')
                ->join('sm_items', 'sm_items.id', 'sys_crm_quote_items.product_id')
                ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_quote_items.deal_id')
                ->join('sys_cust_suppl', 'sys_cust_suppl.id', 'sys_crm_deals.cust_id')
                ->where('deal_id', $request->qt_id)->get();

            // $ret = DB::select("SELECT qi.id,itm.id AS part_id,itm.part_number,qi.qty AS qt_qty,itmstock.qty AS os_qty,
            // (SELECT qty FROM sys_proforma_invoice_items WHERE ref_qt_id = qi.qt_id AND part_number = qi.part_number) pro_qty
            // FROM sys_quotations_items qi
            // INNER JOIN sm_items itm ON itm.id=qi.part_number
            // INNER JOIN sys_item_stock itmstock ON itmstock.partno=qi.part_number
            // WHERE qt_id = '". $request->qt_id."'");

            /*$ret = SysQuotationsItems::select('sys_quotations_items.id','sm_items.part_number','sys_quotations_items.qty as qt_qty','sys_item_stock.qty as os_qty','sys_proforma_invoice_items.qty as pro_qty')
                    ->join('sm_items','sm_items.id','sys_quotations_items.part_number')
                    ->join('sys_item_stock','sys_item_stock.partno','sys_quotations_items.part_number')
                    ->leftjoin('sys_proforma_invoice_items','sys_proforma_invoice_items.ref_qt_id','sys_quotations_items.qt_id')
                    ->where('qt_id',$request->qt_id)->get();*/

            return response()->json([$ret]);
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function store(Request $request)
    {

        if ($request->is_professional_service == 1) {
            $is_professional_service = 1;
        } else {
            $is_professional_service = 0;
        }

        DB::beginTransaction();
        try {

            if ($request->qt_id == null || $request->qt_id == "") {


                if ($request->deal_value == "") {
                    $deal_value = "0.00";
                } else {
                    $deal_value = $request->deal_value;
                }




                $delivery_company = SysCustSuppl::select('sys_cust_suppl.*')
                    ->join('sys_chartofaccounts as ca', 'ca.account_code', 'sys_cust_suppl.code')
                    ->where('sys_cust_suppl.id', $request->cust_id)->first();




                $scd = new SysCrmDeals();
                $scd->code = SysHelper::get_new_code_lead('sys_crm_deals', 'DL', 'code', session('logged_session_data.company_id'));
                $scd->date = Carbon::now()->format('Y-m-d');
                $scd->deal_name = $request->deal_name;
                $scd->cust_id = $request->cust_id;


                $scd->cust_name = $delivery_company->cust_name;
                $scd->cust_no = $delivery_company->cust_no;
                $scd->cust_email = $delivery_company->email;
                $scd->deal_value = $deal_value;
                $scd->tags = null;
                $scd->stage = 4;
                $scd->owner = $request->owner;



                $scd->isproject = null;
                $scd->designation = $delivery_company->designation;

                $state = SysStates::find($delivery_company->vat_state);
                $country = SysCountries::find($delivery_company->vat_country);

                $scd->address = $delivery_company->flat_office_no . ' ' . $delivery_company->building_name . ' ' . $delivery_company->area . ' ' . $delivery_company->city . ' ' . $state->name . ' ' . $country->name;


                
                $scd->delivery_company = $delivery_company->id;

             
 
                $scd->delivery_name = $delivery_company->customer_salutation.' '.$delivery_company->first_name.' '.$delivery_company->last_name;
                $scd->delivery_number = $delivery_company->contcat_number;
                $scd->delivery_email = $delivery_company->email;
                $scd->delivery_country = $delivery_company->vat_country;
                $scd->delivery_state = $delivery_company->vat_state;
                $scd->delivery_city = $delivery_company->city;
                $scd->delivery_area = $delivery_company->area;
                $scd->delivery_building = $delivery_company->building_name;
                $scd->delivery_flat_office_no = $delivery_company->flat_office_no;
                $scd->delivery_zip_code = $delivery_company->zip_code;

                $scd->followup_date = Carbon::now()
                    ->addDays(3)
                    ->setTime(11, 0, 0)
                    ->format('Y-m-d H:i:s');


                $scd->status = 1;
                $scd->estimated_close_date = SysHelper::normalizeToYmd($request->estimated_close_date);
                $scd->created_by = Auth::user()->id;
                $scd->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                $scd->company_id = session('logged_session_data.company_id');
                $scd->is_professional_service = $is_professional_service;
                $scd->save();
                $scd->toArray();


                $quote_id = SysCrmQuoteItems::where('deal_id', $scd->id)->max('quote_id');
                $customer = SysCustSuppl::where('id', $request->cust_id)->first();
                $document_number = SysHelper::getNextDealQuoteDocNo();

                if ($request->part_number != null && count($request->part_number) > 0) {


                    for ($i = 0; $i < count($request->part_number); $i++) {
                        $data[] = [
                            'user_id' => $request->owner,
                            'deal_id' => $scd->id,
                            'company_id' => session('logged_session_data.company_id'),
                            'currency_id' => $request->currency_id !== '' ? $request->currency_id : null,
                            'customer_type' => $customer->customer_type,
                            'quote_validity' => $request->quote_validity,
                            'payment_terms' => $request->payment_terms,
                            'delivery_date' => SysHelper::normalizeToYmd($request->estimated_close_date),
                            'payment_terms_txt' => $request->payment_terms_txt,
                            'delivery_time' => $request->delivery_time,
                            'product_id' => $request->part_number[$i],
                            'qty' => $request->qty[$i],
                            'price' => (float) str_replace(',', '', $request->unitprice[$i] ?? 0),
                            'description' => $request->description[$i],
                            'discount' => (float) str_replace(',', '', $request->discount[$i] ?? 0),
                            'vat' => $request->tax[$i],
                            'cost' => (float) str_replace(',', '', $request->cost[$i] ?? 0),
                            'status' => 1,
                            'sort_id' => $i + 1,
                            'created_by' => Auth::user()->id,
                            'product_type' => !empty($request->product_type[$i])
                                ? $request->product_type[$i]
                                : null,
                            'quote_id' => $quote_id + 1,
                            'document_number' => $document_number,
                        ];


                    }


                    SysCrmQuoteItems::insert($data);
                    DB::table('sys_crm_deals')->where('id', $scd->id)
                        ->update(['estimated_close_date' => SysHelper::normalizeToYmd($request->estimated_close_date), 'quote_id' => $quote_id + 1, 'deal_discount' => (float) str_replace(',', '', $request->deal_discount ?? 0),'deal_discount_vat' => ($request->deal_discount_vat ?? 0), 'terms_and_condition' => $request->terms_and_condition]);
                    SysHelper::deal_updated_at($scd->id);

                    $scd->note = 'Deal created from quotation. Quotation ID: ' . $document_number;
                    $scd->save();



                    if (!isset($request->tags) || $request->tags == '') {

                        $item = SmItem::find($request->part_number[0]);
                        if ($item) {
                            $brandName = SmItem::getBrandName($item->brand);
                            $scd->tags = $brandName;
                        } else {
                            $scd->tags = '';
                        }

                        $scd->save();
                    }

                     //sys_crm_quote_charges
                for ($i = 0; $i < count($request->cfc_name); $i++) {
                    if ($request->cfc_name[$i] != "" && $request->cfc_credit_account[$i] != "" && $request->cfc_amount[$i] != "") {
                    
                        $cfc = new SysCrmQuoteCharges();
                        $cfc->deal_id = $scd->id;
                        $cfc->quote_id = $quote_id + 1;
                        $cfc->selling_exp_account = $request->cfc_name[$i];
                        $cfc->credit_account = $request->cfc_credit_account[$i];
                        $cfc->amount = $request->cfc_amount[$i];
                        $cfc->remarks = $request->cfc_remarks[$i];
                        $cfc->status = 1;
                        $cfc->created_by = Auth::user()->id;
                        $cfc->save();
                    }
                }
                }


                $pfi_id = SysProformaInvoiceController::re_generate_deal($scd->id, $request->lpo_number, $request->doc_date);


                $pro_doc = SysProformaInvoice::find($pfi_id);
                $pro_doc->proforma_invoice = $request->proforma_invoice;
                $pro_doc->sales_man = $request->input('owner', $request->input('sales_man'));
                $pro_doc->save();

                $scd->note = $request->narration . ' Deal created from Proforma Invoice. Proforma Invoice: ' . $pro_doc->doc_number;
                $scd->save();

                SysHelper::set_deal_profit($scd->id);


                SysHelper::set_user_custsupp($request->owner, $request->cust_id);

                SysCrmQuoteCart::where([
                    'cart_id' => session('logged_session_data.cart_id'),
                    'user_id' => Auth::user()->id,
                ])->delete();








                $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

                $check = SysCrmDealTrackTemp::select('id', 'accounts', 'sales', 'purchease', 'invoice', 'delivery', 'receivables', 'tech')->where('deal_id', $scd->id)->first();


                if (isset($check)) {
                    $deal_track = SysCrmDealTrackTemp::find($check->id);
                    $deal_track->reference_no = $scd->lpo_number;
                    if ($request->doc_date)
                        $deal_track->reference_date = SysHelper::normalizeToYmd($request->doc_date);
                    $deal_track->updated_by = Auth::user()->id;
                    $deal_track->updated_at = $trn_time;
                    $deal_track->save();
                } else {
                    $deal_track = new SysCrmDealTrackTemp();
                    $deal_track->deal_id = $scd->id;
                    $proforma_doc = SysProformaInvoice::find($pfi_id);
                    $deal_track->remarks = 'Deal created from Proforma Invoice. Proforma ID: ' . $proforma_doc->doc_number;
                    $deal_track->reference_no = $request->lpo_number;
                    if ($request->doc_date)
                        $deal_track->reference_date = SysHelper::normalizeToYmd($request->doc_date);
                    $deal_track->accounts = 0;
                    $deal_track->sales = 0;
                    $deal_track->purchease = 0;
                    $deal_track->invoice = 0;
                    $deal_track->delivery = 0;
                    $deal_track->receivables = 0;
                    $deal_track->tech = 0;
                    $deal_track->created_by = Auth::user()->id;
                    $deal_track->created_at = $trn_time;
                    $deal_track->created_date = $trn_time;
                    $deal_track->company_id = session('logged_session_data.company_id');
                    $deal_track->save();
                    $deal_track->toArray();
                }



            } else {

                $proforma = new SysProformaInvoice();
                $proforma->ref_qt_id = $request->qt_id;
                $proforma->doc_number = SysHelper::get_new_code('sys_proforma_invoice', 'PF', 'doc_number');
                $proforma->doc_date = Carbon::createFromFormat('d/m/Y', $request->doc_date);
                $proforma->customer = $request->customer;
                $proforma->currency = $request->currency;
                $proforma->lpo_number = $request->lpo_number;
                $proforma->lpo_date = Carbon::createFromFormat('d/m/Y', $request->lpo_date);
                $proforma->payment_terms = $request->payment_terms;
                $proforma->payment_terms2 = $request->payment_terms2;
                $proforma->delivery_terms = $request->delivery_terms;
                $proforma->proforma_invoice = $request->proforma_invoice;
                $proforma->sales_man = $request->input('owner', $request->input('sales_man'));
                $proforma->narration = $request->narration;
                $proforma->shipping_name = $request->shipping_name;
                $proforma->shipping_address = $request->shipping_address;
                $proforma->customer_type = $request->customer_type;
                $proforma->sale_type = $request->sale_type;
                $proforma->customer_country = $request->customer_country;
                $proforma->customer_state = $request->customer_state;
                $proforma->end_user_name = $request->end_user_name;
                $proforma->contact_person_name = $request->contact_person_name;
                $proforma->contact_person_email = $request->contact_person_email;
                $proforma->contact_person_no = $request->contact_person_no;
                $proforma->deal_id = $request->deal_id;
                $proforma->company_id = session('logged_session_data.company_id');
                $proforma->created_by = Auth::user()->id;
                $proforma->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                $proforma->status = 1;
                $proforma->save();
                $proforma->toArray();

                for ($i = 0; $i < count($request->part_number); $i++) {
                    if ($request->qty[$i] != "" && $request->qty[$i] > 0) {
                        $pii = new SysProformaInvoiceItems();
                        $pii->profo_id = $proforma->id;
                        $pii->ref_qt_id = $request->qt_id;
                        $pii->part_number = $request->part_number[$i];
                        $pii->tax = $request->net_vat;
                        $pii->qty = $request->qty[$i];
                        $pii->unitprice = $request->unitprice[$i];
                        $pii->value = $request->value[$i];
                        $pii->discount = ($request->discount[$i] === '' ? '0.00' : $request->discount[$i]);
                        $pii->taxableamount = ($request->taxableamount[$i] === '' ? '0.00' : $request->taxableamount[$i]);
                        $pii->vatamount = ($request->vatamount[$i] === '' ? '0.00' : $request->vatamount[$i]);
                        $pii->status = 1;
                        $pii->created_by = Auth::user()->id;
                        $pii->save();
                    }
                }

            }


            DB::commit();

            Toastr::success('Proforma Invoice has been added successfully', 'Success');
            return redirect('proforma-invoice/' . $pfi_id);
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }






    // public function store(Request $request)
    // {

    //     try {
    //         DB::beginTransaction();
    //         if (count(array_filter($request->part_number)) > 0 && count(array_filter($request->qty)) > 0 && count(array_filter($request->unitprice)) > 0) {
    //             $proforma = new SysProformaInvoice();
    //             $proforma->ref_qt_id = $request->qt_id;
    //             $proforma->doc_number = SysHelper::get_new_code('sys_proforma_invoice', 'PF', 'doc_number');
    //             $proforma->doc_date = Carbon::createFromFormat('d/m/Y', $request->doc_date);
    //             $proforma->customer = $request->customer;
    //             $proforma->currency = $request->currency;
    //             $proforma->lpo_number = $request->lpo_number;
    //             $proforma->lpo_date = Carbon::createFromFormat('d/m/Y', $request->lpo_date);
    //             $proforma->payment_terms = $request->payment_terms;
    //             $proforma->payment_terms2 = $request->payment_terms2;
    //             $proforma->delivery_terms = $request->delivery_terms;
    //             $proforma->sales_man = $request->sales_man;
    //             $proforma->narration = $request->narration;
    //             $proforma->shipping_name = $request->shipping_name;
    //             $proforma->shipping_address = $request->shipping_address;
    //             $proforma->customer_type = $request->customer_type;
    //             $proforma->sale_type = $request->sale_type;
    //             $proforma->customer_country = $request->customer_country;
    //             $proforma->customer_state = $request->customer_state;
    //             $proforma->end_user_name = $request->end_user_name;
    //             $proforma->contact_person_name = $request->contact_person_name;
    //             $proforma->contact_person_email = $request->contact_person_email;
    //             $proforma->contact_person_no = $request->contact_person_no;
    //             $proforma->deal_id = $request->deal_id;
    //             $proforma->company_id = session('logged_session_data.company_id');
    //             $proforma->created_by = Auth::user()->id;
    //             $proforma->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
    //             $proforma->status = 1;
    //             $proforma->save();
    //             $proforma->toArray();

    //             for ($i = 0; $i < count($request->part_number); $i++) {
    //                 if ($request->qty[$i] != "" && $request->qty[$i] > 0) {
    //                     $pii = new SysProformaInvoiceItems();
    //                     $pii->profo_id = $proforma->id;
    //                     $pii->ref_qt_id = $request->qt_id;
    //                     $pii->part_number = $request->part_number[$i];
    //                     $pii->tax = $request->net_vat;
    //                     $pii->qty = $request->qty[$i];
    //                     $pii->unitprice = $request->unitprice[$i];
    //                     $pii->value = $request->value[$i];
    //                     $pii->discount = ($request->discount[$i] === '' ? '0.00' : $request->discount[$i]);
    //                     $pii->taxableamount = ($request->taxableamount[$i] === '' ? '0.00' : $request->taxableamount[$i]);
    //                     $pii->vatamount = ($request->vatamount[$i] === '' ? '0.00' : $request->vatamount[$i]);
    //                     $pii->status = 1;
    //                     $pii->created_by = Auth::user()->id;
    //                     $pii->save();
    //                 }
    //             }





    //             DB::commit();
    //             Toastr::success('Operation successful', 'Success');
    //             return redirect('proforma-invoice');
    //         } else {
    //             Toastr::error('Operation Failed. please enter valid data', 'Failed');
    //             return redirect()->back();
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return $e;
    //         Toastr::error('Operation Failed', 'Failed');
    //         return redirect()->back();
    //     }
    // }
    //end store method 

    public function show(Request $request, $id)
    {
        return $this->index($request, $id);
        return "";
        try {
            $quotation = SysQuotations::find($id);
            return view('backEnd/quotations/quotationView', compact('quotation'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function edit(Request $request, $id)
    {
        $des = SmItem::select('description')->where('id', 1001)->first();
        //return $des->description;
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $editData = SysQuotations::find($id);
            $editDataList = SysQuotationsItems::where('qt_id', $id)->get();

            $currency = SysCurrencySettings::all();
            $items = SysHelper::get_product_list($company_id);
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $sales_man = SmStaff::where('role_id', 2)->get();
            $vendors = SysHelper::get_customer_list_all($company_id);
            $custsuppl = SysCustSuppl::where('catid', 1)->get(); //1 customers, 2 suppliers
            $items = SmItem::where('status', 1)->get();
            $quotations = SmQuotation::all();
            $departments = SmInspectingDepartment::all();
            $countries = SysCountries::All();
            $vattype = SysVatType::all();
            $paymentterms = SysPaymentTerms::all();

            return view('backEnd/quotations/manage_quotations_edit', compact('quotations', 'sales_man', 'vendors', 'items', 'departments', 'currency', 'items', 'company', 'custsuppl', 'countries', 'vattype', 'paymentterms', 'editData', 'editDataList'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        
        DB::beginTransaction();
        try {
            $delivery_company = SysCustSuppl::select('sys_cust_suppl.*')
                ->join('sys_chartofaccounts as ca', 'ca.account_code', 'sys_cust_suppl.code')
                ->where('sys_cust_suppl.id', $request->cust_id)->first();


            $scd = SysCrmDeals::find($request->deal_id);
           
            $scd->deal_name = $request->deal_name;
            $scd->cust_id = $request->cust_id;


            $scd->cust_name = $delivery_company->cust_name;
            $scd->cust_no = $delivery_company->cust_no;
            $scd->cust_email = $delivery_company->email;
            $scd->tags = null;
            $scd->stage = 4;
            $scd->owner = $request->owner;



            $scd->isproject = null;
            $scd->designation = $delivery_company->designation;

            $state = SysStates::find($delivery_company->vat_state);
            $country = SysCountries::find($delivery_company->vat_country);

            $scd->address = $delivery_company->flat_office_no . ' ' . $delivery_company->building_name . ' ' . $delivery_company->area . ' ' . $delivery_company->city . ' ' . $state->name . ' ' . $country->name;

            $scd->delivery_company = $delivery_company->id;
       
                $scd->delivery_name = $delivery_company->customer_salutation.' '.$delivery_company->first_name.' '.$delivery_company->last_name;
                $scd->delivery_number = $delivery_company->contcat_number;
                $scd->delivery_email = $delivery_company->email;
                $scd->delivery_country = $delivery_company->vat_country;
                $scd->delivery_state = $delivery_company->vat_state;
                $scd->delivery_city = $delivery_company->city;
                $scd->delivery_area = $delivery_company->area;
                $scd->delivery_building = $delivery_company->building_name;
                $scd->delivery_flat_office_no = $delivery_company->flat_office_no;
                $scd->delivery_zip_code = $delivery_company->zip_code;

            $scd->status = 1;
            $scd->estimated_close_date = SysHelper::normalizeToYmd($request->estimated_close_date);
            $scd->updated_by = Auth::user()->id;
            $scd->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $scd->update();
            $scd->toArray();


            $get_existing_doc_no = "";


            $get_existing_doc = SysCrmQuoteItems::where('deal_id', $scd->id)->where('quote_id', $scd->quote_id)->first(['document_number']);
            if ($get_existing_doc) {
                $get_existing_doc_no = $get_existing_doc->document_number;
            }

            $deleted = SysCrmQuoteItems::where('deal_id', $scd->id)->where('quote_id', $scd->quote_id)->delete();
            $customer = SysCustSuppl::where('id', $request->cust_id)->first();





            if ($request->part_number != null && count($request->part_number) > 0) {
                for ($i = 0; $i < count($request->part_number); $i++) {
                    $data[] = [
                        'user_id' => $request->owner,
                        'deal_id' => $scd->id,
                        'currency_id' => $request->currency_id,
                        'company_id' => $scd->company_id,
                        'customer_type' => $customer->customer_type,
                        'quote_validity' => $request->quote_validity,
                        'payment_terms' => $request->payment_terms,
                        'delivery_date' => SysHelper::normalizeToYmd($request->estimated_close_date),
                        'payment_terms_txt' => $request->payment_terms_txt,
                        'delivery_time' => $request->delivery_time,
                        'product_id' => $request->part_number[$i],
                        'qty' => $request->qty[$i],
                        'price' => (float) str_replace(',', '', $request->unitprice[$i] ?? 0),
                        'description' => $request->description[$i],
                        'discount' => (float) str_replace(',', '', $request->discount[$i] ?? 0),
                        'vat' => $request->tax[$i],
                        'cost' => (float) str_replace(',', '', $request->cost[$i] ?? 0),
                        'status' => 1,
                        'sort_id' => $i + 1,
                        'created_by' => Auth::user()->id,
                        'product_type' => !empty($request->product_type[$i])
                            ? $request->product_type[$i]
                            : null,
                        'quote_id' => $scd->quote_id,
                        'document_number' => $get_existing_doc_no,
                    ];
                }
                $inserted = SysCrmQuoteItems::insert($data);

        

                DB::table('sys_crm_deals')->where('id', $scd->id)
                    ->update(['estimated_close_date' => SysHelper::normalizeToYmd($request->estimated_close_date), 'quote_id' => $scd->quote_id, 'deal_discount' => (float) str_replace(',', '', $request->deal_discount ?? 0), 'deal_discount_vat' => ($request->deal_discount_vat ?? 0),'terms_and_condition' => $request->terms_and_condition]);
                SysHelper::deal_updated_at($scd->id);

                if ($scd->tags == "" || $scd->tags == null) {

                    $item = SmItem::find($request->part_number[0]);
                    if ($item) {
                        $brandName = SmItem::getBrandName($item->brand);
                        $scd->tags = $brandName;
                    } else {
                        $scd->tags = '';
                    }

                    $scd->update();
                }

                  //sys_crm_quote_charges
                SysCrmQuoteCharges::where('deal_id', $scd->id)->delete();
                for ($i = 0; $i < count($request->cfc_name); $i++) {
                    if ($request->cfc_name[$i] != "" && $request->cfc_credit_account[$i] != "" && $request->cfc_amount[$i] != "") {
                        $cfc = new SysCrmQuoteCharges();
                        $cfc->deal_id = $scd->id;
                        $cfc->quote_id = $request->quote_id;
                        $cfc->selling_exp_account = $request->cfc_name[$i];
                        $cfc->credit_account = $request->cfc_credit_account[$i];
                        $cfc->amount = $request->cfc_amount[$i];
                        $cfc->remarks = $request->cfc_remarks[$i];
                        $cfc->status = 1;
                        $cfc->created_by = Auth::user()->id;
                        $cfc->save();
                    }
                }
            }

            


            $pfi_id = SysProformaInvoiceController::re_generate_deal($scd->id, $request->lpo_number, $request->doc_date);

            $proforma_invoice = SysProformaInvoice::find($pfi_id);
            $proforma_invoice->proforma_invoice = $request->proforma_invoice;
            // Persist updated sales person in proforma invoice record as well
            $proforma_invoice->sales_man = $request->owner;
            $proforma_invoice->save();

            SysHelper::set_deal_profit($scd->id);


            SysHelper::set_user_custsupp($request->owner, $request->cust_id);

            SysCrmQuoteCart::where([
                'cart_id' => session('logged_session_data.cart_id'),
                'user_id' => Auth::user()->id,
            ])->delete();


            $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

            $check = SysCrmDealTrackTemp::select('id', 'accounts', 'sales', 'purchease', 'invoice', 'delivery', 'receivables', 'tech')->where('deal_id', $scd->id)->first();


            if (isset($check)) {
                $deal_track = SysCrmDealTrackTemp::find($check->id);
                $deal_track->reference_no = $request->lpo_number;
                if ($request->doc_date)
                    $deal_track->reference_date = SysHelper::normalizeToYmd($request->doc_date);
                $deal_track->updated_by = Auth::user()->id;
                $deal_track->updated_at = $trn_time;
                $deal_track->save();
            } else {
                $deal_track = new SysCrmDealTrackTemp();
                $deal_track->deal_id = $scd->id;
                $proforma_doc = SysProformaInvoice::find($pfi_id);
                $deal_track->remarks = 'Deal created from Proforma Invoice. Proforma ID: ' . $proforma_doc->doc_number;
                $deal_track->reference_no = $request->lpo_number;
                if ($request->doc_date)
                    $deal_track->reference_date = SysHelper::normalizeToYmd($request->doc_date);
                $deal_track->accounts = 0;
                $deal_track->sales = 0;
                $deal_track->purchease = 0;
                $deal_track->invoice = 0;
                $deal_track->delivery = 0;
                $deal_track->receivables = 0;
                $deal_track->tech = 0;
                $deal_track->created_by = Auth::user()->id;
                $deal_track->created_at = $trn_time;
                $deal_track->created_date = $trn_time;
                $deal_track->company_id = session('logged_session_data.company_id');
                $deal_track->save();
                $deal_track->toArray();
            }

            DB::commit();
            Toastr::success('Proforma Invoice has been updated successfully', 'Success');
            return redirect('proforma-invoice/' . $pfi_id);
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function destroy(Request $request, SmQuotation $smQuotation)
    {

        try {
            $result = SmQuotation::destroy($request->id);

            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect('quotations');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect('quotations');
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function delete($id)
    {

        try {
            $result = SmQuotation::destroy($id);

            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect('quotations');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect('quotations');
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function search(Request $request)
    {

        $q = $request->get('query');
        $formattedDate = null;
        if (preg_match('/\d{2}[\/\-]\d{2}[\/\-]\d{4}/', $q)) {
            $normalized = str_replace('/', '-', $q);
            $formattedDate = date('Y-m-d', strtotime($normalized));
        }


        $company_id = session('logged_session_data.company_id');
        $query = SysProformaInvoice::with(['deal_code:id,code', 'customername:id,name', 'salesman:id,full_name'])->select();
        $query->where('company_id', $company_id)->orderby('doc_number', 'desc');







        $query->where(function ($query) use ($q, $formattedDate) {
            $query->where(function ($qsub) use ($q) {
                if ($q) {
                    $qsub->where('doc_number', 'like', "%{$q}%")
                        ->orWhereHas('deal_code', function ($q1) use ($q) {
                            $q1->where('code', 'like', "%{$q}%");
                        })
                        ->orWhereHas('customername', function ($q2) use ($q) {
                            $q2->where('name', 'like', "%{$q}%");
                        })
                        ->orWhereHas('salesman', function ($q3) use ($q) {
                            $q3->where('full_name', 'like', "%{$q}%");
                        });

                }
            });

            if ($formattedDate) {
                // Combine inside same group
                $query->orWhere(function ($q2) use ($formattedDate) {
                    $q2->whereDate('doc_date', $formattedDate);
                });
            }
        });




        $amc_list = $query->get();






        return response()->json($amc_list);
    }


    public function add()
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::orderby('sort_id', 'asc')->get();
            $product = SysHelper::get_product_list($company_id);

            $staff_query = SmStaff::select('user_id', 'full_name');
            if (Auth::user()->role_id != 1 && Auth::user()->role_id != 35) {
                if (Auth::user()->role_id == 3) { //Department Head
                    $users = SmStaff::select('user_id')->where('department_id', session('logged_session_data.department_id'))->get();
                    foreach ($users as $value) {
                        $userid[] = $value->user_id;
                    }
                    $staff_query->wherein('user_id', $userid);
                } else {
                    $staff_query->where('user_id', Auth::user()->id);
                }
            }
            $staff_query->wherein('company_id', $company_id);
            $staff_query->where('active_status', 1);
            $staff = $staff_query->get();

            $items = SysHelper::get_product_list($company_id);


            $brand = SysBrand::select('title')->orderby('title', 'asc')->get();
            $country = SysCountries::select('id', 'name', 'iso2')->get();

            $query = SysCrmDeals::select('id', 'code', 'deal_name', 'estimated_close_date', 'date', 'stage', 'deal_currency', 'company_id', 'deal_value', 'deal_profit', 'created_at', 'updated_at', 'cust_id', 'owner', 'deleted_at', 'quote_id')->where('stage', '!=', 0);


            if (session('logged_session_data.company_id') != 1) {
                $query->wherein('company_id', $company_id);
            }

            $companylist = SysCompany::wherein('id', $company_id)->orderby('sort_id', 'asc')->get();
            $currencylist = SysCurrencySettings::select('id', 'code')->where('status', 1)->orderBy('code', 'ASC')->get();
            $paymentterms = SysPaymentTerms::all();

            $deals = $query->orderby('id', 'desc')->paginate(200);

            $rolearray = [1, 28, 27, 10, 3, 2, 4, 29, 26, 9, 30, 8, 32];
            if (in_array(Auth::user()->role_id, $rolearray)) {
                $sales_person = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
                $vendors = SysHelper::get_customer_list_deal_lead_all_role();
            } else {
                $vendors = SysHelper::get_customer_list_deal_lead();
                $sales_person = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->orderby('full_name', 'asc')->get();
            }


            $deal_company = SysCompany::find(session('logged_session_data.company_id'));




            $designation = SmDesignation::select('title')->where('active_status', 1)->get();



            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_sales($company_id);

            $supplier = SysHelper::get_supplier_list($company_id);



            $cart = SysCrmQuoteCart::where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id])->get();

            // dd($cart);



            return compact('currency', 'vendors', 'company', 'staff', 'brand', 'country', 'product', 'deals', 'currencylist', 'paymentterms', 'companylist', 'deal_company', 'designation', 'sales_person', 'customs_freight_account', 'supplier', 'items', 'cart');
            // return view('backEnd.crm.DealForm', compact('currency', 'vendors', 'company', 'staff', 'brand', 'country', 'product', 'deals', 'currencylist', 'paymentterms', 'companylist', 'deal_company', 'designation', 'sales_person', 'customs_freight_account', 'supplier', 'items', 'cart'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function getQuoteEdit($id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $proforma_invoice = SysProformaInvoice::find($id);

            $deal_id = $proforma_invoice->deal_id;





            $edit = SysCrmDeals::where('id', $deal_id)->first();

            $quote_id = $edit->quote_id;

            $quotationitems = SysCrmQuoteItems::where('deal_id', $deal_id)->where('quote_id', $quote_id)->orderby('sort_id', 'ASC')->get();



            $edit_cfc = SysCrmQuoteCharges::where('deal_id', $deal_id)->where('quote_id', $quote_id)->get();


            $companylist = SysCompany::wherein('id', $company_id)->orderby('sort_id', 'asc')->get();
            $paymentterms = SysPaymentTerms::all();

            $currencylist = SysCurrencySettings::select('id', 'code', 'ex_rate')->where('status', 1)->orderBy('code', 'ASC')->get();
            $currencylist2 = DB::table('sys_currency_rate as r')->select('r.id', 'r.from_currency', 'r.to_currency', 'c.code', 'r.rate')
                ->join('sys_currency as c', 'c.id', 'r.to_currency')
                ->where('r.status', 1)->where('r.from_currency', $edit->deal_currency)
                ->orderBy('c.code', 'ASC')->get();

            $currency_id = $edit->deal_currency;

            $addressbook = SysCustSupplAddressbook::where('cust_suppl_id', $edit->id)->orderBy('id', 'desc')->first();

            $currency = SysCurrencySettings::select('id', 'code')->get();
            $product = SysHelper::get_product_list($company_id);
            $company = SysCompany::orderby('sort_id', 'asc')->get();


            $com_id = session('logged_session_data.company_id');

            $staff = SysHelper::get_sales_persons();

            

            $rolearray = [1, 28, 27, 10, 3, 2, 4, 29, 26, 9, 30, 8, 32];
            if (in_array(Auth::user()->role_id, $rolearray)) {
                $sales_person = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
                $vendors = SysHelper::get_customer_list_deal_lead_all_role();
            } else {
                $vendors = SysHelper::get_customer_list_deal_lead();
                $sales_person = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->orderby('full_name', 'asc')->get();
            }

            $brand = SysBrand::select('title')->orderby('title', 'asc')->get();
            $country = SysCountries::select('id', 'name')->get();

            if (session('logged_session_data.company_id') == 1) {
                $deal_company = "";
            } else {
                $deal_company = SysCompany::find(session('logged_session_data.company_id'))->company_name;
            }
            $comments = SysCrmDealsComments::with('createdby:id,user_id,first_name,last_name')->where('deal_id', $deal_id)->orderBy('id', 'DESC')->get();
            $cust_supp = SysHelper::get_customer_list_deal_lead_all_role();
            $countries = SysCountries::all();
            $states = SysStates::all();

            $deal_track = SysCrmDealTrack::where('deal_id', $deal_id)->orderby('id', 'desc')->first();
            $check_edit_fullfill = 0;
            if (isset($deal_track)) {
                if ($deal_track->invoice == 1) {
                    $check_invoice_approved = 1;
                }
                if ($deal_track->accounts == 1 && $deal_track->sales == 1) {
                    $check_edit_fullfill = 1;
                }
            }
            $is_amc_item = SysCrmQuoteItems::wherein('product_id', [35657])->where('deal_id', $deal_id)->where('quote_id', $quote_id)->count();

            $to_date = Carbon::now()->format('Y-m-d');
            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_sales($company_id);
            $supplier = SysHelper::get_supplier_list($company_id);
            $deal_track_temp = SysCrmDealTrackTemp::where('deal_id', $deal_id)->orderby('id', 'desc')->first();
            $support = SysCrmSupport::where('deal_id', $deal_id)->get();
            $enduser = SysCrmEndUser::where('deal_id', $deal_id)->first();
            $items = SysHelper::get_product_list($company_id);


            $cart = SysCrmQuoteCartEdit::where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id, 'deal_id' => $deal_id])->get();



            $designation = SmDesignation::select('title')->where('active_status', 1)->get();
            $country = SysCountries::select('id', 'name')->get();

            $customer_type = SysCustomerType::where('status', '=', '1')->get();


            return compact('currency', 'vendors', 'company', 'staff', 'brand', 'country', 'edit', 'product', 'company', 'deal_company', 'paymentterms', 'quotationitems', 'quote_id', 'comments', 'cust_supp', 'countries', 'states', 'check_edit_fullfill', 'is_amc_item', 'addressbook', 'currencylist', 'currencylist2', 'currency_id', 'customs_freight_account', 'supplier', 'edit_cfc', 'deal_track_temp', 'deal_track', 'support', 'enduser', 'items', 'company_id', 'cart', 'sales_person', 'designation', 'country', 'customer_type', 'proforma_invoice');
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }






}
