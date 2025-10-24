@extends('errors::minimal')

@section('title', __('Page Not Found'))
@section('code', '404')
@section('message', __('Oops! Hindi namin mahanap ang page na hinahanap mo.'))
@section('link')
    <a href="{{ route('dashboard') }}" class="text-lg font-medium text-blue-600 hover:text-blue-500">
        Bumalik sa Dashboard
    </a>
@endsection