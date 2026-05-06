<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmInspectingDepartment;
use App\SmItem;
use Illuminate\Http\Request;
use App\SmItemStore;
use App\SmStaff;
use App\SysBrand;
use App\SysChartofAccounts;
use App\SysChartofAccountsTransaction;
use App\SysClearance;
use App\SysCompany;
use App\SysCountries;
use App\SysCrmAmcServiceTable;
use App\SysCrmDeals;
use App\SysCrmDealsComments;
use App\SysCrmDealTrack;
use App\SysCrmDealTrackApprovalAccounts;
use App\SysCrmDealTrackApprovalDelivery;
use App\SysCrmDealTrackApprovalInvoice;
use App\SysCrmDealTrackApprovalPurchease;
use App\SysCrmDealTrackApprovalPurcheaseGrn;
use App\SysCrmDealTrackApprovalReceivables;
use App\SysCrmDealTrackApprovalSales;
use App\SysCrmDealTrackApprovalTechnical;
use App\SysCrmDealTrackTemp;
use App\SysCrmPSServiceTable;
use App\SysCrmPSTableServiceComments;
use App\SysCrmQuoteCharges;
use App\SysCrmQuoteItems;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysDealItemInvoiced;
use App\SysDealPurchaseOrderItems;
use App\SysDeliveryNote;
use App\SysDeliveryNoteItems;
use App\SysDriver;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysJournalVoucher;
use App\SystemNotification;
use App\SysPayment;
use App\SysPaymentTerms;
use App\SysProformaInvoice;
use App\SysPurchaseAuto;
use App\SysPurchaseGRN;
use App\SysPurchaseInvoice;
use App\SysPurchaseOrder;
use App\SysPurchaseOrderItems;
use App\SysPurchaseOrderItemsCart;
use App\SysPurchaseReturn;
use App\SysQuotations;
use App\SysReceipt;
use App\SysSalesInvoice;
use App\SysSalesReturn;
use App\SysShipping;
use App\SysStockIn;
use App\SysStockInSerialNo;
use App\SysSupplierType;
use App\SysCrmEndUser;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;
use Validator;
use App\DealTrackGrnStatus;

