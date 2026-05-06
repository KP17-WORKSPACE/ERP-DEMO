{{--  crm-deal-track-approval-accounts  --}}
@if($deal->accounts==0 && (session('logged_session_data.designation_id')==8 || (App\SysHelper::is_approval_access() && $deal->accounts!=1)))
<div class="card p-3 mb-1">
    <h5 class="page-heading mb-1">For Accounts Approval</h5>
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-accounts','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-accounts']) }}
    
    <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
    <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
    <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />

    <div class="row">
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Customer Status
            <select class="form-control" name="customer_status" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Credit Limit
            <select class="form-control" name="credit_limit" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Payment Terms
            <select class="form-control" name="payment_terms" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Overdue Payment
            <select class="form-control" name="pending_payment" required>
              <option value="" selected>-Select-</option>
              <option value="2">Yes</option>
              <option value="1">No</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Other
            <select class="form-control" name="other" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    </div>
    <div class="row">
    <div class="col-lg-6 mb-1">
        <div class="no-gutters input-right-icon">
            <div class="col">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Remarks')<span></span></label>
                    <textarea class="form-control" rows="4" id="remarks"  name="remarks"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 pt-2">
        <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
        <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
        <button type="submit" class="btn btn-primary mt-5" id="btnSubmit">
                @lang('Submit')
        </button>
        
        
    </div>
    
    
</div>
{{ Form::close() }}
</div>
@endif
{{--  crm-deal-track-approval-accounts  --}}

{{--  crm-deal-track-approval-sales  --}}
@if($deal->sales==0 && (session('logged_session_data.designation_id')==27 || (App\SysHelper::is_approval_access() && $deal->accounts==1)))
<div class="card p-3 mb-1">
    <h5 class="page-heading mb-1">For Sales Manager Approval</h5>
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-sales','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-sales']) }}
    
    <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
    <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
    <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />
    
    <div class="row">
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Margin
            <select class="form-control" name="margin" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Stock
            <select class="form-control" name="stock" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Purchase Quote
            <select class="form-control" name="purcease_quote" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Other
            <select class="form-control" name="other" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Purchase Approval
            <select class="form-control" name="purchease_approval" required>
              <option value="1">Required</option>
              <option value="2">Not Required</option>
            </select>
          </div>
    </div>
    </div>
    <div class="row">
    <div class="col-lg-6 mb-1">
        <div class="no-gutters input-right-icon">
            <div class="col">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Remarks')<span></span></label>
                    <textarea class="form-control" rows="4" id="remarks"  name="remarks"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 pt-2">
        <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
        <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
        <button type="submit" class="btn btn-primary mt-5" id="btnSubmit">
                @lang('Submit')
        </button>
        
        
    </div>
    
    
</div>
{{ Form::close() }}
</div>
@endif
{{--  crm-deal-track-approval-sales  --}}


{{--  crm-deal-track-approval-purchease  20--}}
@if($deal->purchease==0 && (session('logged_session_data.designation_id')==20 || (App\SysHelper::is_approval_access() && $deal->sales==1)))
<div class="card p-3 mb-1">
    <h5 class="page-heading mb-1">For Purchase Approval</h5>
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-purchease','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-purchease']) }}
    
    <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
    <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
    <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />
    
    <div class="row">
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Purchase Quote
            <select class="form-control" name="purchease_quote" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">3 Quote Request
            <select class="form-control" name="quote_request" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="3">Not Required</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Purchase Status
            <select class="form-control" id="validation" name="validation" required>
              <option value="" selected>-Select-</option>
              <option value="1">Purchase Completed</option>
              <option value="3">Under Purchase</option>
              <option value="4">Partial Delivery</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <script>
      $('#validation').on('change', function(e) {
        if ($('#validation').val() == 1) {
          $('#div_validation').css("display", "block");
          $('#lpo_no').prop('required', true);
          $('#cost_of_purchase').prop('required', true);
          $('#cost_of_purchase_currency').prop('required', true);
          $('#part_no').prop('required', false);
          $('#supplier_name').prop('required', false);
          $('#delivery_date').prop('required', false);
          $('#div_validation2').css("display", "none");
        }
        else if ($('#validation').val() == 3) {
          $('#div_validation').css("display", "block");
          $('#lpo_no').prop('required', true);
          $('#cost_of_purchase').prop('required', true);
          $('#cost_of_purchase_currency').prop('required', true);
          $('#part_no').prop('required', true);
          $('#supplier_name').prop('required', true);
          $('#delivery_date').prop('required', true);
          $('#div_validation2').css("display", "none");
        }
        else if ($('#validation').val() == 4) {
          $('#div_validation2').css("display", "block");
          $('#div_validation').css("display", "none");
          $('#lpo_no').prop('required', false);
          $('#cost_of_purchase').prop('required', false);
          $('#cost_of_purchase_currency').prop('required', false);
          $('#part_no').prop('required', false);
          $('#supplier_name').prop('required', false);
          $('#delivery_date').prop('required', false);
        } else {
          $('#div_validation').css("display", "none");
          $('#lpo_no').prop('required', false);
          $('#cost_of_purchase').prop('required', false);
          $('#cost_of_purchase_currency').prop('required', false);
          $('#part_no').prop('required', false);
          $('#supplier_name').prop('required', false);
          $('#delivery_date').prop('required', false);
          $('#div_validation2').css("display", "none");
        }
      });
      </script>
    <div class="col-lg-3 mb-1" id="div_validation" style="display: none;">
      <div class="form-check-label">
        LPO No
        <input type="text" class="form-control" id="lpo_no" name="lpo_no"/>
          Cost of Purchase
          <input type="number" step="any" class="form-control" id="cost_of_purchase" name="cost_of_purchase"/>
          Currency
          <select class="form-control" name="cost_of_purchase_currency" id="cost_of_purchase_currency" required>
            <option value="">-Select-</option>
            @foreach ($currencylist as $value)
                <option value="{{ @$value->id }}">{{ @$value->code }}</option>
            @endforeach
        </select>
        Part No
        <input type="text" class="form-control" id="part_no" name="part_no"/>
        Supplier Name
        <input type="text" class="form-control" id="supplier_name" name="supplier_name"/>
        Delivery Date
        <input type="date" class="form-control" id="delivery_date" name="delivery_date"/>
      </div>
    </div>
    
    <div class="col-lg-3 mb-1" id="div_validation2" style="display: none;">
      Partial Delivery Note
      <input type="text" class="form-control" id="partial_delivery_note" name="partial_delivery_note"/>
    </div>

    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Other
            <select class="form-control" name="other" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Choose Quote 1
            <input type="file" class="form-control" id="fileone" name="fileone">
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Choose Quote 2
            <input type="file" class="form-control" id="filetwo" name="filetwo">
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Choose Quote 3
            <input type="file" class="form-control" id="filethree" name="filethree">
          </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-4 mb-1">
        <div class="no-gutters input-right-icon">
            <div class="col">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Remarks')<span></span></label>
                    <textarea class="form-control" rows="4" id="remarks"  name="remarks"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 pt-2">
        <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
        <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
        <button type="submit" class="btn btn-primary mt-5" id="btnSubmit">
                @lang('Submit')
        </button>
        
        
    </div>
    
    
