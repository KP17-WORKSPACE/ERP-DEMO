<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmInspectingDepartment;
use App\SmItem;
use App\SmItemCategory;
use Illuminate\Http\Request;
use App\SmItemStore;
use App\SmItemSubcategory;
use App\SmStaff;
use App\SysBrand;
use App\SysChartofAccounts;
use App\SysCompany;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCrmDealsComments;
use App\SysCrmQuoteCart;
use App\SysCrmQuoteCharges;
use App\SysCrmQuoteCSItems;
use App\SysCrmQuoteItems;
use App\SysCurrency;
use App\SysCurrencyRate;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysPriceBook;
use App\SysSalesInvoiceItemsCart;
use App\SysShipping;
use App\SysStockIn;
use App\SysStockInSerialNo;
use App\SysSupplierType;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;
use Validator;
use Barryvdh\DomPDF\Facade as PDF;
use PHPExcel;
use PHPExcel_IOFactory;
use App\SysVat; //kunal added

class SysCrmQuoteNewController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function createquote(Request $request, $id)//kunal modified
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            //$companylist = SysCompany::select('id','company_name','city')->where('status',1)->wherein('id',$company_id)->orderBy('company_name','ASC')->get();

            //if(session('logged_session_data.company_id')==1){
            //    $companylist = SysCompany::orderby('sort_id','asc')->get();
            //} else {
            $companylist = SysCompany::wherein('id', $company_id)->orderby('sort_id', 'asc')->get();
            //}

            $currencylist = SysCurrencySettings::select('id', 'code')->where('status', 1)->orderBy('code', 'ASC')->get();
            $paymentterms = SysPaymentTerms::all();
            $items = SysHelper::get_product_list($company_id);

            $edit = SysCrmDeals::where('id', $id)->first();
            $customer = SysCustSuppl::where('id', $edit->cust_id)->first();

            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_sales($company_id);
            $supplier = SysHelper::get_supplier_list($company_id);

            //$quotation = SysCrmDeals::where('id',$id)->first();
            $quotationitems = SysCrmQuoteCart::select('sys_crm_quote_cart.*', 'sm_items.part_number')
                ->join('sm_items', 'sm_items.id', 'sys_crm_quote_cart.product_id')
                ->where('cart_id', session('logged_session_data.cart_id'))->where('cust_id', $edit->cust_id)->where('user_id', Auth::user()->id)->orderby('id', 'ASC')->get();



            $basecompany_vat = SysVat::wherein('company_id', $company_id)->get();
            if (count($quotationitems) > 0) {
                $deal_id = $quotationitems[0]->deal_id;
                $company_id = $quotationitems[0]->company_id;
                $currency_id = $quotationitems[0]->currency_id;
                $customer_type = $quotationitems[0]->customer_type;
                $quote_validity = $quotationitems[0]->quote_validity;
                $payment_terms = $quotationitems[0]->payment_terms;
                $payment_terms_name = $quotationitems[0]->payment_terms_txt;
                $delivery_date = $quotationitems[0]->delivery_date;
                $delivery_time = $quotationitems[0]->delivery_time;
            } else {
                $deal_id = $id;
                $company_id = $customer->company_id;
                $currency_id = $companylist[0]->currency_id;
                $customer_type = $customer->account_type;
                $quote_validity = "2 Weeks";
                $payment_terms = $customer->payment_terms;
                $payment_terms_name = "";
                $delivery_date = $edit->estimated_close_date;
                $delivery_time = "2 Weeks";
            }
            return view('backEnd.crm.Quote_New', compact('edit', 'companylist', 'currencylist', 'paymentterms', 'deal_id', 'company_id', 'currency_id', 'customer_type', 'quote_validity', 'payment_terms', 'payment_terms_name', 'delivery_date', 'delivery_time', 'quotationitems', 'items', 'customs_freight_account', 'supplier', 'basecompany_vat'));


        } catch (\Throwable $th) {
            return $th;
        }
    }

    function crmquoteadditemscart(Request $request)
    {
        try {
            $ret = 0;
            // $check =  DB::table('sys_crm_quote_cart')->where(
            //     [
            //         'cart_id' => session('logged_session_data.cart_id'),
            //         'user_id' => Auth::user()->id,
            //         'deal_id' => $request->deal_id,
            //         'cust_id' => $request->cust_id,
            //         'company_id' => $request->company_id,
            //         'currency_id' => $request->currency_id,
            //         'customer_type' => $request->customer_type,
            //         'quote_validity' => $request->quote_validity,
            //         'payment_terms' => $request->payment_terms,
            //         'delivery_date' => $request->delivery_date,
            //         'payment_terms_txt' => $request->payment_terms_txt,
            //         'delivery_time' => $request->delivery_time,
            //         'product_id' => $request->product_id,
            //         'cost' => $request->cost,
            //         'qty' => $request->qty,
            //         'price' => $request->price,
            //         'description' => $request->description,
            //         'discount' =>  $request->discount,
            //         'vat' =>  $request->vat,
            //         'status' => 1,
            //         'sort_id' => 999,
            //         'created_by' => Auth::user()->id,
            //     ]
            //     )->count();
            // if($check==0){
            DB::table('sys_crm_quote_cart')->insert(
                [
                    'cart_id' => session('logged_session_data.cart_id'),
                    'user_id' => Auth::user()->id,
                    'deal_id' => $request->deal_id,
                    'cust_id' => $request->cust_id,
                    'company_id' => $request->company_id,
                    'currency_id' => $request->currency_id,
                    'customer_type' => $request->customer_type,
                    'quote_validity' => $request->quote_validity,
                    'payment_terms' => $request->payment_terms,
                    'delivery_date' => $request->delivery_date,
                    'payment_terms_txt' => $request->payment_terms_txt,
                    'delivery_time' => $request->delivery_time,
                    'product_id' => $request->product_id,
                    'cost' => $request->cost,
                    'qty' => $request->qty,
                    'price' => $request->price,
                    'description' => $request->description,
                    'discount' => $request->discount,
                    'vat' => $request->vat,
                    'status' => 1,
                    'sort_id' => 999,
                    'created_by' => Auth::user()->id,
                ]
            );
            //}
            $ret = 1;

            if ($ret == 1) {
                $ret = 'OK';
                return json_encode(array('data' => $ret));
            } else {
                $ret = 'ERROR';
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    function crmquoteupdateitemscart(Request $request)
    {
        try {
            $ret = 0;
            DB::table('sys_crm_quote_cart')->where('id', $request->itm_id)->update(
                [
                    'cust_id' => $request->cust_id,
                    'company_id' => $request->company_id,
                    'currency_id' => $request->currency_id,
                    'customer_type' => $request->customer_type,
                    'quote_validity' => $request->quote_validity,
                    'payment_terms' => $request->payment_terms,
                    'delivery_date' => $request->delivery_date,
                    'payment_terms_txt' => $request->payment_terms_txt,
                    'delivery_time' => $request->delivery_time,
                    'product_id' => $request->product_id,
                    'qty' => $request->qty,
                    'price' => $request->price,
                    'description' => $request->description,
                    'discount' => $request->discount,
                    'vat' => $request->vat,
                    'cost' => $request->cost,
                    'status' => 1,
                    'sort_id' => 999,
                    'created_by' => Auth::user()->id,
                ]
            );
            $ret = 1;

            if ($ret == 1) {
                $ret = 'OK';
                return json_encode(array('data' => $ret));
            } else {
                $ret = 'ERROR';
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function crmquotedeleteitemscart($id)
    {
        try {
            DB::table('sys_crm_quote_cart')->where('id', $id)->delete();
            Toastr::success('Item Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function savequote(Request $request)
    {
        $cart = SysCrmQuoteCart::where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id])->where('cust_id', $request->cust_id)->get();

        $quote_id = SysCrmQuoteItems::where('deal_id', $request->deal_id)->max('quote_id');

        if (count($cart) == 0) {
            Toastr::error('No Items Found.', 'Failed');
            return redirect()->back();
        }
        $i = 1;
        foreach ($cart as $items) {
            DB::table('sys_crm_quote_items')->insert([
                'user_id' => $items->user_id,
                'deal_id' => $items->deal_id,
                'company_id' => $items->company_id,
                'currency_id' => $items->currency_id,
                'customer_type' => $items->customer_type,
                'quote_validity' => $items->quote_validity,
                'payment_terms' => $items->payment_terms,
                'delivery_date' => $items->delivery_date,
                'payment_terms_txt' => $items->payment_terms_txt,
                'delivery_time' => $items->delivery_time,
                'product_id' => $items->product_id,
                'qty' => $items->qty,
                'price' => $items->price,
                'description' => $items->description,
                'discount' => $items->discount,
                'vat' => $items->vat,
                'cost' => $items->cost,
                'status' => $items->status,
                'sort_id' => $i++,
                'created_by' => Auth::user()->id,
                'quote_id' => $quote_id + 1,
            ]);

            DB::table('sys_crm_deals')->where('id', $items->deal_id)
                ->update(['estimated_close_date' => date('Y-m-d', strtotime($items->delivery_date)), 'quote_id' => $quote_id + 1, 'deal_discount' => $request->deal_discount, 'terms_and_condition' => $request->terms_and_condition]);
            SysHelper::deal_updated_at($request->deal_id);
        }


        //sys_crm_quote_charges
        for ($i = 0; $i < count($request->cfc_name); $i++) {
            if ($request->cfc_name[$i] != "" && $request->cfc_credit_account[$i] != "" && $request->cfc_amount[$i] != "") {
                $cfc = new SysCrmQuoteCharges();
                $cfc->deal_id = $request->deal_id;
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


        SysProformaInvoiceController::re_generate($request->deal_id, '', '');

        DB::table('sys_crm_quote_cart')->where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id])->delete();

        return redirect('crm-deals/' . $items->deal_id . '/view');

    }

    public function quoteedit(Request $request, $id, $qid)//kunal modified
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $companylist = SysCompany::select('id', 'company_name', 'city')->where('status', 1)->wherein('id', $company_id)->orderBy('company_name', 'ASC')->get();
            $currencylist = SysCurrencySettings::select('id', 'code', 'ex_rate')->where('status', 1)->orderBy('code', 'ASC')->get();
            $paymentterms = SysPaymentTerms::all();
            $items = SysHelper::get_product_list($company_id);

            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_sales($company_id);
            $supplier = SysHelper::get_supplier_list($company_id);

            $edit = SysCrmDeals::where('id', $id)->first();
            $edit_cfc = SysCrmQuoteCharges::where('deal_id', $id)->where('quote_id', $qid)->get();

            $currencylist2 = DB::table('sys_currency_rate as r')->select('r.id', 'r.from_currency', 'r.to_currency', 'c.code', 'r.rate')
                ->join('sys_currency as c', 'c.id', 'r.to_currency')
                ->where('r.status', 1)->where('r.from_currency', $edit->deal_currency)
                ->orderBy('c.code', 'ASC')->get();

            $basecompany_vat = SysVat::wherein('company_id', $company_id)->get();

            //$quotation = SysCrmDeals::where('id',$id)->first();
            $quotationitems = SysCrmQuoteItems::select('sys_crm_quote_items.*', 'sm_items.part_number')
                ->leftjoin('sm_items', 'sm_items.id', 'sys_crm_quote_items.product_id')
                ->where('deal_id', $id)->where('quote_id', $qid)->orderby('sort_id', 'asc')->get();

            if (count($quotationitems) > 0) {
                $deal_id = $quotationitems[0]->deal_id;
                $quote_id = $quotationitems[0]->quote_id;
                $company_id = $quotationitems[0]->company_id;
                $currency_id = $quotationitems[0]->currency_id;
                $customer_type = $quotationitems[0]->customer_type;
                $quote_validity = $quotationitems[0]->quote_validity;
                $payment_terms = $quotationitems[0]->payment_terms;
                $payment_terms_name = $quotationitems[0]->payment_terms_txt;
                $delivery_date = $quotationitems[0]->delivery_date;
                $delivery_time = $quotationitems[0]->delivery_time;
                $quote_id = $quotationitems[0]->quote_id;
            } else {
                $deal_id = "";
                $quote_id = "";
                $company_id = "";
                $currency_id = "";
                $customer_type = "";
                $quote_validity = "";
                $payment_terms = "";
                $payment_terms_name = "";
                $delivery_date = "";
                $delivery_time = "";
            }
            return view('backEnd.crm.Quote_New_Edit', compact('edit', 'companylist', 'currencylist', 'currencylist2', 'paymentterms', 'deal_id', 'quote_id', 'company_id', 'currency_id', 'customer_type', 'quote_validity', 'payment_terms', 'payment_terms_name', 'delivery_date', 'delivery_time', 'quotationitems', 'items', 'customs_freight_account', 'supplier', 'edit_cfc', 'basecompany_vat'));


        } catch (\Throwable $th) {
            return $th;

        }
    }

    function crmquoteupdate_discount(Request $request)
    {
        try {
            if ($request->discount_amount != "") {
                $total = 0;
                $qt = SysCrmQuoteItems::where('deal_id', $request->discount_amount_deal_id)->where('quote_id', $request->discount_amount_quote_id)->get();
                $discount_amount = $request->discount_amount;
                foreach ($qt as $t) {
                    $total += $t->qty * $t->price;
                }
                foreach ($qt as $t) {
                    $new_discount = (($t->qty * $t->price) / $total) * $discount_amount;
                    SysCrmQuoteItems::where('id', $t->id)->update(
                        [
                            'discount' => $new_discount,
                        ]
                    );
                }
            }
            Toastr::success('Discount Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function crmquoteadditems(Request $request)//kunal modified
    {
        try {
            $ret = 0;
            // $check = DB::table('sys_crm_quote_items')->where(
            //     [
            //         'user_id' => Auth::user()->id,
            //         'deal_id' => $request->deal_id,
            //         'company_id' => $request->company_id,
            //         'currency_id' => $request->currency_id,
            //         'customer_type' => $request->customer_type,
            //         'payment_terms' => $request->payment_terms,
            //         'delivery_date' => $request->delivery_date,
            //         'payment_terms_txt' => $request->payment_terms_txt,
            //         'delivery_time' => $request->delivery_time,
            //         'product_id' => $request->product_id,
            //         'qty' => $request->qty,
            //         'price' => $request->price,
            //         'description' => $request->description,
            //         'discount' => $request->discount,
            //         'vat' =>  $request->vat,
            //         'status' => 1,
            //         'sort_id' => $request->sort_id+1,
            //         'created_by' => Auth::user()->id,
            //         'quote_id' => $request->quote_id,
            //         'cost' => $request->cost,
            //     ]
            //     )->count();
            // if($check==0){
            DB::table('sys_crm_quote_items')->insert(
                [
                    'user_id' => Auth::user()->id,
                    'deal_id' => $request->deal_id,
                    'company_id' => $request->company_id,
                    'currency_id' => $request->currency_id,
                    'customer_type' => $request->customer_type,
                    'payment_terms' => $request->payment_terms,
                    'delivery_date' => $request->delivery_date,
                    'payment_terms_txt' => $request->payment_terms_txt,
                    'delivery_time' => $request->delivery_time,
                    'product_id' => $request->product_id,
                    'qty' => $request->qty,
                    'price' => $request->price,
                    'description' => $request->description,
                    'discount' => $request->discount,
                    'vat' => $request->vat,
                    'status' => 1,
                    'sort_id' => $request->sort_id + 1,
                    'created_by' => Auth::user()->id,
                    'quote_id' => $request->quote_id,
                    'cost' => $request->cost,
                ]
            );
            // }
            $ret = 1;

            if ($ret == 1) {
                $ret = 'OK';
                return json_encode(array('data' => $ret));
            } else {
                $ret = 'ERROR';
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    function crmquoteupdate(Request $request)
    {
        try {

            if ($request->submit == "CP") {
                if ($request->currency_id != $request->currency_old) {

                    $old_currancy = SysCurrency::where('id', $request->currency_old)->first();
                    $new_currancy = SysCurrency::where('id', $request->currency_id)->first();

                    $qt = SysCrmQuoteItems::where('quote_id', $request->quote_id)->where('deal_id', $request->deal_id)->get();
                    foreach ($qt as $t) {
                        $old_price = $t->price / $old_currancy->ex_rate;
                        $new_price = $old_price * $new_currancy->ex_rate;
                        DB::table('sys_crm_quote_items')->where('id', $t->id)->update(
                            [
                                'price' => $new_price,
                            ]
                        );
                    }
                }
            }

            DB::table('sys_crm_quote_items')->where('quote_id', $request->quote_id)->where('deal_id', $request->deal_id)->update(
                [
                    'company_id' => $request->company_id,
                    'currency_id' => $request->currency_id,
                    'customer_type' => $request->customer_type,
                    'payment_terms' => $request->payment_terms,
                    'delivery_date' => $request->delivery_date,
                    'quote_validity' => $request->quote_validity,
                    'payment_terms_txt' => $request->payment_terms_txt,
                    'delivery_time' => $request->delivery_time,
                    'updated_by' => Auth::user()->id,
                    'quote_id' => $request->quote_id,
                ]
            );
            DB::table('sys_crm_deal_track')->where('deal_id', $request->deal_id)->update(['payment_terms' => $request->payment_terms,]);

            if (count($request->item_id) > 0) {
                for ($i = 0; $i < count($request->item_id); $i++) {
                    SysCrmQuoteItems::where('id', $request->item_id[$i])->update(['sort_id' => $request->sort_id[$i]]);
                }
            }

            DB::table('sys_crm_deals')->where('id', $request->deal_id)
                ->update(['estimated_close_date' => date('Y-m-d', strtotime($request->delivery_date)), 'quote_id' => $request->quote_id, 'deal_currency' => $request->currency_id, 'deal_discount' => $request->deal_discount, 'terms_and_condition' => $request->terms_and_condition]);
            SysHelper::deal_updated_at($request->deal_id);

            DB::table('sys_crm_quote_charges')->where('deal_id', $request->deal_id)->where('quote_id', $request->quote_id)->delete();
            //sys_crm_quote_charges
            for ($i = 0; $i < count($request->cfc_name); $i++) {
                if ($request->cfc_name[$i] != "" && $request->cfc_credit_account[$i] != "" && $request->cfc_amount[$i] != "") {
                    $cfc = new SysCrmQuoteCharges();
                    $cfc->deal_id = $request->deal_id;
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

            SysProformaInvoiceController::re_generate($request->deal_id, '', '');

            Toastr::success('Updated Successfully', 'Success');
            return redirect('crm-deals/' . $request->deal_id . '/view');
            //return redirect()->back(); 

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    function crm_update_quote_sort_order(Request $request)
    {
        try {
            if (count($request->item_id) > 0) {
                for ($i = 0; $i < count($request->item_id); $i++) {
                    SysCrmQuoteItems::where('id', $request->item_id[$i])->update(['sort_id' => $request->sort_id[$i]]);
                }
            }
            Toastr::success('Updated Successfully', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    function crmquoteupdate_currency(Request $request)
    {
        try {
            if ($request->to_currency_id != $request->from_currency_id) {

                //$old_currancy = SysCurrency::where('id',$request->from_currency_id)->first();

                $to_currency = SysCurrencyRate::where('id', $request->to_currency_id)->value('to_currency');
                $qt = SysCrmQuoteItems::where('quote_id', $request->cur_quote_id)->where('deal_id', $request->cur_deal_id)->get();
                foreach ($qt as $t) {
                    //$old_price = $t->price / $old_currancy->ex_rate;
                    $new_price = $t->price * $request->to_currency_rate;

                    $new_discount = $t->discount * $request->to_currency_rate;
                    //$old_cost = $t->cost / $old_currancy->ex_rate;
                    $new_cost = $t->cost * $request->to_currency_rate;
                    // dd( $new_price, $t->price, $request->to_currency_rate);


                    DB::table('sys_crm_quote_items')->where('id', $t->id)->update(
                        [
                            'currency_id' => $to_currency,
                            'price' => $new_price,
                            'cost' => $new_cost,
                            'discount' => $new_discount,
                        ]
                    );
                }
                $deal_discount = DB::table('sys_crm_deals')->where('id', $request->cur_deal_id)->value('deal_discount');
                $new_deal_discount = $deal_discount * $request->to_currency_rate;

                DB::table('sys_crm_deals')->where('id', $request->cur_deal_id)
                    ->update(['deal_currency' => $to_currency, 'deal_discount' => $new_deal_discount]);
            }

            Toastr::success('Updated Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function crmquoteupdateitems(Request $request)
    {
        try {
            DB::table('sys_crm_quote_items')->where('id', $request->itm_id)->update(
                [
                    'company_id' => $request->company_id,
                    'currency_id' => $request->currency_id,
                    'customer_type' => $request->customer_type,
                    'payment_terms' => $request->payment_terms,
                    'delivery_date' => $request->delivery_date,
                    'payment_terms_txt' => $request->payment_terms_txt,
                    'delivery_time' => $request->delivery_time,
                    'product_id' => $request->product_id,
                    'qty' => $request->qty,
                    'price' => $request->price,
                    'description' => $request->description,
                    'discount' => $request->discount,
                    'vat' => $request->vat,
                    'updated_by' => Auth::user()->id,
                    'quote_id' => $request->quote_id,
                    'cost' => $request->cost,
                ]
            );

            DB::table('sys_crm_deals')->where('id', $request->deal_id)
                ->update(['estimated_close_date' => date('Y-m-d', strtotime($request->delivery_date)), 'quote_id' => $request->quote_id]);
            SysHelper::deal_updated_at($request->deal_id);

            $ret = 1;

            if ($ret == 1) {
                $ret = 'OK';
                return json_encode(array('data' => $ret));
            } else {
                $ret = 'ERROR';
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = $e;//'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function crmquotedelete($id)
    {
        try {
            DB::table('sys_crm_quote_items')->where('id', $id)->delete();
            Toastr::success('Item Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function quote_upload_excel_cart(Request $request)
    {

        try {
            
            //return $request->all();
            DB::beginTransaction();
            $selected_file = "";
            if (!isset($request->excel_part_no) || count($request->excel_part_no) == 0) {
                Toastr::error('No Data found in excel', 'Failed');
                return redirect()->back();
            }
            // if ($request->file('import_file') != "") {
            //     $file = $request->file('import_file');
            //     $selected_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            //     $file->move('public/uploads/product_upload/', $selected_file);
            //     $selected_file = 'public/uploads/product_upload/' . $selected_file;
            //     //return  $selected_file;
            // }

            // $objPHPExcel = PHPExcel_IOFactory::load($selected_file);
            // $objWorksheet = $objPHPExcel->getActiveSheet();
            // $highestRow = $objWorksheet->getHighestRow();

            // $dataArray = $objPHPExcel->getActiveSheet()->toArray();


            if (count($request->excel_part_no) > 0) {
                for ($i = 0; $i < count($request->excel_part_no); $i++) {
                    if ($request->excel_part_no[$i] != "") {
                        $pid = SmItem::where('part_number', $request->excel_part_no[$i])->where('status', 1)->max('id');
                        if ($pid != "") {
                            $description = $request->excel_description[$i];
                            if ($description == false) { //check null value
                                $description = SmItem::where('part_number', $request->excel_part_no[$i])->where('status', 1)->max('description');
                            }

                            $data[] = [
                                'cart_id' => session('logged_session_data.cart_id'),
                                'user_id' => Auth::user()->id,
                                'partnumber' => $request->excel_part_no[$i],
                                'deal_id' => $request->excel_deal_id,
                                'cust_id' => $request->excel_cust_id,
                                'company_id' => $request->excel_company_id,
                                'currency_id' => $request->excel_currency_id,
                                'customer_type' => $request->excel_customer_type,
                                'quote_validity' => $request->excel_quote_validity,
                                'payment_terms' => $request->excel_payment_terms,
                                'delivery_date' => $request->excel_delivery_date,
                                'payment_terms_txt' => $request->excel_payment_terms_txt,
                                'delivery_time' => $request->excel_delivery_time,
                                'product_id' => $pid,
                                'cost' => $request->excel_cost[$i],
                                'qty' => $request->excel_qty[$i],
                                'price' => $request->excel_unit_price[$i],
                                'description' => $description,
                                'discount' => $request->excel_discount[$i],
                                'vat' => $request->vat_excel[$i],
                                'status' => 1,
                                'created_by' => Auth::user()->id,
                                'sort_id' => $i,
                            ];
                        } else {
                            DB::rollBack();
                            Toastr::error('Item not found in System ' . $request->excel_part_no[$i], 'Failed');
                            return redirect()->back();
                        }
                    }
                }
            }
            if (count($data) > 0) {
                DB::table('sys_crm_quote_cart')->insert($data);
            }
            DB::commit();
            Toastr::success('Item Imported Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function quote_upload_excel(Request $request)
    {
        try {

        } catch (\Exception $e) {

        }
    }







    public function download(Request $request, $id)
    {
        try {
            $quotation = SysCrmDeals::where('id', $id)->first();
            $quotationitems = SysCrmQuoteCSItems::where('deal_id', $id)->orderby('id', 'ASC')->get();

            $currency = $quotationitems[0]->currency->code;
            $paymentterms = $quotationitems[0]->paymentterms->title;
            $deliverydate = $quotationitems[0]->delivery_date;

            $pdfheader = $quotationitems[0]->company->pdf_header;
            $pdffooter = $quotationitems[0]->company->pdf_footer;
            $pdfwatermark = $quotationitems[0]->company->pdf_watermark;
            $pdffirstpage = $quotationitems[0]->company->pdf_first_page;
            $net_vat = $quotationitems[0]->company->net_vat;
            if ($net_vat == "") {
                $net_vat = 5;
            }

            $data = [
                'quotation' => $quotation,
                'quotationitems' => $quotationitems,
                'currency' => $currency,
            ];
            //return $data;
            $wp = $request->wp;

            $pdf = PDF::loadView('backEnd.crm.QuoteCSPDF', ['quotation' => $quotation, 'quotationitems' => $quotationitems, 'currency' => $currency, 'paymentterms' => $paymentterms, 'deliverydate' => $deliverydate, 'wp' => $wp, 'pdfheader' => $pdfheader, 'pdffooter' => $pdffooter, 'pdfwatermark' => $pdfwatermark, 'pdffirstpage' => $pdffirstpage, 'net_vat' => $net_vat]);
            $pdf->setPaper('A4', 'portrait');
            $pageName = "Quote-No-" . $id . ".pdf";
            return $pdf->download($pageName);

            //return view('admin.boq.form_pdf',['boq' => $boq, 'items'=>$items]);

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function chooseitems(Request $request)
    {
        try {

            $brands = SysBrand::all();
            $itemCategories = SmItemCategory::all();
            $SuCategories = SmItemSubcategory::all();

            $cart_items = SysCrmQuoteCart::select('sys_crm_quote_cart.id', 'sys_crm_quote_cart.qty', 'sys_crm_quote_cart.price', 'sm_items.part_number', 'sys_crm_quote_cart.description')
                ->join('sm_items', 'sm_items.id', 'sys_crm_quote_cart.product_id')
                ->where(['cart_id' => session('logged_session_data.cart_id')])->get();

            $currancy = DB::table('sys_currency')->select('code')->where('id', session('form_session_data.currency_id'))->first();

            $product = [];
            if ($_POST) {
                try {
                    if (session('form_session_data.customer_type') == 1) {

                        $product = SmItem::select('sm_items.id', 'sm_items.part_number', 'sm_items.description', 'sys_price_book.r_price as price')
                            ->join('sys_price_book', 'sys_price_book.pid', 'sm_items.id')
                            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->where(function ($query) use ($request) {
                                $query->where('sm_items.part_number', 'like', '%' . $request->part_number . '%')
                                    ->orwhere('sm_items.description', 'like', '%' . $request->part_number . '%');
                            })->get();

                        // $product = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
                        // ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                        // ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))
                        // ->where('sm_items.part_number','like','%'.$request->part_number.'%')
                        // ->orwhere('sm_items.description','like','%'.$request->part_number.'%')->get();
                    } else {
                        $product = SmItem::select('sm_items.id', 'sm_items.part_number', 'sm_items.description', 'sys_price_book.e_price as price')
                            ->join('sys_price_book', 'sys_price_book.pid', 'sm_items.id')
                            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->where(function ($query) use ($request) {
                                $query->where('sm_items.part_number', 'like', '%' . $request->part_number . '%')
                                    ->orwhere('sm_items.description', 'like', '%' . $request->part_number . '%');
                            })->get();

                        // $product = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
                        // ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                        // ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))
                        // ->where('sm_items.part_number','like','%'.$request->part_number.'%')
                        // ->orwhere('sm_items.description','like','%'.$request->part_number.'%')->get();
                    }
                } catch (\Throwable $th) {
                    return $th;
                }
            }
            return view('backEnd.crm.QuoteNew', compact('product', 'cart_items', 'currancy', 'brands', 'itemCategories', 'SuCategories'));
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function additems(Request $request)
    {
        try {
            $cart = SysCrmQuoteCart::select('id', 'qty')
                ->where(['cart_id' => session('logged_session_data.cart_id'), 'product_id' => $request->id])->first();

            if (isset($cart)) {
                DB::table('sys_crm_quote_cart')->where('id', $cart->id)
                    ->update([
                        'qty' => $cart->qty + $request->qty,
                        'price' => $request->price,
                        'description' => $request->description,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            } else {
                DB::table('sys_crm_quote_cart')->insert(
                    [
                        'cart_id' => session('logged_session_data.cart_id'),
                        'user_id' => Auth::user()->id,
                        'deal_id' => session('form_session_data.deal_id'),
                        'company_id' => session('form_session_data.company_id'),
                        'currency_id' => session('form_session_data.currency_id'),
                        'customer_type' => session('form_session_data.customer_type'),
                        'payment_terms' => session('form_session_data.payment_terms'),
                        'delivery_date' => session('form_session_data.delivery_date'),
                        'product_id' => $request->id,
                        'qty' => $request->qty,
                        'price' => $request->price,
                        'description' => $request->description,
                        'discount' => 0,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                    ]
                );
            }
            $bug = 0;
        } catch (\Throwable $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            $retData = "OK";
            return json_encode(array('data' => $retData));
        } else {
            $retData = 'ERROR';
            return json_encode(array('data' => $retData));
        }
    }
    public function addbulkitems(Request $request)
    {
        $add_check = $request['add'];
        if (array_sum($add_check) == 0) {
            Toastr::error('Product Added Failed, Please Select Products', 'Failed');
            return redirect()->back();
        }
        foreach ($add_check as $dat) {
            if ($dat === '1') {
                if (end($add_id) == 0) {
                    array_pop($add_id);
                }
                $add_id[] = 1;
            } else {
                $add_id[] = 0;
            }
        }

        try {
            for ($i = 0; $i < count($add_id); $i++) {
                if ($add_id[$i] == 1) {
                    $qty = $request->b_qty[$i];

                    if ($request->b_qty[$i] == "" || $request->b_qty[$i] == 0) {
                        $qty = 1;
                    }
                    $cart = SysCrmQuoteCart::select('id', 'qty')
                        ->where(['cart_id' => session('logged_session_data.cart_id'), 'product_id' => $request->pid[$i]])->first();

                    if (isset($cart)) {
                        DB::table('sys_crm_quote_cart')->where('id', $cart->id)
                            ->update([
                                'qty' => $cart->qty + $qty,
                                'price' => $request->b_price[$i],
                                'description' => $request->b_description[$i],
                                'updated_by' => Auth::user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    } else {
                        DB::table('sys_crm_quote_cart')->insert(
                            [
                                'cart_id' => session('logged_session_data.cart_id'),
                                'user_id' => Auth::user()->id,
                                'deal_id' => session('form_session_data.deal_id'),
                                'company_id' => session('form_session_data.company_id'),
                                'currency_id' => session('form_session_data.currency_id'),
                                'customer_type' => session('form_session_data.customer_type'),
                                'payment_terms' => session('form_session_data.payment_terms'),
                                'delivery_date' => session('form_session_data.delivery_date'),
                                'product_id' => $request->pid[$i],
                                'qty' => $qty,
                                'price' => $request->b_price[$i],
                                'description' => $request->b_description[$i],
                                'discount' => 0,
                                'status' => 1,
                                'created_by' => Auth::user()->id,
                            ]
                        );
                    }

                }
            }

            $bug = 0;
        } catch (\Throwable $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            Toastr::success('Product Added Successfully', 'Success');
            return redirect()->back();
        } else {
            Toastr::error('Product Added Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function generatequote(Request $request)
    {
        $cart = SysCrmQuoteCart::where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id])->get();
        foreach ($cart as $items) {
            DB::table('sys_crm_quote_items')->insert([
                'user_id' => $items->user_id,
                'deal_id' => $items->deal_id,
                'company_id' => $items->company_id,
                'currency_id' => $items->currency_id,
                'customer_type' => $items->customer_type,
                'payment_terms' => $items->payment_terms,
                'delivery_date' => $items->delivery_date,
                'product_id' => $items->product_id,
                'qty' => $items->qty,
                'price' => $items->price,
                'description' => $items->description,
                'discount' => $items->discount,
                'status' => $items->status,
                'created_by' => Auth::user()->id,
            ]);
        }

        DB::table('sys_crm_quote_cart')->where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id])->delete();

        return redirect('crm-deals/' . $items->deal_id . '/view');

    }


    public function quoteaddnew(Request $request, $id)
    {
        try {
            $quotation = SysCrmDeals::where('id', $id)->first();
            $quotationitems = SysCrmQuoteItems::where('deal_id', $id)->orderby('id', 'ASC')->get();

            $deal_id = $quotationitems[0]->deal_id;
            $company_id = $quotationitems[0]->company_id;
            $currency_id = $quotationitems[0]->currency_id;
            $customer_type = $quotationitems[0]->customer_type;
            $currency_code = $quotationitems[0]->currency->code;
            $payment_terms = $quotationitems[0]->payment_terms;
            $payment_terms_name = $quotationitems[0]->paymentterms->title;
            $delivery_date = $quotationitems[0]->delivery_date;

            $brands = SysBrand::all();
            $itemCategories = SmItemCategory::all();
            $SuCategories = SmItemSubcategory::all();

            $product = [];
            if ($_POST) {
                try {
                    if ($customer_type == 1) {
                        $product = SmItem::select('sm_items.id', 'sm_items.part_number', 'sm_items.description', 'sys_price_book.r_price as price')
                            ->join('sys_price_book', 'sys_price_book.pid', 'sm_items.id')
                            ->where('sys_price_book.currency_id', $currency_id)->where(function ($query) use ($request) {
                                $query->where('sm_items.part_number', 'like', '%' . $request->part_number . '%')
                                    ->orwhere('sm_items.description', 'like', '%' . $request->part_number . '%');
                            })->get();

                        // $product = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
                        // ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                        // ->where('sys_price_book.currency_id', $currency_id)
                        // ->where('sm_items.part_number','like','%'.$request->part_number.'%')
                        // ->orwhere('sm_items.description','like','%'.$request->part_number.'%')->get();
                    } else {
                        $product = SmItem::select('sm_items.id', 'sm_items.part_number', 'sm_items.description', 'sys_price_book.e_price as price')
                            ->join('sys_price_book', 'sys_price_book.pid', 'sm_items.id')
                            ->where('sys_price_book.currency_id', $currency_id)->where(function ($query) use ($request) {
                                $query->where('sm_items.part_number', 'like', '%' . $request->part_number . '%')
                                    ->orwhere('sm_items.description', 'like', '%' . $request->part_number . '%');
                            })->get();

                        // $product = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
                        // ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                        // ->where('sys_price_book.currency_id', $currency_id)
                        // ->where('sm_items.part_number','like','%'.$request->part_number.'%')
                        // ->orwhere('sm_items.description','like','%'.$request->part_number.'%')->get();
                    }
                } catch (\Throwable $th) {
                    return $th;
                }
            }

            return view('backEnd.crm.QuoteEditNew', compact('quotation', 'quotationitems', 'deal_id', 'company_id', 'currency_id', 'customer_type', 'currency_code', 'product', 'payment_terms', 'delivery_date', 'payment_terms_name', 'brands', 'itemCategories', 'SuCategories'));

        } catch (\Throwable $th) {
            return redirect('crm-deals/' . $id . '/view');
        }
    }

    public function additemsedit(Request $request)
    {
        try {
            $quote = SysCrmQuoteItems::select('id', 'qty')
                ->where(['deal_id' => $request->deal_id, 'product_id' => $request->id])->first();

            if (isset($quote)) {
                DB::table('sys_crm_quote_items')->where('id', $quote->id)
                    ->update([
                        'qty' => $quote->qty + $request->qty,
                        'price' => $request->price,
                        'description' => $request->description,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            } else {
                DB::table('sys_crm_quote_items')->insert(
                    [
                        'user_id' => Auth::user()->id,
                        'deal_id' => $request->deal_id,
                        'company_id' => $request->company_id,
                        'currency_id' => $request->currency_id,
                        'customer_type' => $request->customer_type,
                        'payment_terms' => $request->payment_terms,
                        'delivery_date' => $request->delivery_date,
                        'product_id' => $request->id,
                        'qty' => $request->qty,
                        'price' => $request->price,
                        'description' => $request->description,
                        'discount' => 0,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                    ]
                );
            }
            $bug = 0;
        } catch (\Throwable $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            $retData = "OK";
            return json_encode(array('data' => $retData));
        } else {
            $retData = 'ERROR';
            return json_encode(array('data' => $retData));
        }
    }
    public function addbulkitemsedit(Request $request)
    {
        $add_check = $request['add'];
        if (array_sum($add_check) == 0) {
            Toastr::error('Product Added Failed, Please Select Products', 'Failed');
            return redirect()->back();
        }
        foreach ($add_check as $dat) {
            if ($dat === '1') {
                if (end($add_id) == 0) {
                    array_pop($add_id);
                }
                $add_id[] = 1;
            } else {
                $add_id[] = 0;
            }
        }

        try {
            for ($i = 0; $i < count($add_id); $i++) {
                if ($add_id[$i] == 1) {
                    $qty = $request->b_qty[$i];

                    if ($request->b_qty[$i] == "" || $request->b_qty[$i] == 0) {
                        $qty = 1;
                    }
                    $quote = SysCrmQuoteItems::select('id', 'qty')
                        ->where(['deal_id' => $request->b_deal_id, 'product_id' => $request->pid[$i]])->first();
                    if (isset($quote)) {
                        DB::table('sys_crm_quote_items')->where('id', $quote->id)
                            ->update([
                                'qty' => $quote->qty + $qty,
                                'price' => $request->b_price[$i],
                                'description' => $request->b_description[$i],
                                'updated_by' => Auth::user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    } else {
                        DB::table('sys_crm_quote_items')->insert(
                            [
                                'user_id' => Auth::user()->id,
                                'deal_id' => $request->b_deal_id,
                                'company_id' => $request->b_company_id,
                                'currency_id' => $request->b_currency_id,
                                'customer_type' => $request->b_customer_type,
                                'payment_terms' => $request->b_payment_terms,
                                'delivery_date' => $request->b_delivery_date,
                                'product_id' => $request->pid[$i],
                                'qty' => $qty,
                                'price' => $request->b_price[$i],
                                'description' => $request->b_description[$i],
                                'discount' => 0,
                                'status' => 1,
                                'created_by' => Auth::user()->id,
                            ]
                        );
                    }
                }
            }
            $bug = 0;
        } catch (\Throwable $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            Toastr::success('Product Added Successfully', 'Success');
            return redirect()->back();
        } else {
            Toastr::error('Product Added Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function addnewbulkitemsedit(Request $request)
    {
        $add_check = $request['add'];
        if (array_sum($add_check) == 0) {
            Toastr::error('Product Added Failed, Please Select Products', 'Failed');
            return redirect()->back();
        }
        foreach ($add_check as $dat) {
            if ($dat === '1') {
                if (end($add_id) == 0) {
                    array_pop($add_id);
                }
                $add_id[] = 1;
            } else {
                $add_id[] = 0;
            }
        }

        try {
            for ($i = 0; $i < count($add_id); $i++) {
                if ($add_id[$i] == 1) {
                    $qty = $request->b_qty[$i];

                    if ($request->b_qty[$i] == "" || $request->b_qty[$i] == 0) {
                        $qty = 1;
                    }
                    $quote = SysCrmQuoteItems::select('id', 'qty')
                        ->where(['deal_id' => $request->b_deal_id, 'product_id' => $request->pid[$i]])->first();
                    if (isset($quote)) {
                        DB::table('sys_crm_quote_items')->where('id', $quote->id)
                            ->update([
                                'qty' => $quote->qty + $qty,
                                'price' => $request->b_price[$i],
                                'description' => $request->b_description[$i],
                                'updated_by' => Auth::user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    } else {
                        DB::table('sys_crm_quote_items')->insert(
                            [
                                'user_id' => Auth::user()->id,
                                'deal_id' => $request->b_deal_id,
                                'company_id' => $request->b_company_id,
                                'currency_id' => $request->b_currency_id,
                                'customer_type' => $request->b_customer_type,
                                'payment_terms' => $request->b_payment_terms,
                                'delivery_date' => $request->b_delivery_date,
                                'product_id' => $request->pid[$i],
                                'qty' => $qty,
                                'price' => $request->b_price[$i],
                                'description' => $request->b_description[$i],
                                'discount' => 0,
                                'status' => 1,
                                'created_by' => Auth::user()->id,
                            ]
                        );
                    }
                }
            }
            $bug = 0;
        } catch (\Throwable $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            Toastr::success('Product Added Successfully', 'Success');
            return redirect()->back();
        } else {
            Toastr::error('Product Added Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function upditemsedit(Request $request)
    {
        try {
            $quote = SysCrmQuoteItems::select('id', 'qty')
                ->where(['deal_id' => $request->deal_id, 'id' => $request->id])->first();
            if (isset($quote)) {
                DB::table('sys_crm_quote_items')->where('id', $quote->id)
                    ->update([
                        'qty' => $request->qty,
                        'price' => $request->price,
                        'description' => $request->description,
                        'discount' => $request->discount,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }
            $bug = 0;
        } catch (\Throwable $e) {
            return json_encode(array('data' => $e));
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            $retData = "OK";
            return json_encode(array('data' => $retData));
        } else {
            $retData = 'ERROR';
            return json_encode(array('data' => $retData));
        }
    }
    public function deleteitemsedit(Request $request)
    {
        $input = $request->all();

        try {
            DB::table('sys_crm_quote_items')->where('id', $request->id)->delete();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            $retData = "OK";
            return json_encode(array('data' => $retData));
        } else {
            $retData = 'ERROR';
            return json_encode(array('data' => $retData));
        }
    }

    public function addnewproduct(Request $request)
    {
        $check_part_number = SmItem::select('id')->where('part_number', $request->part_number)->get();
        if (count($check_part_number) > 0) {
            Toastr::error('Part Number Already Existing, please check try again', 'Failed');
            return redirect()->back();
            //return redirect()->back()->with('message-danger', 'Part Number Already Existing, please check try again');
        }
        try {
            $brand = $request->brand;
            $category_name = $request->category_name;
            $subcategory_name = $request->subcategory_name;
            if ($request->brand == "") {
                $brand = 13;
            } //Other Brand
            if ($request->category_name == "") {
                $category_name = 14;
            } //Other Category
            if ($request->subcategory_name == "") {
                $subcategory_name = 78;
            } //Other Sub Category

            $items = new SmItem();
            //$items->item_name = $request->item_name;
            $items->item_code = $request->part_number;
            $items->part_number = $request->part_number;
            $items->brand = $brand;
            $items->product_type = 1;
            $items->category_name = $category_name;
            $items->subcategory_name = $subcategory_name;
            $items->description = $request->description;
            $items->vat = "5";
            $items->uom = "5";
            $items->coo = "5";
            $items->hscode = "5";
            $items->weight = "0.00";
            $items->status = 1;
            $items->created_by = Auth::user()->id;
            $results = $items->save();

            SysPriceBook::insert([
                'pid' => $items->id,
                'currency_id' => 1,
                'r_price' => '0.00',
                'e_price' => '0.00',
                'status' => 1,
                'created_by' => Auth::user()->id,
                'company_id' => session('logged_session_data.company_id')
            ]);
            SysPriceBook::insert([
                'pid' => $items->id,
                'currency_id' => 2,
                'r_price' => '0.00',
                'e_price' => '0.00',
                'status' => 1,
                'created_by' => Auth::user()->id,
                'company_id' => session('logged_session_data.company_id')
            ]);
            SysPriceBook::insert([
                'pid' => $items->id,
                'currency_id' => 3,
                'r_price' => '0.00',
                'e_price' => '0.00',
                'status' => 1,
                'created_by' => Auth::user()->id,
                'company_id' => session('logged_session_data.company_id')
            ]);
            SysPriceBook::insert([
                'pid' => $items->id,
                'currency_id' => 4,
                'r_price' => '0.00',
                'e_price' => '0.00',
                'status' => 1,
                'created_by' => Auth::user()->id,
                'company_id' => session('logged_session_data.company_id')
            ]);
            SysPriceBook::insert([
                'pid' => $items->id,
                'currency_id' => 5,
                'r_price' => '0.00',
                'e_price' => '0.00',
                'status' => 1,
                'created_by' => Auth::user()->id,
                'company_id' => session('logged_session_data.company_id')
            ]);

            Toastr::success('Product Added Successfully', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Product Added Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function store(Request $request)
    {
        $tags = "";
        if ($request->tags != "") {
            $tags = implode(",", $request->tags);
        }
        $doc_file = "";
        if ($request->file('doc') != "") {
            $file = $request->file('doc');
            $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
            $file->move('public/uploads/crm_lead_doc/', $doc_file);
            $doc_file = $doc_file;
        }

        DB::beginTransaction();
        try {
            $scd = new SysCrmDeals();
            $scd->date = date('Y-m-d', strtotime($request->date));
            $scd->deal_name = $request->deal_name;
            $scd->cust_id = $request->cust_id;
            $scd->cust_name = $request->cust_name;
            $scd->cust_no = $request->cust_no;
            $scd->cust_email = $request->cust_email;
            $scd->deal_value = $request->deal_value;
            $scd->source = $request->source;
            $scd->source_o = $request->source_o;
            $scd->tags = $tags;
            $scd->stage = $request->stage;
            $scd->owner = $request->owner;
            $scd->doc = $doc_file;
            $scd->estimated_close_date = date('Y-m-d', strtotime($request->estimated_close_date));
            $scd->created_by = Auth::user()->id;
            $scd->company_id = session('logged_session_data.company_id');
            $scd->save();
            $scd->toArray();

            $results = 0;
            DB::commit();

            if ($results == 0) {
                Toastr::success('Deal has been added successfully', 'Success');
                return redirect()->back();
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

    public function show()
    {
        try {
            $deals = SysCrmDeals::all();

            return view('backEnd.crm.DealList', compact('deals'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            //return redirect()->back();
            return $e;
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $staff = SmStaff::select('user_id', 'full_name')->get();
            $vendors = SysCustSuppl::select('id', 'code', 'name')->where('catid', 1)->get(); // 1 customers, 2 suppliers
            $deals = SysCrmDeals::all();
            $brand = SysBrand::all();
            $edit = SysCrmDeals::where('id', $id)->first();
            $country = SysCountries::select('id', 'name')->get();
            return view('backEnd.crm.DealForm', compact('currency', 'vendors', 'company', 'staff', 'edit', 'deals', 'brand', 'country'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function view($id)
    {
        try {
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $staff = SmStaff::select('user_id', 'full_name')->get();
            $vendors = SysCustSuppl::select('id', 'code', 'name')->where('catid', 1)->get(); // 1 customers, 2 suppliers
            $leads = SysCrmDeals::where('id', $id)->first();
            $edit = SysCrmDeals::where('id', $id)->first();
            $comments = SysCrmDealsComments::where('deal_id', $id)->orderBy('id', 'DESC')->get();

            return view('backEnd.crm.DealView', compact('currency', 'vendors', 'company', 'staff', 'edit', 'leads', 'comments'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function update(Request $request, $id)
    {
        $tags = "";
        if ($request->tags != "") {
            $tags = implode(",", $request->tags);
        }
        $doc_file = $request->file_name;
        if ($request->file('doc') != "") {
            $file = $request->file('doc');
            $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
            $file->move('public/uploads/crm_lead_doc/', $doc_file);
            $doc_file = $doc_file;
        }

        $scd = SysCrmDeals::find($id);
        $scd->date = date('Y-m-d', strtotime($request->date));
        $scd->deal_name = $request->deal_name;
        $scd->cust_id = $request->cust_id;
        $scd->cust_name = $request->cust_name;
        $scd->cust_no = $request->cust_no;
        $scd->cust_email = $request->cust_email;
        $scd->deal_value = $request->deal_value;
        $scd->source = $request->source;
        $scd->source_o = $request->source_o;
        $scd->tags = $tags;
        $scd->stage = $request->stage;
        $scd->owner = $request->owner;
        $scd->doc = $doc_file;
        $scd->estimated_close_date = date('Y-m-d', strtotime($request->estimated_close_date));
        $scd->updated_by = Auth::user()->id;
        $scd->company_id = session('logged_session_data.company_id');
        $results = $scd->update();

        if ($results) {
            Toastr::success('Deal has been updated successfully', 'Success');
            return redirect()->back();
        } else {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function crmdealscommentsadd(Request $request)
    {
        try {
            DB::table('sys_crm_deals_comments')->insert(
                [
                    'deal_id' => $request->commentsid,
                    'comments' => $request->comments,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                ]
            );
            Toastr::success('Comments has been added successfully', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function updatepaymentterms(Request $request)
    {
        try {
            DB::table('sys_crm_quote_items')->where('deal_id', $request->edit_deal_id)->update(
                [
                    'payment_terms' => $request->payment_terms,
                    'delivery_date' => date('Y-m-d', strtotime($request->delivery_date)),
                    'updated_by' => Auth::user()->id,
                ]
            );
            Toastr::success('Updated successfully', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        //
    }

    public function deleteStoreView(Request $request, $id)
    {

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($id, null);
        }
        return view('backEnd.inventory.deleteItemStoreView', compact('id'));
    }

    public function deleteStore(Request $request, $id)
    {
        $result = SmItemStore::destroy($id);

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Store  has been deleted successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($result) {
                return redirect('item-store')->with('message-success-delete', 'Store  has been deleted successfully');
            } else {
                return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            }
        }
    }

    public function update_excel_in_quoteedit(Request $request)//kunal added new
    {
        // dd($request->all());

        try {
            //return $request->all();
            DB::beginTransaction();
            $selected_file = "";
            if (!isset($request->excel_part_no) || count($request->excel_part_no) == 0) {
                Toastr::error('No Data found in excel', 'Failed');
                return redirect()->back();
            }
            // if ($request->file('import_file') != "") {
            //     $file = $request->file('import_file');
            //     $selected_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            //     $file->move('public/uploads/product_upload/', $selected_file);
            //     $selected_file = 'public/uploads/product_upload/' . $selected_file;
            //     //return  $selected_file;
            // }

            // $objPHPExcel = PHPExcel_IOFactory::load($selected_file);
            // $objWorksheet = $objPHPExcel->getActiveSheet();
            // $highestRow = $objWorksheet->getHighestRow();

            // $dataArray = $objPHPExcel->getActiveSheet()->toArray();


            if (count($request->excel_part_no) > 0) {
                for ($i = 0; $i < count($request->excel_part_no); $i++) {
                    if ($request->excel_part_no[$i] != "") {
                        $pid = SmItem::where('part_number', $request->excel_part_no[$i])->where('status', 1)->max('id');
                        if ($pid != "") {
                            $description = $request->excel_description[$i];
                            if ($description == false) { //check null value
                                $description = SmItem::where('part_number', $request->excel_part_no[$i])->where('status', 1)->max('description');
                            }

                            $data[] = [
                                'cart_id' => session('logged_session_data.cart_id'),
                                'user_id' => Auth::user()->id,
                                'partnumber' => $request->excel_part_no[$i],
                                'deal_id' => $request->excel_deal_id,
                                'cust_id' => $request->excel_cust_id,
                                'company_id' => $request->excel_company_id,
                                'currency_id' => $request->excel_currency_id,
                                'customer_type' => $request->excel_customer_type,
                                'quote_validity' => $request->excel_quote_validity,
                                'payment_terms' => $request->excel_payment_terms,
                                'delivery_date' => $request->excel_delivery_date,
                                'payment_terms_txt' => $request->excel_payment_terms_txt,
                                'delivery_time' => $request->excel_delivery_time,
                                'product_id' => $pid,
                                'cost' => $request->excel_cost[$i],
                                'qty' => $request->excel_qty[$i],
                                'price' => $request->excel_unit_price[$i],
                                'description' => $description,
                                'discount' => $request->excel_discount[$i],
                                'vat' => $request->vat_excel[$i],
                                'status' => 1,
                                'created_by' => Auth::user()->id,
                                'sort_id' => $i,
                            ];
                        } else {
                            DB::rollBack();
                            Toastr::error('Item not found in System ' . $request->excel_part_no[$i], 'Failed');
                            return redirect()->back();
                        }
                    }
                }
            }
            if (count($data) > 0) {
                DB::table('sys_crm_quote_cart_edit')->insert($data);
            }
            DB::commit();
            Toastr::success('Item Imported Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
