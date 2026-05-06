@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">On Process Report by User</h2>
            <span class="page-label">Home - On Process Report by User</span>
        </div>
        
    </div>
    <div style="display: none;" >
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
                            <label for="" class="form-check-label">Form Date</label>
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
                                            <th>@lang('Deal ID')</th>
                                            <th>@lang('Company Name')</th>
                                            <th>@lang('Deal Stage')</th>
                                            <th class="text-right">@lang('Value')</th>
                                            <th class="text-right">@lang('GP')</th>
                                            <th class="text-right">@lang('GP%')</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $deal_value_2=0; $deal_value_3=0; $gp=0; ?>
                                        @foreach($deals as $value)
                                <tr>
                                    {{--  href="{{url('crm-leads/'.$value->id.'/view')}}"  --}}
                                    <td><a href="{{url('crm-deals/'.$value->id.'/view')}}" target="_blank">{{@$value->code}}</a></td>
                                    <td><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->customername->name}}</div></td>
                                    <td>
                                        @if ($value->stage==2)
                                        <span class="text-success mb-2">Quote</span>
                                        @elseif ($value->stage==3)
                                        <span class="text-primary mb-2">Closure</span>
                                        @elseif ($value->stage==1)
                                        <span class="text-primary mb-2">Prospecting</span>
                                        @else
                                        <span class="text-primary mb-2">Won</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        
                                        @php
                                        $aed=@App\SysHelper::get_aed_amount_new($value->deal_currency,$value->deal_value);
                                        $deal_profit=@App\SysHelper::get_aed_amount_new($value->deal_currency,$value->deal_profit);
                                        @endphp
                                        {{ @App\SysHelper::com_curr_format($aed, 2, '.', ',') }} <?php $deal_value_2 += $aed; ?>
                                                                          
                                    </td>
                                    <td class="text-right">{{ @App\SysHelper::com_curr_format($deal_profit, 2, '.', ',') }}<?php $gp += $deal_profit; ?></td>
                                    <td class="text-right">@if($aed !=0 ){{ round($deal_profit/$aed*100,2) }}% @else 0% @endif</td>
                                    <td class="text-right">
                                        <a class="btn-sm btn-info" href="{{url('crm-deals/'.$value->id.'/view')}}" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                                  
                                @endforeach
                                    </tbody>
                                    <tfoot>
                                        <th></th>
                                        <th></th>
                                        <th class="text-right">Total : </th>
                                        <th class="text-right">{{ @App\SysHelper::com_curr_format($deal_value_2, 2, '.', ',') }}</th>
                                        <th class="text-right">{{ @App\SysHelper::com_curr_format($gp, 2, '.', ',') }}</th>
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