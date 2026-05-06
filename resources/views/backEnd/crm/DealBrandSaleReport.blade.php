@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Sales Report By Brand</h2>
            <span class="page-label">Home - Sales Report By Brand</span>
        </div>
    </div>

    <div>
        <div class="card shadow mb-4 p-4">            
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-brand-sale-report', 'method' => 'POST', 'id' => 'crm-brand-sale-report']) }}
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Brand</label>
                    <select class="form-control js-example-basic-single" name="brand_id" id="brand_id">
                        <option value="">-Select-</option>
                        @foreach ($brand as $value)
                        <option value="{{ @$value->id }}" @if($ctrl_brand ==$value->id) selected @endif>{{ @$value->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Part Number</label>
                    <input class="form-control" id="part_number" type="text" autocomplete="off" name="part_number" value="{{ $ctrl_part_number }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">From Date</label>
                    <input class="form-control datepicker" id="date" type="date" autocomplete="off" name="date" value="{{ $ctrl_date }}" required>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">To Date</label>
                    <input class="form-control" id="date2" type="date" autocomplete="off" name="date2" value="{{ $ctrl_date2 }}" required>
                </div>

                <div class="col-1 mb-2"><br />
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
                                            <th>@lang('Part Number')</th>
                                            <th>@lang('Qty')</th>
                                            <th class="text-right">@lang('Amount')</th>
                                            <th class="text-right">@lang('Deal ID')</th>
                                            <th>@lang('Description')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $sum = 0; ?>
                                        @if(count($data)>0)
                                        @foreach($data as $value)
                                <tr>
                                    <td class="text-dark">{{@$value['pno']}}</td>
                                    <td class="text-dark">{{@$value['qty']}}</td>
                                    <td class="text-dark text-right">{{@$value['amount'] }}
                                       <?php $sum += $value['amount']; ?>
                                    </td>
                                    <td class="text-dark text-right"><a target="_blank" href="{{url('crm-deals/'.$value['deal_id'].'/view')}}" class="btn btn-info btn-xs pt-0 pb-0 pl-2 pr-2">
                                        {{@$value['deal_id']}}</a>
                                    </td>
                                    <td class="text-dark">{{  @$value['description'] }}}</td>
                                    <td class="text-right">
                                        
                                    </td>
                                </tr>
                                  
                                @endforeach
                                @endif
                                    </tbody>
                                    <tfoot>
                                        <th></th>
                                        <th class="text-right"></th>
                                        <th class="text-right">{{ @App\SysHelper::com_curr_format($sum, 2, '.', '') }}</th>
                                        <th class="text-right"></th>
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