</div>
{{ Form::close() }}
</div>
@endif
{{--  crm-deal-track-approval-purchease  --}}

{{--  crm-deal-track-approval-invoice  --}}
@if(($deal->invoice==0 || $deal->invoice==3) && $deal->sales==1 && ($deal->invoice==1 && $purchease[0]->validation ==3) && (session('logged_session_data.designation_id')==35 || (App\SysHelper::is_approval_access() && $deal->purchease==1 && $deal->sales==1)) || (session('logged_session_data.designation_id')==35 && $deal->purchease==4 && $deal->invoice!=1))
<div class="card p-3 mb-1">
    <h5 class="page-heading mb-1">For Invoice Approval</h5>
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-invoice','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-invoice']) }}
    
    <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
    <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
    <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />
    
    <div class="row">
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Delivery Advice
            <select class="form-control" name="delivery_advice" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Validation
            <select class="form-control" name="validation" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Hold
            <select class="form-control" name="hold" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
              <option value="3">Pending</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Print
            <select class="form-control" name="print" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
              <option value="3">Pending</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Invoice No
            <input type="text" class="form-control" id="invoice_no" name="invoice_no" required />
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Partial Invoice
            <select class="form-control" name="partial_invoice" id="partial_invoice">
              <option value="0">No</option>
              <option value="1">Yes</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1" id="partial_invoice_amount_div" style="display: none;">
        <div class="form-check-label">Partial Invoice Amount
            <input type="number" step="any" class="form-control" name="partial_invoice_amount" id="partial_invoice_amount" />
          </div>
    </div>
    <script>
      $('#partial_invoice').on('change', function(e) {
        if ($('#partial_invoice').val() == 1) {
          $('#partial_invoice_amount_div').css("display", "block");
          $('#partial_invoice_amount').prop('required', true);
        } else {
          $('#partial_invoice_amount_div').css("display", "none");
          $('#partial_invoice_amount').prop('required', false);
        }
      });
      </script>
    </div>
    <div class="row">
    <div class="col-lg-6 mb-1">
        <div class="no-gutters input-right-icon">
            <div class="col">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Remarks')<span></span></label>
                    <textarea class="form-control" cols="30" rows="4" id="remarks"  name="remarks"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 pt-2">
        <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
        <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
        <button type="submit" class="btn btn-primary mt-5" id="btnSubmit">
                @lang('Submit')
        </button>
        
        
    </div>
    
    
</div>
{{ Form::close() }}
</div>
@endif
{{--  crm-deal-track-approval-invoice  --}}

{{--  crm-deal-track-approval-delivery  --}}

@if( $deal->purchease == 1 && ((session('logged_session_data.designation_id')==34 || Auth::user()->id== 74) && $deal->delivery !=1 &&  (($deal->delivery==0 || $deal->delivery==3 ) || ( $deal->purchease != 4 && $deal->invoice==1) || (App\SysHelper::is_approval_access()  && $purchease[0]->validation !=3))))
<div class="card p-3 mb-1">
    <h5 class="page-heading mb-1">For Delivery Approval</h5>
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-delivery','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-delivery']) }}
    
    <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
    <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
    <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />
    <?php

$do_status="";
$do_no="";
$print_invoice_no="";
$cheque_collection="";
$cheque_collection_file="";
$delivery_status="";
$deliver_by="";
$driver_txt="";
$remarks="";
$cash_collected="";
$contact_no="";
$id_no="";
$attach_file="";
$awb_no="";
if(count($invoice)>0){
  $print_invoice_no=$invoice[0]->invoice_no;
}
if(count($delivery)>0){
  foreach ($delivery as $del){
    $do_status=$del->do_status;
    $do_no=$del->do_no;
    $print_invoice_no=$del->print_invoice_no;
    $cheque_collection=$del->cheque_collection;
    $cheque_collection_file=$del->cheque_collection_file;
    $delivery_status=$del->delivery_status;
    $deliver_by=$del->deliver_by;
    $driver_txt=$del->driver;
    $remarks=$del->remarks;
    $cash_collected=$del->cash_collected;
    $contact_no=$del->contact_no;
    $id_no=$del->id_no;
    $attach_file=$del->attach_file;
    $awb_no=$del->awb_no;
  }
}

