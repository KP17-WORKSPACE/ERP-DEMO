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
use App\SmGeneralSettings;
use App\SysAppTabs;
use App\ApiBaseMethod;
use App\SmInspectingDepartment;
use App\SysCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Brian2694\Toastr\Facades\Toastr;
//use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade as PDF;


use App\Role;
use App\User;
use Illuminate\Support\Facades\Hash;

use function GuzzleHttp\Promise\exception_for;

class SysPageFrameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request)
    {
        try{
            return view('backEnd.pageframe');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }        
    }
}