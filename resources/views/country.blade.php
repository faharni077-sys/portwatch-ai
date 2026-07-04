@extends('layouts.master')

@section('content')

<h2 class="mb-4 fw-bold">🌍 Country Dashboard</h2>

<div class="row mb-4">

    <div class="col-md-8">
        <input type="text"
               id="searchCountry"
               class="form-control"
               placeholder="🔍 Cari Negara...">
    </div>

    <div class="col-md-4">
        <select class="form-select" id="sortCountry">
            <option value="">Urutkan</option>
            <option value="asc">A - Z</option>
            <option value="desc">Z - A</option>
        </select>
    </div>

</div>

<div class="row" id="countryContainer">

@foreach($countries as $country)

<div class="col-md-4 mb-4 country-card">

    <div class="card shadow-sm h-100 country-item"
         data-name="{{ strtolower($country['name']) }}">

        <div class="card-body">

            <div class="d-flex align-items-center mb-2">
    <img src="{{ $country['flag'] ?? '' }}"
     alt="{{ $country['name'] }}"
     width="45"
     class="me-3 rounded shadow-sm">

    <div>
        <h5 class="mb-0">{{ $country['name'] }}</h5>
        <small class="text-muted">{{ $country['region'] }}</small>
    </div>
</div>

            <p>🏛️ <b>Capital:</b> {{ $country['capital'] }}</p>

                <p>💰 <b>Currency:</b> {{ $country['currency'] }}</p>

                 <p>🗣️ <b>Language:</b> {{ $country['language'] }}</p>

                  <p>👥 <b>Population:</b>
                    {{ number_format($country['population']) }}
                    </p>
            
            <div class="mt-3">

    <a href="{{ route('country.show', $country['iso2']) }}" class="btn btn-primary">
    Monitor Country
</a>

</div>

        </div>

    </div>

</div>

@endforeach

</div>

<script>

const search = document.getElementById('searchCountry');

search.addEventListener('keyup', function(){

    let keyword = this.value.toLowerCase();

    document.querySelectorAll('.country-item').forEach(function(card){

        let name = card.dataset.name;

        if(name.includes(keyword)){
            card.parentElement.style.display='';
        }else{
            card.parentElement.style.display='none';
        }

    });

});

</script>

@endsection