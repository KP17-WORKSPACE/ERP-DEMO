@include('install._header')
    <div class="col-md-6 offset-3  mt-40"> 
            <ul id="progressbar">
                <li class="active">{{ __('Welcome') }}</li>
                <li>{{ __('Verification') }}</li> 
                <li>{{ __('Environment') }}</li>
                <li>{{ __('System Setup') }}</li>
            </ul>
        <div class="card">
            <div class="single-report-admit">
                <div class="card-header">
                    <h2 class="text-center text-uppercase welcome-to-infix-h " >{{ __('Welcome to Infix Business ERP')}}</h2>
                </div>
            </div>
            <div class="card-body">
                <p class="welcome-to-infix-p" >
                    {{ __('Thank you for choosing Infix Business ERP for your business. Please follow the steps to complete Infix Business ERP installation!')}}
                </p>
               <a href="{{url('/check-purchase-verification')}}"  class="offset-3 col-sm-6 primary-btn fix-gr-bg mt-40 mb-20"  >{{ __('Let\'s Go') }}   </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
