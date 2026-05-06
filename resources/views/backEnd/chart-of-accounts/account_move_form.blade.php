<?php
$com_id = session('logged_session_data.company_id');
$account_list = @App\SysChartofAccounts::whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->where('account_code', 'like', 'ACC%')->where('main_account_id', '=', 0)->get();
?>

<div class="modal modal-draggable side-panel fade" id="ModalMoveAccount" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="staticBackdropLabel">Move Account</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {!! Form::open([
                    'class' => 'form-horizontal',
                    'files' => true,
                    'url' => 'account-move',
                    'method' => 'post',
                    'id' => 'account-move',
                ]) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-2">Account
                            <select id="from_account" name="from_account" class="form-control js-example-basic-single"
                                 required>
                                @foreach ($account_list as $data)
                                    <option value="{{ $data->id }}">{{ $data->account_code }} -
                                        {{ $data->account_name }}</option>
                                @endforeach
                            </select>
                        </div>

                         <div class="col-md-12 mb-2">Move To
                            <select id="move_to" name="move_to" class="form-control js-example-basic-single"
                                 required>
                             
                                    <option value="other_account">Sub Account</option>
                                    <option value="other_subgroup">Sub Group</option>
                               
                            </select>
                        </div>

                        <div id="subAccountDiv" class="col-md-12 mb-2 d-none">Account
                            <select id="sub_account" name="sub_account" class="form-control js-example-basic-single"
                                 required>
                             
                                    @foreach ($account_list as $data)
                                    <option value="{{ $data->id }}">{{ $data->account_code }} -
                                        {{ $data->account_name }}</option>
                                @endforeach
                               
                            </select>
                        </div>
                        @php
                              $accountgroupsub = @App\SysAccountGroupSub::where('status', 1)->orderBy('group_id')->get();
                        @endphp

                           <div id="groupAccountDiv" class="col-md-12 mb-2 d-none">Group
                            <select id="group_account" name="group_account" class="form-control js-example-basic-single"
                                 required>
                             
                                    @foreach ($accountgroupsub as $data)
                                    <option value="{{ $data->id }}">{{ $data->title }}</option>
                                @endforeach
                               
                            </select>
                        </div>


                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button  class="btn btn-light add-btn ms-2" type="submit"
                        >
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Move
                    </button>
                </div>
              <script>
    $(document).ready(function() {

        $('#move_to').change(function() {
            var val = $(this).val();

            // Hide both first
            $('#subAccountDiv').addClass('d-none');
            $('#groupAccountDiv').addClass('d-none');

            $('#sub_account').prop('required', false);
            $('#group_account').prop('required', false);

            // Show based on selection
            if (val === 'other_account') {
                $('#subAccountDiv').removeClass('d-none');
                $('#sub_account').prop('required', true);
            }

            if (val === 'other_subgroup') {
                $('#groupAccountDiv').removeClass('d-none');
                $('#group_account').prop('required', true);
            }
        });

        // Initialize on load
        $('#move_to').trigger('change');
    });
</script>
                {!! Form::close() !!}

            </div>
        </div>
    </div>