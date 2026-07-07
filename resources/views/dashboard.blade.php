<x-app-layout>

<div class="container-fluid">

<div class="row">

    {{-- SIDEBAR --}}

    <div class="col-lg-2 sidebar">

        <h3 class="logo">
            🌍 PortWatch AI
        </h3>

        <ul class="menu">

            <li class="active">
                <i class="bi bi-grid"></i>
                Dashboard
            </li>

            <li>
                <i class="bi bi-globe2"></i>
                Country
            </li>

            <li>
                <i class="bi bi-cloud-sun"></i>
                Weather
            </li>

            <li>
                <i class="bi bi-currency-exchange"></i>
                Currency
            </li>

            <li>
                <i class="bi bi-newspaper"></i>
                News
            </li>

            <li>
                <i class="bi bi-truck"></i>
                Ports
            </li>

            <li>
                <i class="bi bi-graph-up"></i>
                Analytics
            </li>

            <li>
                <i class="bi bi-star"></i>
                Watchlist
            </li>

        </ul>

    </div>

    {{-- CONTENT --}}

    <div class="col-lg-10">

        <div class="topbar">

            <h4>
                Welcome Back,
                {{ Auth::user()->name }}
            </h4>

            <button class="btn btn-danger">

                Logout

            </button>

        </div>

        <div class="hero-dashboard">

            <div class="overlay"></div>

            <div class="hero-content">

                <h1>
                    Global Logistics Dashboard
                </h1>

                <p>

                    Monitor worldwide shipment,
                    weather,
                    exchange rate,
                    logistics news
                    and supply chain.

                </p>

            </div>

        </div>

        {{-- CARD --}}

        <div class="row mt-4">

            <div class="col-md-3">

                <div class="info-card">

                    <h5>Countries</h5>

                    <h2>195</h2>

                </div>

            </div>

            <div class="col-md-3">

                <div class="info-card">

                    <h5>Shipment</h5>

                    <h2>1540</h2>

                </div>

            </div>

            <div class="col-md-3">

                <div class="info-card">

                    <h5>Risk</h5>

                    <h2>Low</h2>

                </div>

            </div>

            <div class="col-md-3">

                <div class="info-card">

                    <h5>Currency</h5>

                    <h2>Live</h2>

                </div>

            </div>
            {{-- MAP + WEATHER --}}

<div class="row mt-4">

    <div class="col-lg-8">

        <div class="dashboard-widget">

            <h4>
                🌍 Global Supply Chain Map
            </h4>

            <div class="map-placeholder">

                World Map (Leaflet API)

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="dashboard-widget">

            <h4>
                🌦 Weather
            </h4>

            <h2>29°C</h2>

            <p>Jakarta, Indonesia</p>

            <hr>

            <small>
                Wind : 10 km/h
            </small>

            <br>

            <small>
                Humidity : 81%
            </small>

        </div>

    </div>

</div>


{{-- NEWS + CURRENCY --}}

<div class="row mt-4">

    <div class="col-lg-8">

        <div class="dashboard-widget">

            <h4>
                📰 Logistics News
            </h4>

            <ul class="news-list">

                <li>Container traffic increases in Singapore Port</li>

                <li>China export rises 3%</li>

                <li>USD weakens against JPY</li>

                <li>Heavy rain affects Rotterdam Port</li>

            </ul>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="dashboard-widget">

            <h4>
                💱 Exchange Rate
            </h4>

            <h2>USD / IDR</h2>

            <h3>16,250</h3>

            <span class="text-success">
                ▲ +0.25%
            </span>

        </div>

    </div>

</div>


{{-- CHART --}}

<div class="row mt-4">

    <div class="col-lg-12">

        <div class="dashboard-widget">

            <h4>
                📈 Supply Chain Analytics
            </h4>

            <div class="chart-placeholder">

                Chart.js (Coming Soon)

            </div>

        </div>

    </div>

</div>

<div class="row mt-4">

    <div class="col-lg-8">

        <div class="dashboard-widget">

            <h4>📦 Recent Shipment</h4>

            <table class="table table-dark table-hover align-middle">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Country</th>
                        <th>Port</th>
                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>PW001</td>

                        <td>Singapore</td>

                        <td>PSA</td>

                        <td>
                            <span class="badge bg-success">
                                Delivered
                            </span>
                        </td>

                    </tr>

                    <tr>

                        <td>PW002</td>

                        <td>China</td>

                        <td>Shanghai</td>

                        <td>

                            <span class="badge bg-warning">

                                In Transit

                            </span>

                        </td>

                    </tr>

                    <tr>

                        <td>PW003</td>

                        <td>USA</td>

                        <td>Los Angeles</td>

                        <td>

                            <span class="badge bg-danger">

                                Delayed

                            </span>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="dashboard-widget">

            <h4>

                ⚠ AI Risk Prediction

            </h4>

            <h2 class="text-warning">

                MEDIUM

            </h2>

            <p>

                Heavy rain detected near Singapore Port.

            </p>

            <p>

                USD exchange volatility increased.

            </p>

        </div>

    </div>

</div>
        </div>

    </div>

</div>

</div>

</x-app-layout>