class SysCrmDealTrackController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function crmdealtracksubmit(Request $request)
    {




        $lpo_file = "";
        $purchease_quote_file = "";
        $cheque_copy_file = "";

        // if ($request->file('lpo') != "") {
        //     $files = $request->file('lpo');
        //     for ($i = 0; $i < count($files); $i++) {
        //         $file1 = $files[$i];
        //         $lpo_file = md5(time()) . "_lpo_" . $i . "." . $file1->getclientoriginalextension();
        //         $file1->move('public/uploads/crm_deal_track_doc/', $lpo_file);
        //         $lpo[] = $lpo_file;
        //     }
        //     $lpo_file = implode("|", $lpo);
        // }
        // if ($request->file('purchease_quote') != "") {
        //     $files = $request->file('purchease_quote');
        //     for ($i = 0; $i < count($files); $i++) {
        //         $file2 = $files[$i];
        //         $purchease_quote_file = md5(time()) . "_po_" . $i . "." . $file2->getclientoriginalextension();
        //         $file2->move('public/uploads/crm_deal_track_doc/', $purchease_quote_file);
        //         $purchease[] = $purchease_quote_file;
        //     }
        //     $purchease_quote_file = implode("|", $purchease);
        // }
        // if ($request->file('cheque_copy') != "") {
        //     $files = $request->file('cheque_copy');
        //     for ($i = 0; $i < count($files); $i++) {
        //         $file3 = $files[$i];
        //         $cheque_copy_file = md5(time()) . "_cc_" . $i . "." . $file3->getclientoriginalextension();
        //         $file3->move('public/uploads/crm_deal_track_doc/', $cheque_copy_file);
        //         $cheque[] = $cheque_copy_file;
        //     }
        //     $cheque_copy_file = implode("|", $cheque);
        // }



        $uploadPath = public_path('uploads/crm_deal_track_doc/');
        $dealId = $request->deal_id; // Use deal ID or 'new' as fallback

        // --- LPO Upload ---
        if ($request->hasFile('lpo')) {
            $lpoFiles = [];
            foreach ($request->file('lpo') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                // Sanitize name and append -dealID
                $safeName = str_slug($originalName, '_');
                $filename = "{$safeName}-{$dealId}.{$extension}";

                // Move file to destination
                $file->move($uploadPath, $filename);
                $lpoFiles[] = $filename;
            }
            $lpo_file = implode('|', $lpoFiles);
        }

        // --- Purchase Quote Upload ---
        if ($request->hasFile('purchease_quote')) {
            $purchaseFiles = [];
            foreach ($request->file('purchease_quote') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $safeName = str_slug($originalName, '_');
                $filename = "{$safeName}-{$dealId}.{$extension}";

                $file->move($uploadPath, $filename);
                $purchaseFiles[] = $filename;
            }
            $purchease_quote_file = implode('|', $purchaseFiles);
        }

        // --- Cheque Copy Upload ---
        if ($request->hasFile('cheque_copy')) {
            $chequeFiles = [];
            foreach ($request->file('cheque_copy') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $safeName = str_slug($originalName, '_');
                $filename = "{$safeName}-{$dealId}.{$extension}";

                $file->move($uploadPath, $filename);
                $chequeFiles[] = $filename;
            }
            $cheque_copy_file = implode('|', $chequeFiles);
        }



        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

        if ($request->start_date == null || $request->start_date == '') {

            $start_date = date('Y-m-d', time());
            $start_date_only = date('Y-m-d', time());
        } else
            $start_date = $request->start_date;

        if ($request->end_date == null || $request->end_date == '') {

            $end_date = date('Y-m-d', time());
            $end_date_only = date('Y-m-d', time());

        } else
            $end_date = $request->end_date;


        $hasLpoNumber = trim((string) $request->reference_no) !== '';
        if ($hasLpoNumber) {
            SysProformaInvoiceController::re_generate($request->deal_id, $request->reference_no, SysHelper::normalizeToYmd($request->reference_date));
        }

        if ($request->btnSubmit == "save") {


            try {


                DB::beginTransaction();
                $check = SysCrmDealTrackTemp::select('id', 'accounts', 'sales', 'purchease', 'invoice', 'delivery', 'receivables', 'tech')->where('deal_id', $request->deal_id)->first();


                if (isset($check)) {
                    /* geo start */

                    /* geo end */



                    $scd = SysCrmDealTrackTemp::find($check->id);
                    $scd->delivery_date = SysHelper::normalizeToYmd($request->delivery_date);

                    if ($check->accounts != 1) {
                        $scd->payment_terms = $request->payment_terms;
                        $scd->payment_terms_txt = $request->payment_terms_txt;
                    }

                    $scd->payment_mode = $request->payment_mode;
                    if ($request->payment_mode_sec != "") {
                        $scd->payment_mode_sec = $request->payment_mode_sec;
                    }

                    $scd->purchease_required = $request->purchease_approval;
                    $scd->partial_delivery = $request->partial_delivery;
                    $scd->technical = $request->technical;

                    $scd->technical_detail = $request->technical_detail;
                    $scd->remarks = $request->remarks;
                    $scd->special_instruction = $request->special_instruction;
                    $scd->reference_no = $request->reference_no;

                    if ($request->reference_date)
                        $scd->reference_date = SysHelper::normalizeToYmd($request->reference_date);

                    $scd->purchease_approval = $request->purchease_approval;
                    $scd->invoice_approval = $request->invoice_approval;
                    $scd->delivery_approval = $request->delivery_approval;
                    $scd->receivables_approval = $request->receivables_approval;

                    $scd->start_date = $start_date;
                    $scd->end_date = $end_date;
                    $scd->invoicing = $request->amc_invoice ?? null;

                    if ($lpo_file != "") {
                        $scd->lpo = $lpo_file;
                    }
                    if ($purchease_quote_file != "") {
                        $scd->purchease_quote = $purchease_quote_file;
                    }
                    if ($cheque_copy_file != "") {
                        $scd->cheque_copy = $cheque_copy_file;
                    }

                    $scd->updated_by = Auth::user()->id;
                    $scd->updated_at = $trn_time;
                    $scd->save();



                } else {
                    $scd = new SysCrmDealTrackTemp();
                    $scd->deal_id = $request->deal_id;
                    $scd->delivery_date = SysHelper::normalizeToYmd($request->delivery_date);
                    $scd->payment_terms = $request->payment_terms;
                    $scd->payment_terms_txt = $request->payment_terms_txt;

                    $scd->payment_mode = $request->payment_mode;
                    if ($request->payment_mode_sec != "") {
                        $scd->payment_mode_sec = $request->payment_mode_sec;
                    }

                    $scd->lpo = $lpo_file;
                    $scd->purchease_quote = $purchease_quote_file;
                    $scd->cheque_copy = $cheque_copy_file;
                    $scd->purchease_required = $request->purchease_approval;
                    $scd->partial_delivery = $request->partial_delivery;
                    $scd->technical = $request->technical;
                    $scd->technical_detail = $request->technical_detail;
                    $scd->remarks = $request->remarks;
                    $scd->special_instruction = $request->special_instruction;
                    $scd->reference_no = $request->reference_no;
                    if ($request->reference_date)
                        $scd->reference_date = SysHelper::normalizeToYmd($request->reference_date);

                    $scd->purchease_approval = $request->purchease_approval;
                    $scd->invoice_approval = $request->invoice_approval;
                    $scd->delivery_approval = $request->delivery_approval;
                    $scd->receivables_approval = $request->receivables_approval;
                    $scd->start_date = $start_date;
                    $scd->end_date = $end_date;
                    $scd->invoicing = $request->amc_invoice ?? null;

                    $scd->accounts = 0;
                    $scd->sales = 0;
                    $scd->purchease = 0;
                    $scd->invoice = 0;
                    $scd->delivery = 0;
                    $scd->receivables = 0;
                    $scd->tech = 0;
                    $scd->created_by = Auth::user()->id;
                    $scd->created_at = $trn_time;
                    $scd->created_date = $trn_time;
                    $scd->company_id = session('logged_session_data.company_id');
                    $scd->save();
                    $scd->toArray();
                }



                DB::commit();

                // return ID directly
                return response()->json([
                    'status' => 'save',
                    'id' => $scd->deal_id
                ]);
                Toastr::success('Saved successfully', 'Success');



                return redirect()->back();
            } catch (\Throwable $th) {
                return response()->json(['status' => 'error', 'message' => 'Save Failed']);
                DB::rollBack();
                return $th;
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }



        DB::beginTransaction();
        try {
            $check = SysCrmDealTrack::select('id', 'accounts', 'sales', 'purchease', 'invoice', 'delivery', 'receivables', 'tech')->where('deal_id', $request->deal_id)->first();

            $deal_det_for_serv = SysCrmDeals::where('id', $request->deal_id)->first();

            //Professional Services - 35710
            $quote_det_for_serv = SysCrmQuoteItems::where('deal_id', $request->deal_id)->where('quote_id', $request->quote_id)->where('product_type', 3)->get();


            if ($request->technical == 1 || (count($quote_det_for_serv) > 0)) {

                if (count($quote_det_for_serv) > 0) {
                    $amount = $quote_det_for_serv[0]->price;
                    $deal_description = $quote_det_for_serv[0]->description;
                } else {
                    $amount = 0;
                    $deal_description = $deal_det_for_serv->note;
                }

                SysCrmPSServiceTable::insert(
                    [
                        'doc_number' => SysHelper::get_new_code('sys_crm_ps_service_table', 'PR', 'doc_number'),
                        'deal_id' => $request->deal_id,
                        'date' => Carbon::now('+04:00'),
                        'cust_name' => $deal_det_for_serv->cust_id,
                        'contact_person' => $deal_det_for_serv->cust_name,
                        'mobile' => $deal_det_for_serv->cust_no,
                        'location_of_work' => $deal_det_for_serv->delivery_address,
                        'amount' => $amount,
                        'sales_person' => $deal_det_for_serv->owner,
                        'deal_description' => $request->technical_detail,
                        'status' => 0,
                        'company_id' => $deal_det_for_serv->company_id,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                    ]
                );
            }



            if (isset($check)) {

                $scd = SysCrmDealTrack::find($check->id);
                $scd->delivery_date = SysHelper::normalizeToYmd($request->delivery_date);

                if ($check->accounts != 1) {
                    $scd->payment_terms = $request->payment_terms;
                    $scd->payment_terms_txt = $request->payment_terms_txt;
                }

                $scd->payment_mode = $request->payment_mode;
                if ($request->payment_mode_sec != "") {
                    $scd->payment_mode_sec = $request->payment_mode_sec;
                }

                $scd->purchease_required = $request->purchease_approval;
                $scd->partial_delivery = $request->partial_delivery;
                $scd->technical = $request->technical;
                $scd->technical_detail = $request->technical_detail;
                $scd->remarks = $request->remarks;
                $scd->special_instruction = $request->special_instruction;
                $scd->reference_no = $request->reference_no;
                if ($request->reference_date)
                    $scd->reference_date = SysHelper::normalizeToYmd($request->reference_date);
                $scd->purchease_approval = $request->purchease_approval;
                $scd->invoice_approval = $request->invoice_approval;
                $scd->delivery_approval = $request->delivery_approval;
                $scd->receivables_approval = $request->receivables_approval;
                $scd->start_date = $start_date;
                $scd->end_date = $end_date;
                $scd->invoicing = $request->amc_invoice ?? null;

                if ($lpo_file != "") {
                    $scd->lpo = $lpo_file;
                }
                if ($purchease_quote_file != "") {
                    $scd->purchease_quote = $purchease_quote_file;
                }
                if ($cheque_copy_file != "") {
                    $scd->cheque_copy = $cheque_copy_file;
                }

                if ($check->accounts == 2) {
                    $scd->accounts = 0;
                    SysHelper::Erp_Notify_re_submit(8, $scd->id, $request->deal_id);
                }
                if ($check->sales == 2) {
                    $scd->sales = 0;
                    SysHelper::Erp_Notify_re_submit(27, $scd->id, $request->deal_id);
                }
                if ($check->purchease == 2) {
                    $scd->purchease = 0;
                    SysHelper::Erp_Notify_re_submit(20, $scd->id, $request->deal_id);
                }
                if ($check->invoice == 2) {
                    $scd->invoice = 0;
                    SysHelper::Erp_Notify_re_submit(35, $scd->id, $request->deal_id);
                }
                if ($check->delivery == 2) {
                    $scd->delivery = 0;
                    SysHelper::Erp_Notify_re_submit(34, $scd->id, $request->deal_id);
                }
                if ($check->tech == 2) {
                    $scd->tech = 0;
                    SysHelper::Erp_Notify_re_submit(33, $scd->id, $request->deal_id);
                }
                if ($check->receivables == 2) {
                    $scd->receivables = 0;
                    SysHelper::Erp_Notify_re_submit(2, $scd->id, $request->deal_id);
                }

                $scd->updated_by = Auth::user()->id;
                $scd->updated_at = $trn_time;
                $scd->save();
            } else {


                $is_saved = SysCrmDealTrackTemp::where('deal_id', $request->deal_id)->orderby('id', 'desc')->first();
                if (isset($is_saved)) {


                    $scd = new SysCrmDealTrack();
                    $scd->deal_id = $is_saved->deal_id;
                    $scd->delivery_date = SysHelper::normalizeToYmd($request->delivery_date);
                    $scd->payment_terms = $is_saved->payment_terms;
                    $scd->payment_terms_txt = $is_saved->payment_terms_txt;

                    $scd->payment_mode = $is_saved->payment_mode;
                    if ($is_saved->payment_mode_sec != "") {
                        $scd->payment_mode_sec = $is_saved->payment_mode_sec;
                    }

                    if ($lpo_file != "") {
                        $scd->lpo = $lpo_file;
                    } else {
                        $scd->lpo = $is_saved->lpo;
                    }

                    if ($purchease_quote_file != "") {
                        $scd->purchease_quote = $purchease_quote_file;
                    } else {
                        $scd->purchease_quote = $is_saved->purchease_quote;
                    }

                    if ($cheque_copy_file != "") {
                        $scd->cheque_copy = $cheque_copy_file;
                    } else {
                        $scd->cheque_copy = $is_saved->cheque_copy;
                    }

                    $scd->purchease_required = $is_saved->purchease_approval;
                    $scd->partial_delivery = $is_saved->partial_delivery;
                    $scd->technical = $is_saved->technical;
                    $scd->technical_detail = $is_saved->technical_detail;
                    $scd->remarks = $is_saved->remarks;
                    $scd->special_instruction = $is_saved->special_instruction;
                    $scd->reference_no = $is_saved->reference_no;
                    if ($request->reference_date)
                        $scd->reference_date = $is_saved->reference_date;

                    $scd->purchease_approval = $is_saved->purchease_approval;
                    $scd->invoice_approval = $is_saved->invoice_approval;
                    $scd->delivery_approval = $is_saved->delivery_approval;
                    $scd->receivables_approval = $is_saved->receivables_approval;
                    $scd->start_date = $start_date;
                    $scd->end_date = $end_date;
                    $scd->invoicing = $request->amc_invoice ?? null;

                    $scd->accounts = 0;
                    $scd->sales = 0;
                    $scd->purchease = 0;
                    $scd->invoice = 0;
                    $scd->delivery = 0;
                    $scd->receivables = 0;
                    $scd->tech = 0;
                    $scd->created_by = Auth::user()->id;
                    $scd->created_at = $trn_time;
                    $scd->created_date = $trn_time;
                    $scd->company_id = session('logged_session_data.company_id');
                    $scd->save();
                    $scd->toArray();
                } else {
                    $scd = new SysCrmDealTrack();
                    $scd->deal_id = $request->deal_id;
                    $scd->delivery_date = SysHelper::normalizeToYmd($request->delivery_date);
                    $scd->payment_terms = $request->payment_terms;
                    $scd->payment_terms_txt = $request->payment_terms_txt;

                    $scd->payment_mode = $request->payment_mode;
                    if ($request->payment_mode_sec != "") {
                        $scd->payment_mode_sec = $request->payment_mode_sec;
                    }

                    $scd->lpo = $lpo_file;
                    $scd->purchease_quote = $purchease_quote_file;
                    $scd->cheque_copy = $cheque_copy_file;
                    $scd->purchease_required = $request->purchease_approval;
                    $scd->partial_delivery = $request->partial_delivery;
                    $scd->technical = $request->technical;
                    $scd->technical_detail = $request->technical_detail;
                    $scd->remarks = $request->remarks;
                    $scd->special_instruction = $request->special_instruction;
                    $scd->reference_no = $request->reference_no;

                    if ($request->reference_date)
                        $scd->reference_date = SysHelper::normalizeToYmd($request->reference_date);

                    $scd->purchease_approval = $request->purchease_approval;
                    $scd->invoice_approval = $request->invoice_approval;
                    $scd->delivery_approval = $request->delivery_approval;
                    $scd->receivables_approval = $request->receivables_approval;
                    $scd->start_date = $start_date;
                    $scd->end_date = $end_date;
                    $scd->invoicing = $request->amc_invoice ?? null;

                    $scd->accounts = 0;
                    $scd->sales = 0;
                    $scd->purchease = 0;
                    $scd->invoice = 0;
                    $scd->delivery = 0;
                    $scd->receivables = 0;
                    $scd->tech = 0;
                    $scd->created_by = Auth::user()->id;
                    $scd->created_at = $trn_time;
                    $scd->created_date = $trn_time;
                    $scd->company_id = session('logged_session_data.company_id');
                    $scd->save();
                    $scd->toArray();
                }

                $user = DB::table('sm_staffs')->select('user_id')->where('role_id', 27)->get(); //Accounts
                if (count($user) > 0) {
                    foreach ($user as $u) {
                        SysHelper::exe_web_push($u->user_id, 'Track Received', 'Deal ' . $request->deal_id, 'crm-deal-track-approval/' . $scd->id);
                        SysHelper::Erp_Notify_in($u->user_id, 'Deal ' . $request->deal_id . ' Track Received', $u->user_id, 'http://erp.venushrms.com/crm-deal-track-approval/' . $scd->id . '');
                    }
                }
            }




            DB::table('sys_crm_deals_comments')->insert([
                'deal_id' => $request->deal_id,
                'comments' => "Deal Track Submitted By " . Auth::user()->full_name . " on " . Carbon::now('+04:00')->format('d/m/Y h:i A'),
                'status' => 1,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
            ]);


            $results = 0;

            $deal_det_for_serv->stage = 4;
            $deal_det_for_serv->save();

            DB::commit();


            SysHelper::notify([
                'user_id' => null,
                'type' => 'dealtrack',
                'role' => 'accounts',
                'record_id' => $scd->id,
                'title' => 'Deal Track Submitted',
                'deal_id' => $deal_det_for_serv->code,
                'company_id' => $deal_det_for_serv->company_id,
                'customer_name' => $deal_det_for_serv->cust_id,
                'sales_person' => $deal_det_for_serv->owner,
                'submitted_time' => $deal_det_for_serv->created_at,
                'value' => $deal_det_for_serv->deal_value,
                'message' => 'Deal Track Submitted, Please Take Action In 15 Minutes.',
            ]);


            if ($results == 0) {
                // Toastr::success('Deal Track has been added successfully', 'Success');
                // return ID directly
                return response()->json([
                    'status' => 'approve',
                    'id' => $scd->deal_id
                ]);
                // return redirect('crm-deal-track/' . $request->deal_id . '/view');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                // return redirect()->back();
                return "op failed";
            }
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function crmdealtracksubmitedit(Request $request)
    {
        $lpo_file = "";
        $purchease_quote_file = "";
        $cheque_copy_file = "";

        $uploadPath = public_path('uploads/crm_deal_track_doc/');
        $dealId = $request->deal_id; // Use deal ID or 'new' as fallback

        // --- LPO Upload ---
        if ($request->hasFile('lpo')) {
            $lpoFiles = [];
            foreach ($request->file('lpo') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                // Sanitize name and append -dealID
                $safeName = str_slug($originalName, '_');
                $filename = "{$safeName}-{$dealId}.{$extension}";

                // Move file to destination
                $file->move($uploadPath, $filename);
                $lpoFiles[] = $filename;
            }
            $lpo_file = implode('|', $lpoFiles);
        }

        // --- Purchase Quote Upload ---
        if ($request->hasFile('purchease_quote')) {
            $purchaseFiles = [];
            foreach ($request->file('purchease_quote') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $safeName = str_slug($originalName, '_');
                $filename = "{$safeName}-{$dealId}.{$extension}";

                $file->move($uploadPath, $filename);
                $purchaseFiles[] = $filename;
            }
            $purchease_quote_file = implode('|', $purchaseFiles);
        }

        // --- Cheque Copy Upload ---
        if ($request->hasFile('cheque_copy')) {
            $chequeFiles = [];
            foreach ($request->file('cheque_copy') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $safeName = str_slug($originalName, '_');
                $filename = "{$safeName}-{$dealId}.{$extension}";

                $file->move($uploadPath, $filename);
                $chequeFiles[] = $filename;
            }
            $cheque_copy_file = implode('|', $chequeFiles);
        }







        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');



        DB::beginTransaction();
        try {
            $hasLpoNumber = trim((string) $request->reference_no) !== '';
            if ($hasLpoNumber) {
                SysProformaInvoiceController::re_generate($request->deal_id, $request->reference_no, SysHelper::normalizeToYmd($request->reference_date));
            }

            $check = SysCrmDealTrack::select('id', 'accounts', 'sales', 'purchease', 'invoice', 'delivery', 'receivables', 'tech')->where('deal_id', $request->deal_id)->first();

            $checkps = SysCrmPSServiceTable::where('deal_id', $request->deal_id)->get();
            $deal_det_for_serv = SysCrmDeals::where('id', $request->deal_id)->first();
            //Professional Services - 35710
            $quote_det_for_serv = SysCrmQuoteItems::where('deal_id', $request->deal_id)->where('quote_id', $request->quote_id)->where('product_type', 3)->get();
            if ($request->technical == 1 || (count($quote_det_for_serv) > 0)) {
                if (count($checkps) == 0) {
                    if (count($quote_det_for_serv) > 0) {
                        $amount = $quote_det_for_serv[0]->price;
                        $deal_description = $quote_det_for_serv[0]->description;
                    } else {
                        $amount = 0;
                        $deal_description = $deal_det_for_serv->note;
                    }
                    SysCrmPSServiceTable::insert(
                        [
                            'doc_number' => SysHelper::get_new_code('sys_crm_ps_service_table', 'PR', 'doc_number'),
                            'deal_id' => $request->deal_id,
                            'date' => Carbon::now('+04:00'),
                            'cust_name' => $deal_det_for_serv->cust_id,
                            'contact_person' => $deal_det_for_serv->cust_name,
                            'mobile' => $deal_det_for_serv->cust_no,
                            'location_of_work' => $deal_det_for_serv->delivery_address,
                            'amount' => $amount,
                            'sales_person' => $deal_det_for_serv->owner,
                            'deal_description' => $request->technical_detail,
                            'status' => 0,
                            'company_id' => session('logged_session_data.company_id'),
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
                        ]
                    );
                }
            } else {
                if (count($checkps) > 0) {
                    SysCrmPSServiceTable::where('deal_id', $request->deal_id)->delete();
                    SysCrmPSTableServiceComments::where('ps_id', $checkps[0]->id)->delete();
                }
            }




            if (isset($check)) {

                $scd = SysCrmDealTrack::find($check->id);
                $scd->delivery_date = SysHelper::normalizeToYmd($request->delivery_date);

                if ($check->accounts != 1) {
                    $scd->payment_terms = $request->payment_terms;
                    $scd->payment_terms_txt = $request->payment_terms_txt;
                }

                $scd->payment_mode = $request->payment_mode;
                if ($request->payment_mode_sec != "") {
                    $scd->payment_mode_sec = $request->payment_mode_sec;
                }

                $scd->purchease_required = $request->purchease_approval;
                $scd->partial_delivery = $request->partial_delivery;
                $scd->technical = $request->technical;
                $scd->technical_detail = $request->technical_detail;
                $scd->remarks = $request->remarks;
                $scd->special_instruction = $request->special_instruction;
                $scd->reference_no = $request->reference_no;
                $scd->reference_date = SysHelper::normalizeToYmd($request->reference_date);
                $scd->start_date = SysHelper::normalizeToYmd($request->start_date);
                $scd->end_date = SysHelper::normalizeToYmd($request->end_date);
                $scd->invoicing = $request->amc_invoice ?? null;

                if ($scd->accounts == 0) {
                }
                if ($scd->sales == 0) {
                }
                if ($scd->purchease == 0) {
                    $scd->purchease_approval = $request->purchease_approval;
                }
                if ($scd->invoice == 0) {
                    $scd->invoice_approval = $request->invoice_approval;
                }
                if ($scd->delivery == 0) {
                    $scd->delivery_approval = $request->delivery_approval;
                }
                if ($scd->receivables == 0) {
                    $scd->receivables_approval = $request->receivables_approval;
                }

                if ($lpo_file != "") {
                    $scd->lpo = $lpo_file;
                }
                if ($purchease_quote_file != "") {
                    $scd->purchease_quote = $purchease_quote_file;
                }
                if ($cheque_copy_file != "") {
                    $scd->cheque_copy = $cheque_copy_file;
                }


                $scd->updated_by = Auth::user()->id;
                $scd->updated_at = $trn_time;
                $scd->save();
            }

            $results = 0;
            $deal_det_for_serv->stage = 4;
            $deal_det_for_serv->save();
            DB::commit();

            DB::table('sys_crm_deals_comments')->insert([
                'deal_id' => $request->deal_id,
                'comments' => "Deal Track Form Updated By " . Auth::user()->full_name . " on " . Carbon::now('+04:00')->format('d/m/Y h:i A'),
                'status' => 1,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
            ]);


            if ($results == 0) {
                Toastr::success('Deal Track has been added successfully', 'Success');
                return response()->json([
                    'status' => 'success',
                    'id' => $scd->deal_id
                ]);
                // return redirect('crm-deal-track/' . $request->deal_id . '/view');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function view($id)
    {
        // $check_quote = SysCrmQuoteItems::where('deal_id', $id)->count();
        // if ($check_quote == 0) {
        //     Toastr::error('Quotation Not Found. Please Create Quotation', 'Failed');
        //     return redirect()->back();
        // }
        try {
            //     $paymentterms = SysPaymentTerms::all();
            //     $edit = SysCrmDeals::where('id', $id)->first();
            //     $comments = SysCrmDealsComments::where('deal_id', $id)->orderBy('id', 'DESC')->get();
            //     $quoteitems = SysCrmQuoteItems::where('deal_id', $id)->where('quote_id', $edit->quote_id)->get();
            //     $quote_charges = SysCrmQuoteCharges::where('deal_id', $id)->where('quote_id', $edit->quote_id)->get();

            //     $dealtrack = SysCrmDealTrack::where('deal_id', $id)->first();
            //     //if($id==10686){
            //     //    $addressbook = SysCustSupplAddressbook::where('cust_suppl_id',$edit->cust_id)->where('id',68)->orderBy('id','desc')->first();
            //     //}else{
            //     $addressbook = SysCustSupplAddressbook::where('cust_suppl_id', $edit->cust_id)->where('set_default', 1)->orderBy('id', 'desc')->first();
            //     //}

            //     if (isset($dealtrack)) {
            //         $accounts = SysCrmDealTrackApprovalAccounts::where('deal_track_id', $dealtrack->id)->get();
            //         $sales = SysCrmDealTrackApprovalSales::where('deal_track_id', $dealtrack->id)->get();
            //         $purchease = SysCrmDealTrackApprovalPurchease::where('deal_track_id', $dealtrack->id)->get();
            //         $invoice = SysCrmDealTrackApprovalInvoice::where('deal_track_id', $dealtrack->id)->get();
            //         $delivery = SysCrmDealTrackApprovalDelivery::where('deal_track_id', $dealtrack->id)->get();
            //         $receivables = SysCrmDealTrackApprovalReceivables::where('deal_track_id', $dealtrack->id)->get();
            //         $tech = SysCrmDealTrackApprovalTechnical::where('deal_track_id', $dealtrack->id)->get();
            //     } else {
            //         $accounts = [];
            //         $sales = [];
            //         $purchease = [];
            //         $invoice = [];
            //         $delivery = [];
            //         $receivables = [];
            //         $tech = [];
            //     }


            $dealtrack = SysCrmDealTrack::where('deal_id', $id)->first();

            $trackdata = $this->get_deal_track_data($dealtrack->id);
            return view('backEnd.crm.DealTrack', $trackdata);
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function crmdealtrackapprovallisting()
    {
        try {
            $dealtrack = session('deal_track_query.dealtrack');
            $vendors = session('deal_track_query.vendors');
            $staff = session('deal_track_query.staff');
            $ctrl_deal_id = session('deal_track_query.ctrl_deal_id');
            $ctrl_company_id = session('deal_track_query.ctrl_company_id');
            $ctrl_owner_id = session('deal_track_query.ctrl_owner_id');
            $ctrl_status_id = session('deal_track_query.ctrl_status_id');
            $ctrl_date = session('deal_track_query.ctrl_date');
            $ctrl_partial_delivery = session('deal_track_query.ctrl_partial_delivery');

            return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date', 'ctrl_partial_delivery'));
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function crmdealtrackapprovallist(Request $request, $id = null)
    {
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];

        $rolearray = [1, 28, 27, 10, 3, 2, 4, 29, 26, 9, 30, 8, 32];

        if (in_array(Auth::user()->role_id, $rolearray)) {
            $staff = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
            $vendors = SysHelper::get_customer_list_deal_lead_all_role();
        } else {
            $vendors = SysHelper::get_customer_list_deal_lead();
            $staff = SysHelper::get_sales_persons();
        }
        db::table('sys_deal_sales_invoice_items')->where(['cart_id' => session('logged_session_data.cart_id')])->delete();
        db::table('sys_deal_sales_invoice_items_cart')->where(['cart_id' => session('logged_session_data.cart_id')])->delete();

        //$vendors = SysCustSuppl::select('id', 'code', 'name')->wherein('company_id', $company_id)->where('catid', 1)->orderby('name', 'asc')->get(); // 1 customers, 2 suppliers


        $company_list = SysCompany::select('id', 'company_name')->orderby('sort_id', 'asc')->get();

        $ctrl_deal_id = "";
        $from_date = null;
        $ctrl_company_id2 = "";
        $to_date = null;
        $ctrl_company_id = "";
        $ctrl_owner_id = "";
        $ctrl_status_id = "10";
        $ctrl_date = '';
        $ctrl_partial_delivery = '';
        try {
            $query = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deals.deal_value', 'sys_crm_deals.deal_currency')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id');
            /*
            //accounts
            if(session('logged_session_data.designation_id')==8){
                $query = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deals.deal_value','sys_crm_deals.deal_currency')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id');
            }
            //sales
            if(session('logged_session_data.designation_id')==27){
                $query = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deals.deal_value','sys_crm_deals.deal_currency')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where([['accounts','=',1]]);
            }
            //purchease
            if(session('logged_session_data.designation_id')==20){
                $query = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deals.deal_value','sys_crm_deals.deal_currency')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where([['accounts','=',1],['sales','=',1]]);
            }
            //invoice
            if(session('logged_session_data.designation_id')==35){
                $query = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deals.deal_value','sys_crm_deals.deal_currency','sys_crm_deal_track_approval_invoice.invoice_no')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_track_id','sys_crm_deal_track.id')->where([['accounts','=',1],['sales','=',1]])->wherein('purchease',[1,4]);
            }
            //delivery
            if(session('logged_session_data.designation_id')==34){
                $query = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deals.deal_value','sys_crm_deals.deal_currency')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where([['accounts','=',1],['sales','=',1],['purchease','=',1],['invoice','=',1]]);
            }
            //technical
            if(session('logged_session_data.designation_id')==33){
                $query = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deals.deal_value','sys_crm_deals.deal_currency')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where([['accounts','=',1],['sales','=',1],['invoice','=',1],['delivery','=',1]])
                ->where('sys_crm_deal_track.technical', 1)->where('sys_crm_deal_track.tech','!=', 1);
            }
            //receivables
            if(session('logged_session_data.designation_id')==2){
                $query = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deals.deal_value','sys_crm_deals.deal_currency')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where([['accounts','=',1],['sales','=',1],['purchease','=',1],['invoice','=',1],['delivery','=',1]])
                ->where(function($query) {$query->where('sys_crm_deal_track.technical', 0)->orwhere('sys_crm_deal_track.tech',1);});
            }
            */
            //if($_POST){
            if (count($request->all()) > 0) {

                if ($request->company_id2 != "") {
                    $query->where('sys_crm_deals.company_id', $request->company_id2);
                    $ctrl_company_id2 = $request->company_id2;
                }

                if ($request->deal_id != "") {
                    $query->where('sys_crm_deals.code', $request->deal_id);
                    $ctrl_deal_id = $request->deal_id;
                }
                if ($request->company_id != "") {
                    $query->where('sys_crm_deals.cust_id', $request->company_id);
                    $ctrl_company_id = $request->company_id;
                }
                if ($request->owner_id != "") {
                    $query->where('sys_crm_deals.owner', $request->owner_id);
                    $ctrl_owner_id = $request->owner_id;
                }
                if ($request->date != "") {

                    $query->whereRaw("DATE_FORMAT(sys_crm_deals.created_at, '%Y-%m-%d') = '" . SysHelper::normalizeToYmd($request->date) . "'");

                    $ctrl_date = $request->date;
                }


                if (!empty($request->from_date)) {
                    $from_date = SysHelper::normalizeToYmd($request->from_date);
                    $to_date = !empty($request->to_date)
                        ? SysHelper::normalizeToYmd($request->to_date)
                        : '';

                        if(!empty($to_date)){
                            $query->whereBetween(DB::raw("DATE(sys_crm_deal_track.created_at)"), [$from_date, $to_date]);
                        }else{
                            //created after date
                            $query->whereDate('sys_crm_deal_track.created_at','>=', $from_date);
                        }

                }





                if ($request->status_id != "" && $request->status_id != "10") {
                    $ctrl_status_id = $request->status_id;

                    $track = str_split($request->status_id, 1)[0];
                    $status = str_split($request->status_id, 1)[1];
                    //accounts
                    if ($track == "A") {
                        $query->where('sys_crm_deal_track.accounts', $status);
                        $query->where('sys_crm_deal_track.sales', 0);
                        $query->where('sys_crm_deal_track.purchease', 0);
                        $query->where('sys_crm_deal_track.invoice', 0);
                        $query->where('sys_crm_deal_track.delivery', 0);
                        $query->where('sys_crm_deal_track.receivables', 0);
                    }
                    //sales
                    else if ($track == "S") {
                        $query->where('sys_crm_deal_track.sales', $status);
                        $query->where('sys_crm_deal_track.purchease', 0);
                        $query->where('sys_crm_deal_track.invoice', 0);
                        $query->where('sys_crm_deal_track.delivery', 0);
                        $query->where('sys_crm_deal_track.receivables', 0);
                    }
                    //purchease
                    else if ($track == "P") {
                        $query->where('sys_crm_deal_track.purchease', $status);
                        $query->where('sys_crm_deal_track.invoice', 0);
                        $query->where('sys_crm_deal_track.delivery', 0);
                        $query->where('sys_crm_deal_track.receivables', 0);
                    }
                    //invoice
                    else if ($track == "I") {
                        $query->where('sys_crm_deal_track.invoice', $status);
                        $query->where('sys_crm_deal_track.delivery', 0);
                        $query->where('sys_crm_deal_track.receivables', 0);
                    }
                    //delivery
                    else if ($track == "D") {
                        $query->where('sys_crm_deal_track.delivery', $status);
                        $query->where('sys_crm_deal_track.receivables', 0);
                    }
                    //receivables
                    else if ($track == "R") {
                        $query->where('sys_crm_deal_track.receivables', $status);
                    } else if ($track == "Z") {
                        $query->where('sys_crm_deal_track.partial_delivery', $status);
                        $query->where('sys_crm_deal_track.delivery', '!=', 1);

                    } else {
                        if ($request->status_id == 0) {
                            $query->orwhere('sys_crm_deal_track.accounts', $request->status_id);
                        } else {
                            $query->orwhere('sys_crm_deal_track.accounts', $request->status_id);
                            $query->orwhere('sys_crm_deal_track.sales', $request->status_id);
                            $query->orwhere('sys_crm_deal_track.purchease', $request->status_id);
                            $query->orwhere('sys_crm_deal_track.invoice', $request->status_id);
                            $query->orwhere('sys_crm_deal_track.delivery', $request->status_id);
                            $query->orwhere('sys_crm_deal_track.receivables', $request->status_id);
                        }
                    }
                }


            } else {
                $query->where('sys_crm_deal_track.receivables', '!=', 1);
            }

            if (session('logged_session_data.company_id') != 1) {
                $query->wherein('sys_crm_deal_track.company_id', $company_id);
            }

            //$query->wherein('r.created_by',$r[1]);

            if (Auth::user()->role_id == 5) {
                $query->where('sys_crm_deal_track.created_by', Auth::user()->id);
            }

            /*if(session('logged_session_data.designation_id')==2){
                $query->where('sys_crm_deals.stage','=', 4);
                $dealtrack = $query->orderby('receivables','asc')->orderby('id','desc')->paginate(50);
            }else{*/
            $query->where('sys_crm_deals.stage', '=', 4);


            if ($_POST) {
                $dealtrack = $query->orderby('sys_crm_deal_track.id', 'desc')->paginate(500000);
            } else {
                $dealtrack = $query->orderby('sys_crm_deal_track.id', 'desc')->paginate(100);
            }

            /*}*/

            if ($id == null) {
                if ($dealtrack->first())
                    $id = $dealtrack->first()->id;
            }




            $trackdata = $this->get_deal_track_data($id);

            $active_id = $id;


            return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date', 'ctrl_partial_delivery', 'company_list', 'ctrl_company_id2', 'trackdata', 'active_id', 'from_date', 'to_date'));

            /*$form_data = [
                'dealtrack' => $dealtrack,
                'vendors' => $vendors,
                'staff' => $staff,
                'ctrl_deal_id' => $ctrl_deal_id,
                'ctrl_company_id' => $ctrl_company_id,
                'ctrl_owner_id' => $ctrl_owner_id,
                'ctrl_status_id' => $ctrl_status_id,
                'ctrl_date' => $ctrl_date,
                'ctrl_partial_delivery' => $ctrl_partial_delivery,
            ];
            session()->put('deal_track_query', $form_data);
            return redirect('crm-deal-track-approval-listing');*/
            //return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function search(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $query = $request->get('query');

            $invoices = DB::table('sys_crm_deals as d')->select('sys_crm_deal_track.id', 'd.code', 'd.deal_name', 'd.date', 'd.deal_currency', 'd.company_id', 'd.deal_value', 'd.deal_profit', 'd.quote_id', 'sys_currency.code as currency_code', 'sys_cust_suppl.code as account_code', 'sys_cust_suppl.name as account_name')->where('d.stage', '!=', 0)
                ->join('sys_currency', 'sys_currency.id', '=', 'd.deal_currency')
                ->join('sys_crm_deal_track', 'sys_crm_deal_track.deal_id', '=', 'd.id')
                ->join('sys_cust_suppl', 'sys_cust_suppl.id', '=', 'd.cust_id')
                ->when(session('logged_session_data.company_id') != 1, function ($q) use ($company_id) {
                    $q->where('d.company_id', $company_id);
                })
                ->where(function ($q) use ($query) {
                    $q->where('d.code', 'LIKE', "%{$query}%")
                        ->orWhere('d.deal_name', 'LIKE', "%{$query}%")
                        ->orWhere('sys_cust_suppl.name', 'LIKE', "%{$query}%")
                        ->orWhere('sys_cust_suppl.code', 'LIKE', "%{$query}%");
                })
                ->orderBy('d.id', 'desc')
                ->limit(20)
                ->get();
            return response()->json($invoices);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getDetails($id)
    {
        $data = $this->get_deal_track_data($id);
        if (count($data) > 0) {
            return view('backEnd.crm.DealTrackApprovalDetail', $data);
        } else {
            return "error!!";
        }

    }
    public function get_deal_track_data($id)
    {
        try {
            SysPurchaseOrderItemsCart::where(['cart_id' => session('logged_session_data.cart_id')])->delete();
            SysDealPurchaseOrderItems::where(['cart_id' => session('logged_session_data.cart_id')])->delete();

            $deal = SysCrmDealTrack::find($id);
            if(!$deal){
                return [];
            }
            $del = SysCrmDeals::where('id', $deal->deal_id)->first();
            $cust = SysCustSuppl::where('id', $del->cust_id)->first();
            $account_id = SysChartofAccounts::where('account_code', $cust->code)->first();

            $paymentterms = SysPaymentTerms::orderby('title', 'asc')->get();

            $comments = SysCrmDealsComments::where('deal_id', $deal->deal_id)->orderBy('id', 'DESC')->get();
            $dn_detail = SysDeliveryNote::select('id', 'doc_number')->where('deal_id', $deal->deal_id)->get();
            if (count($dn_detail) > 0) {
                $dn_items = SysDeliveryNoteItems::select('part_number', db::raw('sum(qty) as dn_qty'))->wherein('dn_id', $dn_detail->pluck('id'))->groupby('part_number', 'refid')->get();
                if (count($dn_items) > 0) {
                    foreach ($dn_items as $dni) {
                        SysCrmQuoteItems::where('deal_id', $deal->deal_id)->where('quote_id', $del->quote_id)->where('product_id', $dni->part_number)->where('id', $dni->refid)
                            ->update(['dn_qty' => $dni->dn_qty]);
                    }
                }
            }
            $quoteitems = SysCrmQuoteItems::where('deal_id', $deal->deal_id)->where('quote_id', $del->quote_id)->orderby('sort_id', 'asc')->get();
            $enduser = SysCrmEndUser::where('deal_id', $deal->deal_id)->first();


            $quote_charges = SysCrmQuoteCharges::where('deal_id', $deal->deal_id)->where('quote_id', $del->quote_id)->get();

            $check_po = SysPurchaseOrder::where('deal_id', $deal->deal_id)->where('status', 1)->get();
            $check_dn = SysDeliveryNote::where('deal_id', $deal->deal_id)->where('status', 1)->get();
            $check_si = SysSalesInvoice::where('deal_id', $deal->deal_id)->where('status', 1)->get();
            $check_cl = SysClearance::where('deal_id', $deal->deal_id)->where('status', 1)->get();
            // $items_in_po=[];
            // if(count($check_po)>0){
            //     $items_in_po = SysPurchaseOrderItems::select('part_number',db::raw('sum(qty) as qty'))->wherein('po_id',$check_po->pluck('id'))->groupby('part_number','unitprice')->get();
            // }

            $quoteitems_invoiced = SysDealItemInvoiced::where('deal_id', $deal->deal_id)->sum('qty');
            //$sales_invoice = SysSalesInvoice::where('deal_id',$deal->deal_id)->count();

            $accounts = SysCrmDealTrackApprovalAccounts::where('deal_track_id', $id)->get();
            $sales = SysCrmDealTrackApprovalSales::where('deal_track_id', $id)->get();
            $purchease = SysCrmDealTrackApprovalPurchease::where('deal_track_id', $id)->get();

            $po_detail = DB::table('sys_purchase_order as po')
                ->select(
                    'po.doc_number',
                    'po.currency',
                    'ca.account_name',
                    DB::raw('GROUP_CONCAT(poi.part_number) as part_numbers'),
                    // get the first part_number (by poi.id order) as first_part_number
                    DB::raw("SUBSTRING_INDEX(GROUP_CONCAT(poi.part_number ORDER BY poi.id ASC), ',', 1) as first_part_number"),
                    DB::raw('SUM(taxableamount) + SUM(vatamount) as amount')
                )
                ->join('sys_purchase_order_items as poi', 'poi.po_id', 'po.id')
                ->join('sys_chartofaccounts as ca', 'ca.id', 'po.vendors')
                ->where('deal_id', $deal->deal_id)
                ->groupBy('po.doc_number', 'po.currency', 'ca.account_name')
                ->get();




            // $po_detail = DB::table('sys_purchase_order as po')->select('po.doc_number', 'po.currency', 'ca.account_name', 'poi.part_number', DB::raw('sum(taxableamount) + sum(vatamount) as amount'))
            //     ->join('sys_purchase_order_items as poi', 'poi.po_id', 'po.id')
            //     ->join('sys_chartofaccounts as ca', 'ca.id', 'po.vendors')
            //     ->where('deal_id', $deal->deal_id)->groupby('po.doc_number', 'po.currency', 'ca.account_name', 'poi.part_number')->get();

            $invoice = SysCrmDealTrackApprovalInvoice::where('deal_track_id', $id)->get();
            $invo_detail = SysSalesInvoice::select('doc_number')->where('deal_id', $deal->deal_id)->get();

            $delivery = SysCrmDealTrackApprovalDelivery::where('deal_track_id', $id)->get();

            $receivables = SysCrmDealTrackApprovalReceivables::where('deal_track_id', $id)->get();

            $r_deal_id = $deal->deal_id;
            $receipt_details = SysReceipt::select('sys_receipt.*', 'cat.debit_amount', 'ra.bi_doc_no')
                ->join('sys_chartofaccounts_transaction as cat', 'cat.transaction_no', 'sys_receipt.doc_number')
                ->leftjoin('sys_receipt_adjustments as ra', 'ra.bi_doc_number', 'sys_receipt.doc_number')
                ->wherein('cat.transaction_type', ['bankreceipt', 'cashreceipt'])->where('is_main_account', 1)->where(
                    function ($query) use ($r_deal_id) {
                        $query->orWhereRaw("FIND_IN_SET($r_deal_id, deal_id) > 0");
                    }
                )->get();

            $tech = SysCrmDealTrackApprovalTechnical::where('deal_track_id', $id)->get();

            $shipping = SysCustSuppl::where('company_id',session('logged_session_data.company_id') )->where('catid', 2)->where('account_type',3)->orderby('name', 'asc')->get(); // 1 customers, 2 suppliers
            
            // $shipping = SysShipping::select('id', 'shipping_name')->where('status', 1)->get();
            $driver = SysDriver::where('status', 1)->get();
            if ($deal->deal_id == 10686) {
                $addressbook = SysCustSupplAddressbook::where('cust_suppl_id', $del->cust_id)->where('id', 68)->orderBy('id', 'desc')->first();
            } else {
                $addressbook = SysCustSupplAddressbook::where('cust_suppl_id', $del->cust_id)->where('set_default', 1)->orderBy('id', 'desc')->first();
            }
            $receivable_amount_sum = 0;
            $check_receipt = DB::table('sys_receipt as r')->select('r.doc_number', 'r.id', db::raw('sum(c.debit_amount) as amount'))
                ->join('sys_chartofaccounts_transaction as c', 'c.transaction_no', 'r.doc_number')
                ->where(
                    function ($query) use ($r_deal_id) {
                        $query->orWhereRaw("FIND_IN_SET($r_deal_id, deal_id) > 0");
                    }
                )->groupby('r.doc_number', 'r.id')->get();
            $check_jv = DB::table('sys_journalvoucher as j')->select('j.doc_number', 'j.id', db::raw('sum(c.debit_amount) as d_amount'), db::raw('sum(c.credit_amount) as c_amount'))
                ->join('sys_chartofaccounts_transaction as c', 'c.transaction_no', 'j.doc_number')
                ->where('j.deal_id', $deal->deal_id)->groupby('j.doc_number', 'j.id')->get();

            // dd($check_jv);

            if (count($receivables) > 0) {
                $receivable_amount_sum += $receivables->sum('amount');
                $receivable_amount_sum += $receivables->sum('amount2');
                $receivable_amount_sum += $receivables->sum('amount3');
            }
            $currencylist = SysCurrencySettings::select('id', 'code')->where('status', 1)->orderBy('code', 'ASC')->get();


            //vat amount set as deal value
            $extra_charges = 0;
            $quote_charges = SysCrmQuoteCharges::where('deal_id', $del->id)->where('quote_id', $del->quote_id)->get();


            $purchase_auto = SysPurchaseAuto::where(['deal_id' => $del->id, 'status' => 2, 'req_cost' => 1])->pluck('po_id');
            $purchase_cost = 0;
            if (count($purchase_auto) > 0) {
                $purchase_cost = SysPurchaseOrderItems::select(DB::raw('sum(taxableamount) as total_cost'))->wherein('po_id', $purchase_auto)->get();
                $purchase_cost = $purchase_cost[0]->total_cost;
            }

            //return $quote_charges;

            if (count($quote_charges) > 0) {
                $extra_charges = $quote_charges->sum('amount');
            }

            $net = 0;
            $vat = 0;
            $curr = 1;
            $delivery_date = '';
            $deal_profit = 0;
            $deal_cost = 0;
            $check_edit_fullfill = 0;



            SysHelper::set_deal_profit($del->id);

            $list_performa_invoice = SysProformaInvoice::where('deal_id', $del->id)->get();
            $list_sales_invoice = SysSalesInvoice::where('deal_id', $del->id)->get();
            $list_delivery_note = SysDeliveryNote::where('deal_id', $del->id)->get();
            $list_sales_return = SysSalesReturn::where('deal_id', $del->id)->get();
            $list_receipt = SysReceipt::where('deal_id', $del->id)->get();
            $list_purchase_order = SysPurchaseOrder::where('deal_id', $del->id)->get();
            $list_goods_receipt_note = SysPurchaseGRN::where('deal_id', $del->id)->get();
            $list_purchase_invoice = SysPurchaseInvoice::where('deal_id', $del->id)->get();
            $list_purchase_return = SysPurchaseReturn::where('deal_id', $del->id)->get();
            $list_payment = SysPayment::where('deal_id', $del->id)->get();
            //$list_journalvoucher = SysJournalVoucher::where('deal_id',$del->id)->get();
            $list_journalvoucher = SysChartofAccountsTransaction::where('transaction_ref', $del->id)->get();

            $list_journalvoucher_det = [];
            $list_journalvoucher_det_other = [];
            if (count($list_journalvoucher) > 0) {
                $list_journalvoucher_det = DB::table('sys_chartofaccounts_transaction as t')->select('t.id', 't.account_id', 't.transaction_no', 't.debit_amount', 't.credit_amount', 't.remarks', 'c.account_name')
                    ->join('sys_chartofaccounts as c', 'c.id', 't.account_id')->where('t.transaction_ref', $del->id)->wherein('t.transaction_no', $list_journalvoucher->pluck('transaction_no'))->get();

                $list_journalvoucher_det_other = DB::table('sys_chartofaccounts_transaction as t')->select('t.id', 't.account_id', 't.transaction_no', 't.debit_amount', 't.credit_amount', 't.remarks', 'c.account_name')
                    ->join('sys_chartofaccounts as c', 'c.id', 't.account_id')->wherein('t.transaction_no', $list_journalvoucher->pluck('transaction_no'))->get();
            }

            //return $list_journalvoucher_det;
            $paymentmode_cash = SysHelper::get_cash_account();
            $paymentmode_bank = SysHelper::get_bank_account();

            $poitems = [];

            $get_auto = DB::table('sys_purchase_auto')->where('deal_id', $del->id)->where('status', 2)->pluck('po_no');

            if (count($get_auto) > 0) {

                $poitems = SysPurchaseOrderItems::select('sys_purchase_order_items.*', 'it.part_number as partno')
                    ->join('sys_purchase_order as po', 'po.id', 'sys_purchase_order_items.po_id')
                    ->join('sm_items as it', 'it.id', 'sys_purchase_order_items.part_number')
                    ->wherenotin('sys_purchase_order_items.part_number', $quoteitems->pluck('product_id'))
                    ->wherein('po.doc_number', $get_auto)->get();

            }

            //fetch purchase order items unique from all 
            if ($check_po) {

                /*
                |--------------------------------------------------------------------------
                | 1. Get PO items (grouped by part_number + unitprice)
                |--------------------------------------------------------------------------
                */
                $purchase_order_items = SysPurchaseOrderItems::select(
                    'part_number',
                    'unitprice',
                   
                    DB::raw('SUM(qty) as total_qty')
                )
                    ->whereIn('po_id', $check_po->pluck('id'))
                    ->groupBy('part_number', 'unitprice')
                    ->get();

                

                /*
                |--------------------------------------------------------------------------
                | 2. Get Quote items (grouped by product_id + cost)
                |--------------------------------------------------------------------------
                */
                $quote_items_qty = SysCrmQuoteItems::select(
                    'product_id',
                    'cost',
                    DB::raw('SUM(qty) as total_qty')
                )
                    ->where('deal_id', $deal->deal_id)
                    ->where('quote_id', $del->quote_id)
                    ->groupBy('product_id', 'cost')
                    ->get();


                /*
                |--------------------------------------------------------------------------
                | 3. Map quote items by composite key (product_id + cost)
                |--------------------------------------------------------------------------
                */
                $quote_items_map = $quote_items_qty->keyBy(function ($item) {
                    return $item->product_id . '_' . $item->cost;
                });


                /*
                |--------------------------------------------------------------------------
                | 4. Compare PO vs Quote using (part_number + unitprice)
                |--------------------------------------------------------------------------
                */
                $excess_po_items = $purchase_order_items
                    ->map(function ($po) use ($quote_items_map) {

                        $key = $po->part_number . '_' . $po->unitprice;

                
                        $quoteQty = $quote_items_map->get($key)->total_qty ?? 0;

                        if ($po->total_qty > $quoteQty) {
                            return [
                                'part_number' => $po->part_number,
                                'unitprice' => $po->unitprice,
                                'po_qty' => $po->total_qty,
                                'quote_qty' => $quoteQty,
                                'excess_qty' => $po->total_qty - $quoteQty,
                                'taxableamount' => $po->taxableamount,
                                'vatamount' => $po->vatamount,
                            ];
                        }

                        return null;
                    })
                    ->filter()
                    ->values();


            }


            $dnitem_set = SysDeliveryNoteItems::selectRaw('SUM(qty) as qty, part_number')
                ->join('sys_delivery_note', 'sys_delivery_note.id', '=', 'sys_delivery_note_items.dn_id')
                ->where('sys_delivery_note.deal_id', $del->id)
                ->groupBy('part_number')
                ->get();

            if (count($dnitem_set) > 0) {
                //SysCrmQuoteItems::where('deal_id', $deal->deal_id)->where('quote_id', $del->quote_id)
                //->update(['dn_qty'=> 0]);

                foreach ($dnitem_set as $item) {
                    //SysCrmQuoteItems::where('deal_id', $deal->deal_id)->where('quote_id', $del->quote_id)
                    //->where('product_id',$item->part_number)->update(['dn_qty'=> $item->qty]);
                }
            }

            $dnitems = SysDeliveryNoteItems::select('sys_delivery_note_items.*', 'it.part_number as partno', 'it.description')
                ->join('sys_delivery_note', 'sys_delivery_note.id', 'sys_delivery_note_items.dn_id')
                ->join('sm_items as it', 'it.id', 'sys_delivery_note_items.part_number')
                ->where('sys_delivery_note.deal_id', $del->id)
                ->where('sys_delivery_note_items.is_deal_aditional', 1)->get();


            $data = [

                'deal' => $deal,
                'accounts' => $accounts,
                'sales' => $sales,
                'purchease' => $purchease,
                'invoice' => $invoice,
                'delivery' => $delivery,
                'receivables' => $receivables,
                'tech' => $tech,
                'cust' => $cust,
                'del' => $del,
                'comments' => $comments,
                'quoteitems' => $quoteitems,
                'shipping' => $shipping,
                'driver' => $driver,
                'addressbook' => $addressbook,
                'paymentterms' => $paymentterms,
                'currencylist' => $currencylist,
                'po_detail' => $po_detail,
                'invo_detail' => $invo_detail,
                'dn_detail' => $dn_detail,
                'receipt_details' => $receipt_details,
                'quoteitems_invoiced' => $quoteitems_invoiced,
                'check_receipt' => $check_receipt,
                'check_po' => $check_po,
                'check_si' => $check_si,
                'check_dn' => $check_dn,
                'receivable_amount_sum' => $receivable_amount_sum,
                'quote_charges' => $quote_charges,
                'account_id' => $account_id,
                'list_performa_invoice' => $list_performa_invoice,
                'list_sales_invoice' => $list_sales_invoice,
                'list_delivery_note' => $list_delivery_note,
                'list_sales_return' => $list_sales_return,
                'list_receipt' => $list_receipt,
                'list_purchase_order' => $list_purchase_order,
                'list_goods_receipt_note' => $list_goods_receipt_note,
                'list_purchase_invoice' => $list_purchase_invoice,
                'list_purchase_return' => $list_purchase_return,
                'list_payment' => $list_payment,
                'list_journalvoucher' => $list_journalvoucher,
                'check_jv' => $check_jv,
                'list_journalvoucher_det' => $list_journalvoucher_det,
                'list_journalvoucher_det_other' => $list_journalvoucher_det_other,
                'paymentmode_cash' => $paymentmode_cash,
                'paymentmode_bank' => $paymentmode_bank,
                'poitems' => $poitems,
                'dnitems' => $dnitems,
                'check_cl' => $check_cl,
                'enduser' => $enduser,
                'excess_po_items' => $excess_po_items,

            ];
            return $data;
        } catch (\Throwable $th) {
            dd($th);
            return [];
        }
    }



    public function crmdealtrackapprovallistsort(Request $request)
    {
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        $staff = SmStaff::select('user_id', 'full_name')->wherein('company_id', $company_id)->orderby('full_name', 'asc')->get();
        $vendors = SysCustSuppl::select('id', 'code', 'name')->wherein('company_id', $company_id)->where('catid', 1)->orderby('name', 'asc')->get(); // 1 customers, 2 suppliers

        $company_list = SysCompany::select('id', 'company_name')->orderby('sort_id', 'asc')->get();

        $ctrl_deal_id = "";
        $ctrl_company_id = "";
        $ctrl_company_id2 = "";
        $ctrl_owner_id = "";
        $ctrl_status_id = "10";
        $ctrl_date = '';
        $ctrl_partial_delivery = '';
        try {
            $query = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deals.deal_value', 'sys_crm_deals.deal_currency')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id');

            if (count($request->all()) > 0) {
                if ($request->company_id2 != "") {
                    $query->where('sys_crm_deals.company_id', $request->company_id2);
                    $ctrl_company_id2 = $request->company_id2;
                }
                if ($request->status_id != "" && $request->status_id != "10") {
                    $ctrl_status_id = $request->status_id;

                    $track = str_split($request->status_id, 1)[0];
                    $status = str_split($request->status_id, 1)[1];
                    //return $track .' / '.$status;
                    //accounts
                    if ($track == "A") {
                        $query->where('sys_crm_deal_track.accounts', $status);
                        $query->where('sys_crm_deal_track.sales', 0);
                        $query->where('sys_crm_deal_track.purchease', 0);
                        $query->where('sys_crm_deal_track.invoice', 0);
                        $query->where('sys_crm_deal_track.delivery', 0);
                        $query->where('sys_crm_deal_track.receivables', 0);
                    }
                    //sales
                    else if ($track == "S") {
                        $query->where('sys_crm_deal_track.sales', $status);
                        $query->where('sys_crm_deal_track.purchease', 0);
                        $query->where('sys_crm_deal_track.invoice', 0);
                        $query->where('sys_crm_deal_track.delivery', 0);
                        $query->where('sys_crm_deal_track.receivables', 0);
                    }
                    //purchease
                    else if ($track == "P") {
                        $query->where('sys_crm_deal_track.purchease', $status);
                        $query->where('sys_crm_deal_track.invoice', 0);
                        $query->where('sys_crm_deal_track.delivery', 0);
                        $query->where('sys_crm_deal_track.receivables', 0);
                    }
                    //invoice
                    else if ($track == "I") {
                        $query->where('sys_crm_deal_track.invoice', $status);
                        $query->where('sys_crm_deal_track.delivery', 0);
                        $query->where('sys_crm_deal_track.receivables', 0);
                    }
                    //delivery
                    else if ($track == "D") {
                        $query->where('sys_crm_deal_track.delivery', $status);
                        $query->where('sys_crm_deal_track.receivables', 0);
                    }
                    //receivables
                    else if ($track == "R") {
                        $query->where('sys_crm_deal_track.receivables', $status);
                    } else {
                        if ($request->status_id == 0) {
                            $query->orwhere('sys_crm_deal_track.accounts', $request->status_id);
                        } else {
                            $query->orwhere('sys_crm_deal_track.accounts', $request->status_id);
                            $query->orwhere('sys_crm_deal_track.sales', $request->status_id);
                            $query->orwhere('sys_crm_deal_track.purchease', $request->status_id);
                            $query->orwhere('sys_crm_deal_track.invoice', $request->status_id);
                            $query->orwhere('sys_crm_deal_track.delivery', $request->status_id);
                            $query->orwhere('sys_crm_deal_track.receivables', $request->status_id);
                        }
                    }
                }
            } else {
                $query->where('sys_crm_deal_track.receivables', '!=', 1);
            }

            if (session('logged_session_data.company_id') != 1) {
                $query->wherein('sys_crm_deal_track.company_id', $company_id);
            }

            //$query->wherein('r.created_by',$r[1]);

            if (Auth::user()->role_id == 5) {
                $query->where('sys_crm_deal_track.created_by', Auth::user()->id);
            }

            /*if(session('logged_session_data.designation_id')==2){
                $query->where('sys_crm_deals.stage','=', 4);
                $dealtrack = $query->orderby('receivables','asc')->orderby('id','desc')->paginate(50);
            }else{*/
            $query->where('sys_crm_deals.stage', '=', 4);
            $dealtrack = $query->orderby('id', 'desc')->paginate(50);

            /*}*/

            return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date', 'ctrl_partial_delivery', 'company_list', 'ctrl_company_id2'));

            /*$form_data = [
                'dealtrack' => $dealtrack,
                'vendors' => $vendors,
                'staff' => $staff,
                'ctrl_deal_id' => $ctrl_deal_id,
                'ctrl_company_id' => $ctrl_company_id,
                'ctrl_owner_id' => $ctrl_owner_id,
                'ctrl_status_id' => $ctrl_status_id,
                'ctrl_date' => $ctrl_date,
                'ctrl_partial_delivery' => $ctrl_partial_delivery,                
            ];
            session()->put('deal_track_query', $form_data);
            return redirect('crm-deal-track-approval-listing');*/
            //return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function crmdealtrackapproval($id)
    {
        try {
            SysPurchaseOrderItemsCart::where(['cart_id' => session('logged_session_data.cart_id')])->delete();
            SysDealPurchaseOrderItems::where(['cart_id' => session('logged_session_data.cart_id')])->delete();

            $deal = SysCrmDealTrack::find($id);
            $del = SysCrmDeals::where('id', $deal->deal_id)->first();
            $cust = SysCustSuppl::where('id', $del->cust_id)->first();
            $account_id = SysChartofAccounts::where('account_code', $cust->code)->first();

            $paymentterms = SysPaymentTerms::orderby('title', 'asc')->get();

            $comments = SysCrmDealsComments::where('deal_id', $deal->deal_id)->orderBy('id', 'DESC')->get();
            $dn_detail = SysDeliveryNote::select('id', 'doc_number')->where('deal_id', $deal->deal_id)->get();
            if (count($dn_detail) > 0) {
                $dn_items = SysDeliveryNoteItems::select('part_number', db::raw('sum(qty) as dn_qty'))->wherein('dn_id', $dn_detail->pluck('id'))->groupby('part_number', 'refid')->get();
                if (count($dn_items) > 0) {
                    foreach ($dn_items as $dni) {
                        SysCrmQuoteItems::where('deal_id', $deal->deal_id)->where('quote_id', $del->quote_id)->where('product_id', $dni->part_number)->where('id', $dni->refid)
                            ->update(['dn_qty' => $dni->dn_qty]);
                    }
                }
            }
            $quoteitems = SysCrmQuoteItems::where('deal_id', $deal->deal_id)->where('quote_id', $del->quote_id)->orderby('sort_id', 'asc')->get();

            $quote_charges = SysCrmQuoteCharges::where('deal_id', $deal->deal_id)->where('quote_id', $del->quote_id)->get();

            $check_po = SysPurchaseOrder::where('deal_id', $deal->deal_id)->where('status', 1)->get();
            $check_dn = SysDeliveryNote::where('deal_id', $deal->deal_id)->where('status', 1)->get();
            $check_si = SysSalesInvoice::where('deal_id', $deal->deal_id)->where('status', 1)->get();
            $check_cl = SysClearance::where('deal_id', $deal->deal_id)->where('status', 1)->get();
            // $items_in_po=[];
            // if(count($check_po)>0){
            //     $items_in_po = SysPurchaseOrderItems::select('part_number',db::raw('sum(qty) as qty'))->wherein('po_id',$check_po->pluck('id'))->groupby('part_number','unitprice')->get();
            // }

            $quoteitems_invoiced = SysDealItemInvoiced::where('deal_id', $deal->deal_id)->sum('qty');
            //$sales_invoice = SysSalesInvoice::where('deal_id',$deal->deal_id)->count();

            $accounts = SysCrmDealTrackApprovalAccounts::where('deal_track_id', $id)->get();
            $sales = SysCrmDealTrackApprovalSales::where('deal_track_id', $id)->get();
            $purchease = SysCrmDealTrackApprovalPurchease::where('deal_track_id', $id)->get();

            $po_detail = DB::table('sys_purchase_order as po')->select('po.doc_number', 'po.currency', 'ca.account_name', DB::raw('sum(taxableamount) + sum(vatamount) as amount'))
                ->join('sys_purchase_order_items as poi', 'poi.po_id', 'po.id')
                ->join('sys_chartofaccounts as ca', 'ca.id', 'po.vendors')
                ->where('deal_id', $deal->deal_id)->groupby('po.doc_number', 'po.currency', 'ca.account_name')->get();

            $invoice = SysCrmDealTrackApprovalInvoice::where('deal_track_id', $id)->get();
            $invo_detail = SysSalesInvoice::select('doc_number')->where('deal_id', $deal->deal_id)->get();

            $delivery = SysCrmDealTrackApprovalDelivery::where('deal_track_id', $id)->get();

            $receivables = SysCrmDealTrackApprovalReceivables::where('deal_track_id', $id)->get();

            $r_deal_id = $deal->deal_id;
            $receipt_details = SysReceipt::select('sys_receipt.*', 'cat.debit_amount', 'ra.bi_doc_no')
                ->join('sys_chartofaccounts_transaction as cat', 'cat.transaction_no', 'sys_receipt.doc_number')
                ->leftjoin('sys_receipt_adjustments as ra', 'ra.bi_doc_number', 'sys_receipt.doc_number')
                ->wherein('cat.transaction_type', ['bankreceipt', 'cashreceipt'])->where('is_main_account', 1)->where(
                    function ($query) use ($r_deal_id) {
                        $query->orWhereRaw("FIND_IN_SET($r_deal_id, deal_id) > 0");
                    }
                )->get();

            $tech = SysCrmDealTrackApprovalTechnical::where('deal_track_id', $id)->get();

             $shipping = SysCustSuppl::where('company_id',session('logged_session_data.company_id') )->where('catid', 2)->where('account_type',3)->orderby('name', 'asc')->get(); // 1 customers, 2 suppliers
            
            $driver = SysDriver::where('status', 1)->get();
            if ($deal->deal_id == 10686) {
                $addressbook = SysCustSupplAddressbook::where('cust_suppl_id', $del->cust_id)->where('id', 68)->orderBy('id', 'desc')->first();
            } else {
                $addressbook = SysCustSupplAddressbook::where('cust_suppl_id', $del->cust_id)->where('set_default', 1)->orderBy('id', 'desc')->first();
            }
            $receivable_amount_sum = 0;
            $check_receipt = DB::table('sys_receipt as r')->select('r.doc_number', 'r.id', db::raw('sum(c.debit_amount) as amount'))
                ->join('sys_chartofaccounts_transaction as c', 'c.transaction_no', 'r.doc_number')
                ->where(
                    function ($query) use ($r_deal_id) {
                        $query->orWhereRaw("FIND_IN_SET($r_deal_id, deal_id) > 0");
                    }
                )->groupby('r.doc_number', 'r.id')->get();
            $check_jv = DB::table('sys_journalvoucher as j')->select('j.doc_number', 'j.id', db::raw('sum(c.debit_amount) as d_amount'), db::raw('sum(c.credit_amount) as c_amount'))
                ->join('sys_chartofaccounts_transaction as c', 'c.transaction_no', 'j.doc_number')
                ->where('j.deal_id', $deal->deal_id)->groupby('j.doc_number', 'j.id')->get();

            if (count($receivables) > 0) {
                $receivable_amount_sum += $receivables->sum('amount');
                $receivable_amount_sum += $receivables->sum('amount2');
                $receivable_amount_sum += $receivables->sum('amount3');
            }
            $currencylist = SysCurrencySettings::select('id', 'code')->where('status', 1)->orderBy('code', 'ASC')->get();


            //vat amount set as deal value
            $extra_charges = 0;
            $quote_charges = SysCrmQuoteCharges::where('deal_id', $del->id)->where('quote_id', $del->quote_id)->get();


            $purchase_auto = SysPurchaseAuto::where(['deal_id' => $del->id, 'status' => 2, 'req_cost' => 1])->pluck('po_id');
            $purchase_cost = 0;
            if (count($purchase_auto) > 0) {
                $purchase_cost = SysPurchaseOrderItems::select(DB::raw('sum(taxableamount) as total_cost'))->wherein('po_id', $purchase_auto)->get();
                $purchase_cost = $purchase_cost[0]->total_cost;
            }

            //return $quote_charges;

            if (count($quote_charges) > 0) {
                $extra_charges = $quote_charges->sum('amount');
            }

            $net = 0;
            $vat = 0;
            $curr = 1;
            $delivery_date = '';
            $deal_profit = 0;
            $deal_cost = 0;
            $check_edit_fullfill = 0;

            /*if(count($quoteitems)>0){
                $first_item_brand = $quoteitems[0]->title;
                foreach($quoteitems as $itms){
                    $qty = $itms->qty;
                    $price = $itms->price;
                    $discount = $itms->discount;
                    $vat = $itms->vat;
                    $net += (($price * $qty)+(($price * $qty)*$vat/100)) - ($discount+($discount*$vat/100));
                    $curr = $itms->currency_id;
                    $delivery_date = $itms->delivery_date;
                    $deal_cost += ($itms->cost*$itms->qty);
                    $deal_profit += (($price * $qty) - ($discount)) - ($itms->cost*$itms->qty);
                }
                $deal_value = $net - ($del->deal_discount+(($del->deal_discount*$vat)/100));
                //return $deal_value;
                $cost = $deal_cost+$extra_charges;

                $check_invoice_approved=0;
                if(isset($deal_track)){
                    if($deal_track->invoice == 1){
                        $check_invoice_approved=1;
                    }
                    if($deal_track->accounts == 1 && $deal_track->sales == 1){
                        $check_edit_fullfill=1;
                    }
                }

                if($net > 0){
                    DB::table('sys_crm_deals')->where('id',$del->id)
                    ->update([
                        'deal_value' => $deal_value,
                        'deal_currency' => $curr,
                        'deal_profit' => $deal_profit-$extra_charges-$purchase_cost,
                    ]);
                }
            }*/
            SysHelper::set_deal_profit($del->id);
            //vat amount set as deal value

            $list_performa_invoice = SysProformaInvoice::where('deal_id', $del->id)->get();
            $list_sales_invoice = SysSalesInvoice::where('deal_id', $del->id)->get();
            $list_delivery_note = SysDeliveryNote::where('deal_id', $del->id)->get();
            $list_sales_return = SysSalesReturn::where('deal_id', $del->id)->get();
            $list_receipt = SysReceipt::where('deal_id', $del->id)->get();
            $list_purchase_order = SysPurchaseOrder::where('deal_id', $del->id)->get();
            $list_goods_receipt_note = SysPurchaseGRN::where('deal_id', $del->id)->get();
            $list_purchase_invoice = SysPurchaseInvoice::where('deal_id', $del->id)->get();
            $list_purchase_return = SysPurchaseReturn::where('deal_id', $del->id)->get();
            $list_payment = SysPayment::where('deal_id', $del->id)->get();
            //$list_journalvoucher = SysJournalVoucher::where('deal_id',$del->id)->get();
            $list_journalvoucher = SysChartofAccountsTransaction::where('transaction_ref', $del->id)->get();

            $list_journalvoucher_det = [];
            $list_journalvoucher_det_other = [];
            if (count($list_journalvoucher) > 0) {
                $list_journalvoucher_det = DB::table('sys_chartofaccounts_transaction as t')->select('t.id', 't.account_id', 't.transaction_no', 't.debit_amount', 't.credit_amount', 't.remarks', 'c.account_name')
                    ->join('sys_chartofaccounts as c', 'c.id', 't.account_id')->where('t.transaction_ref', $del->id)->wherein('t.transaction_no', $list_journalvoucher->pluck('transaction_no'))->get();

                $list_journalvoucher_det_other = DB::table('sys_chartofaccounts_transaction as t')->select('t.id', 't.account_id', 't.transaction_no', 't.debit_amount', 't.credit_amount', 't.remarks', 'c.account_name')
                    ->join('sys_chartofaccounts as c', 'c.id', 't.account_id')->wherein('t.transaction_no', $list_journalvoucher->pluck('transaction_no'))->get();
            }

            //return $list_journalvoucher_det;
            $paymentmode_cash = SysHelper::get_cash_account();
            $paymentmode_bank = SysHelper::get_bank_account();

            $poitems = [];

            $get_auto = DB::table('sys_purchase_auto')->where('deal_id', $del->id)->where('status', 2)->pluck('po_no');

            if (count($get_auto) > 0) {
                $poitems = SysPurchaseOrderItems::select('sys_purchase_order_items.*', 'it.part_number as partno')
                    ->join('sys_purchase_order as po', 'po.id', 'sys_purchase_order_items.po_id')
                    ->join('sm_items as it', 'it.id', 'sys_purchase_order_items.part_number')
                    ->wherein('po.doc_number', $get_auto)
                    ->wherenotin('sys_purchase_order_items.part_number', $quoteitems->pluck('product_id'))
                    ->get();
            }

            $dnitem_set = SysDeliveryNoteItems::selectRaw('SUM(qty) as qty, part_number')
                ->join('sys_delivery_note', 'sys_delivery_note.id', '=', 'sys_delivery_note_items.dn_id')
                ->where('sys_delivery_note.deal_id', $del->id)
                ->groupBy('part_number')
                ->get();

            if (count($dnitem_set) > 0) {
                //SysCrmQuoteItems::where('deal_id', $deal->deal_id)->where('quote_id', $del->quote_id)
                //->update(['dn_qty'=> 0]);

                foreach ($dnitem_set as $item) {
                    //SysCrmQuoteItems::where('deal_id', $deal->deal_id)->where('quote_id', $del->quote_id)
                    //->where('product_id',$item->part_number)->update(['dn_qty'=> $item->qty]);
                }
            }

            $dnitems = SysDeliveryNoteItems::select('sys_delivery_note_items.*', 'it.part_number as partno', 'it.description')
                ->join('sys_delivery_note', 'sys_delivery_note.id', 'sys_delivery_note_items.dn_id')
                ->join('sm_items as it', 'it.id', 'sys_delivery_note_items.part_number')
                ->where('sys_delivery_note.deal_id', $del->id)
                ->where('sys_delivery_note_items.is_deal_aditional', 1)->get();

            return view('backEnd.crm.DealTrackApproval', compact('deal', 'accounts', 'sales', 'purchease', 'invoice', 'delivery', 'receivables', 'tech', 'cust', 'del', 'comments', 'quoteitems', 'shipping', 'driver', 'addressbook', 'paymentterms', 'currencylist', 'po_detail', 'invo_detail', 'dn_detail', 'receipt_details', 'quoteitems_invoiced', 'check_receipt', 'check_po', 'check_si', 'check_dn', 'receivable_amount_sum', 'quote_charges', 'account_id', 'list_performa_invoice', 'list_sales_invoice', 'list_delivery_note', 'list_sales_return', 'list_receipt', 'list_purchase_order', 'list_goods_receipt_note', 'list_purchase_invoice', 'list_purchase_return', 'list_payment', 'list_journalvoucher', 'check_jv', 'list_journalvoucher_det', 'list_journalvoucher_det_other', 'paymentmode_cash', 'paymentmode_bank', 'poitems', 'dnitems', 'check_cl'));


        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function crmdealtrackapprovalaccounts(Request $request)
    {
        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
        try {
            $status = 1;
            if ($request->customer_status == 2 || $request->credit_limit == 2 || $request->payment_terms == 2 || $request->pending_payment == 2 || $request->other == 2) {
                $status = 2;
            }
            $check = DB::table('sys_crm_deal_track_approval_accounts')->select('id', 'remarks')->where('deal_id', $request->deal_id)->first();

            if (isset($check)) {
                DB::table('sys_crm_deal_track_approval_accounts')->where('id', $check->id)->update(
                    [
                        'customer_status' => $request->customer_status,
                        'credit_limit' => $request->credit_limit,
                        'payment_terms' => $request->payment_terms,
                        'pending_payment' => $request->pending_payment,
                        'other' => $request->other,
                        'remarks' => $request->remarks,
                        'status' => $status,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                        'created_at' => $trn_time,
                        'updated_at' => $trn_time,
                    ]
                );
            } else {
                DB::table('sys_crm_deal_track_approval_accounts')->insert(
                    [
                        'deal_track_id' => $request->deal_track_id,
                        'deal_id' => $request->deal_id,
                        'customer_status' => $request->customer_status,
                        'credit_limit' => $request->credit_limit,
                        'payment_terms' => $request->payment_terms,
                        'pending_payment' => $request->pending_payment,
                        'other' => $request->other,
                        'remarks' => $request->remarks,
                        'status' => $status,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                        'created_at' => $trn_time,
                        'updated_at' => $trn_time,
                        'created_date' => $trn_time,
                    ]
                );
            }

            DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['accounts' => $status]);

            if ($status == 2) {
                SysHelper::exe_web_push($request->owner_id, 'Deal Track Rejected', 'Deal' . $request->deal_id . ' Rejected', 'crm-deal-track/' . $request->deal_id . '/view');
                SysHelper::Erp_Notify_in($request->owner_id, 'Deal' . $request->deal_id . ' Rejected', $request->owner_id, 'http://erp.venushrms.com/crm-deal-track/' . $request->deal_id . '/view');
                SysHelper::Erp_Notify_track_reject($request->deal_id, $request->owner_name, $request->owner_email, "Accounts", $request->remarks);
            }
            if ($status == 1) {
                $user = DB::table('sm_staffs')->select('user_id')->where('role_id', 8)->get(); //Sales
                if (count($user) > 0) {
                    foreach ($user as $u) {
                        SysHelper::exe_web_push($u->user_id, 'Deal Track Received', 'Deal ' . $request->deal_id, 'crm-deal-track-approval/' . $request->deal_track_id . '');
                        SysHelper::Erp_Notify_in($u->user_id, 'Deal Track Received', $u->user_id, 'http://erp.venushrms.com/crm-deal-track-approval/' . $request->deal_track_id . '');
                    }
                }
                SysHelper::Erp_Notify_in($request->owner_id, 'Accounts Approved', $request->owner_id, 'http://erp.venushrms.com/crm-deal-track-approval/' . $request->deal_track_id . '');


            }

            if ($status == 1) {

                SystemNotification::updateNotification('dealtrack', $request->deal_track_id, [
                    'role' => 'sales',
                    'is_resolved' => false,
                    'is_account_rejected' => false,
                    'is_shown' => false,
                    'title' => 'Deal Track Sales Approval Required',
                    'message' => 'Deal requires sales approval',
                    'created_at' => Carbon::now('Asia/Dubai'),
                ]);

                $check = SystemNotification::where('type', 'user')->where('record_id', $request->deal_track_id)->first();

                if ($check) {
                    SystemNotification::updateNotification('user', $request->deal_track_id, [
                        'role' => 'accounts',
                        'is_resolved' => true,
                        'is_account_rejected' => false,
                        'is_shown' => false,
                        'title' => 'Deal Track Accounts Approved By ' . Auth::user()->full_name,
                        'message' => 'Deal requires accounts Approved',
                        'created_at' => Carbon::now('Asia/Dubai'),
                    ]);
                }





                Toastr::success('Approved successfully', 'Success');
            } else if ($status == 2) {

                $deal_track = SysCrmDealTrack::find($request->deal_track_id);
                $deal_det_for_serv = SysCrmDeals::where('id', $request->deal_id)->first();



                $check = SystemNotification::where('type', 'user')->where('record_id', $request->deal_track_id)->first();

                if ($check) {
                    SystemNotification::updateNotification('user', $request->deal_track_id, [
                        'role' => 'accounts',
                        'is_resolved' => false,
                        'is_account_rejected' => false,
                        'is_shown' => false,
                        'title' => 'Accounts Rejected By ' . Auth::user()->full_name,
                        'message' => 'Accounts Rejected',
                        'created_at' => Carbon::now('Asia/Dubai'),
                    ]);
                } else {
                    SysHelper::notify([
                        'user_id' => $request->owner_id,
                        'type' => 'accounts',
                        'role' => 'accounts',
                        'record_id' => $request->deal_track_id,
                        'title' => 'Accounts Rejected By ' . Auth::user()->full_name,
                        'deal_id' => $deal_det_for_serv->code,
                        'company_id' => $deal_det_for_serv->company_id,
                        'customer_name' => $deal_det_for_serv->cust_id,
                        'sales_person' => $deal_det_for_serv->owner,
                        'submitted_time' => $deal_det_for_serv->created_at,
                        'value' => $deal_det_for_serv->deal_value,
                        'message' => 'Accounts Rejected',
                    ]);
                }

                Toastr::error('Rejected successfully', 'Rejected');
            } else {
                Toastr::warning('Updated successfully', 'Updated');
            }

            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function crmdealtrackapprovalsales(Request $request)
    {
        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
        try {
            $status = 1;
            if ($request->margin == 2 || $request->stock == 2 || $request->purcease_quote == 2 || $request->other == 2) {
                $status = 2;
            }


            $check = DB::table('sys_crm_deal_track_approval_sales')->select('id', 'remarks')->where(['deal_id' => $request->deal_id])->first();
            if (isset($check)) {
                DB::table('sys_crm_deal_track_approval_sales')->where('id', $check->id)->update(
                    [
                        'margin' => $request->margin,
                        'stock' => $request->stock,
                        'purcease_quote' => $request->purcease_quote,
                        'other' => $request->other,
                        'purchase_approval' => $request->purchase_approval,
                        'invoice_approval' => $request->invoice_approval,
                        'delivery_approval' => $request->delivery_approval,
                        'receivables_approval' => $request->receivables_approval,
                        'remarks' => $request->remarks,
                        'status' => $status,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                        'created_at' => $trn_time,
                        'updated_at' => $trn_time,
                    ]
                );
            } else {
                DB::table('sys_crm_deal_track_approval_sales')->insert(
                    [
                        'deal_track_id' => $request->deal_track_id,
                        'deal_id' => $request->deal_id,
                        'margin' => $request->margin,
                        'stock' => $request->stock,
                        'purcease_quote' => $request->purcease_quote,
                        'other' => $request->other,

                        'purchase_approval' => $request->purchase_approval,
                        'invoice_approval' => $request->invoice_approval,
                        'delivery_approval' => $request->delivery_approval,
                        'receivables_approval' => $request->receivables_approval,

                        'remarks' => $request->remarks,
                        'status' => $status,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                        'created_at' => $trn_time,
                        'updated_at' => $trn_time,

                        'created_date' => $trn_time,
                    ]
                );
            }

            DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['sales' => $status]);


            //test to delete
            // if($request->purchease_approval==2 && $status==1){
            //     DB::table('sys_crm_deal_track')->where('deal_id',$request->deal_id)->update(['purchease' => 1]);
            // }            
            // $status_purchase_approval = $request->purchase_approval;
            // if($status_purchase_approval==2){$status_purchase_approval=0;}
            // $status_invoice_approval = $request->invoice_approval;
            // if($status_invoice_approval==2){$status_invoice_approval=0;}
            // $status_delivery_approval = $request->delivery_approval;
            // if($status_delivery_approval==2){$status_delivery_approval=0;}
            // $status_receivables_approval = $request->receivables_approval;
            // if($status_receivables_approval==2){$status_receivables_approval=0;}
            //test to delete


            //purchase_approval 1 required, 2 notapplicable
            if ($request->purchase_approval == 2 && $status == 1) {
                DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['purchease' => 1, 'purchease_approval' => 0]);
            }
            if ($request->purchase_approval == 1 && $status == 1) {
                DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->where(['purchease_approval' => 0])->update(['purchease' => 0]);
                DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['purchease_approval' => 1]);
            }
            //return $request->invoice_approval;
            //invoice_approval
            if ($request->invoice_approval == 2 && $status == 1) {
                DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['invoice' => 1, 'invoice_approval' => 0]);
            }
            if ($request->invoice_approval == 1 && $status == 1) {
                DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->where(['invoice_approval' => 0])->update(['invoice' => 0]);
                DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['invoice_approval' => 1]);
            }

            //delivery_approval
            if ($request->delivery_approval == 2 && $status == 1) {
                DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['delivery' => 1, 'delivery_approval' => 0]);
            }
            if ($request->delivery_approval == 1 && $status == 1) {
                DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->where(['delivery_approval' => 0])->update(['delivery' => 0]);
                DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['delivery_approval' => 1]);
            }

            //receivables_approval
            if ($request->receivables_approval == 2 && $status == 1) {
                DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['receivables' => 1, 'receivables_approval' => 0]);
            }
            if ($request->receivables_approval == 1 && $status == 1) {
                DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->where(['receivables_approval' => 0])->update(['receivables' => 0]);
                DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['receivables_approval' => 1]);
            }

            if ($status == 2) {
                SysHelper::exe_web_push($request->owner_id, 'Deal Track Rejected', 'Deal' . $request->deal_id . ' Rejected', 'crm-deal-track/' . $request->deal_id . '/view');
                SysHelper::Erp_Notify_in($request->owner_id, 'Deal' . $request->deal_id . ' Rejected', $request->owner_id, 'http://erp.venushrms.com/crm-deal-track/' . $request->deal_id . '/view');
                SysHelper::Erp_Notify_track_reject($request->deal_id, $request->owner_name, $request->owner_email, "Sales", $request->remarks);
            }
            if ($status == 1) {
                $user = DB::table('sm_staffs')->select('user_id')->where('role_id', 9)->get(); //Purchase
                if (count($user) > 0) {
                    foreach ($user as $u) {
                        SysHelper::exe_web_push($u->user_id, 'Deal Track Received', 'Deal ' . $request->deal_id, 'crm-deal-track-approval/' . $request->deal_track_id . '');
                        SysHelper::Erp_Notify_in($u->user_id, 'Deal Track Received', $u->user_id, 'http://erp.venushrms.com/crm-deal-track-approval/' . $request->deal_track_id . '');
                    }
                }
                SysHelper::Erp_Notify_in($request->owner_id, 'Sales Approved', $request->owner_id, 'http://erp.venushrms.com/crm-deal-track-approval/' . $request->deal_track_id . '');


            }

            if ($status == 1) {

                SystemNotification::updateNotification('dealtrack', $request->deal_track_id, [
                    'role' => 'purchase',
                    'is_resolved' => false,
                    'is_account_rejected' => false,
                    'is_shown' => false,
                    'title' => 'Deal Track Sales Approval Required',
                    'message' => 'Deal requires sales approval',
                    'created_at' => Carbon::now('Asia/Dubai'),
                ]);

                $check = SystemNotification::where('type', 'user')->where('record_id', $request->deal_track_id)->first();

                if ($check) {
                    SystemNotification::updateNotification('user', $request->deal_track_id, [
                        'role' => 'sales',
                        'is_resolved' => true,
                        'is_account_rejected' => false,
                        'is_shown' => false,
                        'title' => 'Deal Track Sales Approved By ' . Auth::user()->full_name,
                        'message' => 'Deal Track Sales Approved',
                        'created_at' => Carbon::now('Asia/Dubai'),
                    ]);
                }

                Toastr::success('Approved successfully', 'Success');
            } else if ($status == 2) {

                $deal_track = SysCrmDealTrack::find($request->deal_track_id);
                $deal_det_for_serv = SysCrmDeals::where('id', $request->deal_id)->first();

                $check = SystemNotification::where('type', 'user')->where('record_id', $request->deal_track_id)->first();

                if ($check) {
                    SystemNotification::updateNotification('user', $request->deal_track_id, [
                        'role' => 'sales',
                        'is_resolved' => false,
                        'is_account_rejected' => false,
                        'is_shown' => false,
                        'title' => 'Sales Rejected By ' . Auth::user()->full_name,
                        'message' => 'Sales Rejected',
                        'created_at' => Carbon::now('Asia/Dubai'),
                    ]);
                } else {
                    SysHelper::notify([
                        'user_id' => $request->owner_id,
                        'type' => 'user',
                        'role' => 'sales',
                        'record_id' => $request->deal_track_id,
                        'title' => 'Sales Rejected By ' . Auth::user()->full_name,
                        'deal_id' => $deal_det_for_serv->code,
                        'company_id' => $deal_det_for_serv->company_id,
                        'customer_name' => $deal_det_for_serv->cust_id,
                        'sales_person' => $deal_det_for_serv->owner,
                        'submitted_time' => $deal_det_for_serv->created_at,
                        'value' => $deal_det_for_serv->deal_value,
                        'message' => 'Sales Rejected',
                    ]);
                }

                Toastr::error('Rejected successfully', 'Rejected');
            } else {
                Toastr::warning('Updated successfully', 'Updated');
            }

            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function crmdealtrackapprovalpurchease(Request $request)
    {


        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
        if ($request->cost_of_purchase) {
            $cost_of_purchase = $request->cost_of_purchase;
            $cost_of_purchase_currency = $request->cost_of_purchase_currency;
        } else {
            $cost_of_purchase = 0.00;
            $cost_of_purchase_currency = 1;
        }

        $fileone = "";
        $filetwo = "";
        $filethree = "";
        if ($request->file('fileone') != "") {
            $file1 = $request->file('fileone');
            $fileone = md5(time()) . "fileone." . $file1->getclientoriginalextension();
            $file1->move('public/uploads/crm_deal_track_doc/', $fileone);
            $fileone = $fileone;
        }
        if ($request->file('filetwo') != "") {
            $file2 = $request->file('filetwo');
            $filetwo = md5(time()) . "filetwo." . $file2->getclientoriginalextension();
            $file2->move('public/uploads/crm_deal_track_doc/', $filetwo);
            $filetwo = $filetwo;
        }
        if ($request->file('filethree') != "") {
            $file3 = $request->file('filethree');
            $filethree = md5(time()) . "filethree." . $file3->getclientoriginalextension();
            $file3->move('public/uploads/crm_deal_track_doc/', $filethree);
            $filethree = $filethree;
        }

        try {
            $status = 1;
            if ($request->purchease_quote == 2 || $request->quote_request == 2 || $request->validation == 2 || $request->other == 2) {
                $status = 2;
            }
            if ($request->validation == 4) {
                $status = 4;
            }
            if ($request->validation == 3) {
                $status = 3;
                SysCrmDealTrackApprovalPurcheaseGrn::insert([
                    'deal_id' => $request->deal_id,
                    'deal_track_id' => $request->deal_track_id,
                    'remarks' => $request->remarks,
                    'status' => 0,
                    'created_by' => Auth::user()->id,
                    'created_at' => $trn_time,
                ]);
            }
            $check = DB::table('sys_crm_deal_track_approval_purchease')->select('id', 'remarks', 'fileone', 'filetwo', 'filethree')->where(['deal_id' => $request->deal_id])->first();
            if (isset($check)) {

                if ($fileone != "") {
                    $fileone = $check->fileone;
                }
                if ($filetwo != "") {
                    $filetwo = $check->filetwo;
                }
                if ($filethree != "") {
                    $filethree = $check->filethree;
                }

                DB::table('sys_crm_deal_track_approval_purchease')->where('id', $check->id)->update(
                    [
                        'purchease_quote' => $request->purchease_quote,
                        'three_quote_request' => $request->quote_request,
                        'validation' => $request->validation,
                        'other' => $request->other,
                        'remarks' => $request->remarks,
                        'status' => $status,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                        'created_at' => $trn_time,
                        'updated_at' => $trn_time,
                        'fileone' => $fileone,
                        'filetwo' => $filetwo,
                        'filethree' => $filethree,
                        'lpo_no' => $request->lpo_no,
                        'supplier_name' => $request->supplier_name,
                        'ref_supplier_id' => !empty($request->ref_supplier_id)
                            ? implode(',', $request->ref_supplier_id)
                            : null,
                        'part_no' => $request->part_no,
                        'cost_of_purchase' => $cost_of_purchase,
                        'cost_of_purchase_currency' => $cost_of_purchase_currency,
                        'delivery_date' => SysHelper::normalizeToYmd($request->delivery_date),
                        'partial_delivery_note' => $request->partial_delivery_note,
                    ]
                );
            } else {

                DB::table('sys_crm_deal_track_approval_purchease')->insert(
                    [
                        'deal_track_id' => $request->deal_track_id,
                        'deal_id' => $request->deal_id,
                        'purchease_quote' => $request->purchease_quote,
                        'three_quote_request' => $request->quote_request,
                        'validation' => $request->validation,
                        'other' => $request->other,
                        'remarks' => $request->remarks,
                        'status' => $status,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                        'created_at' => $trn_time,
                        'updated_at' => $trn_time,
                        'fileone' => $fileone,
                        'filetwo' => $filetwo,
                        'filethree' => $filethree,
                        'lpo_no' => $request->lpo_no,
                        'supplier_name' => $request->supplier_name,
                        'ref_supplier_id' => !empty($request->ref_supplier_id)
                            ? implode(',', $request->ref_supplier_id)
                            : null,
                        'part_no' => $request->part_no,
                        'cost_of_purchase' => $cost_of_purchase,
                        'cost_of_purchase_currency' => $cost_of_purchase_currency,
                        'delivery_date' => SysHelper::normalizeToYmd($request->delivery_date),
                        'partial_delivery_note' => $request->partial_delivery_note,
                        'created_date' => $trn_time,
                    ]
                );
            }


            if ($request->validation == 4) {
                DB::table('sys_crm_deals')->where('id', $request->deal_id)->update(['is_partial_delivery' => 1]);
            } else {
                DB::table('sys_crm_deals')->where('id', $request->deal_id)->update(['is_partial_delivery' => 0]);
            }

            DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['purchease' => $status, 'purchease_approval' => 1]);

            if ($status == 2) {
                SysHelper::exe_web_push($request->owner_id, 'Deal Track Rejected', 'Deal' . $request->deal_id . ' Rejected', 'crm-deal-track/' . $request->deal_id . '/view');
                SysHelper::Erp_Notify_in($request->owner_id, 'Deal' . $request->deal_id . ' Rejected', $request->owner_id, 'http://erp.venushrms.com/crm-deal-track/' . $request->deal_id . '/view');
                SysHelper::Erp_Notify_track_reject($request->deal_id, $request->owner_name, $request->owner_email, "Purchase", $request->remarks);
            }
            if ($status == 1) {

                DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->where('invoice_approval', 1)->update(['invoice' => 3]);

                $user = DB::table('sm_staffs')->select('user_id')->where('role_id', 4)->get(); //Invoice
                if (count($user) > 0) {
                    foreach ($user as $u) {
                        SysHelper::exe_web_push($u->user_id, 'Deal Track Received', 'Deal ' . $request->deal_id, 'crm-deal-track-approval/' . $request->deal_track_id . '');
                        SysHelper::Erp_Notify_in($u->user_id, 'Deal Track Received', $u->user_id, 'http://erp.venushrms.com/crm-deal-track-approval/' . $request->deal_track_id . '');
                    }
                }
                SysHelper::Erp_Notify_in($request->owner_id, 'Purchase Approved', $request->owner_id, 'http://erp.venushrms.com/crm-deal-track-approval/' . $request->deal_track_id . '');


            }

            if ($status == 1) {

                SystemNotification::updateNotification('dealtrack', $request->deal_track_id, [
                    'role' => 'invoice',
                    'is_resolved' => false,
                    'is_account_rejected' => false,
                    'is_shown' => false,
                    'title' => 'Deal Track Sales Approval Required',
                    'message' => 'Deal requires sales approval',
                    'created_at' => Carbon::now('Asia/Dubai'),
                ]);

                Toastr::success('Approved successfully', 'Success');
            } else if ($status == 2) {
                Toastr::error('Rejected successfully', 'Rejected');
            } else {
                Toastr::warning('Updated successfully', 'Updated');
            }

            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function crmdealtrackapprovalinvoice(Request $request)
    {
        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
        try {
            $status = 1;
            if ($request->delivery_advice == 3 || $request->validation == 3 || $request->hold == 3 || $request->print == 3) {
                $status = 3;
            } elseif ($request->delivery_advice == 2 || $request->validation == 2 || $request->hold == 2 || $request->print == 2) {
                $status = 2;
            }
            $partial_invoice_amount = $request->partial_invoice_amount;
            if ($request->partial_invoice_amount == "") {
                $partial_invoice_amount = 0.00;
            }

            $check = DB::table('sys_crm_deal_track_approval_invoice')->select('id', 'remarks')->where(['deal_id' => $request->deal_id])->first();
            if (isset($check)) {
                DB::table('sys_crm_deal_track_approval_invoice')->where('id', $check->id)->update(
                    [
                        'delivery_advice' => $request->delivery_advice,
                        'validation' => $request->validation,
                        'hold' => $request->hold,
                        'print' => $request->print,
                        'remarks' => $request->remarks,
                        'status' => $status,
                        'invoice_no' => $request->invoice_no,
                        'partial_invoice' => $request->partial_invoice,
                        'partial_invoice_amount' => $partial_invoice_amount,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => $trn_time,
                    ]
                );
            } else {
                DB::table('sys_crm_deal_track_approval_invoice')->insert(
                    [
                        'deal_track_id' => $request->deal_track_id,
                        'deal_id' => $request->deal_id,
                        'delivery_advice' => $request->delivery_advice,
                        'validation' => $request->validation,
                        'hold' => $request->hold,
                        'print' => $request->print,
                        'remarks' => $request->remarks,
                        'status' => $status,
                        'invoice_no' => $request->invoice_no,
                        'partial_invoice' => $request->partial_invoice,
                        'partial_invoice_amount' => $partial_invoice_amount,
                        'created_by' => Auth::user()->id,
                        'created_at' => $trn_time,
                        'created_date' => $trn_time,
                    ]
                );
            }
            if ($request->partial_invoice == 1) {
                //update GP
                SysHelper::set_deal_profit($request->deal_id);

                DB::table('sys_crm_deals')->where('id', $request->deal_id)->update(['is_partial_invoice' => 1]);
            } else {
                DB::table('sys_crm_deals')->where('id', $request->deal_id)->update(['is_partial_invoice' => 0]);
            }

            DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['invoice' => $status, 'invoice_approval' => 1]);

            if ($status == 2) {
                SysHelper::exe_web_push($request->owner_id, 'Deal Track Rejected', 'Deal ' . $request->deal_id, 'crm-deal-track/' . $request->deal_id . '/view');
                SysHelper::Erp_Notify_in($request->owner_id, 'Deal' . $request->deal_id . ' Rejected', $request->owner_id, 'http://erp.venushrms.com/crm-deal-track/' . $request->deal_id . '/view');
                SysHelper::Erp_Notify_track_reject($request->deal_id, $request->owner_name, $request->owner_email, "Invoice", $request->remarks);
            }
            if ($status == 1) {

                $deals = SysCrmDeals::where('id', $request->deal_id)->first();
                $products = SysCrmQuoteItems::where('deal_id', $request->deal_id)->first();
                $deals_track = SysCrmDealTrack::where('deal_id', $request->deal_id)->first();
                if ($deals_track->start_date == null)
                    $start_date = date('Y-m-d h:i:s', time());
                else
                    $start_date = $deals_track->start_date;

                if ($deals_track->end_date == null)
                    $end_date = date('Y-m-d h:i:s', time());
                else
                    $end_date = $deals_track->end_date;

                $deals = SysCrmDeals::where('id', $request->deal_id)->first();
                $deals_item = SysCrmQuoteItems::wherein('product_id', [35657])->where('deal_id', $request->deal_id)->where('quote_id', $deals->quote_id)->first();

                $is_amc_item = SysCrmQuoteItems::wherein('product_id', [35657])->where('deal_id', $request->deal_id)->where('quote_id', $deals->quote_id)->count();

                if ($is_amc_item > 0) {
                    $invoice = SysHelper::get_amc_period($start_date, $end_date);
                    DB::table('sys_crm_amc_table')->insert(
                        [
                            'doc_number' => SysHelper::get_new_code('sys_crm_amc_table', 'AM', 'doc_number'),
                            'deal_id' => $request->deal_id,
                            'date' => Carbon::now('+04:00')->format('Y-m-d'),
                            'cust_name' => $deals->cust_id,
                            'contact_person' => $deals->cust_name,
                            'mobile_no' => $deals->cust_no,
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'invoice' => !empty($deals_track->invoicing) ? $deals_track->invoicing : $invoice,
                            'amount' => $deals_item->price,
                            'sales_person' => $deals->owner,
                            'description' => $deals_item->description,
                            'status' => 1,
                            'is_auto' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                            'company_id' => session('logged_session_data.company_id'),
                        ]
                    );
                }

                //AMC Update;
                //SysHelper::set_amc_per_month($request->deal_id, $request->owner_id, 12);

                $user = DB::table('sm_staffs')->select('user_id')->where('role_id', 29)->get(); //Delivery
                if (count($user) > 0) {
                    foreach ($user as $u) {
                        SysHelper::exe_web_push($u->user_id, 'Deal Track Received', 'Deal ' . $request->deal_id, 'crm-deal-track-approval/' . $request->deal_track_id . '');
                        SysHelper::Erp_Notify_in($u->user_id, 'Deal Track Received', $u->user_id, 'http://erp.venushrms.com/crm-deal-track-approval/' . $request->deal_track_id . '');
                    }
                }
                SysHelper::Erp_Notify_in($request->owner_id, 'Invoice Approved', $request->owner_id, 'http://erp.venushrms.com/crm-deal-track-approval/' . $request->deal_track_id . '');


            }

            if ($status == 1) {
                SystemNotification::updateNotification('dealtrack', $request->deal_track_id, [
                    'role' => 'delivery',
                    'is_resolved' => false,
                    'is_account_rejected' => false,
                    'is_shown' => false,
                    'title' => 'Deal Track Delivery Approval Required',
                    'message' => 'Deal requires delivery approval',
                    'created_at' => Carbon::now('Asia/Dubai'),
                ]);
                Toastr::success('Approved successfully', 'Success');
            } else if ($status == 2) {
                Toastr::error('Rejected successfully', 'Rejected');
            } else {
                Toastr::warning('Updated successfully', 'Updated');
            }

            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function crmdealtrackapprovalinvoiceupdate(Request $request)
    {
        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
        try {

            DB::table('sys_crm_deal_track_approval_invoice')->where('id', $request->inv_id)->update(
                [
                    'remarks' => $request->inv_remarks,
                    'invoice_no' => $request->inv_no,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => $trn_time,
                ]
            );
            Toastr::success('Invoice No Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function crmdealtrackapprovalaccountsupdate(Request $request)
    {
        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
        try {
            DB::table('sys_crm_deal_track')->where('id', $request->acc_deal_id)->update(
                [
                    'accounts' => $request->acc_status,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => $trn_time,
                ]
            );

            //'deal_id' => $request->acc_deal_id, this is teal track id
            DB::table('sys_crm_deal_track_approval_accounts_pending')->insert(
                [
                    'deal_id' => $request->acc_deal_id,
                    'status' => $request->acc_status,
                    'remarks' => $request->acc_remarks,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'created_at' => $trn_time,
                    'updated_at' => $trn_time,
                ]
            );
            Toastr::success('Accounts Status Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function crm_deal_track_approval_purchase_not_required(Request $request)
    {
        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
        try {
            DB::table('sys_crm_deal_track')->where('deal_id', $request->purchase_not_required_deal_id)->update(
                [
                    'purchease' => 1,
                    'purchease_approval' => 0,
                ]
            );
            Toastr::success('Purchase Not Required Updated Successfully', 'Success');

            $deal_track_id = DB::table('sys_crm_deal_track')->where('deal_id', $request->purchase_not_required_deal_id)->value('id');

            SystemNotification::updateNotification('dealtrack', $deal_track_id, [
                'role' => 'invoice',
                'is_resolved' => false,
                'is_account_rejected' => false,
                'is_shown' => false,
                'title' => 'Deal Track Delivery Approval Required',
                'message' => 'Deal requires delivery approval',
                'created_at' => Carbon::now('Asia/Dubai'),
            ]);

            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function crm_deal_track_approval_purchase_required(Request $request)
    {
        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
        try {
            DB::table('sys_crm_deal_track')->where('deal_id', $request->purchase_required_deal_id)->update(
                [
                    'purchease' => 0,
                    'purchease_approval' => 1,
                ]
            );
            Toastr::success('Purchase Not Required Updated Successfully', 'Success');

            $deal_track_id = DB::table('sys_crm_deal_track')->where('deal_id', $request->purchase_required_deal_id)->value('id');

            SystemNotification::updateNotification('dealtrack', $deal_track_id, [
                'role' => 'invoice',
                'is_resolved' => false,
                'is_account_rejected' => false,
                'is_shown' => false,
                'title' => 'Deal Track Delivery Approval Required',
                'message' => 'Deal requires delivery approval',
                'created_at' => Carbon::now('Asia/Dubai'),
            ]);

            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function crmdealtrackapprovaldelivery(Request $request)
    {
        
        $deliver_by = "";
        $driver = "";
        if ($request->deliver_by == 1) {
            $deliver_by = "Courier";
            if ($request->courier == "Other") {
                $driver = $request->other_courier;
            } else {
                $driver = $request->courier;
            }
        }
        if ($request->deliver_by == 2) {
            $deliver_by = "Driver";
            if ($request->driver == "Other") {
                $driver = $request->other_driver;
            } else {
                $driver = $request->driver;
            }
        }
        if ($request->deliver_by == 3) {
            $deliver_by = "Local Delivery";
            if ($request->localdelivery == "Other") {
                $driver = $request->other_localdelivery;
            } else {
                $driver = $request->localdelivery;
            }
        }
        if ($request->deliver_by == 4) {
            $deliver_by = "Office Boy";
            if ($request->officeboy == "Other") {
                $driver = $request->other_officeboy;
            } else {
                $driver = $request->officeboy;
            }
        }
        if ($request->deliver_by == 5) {
            $deliver_by = "Collection by Client";
            $driver = $request->collectionbyclient;
        }
        if ($request->deliver_by == 6) {
            $deliver_by = "By Email";
            $driver = $request->byemail;
        }

        if ($request->deliver_by == 7) {
            $deliver_by = "Forwarder";
            if ($request->forwarder == "Other") {
                $driver = $request->other_forwarder;
            } else {
                $driver = $request->forwarder;
            }   
        }

        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
        $cheque_collection_file = "";
        if ($request->file('cheque_collection_file') != "") {
            $file1 = $request->file('cheque_collection_file');
            $cheque_collection_file = md5(time()) . "cheque." . $file1->getclientoriginalextension();
            $file1->move('public/uploads/crm_deal_track_doc/', $cheque_collection_file);
            $cheque_collection_file = $cheque_collection_file;
        }
        $attach_file = "";
        if ($request->file('attach_file') != "") {
            $file1 = $request->file('attach_file');
            $attach_file = md5(time()) . "awb." . $file1->getclientoriginalextension();
            $file1->move('public/uploads/crm_deal_track_doc/', $attach_file);
            $attach_file = $attach_file;
        }

        try {
            $status = 1;
            if ($request->do_status == 2 || $request->cheque_collection == 2) {
                $status = 2;
            } else if ($request->delivery_status == 3) {
                $status = 3;
            } else if ($request->delivery_status == 4) {
                $status = 5;
            } else if ($request->delivery_status == 5) {
                $status = 6;
            } else if ($request->delivery_status == 2) {
                $status = 4;
            } else if ($request->delivery_status == 1) {
                $status = 1;
            }
            $check = DB::table('sys_crm_deal_track_approval_delivery')->select('id', 'remarks', 'cheque_collection_file', 'attach_file')->where(['deal_id' => $request->deal_id])->first();
            if (isset($check)) {

                if ($cheque_collection_file == "") {
                    $cheque_collection_file = $check->cheque_collection_file;
                }
                if ($attach_file == "") {
                    $attach_file = $check->attach_file;
                }

                DB::table('sys_crm_deal_track_approval_delivery')->where('id', $check->id)->update(
                    [
                        'do_status' => $request->do_status,
                        'do_no' => $request->do_no,
                        'print_invoice_no' => $request->print_invoice_no,
                        'cheque_collection' => $request->cheque_collection,
                        'cheque_collection_file' => $cheque_collection_file,
                        'delivery_status' => $request->delivery_status,
                        'deliver_by' => $deliver_by,
                        'driver' => $driver,
                        'remarks' => $request->remarks,
                        'status' => $status,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                        'created_at' => $trn_time,
                        'updated_at' => $trn_time,
                        'cash_collected' => $request->cash_collected,
                        'contact_no' => $request->contact_no,
                        'id_no' => $request->id_no,
                        'attach_file' => $attach_file,
                        'awb_no' => $request->awb_no,
                    ]
                );
            } else {
                DB::table('sys_crm_deal_track_approval_delivery')->insert(
                    [
                        'deal_track_id' => $request->deal_track_id,
                        'deal_id' => $request->deal_id,
                        'do_status' => $request->do_status,
                        'do_no' => $request->do_no,
                        'print_invoice_no' => $request->print_invoice_no,
                        'cheque_collection' => $request->cheque_collection,
                        'cheque_collection_file' => $cheque_collection_file,
                        'delivery_status' => $request->delivery_status,
                        'deliver_by' => $deliver_by,
                        'driver' => $driver,
                        'remarks' => $request->remarks,
                        'status' => $status,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                        'created_at' => $trn_time,
                        'updated_at' => $trn_time,
                        'cash_collected' => $request->cash_collected,
                        'contact_no' => $request->contact_no,
                        'id_no' => $request->id_no,
                        'attach_file' => $attach_file,
                        'awb_no' => $request->awb_no,
                        'created_date' => $trn_time,
                    ]
                );
            }

            DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['delivery' => $status, 'delivery_approval' => 1]);

            if ($status == 2) {
                SysHelper::exe_web_push($request->owner_id, 'Deal Track Rejected', 'Deal' . $request->deal_id . ' Rejected', 'crm-deal-track/' . $request->deal_id . '/view');
                SysHelper::Erp_Notify_in($request->owner_id, 'Deal' . $request->deal_id . ' Rejected', $request->owner_id, 'http://erp.venushrms.com/crm-deal-track/' . $request->deal_id . '/view');
                SysHelper::Erp_Notify_track_reject($request->deal_id, $request->owner_name, $request->owner_email, "Delivery", $request->remarks);
            }
            if ($status == 1) {
                $user = DB::table('sm_staffs')->select('user_id')->where('role_id', 3)->get(); //Receivable
                if (count($user) > 0) {
                    foreach ($user as $u) {
                        SysHelper::exe_web_push($u->user_id, 'Deal Track Received', 'Deal ' . $request->deal_id, 'crm-deal-track-approval/' . $request->deal_track_id . '');
                        SysHelper::Erp_Notify_in($u->user_id, 'Deal Track Received', $u->user_id, 'http://erp.venushrms.com/crm-deal-track-approval/' . $request->deal_track_id . '');
                    }
                }
                SysHelper::Erp_Notify_in($request->owner_id, 'Delivery Approved', $request->owner_id, 'http://erp.venushrms.com/crm-deal-track-approval/' . $request->deal_track_id . '');


            }

            if ($status == 1) {
                SystemNotification::updateNotification('dealtrack', $request->deal_track_id, [
                    'role' => 'receivables',
                    'is_resolved' => false,
                    'is_account_rejected' => false,
                    'is_shown' => false,
                    'title' => 'Deal Track Delivery Approval Required',
                    'message' => 'Deal requires delivery approval',
                    'created_at' => Carbon::now('Asia/Dubai'),
                ]);
                Toastr::success('Approved successfully', 'Success');
            } else if ($status == 2) {
                Toastr::error('Rejected successfully', 'Rejected');
            } else {
                SystemNotification::updateNotification('dealtrack', $request->deal_track_id, [
                    'role' => 'receivables',
                    'is_resolved' => false,
                    'is_account_rejected' => false,
                    'is_shown' => false,
                    'title' => 'Deal Track Delivery Approval Required',
                    'message' => 'Deal requires delivery approval',
                    'created_at' => Carbon::now('Asia/Dubai'),
                ]);
                Toastr::warning('Updated successfully', 'Updated');
            }

            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function crmdealtrackapprovalprofessionalservice(Request $request)
    {
        try {
            $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $status = 1;
            if ($request->technical_approve == 2) {
                $status = 2;
            }
            $check = DB::table('sys_crm_deal_track_approval_technical')->select('id', 'remarks')->where(['deal_id' => $request->deal_id])->first();
            if (isset($check)) {


                DB::table('sys_crm_deal_track_approval_technical')->where('id', $check->id)->update(
                    [
                        'technical_approve' => $request->technical_approve,
                        'remarks' => $request->remarks,
                        'status' => $status,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                        'created_at' => $trn_time,
                        'updated_at' => $trn_time,
                    ]
                );
            } else {
                DB::table('sys_crm_deal_track_approval_technical')->insert(
                    [
                        'deal_track_id' => $request->deal_track_id,
                        'deal_id' => $request->deal_id,
                        'technical_approve' => $request->technical_approve,
                        'remarks' => $request->remarks,
                        'status' => $status,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                        'created_at' => $trn_time,
                        'updated_at' => $trn_time,
                    ]
                );
            }

            DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['tech' => $status]);

            if ($status == 2) {
                SysHelper::exe_web_push($request->owner_id, 'Deal Track Rejected', 'Deal' . $request->deal_id . ' Rejected', 'crm-deal-track/' . $request->deal_id . '/view');
                SysHelper::Erp_Notify_in($request->owner_id, 'Deal' . $request->deal_id . ' Rejected', $request->owner_id, 'http://erp.venushrms.com/crm-deal-track/' . $request->deal_id . '/view');
                SysHelper::Erp_Notify_track_reject($request->deal_id, $request->owner_name, $request->owner_email, "Professional Service", $request->remarks);
            }
            if ($status == 1) {
                $user = DB::table('sm_staffs')->select('user_id')->where('designation_id', 2)->get();
                if (count($user) > 0) {
                    foreach ($user as $u) {
                        SysHelper::exe_web_push($u->user_id, 'Deal Track Received', 'Deal ' . $request->deal_id, 'crm-deal-track-approval/' . $request->deal_track_id . '');
                        SysHelper::Erp_Notify_in($u->user_id, 'Deal Track Received', $u->user_id, 'http://erp.venushrms.com/crm-deal-track-approval/' . $request->deal_track_id . '');
                    }
                }
            }

            if ($status == 1) {
                SystemNotification::updateNotification('dealtrack', $request->deal_track_id, [
                    'role' => 'receivables',
                    'is_resolved' => false,
                    'is_account_rejected' => false,
                    'is_shown' => false,
                    'title' => 'Deal Track Delivery Approval Required',
                    'message' => 'Deal requires delivery approval',
                    'created_at' => Carbon::now('Asia/Dubai'),
                ]);
                Toastr::success('Approved successfully', 'Success');
            } else if ($status == 2) {
                Toastr::error('Rejected successfully', 'Rejected');
            } else {
                Toastr::warning('Updated successfully', 'Updated');
            }

            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function crmdealtrackapprovalreceivables(Request $request)
    {

        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
        $cheque_copy = "";
        $banktt_copy = "";
        if ($request->file('cheque_copy') != "") {
            $files = $request->file('cheque_copy');
            for ($i = 0; $i < count($files); $i++) {
                $file1 = $files[$i];
                $cheque_copy = md5(time()) . "_cheque_" . $i . "." . $file1->getclientoriginalextension();
                $file1->move('public/uploads/crm_deal_track_doc/', $cheque_copy);
                $cheque[] = $cheque_copy;
            }
            $cheque_copy = implode("|", $cheque);
        }
        if ($request->file('banktt_copy') != "") {
            $files = $request->file('banktt_copy');
            for ($i = 0; $i < count($files); $i++) {
                $file2 = $files[$i];
                $banktt_copy = md5(time()) . "_banktt_" . $i . "." . $file2->getclientoriginalextension();
                $file2->move('public/uploads/crm_deal_track_doc/', $banktt_copy);
                $banktt[] = $banktt_copy;
            }
            $banktt_copy = implode("|", $banktt);
        }

        // if ($request->file('cheque_copy') != "") {
        //     $file1 = $request->file('cheque_copy');
        //     $cheque_copy = md5(time()) . "cheque." . $file1->getclientoriginalextension();
        //     $file1->move('public/uploads/crm_deal_track_doc/', $cheque_copy);
        //     $cheque_copy = $cheque_copy;
        // }
        // if ($request->file('banktt_copy') != "") {
        //     $file2 = $request->file('banktt_copy');
        //     $banktt_copy = md5(time()) . "banktt." . $file2->getclientoriginalextension();
        //     $file2->move('public/uploads/crm_deal_track_doc/', $banktt_copy);
        //     $banktt_copy = $banktt_copy;
        // }



        try {
            $status = 1;
            $payment_status = 0;
            $amount = 0;
            if ($request->payment_collection == 2) {
                $status = 2;
            }
            if ($request->payment_collection == 3) {
                $status = 4;
            }
            if ($request->payment_collection != 3) {
                $payment_status = $request->payment_status;
                $amount = $request->amount;
                if ($request->payment_status == 2) {
                    $status = 3;
                }
            }
            $check = DB::table('sys_crm_deal_track_approval_receivables')->select('id', 'remarks', 'cheque_copy', 'banktt_copy')->where(['deal_id' => $request->deal_id])->first();

            //return $request->all();


            if (isset($check)) {

                // if ($cheque_copy == "") {
                //     $cheque_copy = $check->cheque_copy;
                // }
                // if ($banktt_copy == "") {
                //     $banktt_copy = $check->banktt_copy;
                // }

                $rec = SysCrmDealTrackApprovalReceivables::find($check->id);
                $rec->deal_track_id = $request->deal_track_id;
                $rec->deal_id = $request->deal_id;
                $rec->payment_collection = $request->payment_collection;
                $rec->payment_status = $payment_status;

                if ($request->reminder_date != "") {
                    $rec->reminder_date = Carbon::createFromFormat('d/m/Y H:i', $request->reminder_date . ' ' . $request->reminder_time)
                        ->format('Y-m-d H:i:s');
                    // $rec->reminder_date = date('Y-m-d H:i:s', strtotime($request->reminder_date . '' . $request->reminder_time));
                }


                $rec->remarks = $request->remarks;
                $rec->status = $status;
                $rec->created_by = Auth::user()->id;
                $rec->updated_by = Auth::user()->id;
                $rec->created_at = $trn_time;
                $rec->updated_at = $trn_time;
                $rec->paymenttype = $request->payment_mode;
                $rec->amount = $request->amount;


                $rec->doc_number = $request->doc_number;

                $rec->receipt_mode = $request->receipt_mode;
                $rec->receipt_date = $request->receipt_date;
                $rec->invoice_no = $request->invoice_no;
                $rec->receipt_through = $request->receipt_through;
                $rec->cheque_date = SysHelper::normalizeToYmd($request->cheque_date);
                $rec->cheque_no = $request->cheque_no;
                $rec->bank_name = $request->bank_name;




                $rec->credit_note = $request->credit_note;
                $rec->update();
            } else {
                $rec = new SysCrmDealTrackApprovalReceivables();
                $rec->deal_track_id = $request->deal_track_id;
                $rec->deal_id = $request->deal_id;
                $rec->payment_collection = $request->payment_collection;
                $rec->payment_status = $payment_status;

                if ($request->reminder_date != "") {
                    $rec->reminder_date = Carbon::createFromFormat('d/m/Y H:i', $request->reminder_date . ' ' . $request->reminder_time)
                        ->format('Y-m-d H:i:s');
                    // $rec->reminder_date = date('Y-m-d H:i:s', strtotime($request->reminder_date . '' . $request->reminder_time));
                }

                $rec->remarks = $request->remarks;
                $rec->status = $status;
                $rec->created_by = Auth::user()->id;
                $rec->updated_by = Auth::user()->id;
                $rec->created_at = $trn_time;
                $rec->created_date = $trn_time;
                $rec->updated_at = $trn_time;
                $rec->paymenttype = $request->payment_mode;
                $rec->amount = $amount;


                $rec->doc_number = $request->doc_number;

                $rec->receipt_mode = $request->receipt_mode ?: 0;
                $rec->receipt_date = SysHelper::normalizeToYmd($request->receipt_date);
                $rec->invoice_no = $request->invoice_no;
                $rec->receipt_through = $request->receipt_through;
                $rec->cheque_date = SysHelper::normalizeToYmd($request->cheque_date);
                $rec->cheque_no = $request->cheque_no;
                $rec->bank_name = $request->bank_name;

                $rec->credit_note = $request->credit_note;
                $rec->save();
            }

            DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['receivables' => $status, 'receivables_approval' => 1]);

            if ($status == 2) {
                SysHelper::exe_web_push($request->owner_id, 'Deal Track Rejected', 'Deal' . $request->deal_id . ' Rejected', 'crm-deal-track/' . $request->deal_id . '/view');
                SysHelper::Erp_Notify_in($request->owner_id, 'Deal' . $request->deal_id . ' Rejected', $request->owner_id, 'http://erp.venushrms.com/crm-deal-track/' . $request->deal_id . '/view');
                SysHelper::Erp_Notify_track_reject($request->deal_id, $request->owner_name, $request->owner_email, "Receivables", $request->remarks);
            }
            if ($status == 1) {

                //update GP
                SysHelper::set_deal_profit($request->deal_id);

                SysHelper::exe_web_push($request->owner_id, 'Deal Track Compleated', 'Deal ' . $request->deal_id, 'crm-deal-track-approval/' . $request->deal_track_id . '');
                SysHelper::Erp_Notify_in($request->owner_id, 'Receivables Approved', $request->owner_id, 'http://erp.venushrms.com/crm-deal-track-approval/' . $request->deal_track_id . '');

                SystemNotification::updateNotification('dealtrack', $request->deal_track_id, [
                    'role' => 'receivables',
                    'is_resolved' => true,
                    'is_account_rejected' => false,
                    'is_shown' => false,
                    'title' => 'Deal Track Delivery Approval Required',
                    'message' => 'Deal requires delivery approval',
                    'created_at' => Carbon::now('Asia/Dubai'),
                ]);

                Toastr::success('Approved successfully', 'Success');
            } else if ($status == 2) {
                Toastr::error('Rejected successfully', 'Rejected');
            } else {
                Toastr::warning('Updated successfully', 'Updated');
            }

            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function getdriverbyshipping(Request $request)
    {
        try {
            $shipping = SysShipping::select('id')->where('shipping_name', $request->deliver_by)->first();
            $driver = SysDriver::select('driver_name')->where('shipping_id', $shipping->id)->get();
            return json_encode(array('data' => $driver));
        } catch (\Exception $e) {
            $retData = "ERROR";
            return json_encode(array('data' => $retData));
        }
    }
    public function crmcustomercolor(Request $request)
    {
        try {
            DB::table('sys_cust_suppl')->where('id', $request->color_customer_id)->update(
                [
                    'type' => $request->edit_color,
                    'updated_by' => Auth::user()->id
                ]
            );
            Toastr::success('Color updated successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Color Updation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function crmdealtrackapprovalreceivablespaymentmode(Request $request)
    {
        try {
            DB::table('sys_crm_deal_track')->where('deal_id', $request->edit_payment_mode_id)->update(
                [
                    'payment_mode' => $request->edit_payment_mode,
                    'updated_by' => Auth::user()->id
                ]
            );
            Toastr::success('Color updated successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Color Updation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function crmdealtrackapprovalreceivablespaymenttermsmode(Request $request)
    {
        try {
            
            DB::table('sys_crm_deal_track')->where('deal_id', $request->edit_payment_mode_id)->update(
                [
                    'payment_mode' => $request->edit_payment_mode,
                    'payment_terms' => $request->edit_payment_terms,
                    'payment_terms_txt' => $request->edit_payment_terms_txt,
                    'updated_by' => Auth::user()->id
                ]
            );
            Toastr::success('Payment terms updated successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Payment terms Updation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function trackpagefilter($track)
    {
        $staff = SmStaff::select('user_id', 'full_name')->orderby('full_name', 'asc')->get();
        $vendors = SysCustSuppl::select('id', 'code', 'name')->where('catid', 1)->orderby('name', 'asc')->get(); // 1 customers, 2 suppliers
        $ctrl_deal_id = "";
        $ctrl_company_id = "";
        $ctrl_owner_id = "";
        $ctrl_status_id = "10";
        $ctrl_date = '';
        try {

            if ($track == "pendingpayments") {

                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')
                    ->join('sys_crm_deals', 'sys_crm_deal_track.deal_id', 'sys_crm_deals.id')
                    ->join('sys_crm_deal_track_approval_receivables', 'sys_crm_deals.id', 'sys_crm_deal_track_approval_receivables.deal_id')
                    ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_receivables.created_at, '%Y-%m') = '" . date('Y-m') . "'")->where('sys_crm_deal_track.receivables', '!=', 1)->where('sys_crm_deal_track.delivery', 1)
                    ->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'));
                $dealtrack->orderby('sys_crm_deal_track_approval_receivables.id', 'asc')->paginate(50);

                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date'));
            }

            if ($track == "orderinprocess") {
                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '" . date('Y-m') . "'")->where('sys_crm_deal_track.receivables', '!=', 1)
                    ->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'));
                $dealtrack->orderby('sys_crm_deal_track.id', 'desc')->paginate(50);

                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date'));
            }

            if ($track == "paymentreminder") {
                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                    ->join('sys_crm_deal_track_approval_receivables', 'sys_crm_deal_track_approval_receivables.deal_id', 'sys_crm_deal_track.deal_id')
                    ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') >= '" . date('Y-m-d') . "'")
                    ->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'));
                $dealtrack->orderby('reminder_date', 'asc')->paginate(50);

                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date'));
            }

            if ($track == "paymentpendingafterreminder") {
                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                    ->join('sys_crm_deal_track_approval_receivables', 'sys_crm_deal_track_approval_receivables.deal_id', 'sys_crm_deal_track.deal_id')
                    ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') < '" . date('Y-m-d') . "'")
                    ->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'));
                $dealtrack->orderby('reminder_date', 'asc')->paginate(50);

                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date'));
            }

            if ($track == "salesorderinprocess") {

                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')
                    ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                    ->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '" . date('Y-m') . "'")
                    ->where('sys_crm_deals.owner', Auth::user()->id)->where('sys_crm_deal_track.receivables', '!=', 1)
                    ->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'));
                $dealtrack->orderby('sys_crm_deal_track.id', 'desc')->paginate(50);

                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date'));
            }

            if ($track == "salesteamorderinprocess") {
                $teams = array(Auth::user()->id);
                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')
                    ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                    ->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '" . date('Y-m') . "'")
                    ->wherein('sys_crm_deals.owner', $teams)->where('sys_crm_deal_track.receivables', '!=', 1)
                    ->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'));
                $dealtrack->orderby('sys_crm_deal_track.id', 'desc')->paginate(50);

                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date'));
            }

            if ($track == "salespendingpayments") {
                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')
                    ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                    ->join('sys_crm_deal_track_approval_receivables', 'sys_crm_deals.id', 'sys_crm_deal_track_approval_receivables.deal_id')
                    ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_receivables.created_at, '%Y-%m') = '" . date('Y-m') . "'")->where('sys_crm_deal_track.receivables', '!=', 1)->where('sys_crm_deal_track.delivery', 1)->where('owner', Auth::user()->id)
                    ->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'));
                $dealtrack->orderby('sys_crm_deal_track_approval_receivables.id', 'asc')->paginate(50);

                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date'));
            }

            if ($track == "salesteampendingpayments") {

                $teams = array(Auth::user()->id);
                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')
                    ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                    ->join('sys_crm_deal_track_approval_receivables', 'sys_crm_deals.id', 'sys_crm_deal_track_approval_receivables.deal_id')
                    ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_receivables.created_at, '%Y-%m') = '" . date('Y-m') . "'")->where('sys_crm_deal_track.receivables', '!=', 1)->where('sys_crm_deal_track.delivery', 1)->wherein('owner', $teams)
                    ->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'));
                $dealtrack->orderby('sys_crm_deal_track_approval_receivables.id', 'asc')->paginate(50);

                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date'));
            }

            if ($track == "partialdelivery") {
                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->where('sys_crm_deal_track.purchease', 4)
                    ->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'));
                $dealtrack->orderby('sys_crm_deal_track.id', 'desc')->paginate(50);

                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date'));
            }

            if ($track == "purchasecompleted") {
                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                    ->join('sys_crm_deal_track_approval_purchease', 'sys_crm_deal_track_approval_purchease.deal_id', 'sys_crm_deal_track.deal_id')
                    ->where('sys_crm_deal_track_approval_purchease.validation', 1)
                    ->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'));
                $dealtrack->orderby('sys_crm_deal_track.id', 'desc')->paginate(50);

                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date'));
            }

            if ($track == "underpurchase") {
                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')
                    ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                    ->join('sys_crm_deal_track_approval_purchease', 'sys_crm_deal_track_approval_purchease.deal_id', 'sys_crm_deal_track.deal_id')
                    ->where('sys_crm_deal_track.purchease', 3)
                    ->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'));
                $dealtrack->orderby('sys_crm_deal_track.id', 'desc')->paginate(50);

                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date'));
            }

            if ($track == "salesapprovedlist") {
                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '" . date('Y-m') . "'")->where('sys_crm_deal_track.sales', 1)
                    ->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'));
                $dealtrack->orderby('sys_crm_deal_track.id', 'desc')->paginate(50);

                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date'));
            }

            if ($track == "doonprocess") {
                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '" . date('Y-m') . "'")->where([['sys_crm_deal_track.delivery', '=', 3], ['sys_crm_deal_track.delivery', '=', 4], ['sys_crm_deal_track.invoice', 1]])
                    ->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'));
                $dealtrack->orderby('sys_crm_deal_track.id', 'desc')->paginate(50);

                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date'));
            }

            if ($track == "dopending") {
                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '" . date('Y-m') . "'")->where('sys_crm_deal_track.delivery', 0)->where('sys_crm_deals.stage', 4)->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'))->orderby('sys_crm_deal_track.id', 'asc')->paginate(50);

                // $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '" . date('Y-m') . "'")->where('sys_crm_deal_track.delivery', 0)
                //     ->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'));
                // $dealtrack->orderby('sys_crm_deal_track.id', 'asc')->get();

                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date'));
            }

            if ($track == "0") {

                $query = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id');

                //Accounts:- Ananthu Satheesh (accounts@sysllc.com) Accounts Dept Head 27
                //Sales :- Parveen Sheik Asif (parveen@sysllc.com) Sales Department Head 8
                //Purchase:- Zahid Khan (procurement@sysllc.com) Procurement Dept Head 9
                //Invoice:- Hennie Navales (hennie@sysllc.com) Billing 4
                //Delivery :- Nihad Shaikh (nihad@sysllc.com) Logistic Dept. Head 29
                //Receivable :- Shameen Khot (accounts1@sysllc.com) Accounts Receivable 3

                //Accounts Dept Head
                if (Auth::user()->role_id == 27) {
                    $query = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id');
                }
                //sales
                if (Auth::user()->role_id == 8) {
                    $query = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->where([['accounts', '=', 1]]);
                }
                //purchease
                if (Auth::user()->role_id == 9) {
                    $query = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->where([['accounts', '=', 1], ['sales', '=', 1]]);
                }
                //invoice
                if (Auth::user()->role_id == 4) {
                    $query = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track_approval_invoice.invoice_no', 'sys_crm_deals.company_id', 'sys_crm_deal_track.company_id', 'sys_crm_deals.deal_currency', 'sys_crm_deals.deal_value')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_track_id', 'sys_crm_deal_track.id')->where([['accounts', '=', 1], ['sales', '=', 1]])->wherein('purchease', [1, 4]);

                    // $query = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at','sys_crm_deals.date')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where('sys_crm_deal_track.sales', 1)->wherein('sys_crm_deal_track.purchease',[1,4])->where(
                    //     function($q){
                    //         $q->where('sys_crm_deal_track.invoice', 0)
                    //           ->orWhere('sys_crm_deal_track.invoice', 3);
                    //    }
                    // );

                }
                //delivery
                if (Auth::user()->role_id == 29) {
                    $query = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->where([['accounts', '=', 1], ['sales', '=', 1], ['purchease', '=', 1], ['invoice', '=', 1]]);
                }
                //receivables
                if (Auth::user()->role_id == 3) {
                    $query = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deals.deal_value', 'sys_crm_deals.deal_currency')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->where([['accounts', '=', 1], ['sales', '=', 1], ['purchease', '=', 1], ['invoice', '=', 1], ['delivery', '=', 1]]);
                }
                $ctrl_status_id = $track;
                //accounts
                if (Auth::user()->role_id == 27) {
                    $query->where('sys_crm_deal_track.accounts', $track);
                }
                //sales
                else if (Auth::user()->role_id == 8) {
                    $query->where('sys_crm_deal_track.sales', $track);
                }
                //purchease
                else if (Auth::user()->role_id == 9) {
                    $query->where('sys_crm_deal_track.purchease', $track);
                }
                //invoice
                else if (Auth::user()->role_id == 4) {
                    $query->where('sys_crm_deal_track.invoice', $track);
                }
                //delivery
                else if (Auth::user()->role_id == 29) {
                    $query->where('sys_crm_deal_track.delivery', $track);
                }
                //receivables
                else if (Auth::user()->role_id == 3) {
                    $query->where('sys_crm_deal_track.receivables', $track);
                } else {
                    if ($track == 0) {
                        $query->orwhere('sys_crm_deal_track.accounts', $track);
                    } else {
                        $query->orwhere('sys_crm_deal_track.accounts', $track);
                        $query->orwhere('sys_crm_deal_track.sales', $track);
                        $query->orwhere('sys_crm_deal_track.purchease', $track);
                        $query->orwhere('sys_crm_deal_track.invoice', $track);
                        $query->orwhere('sys_crm_deal_track.delivery', $track);
                        $query->orwhere('sys_crm_deal_track.receivables', $track);
                    }
                }
                $dealtrack = $query->where('stage', 4)->where('sys_crm_deal_track.company_id', session('logged_session_data.company_id'))->orderby('id', 'desc')->paginate(50);
                //return $dealtrack;

                // if(session('logged_session_data.designation_id')==2){
                //     $query->where('sys_crm_deals.stage','=', 4);
                //     $dealtrack = $query->orderby('receivables','asc')->orderby('id','desc')->get();
                // }else{
                //     $query->where('sys_crm_deals.stage','=', 4);
                //     $dealtrack = $query->orderby('id','desc')->get();
                // }
            }

            return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function crmdealuploadimgview()
    {
        try {
            return view('backEnd.crm.uploadForm');
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function crmdealuploadimg(Request $request)
    {
        try {

            $lpo_file = "";

            if ($request->file('lpo') != "") {
                $files = $request->file('lpo');
                for ($i = 0; $i < count($files); $i++) {
                    $file1 = $files[$i];

                    if (str_contains($file1->getClientOriginalName(), 'dubai-uae')) {
                        $newname = str_replace('dubai-uae', 'georgia-us', $file1->getClientOriginalName());
                    } else if (str_contains($file1->getClientOriginalName(), 'dubai')) {
                        $newname = str_replace('dubai', 'georgia', $file1->getClientOriginalName());
                    } else if (str_contains($file1->getClientOriginalName(), 'Dubai')) {
                        $newname = str_replace('Dubai', 'georgia', $file1->getClientOriginalName());
                    } else if (str_contains($file1->getClientOriginalName(), 'UAE')) {
                        $newname = str_replace('UAE', 'us', $file1->getClientOriginalName());
                    } else if (str_contains($file1->getClientOriginalName(), 'uae')) {
                        $newname = str_replace('uae', 'us', $file1->getClientOriginalName());
                    } else {
                        $newname = $file1->getClientOriginalName();
                    }

                    $lpo_file = $newname;
                    $file1->move('public/uploads/syscom_us/', $lpo_file);
                    $lpo[] = $lpo_file;
                }
            }
            return "success";
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function crmdealsdeliveryupdateitems(Request $request)
    {
        try {
            foreach ($request->checkbx as $chk) {
                $a = 'qty_' . $chk;
                $quote_item_id = $chk;
                $qty = $request->$a;

                $values = array('deal_id' => $request->update_item_deal_id, 'quote_item_id' => $quote_item_id, 'qty' => $qty, 'updated_on' => Carbon::now('+04:00')->format('Y-m-d H:i:00'));
                $chk = DB::table('sys_crm_deal_delivery_items')->where($values)->count();
                if ($chk == 0) {
                    DB::table('sys_crm_deal_delivery_items')->insert($values);
                }
            }
            Toastr::success('Item QTY Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            //return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

     public function crmdealtrackGRN(Request $request)
    {
        try {
            $data = [
                'deal_id' => $request->grn_deal_id,
                'grn_status' => $request->grn_submitted,
                'deal_track' => $request->grn_deal_track_id,
                'remarks' => $request->remarks,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:00'),
                'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:00'),
            ];

            DealTrackGrnStatus::updateOrCreate(
                [
                    'deal_track' => $request->grn_deal_track_id,
                    'deal_id' => $request->grn_deal_id,
                ],
                $data
            );

            Toastr::success('GRN Status Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            //return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
}
