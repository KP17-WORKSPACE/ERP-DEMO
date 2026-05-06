@extends('backEnd.newmasterpage')
@section('mainContent')

<?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<style>

    .search-container {
      display: flex;
      align-items: center;
      width: 100%;
      padding: 10px 20px;
      border: 1px solid #dfe1e5;
      border-radius: 24px;
      box-shadow: none;
      transition: box-shadow 0.3s ease;
      background: #ffffff;
    }

    .search-container:hover,
    .search-container:focus-within {
      box-shadow: 0 1px 6px rgba(32, 33, 36, 0.28);
    }

    .search-input {
      flex: 1;
      border: none;
      outline: none;
      font-size: 16px;
    }

    .search-icon {
      width: 20px;
      height: 20px;
      margin-right: 10px;
      opacity: 0.5;
    }
  </style>

<div class="container-fluid">
    <div class=" p-4 mb-2">
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'customer-search', 'method' => 'POST', 'id' => 'customer-search']) }}
            <div class="row">
                <div class="col-md-12 mb-2 text-center"><h3>Customer Search</h3></div>
                <div class="col-md-12 mb-4 text-center font-weight-bold">Customer Name / Code</div>
                <div class="col-md-2 mb-2"></div>
                <div class="col-md-8 mb-2 text-center">
                    <div class="search-container">
                        <img class="search-icon" src="https://www.svgrepo.com/show/7109/search.svg" alt="search">
                        <input class="search-input" type="text" autocomplete="off" id="cust_name" name="cust_name" value="{{ $cust_name }}" onkeyup="getData(1)" placeholder="Search Customer Name / Code">
                        <button type="submit" hidden class="btn btn-primary" id="btnSubmit"><i class="fa fa-search" aria-hidden="true"></i></button>
                      </div>
                </div>
                <div class="col-md-2 mb-2"></div>
            </div>
        {{ Form::close() }}
    </div>
    
    <div class="row">
        <div class="col-md-2 mb-2"></div>
        <div class="col-md-8 mb-2">
            <div class="card shadow mb-4">
                <div class="card-body" style="padding: 0; border: solid 1px #d1d3e2;">
                    <div class="pdata" id="pdivdata">
                        @include('backEnd.cust-suppl.CustomerSearchPage')
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-2"></div>
    </div>

<script>
function getData(page) {
    var cust_name = $('#cust_name').val();
    if (cust_name.length < 5) {
        $('.pdata').html('');
        return;
    }
    $("#loading_bg").css("display", "block");
    $.ajax({
        url: '?page=' + page + "&cust_name=" + cust_name,
        dataType: "html",
    }).done(function (data) {
        var $html = $('<div>').html(data);
        var $pdivdata = $html.find('#pdivdata');

        if ($pdivdata.length) {
            $('.pdata').html($pdivdata.html());
        } else {
            $('.pdata').html('<p>No data found.</p>');
        }

        $("html, body").animate({ scrollTop: 0 }, 800);
    }).fail(function () {
        alert('No response from server');
    });
    $("#loading_bg").css("display", "none");
}
</script>

   


@endsection