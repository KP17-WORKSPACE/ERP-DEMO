@extends('backEnd.masterpage')
@section('mainContent')

@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Import Shortage Stock (Stock Out)</h2>
            <span class="page-label">Home - Import Shortage Stock (Stock Out)</span>
        </div>
        <div>
            <a href="{{ url('stock-out/show') }}" class="btn btn-primary"><i class="fa fa-plus"></i> List</a>
            <a href="{{ url('stock-out-import') }}" class="btn btn-info"><i class="fa fa-plus"></i> Import</a>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    {{ Form::open(['class' => 'form-horizontal','url' => 'stock-out-import-list', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    <div class="boxed-formctrl">
                        <div class="add-visitor">
                            <div class="row mb-10">
                                <div class="col-lg-12">
                                    @if (session()->has('message-success'))
                                        <div class="alert alert-success mb-20">
                                            {{ session()->get('message-success') }}
                                        </div>
                                    @elseif(session()->has('message-danger'))
                                        <div class="alert alert-danger">
                                            {{ session()->get('message-danger') }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">Choose File<span>*.csv</span> (<a href="{{ url('public/uploads/product_upload/stock_import_sample_file.csv') }}" target="_blank">Sample File</a>)</label>
                                        <input class="form-control" type="file" accept=".csv" name="import_file" required>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-effect">
                                        <label class="txtlbl"><br />
                                        <button class="btn btn-primary mt-2">
                                            <span class="ti-check"></span> Submit
                                        </button>
                                        @if (count($data)>0)
                                        <a href="{{ url('stock-out-import-clear') }}" class="btn btn-info mt-2">Clear Data</a> @endif
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>


                
                <div class="col-lg-12" style="overflow: scroll;">
                    <table class="table table-bordered table-striped" cellspacing="0">
                            <thead>
                                @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                                    <tr>
                                        <td colspan="11">
                                            @if (session()->has('message-success-delete'))
                                                <div class="alert alert-success">
                                                    {{ session()->get('message-success-delete') }}
                                                </div>
                                            @elseif(session()->has('message-danger-delete'))
                                                <div class="alert alert-danger">
                                                    {{ session()->get('message-danger-delete') }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th width="">part_number</th>
                                    <th width="">description</th>
                                    <th width="">qty</th>
                                    <th width="">unitprice</th>
                                    <th width="">value</th>
                                    <th width="">refid</th>
                                    <th width="">serialno</th>
                                    <th width="">narration</th>
                                    <th width="">remarks</th>
                                    <th width="">currancy</th>
                                    <th width="">date</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if (count($data)>0)
                                @php $check = ""; @endphp
                                    @foreach ($data as $value)
                                    @php
                                        $item = $items->where('part_number',$value->part_number)->max('id');
                                        $curr = $currancy->where('code',$value->currancy)->max('id');
                                    @endphp
                                        <tr>
                                            <td @if($item == "") @php $check = "error"; @endphp class="bg-danger text-white" @endif >{{ @$value->part_number }}</td>
                                            <td>{{ @$value->description }}</td>
                                            <td>{{ @$value->qty }}</td>
                                            <td>{{ @$value->unitprice }}</td>
                                            <td>{{ @$value->value }}</td>
                                            <td>{{ @$value->refid }}</td>
                                            <td>{{ @$value->serialno }}</td>
                                            <td>{{ @$value->narration }}</td>
                                            <td>{{ @$value->remarks }}</td>
                                            <td @if($curr == "") @php $check = "error"; @endphp class="bg-danger text-white" @endif >{{ @$value->currancy }}</td>
                                            <td>{{ @$value->date }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            <?php try{ ?>
                            <footer>
                                <tr>
                                    <td colspan="11">
                                        
                                    </td>
                                </tr>
                            </footer>
                            <?php }catch (\Exception $e) { } ?>
                        </table>
                </div>
                @if (count($data)>0 && $check=="")
                <div class="col-lg-12 text-center">
                    {{ Form::open(['class' => 'form-horizontal','url' => 'stock-out-import-data', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @if (session()->has('message-success'))
                            <div class="alert alert-success mb-20">
                                {{ session()->get('message-success') }}
                            </div>
                        @elseif(session()->has('message-danger'))
                            <div class="alert alert-danger">
                                {{ session()->get('message-danger') }}
                            </div>
                        @endif
                            <button class="btn btn-danger mt-2">
                                <span class="ti-check"></span> Import Data
                            </button>
                    </div>
                    {{ Form::close() }}
                </div>
                @endif

            </div>
        </div>
    </div>    

</div>
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


@endsection

@section('script')
    <script>

$(document).ready(function()
    {
        // Stop user to press enter in textbox
        $("input:text").keypress(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
});

    </script>
@endsection
