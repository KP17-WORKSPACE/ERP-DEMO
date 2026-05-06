@extends('backEnd.newmasterpage')

@section('mainContent')
@php
$isEdit = true;
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

@include('backEnd.employee.leaves.form', [
    'authUser' => $authUser,
    'leave' => $leave,
    'reportingManager' => $reportingManager,
    'leaveTypes' => $leaveTypes,
])
@endsection
