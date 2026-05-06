@extends('errors::illustrated-layout')

@section('code', '503')
@section('title', __('Service Unavailable'))

@section('image')
    <div class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center error_503_style">
    </div>
@endsection

@section('message', __(@$exception->getMessage() ?: 'Sorry, we are doing some maintenance. Please check back soon.'))
