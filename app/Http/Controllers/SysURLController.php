<?php

namespace App\Http\Controllers;

use App\SmItem;
use App\SmStaff;
use App\SmSupplier;
use App\SysCompany;
use App\SysPurchaseOrder;
use App\SysPurchaseOrderItems;
use App\SysPurchaseOrderAttachment;
use App\SmQuotation;
use App\SysCurrencySettings;
use App\SysPaymentTerms;
use App\SysShipping;
use App\SmGeneralSettings;
use App\SmQuotationProducts;
use App\ApiBaseMethod;
use App\SysAppTabs;
use App\SmInspectingDepartment;
use App\SysCustomer;
use App\SysSupplierType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Brian2694\Toastr\Facades\Toastr;
//use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade as PDF;


use App\Role;
use App\SysChartofAccounts;
use App\SysClearance;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCustSuppl;
use App\SysDeliveryNote;
use App\SysGRNItemsCart;
use App\SysHelper;
use App\SysItemStock;
use App\SysJournalVoucher;
use App\SysPackingList;
use App\SysPayment;
use App\SysProformaInvoice;
use App\SysPurchaseGRN;
use App\SysPurchaseGRNItems;
use App\SysPurchaseGRNItemsSrlnoCart;
use App\SysPurchaseInvoice;
use App\SysPurchaseOrderItemsCart;
use App\SysPurchaseReturn;
use App\SysPurchaseType;
use App\SysReceipt;
use App\SysSalesInvoice;
use App\SysSalesReturn;
use App\SysStates;
use App\SysStockIn;
use App\SysStockOut;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Nexmo\Numbers\Number;

use function GuzzleHttp\Promise\exception_for;


