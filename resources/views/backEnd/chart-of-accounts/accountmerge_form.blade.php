<?php
$com_id = session('logged_session_data.company_id');
$account_list = @App\SysChartofAccounts::whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->where('account_code', 'like', 'ACC%')->where('main_account_id', '=', 0)->get();
?>

<div class="modal modal-draggable side-panel fade" id="ModalMergeAccount" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="staticBackdropLabel">Merge Account</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {!! Form::open([
                    'class' => 'form-horizontal',
                    'files' => true,
                    'url' => 'account-merge',
                    'method' => 'post',
                    'id' => 'account-merge',
                ]) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">From Account
                            <select id="from_account" name="from_account[]" class="form-control js-example-basic-single"
                                multiple required>
                                @foreach ($account_list as $data)
                                    <option value="{{ $data->id }}">{{ $data->account_code }} -
                                        {{ $data->account_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">To Account
                            <select id="to_account" name="to_account" class="form-control js-example-basic-single"
                                required>
                                <option value="">Select</option>
                                @foreach ($account_list as $data)
                                    <option value="{{ $data->id }}">{{ $data->account_code }} -
                                        {{ $data->account_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light add-btn ms-2" type="submit"
                        onclick="return confirm('Are you sure you want to Merge this?');">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Merge
                    </button>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>