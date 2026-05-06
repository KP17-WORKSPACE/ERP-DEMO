@include('backEnd.partials.header')

@yield('mainContent')
<?php
    if(App\SysHelper::password_update()==1){
        header("Location: ".url('/')."/password-exp");
        exit();
    } ?>
<div id="loading_bg" style="width: 100vw; height: 100vh; background: #00000085; position: fixed; z-index: 999; text-align: center; display: none;">
    <img src="{!! asset('public/backEnd/img/loader.gif') !!}" style="width: 50px; margin: 20%;">
</div>

@include('backEnd.partials.footer')            
