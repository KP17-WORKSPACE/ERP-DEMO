<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\SmStaff;
use App\SysChartofAccounts;
use App\SysCompany;
use App\SysCrmDeals;
use App\SysCrmDealsComments;
use App\SysCrmDealTrack;
use App\SysCrmDealTrackApprovalAccounts;
use App\SysCrmDealTrackApprovalDelivery;
use App\SysCrmDealTrackApprovalInvoice;
use App\SysCrmDealTrackApprovalPurchease;
use App\SysCrmDealTrackApprovalReceivables;
use App\SysCrmDealTrackApprovalSales;
use App\SysCrmDealTrackApprovalTechnical;
use App\SysCrmQuoteItems;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysDriver;
use App\SysHelper;
use App\SysPaymentTerms;
use App\SysShipping;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\SysDealItemInvoiced;
use App\SysDealPurchaseOrderItems;
use App\SysDeliveryNote;
use App\SysDeliveryNoteItems;
use App\SysPayment;
use App\SysProformaInvoice;
use App\SysPurchaseAuto;
use App\SysPurchaseGRN;
use App\SysPurchaseInvoice;
use App\SysPurchaseOrder;
use App\SysPurchaseOrderItems;
use App\SysPurchaseOrderItemsCart;
use App\SysPurchaseReturn;
use App\SysReceipt;
use App\SysSalesInvoice;
use App\SysSalesReturn;
use App\SysChartofAccountsTransaction;
use App\SysClearance;
use App\SysCrmQuoteCharges;
use App\SysCrmEndUser;






class SysCrmDealTrackStatusController extends Controller
{


    public function index(Request $request, $id = null)
    {
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];

        $rolearray=[1,28,27,10,3,2,4,29,26,9,30,8,32];

        if(in_array(Auth::user()->role_id, $rolearray)) {
            $staff      = SmStaff::select('user_id','full_name')->where('active_status',1)->orderby('full_name','asc')->get();
            $vendors = SysHelper::get_customer_list_deal_lead_all_role();
        } else{
            $vendors = SysHelper::get_customer_list_deal_lead();
            $staff      = SysHelper::get_sales_persons();
        }

        $ctrl_deal_id = "";
        $ctrl_company_id = "";
        $ctrl_company_id2 = "";
        $ctrl_owner_id = "";
        $ctrl_status_id = "10";
        $ctrl_date = '';
        $ctrl_partial_delivery = '';
        $ctrl_not_applicable = "";
        $ctrl_date_from = "";
        $ctrl_date_to = "";
        $filter_by = "";
        $company_list = SysCompany::select('id', 'company_name')->orderby('sort_id', 'asc')->get();


        try {
            $query = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.cust_id', 'sys_crm_deals.stage as deal_stage', 'sys_crm_deals.owner', 'sys_crm_deals.deal_value', 'sys_crm_deals.deal_currency')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id');

