<?php

namespace App\Http\Controllers;


use App\SysHelper;
use App\Chequebook;
use App\SysPayment;
use App\SysPaymentCheque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;



class ChequeBookController extends Controller
{


    public function index(Request $request)
    {
        try {
            $accounts = SysHelper::get_bank_account();
            if ($accounts === 0 || $accounts === null || !is_countable($accounts)) {
                $accounts = collect();
            }
            $selectedBankId = $request->input('account_id', 'all');
            $companyId = session('logged_session_data.company_id');

            if ($selectedBankId === 'all') {
                $chequebooks = Chequebook::withTrashed()->with('bank')->where('company_id', $companyId)->latest()->get();
            } elseif (!empty($selectedBankId)) {
                $chequebooks = Chequebook::withTrashed()->with('bank')->where('bank_id', $selectedBankId)->where('company_id', $companyId)->latest()->get();
            } else {
                // fallback if account_id is empty
                $chequebooks = Chequebook::withTrashed()->with('bank')->where('company_id', $companyId)->latest()->get();
                $selectedBankId = 'all';
            }

            $chequebookIds = $chequebooks->pluck('id')->filter()->toArray();

            $statusStats = SysPayment::select(
                    'chequebook_id',
                    DB::raw('SUM(CASE WHEN cheque_status = 4 THEN 1 ELSE 0 END) AS issued'),
                    DB::raw('SUM(CASE WHEN cheque_status = 2 THEN 1 ELSE 0 END) AS cleared'),
                    DB::raw('SUM(CASE WHEN cheque_status = 3 THEN 1 ELSE 0 END) AS missed'),
                    DB::raw('SUM(CASE WHEN cheque_status = 1 THEN 1 ELSE 0 END) AS cancelled'),
                    DB::raw('COUNT(*) AS used')
                )
                ->whereIn('chequebook_id', $chequebookIds)
                ->groupBy('chequebook_id')
                ->get()
                ->keyBy('chequebook_id');

            foreach ($chequebooks as $chequebook) {
                $stats = $statusStats->get($chequebook->id);
                $chequebook->issued_count = $stats ? (int) $stats->issued : 0;
                $chequebook->cleared_count = $stats ? (int) $stats->cleared : 0;
                $chequebook->missed_count = $stats ? (int) $stats->missed : 0;
                $chequebook->cancelled_count = $stats ? (int) $stats->cancelled : 0;
                $chequebook->used_count = $stats ? (int) $stats->used : 0;
                $chequebook->total_count = (int) $chequebook->no_of_cheques;
                $chequebook->remaining_count = max(0, $chequebook->total_count - $chequebook->used_count);
            }

            return view('backEnd.chequebook.view', compact('accounts', 'chequebooks', 'selectedBankId'));

        } catch (\Exception $e) {
            dd($e->getMessage());
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {

        try {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('chequebooks', 'public');
            }

            $docNumber = SysHelper::get_new_chequebook_doc_number();

            Chequebook::create([
                'doc_number' => $docNumber,
                'bank_id' => $request->input('account_id'),
                'no_of_cheques' => $request->input('no_of_cheques'),
                'start_no' => $request->input('start_no'),
                'end_no' => $request->input('end_no'),
                'attachment' => $attachmentPath,
                'remarks' => $request->input('remarks'),
                'created_by' => Auth::id(),
                'company_id' => session('logged_session_data.company_id')
            ]);

            Toastr::success('Cheque book saved successfully', 'Success');
            $accountId = $request->input('account_id');
            $url = url('chequebook');
            if (!empty($accountId)) {
                $url .= '?account_id=' . urlencode($accountId);
            }
            return redirect($url);
        } catch (\Exception $e) {
            dd($e->getMessage());
            Toastr::error('Operation Failed: ' . $e->getMessage(), 'Failed');
            $accountId = $request->input('account_id');
            $url = url('chequebook');
            if (!empty($accountId)) {
                $url .= '?account_id=' . urlencode($accountId);
            }
            return redirect($url)->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $chequebook = Chequebook::findOrFail($id);
            $chequebook->deleted_by = Auth::id();
            $chequebook->save();
            $chequebook->delete();

            Toastr::success('Cheque book deleted successfully', 'Success');
            $accountId = $request->input('account_id');
            $url = url('chequebook');
            if (!empty($accountId)) {
                $url .= '?account_id=' . urlencode($accountId);
            }
            return redirect($url);
        } catch (\Exception $e) {
            Toastr::error('Delete failed: ' . $e->getMessage(), 'Failed');
            $accountId = $request->input('account_id');
            $url = url('chequebook');
            if (!empty($accountId)) {
                $url .= '?account_id=' . urlencode($accountId);
            }
            return redirect($url);

        }
    }

    public function restore(Request $request, $id)
    {
        try {
            $chequebook = Chequebook::withTrashed()->findOrFail($id);
            if ($chequebook->trashed()) {
                $chequebook->restore();
                $chequebook->deleted_by = null;
                $chequebook->save();
            }

            Toastr::success('Cheque book restored successfully', 'Success');
            $accountId = $request->input('account_id');
            $url = url('chequebook');
            if (!empty($accountId)) {
                $url .= '?account_id=' . urlencode($accountId);
            }
            return redirect($url);
        } catch (\Exception $e) {
            Toastr::error('Restore failed: ' . $e->getMessage(), 'Failed');
            $accountId = $request->input('account_id');
            $url = url('chequebook');
            if (!empty($accountId)) {
                $url .= '?account_id=' . urlencode($accountId);
            }
            return redirect($url);

        }
    }

    public function globalSearch(Request $request)
    {
        try {
            $q = trim($request->input('q', ''));
            $companyId = session('logged_session_data.company_id');

            if (strlen($q) < 2) {
                return response()->json(['success' => true, 'chequebooks' => [], 'cheques' => []]);
            }

            $like = '%' . $q . '%';

            $chequebooks = Chequebook::withTrashed()
                ->with('bank')
                ->where('company_id', $companyId)
                ->where(function ($query) use ($like) {
                    $query->where('doc_number', 'LIKE', $like)
                          ->orWhere('remarks', 'LIKE', $like)
                          ->orWhere('start_no', 'LIKE', $like)
                          ->orWhere('end_no', 'LIKE', $like)
                          ->orWhereHas('bank', function ($q2) use ($like) {
                              $q2->where('account_name', 'LIKE', $like);
                          });
                })
                ->limit(20)
                ->get()
                ->map(function ($cb) {
                    return [
                        'id'         => $cb->id,
                        'doc_number' => $cb->doc_number,
                        'bank_name'  => optional($cb->bank)->account_name,
                        'start_no'   => $cb->start_no,
                        'end_no'     => $cb->end_no,
                        'remarks'    => $cb->remarks,
                        'deleted_at' => $cb->deleted_at,
                    ];
                });

            $cheques = SysPaymentCheque::with(['payment', 'bankname', 'suppliername', 'deal_code', 'createdby', 'cheque'])
                ->where('company_id', $companyId)
                ->where('status', 1)
                ->where(function ($query) use ($like) {
                    $query->where('doc_number', 'LIKE', $like)
                          ->orWhere('cheque_number', 'LIKE', $like)
                          ->orWhere('amount', 'LIKE', $like)
                          ->orWhere('other_supplier_name', 'LIKE', $like)
                          ->orWhereHas('suppliername', function ($q2) use ($like) {
                              $q2->where('account_name', 'LIKE', $like);
                          })
                          ->orWhereHas('bankname', function ($q2) use ($like) {
                              $q2->where('account_name', 'LIKE', $like);
                          })
                          ->orWhereHas('payment', function ($q2) use ($like) {
                              $q2->where('doc_number', 'LIKE', $like);
                          })
                          ->orWhereHas('createdby', function ($q2) use ($like) {
                              $q2->where('full_name', 'LIKE', $like);
                          });
                })
                ->limit(100)
                ->get()
                ->map(function ($item) {
                    return [
                        'id'              => $item->id,
                        'cheque_id'       => $item->cheque_id,
                        'doc_number'      => $item->doc_number,
                        'doc_date'        => $item->doc_date ? date('d/m/Y', strtotime($item->doc_date)) : null,
                        'bank_name'       => optional($item->bankname)->account_name,
                        'cheque_date'     => $item->cheque_date ? date('d/m/Y', strtotime($item->cheque_date)) : null,
                        'supplier_name'   => ($item->supplier_name == 0 ? $item->other_supplier_name : optional($item->suppliername)->account_name),
                        'amount'          => $item->amount,
                        'payment_doc'     => optional($item->payment)->doc_number,
                        'payment_id'      => optional($item->payment)->id,
                        'cheque_number'   => $item->cheque_number,
                        'deal_id'         => optional($item->deal_code)->code,
                        'deal_int_id'     => $item->deal_id,
                        'created_by'      => optional($item->createdby)->full_name,
                        'chequebook_doc'  => optional($item->cheque)->doc_number,
                        'cheque_status'   => optional($item->payment)->cheque_status,
                        'reference'       => $item->reference,
                    ];
                });

            return response()->json(['success' => true, 'chequebooks' => $chequebooks, 'cheques' => $cheques]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $chequebook = Chequebook::withTrashed()->findOrFail($id);

            $chequebook->bank_id = $request->input('account_id');
            $chequebook->no_of_cheques = $request->input('no_of_cheques');
            $chequebook->start_no = $request->input('start_no');
            $chequebook->end_no = $request->input('end_no');
            $chequebook->remarks = $request->input('remarks');

            if ($request->hasFile('attachment')) {
                $chequebook->attachment = $request->file('attachment')->store('chequebooks', 'public');
            }

            $chequebook->updated_by = Auth::id();
            $chequebook->save();

            Toastr::success('Cheque book updated successfully', 'Success');
            $accountId = $request->input('account_id');
            $url = url('chequebook');
            if (!empty($accountId)) {
                $url .= '?account_id=' . urlencode($accountId);
            }
            return redirect($url);
        } catch (\Exception $e) {
            Toastr::error('Update failed: ' . $e->getMessage(), 'Failed');
            $accountId = $request->input('account_id');
            $url = url('chequebook');
            if (!empty($accountId)) {
                $url .= '?account_id=' . urlencode($accountId);
            }
            return redirect($url);

        }
    }
}