?>
    <div class="row">
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Do Status
            <select class="form-control" name="do_status" required>
              <option value="" @if($do_status=="") selected @endif>-Select-</option>
              <option value="1" @if($do_status=="1") selected @endif>Approved</option>
              <option value="2" @if($do_status=="2") selected @endif>Disapproved</option>
            </select>
          </div>
    </div>


    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Do No
            <input type="text" class="form-control" value="{{ $do_no }}" name="do_no" required />
          </div>
    </div>
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Print Invoice No
            <input type="text" class="form-control" name="print_invoice_no" value="{{ $print_invoice_no }}" required />
          </div>
    </div>
    
    @if($deal->payment_mode==1)            
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Cash Collected
            <input type="number" class="form-control" name="cash_collected" value="{{ $cash_collected }}" required />
          </div>
    </div>
    @else
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Cheque Collection
            <select class="form-control" name="cheque_collection" required>
              <option value="" @if($cheque_collection=="") selected @endif>-Select-</option>
              <option value="1" @if($cheque_collection=="1") selected @endif>Approved</option>
              <option value="2" @if($cheque_collection=="2") selected @endif>Disapproved</option>
              <option value="3" @if($cheque_collection=="3") selected @endif>Pending</option>
            </select>
          </div>
    </div>
    
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Cheque Copy
          @if($cheque_collection_file!= "")
          <a class="text-info text-xs" href="{{ asset('public/uploads/crm_deal_track_doc/') }}/{{ $cheque_collection_file }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Cheque Copy</a>@endif
            <input type="file" class="form-control" id="cheque_collection_file" name="cheque_collection_file">
          </div>
    </div>            
    @endif

    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Delivery Status
            <select class="form-control" name="delivery_status" required>
              <option value="" @if($delivery_status=="") selected @endif>-Select-</option>
              <option value="2" @if($delivery_status=="2") selected @endif>Pending For Delivery</option>
              <option value="4" @if($delivery_status=="4") selected @endif>Ready For Delivery</option>
              <option value="3" @if($delivery_status=="3") selected @endif>Out For Delivery</option>
              <option value="1" @if($delivery_status=="1") selected @endif>Delivery Completed</option>
              <option value="5" @if($delivery_status=="5") selected @endif>Partial Delivery</option>
            </select>
          </div>
    </div>
    
    <div class="col-lg-3 mb-1">
        <div class="form-check-label">Delivered Through
            <select class="form-control" id="deliver_by_new" name="deliver_by" required>
              <option value="" @if($deliver_by=="") selected @endif>-Select-</option>
              <option value="1" @if($deliver_by==1) selected @endif>Courier</option>
              <option value="2" @if($deliver_by==2) selected @endif>Driver</option>
              <option value="3" @if($deliver_by==3) selected @endif>Local Delivery</option>
              <option value="4" @if($deliver_by==4) selected @endif>Office Boy</option>
              <option value="5" @if($deliver_by==5) selected @endif>Collection by Client</option>
              <option value="6" @if($deliver_by==6) selected @endif>By Email</option>
            </select>
          </div>
    </div>
    <div class="col-lg-3 mb-1" id="div_byemail" style="display: none;">
      <div class="form-check-label">Email IDs
          <input type="text" class="form-control" id="byemail" name="byemail" value="{{ $driver_txt }}" placeholder="Email Ids">
      </div>
    </div>
    <div class="col-lg-3 mb-1">
      <script>
        $('#deliver_by_new').on('change', function(e) {
          if ($('#deliver_by_new').val() == 1) {
            $('#div_courier').css("display", "block");
            $('#div_attach_file').css("display", "block");
            $('#div_driver').css("display", "none");
            $('#div_localdelivery').css("display", "none");
            $('#div_officeboy').css("display", "none");
            $('#div_collectionbyclient').css("display", "none");
            $('#div_byemail').css("display", "none");
          }
          if ($('#deliver_by_new').val() == 2) {
            $('#div_courier').css("display", "none");
            $('#div_attach_file').css("display", "none");
            $('#div_driver').css("display", "block");
            $('#div_localdelivery').css("display", "none");
            $('#div_officeboy').css("display", "none");
            $('#div_collectionbyclient').css("display", "none");
            $('#div_byemail').css("display", "none");
          }
          if ($('#deliver_by_new').val() == 3) {
            $('#div_courier').css("display", "none");
            $('#div_attach_file').css("display", "none");
            $('#div_driver').css("display", "none");
            $('#div_localdelivery').css("display", "block");
            $('#div_officeboy').css("display", "none");
            $('#div_collectionbyclient').css("display", "none");
            $('#div_byemail').css("display", "none");
          }
          if ($('#deliver_by_new').val() == 4) {
            $('#div_courier').css("display", "none");
            $('#div_attach_file').css("display", "none");
            $('#div_driver').css("display", "none");
            $('#div_localdelivery').css("display", "none");
            $('#div_officeboy').css("display", "block");
            $('#div_collectionbyclient').css("display", "none");
            $('#div_byemail').css("display", "none");
          }
          if ($('#deliver_by_new').val() == 5) {
            $('#div_courier').css("display", "none");
            $('#div_attach_file').css("display", "none");
            $('#div_driver').css("display", "none");
            $('#div_localdelivery').css("display", "none");
            $('#div_officeboy').css("display", "none");
            $('#div_collectionbyclient').css("display", "block");
            $('#div_byemail').css("display", "none");
          }
          if ($('#deliver_by_new').val() == 6) {
            $('#div_courier').css("display", "none");
            $('#div_attach_file').css("display", "none");
            $('#div_driver').css("display", "none");
            $('#div_localdelivery').css("display", "none");
            $('#div_officeboy').css("display", "none");
            $('#div_collectionbyclient').css("display", "none");
            $('#div_byemail').css("display", "block");
          }
          if ($('#deliver_by_new').val() == "") {
            $('#div_courier').css("display", "none");
            $('#div_attach_file').css("display", "none");
            $('#div_driver').css("display", "none");
            $('#div_localdelivery').css("display", "none");
            $('#div_officeboy').css("display", "none");
            $('#div_collectionbyclient').css("display", "none");
            $('#div_byemail').css("display", "none");
          }
      });
      </script>
    {{--  options  --}}
      <div class="form-check-label" id="div_courier" style="display: none;">Courier
          <select class="form-control" id="courier" name="courier">
              <option value="" selected>-Select-</option>
              @foreach ($shipping as $value)<option value="{{ @$value->shipping_name }}" @if($driver_txt==$value->shipping_name) selected @endif>{{ @$value->shipping_name }}</option>@endforeach
              <option value="Other">Other</option>
            </select>
            <input type="text" class="form-control" id="other_courier" name="other_courier" placeholder="Other Courier" style="display: none;"/>
            <script>
              $('#courier').on('change', function(e) {
                if ($('#courier').val() == "Other") {
                  $('#other_courier').css("display", "block");
                } else { $('#other_courier').css("display", "none"); }
            });
            </script>
      </div>
      <div class="form-check-label" id="div_driver" style="display: none;">Delivered By
          <select class="form-control" id="driver" name="driver">
              <option value="" selected>-Select-</option>
              @foreach ($driver as $value)<option value="{{ @$value->driver_name }}" @if($driver_txt==$value->driver_name) selected @endif>{{ @$value->driver_name }}</option>@endforeach
              <option value="Other">Other</option>
          </select>
          <input type="text" class="form-control" id="other_driver" name="other_driver" placeholder="Other Driver" style="display: none;"/>
          <script>
            $('#driver').on('change', function(e) {
              if ($('#driver').val() == "Other") {
                $('#other_driver').css("display", "block");
              } else { $('#other_driver').css("display", "none"); }
          });
          </script>
      </div>
      <div class="form-check-label" id="div_localdelivery" style="display: none;">Local Delivery
          <select class="form-control" id="localdelivery" name="localdelivery">
            <option value="" @if($driver_txt=="") selected @endif>-Select-</option>
            <option value="Salman" @if($driver_txt=="Ahmed") selected @endif>Ahmed</option>
            <option value="Mohid" @if($driver_txt=="Mohid") selected @endif>mohid</option>
            <option value="Usman" @if($driver_txt=="Usman") selected @endif>Usman</option>
            <option value="Shoyeb" @if($driver_txt=="Shoyeb") selected @endif>Shoyeb</option>
            <option value="Imran" @if($driver_txt=="Imran") selected @endif>Imran</option>
            <option value="Other">Other</option>
          </select>
          <input type="text" class="form-control" id="other_localdelivery" name="other_localdelivery" placeholder="Other Local Delivery" style="display: none;"/>
          <script>
            $('#localdelivery').on('change', function(e) {
              if ($('#localdelivery').val() == "Other") {
                $('#other_localdelivery').css("display", "block");
              } else { $('#other_localdelivery').css("display", "none"); }
          });
          </script>
      </div>
      <div class="form-check-label" id="div_officeboy" style="display: none;">Office Boy
          <select class="form-control" id="officeboy" name="officeboy">
            <option value="" @if($driver_txt=="") selected @endif>-Select-</option>
            <option value="Salman" @if($driver_txt=="Salman") selected @endif>Salman</option>
            <option value="Mohid" @if($driver_txt=="Mohid") selected @endif>Mohid</option>
            <option value="Manan" @if($driver_txt=="Manan") selected @endif>Mannan</option>
            <option value="Usman" @if($driver_txt=="Usman") selected @endif>Usman</option>
            <option value="Ziyad" @if($driver_txt=="Ziyad") selected @endif>Ziyad</option>
            <option value="Akhil" @if($driver_txt=="Akhil") selected @endif>Akhil</option>
            <option value="Other">Other</option>
          </select>
          <input type="text" class="form-control" id="other_officeboy" name="other_officeboy" placeholder="Other Office Boy" style="display: none;"/>
          <script>
            $('#officeboy').on('change', function(e) {
              if ($('#officeboy').val() == "Other") {
                $('#other_officeboy').css("display", "block");
              } else { $('#other_officeboy').css("display", "none"); }
          });
          </script>
      </div>
      <div class="form-check-label" id="div_collectionbyclient" style="display: none;">Collection by Client
          <input type="text" class="form-control" id="collectionbyclient" name="collectionbyclient" placeholder="Name" value="{{ $driver_txt }}">
          <input type="text" class="form-control mt-2" id="contact_no" name="contact_no" placeholder="Contact No" value="{{ $contact_no }}">
          <input type="text" class="form-control mt-2" id="id_no" name="id_no" placeholder="ID No" value="{{ $id_no }}">
      </div>
      <div class="form-check-label" id="div_attach_file" style="display: none;">
        <input type="text" class="form-control mt-2" id="awb_no" name="awb_no" placeholder="AWB No" value="{{ $awb_no }}">
      </div>
    {{--  options  --}}
    </div>
    <div class="col-lg-3 mb-1">
      <div class="form-check-label">Attachment/AWB Copy
        @if($attach_file!= "")
        <a class="text-info text-xs" href="{{ asset('public/uploads/crm_deal_track_doc/') }}/{{ $attach_file }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> AWB Copy</a>@endif
      <input type="file" class="form-control" id="attach_file" name="attach_file" >
      </div>
    </div>

    </div>
    <div class="row">
    <div class="col-lg-6 mb-1">
        <div class="no-gutters input-right-icon">
            <div class="col">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Remarks')<span></span></label>
                    <textarea class="form-control" rows="4" id="remarks"  name="remarks">{{ $remarks }}</textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 pt-2">
        <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
        <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
        <button type="submit" class="btn btn-primary mt-5" id="btnSubmit">
                @lang('Submit')
        </button>
    </div>
    