            if (SysHelper::get_pagination_post($request)) {
                //if($_POST){
                if ($request->deal_id != "") {
                    $query->where('sys_crm_deals.code', $request->deal_id);
                    $ctrl_deal_id = $request->deal_id;
                }
                if ($request->company_id != "") {
                    $query->where('sys_crm_deals.cust_id', $request->company_id);
                    $ctrl_company_id = $request->company_id;
                }
                if ($request->company_id2 != "") {
                    $query->where('sys_crm_deals.company_id', $request->company_id2);
                    $ctrl_company_id2 = $request->company_id2;
                }
                if ($request->owner_id != "") {
                    $query->where('sys_crm_deals.owner', $request->owner_id);
                    $ctrl_owner_id = $request->owner_id;
                }

                if (!empty($request->date_from)) {
                    $ctrl_date_from = SysHelper::normalizeToYmd($request->date_from);
                }
                if (!empty($request->date_to)) {
                    $ctrl_date_to =  SysHelper::normalizeToYmd($request->date_to);
                }

                // Priority 2: Predefined filters (only if manual dates are not used)
                if (!empty($request->filter_by)) {

                    switch ($request->filter_by) {
                        case "today":
                            $ctrl_date_from = $ctrl_date_to = date('Y-m-d');
                            $filter_by = 'today';
                            break;

                        case "this_week":
                            $ctrl_date_from = date('Y-m-d', strtotime('last sunday'));
                            $ctrl_date_to = date('Y-m-d', strtotime('this saturday'));
                            $filter_by = 'this_week';
                            break;

                        case "last_week":
                            $ctrl_date_from = date('Y-m-d', strtotime('last sunday -7 days'));
                            $ctrl_date_to = date('Y-m-d', strtotime('last saturday'));
                            $filter_by = 'last_week';
                            break;

                        case "this_month":
                            $ctrl_date_from = date('Y-m-01');
                            $ctrl_date_to = date('Y-m-t');
                            $filter_by = 'this_month';
                            break;

                        case "last_month":
                            $ctrl_date_from = date('Y-m-d', strtotime('first day of previous month'));
                            $ctrl_date_to = date('Y-m-d', strtotime('last day of previous month'));
                            $filter_by = 'last_month';
                            break;

                        case "last_6_months":
                            $ctrl_date_from = date('Y-m-d', strtotime('first day of this month - 6 months'));
                            $ctrl_date_to = date('Y-m-d', strtotime("last day of this month"));
                            $filter_by = 'last_6_months';
                            break;

                        case "this_year":
                            $ctrl_date_from = date('Y-01-01');
                            $ctrl_date_to = date('Y-12-31');
                            $filter_by = 'this_year';
                            break;

                        case "last_year":
                            $ctrl_date_from = date('Y-01-01', strtotime('-1 year'));
                            $ctrl_date_to = date('Y-12-31', strtotime('-1 year'));
                            $filter_by = 'last_year';
                            break;
                    }


                }

                // Apply filter only if both dates are set
                if (!empty($ctrl_date_from) && !empty($ctrl_date_to)) {
                    $query->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_date, '%Y-%m-%d') >= '" . date('Y-m-d', strtotime($ctrl_date_from)) . "' and DATE_FORMAT(sys_crm_deal_track.created_date, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime($ctrl_date_to)) . "'");
                } elseif (!empty($ctrl_date_from)) {
                    $query->whereDate('sys_crm_deal_track.created_date', '>=', $ctrl_date_from);
                } elseif (!empty($ctrl_date_to)) {
                    $query->whereDate('sys_crm_deal_track.created_date', '<=', $ctrl_date_to);
                }


                if ($request->status_id == "PD1") {
                    $query->where('sys_crm_deal_track.partial_delivery', 1);
                    $query->where('sys_crm_deal_track.delivery', '!=', 1);
                    $ctrl_partial_delivery = 1;
                    $request->status_id = "";
                }

                if ($request->status_id == "P") {
                    $query->where('sys_crm_deal_track.purchease_approval', 0);
                    $ctrl_not_applicable = "P";
                    $request->status_id = "";
                    

                }
                if ($request->status_id == "I") {
                    $query->where('sys_crm_deal_track.invoice_approval', 0);
                    $ctrl_not_applicable = "I";
                    $request->status_id = "";

                }
                if ($request->status_id == "D") {
                    $query->where('sys_crm_deal_track.delivery_approval', 0);
                    $ctrl_not_applicable = "D";
                    $request->status_id = "";

                }
                if ($request->status_id == "R") {
                    $query->where('sys_crm_deal_track.receivables_approval', 0);
                    $ctrl_not_applicable = "R";
                    $request->status_id = "";

                }

                if ($request->status_id != "" && $request->status_id != "10") {
                    $ctrl_status_id = $request->status_id;

                    $track = str_split($request->status_id, 1)[0];
                    $status = str_split($request->status_id, 1)[1];


                    //accounts
                    if ($track == "A") {
                        if ($status == 4) {
                            $query->whereNotIn('sys_crm_deal_track.accounts', [1, 2, 3]);
                        } else {
                            $query->where('sys_crm_deal_track.accounts', $status);
                        }

                        SysHelper::applyDateFiltersBase($query, $ctrl_date_from, $ctrl_date_to, 'account');

                    }
                    //sales
                    else if ($track == "S") {
                        if ($status == 4) {
                            $query->whereNotIn('sys_crm_deal_track.sales', [1, 2, 3]);
                        } else {
                            $query->where('sys_crm_deal_track.sales', $status);
                        }

                        SysHelper::applyDateFiltersBase($query, $ctrl_date_from, $ctrl_date_to, 'sales');

                    }
                    //purchease
                    else if ($track == "P") {
                        if ($status == 5) {
                            $query->whereNotIn('sys_crm_deal_track.purchease', [1, 2, 3, 4])->where('sys_crm_deal_track.purchease_approval', '!=', 0);
                        } else {
                            $query->where('sys_crm_deal_track.purchease', $status);
                        }
                        SysHelper::applyDateFiltersBase($query, $ctrl_date_from, $ctrl_date_to, 'purchease');

                    }
                    //invoice
                    else if ($track == "I") {
                        if ($status == 4) {
                            $query->whereNotIn('sys_crm_deal_track.invoice', [1, 2, 3])->where('sys_crm_deal_track.invoice_approval', '!=', 0);
                        } else {
                            $query->where('sys_crm_deal_track.invoice', $status);
                        }
                        SysHelper::applyDateFiltersBase($query, $ctrl_date_from, $ctrl_date_to, 'invoice');

                    }
                    //delivery
                    else if ($track == "D") {
                        if ($status == 7) {
                            $query->whereNotIn('sys_crm_deal_track.delivery', [1, 2, 3, 4, 5, 6])->where('sys_crm_deal_track.delivery_approval', '!=', 0);
                        } else {
                            $query->where('sys_crm_deal_track.delivery', $status);
                        }
                        SysHelper::applyDateFiltersBase($query, $ctrl_date_from, $ctrl_date_to, 'delivery');

                    }
                    //receivables
                    else if ($track == "R") {
                        if ($status == 7) {
                            $query->whereNotIn('sys_crm_deal_track.receivables', [1, 2, 3, 4])->where('sys_crm_deal_track.receivables_approval', '!=', 0);
                        } else {
                            $query->where('sys_crm_deal_track.receivables', $status);
                        }
                        SysHelper::applyDateFiltersBase($query, $ctrl_date_from, $ctrl_date_to, 'receivablesApproval');

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
            }

            if (session('logged_session_data.company_id') != 1) {
                $query->wherein('sys_crm_deal_track.company_id', $company_id);
            }

            if (Auth::user()->role_id == 5) {
                $query->where('sys_crm_deals.owner', Auth::user()->id);
            }






            $clone_query = clone $query;

            $records = $clone_query->with([
                'account',
                'salesApproval',
                'purcheaseApproval',
                'invoiceApproval',
                'deliveryApproval',
                'receivablesApproval'
            ])->get();

            $counters = [
                'account' => ['seconds' => 0, 'count' => 0],
                'sales' => ['seconds' => 0, 'count' => 0],
                'purchase' => ['seconds' => 0, 'count' => 0],
                'invoice' => ['seconds' => 0, 'count' => 0],
                'delivery' => ['seconds' => 0, 'count' => 0],
                'receivables' => ['seconds' => 0, 'count' => 0],
            ];

            foreach ($records as $record) {
                // Account Approval Time
                if ($record->created_date && in_array($record->accounts, [1, 2]) && optional($record->account)->created_date) {

                    $counters['account']['seconds'] += Carbon::parse($record->account->created_date)->diffInSeconds($record->created_date, true);
                    $counters['account']['count']++;
                }

                // Sales Approval Time
                if (in_array($record->sales, [1, 2]) && optional($record->salesApproval)->created_date && optional($record->account)->created_date) {
                    $counters['sales']['seconds'] += Carbon::parse($record->salesApproval->created_date)->diffInSeconds($record->account->created_date, true);
                    $counters['sales']['count']++;
                }

                // Purchase Approval Time
                if ($record->purchease_approval != 0 && in_array($record->purchease, [1, 2, 3, 4]) && optional($record->purcheaseApproval)->created_date) {
                    $from = optional($record->salesApproval)->created_date ?? optional($record->account)->created_date;
                    if ($from) {
                        $counters['purchase']['seconds'] += Carbon::parse($record->purcheaseApproval->created_date)->diffInSeconds($from, true);
                        $counters['purchase']['count']++;
                    }
                }

                // Invoice Approval Time
                if ($record->invoice_approval != 0 && in_array($record->invoice, [1, 2]) && optional($record->invoiceApproval)->created_date) {
                    $from = optional($record->purcheaseApproval)->created_date
                        ?? optional($record->salesApproval)->created_date
                        ?? optional($record->account)->created_date;

                    if ($from) {
                        $counters['invoice']['seconds'] += Carbon::parse($record->invoiceApproval->created_date)->diffInSeconds($from, true);
                        $counters['invoice']['count']++;
                    }
                }

                // Delivery Approval Time
                if ($record->delivery_approval != 0 && in_array($record->delivery, [1, 2, 3, 4, 5, 6]) && optional($record->deliveryApproval)->created_date) {
                    $from = optional($record->invoiceApproval)->created_date
                        ?? optional($record->purcheaseApproval)->created_date
                        ?? optional($record->salesApproval)->created_date
                        ?? optional($record->account)->created_date;

                    if ($from) {
                        $counters['delivery']['seconds'] += Carbon::parse($record->deliveryApproval->created_date)->diffInSeconds($from, true);
                        $counters['delivery']['count']++;
                    }
                }

                // Receivables Approval Time
                if ($record->receivables_approval != 0 && in_array($record->receivables, [1, 2, 3, 4]) && optional($record->receivablesApproval)->created_date) {
                    $from = optional($record->deliveryApproval)->created_date
                        ?? optional($record->invoiceApproval)->created_date
                        ?? optional($record->purcheaseApproval)->created_date
                        ?? optional($record->salesApproval)->created_date
                        ?? optional($record->account)->created_date;

                    if ($from) {
                        $counters['receivables']['seconds'] += Carbon::parse($record->receivablesApproval->created_date)->diffInSeconds($from, true);
                        $counters['receivables']['count']++;
                    }
                }
            }


            $deal_stats_avg = [];

            foreach ($counters as $key => $data) {
                if ($data['count'] > 0) {
                    $avgSeconds = intval(round($data['seconds'] / $data['count']));
                    $deal_stats_avg["{$key}_approval_time"] = Carbon::now()->subSeconds($avgSeconds)->diffForHumans(null, true);
                } else {
                    $deal_stats_avg["{$key}_approval_time"] = 'N/A';
                }
            }



            // $clone_query = clone $query->with([
            //     'account',
            //     'salesApproval',
            //     'purcheaseApproval',
            //     'invoiceApproval',
            //     'deliveryApproval',
            //     'receivablesApproval'
            // ]);


            if (!empty($ctrl_date_from) || !empty($ctrl_date_to)) {


                $clone_query
                    ->leftJoin('sys_crm_deal_track_approval_accounts', 'sys_crm_deal_track_approval_accounts.deal_id', '=', 'sys_crm_deal_track.deal_id')
                    ->leftJoin('sys_crm_deal_track_approval_sales', 'sys_crm_deal_track_approval_sales.deal_id', '=', 'sys_crm_deal_track.deal_id')
                    ->leftJoin('sys_crm_deal_track_approval_receivables', 'sys_crm_deal_track_approval_receivables.deal_id', '=', 'sys_crm_deal_track.deal_id')
                    ->leftJoin('sys_crm_deal_track_approval_purchease', 'sys_crm_deal_track_approval_purchease.deal_id', '=', 'sys_crm_deal_track.deal_id')
                    ->leftJoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', '=', 'sys_crm_deal_track.deal_id')
                    ->leftJoin('sys_crm_deal_track_approval_delivery', 'sys_crm_deal_track_approval_delivery.deal_id', '=', 'sys_crm_deal_track.deal_id');



                $account_new = clone $clone_query;
                $account_new->whereNotIn('sys_crm_deal_track.accounts', [1, 2, 3]);
                $account_new = SysHelper::applyDateFilters($account_new, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_accounts');
                $deal_stats['account_new'] = $account_new->count();

                $account_pending = clone $clone_query;
                $account_pending->where('sys_crm_deal_track.accounts', 3);
                $account_pending = SysHelper::applyDateFilters($account_pending, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_accounts');
                $deal_stats['account_pending'] = $account_pending->count();


                $account_approved = clone $clone_query;
                $account_approved->where('sys_crm_deal_track.accounts', 1);
                $account_approved = SysHelper::applyDateFilters($account_approved, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_accounts');
                $deal_stats['account_approved'] = $account_approved->count();


                $account_rejected = clone $clone_query;
                $account_rejected->where('sys_crm_deal_track.accounts', 2);
                $account_rejected = SysHelper::applyDateFilters($account_rejected, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_accounts');
                $deal_stats['account_rejected'] = $account_rejected->count();


                $sales_approved = clone $clone_query;
                $sales_approved->where('sys_crm_deal_track.sales', 1);
                $sales_approved = SysHelper::applyDateFilters($sales_approved, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_sales');
                $deal_stats['sales_approved'] = $sales_approved->count();



                $sales_rejected = clone $clone_query;
                $sales_rejected->where('sys_crm_deal_track.sales', 2);
                $sales_rejected = SysHelper::applyDateFilters($sales_rejected, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_sales');
                $deal_stats['sales_rejected'] = $sales_rejected->count();

                $sales_pending = clone $clone_query;
                $sales_pending->where('sys_crm_deal_track.sales', 3);
                $sales_pending = SysHelper::applyDateFilters($sales_pending, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_sales');
                $deal_stats['sales_pending'] = $sales_pending->count();

                $sales_new = clone $clone_query;
                $sales_new->whereNotIn('sys_crm_deal_track.sales', [1, 2, 3]);
                $sales_new = SysHelper::applyDateFilters($sales_new, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_sales');
                $deal_stats['sales_new'] = $sales_new->whereNotIn('sales', [1, 2, 3])->count();


                $purchease_approved = clone $clone_query;
                $purchease_approved = $purchease_approved->where('purchease', 1)->where('sys_crm_deal_track.purchease_approval', '!=', 0);
                $purchease_approved = SysHelper::applyDateFilters($purchease_approved, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_purchease');
                $deal_stats['purchease_approved'] = $purchease_approved->count();

                $purchease_rejected = clone $clone_query;
                $purchease_rejected = $purchease_rejected->where('purchease', 2)->where('purchease_approval', '!=', 0);
                $purchease_rejected = SysHelper::applyDateFilters($purchease_rejected, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_purchease');
                $deal_stats['purchease_rejected'] = $purchease_rejected->count();

                $purchease_pending = clone $clone_query;
                $purchease_pending = $purchease_pending->where('purchease', 3)->where('purchease_approval', '!=', 0);
                $purchease_pending = SysHelper::applyDateFilters($purchease_pending, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_purchease');
                $deal_stats['purchease_pending'] = $purchease_pending->count();

                $purchease_delivery = clone $clone_query;
                $purchease_delivery = $purchease_delivery->where('purchease', 4)->where('purchease_approval', '!=', 0);
                $purchease_delivery = SysHelper::applyDateFilters($purchease_delivery, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_purchease');
                $deal_stats['purchease_delivery'] = $purchease_delivery->count();


                $purchease_new = clone $clone_query;
                $purchease_new = $purchease_new->whereNotIn('purchease', [1, 2, 3, 4])->where('purchease_approval', '!=', 0);
                $purchease_new = SysHelper::applyDateFilters($purchease_new, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_purchease');
                $deal_stats['purchease_new'] = $purchease_new->count();

                $invoice_approved = clone $clone_query;
                $invoice_approved = $invoice_approved->where('invoice', 1)->where('sys_crm_deal_track.invoice_approval', '!=', 0);
                $invoice_approved = SysHelper::applyDateFilters($invoice_approved, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_invoice');
                $deal_stats['invoice_approved'] = $invoice_approved->count();

                $invoice_rejected = clone $clone_query;
                $invoice_rejected = $invoice_rejected->where('invoice', 2)->where('sys_crm_deal_track.invoice_approval', '!=', 0);
                $invoice_rejected = SysHelper::applyDateFilters($invoice_rejected, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_invoice');
                $deal_stats['invoice_rejected'] = $invoice_rejected->count();

                $invoice_pending = clone $clone_query;
                $invoice_pending = $invoice_pending->where('invoice', 3)->where('sys_crm_deal_track.invoice_approval', '!=', 0);
                $invoice_pending = SysHelper::applyDateFilters($invoice_pending, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_invoice');
                $deal_stats['invoice_pending'] = $invoice_pending->count();

                $invoice_new = clone $clone_query;
                $invoice_new = $invoice_new->whereNotIn('invoice', [1, 2, 3])->where('sys_crm_deal_track.invoice_approval', '!=', 0);
                $invoice_new = SysHelper::applyDateFilters($invoice_new, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_invoice');
                $deal_stats['invoice_new'] = $invoice_new->count();


                $delivery_completed = clone $clone_query;
                $delivery_completed = $delivery_completed->where('delivery', 1)->where('sys_crm_deal_track.delivery_approval', '!=', 0);
                $delivery_completed = SysHelper::applyDateFilters($delivery_completed, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_delivery');
                $deal_stats['delivery_completed'] = $delivery_completed->count();

                $delivery_rejected = clone $clone_query;
                $delivery_rejected = $delivery_rejected->where('delivery', 2)->where('sys_crm_deal_track.delivery_approval', '!=', 0);
                $delivery_rejected = SysHelper::applyDateFilters($delivery_rejected, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_delivery');
                $deal_stats['delivery_rejected'] = $delivery_rejected->count();

                $out_for_delivery = clone $clone_query;
                $out_for_delivery = $out_for_delivery->where('delivery', 3)->where('sys_crm_deal_track.delivery_approval', '!=', 0);
                $out_for_delivery = SysHelper::applyDateFilters($out_for_delivery, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_delivery');
                $deal_stats['out_for_delivery'] = $out_for_delivery->count();

                $delivery_pending = clone $clone_query;
                $delivery_pending = $delivery_pending->where('delivery', 4)->where('sys_crm_deal_track.delivery_approval', '!=', 0);
                $delivery_pending = SysHelper::applyDateFilters($delivery_pending, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_delivery');
                $deal_stats['delivery_pending'] = $delivery_pending->count();

                $ready_for_delivery = clone $clone_query;
                $ready_for_delivery = $ready_for_delivery->where('delivery', 5)->where('sys_crm_deal_track.delivery_approval', '!=', 0);
                $ready_for_delivery = SysHelper::applyDateFilters($ready_for_delivery, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_delivery');
                $deal_stats['ready_for_delivery'] = $ready_for_delivery->count();

                $partial_delivery = clone $clone_query;
                $partial_delivery = $partial_delivery->where('delivery', 6)->where('sys_crm_deal_track.delivery_approval', '!=', 0);
                $partial_delivery = SysHelper::applyDateFilters($partial_delivery, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_delivery');
                $deal_stats['partial_delivery'] = $partial_delivery->count();

                $delivery_new = clone $clone_query;
                $delivery_new = $delivery_new->whereNotIn('delivery', [1, 2, 3, 4, 5, 6])->where('sys_crm_deal_track.delivery_approval', '!=', 0);
                $delivery_new = SysHelper::applyDateFilters($delivery_new, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_delivery');
                $deal_stats['delivery_new'] = $delivery_new->count();

                $payment_received = clone $clone_query;
                $payment_received = $payment_received->where('receivables', 1)->where('sys_crm_deal_track.receivables_approval', '!=', 0);
                $payment_received = SysHelper::applyDateFilters($payment_received, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_receivables');
                $deal_stats['payment_received'] = $payment_received->count();

                $payment_rejected = clone $clone_query;
                $payment_rejected = $payment_rejected->where('receivables', 2)->where('sys_crm_deal_track.receivables_approval', '!=', 0);
                $payment_rejected = SysHelper::applyDateFilters($payment_rejected, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_receivables');
                $deal_stats['payment_rejected'] = $payment_rejected->count();

                $payment_pending = clone $clone_query;
                $payment_pending = $payment_pending->where('receivables', 3)->where('sys_crm_deal_track.receivables_approval', '!=', 0);
                $payment_pending = SysHelper::applyDateFilters($payment_pending, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_receivables');
                $deal_stats['payment_pending'] = $payment_pending->count();

                $orders_cancelled = clone $clone_query;
                $orders_cancelled = $orders_cancelled->where('receivables', 4)->where('sys_crm_deal_track.receivables_approval', '!=', 0);
                $orders_cancelled = SysHelper::applyDateFilters($orders_cancelled, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_receivables');
                $deal_stats['orders_cancelled'] = $orders_cancelled->count();

                $receivables_new = clone $clone_query;
                $receivables_new = $receivables_new->whereNotIn('receivables', [1, 2, 3, 4])->where('sys_crm_deal_track.receivables_approval', '!=', 0);
                $receivables_new = SysHelper::applyDateFilters($receivables_new, $ctrl_date_from, $ctrl_date_to, 'sys_crm_deal_track_approval_receivables');
                $deal_stats['receivables_new'] = $receivables_new->count();

            } else {
                $clone_query = clone $query;


                $deal_stats['account_new'] = (clone $clone_query)->whereNotIn('accounts', [1, 2, 3])->count();
                $deal_stats['account_pending'] = (clone $clone_query)->where('accounts', 3)->count();
                $deal_stats['account_approved'] = (clone $clone_query)->where('accounts', 1)->count();
                $deal_stats['account_rejected'] = (clone $clone_query)->where('accounts', 2)->count();

                $deal_stats['sales_approved'] = (clone $clone_query)->where('sales', 1)->count();
                $deal_stats['sales_rejected'] = (clone $clone_query)->where('sales', 2)->count();
                $deal_stats['sales_pending'] = (clone $clone_query)->where('sales', 3)->count();
                $deal_stats['sales_new'] = (clone $clone_query)->whereNotIn('sales', [1, 2, 3])->count();



                $deal_stats['purchease_approved'] = (clone $clone_query)->where('purchease', 1)->where('purchease_approval', '!=', 0)->count();
                $deal_stats['purchease_rejected'] = (clone $clone_query)->where('purchease', 2)->where('purchease_approval', '!=', 0)->count();
                $deal_stats['purchease_pending'] = (clone $clone_query)->where('purchease', 3)->where('purchease_approval', '!=', 0)->count();
                $deal_stats['purchease_delivery'] = (clone $clone_query)->where('purchease', 4)->where('purchease_approval', '!=', 0)->count();
                $deal_stats['purchease_new'] = (clone $clone_query)->whereNotIn('purchease', [1, 2, 3, 4])->where('purchease_approval', '!=', 0)->count();


                $deal_stats['invoice_approved'] = (clone $clone_query)->where('invoice', 1)->where('invoice_approval', '!=', 0)->count();
                $deal_stats['invoice_rejected'] = (clone $clone_query)->where('invoice', 2)->where('invoice_approval', '!=', 0)->count();
                $deal_stats['invoice_pending'] = (clone $clone_query)->where('invoice', 3)->where('invoice_approval', '!=', 0)->count();
                $deal_stats['invoice_new'] = (clone $clone_query)->whereNotIn('invoice', [1, 2, 3])->where('invoice_approval', '!=', 0)->count();


                $deal_stats['delivery_completed'] = (clone $clone_query)->where('delivery', 1)->where('delivery_approval', '!=', 0)->count();
                $deal_stats['delivery_rejected'] = (clone $clone_query)->where('delivery', 2)->where('delivery_approval', '!=', 0)->count();
                $deal_stats['out_for_delivery'] = (clone $clone_query)->where('delivery', 3)->where('delivery_approval', '!=', 0)->count();
                $deal_stats['delivery_pending'] = (clone $clone_query)->where('delivery', 4)->where('delivery_approval', '!=', 0)->count();
                $deal_stats['ready_for_delivery'] = (clone $clone_query)->where('delivery', 5)->where('delivery_approval', '!=', 0)->count();
                $deal_stats['partial_delivery'] = (clone $clone_query)->where('delivery', 6)->where('delivery_approval', '!=', 0)->count();
                $deal_stats['delivery_new'] = (clone $clone_query)->whereNotIn('delivery', [1, 2, 3, 4, 5, 6])->where('delivery_approval', '!=', 0)->count();

                $deal_stats['payment_received'] = (clone $clone_query)->where('receivables', 1)->where('receivables_approval', '!=', 0)->count();
                $deal_stats['payment_rejected'] = (clone $clone_query)->where('receivables', 2)->where('receivables_approval', '!=', 0)->count();
                $deal_stats['payment_pending'] = (clone $clone_query)->where('receivables', 3)->where('receivables_approval', '!=', 0)->count();
                $deal_stats['orders_cancelled'] = (clone $clone_query)->where('receivables', 4)->where('receivables_approval', '!=', 0)->count();
                $deal_stats['receivables_new'] = (clone $clone_query)->whereNotIn('receivables', [1, 2, 3, 4])->where('receivables_approval', '!=', 0)->count();
            }



            


            //$dealtrack = $query->wherein('sys_crm_deals.company_id',$company_id)->orderby('id','desc')->paginate(30);
            $dealtrack = $query->orderby('id', 'desc')->paginate(200);



            if($id == null){
                if($dealtrack->first())
                $id = $dealtrack->first()->id;
            }

           

           
            $trackdata = $this->get_deal_track_data($id);

            $active_id = $id;



            return view('backEnd.crm.DealTrackStatusList', compact('dealtrack', 'vendors', 'staff', 'ctrl_deal_id', 'ctrl_company_id', 'ctrl_owner_id', 'ctrl_status_id', 'ctrl_date', 'ctrl_partial_delivery', 'ctrl_not_applicable', 'ctrl_date_from', 'ctrl_date_to', 'filter_by', 'company_list', 'ctrl_company_id2', 'deal_stats', 'deal_stats_avg','active_id','trackdata'));


        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
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

            $shipping = SysShipping::select('id', 'shipping_name')->where('status', 1)->get();
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




}