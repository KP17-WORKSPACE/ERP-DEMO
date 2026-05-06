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

class SysAppTabsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
         try {
            $app = new SysAppTabs();
            $app->tab_session = session_id();
            $app->tab_name = $request->tab_name;
            $app->page_url = $request->page_url;
            $app->status = 1;
            $app->created_by = Auth::user()->id;
            $app->save();
            $app->toArray();

            $ret='SUCCESS';
                return json_encode(array('data'=>$ret));


        } catch (\Exception $e) {
            $ret='ERROR';
             return json_encode(array('data'=>$ret));
        }
    }
    public function del(Request $request)
    {
         try {
            DB::table('sys_app_tabs')->where('id', $request->id)->delete();
            $ret='SUCCESS';
            return json_encode(array('data'=>$ret));
        } catch (\Exception $e) {
            $ret='ERROR';
             return json_encode(array('data'=>$ret));
        }
    }
}
