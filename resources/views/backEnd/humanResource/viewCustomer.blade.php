@extends('backEnd.master')
@section('mainContent')

@php
function showPicName($data){
$name = explode('/', $data);
return $name[4];
}
function showJoiningLetter($data){
$name = explode('/', $data);
return $name[3];
}
function showResume($data){
$name = explode('/', $data);
return $name[3];
}
function showOtherDocument($data){
$name = explode('/', $data);
return $name[3];
}

@endphp

<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.customer') @lang('lang.profile')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="{{route('staff_directory')}}">@lang('lang.customer') @lang('lang.details')</a>
            </div>
        </div>
    </div>
</section>


<section class="mb-40 student-details">
    @if(session()->has('message-success'))
    <div class="alert alert-success">
        {{ session()->get('message-success') }}
    </div>
    @elseif(session()->has('message-danger'))
    <div class="alert alert-danger">
        {{ session()->get('message-danger') }}
    </div>
    @endif
    <div class="container-fluid p-0">
        <div class="row">
         <div class="col-lg-3">
            <!-- Start Student Meta Information -->
            <div class="main-title">
                <h3 class="mb-20">@lang('lang.customer') @lang('lang.details')</h3>
            </div>
            <div class="student-meta-box">
                <div class="student-meta-top"></div>

                @if (file_exists(@$custDetails->staff_photo))
                    <img class="student-meta-img img-100" src="{{asset($custDetails->staff_photo)}}" alt="">
                    @else
                    <img class="student-meta-img img-100" src="{{ asset('/') }}public/uploads/staff/demo/staff.jpg" alt="">
                @endif
                <div class="white-box">

               @if(isset($custDetails) && !empty(@$custDetails->customer_name))
                    <div class="single-meta mt-10">
                        <div class="d-flex justify-content-between">
                            <div class="name">
                                @lang('Customer Name')
                            </div>
                            <div class="value">

                                @if(isset($custDetails)){{@$custDetails->customer_name}}@endif

                            </div>
                        </div>
                    </div>

                @endif
               @if(isset($custDetails) && !empty(@$custDetails->customer_code))
                    <div class="single-meta">
                        <div class="d-flex justify-content-between">
                            <div class="name">
                                @lang('lang.role') 
                            </div>
                            <div class="value">
                               @if(isset($custDetails)){{@$custDetails->customer_code }}@endif
                           </div>
                       </div>
                   </div>

                @endif
</div>
</div>
<!-- End Student Meta Information -->

</div>

