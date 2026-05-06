@include('install._header')
    <div class="col-md-6 offset-3  mt-40">


        <div class="card">
            <div class="single-report-admit">
                <div class="card-header">
                    <h2 class="text-center text-uppercase">{{ __('VERIFICATION')}}</h2>
                </div>
            </div>
            <div class="card-body">
                @if(Session::has('message-danger'))
                    <p class="alert alert-danger">{{ Session::get('message-danger') }}</p>
                @endif



               <form method="post" action="{{url('verified-code')}}">
                   {{csrf_field()}}
                   <div class="form-group">
                       <label for="user">{{ __('Envato Username :')}}</label>
                       <input type="text" class="form-control" name="envatouser" value="">
                   </div>
                   <div class="form-group">
                       <label for="purchasecode">{{ __('Envato Purchase Code:')}}</label>
                       <input type="text" class="form-control" name="purchasecode" value="">
                   </div>
                   <div class="form-group">
                       <label for="domain">{{ __('Installation Domain:')}}</label>
                       <input type="text" class="form-control" name="installationdomain" value="">
                   </div>
                   <input type="submit" value="Next" class="offset-3 col-sm-6 primary-btn fix-gr-bg verified-code" >
               </form>
            </div>
        </div>
    </div>

</div>
</body>
</html>
