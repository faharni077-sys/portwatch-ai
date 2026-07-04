@extends('layouts.master')

@section('content')

<div class="container-fluid">

    <h2 class="mb-4 fw-bold">
        Dashboard
    </h2>

    <div class="row">

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Total Countries</h5>
                    <h2>195</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Average Risk</h5>
                    <h2>Low</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Exchange Rate</h5>
                    <h2>Live</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Weather</h5>
                    <h2>Realtime</h2>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection