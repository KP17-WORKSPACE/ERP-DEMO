@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Lead Convertion Report</h2>
            <span class="page-label">Home - Lead Convertion Report</span>
        </div>
        
    </div>
    <div >
                    <div class="card shadow mb-4 p-4">
                        
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-lead-convertion-report', 'method' => 'POST', 'id' => 'crm-lead-convertion-report']) }}
                    <div class="row">
                        <div class="col-md-2 mb-2">
                            <label for="" class="form-check-label">Select Month</label>
                            <input class="form-control datepicker" id="date" type="month" autocomplete="off" name="date" value="{{ $ctrl_date }}" required>
                        </div>

                        <div class="col-2 mb-2"><br />
                            <button type="submit" class="btn btn-primary" id="btnSubmit">Filter</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                    </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        @if(session()->has('message-success') != "" || session()->get('message-danger') != "")
                        <tr>
                            <td colspan="7">
                                @if(session()->has('message-success'))
                                <div class="alert alert-success">
                                    {{ session()->get('message-success') }}
                                </div>
                                @elseif(session()->has('message-danger'))
                                <div class="alert alert-danger">
                                    {{ session()->get('message-danger') }}
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endif
                        
                        <tr>
                            <th class="p-1">Created By</th>
                            <th class="text-right">Total Leads</th>
                            <th class="text-right">Converted</th>
                            <th class="text-right">Quote</th>
                            <th class="text-right">Won</th>
                            <th class="text-right">Lost</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $deal_value=0; ?>
                        @if($data!='0')
                        @foreach($data as $value)
                <tr>
                    <td><a class="text-dark p-3" style="font-size: 14px;">{{@$value->createdby->full_name}}</a></td>
                    <td class="text-right text-danger" style="font-size: 14px;">{{ App\SysHelper::get_total_leads_by_user(@$value->created_by,$ctrl_date)}}</td>
                    <td class="text-right text-primary" style="font-size: 14px;">{{ App\SysHelper::get_total_leads_convert_by_user(@$value->created_by,$ctrl_date)}}</td>
                    <td class="text-right text-info" style="font-size: 14px;">{{ App\SysHelper::get_total_leads_convert_quote_by_user(@$value->created_by,$ctrl_date)}}</td>
                    <td class="text-right text-success" style="font-size: 14px;">{{ App\SysHelper::get_total_leads_convert_won_by_user(@$value->created_by,$ctrl_date)}}</td>
                    <td class="text-right text-warning" style="font-size: 14px;">{{ App\SysHelper::get_total_leads_convert_lost_by_user(@$value->created_by,$ctrl_date)}}</td>
                </tr>
                  
                @endforeach
                @endif
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>

                </div>
<?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>


<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection