@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Forecast Report</h2>
            <span class="page-label">Home - Forecast Report</span>
        </div>
        
    </div>
    <div >
                    <div class="card shadow mb-4 p-4">
                        
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals-forecast-report', 'method' => 'POST', 'id' => 'crm-deals-forecast-report']) }}
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label for="" class="form-check-label">Company</label>
                            <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                                <option value="">-Select-</option>
                                @foreach ($company as $value)
                                <option value="{{ @$value->id }}" @if($ctrl_company ==$value->id) selected @endif>{{ @$value->company_name }} - {{ @$value->city }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="" class="form-check-label">Owner</label>
                            <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                                <option value="">-Select-</option>
                                @foreach ($staff as $value)
                                <option value="{{ @$value->user_id }}" @if($ctrl_owner ==$value->user_id) selected @endif>{{ @$value->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="" class="form-check-label">From Date</label>
                            <input class="form-control datepicker" id="date" type="date" autocomplete="off" name="date" value="{{ $ctrl_date }}" required>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="" class="form-check-label">To Date</label>
                            <input class="form-control" id="date2" type="date" autocomplete="off" name="date2" value="{{ $ctrl_date2 }}" required>
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
                            <th>@lang('Name')</th>
                            <th class="text-right">@lang('Forecast')</th>
                            <th class="text-right">Detail</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $deal_value=0; ?>
                        @if($data!='0')
                        @foreach($data as $value)
                <tr>
                    <td><a class="text-dark">{{@$value["full_name"]}}</a></td>
                    <td class="text-right">
                        @if (@$value["forcast"] == "0.00")
                            <b>
                        @else
                            <b class="text-danger font-bold">
                        @endif
                            {{ @$value["forcast"] }}
                            </b>
                            <?php  if(@$value["user_id"]!=26 && @$value["user_id"]!=112) { $deal_value += @$value["forcast"]; } ?>
                    </td>
                    <td class="text-right"><a href="{{url('crm-deals-forecast-report-list/'.$value["user_id"].'/'.$ctrl_company.'/'.$ctrl_date.'/'.$ctrl_date2)}}" class="bg-info text-white p-2">View Detail</a></td>
                </tr>
                  
                @endforeach
                @endif
                    </tbody>
                    <tfoot>
                        <th></th>
                        <th class="text-right">{{ @App\SysHelper::com_curr_format($deal_value, 2, '.', ',') }}</th>
                        <th></th>
                    </tfoot>
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