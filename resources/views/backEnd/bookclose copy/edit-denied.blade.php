@extends('backEnd.masterpage')
@section('mainContent')
@php
	$module_links = [];
	$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<div class="container-fluid">
	<div class="d-sm-flex justify-content-between">
		<div class="mb-3">
			<h2 class="page-heading m-0">Action denied</h2>
			<span class="page-label">Home - Action denied</span>
		</div>
		<div>
			<a href="{{ url()->previous() }}" type="button" class="btn btn-primary">Back</a>
		</div>
	</div>
	<div class="card p-4 mb-2">
        <div class="row">
			<div class="col-lg-12">
                
                <div class="white-box">
                	<div class="col-lg-12 text-center"> 
<br /><br /><br />
<h2>Action denied.</h2><br />
<h5>This transaction falls within a closed accounting period.</h5><br />
<h5>Please contact the Finance Department.</h5><br />
<a href="{{ url()->previous() }}" type="button" class="btn btn-primary">Back</a>
<br /><br /><br />
                </div>
            </div>
		</div>
	</div>
</div>

@endsection
