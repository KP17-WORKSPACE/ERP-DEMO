@include('install._header')
    <div class="col-md-6 offset-3  mt-40"> 
        <ul id="progressbar">
            <li class="active">{{ __('Welcome') }}</li>
            <li class="active">{{ __('Verification') }}</li> 
            <li class="active">{{ __('Environment') }}</li>
            <li>{{ __('System Setup') }}</li>
        </ul>  
        <div class="card">
            <div class="single-report-admit">
            <div class="card-header">
                <h2 class="text-center text-uppercase environment-h2">{{ __('ENVIRONMENT SETUP')}}</h2>
            </div>
            </div>
            <div class="card-body environment-setup environment-body-card"> 

                @if(Session::has('message-success'))
                    <p class="text-success text-center mt-20 mb-20">{{ Session::get('message-success') }}</p>
                @endif
                @if(Session::has('message-danger'))
                    <p class="text-danger text-center mt-20 mb-20">{{ Session::get('message-danger') }}</p>
                @endif


                <h4 class="environment-body-card-h4">{{ __('Basic Requirements')}} </h4>
                <p class="mb-20"> {{ __('Please make sure your server meets the following requirements:')}}</p>


                @foreach($folders as $f)
                <p class="permission">{{ __('*** public_html')}}{{$f}}</p>
                @endforeach
                <p class="text-danger">{{ __('Please make sure above folders has permission 777.')}}</p>


                <h4 class="environment-body-card-h4 mt-20">{{ __('Advance Requirements')}} </h4>
                <div class="requirements">
                   <table class="table">
                       <thead>
                       <th>{{ __('Status')}} </th>
                       <th>{{ __('Current Available')}} </th>
                       <th>{{ __('Required')}} </th>
                       </thead>
                       <tbody>
                       <tr>
                           <td> <span class=' @if(phpversion()>=7.1) text-success ti-check-box @else text-danger ti-na @endif' ></span></td>
                           <td> <p class="@if(phpversion()>=7.1) text-success @else text-danger @endif"> {{ __('PHP >=')}}{{phpversion()}}</p> </td>
                           <td>{{ __('PHP >= 7.1.3')}}</td>
                       </tr>

                       <tr>
                           <td> <span class='@if( OPENSSL_VERSION_NUMBER < 0x009080bf) ti-na text-danger @else ti-check-box  text-success @endif'></span>  </td>
                           <td> <p class="@if( OPENSSL_VERSION_NUMBER < 0x009080bf)  text-danger @else  text-success @endif"> {{ __('OpenSSL Version')}}</p>  </td>
                           <td>{{ __('OpenSSL PHP Extension')}}</td>
                       </tr>

                       <tr>
                           <td> <span class='@if(PDO::getAvailableDrivers()) ti-check-box  text-success @else  ti-na text-danger  @endif'></span>  </td>
                           <td> <p class="@if(PDO::getAvailableDrivers())  text-success @else  text-danger  @endif"> {{ __('PDO PHP Extension')}}</p>  </td>
                           <td>{{ __('PDO PHP Extension')}}</td>
                       </tr>
                       <tr>
                           <td> <span class="@if(extension_loaded('mbstring')) ti-check-box  text-success @else  ti-na text-danger  @endif"></span>  </td>
                           <td> <p class="@if(extension_loaded('mbstring'))  text-success @else  text-danger  @endif"> {{ __('Mbstring PHP Extension')}} </p>  </td>
                           <td>{{ __('Mbstring PHP Extension')}} </td>
                       </tr>
                       <tr>
                           <td> <span class="@if(extension_loaded('tokenizer')) ti-check-box  text-success @else  ti-na text-danger  @endif"></span>  </td>
                           <td> <p class="@if(extension_loaded('tokenizer'))  text-success @else  text-danger  @endif"> {{ __('Tokenizer PHP Extension')}}</p>  </td>
                           <td>{{ __('Tokenizer PHP Extension')}}</td>
                       </tr>
                       <tr>
                           <td> <span class="@if(extension_loaded('xml')) ti-check-box  text-success @else  ti-na text-danger  @endif"></span>  </td>
                           <td> <p class="@if(extension_loaded('xml'))  text-success @else  text-danger  @endif"> {{ __('XML PHP Extension')}}</p>  </td>
                           <td>{{ __('XML PHP Extension')}}</td>
                       </tr>
                       <tr>
                           <td> <span class="@if(extension_loaded('json')) ti-check-box  text-success @else  ti-na text-danger  @endif"></span>  </td>
                           <td> <p class="@if(extension_loaded('json'))  text-success @else  text-danger  @endif"> {{ __('JSON PHP Extension')}} </p>  </td>
                           <td>{{ __('JSON PHP Extension')}} </td>
                       </tr> 

                       </tbody>
                   </table>


                </div>

                <form action="{{url('checking-environment')}}" method="get">
                    {{csrf_field()}}
                    <input type="submit" class="offset-3 col-sm-6  primary-btn fix-gr-bg mt-20 mb-20 check-environment-page-input" value="Next Step" name="next">
                </form>
            </div>
        </div>
    </div>

</div>
</body>
</html>


