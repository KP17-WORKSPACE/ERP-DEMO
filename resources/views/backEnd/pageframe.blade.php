@extends('backEnd.master')

@section('mainContent')

<style>
.nav-tabs li{background: #2954a2;padding: 0px 10px;border-radius: 5px 5px 0px 0px; margin: 0 2px 0 0;}
.nav-tabs li:active{background: #000000;}
.nav-tabs li a{color: #000000;}
.show{display: inline; color:#ffffff !important;}
</style>

<div role="tabpanel">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab">Tab 1</a> <a href="#">X</a></li>
        <li role="presentation"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">Tab 2</a> <a href="#">X</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="tab1"></div>
        <div role="tabpanel" class="tab-pane" id="tab2"></div>
    </div>
</div>




@endsection

@section('script')

<script>

$.ajax({ url: 'http://localhost:81/venus-erp/chartofaccounts-add', success: function(data) { $("#tab1").html(data); } });
$.ajax({ url: 'http://localhost:81/venus-erp/purchase-order', success: function(data) { $("#tab2").html(data); } });

function tests()
{alert("hello");}

//$("#res").html(iframeContent.find('main-content').html);
// $.get('/jquery/getjsondata', {name:'Steve'}, function (data, textStatus, jqXHR) {
//     $('p').append(data.firstName);
// });


</script>

@endsection
