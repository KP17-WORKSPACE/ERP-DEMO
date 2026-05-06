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
                    <h2 class="system-setup-page-h">{{ __('System Setup') }}</h2>
                </div>
            </div>
            <div class="card-body">
                @if($errors)
                    @foreach($errors->all() as $error)
                    <div class="alert alert-danger"><ul><li>{{ $error }}</li></ul></div>
                    @endforeach
                @endif
                <form method="post" action="{{url('confirm-installing')}}">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="institution_name">{{ __('System Name')}}</label>
                        <input type="text" class="form-control" name="institution_name" required value="{{old('institution_name')}}">
                    </div>
                    <div class="form-group">  
                        <input type="hidden" name="institution_address" value="201 Conference Center Way, Bridgeport, WV, 26330"> 
                        <input type="hidden" name="session_year" value="{{date('Y')}}"> 
                    </div> 

                    <div class="form-group">
                        <label for="system_admin_email">{{ __('System Admin Email')}}</label>
                        <input type="text" class="form-control" name="system_admin_email" required autocomplete="off" value="{{old('system_admin_email')}}">
                    </div>
                    <div class="form-group">
                        <label for="system_admin_password">{{ __('System Admin Password')}}</label>
                        <input type="password" class="form-control" name="system_admin_password" required autocomplete="off" value="{{old('system_admin_password')}}">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">{{ __('Confirm Password')}}</label>
                        <input type="password" class="form-control" name="password_confirmation" required  autocomplete="false" value="{{old('password_confirmation')}}">
                    </div>

                    <input type="text" hidden value="2" name="install_mode">
                    {{-- <div class="form-group">
                        <label for="install_mode">{{ __('Install Mode') }}</label>
                        <select class="form-control" name="install_mode" required>
                            <option value="1">{{ __('With Sample Data') }}</option>
                            <option value="2">{{ __('Without Sample Data') }}</option> 
                        </select>
                    </div>  --}}
                    <input type="submit" value="Let's Go" class="offset-3 col-sm-6  primary-btn fix-gr-bg mt-40 system-setup-page ">
                </form>
            </div>
        </div>
    </div>

</div>
</body>
</html>

