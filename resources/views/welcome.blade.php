<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PortWatch AI — Global Supply Chain Intelligence Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="pw-landing">

{{-- ============================================================
     HERO
     ============================================================ --}}
<section class="pw-hero">
    <div class="pw-hero-bg"></div>
    <div class="pw-hero-overlay"></div>

    {{-- Navbar --}}
    <nav class="pw-hero-nav">
        <a href="/" class="pw-hero-logo">
            <div class="pw-hero-logo-icon"><i class="bi bi-globe-americas"></i></div>
            <div class="pw-hero-logo-text">
                <span class="name">PORTWATCH AI</span>
                <span class="sub">INTELLIGENCE PLATFORM</span>
            </div>
        </a>
        <div class="pw-hero-nav-links">
            <a href="#features"    class="pw-nav-link d-none d-md-inline">Features</a>
            <a href="#how-it-works" class="pw-nav-link d-none d-md-inline">How It Works</a>
            <a href="#apis"        class="pw-nav-link d-none d-md-inline">APIs</a>
            <a href="{{ route('login') }}"    class="pw-btn-login">Login</a>
            <a href="{{ route('register') }}" class="pw-btn-register">Register</a>
        </div>
    </nav>

    {{-- Hero Text --}}
    <div class="pw-hero-content">
        <div>
            <div class="pw-hero-eyebrow">
                <span class="dot-pulse"></span>
                REAL-TIME MONITORING · GLOBAL INTELLIGENCE
            </div>
            <h1 class="pw-hero-h1">
                Global Supply<br>
                Chain <span class="cyan">Intelligence</span>
            </h1>
            <p class="pw-hero-sub">
                Monitor weather extremes, currency volatility, port congestion,
                geopolitical risk, and economic indicators — all in one
                mission-control platform.
            </p>
            <div class="pw-hero-actions">
                <a href="{{ route('register') }}" class="pw-btn-cta">
                    <i class="bi bi-rocket-takeoff-fill"></i> Get Started Free
                </a>
                <a href="#features" class="pw-btn-ghost">
                    Learn More <i class="bi bi-arrow-down"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="pw-hero-stats">
        <div class="pw-hero-stat">
            <div class="val">190<span class="unit">+</span></div>
            <div class="lbl">COUNTRIES TRACKED</div>
        </div>
        <div class="pw-hero-stat">
            <div class="val">3<span class="unit">k+</span></div>
            <div class="lbl">PORTS INDEXED</div>
        </div>
        <div class="pw-hero-stat">
            <div class="val">6<span class="unit"> APIs</span></div>
            <div class="lbl">LIVE DATA SOURCES</div>
        </div>
        <div class="pw-hero-stat">
            <div class="val">24<span class="unit">/7</span></div>
            <div class="lbl">REALTIME SYNC</div>
        </div>
    </div>

    <div class="pw-scroll-hint">
        <i class="bi bi-chevron-double-down"></i>
        <span>Scroll</span>
    </div>
</section>

{{-- ============================================================
     FEATURES
     ============================================================ --}}
<section class="pw-section pw-section-alt" id="features">
    <div class="container-xl">
        <div class="text-center mb-5">
            <div class="pw-section-eyebrow">PLATFORM CAPABILITIES</div>
            <h2 class="pw-section-h2">Everything You Need to Monitor<br>Global Supply Chains</h2>
            <p class="pw-section-lead mx-auto">
                From weather disruptions to currency swings and port congestion —
                PortWatch AI gives you the full intelligence picture.
            </p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="pw-feature-card">
                    <div class="pw-feature-icon cyan"><i class="bi bi-cloud-lightning-rain-fill"></i></div>
                    <div class="pw-feature-title">Weather Intelligence</div>
                    <div class="pw-feature-desc">
                        Live temperature, wind speed, rainfall, and storm risk data for every country using Open-Meteo API.
                        Know before your cargo departs.
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="pw-feature-card">
                    <div class="pw-feature-icon green"><i class="bi bi-currency-exchange"></i></div>
                    <div class="pw-feature-title">Currency Impact</div>
                    <div class="pw-feature-desc">
                        Real-time exchange rates with trend charts. Monitor USD, EUR, IDR, JPY, CNY volatility
                        to protect import cost margins.
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="pw-feature-card">
                    <div class="pw-feature-icon amber"><i class="bi bi-graph-up-arrow"></i></div>
                    <div class="pw-feature-title">Economic Analytics</div>
                    <div class="pw-feature-desc">
                        GDP, inflation, population, exports and imports from World Bank API.
                        Understand country-level economic health at a glance.
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="pw-feature-card">
                    <div class="pw-feature-icon purple"><i class="bi bi-anchor"></i></div>
                    <div class="pw-feature-title">Port Monitoring</div>
                    <div class="pw-feature-desc">
                        Interactive Leaflet map with 3,000+ world ports indexed.
                        Search by country, filter by name, view location and congestion data.
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="pw-feature-card">
                    <div class="pw-feature-icon cyan"><i class="bi bi-newspaper"></i></div>
                    <div class="pw-feature-title">News Sentiment AI</div>
                    <div class="pw-feature-desc">
                        GNews API feeds with lexicon-based sentiment analysis.
                        Positive, neutral, and negative scoring for logistics & trade news.
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="pw-feature-card">
                    <div class="pw-feature-icon green"><i class="bi bi-shield-exclamation"></i></div>
                    <div class="pw-feature-title">Risk Scoring Engine</div>
                    <div class="pw-feature-desc">
                        Composite risk score from weather, inflation, currency, and news sentiment.
                        LOW / MEDIUM / HIGH classification per country.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     HOW IT WORKS
     ============================================================ --}}
