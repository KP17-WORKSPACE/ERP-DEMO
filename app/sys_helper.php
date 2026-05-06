<?php

use App\SysLedgerEntries;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

function ledgerentry($trnid, $trntype, $account, $dramt, $cramt){
    $entrydate = Carbon::now('+04:00')->addDays(-1)->format('Y-m-d');
    $le = new SysLedgerEntries();
    $le->transaction_id = $trnid;
    $le->transaction_type = $trntype;
    $le->account_id = $account;
    $le->entry_date = $entrydate;
    $le->acc_type = 1; //Credit
    $le->dr_amount = $dramt;
    $le->cr_amount = $cramt;
    $le->status = 1;
    $le->created_by = Auth::user()->id;
    $le->company_id = session('logged_session_data.company_id');
    $le->save();
}

?>