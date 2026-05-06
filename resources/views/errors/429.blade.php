@extends('errors::illustrated-layout')

@section('code', '429')
@section('title', __('Too Many Requests'))

@section('image')
    <div class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center error_429_style">
    </div>
@endsection

@section('message', __('Sorry, you are making too many requests to our servers.'))