<section class="pw-section pw-section-dark" id="how-it-works">
    <div class="container-xl">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="pw-section-eyebrow">WORKFLOW</div>
                <h2 class="pw-section-h2">How PortWatch AI Works</h2>
                <p class="pw-section-lead">
                    A streamlined four-step intelligence loop that keeps your
                    supply chain decisions data-driven.
                </p>
            </div>
            <div class="col-lg-7">
                <div class="pw-step">
                    <div class="pw-step-num">01</div>
                    <div>
                        <div class="pw-step-title">Select a Country or Port</div>
                        <div class="pw-step-desc">
                            Choose from 190+ countries or search 3,000+ ports on the interactive map.
                            Filter by region, risk level, or currency zone.
                        </div>
                    </div>
                </div>
                <div class="pw-step">
                    <div class="pw-step-num">02</div>
                    <div>
                        <div class="pw-step-title">Fetch Live Intelligence</div>
                        <div class="pw-step-desc">
                            Platform pulls real-time data from 6 external APIs — weather, economic,
                            currency, news, port, and geospatial data fused together.
                        </div>
                    </div>
                </div>
                <div class="pw-step">
                    <div class="pw-step-num">03</div>
                    <div>
                        <div class="pw-step-title">Risk Score Calculated</div>
                        <div class="pw-step-desc">
                            Weighted algorithm combines Weather Risk (30%), Political News Risk (40%),
                            Inflation Risk (20%), and Currency Risk (10%).
                        </div>
                    </div>
                </div>
                <div class="pw-step">
                    <div class="pw-step-num">04</div>
                    <div>
                        <div class="pw-step-title">Make Informed Decisions</div>
                        <div class="pw-step-desc">
                            Dashboard shows LOW / MEDIUM / HIGH risk classification with
                            AI recommendation and trend analysis for business decisions.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     API INTEGRATIONS
     ============================================================ --}}
<section class="pw-section pw-section-alt" id="apis">
    <div class="container-xl">
        <div class="text-center mb-5">
            <div class="pw-section-eyebrow">DATA SOURCES</div>
            <h2 class="pw-section-h2">Powered by 6 Live APIs</h2>
            <p class="pw-section-lead mx-auto">
                All free, open-access APIs — delivering enterprise-grade intelligence
                without the enterprise price tag.
            </p>
        </div>
        <div class="text-center">
            <span class="pw-api-badge"><i class="bi bi-cloud-sun-fill"></i> Open-Meteo — Weather</span>
            <span class="pw-api-badge"><i class="bi bi-bank2"></i> World Bank — Economics</span>
            <span class="pw-api-badge"><i class="bi bi-globe2"></i> REST Countries — Nations</span>
            <span class="pw-api-badge"><i class="bi bi-currency-dollar"></i> ExchangeRate API — Forex</span>
            <span class="pw-api-badge"><i class="bi bi-newspaper"></i> GNews API — Media</span>
            <span class="pw-api-badge"><i class="bi bi-map-fill"></i> OpenStreetMap — Geospatial</span>
        </div>

        <div class="row g-4 mt-5">
            <div class="col-md-4">
                <div class="pw-feature-card text-center">
                    <div class="pw-feature-icon cyan mx-auto mb-3"><i class="bi bi-database-check"></i></div>
                    <div class="pw-feature-title">15–20 Database Tables</div>
                    <div class="pw-feature-desc">Structured storage for countries, ports, risk scores, news cache, currency rates, watchlists, and sentiment results.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="pw-feature-card text-center">
                    <div class="pw-feature-icon green mx-auto mb-3"><i class="bi bi-lightning-charge-fill"></i></div>
                    <div class="pw-feature-title">30+ REST Endpoints</div>
                    <div class="pw-feature-desc">Full API layer for countries, risk scores, ports, news, and currency — ready for AJAX and frontend integration.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="pw-feature-card text-center">
                    <div class="pw-feature-icon amber mx-auto mb-3"><i class="bi bi-cpu-fill"></i></div>
                    <div class="pw-feature-title">AI-Assisted Scoring</div>
                    <div class="pw-feature-desc">Lexicon-based sentiment analysis + weighted risk model classifies each country as LOW, MEDIUM, or HIGH risk in real time.</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     CTA BANNER
     ============================================================ --}}
<section class="pw-section pw-section-dark">
    <div class="container-xl">
        <div class="text-center" style="max-width:640px;margin:auto;">
            <div class="pw-section-eyebrow">START MONITORING</div>
            <h2 class="pw-section-h2">Ready to Secure Your<br>Supply Chain?</h2>
            <p class="pw-section-lead mx-auto mb-4">
                Create a free account and start monitoring global supply chain risks
                in under two minutes.
            </p>
            <div class="pw-hero-actions justify-content-center">
                <a href="{{ route('register') }}" class="pw-btn-cta">
                    <i class="bi bi-rocket-takeoff-fill"></i> Create Free Account
                </a>
                <a href="{{ route('login') }}" class="pw-btn-ghost">
                    Sign In
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     FOOTER
     ============================================================ --}}
<footer class="pw-footer">
    <div class="pw-footer-logo">⬡ PORTWATCH AI</div>
    <div class="pw-footer-copy">© {{ date('Y') }} PortWatch AI — Global Supply Chain Intelligence Platform</div>
    <div class="pw-footer-links">
        <a href="#features"    class="pw-footer-link">Features</a>
        <a href="#how-it-works" class="pw-footer-link">How It Works</a>
        <a href="#apis"        class="pw-footer-link">APIs</a>
        <a href="{{ route('login') }}" class="pw-footer-link">Login</a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
    });
});
</script>
</body>
</html>