</div>
{{ Form::close() }}
</div>
@endif
{{--  crm-deal-track-approval-delivery  --}}


{{--  crm-deal-track-approval-professional-service  --}}
@if(($deal->technical==1 && $deal->tech==0) && (session('logged_session_data.designation_id')==33 || (App\SysHelper::is_approval_access() && $deal->delivery==1)))
<div class="card p-3 mb-1">
  <h5 class="page-heading mb-1">For Professional Service Approval</h5>

  {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-professional-service','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-professional-service']) }}
    <div class="row">
      <div class="col-lg-3 mb-1">
      <div class="form-check-label">Status
          <select class="form-control technical_approve" name="technical_approve" required>
            <option value="" selected>-Select-</option>
            <option value="1">Approved</option>
            <option value="2">Disapproved</option>
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-6 mb-1">
      <div class="no-gutters input-right-icon">
          <div class="col">
              <div class="input-effect">
                  <label class="txtlbl">@lang('Remarks')<span></span></label>
                  <textarea class="form-control" rows="4" id="remarks"  name="remarks"></textarea>
              </div>
          </div>
      </div>
      </div>
      <div class="col-lg-3 pt-2">
          <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
          <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
          <button type="submit" class="btn btn-primary mt-5" id="btnSubmit">
                  @lang('Submit')
          </button>
      </div>
    </div>
    {{ Form::close() }}

</div>
@endif
{{--  crm-deal-track-approval-professional-service  --}}

{{--  crm-deal-track-approval-receivables  --}}
@if(($deal->technical==1 && $deal->tech==1) || ($deal->technical==0))
@if(($deal->receivables==0 || $deal->receivables==3) && (session('logged_session_data.designation_id')==2 && $deal->delivery==1)  || (App\SysHelper::is_approval_access() && $deal->delivery==1))
<div class="card p-3 mb-1">
<h5 class="page-heading mb-1">For Receivables Approval

<span class="text-sm text-primary"> ( Payment Mode - 
@if($deal->payment_mode==1) Cash @endif
@if($deal->payment_mode==2) Cheque @endif
@if($deal->payment_mode==3) Bank Transfer @endif
@if($deal->payment_mode==4) Open Credit @endif
@if($deal->payment_mode==5) Credit Card @endif
@if($deal->payment_mode==6) Bank TT @endif )</span>
<a class="btn btn-xs text-info" onclick="update_payment_mode()" title="Edit Color"><i class="fa fa-edit" aria-hidden="true"></i></a>    
</h5>


<div id="div_update_payment_mode2" style="display: none; width: 500px;" class="border border-danger p-4">
{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-receivables-payment-mode', 'method' => 'POST', 'id' => 'update_payment_mode']) }}
<b>Change Payment Mode :</b>
<select class="dynamicstxt_s w-50" name="edit_payment_mode" id="edit_payment_mode" required>
  <option value="1" @if($deal->payment_mode==1) selected @endif>Cash</option>
  <option value="2" @if($deal->payment_mode==2) selected @endif>Cheque</option>
  <option value="3" @if($deal->payment_mode==3) selected @endif>Bank Transfer</option>
  <option value="4" @if($deal->payment_mode==4) selected @endif>Open Credit</option>
  <option value="5" @if($deal->payment_mode==5) selected @endif>Credit Card</option>
  <option value="6" @if($deal->payment_mode==6) selected @endif>Bank TT</option>
</select>
<input type="hidden" name="edit_payment_mode_id" value="{{ $deal->deal_id }}" />
<button type="submit" class="btn btn-xs btn-primary text-xs pt-1 pb-1">Change</button>

{{ Form::close() }}
</div>
<script>
function update_payment_mode() {
    if($('#div_update_payment_mode2').css('display') == 'none'){
      $("#div_update_payment_mode2").css("display", "block");
    }
    else{
      $("#div_update_payment_mode2").css("display", "none");
    }
}
</script>




{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-receivables','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-delivery']) }}

<input type="hidden" name="owner_id" value="{{ $del->owner }}" />
<input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
<input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />

<div class="row">
<div class="col-lg-3 mb-1">
<div class="form-check-label">Collection
    <select class="form-control payment_collection" name="payment_collection" required>
      <option value="" selected>-Select-</option>
      <option value="1">Approved</option>
      <option value="2">Disapproved</option>
      <option value="3">Order Cancelled</option>
    </select>
  </div>
</div>
<script>
$('.payment_collection').on('change', function(e) {
if ($('.payment_collection').val() == 3) {
  $('.credit_note_div').css("display", "block");
  $('.no_cn_div').css("display", "none");
  $('.credit_note').prop('required', true);
  $('.no_cn_req').prop('required', false);
}
else{
  $('.credit_note_div').css("display", "none");
  $('.no_cn_div').css("display", "block");
  $('.credit_note').prop('required', false);
  $('.no_cn_req').prop('required', true);
}
});
</script>
<div class="col-lg-2 mb-1 credit_note_div" style="display: none;">
<div class="form-check-label">Credit Note
    <input type="text" class="form-control credit_note" name="credit_note" />
  </div>
</div>
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">Payment Status
    <select class="form-control no_cn_req" name="payment_status" id="payment_status" required>
      <option value="" selected>-Select-</option>
      <option value="1">Payment Received</option>
      <option value="2">Pending</option>
    </select>
  </div>
</div>
<script>
$('#payment_status').on('change', function(e) {
if ($('#payment_status').val() == 2) {
  $('#payment_status_div').css("display", "block");
  $('#reminder_date').prop('required', true);
  $('#cheque_date').prop('required', false);
  $('#deposit_date').prop('required', false);
  $('#open_credit_date').prop('required', false);
  $('#payment_date').prop('required', false);
  $('#credit_card_deposit_date').prop('required', false);
  $('#banktt_date').prop('required', false);
}
else{
  $('#payment_status_div').css("display", "none");
  $('#reminder_date').prop('required', false);
  $('#cheque_date').prop('required', true);
  $('#deposit_date').prop('required', true);
  $('#open_credit_date').prop('required', true);
  $('#payment_date').prop('required', true);
  $('#credit_card_deposit_date').prop('required', true);
  $('#banktt_date').prop('required', true);
}
});
</script>
<div class="col-lg-3 mb-1" id="payment_status_div" style="display: none;">
<div class="input-group mb-1 text-danger">Reminder Date
    <input type="date" class="form-control" name="reminder_date" id="reminder_date" />
    <select class="form-control" name="reminder_time">
      <option value="" selected>-Select Time-</option>
      <option value="09:00:00">09:00 AM</option>
      <option value="10:00:00">10:00 AM</option>
      <option value="11:00:00">11:00 AM</option>
      <option value="12:00:00">12:00 PM</option>
      <option value="13:00:00">01:00 PM</option>
      <option value="14:00:00">02:00 PM</option>
      <option value="15:00:00">03:00 PM</option>
      <option value="16:00:00">04:00 PM</option>
      <option value="17:00:00">05:00 PM</option>
      <option value="18:00:00">06:00 PM</option>
      <option value="19:00:00">07:00 PM</option>
      <option value="20:00:00">08:00 PM</option>
      <option value="21:00:00">09:00 PM</option>
    </select>
</div>
</div>
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">Amount <a id="addAmount1" class="text-success float-right"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
    <input type="text" class="form-control no_cn_req" id="amount" name="amount" required />
  </div>
</div>
<div class="col-lg-2 mb-1" id="addAmountDiv1" style="display: none;">
<div class="form-check-label">Amount <a id="addAmount2" class="text-danger float-right"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
    <input type="text" class="form-control" name="amount2" />
  </div>
</div>
<div class="col-lg-2 mb-1" id="addAmountDiv2" style="display: none;">
<div class="form-check-label">Amount <a id="addAmount3" class="text-danger float-right"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
    <input type="text" class="form-control" name="amount3" />
  </div>
</div>
<script>
$('#addAmount1').on('click', function(e) {
if( $('#addAmountDiv1'). css("display") == "block" ){
  $('#addAmountDiv2').css("display", "block");
}
  $('#addAmountDiv1').css("display", "block");

  if( $('#addAmountDiv1'). css("display") == "block" && $('#addAmountDiv2'). css("display") == "block" ){          
    $('#addAmount1').css("display", "none");
  }else{$('#addAmount1').css("display", "block");}
});
$('#addAmount2').on('click', function(e) {
  $('#addAmountDiv1').css("display", "none");
  if( $('#addAmountDiv1'). css("display") == "block" && $('#addAmountDiv2'). css("display") == "block" ){          
    $('#addAmount1').css("display", "none");
  }else{$('#addAmount1').css("display", "block");}
});
$('#addAmount3').on('click', function(e) {
  $('#addAmountDiv2').css("display", "none");
  if( $('#addAmountDiv1'). css("display") == "block" && $('#addAmountDiv2'). css("display") == "block" ){          
    $('#addAmount1').css("display", "none");
  }else{$('#addAmount1').css("display", "block");}
});
</script>

<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">Balance Amount
    <input type="text" class="form-control" id="balance_amount" name="balance_amount" />
  </div>
</div>
<input type="hidden" name="payment_mode" value="{{ $deal->payment_mode }}" />
<input type="hidden" name="payment_mode_sec" value="{{ $deal->payment_mode_sec }}" />



@if($deal->payment_mode==1) {{--  Cash  --}}    
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">Date <a id="addCashDate1" class="text-success float-right"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
  <input type="date" class="form-control no_cn_req" name="cash_date" />
</div>
</div>
<div class="col-lg-3 mb-1" id="addCashDateDiv1" style="display: none;">
<div class="form-check-label">Date No <a id="addCashDate2" class="text-danger float-right"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
  <input type="date" class="form-control" name="cash_date2" />
</div>
</div>
<div class="col-lg-3 mb-1" id="addCashDateDiv2" style="display: none;">
<div class="form-check-label">Date No <a id="addCashDate3" class="text-danger float-right"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
  <input type="date" class="form-control " name="cash_date3" />
</div>
</div>
<script>
$('#addCashDate1').on('click', function(e) {
if( $('#addCashDateDiv1'). css("display") == "block" ){
  $('#addCashDateDiv2').css("display", "block");
}
  $('#addCashDateDiv1').css("display", "block");

  if( $('#addCashDateDiv1'). css("display") == "block" && $('#addCashDateDiv2'). css("display") == "block" ){          
    $('#addCashDate1').css("display", "none");
  }else{$('#addCashDate1').css("display", "block");}
});
$('#addCashDate2').on('click', function(e) {
  $('#addCashDateDiv1').css("display", "none");
  if( $('#addCashDateDiv1'). css("display") == "block" && $('#addCashDateDiv2'). css("display") == "block" ){          
    $('#addCashDate1').css("display", "none");
  }else{$('#addCashDate1').css("display", "block");}
});
$('#addCashDate3').on('click', function(e) {
  $('#addCashDateDiv2').css("display", "none");
  if( $('#addCashDateDiv1'). css("display") == "block" && $('#addCashDateDiv2'). css("display") == "block" ){          
    $('#addCashDate1').css("display", "none");
  }else{$('#addCashDate1').css("display", "block");}
});
</script>

<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">1000 x
  <input type="number" class="form-control" id="thousand" name="thousand" />
</div>
</div>
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">500 x
  <input type="number" class="form-control" id="fivehundred" name="fivehundred" />
</div>
</div>
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">100 x
  <input type="number" class="form-control" id="hundred" name="hundred" />
</div>
</div>
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">50 x
  <input type="number" class="form-control" id="fifty" name="fifty" />
</div>
</div>
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">20 x
  <input type="number" class="form-control" id="twenty" name="twenty" />
</div>
</div>
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">10 x
  <input type="number" class="form-control" id="ten" name="ten" />
</div>
</div>
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">5 x
  <input type="number" class="form-control" id="five" name="five" />
</div>
</div>
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">1 x
  <input type="number" class="form-control" id="one" name="one" />
</div>
</div>
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">50 x
  <input type="number" class="form-control" id="fiftyp" name="fiftyp" />
</div>
</div>
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">25 x
  <input type="number" class="form-control" id="twentyfivep" name="twentyfivep" />
</div>
</div>
@elseif($deal->payment_mode==2) {{--  Cheque  --}}
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">Cheque No <a id="addChequeNo1" class="text-success float-right"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
  <input type="text" class="form-control" id="cheque_no" name="cheque_no" />
</div>
</div>
<div class="col-lg-3 mb-1" id="addChequeNoDiv1" style="display: none;">
<div class="form-check-label">Cheque No <a id="addChequeNo2" class="text-danger float-right"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
  <input type="text" class="form-control" name="cheque_no2" />
</div>
</div>
<div class="col-lg-3 mb-1" id="addChequeNoDiv2" style="display: none;">
<div class="form-check-label">Cheque No <a id="addChequeNo3" class="text-danger float-right"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
  <input type="text" class="form-control" name="cheque_no3" />
</div>
</div>
<script>
$('#addChequeNo1').on('click', function(e) {
if( $('#addChequeNoDiv1'). css("display") == "block" ){
  $('#addChequeNoDiv2').css("display", "block");
}
  $('#addChequeNoDiv1').css("display", "block");
  
  if( $('#addChequeNoDiv1'). css("display") == "block" && $('#addChequeNoDiv2'). css("display") == "block" ){          
    $('#addChequeNo1').css("display", "none");
  }else{$('#addChequeNo1').css("display", "block");}
});
$('#addChequeNo2').on('click', function(e) {
  $('#addChequeNoDiv1').css("display", "none");
  if( $('#addChequeNoDiv1'). css("display") == "block" && $('#addChequeNoDiv2'). css("display") == "block" ){          
    $('#addChequeNo1').css("display", "none");
  }else{$('#addChequeNo1').css("display", "block");}
});
$('#addChequeNo3').on('click', function(e) {
  $('#addChequeNoDiv2').css("display", "none");
  if( $('#addChequeNoDiv1'). css("display") == "block" && $('#addChequeNoDiv2'). css("display") == "block" ){          
    $('#addChequeNo1').css("display", "none");
  }else{$('#addChequeNo1').css("display", "block");}
});
</script>
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">Cheque Date <a id="addChequeDate1" class="text-success float-right"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
    <input type="date" class="form-control no_cn_req" id="cheque_date" name="cheque_date" />
  </div>
</div>
<div class="col-lg-3 mb-1" id="addChequeDateDiv1" style="display: none;">
<div class="form-check-label">Cheque Date <a id="addChequeDate2" class="text-danger float-right"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
    <input type="date" class="form-control" name="cheque_date2" />
  </div>
</div>
<div class="col-lg-3 mb-1" id="addChequeDateDiv2" style="display: none;">
<div class="form-check-label">Cheque Date <a id="addChequeDate3" class="text-danger float-right"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
    <input type="date" class="form-control" name="cheque_date3" />
  </div>
</div>
<script>
$('#addChequeDate1').on('click', function(e) {
if( $('#addChequeDateDiv1'). css("display") == "block" ){
  $('#addChequeDateDiv2').css("display", "block");
}
  $('#addChequeDateDiv1').css("display", "block");

  if( $('#addChequeDateDiv1'). css("display") == "block" && $('#addChequeDateDiv2'). css("display") == "block" ){          
    $('#addChequeDate1').css("display", "none");
  }else{$('#addChequeDate1').css("display", "block");}
});
$('#addChequeDate2').on('click', function(e) {
  $('#addChequeDateDiv1').css("display", "none");
  if( $('#addChequeDateDiv1'). css("display") == "block" && $('#addChequeDateDiv2'). css("display") == "block" ){          
    $('#addChequeDate1').css("display", "none");
  }else{$('#addChequeDate1').css("display", "block");}
});
$('#addChequeDate3').on('click', function(e) {
  $('#addChequeDateDiv2').css("display", "none");
  if( $('#addChequeDateDiv1'). css("display") == "block" && $('#addChequeDateDiv2'). css("display") == "block" ){          
    $('#addChequeDate1').css("display", "none");
  }else{$('#addChequeDate1').css("display", "block");}
});
</script>

<div class="col-lg-3 mb-1 no_cn_div">Cheque Copy
<div class="form-check-label">
<div class="form-group files">
  <input type="file" class="form-control" id="cheque_copy" multiple="multiple" name="cheque_copy[]" ></div>
</div>
</div>
@elseif($deal->payment_mode==3) {{--  Bank Transfer  --}}
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">Bank Name
  <input type="text" class="form-control" id="bank_name" name="bank_name" />
</div>
</div>
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">Deposit Date
  <input type="date" class="form-control no_cn_req"  id="deposit_date" name="deposit_date" />
</div>
</div>
<div class="col-lg-3 mb-1">
<div class="form-check-label">Deposit Date 2
<input type="date" class="form-control"  id="deposit_date2" name="deposit_date2"/>
</div>
</div>
@elseif($deal->payment_mode==4) {{--  Open Credit  --}}
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">Open Credit Date
  <input type="date" class="form-control no_cn_req"  id="open_credit_date" name="open_credit_date" />
</div>
</div>
@elseif($deal->payment_mode==5) {{--  Credit Card  --}}
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">Card Type
  <input type="text" class="form-control" id="credit_card_type" name="credit_card_type" />
</div>
</div>
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">Payment Date
  <input type="date" class="form-control primary-input no_cn_req"  id="payment_date" name="payment_date" />
</div>
</div>
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">Deposit Date
<input type="date" class="form-control primary-input no_cn_req"  id="credit_card_deposit_date" name="credit_card_deposit_date" />
</div>
</div>
@elseif($deal->payment_mode==6) {{--  Bank TT  --}}
<div class="col-lg-2 mb-1 no_cn_div">
<div class="form-check-label">BankTT Date <a id="addBankTTDate1" class="text-success float-right"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
  <input type="date" class="form-control primary-input no_cn_req" id="banktt_date" name="banktt_date" />
</div>
</div>
<div class="col-lg-3 mb-1" id="addBankTTDateDiv1" style="display: none;">
<div class="form-check-label">BankTT Date <a id="addBankTTDate2" class="text-danger float-right"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
<input type="date" class="form-control primary-input " name="banktt_date2" />
</div>
</div>
<div class="col-lg-3 mb-1" id="addBankTTDateDiv2" style="display: none;">
<div class="form-check-label">BankTT Date <a id="addBankTTDate3" class="text-danger float-right"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
<input type="date" class="form-control primary-input " name="banktt_date3" />
</div>
</div>
<script>
$('#addBankTTDate1').on('click', function(e) {
if( $('#addBankTTDateDiv1'). css("display") == "block" ){
$('#addBankTTDateDiv2').css("display", "block");
}
$('#addBankTTDateDiv1').css("display", "block");

if( $('#addBankTTDateDiv1'). css("display") == "block" && $('#addBankTTDateDiv2'). css("display") == "block" ){          
$('#addBankTTDate1').css("display", "none");
}else{$('#addBankTTDate1').css("display", "block");}
});
$('#addBankTTDate2').on('click', function(e) {
$('#addBankTTDateDiv1').css("display", "none");
if( $('#addBankTTDateDiv1'). css("display") == "block" && $('#addBankTTDateDiv2'). css("display") == "block" ){          
$('#addBankTTDate1').css("display", "none");
}else{$('#addBankTTDate1').css("display", "block");}
});
$('#addBankTTDate3').on('click', function(e) {
$('#addBankTTDateDiv2').css("display", "none");
if( $('#addBankTTDateDiv1'). css("display") == "block" && $('#addBankTTDateDiv2'). css("display") == "block" ){          
$('#addBankTTDate1').css("display", "none");
}else{$('#addBankTTDate1').css("display", "block");}
});
</script>

<div class="col-lg-3 mb-1 no_cn_div">BankTT Copy
<div class="form-check-label">
<div class="form-group files">
<input type="file" class="form-control" id="banktt_copy" multiple="multiple" name="banktt_copy[]" ></div>
</div>
</div>
@endif


</div>
<div class="row">
<div class="col-lg-6 mb-1">
<div class="no-gutters input-right-icon">
    <div class="col">
        <div class="input-effect">
            <label class="txtlbl">@lang('Remarks')<span></span></label>
            <textarea class="form-control" rows="4" id="remarks"  name="remarks"></textarea>
        </div>
    </div>
</div>
</div>
<div class="col-lg-3 pt-2">
<input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
<input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
<button type="submit" class="btn btn-primary mt-5" id="btnSubmit">
        @lang('Submit')
</button>


</div>


</div>
{{ Form::close() }}
</div>
@endif
@endif
{{--  crm-deal-track-approval-receivables  --}}