<!-- Start Student Details -->
<div class="col-lg-9 staff-details">
    
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            {{-- <a class="nav-link active" href="#studentProfile" role="tab" data-toggle="tab">@lang('lang.profile')</a> --}}
        </li> 
        <li class="nav-item edit-button">
            <a href="{{url('customer-edit/'.@$custDetails->id)}}" class="primary-btn small fix-gr-bg">@lang('lang.edit')
            </a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Start Profile Tab -->
        <div role="tabpanel" class="tab-pane fade show active" id="studentProfile">
            <div class="white-box">
                <h4 class="stu-sub-head">@lang('lang.info')</h4>


 
               @if(isset($custDetails) && !empty(@$custDetails->contcat_person))

                <div class="single-info">
                    <div class="row">
                        <div class="col-lg-5 col-md-5">
                            <div class="">
                                @lang('Contcat Person')
                            </div>
                        </div>

                        <div class="col-lg-7 col-md-6">
                            <div class="">
                                @if(isset($custDetails)){{@$custDetails->contcat_person}}@endif
                            </div>
                        </div>
                    </div>
                </div>

              @endif
               @if(isset($custDetails) && !empty(@$custDetails->mobile))
                <div class="single-info">
                    <div class="row">
                        <div class="col-lg-5 col-md-6">
                            <div class="">
                              @lang('Mobile') 
                           </div>
                       </div>

                       <div class="col-lg-7 col-md-7">
                        <div class="">
                         @if(isset($custDetails)){{@$custDetails->mobile}}@endif
                     </div>
                 </div>
             </div>
         </div>

              @endif
               @if(isset($custDetails) && !empty(@$custDetails->address))
         <div class="single-info">
            <div class="row">
                <div class="col-lg-5 col-md-6">
                    <div class="">
                        @lang('Address')
                    </div>
                </div>

                <div class="col-lg-7 col-md-7">
                    <div class="">
                        @if(isset($custDetails)){{@$custDetails->address}}@endif
                    </div>
                </div>
            </div>
        </div>
              @endif
               @if(isset($custDetails) && !empty(@$custDetails->email))

        <div class="single-info">
            <div class="row">
                <div class="col-lg-5 col-md-6">
                    <div class="">
                        @lang('Email')
                    </div>
                </div>

                <div class="col-lg-7 col-md-7">
                    <div class="">
                        @if(isset($custDetails)){{@$custDetails->email}}@endif
                    </div>
                </div>
            </div>
        </div>
              @endif
               @if(isset($custDetails) && !empty(@$custDetails->vat_number))

        <div class="single-info">
            <div class="row">
                <div class="col-lg-5 col-md-6">
                    <div class="">
                       @lang('Vat Number') 
                    </div>
                </div>

                <div class="col-lg-7 col-md-7">
                    <div class="">
                        @if(isset($custDetails)){{@$custDetails->vat_number}}@endif
                    </div>
                </div>
            </div>
        </div>
              @endif
               @if(isset($custDetails) && !empty(@$custDetails->sales_person_name))
        <div class="single-info">
            <div class="row">
                <div class="col-lg-5 col-md-6">
                    <div class="">
                      @lang('Sales Person Name')
                   </div>
               </div>

               <div class="col-lg-7 col-md-7">
                <div class="">
                    @if(isset($custDetails)){{@$custDetails->sales_person_name}}@endif
                </div>
            </div>
        </div>
    </div>
              @endif
               @if(isset($custDetails) && !empty(@$custDetails->credit_limit))

    <div class="single-info">
        <div class="row">
            <div class="col-lg-5 col-md-6">
                <div class="">
                   @lang('Credit Limit')
                </div>
            </div>

            <div class="col-lg-7 col-md-7">
                <div class="">
                    @if(isset($custDetails)){{@$custDetails->credit_limit}}@endif
                </div>
            </div>
        </div>
    </div>
              @endif
               @if(isset($custDetails) && !empty(@$custDetails->credit_days))

    <div class="single-info">
        <div class="row">
            <div class="col-lg-5 col-md-6">
                <div class="">
                   @lang('Credit Days')
                </div>
            </div>

            <div class="col-lg-7 col-md-7">
                <div class="">
                    @if(isset($custDetails)){{@$custDetails->credit_days}}@endif
                </div>
            </div>
        </div>
    </div>
              @endif
               @if(isset($custDetails) && !empty(@$custDetails->paymentterms->title))

    <div class="single-info">
        <div class="row">
            <div class="col-lg-5 col-md-6">
                <div class="">
                    @lang('Payment Terms')
                </div>
            </div>

            <div class="col-lg-7 col-md-7">
                <div class="">
                    @if(isset($custDetails)){{@$custDetails->paymentterms->title}}@endif
                </div>
            </div>
        </div>
    </div>
              @endif

@if(isset($custDetails) && !empty(@$custDetails->accountant_name))
    <div class="single-info">
        <div class="row">
            <div class="col-lg-5 col-md-6">
                <div class="">
                  @lang('Accountant Name')
               </div>
           </div>

           <div class="col-lg-7 col-md-7">
            <div class="">
                @if(isset($custDetails)){{@$custDetails->accountant_name}}@endif
            </div>
        </div>
    </div>
</div>
@endif 

@if(isset($custDetails) && !empty(@$custDetails->accountant_email))
    <div class="single-info">
        <div class="row">
            <div class="col-lg-5 col-md-6">
                <div class="">
                  @lang('Accountant Email')
               </div>
           </div>

           <div class="col-lg-7 col-md-7">
            <div class="">
                @if(isset($custDetails)){{@$custDetails->accountant_email}}@endif
            </div>
        </div>
    </div>
</div>
@endif
@if(isset($custDetails) && !empty(@$custDetails->accountant_number))
    <div class="single-info">
        <div class="row">
            <div class="col-lg-5 col-md-6">
                <div class="">
                  @lang('Accountant Number')
               </div>
           </div>

           <div class="col-lg-7 col-md-7">
            <div class="">
                @if(isset($custDetails)){{@$custDetails->accountant_number}}@endif
            </div>
        </div>
    </div>
</div>
@endif 


<!-- End Other Information Part -->
</div>
</div>
 

  
           
    </div>
</div>
</section>
@endsection
