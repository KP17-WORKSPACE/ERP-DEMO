@extends('errors::illustrated-layout')

@section('code', '500')
@section('title', __('Error'))

@section('image')
    <div class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center error_500_style">
    </div>
@endsection

@section('message', __('Whoops, something went wrong on our servers.'))