class SysURLController extends Controller
{
    public function deal($id)
    {
        try{
            $query = SysCrmDeals::select('id')->where('code',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('crm-deals/show/'.$query->id);
            }
            //Toastr::error('Operation Failed', 'Failed');
            Toastr::error('You dont have access to this deal or the Deal ID is invalid', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
      public function deal_track($id)
    {
        try{
            $query = SysCrmDeals::select('t.id')
            ->join('sys_crm_deal_track as t','t.deal_id','sys_crm_deals.id')
            ->where('sys_crm_deals.code',$id)->where('sys_crm_deals.company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('crm-deal-track-approval-list/'.$query->id);
            }
            //Toastr::error('Operation Failed', 'Failed');
            Toastr::error('You dont have access to this deal or the Deal ID is invalid', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function purchase_order($id)
    {
        try{
            $query = SysPurchaseOrder::select('id')->where('doc_number',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('purchase-order/'.$query->id);
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function purchase_order_edit($id)
    {
        try{
            $query = SysPurchaseOrder::select('id')
            ->where(function($q) use ($id) {
                $q->where('doc_number', $id)
                  ->orWhereRaw("REGEXP_REPLACE(doc_number, '^[A-Z]+-', '') = ?", [$id]);
            })
            ->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('purchase-order/'.$query->id.'/edit');
            }
            Toastr::error('Wrong Doc Number', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function purchase_grn($id)
    {
        try{
            $query = SysPurchaseGRN::select('id')->where('doc_number',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('goods-receipt-note-list/'.$query->id);
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function purchase_grn_edit($id)
    {
        try{
            $query = SysPurchaseGRN::select('id')
            ->where(function($q) use ($id) {
                $q->where('doc_number', $id)
                  ->orWhereRaw("REGEXP_REPLACE(doc_number, '^[A-Z]+-', '') = ?", [$id]);
            })
            ->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('goods-receipt-note/'.$query->id.'/edit');
            }
            Toastr::error('Wrong Doc Number', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function purchase_invoice($id)
    {
        try{
            $query = SysPurchaseInvoice::select('id')->where('doc_number',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('purchase-invoice/'.$query->id);
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function purchase_invoice_edit($id)
    {
        try{
            $query = SysPurchaseInvoice::select('id')
            ->where(function($q) use ($id) {
                $q->where('doc_number', $id)
                  ->orWhereRaw("REGEXP_REPLACE(doc_number, '^[A-Z]+-', '') = ?", [$id]);
            })
            ->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('purchase-invoice/'.$query->id.'/edit');
            }
            Toastr::error('Wrong Doc Number', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function purchase_return($id)
    {
        try{
            $query = SysPurchaseReturn::select('id')->where('doc_number',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('purchase-return/'.$query->id);
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function purchase_return_edit($id)
    {
        try{
            $query = SysPurchaseReturn::select('id')
            ->where(function($q) use ($id) {
                $q->where('doc_number', $id)
                  ->orWhereRaw("REGEXP_REPLACE(doc_number, '^[A-Z]+-', '') = ?", [$id]);
            })
            ->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('purchase-return/'.$query->id.'/edit');
            }
            Toastr::error('Wrong Doc Number', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function proforma_invoice($id)
    {
        try{
            $query = SysProformaInvoice::select('id')->where('doc_number',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('proforma-invoice/'.$query->id.'/view');
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function sales_invoice($id)
    {
        try{
            $query = SysSalesInvoice::select('id')->where('doc_number',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('sales-invoice/'.$query->id);
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function sales_invoice_pdf_download($id)
    {
        try{
            $query = SysSalesInvoice::select('id')->where('doc_number',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('sales-invoice/'.$query->id.'/download');
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    
    public function sales_invoice_edit($id)
    {
        try{
            $query = SysSalesInvoice::select('id')
            ->where(function($q) use ($id) {
                $q->where('doc_number', $id)
                  ->orWhereRaw("REGEXP_REPLACE(doc_number, '^[A-Z]+-', '') = ?", [$id]);
            })
            ->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('sales-invoice/'.$query->id.'/edit');
            }
            Toastr::error('Wrong Doc Number', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function delivery_note($id)
    {
        try{
            $query = SysDeliveryNote::select('id')->where('doc_number',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('delivery-note/'.$query->id);
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function delivery_note_edit($id)
    {
        try{
            $query = SysDeliveryNote::select('id')
            ->where(function($q) use ($id) {
                $q->where('doc_number', $id)
                  ->orWhereRaw("REGEXP_REPLACE(doc_number, '^[A-Z]+-', '') = ?", [$id]);
            })
            ->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('delivery-note/'.$query->id.'/edit');
            }
            Toastr::error('Wrong Doc Number', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function sales_return($id)
    {
        try{
            $query = SysSalesReturn::select('id')->where('doc_number',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('sales-return/'.$query->id);
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function sales_return_edit($id)
    {
        try{
            $query = SysSalesReturn::select('id')
            ->where(function($q) use ($id) {
                $q->where('doc_number', $id)
                  ->orWhereRaw("REGEXP_REPLACE(doc_number, '^[A-Z]+-', '') = ?", [$id]);
            })
            ->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('sales-return/'.$query->id.'/edit');
            }
            Toastr::error('Wrong Doc Number', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function clearance($id)
    {
        try{
            $query = SysClearance::select('id')->where('doc_no',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('clearance/'.$query->id.'/preview');
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    
    public function journalvoucher($id)
    {
        try{
            $query = SysJournalVoucher::select('id')->where('doc_number',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('journalvoucher/'.$query->id);
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function journalvoucher_edit($id)
    {
        try{
            $query = SysJournalVoucher::select('id')
            ->where(function($q) use ($id) {
                $q->where('doc_number', $id)
                  ->orWhereRaw("REGEXP_REPLACE(doc_number, '^[A-Z]+-', '') = ?", [$id]);
            })
            ->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('journalvoucher/'.$query->id.'/edit');
            }
            Toastr::error('Wrong Doc Number', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function receipt($id)
    {
        try{
            $query = SysReceipt::select('id')->where('doc_number',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('receipt/'.$query->id);
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function receipt_edit($id)
    {
        try{
            $query = SysReceipt::select('id')
            ->where(function($q) use ($id) {
                $q->where('doc_number', $id)
                  ->orWhereRaw("REGEXP_REPLACE(doc_number, '^[A-Z]+-', '') = ?", [$id]);
            })
            ->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('receipt/'.$query->id.'/edit');
            }
            Toastr::error('Wrong Doc Number', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function payment($id)
    {
        try{
            $query = SysPayment::select('id')->where('doc_number',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('payment/'.$query->id);
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function payment_edit($id)
    {
        try{
            $query = SysPayment::select('id')
            ->where(function($q) use ($id) {
                $q->where('doc_number', $id)
                  ->orWhereRaw("REGEXP_REPLACE(doc_number, '^[A-Z]+-', '') = ?", [$id]);
            })
            ->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('payment/'.$query->id.'/edit');
            }
            Toastr::error('Wrong Doc Number', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    
    public function stock_in_edit($id)
    {
        try{
            $query = SysStockIn::select('id')
            ->where(function($q) use ($id) {
                $q->where('doc_number', $id)
                  ->orWhereRaw("REGEXP_REPLACE(doc_number, '^[A-Z]+-', '') = ?", [$id]);
            })
            ->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('stock-in/'.$query->id.'?stockin_action=edit');
            }
            Toastr::error('Wrong Doc Number', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function stock_out_edit($id)
    {
        try{
            $query = SysStockOut::select('id')
            ->where(function($q) use ($id) {
                $q->where('doc_number', $id)
                  ->orWhereRaw("REGEXP_REPLACE(doc_number, '^[A-Z]+-', '') = ?", [$id]);
            })
            ->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('stock-out/'.$query->id.'?stockout_action=edit');
            }
            Toastr::error('Wrong Doc Number', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function packing_list_edit($id)
    {
        try{
            $query = SysPackingList::select('id')
            ->where(function($q) use ($id) {
                $q->where('doc_number', $id)
                  ->orWhereRaw("REGEXP_REPLACE(doc_number, '^[A-Z]+-', '') = ?", [$id]);
            })
            ->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('packing-list/'.$query->id.'/edit');
            }
            Toastr::error('Wrong Doc Number', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function customer($id)
    {
        try{
            $query = SysCustSuppl::select('id')->where('code',$id)->first();
            if($query!=""){
                return redirect('customers/'.$query->id);
            }
            dd($query);
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
            dd($e);
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function supplier($id)
    {
        try{
            $query = SysCustSuppl::select('id')->where('code',$id)->first();
            if($query!=""){
                return redirect('suppliers/'.$query->id);
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function generalledger($id)
    {
        try{
            $from_date = date('01/01/Y');
            $to_date = date('d/m/Y');
            
            return '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Redirecting...</title>
        </head>
        <body>
            <form id="postForm" method="POST" action="'.url('generalledger').'">
                '.csrf_field().'
                <input type="hidden" name="account_id[]" value="'.$id.'">
                <input type="hidden" name="from_date" value="'.$from_date.'">
                <input type="hidden" name="to_date" value="'.$to_date.'">
            </form>

            <script>
                document.getElementById("postForm").submit();
            </script>
        </body>
        </html>';

        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    public function get_url_purchase_invoice($doc_number)
    {
        try{
            $query = SysPurchaseInvoice::select('id')->where('doc_number',$doc_number)->first();
            if($query!=""){
                return redirect('purchase-invoice/'.$query->id);
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }

    }

    public function get_url_purchase_order($doc_number)
    {
        try{
            $query = SysPurchaseOrder::select('id')->where('doc_number',$doc_number)->first();
            if($query!=""){
                return redirect('purchase-order/'.$query->id);
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }

    }

    public function stock_out($id)
    {
        try{
            $query = SysStockOut::select('id')->where('doc_number',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('stock-out/'.$query->id);
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function stock_in($id)
    {
        try{
            $query = SysStockIn::select('id')->where('doc_number',$id)->where('company_id', session('logged_session_data.company_id'))->first();
            if($query!=""){
                return redirect('stock-in/'.$query->id);
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function get_customer_from_chart_of_accounts($id)
    {
        try {
            $query = SysChartofAccounts::with('cust_suppl')->find($id);
    
            if (!$query || !$query->cust_suppl) {
                Toastr::error('Customer not found', 'Failed');
                return redirect()->back();
            }
    
            return redirect('customers/' . $query->cust_suppl->id);
    
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }
    }
    
}