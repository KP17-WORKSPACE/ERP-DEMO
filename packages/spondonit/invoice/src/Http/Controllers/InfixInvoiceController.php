<?php

namespace Spondonit\Invoice\Http\Controllers;

use DB;
use PDF;

use App\Role;
use App\SmItem;
use App\SmStaff;
use App\SmTender;
use Carbon\Carbon;
use App\SmCurrency;
use App\SmAddIncome;
use App\SmBaseSetup;
use App\SmDesignation;
use App\SmItemReceive;
use App\SmTenderProduct;
use App\SmPaymentMethhod;
use App\SmHumanDepartment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Project\Entities\InfixProject;
use Spondonit\Invoice\Models\InfixInvoice;
use Spondonit\Invoice\Models\InfixInvoiceProduct;
use Spondonit\Invoice\Models\InfixInvoiceSetting;
use Spondonit\Invoice\Models\InfixInvoiceCategory;
use Spondonit\Invoice\Models\InfixInvoiceCategoryLink;

class InfixInvoiceController extends Controller
{
    public function index()
    {
        return view('invoice::invoice');
    }


    public function invoiceCategory()
    {
        $categories = InfixInvoiceCategory::all();
        return view('invoice::invoiceCategory', compact('categories'));
    }

    public function invoiceCategoryStore(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $category = new InfixInvoiceCategory();
        $category->name = $request->name;
        $result = $category->save();

        if ($result) {
            return redirect()->back()->with('message-success', 'Category has been created successfully');
        } else {
            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }
    }

    public function invoiceCategoryEdit($id)
    {
        $categories = InfixInvoiceCategory::all();
        $category = InfixInvoiceCategory::find($id);
        return view('invoice::invoiceCategory', compact('categories', 'category'));
    }


