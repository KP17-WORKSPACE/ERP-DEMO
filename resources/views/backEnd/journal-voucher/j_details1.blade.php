<?php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<?php try { ?>

<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">
         {{ $editData->doc_number }}
    </h4>
    <div class="purchase-order-content-header-right">

        <a class="btn btn-light text-dark" href="{{ url('journalvoucher/' . $editData->id . '/edit') }}">
            <i class="ico icon-outline-add-square text-success"></i> Edit
        </a>

        <a class="btn btn-light text-dark" href="{{ url('journalvoucher-add') }}">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </a>

        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">



            </ul>
        </div>


    </div>
</div>

<div class="card mb-3">
    <div class="card-body">

        <div class="row">
            <div class="col-1-5 mb-3">
                <label class="form-label">Doc Date:</label>
                <div class="form-control-plaintext"> {{ Carbon\Carbon::parse($editData->doc_date)->format('d/m/Y') }}
                </div>
            </div>
            <div class="col-1-5 mb-3">
                <label class="form-label">Amount:</label>
                <div class="form-control-plaintext">
                    {{ @App\SysHelper::com_curr_format(@$editData->credit_amount, '', '', ',') }}</div>
            </div>
            <div class="col-1-5 mb-3">
                <label class="form-label">Currency:</label>
                <div class="form-control-plaintext">{{ @$editData->currency_name->code }}</div>
            </div>

            <div class="col-2 mb-3">
                <label class="form-label">Created By:</label>
                <div class="form-control-plaintext"> {{ @$editData->createdby->full_name }}</div>
            </div>

            <div class="col-5 mb-3">
                <label class="form-label">Narration:</label>
                <div class="form-control-plaintext">
                    {{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}
                </div>
            </div>

        </div>
    </div>
</div>

<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="dealTrackTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="deal-info-tab" data-bs-toggle="tab" data-bs-target="#deal-info"
                type="button" role="tab" aria-controls="deal-info" aria-selected="true">Account</button>
        </li>

    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">
        <div class="tab-pane fade show active" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
            <!-- <h4 class="mb-3 color-sub-head">Shipping Address</h4> -->
            <div class="row">

                <div class="table-responsive">
                    <table class="table table-hover" id="long-list" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width:20px;">@lang('#')</th>
                                <th style="width:250px;">@lang('Account Name')</th>
                                <th class="text-end" style="width:100px;">@lang('Debit')</th>
                                <th class="text-end" style="width:100px;">@lang('Credit')</th>
                                <th style="width:200px;">@lang('Narration')</th>
                                <th class="text-center" style="width:100px;">@lang('Deal Id')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $setroid = 8;
                            if (isset($editDataList)) {
                                if (count($editDataList) > 0) {
                                    $setroid = count($editDataList) + 1;
                                }
                            }
                            ?>
                            @for ($roid = 1; $roid < $setroid; $roid++)
                                <tr>
                                    <td>{{ $roid }}</td>
                                    <td>
                                        {{ @$editDataList[$roid - 1]->accounts->account_code }} -
                                        {{ @$editDataList[$roid - 1]->accounts->account_name }}
                                    </td>
                                    <td  class="text-end">
                                        {{ @App\SysHelper::com_curr_format(@$editDataList[$roid - 1]->debit_amount, 2, '.', '') }}
                                    </td>
                                    <td  class="text-end">
                                        {{ @App\SysHelper::com_curr_format(@$editDataList[$roid - 1]->credit_amount, 2, '.', '') }}
                                    </td>
                                    <td>{{ @$editDataList[$roid - 1]->remarks }}</td>
                                    <td  class="text-center">{{ @$editDataList[$roid - 1]->dealid }}</td>
                                </tr>
                            @endfor


                            {{-- <tr>
                                            <td><input type="text" class="form-control" name="sort_id[]" value="{{ $roid }}"  readonly/></td>
                                            <td class="noborder">
                                                <select class="form-control" name="account_id[]" readonly>
                                                <option value=""></option>
                                            </select>
                                            </td> 
                                            <td>                                                                    
                                                <input class="form-control text-end" type="number" name="amount_dr[]" autocomplete="off" onchange="update_totals()" readonly>
                                            </td>
                                            <td>
                                                <input class="form-control text-end" type="number" name="amount_cr[]" autocomplete="off" onchange="update_totals()" readonly>
                                            </td>
                                            <td><input type="text" class="form-control" name="remarks[]" readonly></td>
                                            <td><input type="text" class="form-control" name="dealid[]" readonly></td>
                                        </tr> --}}
                        </tbody>
                        <tfoot>
                            <thead>
                                <th></th>
                                <th></th>
                                <th class="text-end"><b><label
                                            id="dr_total">{{ $editDataList->sum('debit_amount') }}</label></b></th>
                                <th class="text-end"><b><label
                                            id="cr_total">{{ $editDataList->sum('credit_amount') }}</label></b></th>
                                <th></th>
                                <th></th>
                            </thead>
                        </tfoot>
                    </table>
                </div>





            </div>


        </div>

    </div>
</div>




<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
