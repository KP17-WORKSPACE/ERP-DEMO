<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmDesignation;
use App\SmInspectingDepartment;
use App\SmItem;
use Illuminate\Http\Request;
use App\SmItemStore;
use App\SmStaff;
use App\SysBookClosed;
use App\SysBookClosedData;
use App\SysBrand;
use App\SysChartofAccounts;
use App\SysCompany;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCrmDealsCollaboration;
use App\SysCrmDealsComments;
use App\SysCrmDealTrack;
use App\SysCrmDealTrackTemp;
use App\SysCrmEndUser;
use App\SysCrmLeads;
use App\SysCrmQuoteCharges;
use App\SysCrmQuoteCSItems;
use App\SysCrmQuoteItems;
use App\SysCrmService;
use App\SysCrmServiceComments;
use App\SysCrmSupport;
use App\SysCrmSupportActivity;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysPurchaseAuto;
use App\SysPurchaseOrderItems;
use App\SysShipping;
use App\SysStates;
use App\SysStockIn;
use App\SysStockInSerialNo;
use App\SysSupplierType;
use App\SysVat;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;
use Validator;

class SysBookCloseController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }


    public function index(Request $request)
    {
        try {
            $latestPerBook = DB::table('sys_book_closed_data')
                ->select('book_id', DB::raw('MAX(id) as max_id'))
                ->where('company_id', session('logged_session_data.company_id'))
                ->groupBy('book_id');

            $book_data = DB::table('sys_book_closed as b')
                ->select(
                    'b.id',
                    'b.book_name',
                    DB::raw('COALESCE(bd.book_closed_date, "") as book_closed_date'),
                    DB::raw('COALESCE(s.full_name, "") as updated_by'),
                    DB::raw('COALESCE(bd.updated_at, "") as updated_at')
                )
                ->leftJoinSub($latestPerBook, 'latest', function ($join) {
                    $join->on('latest.book_id', '=', 'b.id');
                })
                ->leftJoin('sys_book_closed_data as bd', 'bd.id', '=', 'latest.max_id')
                ->leftJoin('sm_staffs as s', 's.id', '=', 'bd.updated_by')
                ->get();


            // $latestSubQuery = DB::table('sys_book_closed_data')
            //     ->select(DB::raw('MAX(id)'))
            //     ->where('company_id', session('logged_session_data.company_id'))
            //     ->groupBy('book_id')->value('book_id');
            // if($latestSubQuery==""){
            //         $book_data = DB::table('sys_book_closed as b')
            //         ->select(
            //             'b.book_id',
            //             'b.book_name',
            //             '"" as book_closed_date',
            //             '"" as updated_by',
            //             '"" as updated_at'
            //         )
            //         ->where('company_id', session('logged_session_data.company_id'))
            //         ->get();

            // } else {
            //     $book_data = DB::table('sys_book_closed as b')
            //         ->select(
            //             'b.book_id',
            //             'b.book_name',
            //             'bd.book_closed_date',
            //             'bd.updated_by',
            //             'bd.updated_at'
            //         )
            //         ->join('sys_book_closed_data as bd', 'bd.book_id', '=', 'b.id')
            //         ->whereIn('bd.id', $latestSubQuery)
            //         ->get();
            // }


            return view('backEnd.bookclose.index', compact('book_data'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function edit($id)
    {
        $book = DB::table('sys_book_closed')->where('id', $id)->first();

        $history = DB::table('sys_book_closed_data as b')->select('b.*', 's.full_name')
            ->join('sm_staffs as s', 's.user_id', 'b.updated_by')
            ->where('b.book_id', $id)->where('b.company_id', session('logged_session_data.company_id'))
            ->orderBy('b.id', 'desc')
            ->get()
            ->map(function ($r) {

                $attachments = [];

                if (!empty($r->attachment)) {
                    $files = explode('|', $r->attachment);

                    foreach ($files as $file) {
                        $file = trim($file);
                        if ($file === '') continue;
                        $attachments[] = '<a href="' . asset('public/uploads/book_close/' . $file) . '" target="_blank" title="' . e($file) . '"><i class="fa fa-paperclip" aria-hidden="true"></i> ' . e($file) . '</a>';
                    }
                }

                return [
                    'id' => $r->id,
                    'book_closed_date' => date('d/m/Y', strtotime($r->book_closed_date)),
                    'reason' => $r->reason,
                    'updatedby' => $r->full_name,
                    'attachment' => count($attachments) ? implode('<br>', $attachments) : '',
                ];
            });

        return response()->json(compact('book', 'history'));
    }

    public function update(Request $request)
    {
        $fileNames = [];
        $uploadDir = public_path('uploads/book_close');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                if ($file->isValid()) {
                    $name = $file->getClientOriginalName();
                    $file->move($uploadDir, $name);
                    $fileNames[] = $name;
                }
            }
        }

        DB::table('sys_book_closed_data')->insert([
            'book_id' => $request->book_id,
            'book_closed_date' => SysHelper::normalizeToYmd($request->book_closed_date),
            'reason' => $request->reason,
            'attachment' => implode('|', $fileNames), // 👈 IMPORTANT
            'company_id' => $request->company_id,
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        return response()->json(['status' => true]);
    }

    public function update_all(Request $request)
    {
        $fileNames = [];
        $uploadDir = public_path('uploads/book_close');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                if ($file->isValid()) {
                    $name = $file->getClientOriginalName();
                    $file->move($uploadDir, $name);
                    $fileNames[] = $name;
                }
            }
        }
        $b = SysBookClosed::select('id')->get();
        if (count($b) > 0) {
            foreach ($b as $val) {
                DB::table('sys_book_closed_data')->insert([
                    'book_id' => $val->id,
                    'book_closed_date' => SysHelper::normalizeToYmd($request->book_closed_date),
                    'reason' => $request->reason,
                    'attachment' => implode('|', $fileNames), // 👈 IMPORTANT
                    'company_id' => $request->company_id,
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ]);
            }
        }
        return response()->json(['status' => true]);
    }

    public function delete($id)
    {
        DB::table('sys_book_closed_data')->where('id', $id)->update(['status' => 2]);
        return response()->json(['status' => true]);
    }

    public function edit_denied()
    {
        try {
            return view('backEnd.bookclose.edit-denied');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function doc_index(Request $request)
    {
        try {
            $latestPerBook = DB::table('sys_book_closed_doc_number')
                ->select('book_id', DB::raw('MAX(id) as max_id'))
                ->where('company_id', session('logged_session_data.company_id'))
                ->groupBy('book_id');

            $book_data = DB::table('sys_book_closed as b')
                ->select(
                    'b.id',
                    'b.book_name',
                    DB::raw('COALESCE(bd.closing_date, "") as book_closed_date'),
                    DB::raw('COALESCE(s.full_name, "") as updated_by'),
                    DB::raw('COALESCE(bd.updated_at, "") as updated_at'),
                    DB::raw('COALESCE(bd.doc_number, "") as doc_number')
                )
                ->leftJoinSub($latestPerBook, 'latest', function ($join) {
                    $join->on('latest.book_id', '=', 'b.id');
                })
                ->leftJoin('sys_book_closed_doc_number as bd', 'bd.id', '=', 'latest.max_id')
                ->leftJoin('sm_staffs as s', 's.id', '=', 'bd.updated_by')
                ->get();


            return view('backEnd.bookclose.doc-index', compact('book_data'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function doc_edit($id)
    {
        $book = DB::table('sys_book_closed')->where('id', $id)->first();

        $history = DB::table('sys_book_closed_doc_number as b')->select('b.*', 's.full_name')
            ->join('sm_staffs as s', 's.user_id', 'b.updated_by')
            ->where('b.book_id', $id)->where('b.company_id', session('logged_session_data.company_id'))
            ->orderBy('b.id', 'desc')
            ->get()
            ->map(function ($r) {

                return [
                    'id' => $r->id,
                    'closing_date' => SysHelper::normalizeToDmy($r->closing_date),
                    'doc_number' => $r->doc_number,
                    'updatedby' => $r->full_name,
                ];
            });

        return response()->json(compact('book', 'history'));
    }

    public function doc_update(Request $request)
    {
        DB::table('sys_book_closed_doc_number')->insert([
            'book_id' => $request->book_id,
            'closing_date' => SysHelper::normalizeToYmd($request->closing_date),
            'doc_number' => $request->doc_number,
            'company_id' => $request->company_id,
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        return response()->json(['status' => true]);
    }

    public function doc_delete($id)
    {
        DB::table('sys_book_closed_doc_number')->where('id', $id)->update(['status' => 2]);
        return response()->json(['status' => true]);
    }

}