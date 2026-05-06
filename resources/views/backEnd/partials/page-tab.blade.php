<style>
    .app_close_btn{
        color: #ffffff;
        background: #8132fc;
        padding: 5px 10px;
        border-radius: 25px;
        cursor: pointer;
    }
</style>

    


<input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
<input type="hidden" name="addtotab_pagename" id="addtotab_pagename" value="{{ Request::segment(1) }}">

@php
    $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?  "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
@endphp
<input id="addtotab_url" type="hidden" value="{{$link}}">
@if(Request::segment(1) !='admin-dashboard')
<div class="app_close_btn" style="display: none;">
        <span id="btn_addtotab" class="ti-close"></span>
        <input id="addtotab_url" type="hidden">
</div>
@endif