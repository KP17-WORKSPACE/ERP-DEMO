@include('install._header')
    <div class="col-md-6 offset-3  mt-40">

            <ul id="progressbar">
                <li class="active">{{ __('welcome') }}</li>
                <li class="active">{{ __('verification') }}</li> 
                <li>{{ __('Environment') }}</li>
                <li>{{ __('System Setup') }}</li>
            </ul>


        <div class="card">
            <div class="single-report-admit">
                <div class="card-header">
                    <h2 class="install-page2-h">{{ __('Database Connection') }}</h2>
                @if(Session::has('message-danger'))
                    <p class="alert alert-danger">{{ Session::get('message-danger') }}</p>
                @endif
                </div>
            </div>
            <div class="card-body">
                @if(Session::has('message-success'))
                    <p class="alert alert-success">{{ Session::get('message-success') }}</p>
                @endif
                <form method="post" action="{{route('installStep2')}}">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="database_name">{{ __('Database Name:') }}</label>
                        <input type="text" class="form-control" name="database_name" required value="">
                    </div>
                    <div class="form-group">
                        <label for="database_user">{{ __('Database User:') }}</label>
                        <input type="text" class="form-control" name="database_user" required value="">
                    </div>
                    <div class="form-group">
                        <label for="database_password">{{ __('Database Password:') }}</label>
                        <input type="password" class="form-control" name="database_password" value="">
                    </div>
                    <input type="submit" value="Next" class="offset-3 col-sm-6  primary-btn fix-gr-bg install-page2" >
                </form>
            </div>
        </div>
    </div>

</div>
</body>
</html>

