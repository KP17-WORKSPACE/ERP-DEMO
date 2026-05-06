@extends('errors::illustrated-layout')

@section('code', '403')
@section('title', __('Forbidden'))

@section('image')
    <div class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center error_403_style">
    </div>
@endsection

@section('message', __(@$exception->getMessage() ?: 'Sorry, you are forbidden from accessing this page.'))
