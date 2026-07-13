{{-- Master layout just re-delegates to the main app layout --}}
@extends('layouts.app')

@section('content')
    @yield('inner_content')
@endsection
