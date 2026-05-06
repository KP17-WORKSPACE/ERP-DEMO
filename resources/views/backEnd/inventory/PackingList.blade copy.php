@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Packing List</h2>
            <span class="page-label">Home - Packing List</span>
        </div>
        <div>
            <a href="{{ url('packing-list') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
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
                                            <th width="200px">Account</th>
                                            <th width="150px">Date</th>
                                            <th width="150px">Doc Number</th>
                                            <th width="150px">refno</th>
                                            <th width="150px">refdate</th>
                                            <th>Remarks</th>
                                            <th width="150px">Created By</th>
                                            <th width="120px"></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        
                                        @foreach($data as $value)
                                <tr @if($value->status==2) class="bg-dark" @endif>
                                    <td>{{@$value->account->account_name}}</td>
                                    <td>{{date('d/m/Y', strtotime(@$value->date))}}</td>
                                    <td>{{@$value->doc_number}}</td>
                                    <td>{{@$value->refno}}</td>
                                    <td>{{date('d/m/Y', strtotime(@$value->refdate))}}</td>
                                    <td>{{@$value->remarks}}</td>
                                    <td>{{@$value->createdby->full_name}}</td>
                                    <td>
                                        <a class="btn-sm btn-warning" href="{{url('packing-list/'.$value->id.'/download')}}" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>
                                        <a class="btn-sm btn-info" href="{{url('packing-list/'.$value->id.'/view')}}" class="btn-small"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        <a class="btn-sm btn-primary" href="{{url('packing-list/'.$value->id.'/edit')}}" class="btn-small"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                                  
                                @endforeach

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