    public function invoiceCategoryUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);
        $category = InfixInvoiceCategory::find($request->id);
        $category->name = $request->name;
        $result = $category->save();

        if ($result) {
            return redirect('infix/invoice-category')->with('message-success', 'Category has been updated successfully');
        } else {
            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }
    }


    public function invoiceCategoryDelete($id)
    {
        $result = InfixInvoiceCategory::destroy($id);

        if ($result) {
            return redirect('infix/invoice-category')->with('message-success-delete', 'Category has been deleted successfully');
        } else {
            return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
        }
    }

    public function invoiceCategoryAssign($id)
    {
        $category = InfixInvoiceCategory::find($id);

        $assigned_ids = [];
        foreach (explode('-', $category->link_ids) as $link_id) {
            $assigned_ids[] = $link_id;
        }

        $links = InfixInvoiceCategoryLink::all();

        return view('invoice::invoiceCategoryAssign', compact('category', 'links', 'assigned_ids'));
    }

    public function invoicePermissionStore(Request $request)
    {

        $links_ids = '';
        $i = 0;
        if (isset($request->permissions)) {
            foreach ($request->permissions as $permission) {
                $i++;
                if ($i == 1) {
                    $links_ids .= $permission;
                } else {
                    $links_ids .= '-' . $permission;
                }
            }
        }


        $category = InfixInvoiceCategory::find($request->category_id);
        $category->link_ids = $links_ids;
        $result = $category->save();


        if ($result) {
            return redirect('infix/invoice-category')->with('message-success-delete', 'Permission has been assigned successfully');
        } else {
            return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
        }
    }


    public function invoiceSetting()
    {
        $invoiceSetting = InfixInvoiceSetting::first();


        return view('invoice::invoiceSetting', compact('invoiceSetting'));
    }

    public function invoiceSettingUpdate(Request $request)
    {
        $request->validate([
            'tax' => 'required',
            'tax_type' => 'required'
        ]);
        $invoiceSetting = InfixInvoiceSetting::first();
        $invoiceSetting->tax = $request->tax;
        $invoiceSetting->prefix = $request->prefix;
        $invoiceSetting->tax_type = $request->tax_type;
        $result = $invoiceSetting->save();

        if ($result) {
            return redirect()->back()->with('message-success', 'Invoice setting has been updated successfully');
        } else {
            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }
    }

    public function invoiceCreate()
    {

        $customers = SmStaff::where('role_id', 2)->get();

        $items = SmItem::all();
        $payment_methods = SmPaymentMethhod::all();

        // $projects = SmTender::all();
        $currencies = SmCurrency::all();
        $projects = InfixProject::all();

        $invoice_setting = InfixInvoiceSetting::first();

        $invoice_number = InfixInvoice::max('invoice_number');

        $max_staff_no = SmStaff::max('staff_no');
        $roles = Role::where('active_status', '=', '1')->where('id', 2)->get();
        $departments = SmHumanDepartment::where('active_status', '=', '1')->get();
        $designations = SmDesignation::where('active_status', '=', '1')->orderBy('title', 'asc')->get();
        $marital_ststus = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '4')->get();

        $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->get();


        return view('invoice::invoiceCreate', compact('customers', 'payment_methods', 'projects', 'currencies', 'max_staff_no', 'roles', 'departments', 'designations', 'marital_ststus', 'genders', 'items', 'invoice_setting', 'invoice_number'));
    }




    public function invoiceStore(Request $request)
    {






        DB::beginTransaction();
        try {

            $invoice = new InfixInvoice();
            $invoice->customer_id = $request->customer;
            $invoice->invoice_number = $request->invoice_no;

            $invoice->project_id = $request->project;
            $invoice->payment_method_id = $request->payment_method;

            if ($request->invoice_date != "") {
                $invoice->invoice_date = Carbon::createFromFormat('d/m/Y',$request->invoice_date)->format('Y-m-d');
            }


            if ($request->due_date != "") {
                $invoice->invoice_due_date = Carbon::createFromFormat('d/m/Y',$request->due_date)->format('Y-m-d');
            }
            $invoice->currency_id = $request->currency;
            $invoice->recurring_cycle = $request->recurring;
            $invoice->payment_status = $request->payment_status;
            if ($request->payment_status=='PP') {
                $invoice->partial_paymemt = $request->paid_amount;
            }
            if ($request->payment_status=='P') {
                $invoice->partial_paymemt = 0.00;
            }

            $invoice->discount_type = $request->discount_type;

            $invoice->discount_amount = $request->discount;

            $invoice->public_note = $request->public_note;
            $invoice->private_note = $request->private_note;
            $invoice->terms_note = $request->terms_note;
            $invoice->footer_note = $request->footer_note;

            $invoice->tax_percentage = $request->tax;

            $invoice->signature_person = $request->signature_person;
            $invoice->signature_company = $request->signature_company;

            $invoice->save();
            $invoice->toArray();
            if ($invoice->payment_status!='UP') {
                $invoice_text='invoiceNnumber_'.$invoice->invoice_number.'_'.$invoice->id;
                $check_income=SmAddIncome::where('name','=',$invoice_text)->first();

                if ($check_income==null) {
                    $income=new SmAddIncome();
                    $income->name=$invoice_text;
                    $income->payment_method_id=$invoice->payment_method_id;
                    $income->date= $invoice->invoice_due_date;
                    $income->amount= $request->paid_amount;
                    $income->save();
                }else{
                    $income=new SmAddIncome();
                    $income->name=$invoice_text;
                    $income->payment_method_id=$invoice->payment_method_id;
                    $income->date= $invoice->invoice_due_date;
                    $income->amount= $request->paid_amount;
                    $income->save();
                }
            }

            $i = 0;


            foreach ($request->products as $product) {
                if ($product != 'none') {
                    $invoice_product = new InfixInvoiceProduct();
                    $invoice_product->invoice_id = $invoice->id;
                    $invoice_product->product_id = $product;
                    $invoice_product->description = $request->description[$i];
                    $invoice_product->quantity = $request->quantity[$i];
                    $invoice_product->price = $request->unit_price[$i];
                    $invoice_product->save();
                }

                $i++;
            }






            DB::commit();

            return redirect('infix/invoice-list')->with('message-success', 'Invoice has been created successfully');
        } catch (\Exception $e) {
            DB::rollback();


            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }
    }


    public function invoiceList()
    {
        $invoices = InfixInvoice::all();
        $invoice_setting = InfixInvoiceSetting::first();
        return view('invoice::invoiceList', compact('invoices', 'invoice_setting'));
    }


    public function invoiceView($id)
    {
        $invoice = InfixInvoice::find($id);

        $invoice_setting = InfixInvoiceSetting::first();
        return view('invoice::invoiceView', compact('invoice', 'invoice_setting'));
    }

    public function invoiceEdit($id)
    {
        $customers = SmStaff::where('role_id', 2)->get();
        $items = SmItem::all();
        $payment_methods = SmPaymentMethhod::all();

        $projects = SmTender::all();
        $currencies = SmCurrency::all();

        $invoice_setting = InfixInvoiceSetting::first();



        $max_staff_no = SmStaff::max('staff_no');
        $roles = Role::where('active_status', '=', '1')->where('id', 2)->get();
        $departments = SmHumanDepartment::where('active_status', '=', '1')->get();
        $designations = SmDesignation::where('active_status', '=', '1')->orderBy('title', 'asc')->get();
        $marital_ststus = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '4')->get();

        $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->get();

        $invoice = InfixInvoice::find($id);


        return view('invoice::invoice_edit_new', compact('invoice', 'customers', 'payment_methods', 'projects', 'currencies', 'max_staff_no', 'roles', 'departments', 'designations', 'marital_ststus', 'genders', 'items', 'invoice_setting'));
    }



    public function invoiceUpdate(Request $request)
    {


        // return $request;

        DB::beginTransaction();
        try {

            $invoice = InfixInvoice::find($request->id);
            $invoice->customer_id = $request->customer;
            $invoice->invoice_number = $request->invoice_no;

            $invoice->project_id = $request->project;
            $invoice->payment_method_id = $request->payment_method;

            if ($request->invoice_date != "") {
                $invoice->invoice_date = Carbon::createFromFormat('d/m/Y',$request->invoice_date)->format('Y-m-d');
            }


            if ($request->due_date != "") {
                $invoice->invoice_due_date = Carbon::createFromFormat('d/m/Y',$request->due_date)->format('Y-m-d');
            }
            $invoice->currency_id = $request->currency;
            $invoice->recurring_cycle = $request->recurring;
            $invoice->payment_status = $request->payment_status;
            if ($request->payment_status=='PP') {
                $invoice->partial_paymemt = $request->paid_amount;
            }
            if ($request->payment_status=='P') {
                $invoice->partial_paymemt = 0.00;
            }

            $invoice->discount_type = $request->discount_type;

            $invoice->discount_amount = $request->discount;

            $invoice->public_note = $request->public_note;
            $invoice->private_note = $request->private_note;
            $invoice->terms_note = $request->terms_note;
            $invoice->footer_note = $request->footer_note;

            $invoice->tax_percentage = $request->tax;
            $invoice->signature_person = $request->signature_person;
            $invoice->signature_company = $request->signature_company;

            $invoice->save();
            $invoice->toArray();

            if ($invoice->payment_status!='UP') {
                $invoice_text='invoiceNnumber_'.$invoice->invoice_number.'_'.$invoice->id;
                $check_income=SmAddIncome::where('name','=',$invoice_text)->first();

                if ($check_income==null) {
                    $income=new SmAddIncome();
                    $income->name=$invoice_text;
                    $income->payment_method_id=$invoice->payment_method_id;
                    $income->date= $invoice->invoice_due_date;
                    $income->amount= $request->paid_amount;
                    $income->save();
                }else{
                    $new_payment=floatval($request->paid_amount)- floatval($check_income->amount);
                    
                    $income=new SmAddIncome();
                    $income->name=$invoice_text;
                    $income->payment_method_id=$invoice->payment_method_id;
                    $income->date= $invoice->invoice_due_date;
                    $income->amount= $new_payment;
                    $income->save();
                }
            }

            InfixInvoiceProduct::where('invoice_id', $invoice->id)->delete();

            $i = 0;
            foreach ($request->products as $product) {

                if ($product != 'none') {
                    $invoice_product = new InfixInvoiceProduct();
                    $invoice_product->invoice_id = $invoice->id;
                    $invoice_product->product_id = $product;
                    $invoice_product->description = $request->description[$i];
                    $invoice_product->quantity = $request->quantity[$i];
                    $invoice_product->price = $request->unit_price[$i];
                    $invoice_product->save();
                }

                $i++;
            }




            DB::commit();

            return redirect('infix/invoice-list')->with('message-success', 'Invoice has been updated successfully');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }
    }


    public function invoiceGenerate($id)
    {
        $invoice = InfixInvoice::find($id);
        $invoice_setting = InfixInvoiceSetting::first();

        // return view('invoice::invoiceGenerate', compact('invoice', 'invoice_setting'));


        $pdf = PDF::loadView('invoice::invoiceGenerate', ['invoice' => $invoice, 'invoice_setting' => $invoice_setting]);
        return $pdf->stream(date('d-m-Y') . '-' . $invoice->invoice_no . '-' . 'invoice.pdf');


        $customPaper = array(0, 0, 700.00, 1900.80);
        $pdf = PDF::loadView(
            'invoice::invoiceGenerate',
            [
                'invoice' => $invoice,
                'invoice_setting' => $invoice_setting,
            ]
        )
        ->setPaper('A4', 'portrait');
        // ->setPaper($customPaper, 'landscape');
        return $pdf->stream(date('d-m-Y') . '-' . $invoice->invoice_no . '-' . 'invoice.pdf');
    }

    public function invoiceDelete($id)
    {

        $invoice = InfixInvoice::destroy($id);

        if ($invoice) {
            return redirect()->back()->with('message-success', 'Invoice has been deleted successfully');
        } else {
            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }
    }









    public function paymentMethodStore(Request $request)
    {
        $payment_method = new SmPaymentMethhod();
        $payment_method->method = $request->method;
        $result = $payment_method->save();

        if ($result) {
            return redirect()->back()->with('message-success', 'Payment method has been created successfully');
        } else {
            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }
    }



    public function getReceiveItemTender()
    {
        $items = SmItem::all();

        $searchData = [];
        foreach ($items as $item) {
            $searchData[] =  ['id' => $item->id, 'name' => $item->item_name];
        }

        if (!empty($searchData)) {
            return json_encode($searchData);
        }
    }

    public function getReceiveItemDetails(Request $request)
    {

        $product_quantity = SmItemReceive::where('product_id', $request->id)->sum('qnt');
        $sale_quantity = SmTenderProduct::where('product_id', $request->id)->sum('qnt');
        $product_quantity = $product_quantity - $sale_quantity;


        $searchData = SmItemReceive::where('product_id', $request->id)->orderBy('id', 'desc')->first();


        if (!empty($searchData)) {
            return json_encode([$searchData, $product_quantity]);
        }
    }
}
