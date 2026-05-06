@include('install._header')
    <div class="col-md-6 offset-3  mt-40"> 
        <ul id="progressbar">
            <li class="active">{{ __('Welcome') }}</li>
            <li class="active">{{ __('Verification') }}</li> 
            <li>{{ __('Environment') }}</li>
            <li>{{ __('System Setup') }}</li>
        </ul> 


        <div class="card">
            <div class="single-report-admit">
                <div class="card-header">
                    <h2 class="text-center text-uppercase">{{ __('VERIFICATION')}}</h2>
                </div>
            </div>
            <div class="card-body">
                @if(Session::has('message-danger'))
                    <p class="text-danger">** {{ Session::get('message-danger') }}</p>
                @endif
                 @if ($errors->any()) 
                    {{ __('Ops sorry ! Please enter valid input!')}}
                       @foreach ($errors->all() as $error)
                          <p class="text-danger">** {{$error}}</p>
                       @endforeach 
                 @endif


               <form method="post" action="{{url('check-verified-input')}}">
                   {{csrf_field()}}
                   <div class="form-group">
                       <label for="user">{{ __('Envato Username :')}}</label>
                       <input type="text" class="form-control " name="envatouser"  required="required"  placeholder="Enter Your Envato User Name"> 
                   </div>
                   <div class="form-group">
                       <label for="purchasecode">{{ __('Envato Purchase Code:')}}</label>
                       <input type="text" class="form-control" name="purchasecode" required="required" placeholder="Enter Your Envato Purchase Code">
                   </div>
                   <div class="form-group">
                       <label for="domain">{{ __('Installation Domain:')}}</label>
                       <input type="text" class="form-control" name="installationdomain" required="required"  placeholder="Enter Your Installation Domain">
                   </div>
                   <input type="submit" value="Next" class="offset-3 col-sm-6 primary-btn fix-gr-bg mt-40 check-pur-page-input">
               </form>
            </div>
        </div>
    </div>

</div>
</body>
</html>
