@include('install._header')
    <div class="col-md-6 offset-3  mt-40">
        <ul id="progressbar">
            <li class="active">{{ __('Welcome') }}</li>
            <li class="active">{{ __('Verification') }}</li> 
            <li class="active">{{ __('Environment') }}</li>
            <li class="active">{{ __('System Setup') }}</li>
        </ul>  

        <div class="card">

            <div class="single-report-admit">
                <div class="card-header">
                    <h2 class="text-center text-uppercase confirmation-h " >{{ __('Welcome to Infix Business ERP')}} </h2>
                
                </div>
            </div>

            <div class="card-body">
                  <h3 class="text-center text-success">{{ __('Congratulations!')}}</h3>
                  <p class="confirmation-p">{{ __('Your System Admin Email')}}  {{Session::get('system_admin_email')}}</p>
                  <p class="confirmation-p">{{ __('Your System Password')}}  {{Session::get('system_admin_password')}}</p>

               <a href="{{url('/login')}}"  class="offset-3 col-sm-6 primary-btn fix-gr-bg mt-40 mb-20" >  {{ __('Login')}} </a>
            </div>
        </div>
    </div>

</div>
</body>
</html>
