@extends('layouts.layout')



@section('content')

Current default display.

@if($dta!=null)


{{ json_encode($dta) }}

@endif

@endsection