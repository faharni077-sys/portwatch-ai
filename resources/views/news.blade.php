@extends('layouts.app')
@section('title', 'News Intelligence')
@section('breadcrumb', 'NEWS INTELLIGENCE')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <div class="pw-section-title" style="margin-bottom:4px;">
            <i class="bi bi-newspaper me-2 text-cyan"></i>NEWS INTELLIGENCE FEED
        </div>
        <p style="color:var(--pw-text-dim);font-size:13px;margin:0;">
            GNews API · Lexicon-based sentiment analysis · Logistics, Trade, Shipping, Economy
        </p>
    </div>
</div>

{{-- Search & Filter --}}
<div class="pw-card mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">COUNTRY / KEYWORD</label>
            <input type="text" id="newsQuery" class="pw-input mt-1" placeholder="e.g. Germany, Indonesia logistics..." value="global logistics trade">
        </div>
        <div class="col-md-3">
            <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">CATEGORY</label>
            <select id="newsCategory" class="pw-select mt-1">
                <option value="logistics">Logistics</option>
                <option value="trade">Trade</option>
                <option value="shipping">Shipping</option>
                <option value="economy">Economy</option>
                <option value="supply chain">Supply Chain</option>
            </select>
        </div>
        <div class="col-md-3">
            <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">SENTIMENT FILTER</label>
            <select id="sentimentFilter" class="pw-select mt-1">
                <option value="">All Sentiment</option>
                <option value="Positive">Positive Only</option>
                <option value="Neutral">Neutral Only</option>
                <option value="Negative">Negative Only</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn-pw-primary w-100" onclick="fetchNews()">
                <i class="bi bi-broadcast me-1"></i> Scan
            </button>
        </div>
    </div>
</div>

{{-- Quick topics --}}
<div class="mb-4">
    <div style="font-size:11px;letter-spacing:2px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;margin-bottom:10px;">QUICK TOPICS</div>
    <div style="display:flex;flex-wrap:wrap;gap:8px;">
        @foreach(['global logistics','shipping disruption','port congestion','supply chain risk','trade war','currency crisis','inflation impact','export growth'] as $topic)
        <button class="pw-quick-btn" onclick="quickTopic('{{ $topic }}')">{{ $topic }}</button>
        @endforeach
    </div>
</div>

{{-- Main content --}}
<div id="newsResults" style="display:none;">

    {{-- Sentiment Summary --}}
    <div class="pw-stat-row mb-4" id="sentimentSummary">
        <div class="pw-card text-center" style="padding:18px;">
            <div style="font-size:32px;margin-bottom:6px;">✅</div>
            <div class="pw-card-label">POSITIVE</div>
            <div class="pw-card-value text-green" id="posCount">0</div>
            <div id="posBar" class="pw-progress mt-2"><div class="pw-progress-bar green" id="posBarFill" style="width:0%;"></div></div>
        </div>
        <div class="pw-card text-center" style="padding:18px;">
            <div style="font-size:32px;margin-bottom:6px;">➖</div>
            <div class="pw-card-label">NEUTRAL</div>
            <div class="pw-card-value" id="neuCount" style="color:var(--pw-amber);">0</div>
            <div class="pw-progress mt-2"><div class="pw-progress-bar amber" id="neuBarFill" style="width:0%;"></div></div>
        </div>
        <div class="pw-card text-center" style="padding:18px;">
            <div style="font-size:32px;margin-bottom:6px;">⚠️</div>
            <div class="pw-card-label">NEGATIVE</div>
            <div class="pw-card-value" id="negCount" style="color:var(--pw-red);">0</div>
            <div class="pw-progress mt-2"><div class="pw-progress-bar red" id="negBarFill" style="width:0%;"></div></div>
        </div>
        <div class="pw-card text-center" style="padding:18px;">
            <div style="font-size:32px;margin-bottom:6px;">🧠</div>
            <div class="pw-card-label">OVERALL SIGNAL</div>
            <div class="pw-card-value" id="overallSentiment" style="font-size:20px;">—</div>
        </div>
    </div>

    {{-- Layout: articles + chart --}}
    <div style="display:grid;grid-template-columns:1fr 320px;gap:16px;">

        {{-- Articles --}}
        <div>
            <div id="newsContainer"></div>
        </div>

        {{-- Sentiment Pie + Legend --}}
        <div>
            <div class="pw-card mb-3">
                <div class="pw-section-title"><i class="bi bi-pie-chart me-2 text-cyan"></i>SENTIMENT DISTRIBUTION</div>
                <div style="position:relative;height:200px;">
                    <canvas id="sentimentChart"></canvas>
                </div>
            </div>

            <div class="pw-card">
                <div class="pw-section-title"><i class="bi bi-cpu me-2 text-cyan"></i>AI ANALYSIS ENGINE</div>
                <div style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--pw-text-dim);line-height:2;">
                    <div>METHOD: <span style="color:#fff;">LEXICON-BASED</span></div>
                    <div>TYPE: <span style="color:#fff;">KEYWORD SCORING</span></div>
                    <div id="posWordsUsed">POS WORDS: —</div>
                    <div id="negWordsUsed">NEG WORDS: —</div>
                    <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--pw-border);color:var(--pw-text);font-size:12px;line-height:1.7;" id="aiSummaryText">
                        Run a scan to generate AI analysis summary.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Placeholder --}}
<div id="newsPlaceholder" style="text-align:center;padding:80px;color:var(--pw-text-dim);">
    <i class="bi bi-broadcast" style="font-size:56px;display:block;margin-bottom:16px;color:var(--pw-border);"></i>
    <div style="font-family:'JetBrains Mono',monospace;font-size:13px;margin-bottom:8px;">INTELLIGENCE FEED OFFLINE</div>
    <div style="font-size:13px;">Select a topic and click <strong style="color:var(--pw-cyan);">Scan</strong> to activate the news feed.</div>
</div>

<div id="newsLoading" style="display:none;text-align:center;padding:60px;color:var(--pw-text-dim);">
    <div style="font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--pw-cyan);">SCANNING INTELLIGENCE SOURCES...</div>
</div>

@endsection

@section('scripts')
<script>
// Lexicon — same as SentimentService.php
const POSITIVE_WORDS = ['growth','increase','success','profit','safe','stable','recover','improve','peace','surge','gain','boost','strong','rise','expand','agreement','deal','partner','positive','record'];
const NEGATIVE_WORDS = ['war','conflict','crisis','attack','risk','inflation','terror','disaster','decline','sanction','shortage','delay','strike','protest','collapse','ban','tariff','drop','fall','loss','flood','storm','disruption'];

function analyze(text) {
    const t = text.toLowerCase();
    let pos = 0, neg = 0;
    const posFound = [], negFound = [];
    POSITIVE_WORDS.forEach(w => { if (t.includes(w)) { pos++; posFound.push(w); }});
    NEGATIVE_WORDS.forEach(w => { if (t.includes(w)) { neg++; negFound.push(w); }});
    if (pos > neg) return { sentiment: 'Positive', pos, neg, posFound, negFound };
    if (neg > pos) return { sentiment: 'Negative', pos, neg, posFound, negFound };
    return { sentiment: 'Neutral', pos, neg, posFound, negFound };
}

let sentimentChartObj = null;
let allArticles = [];

async function fetchNews() {
    const query    = document.getElementById('newsQuery').value.trim() || 'logistics';
    const category = document.getElementById('newsCategory').value;

    document.getElementById('newsPlaceholder').style.display = 'none';
    document.getElementById('newsLoading').style.display = 'block';
    document.getElementById('newsResults').style.display = 'none';

    // Use GNews-formatted URL — if GNEWS_API_KEY not set, show sample data
    const apiKey = '{{ env("GNEWS_API_KEY", "") }}';

    if (!apiKey) {
        // Show sample data for demo
        allArticles = getSampleArticles(query);
        renderNews(allArticles);
        return;
    }

    try {
        const url = `https://gnews.io/api/v4/search?q=${encodeURIComponent(query + ' ' + category)}&lang=en&max=10&apikey=${apiKey}`;
        const r   = await fetch(url);
        if (!r.ok) throw new Error();
        const data = await r.json();
        allArticles = (data.articles ?? []).map(a => ({
            title: a.title,
            description: a.description ?? '',
            source: a.source?.name ?? '—',
            url: a.url,
            publishedAt: a.publishedAt,
        }));
        renderNews(allArticles);
    } catch(e) {
        allArticles = getSampleArticles(query);
        renderNews(allArticles);
    }
}

function quickTopic(topic) {
    document.getElementById('newsQuery').value = topic;
    fetchNews();
}

function renderNews(articles) {
    document.getElementById('newsLoading').style.display = 'none';
    document.getElementById('newsResults').style.display = 'block';

    const filter = document.getElementById('sentimentFilter').value;

    let pos = 0, neu = 0, neg = 0;
    let allPos = [], allNeg = [];
    const container = document.getElementById('newsContainer');
    container.innerHTML = '';

    articles.forEach(a => {
        const result = analyze(a.title + ' ' + a.description);
        a._sentiment = result.sentiment;
        if (result.sentiment === 'Positive') pos++;
        else if (result.sentiment === 'Neutral') neu++;
        else neg++;
        allPos.push(...result.posFound);
        allNeg.push(...result.negFound);

        if (filter && result.sentiment !== filter) return;

        const sentColor = result.sentiment === 'Positive' ? '#22c55e' :
                          result.sentiment === 'Negative' ? '#ef4444' : '#f59e0b';
        const sentIcon  = result.sentiment === 'Positive' ? '↑' :
                          result.sentiment === 'Negative' ? '↓' : '→';

        const el = document.createElement('div');
        el.className = 'pw-news-item mb-2';
        el.innerHTML = `
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                <div style="flex:1;">
                    <div class="pw-news-title">
                        <a href="${a.url ?? '#'}" target="_blank" style="color:#fff;text-decoration:none;">
                            ${a.title}
                        </a>
                    </div>
                    <div style="font-size:12px;color:var(--pw-text-dim);margin-top:4px;line-height:1.5;">
                        ${a.description ? a.description.slice(0, 120) + '...' : ''}
                    </div>
                    <div class="pw-news-meta mt-2">
                        <span style="color:var(--pw-text-dim);"><i class="bi bi-rss me-1"></i>${a.source}</span>
                        &nbsp;·&nbsp;
                        <span>${a.publishedAt ? new Date(a.publishedAt).toLocaleDateString('en-GB') : '—'}</span>
                    </div>
                </div>
                <div style="flex-shrink:0;text-align:center;padding:6px 12px;background:${sentColor}22;border:1px solid ${sentColor}44;border-radius:8px;">
                    <div style="font-size:18px;font-weight:800;color:${sentColor};">${sentIcon}</div>
                    <div style="font-size:10px;color:${sentColor};font-family:'JetBrains Mono',monospace;">${result.sentiment.toUpperCase()}</div>
                </div>
            </div>
        `;
        container.appendChild(el);
    });

    const total = pos + neu + neg;
    document.getElementById('posCount').textContent = pos;
    document.getElementById('neuCount').textContent = neu;
    document.getElementById('negCount').textContent = neg;
    document.getElementById('posBarFill').style.width = total ? ((pos/total)*100) + '%' : '0%';
    document.getElementById('neuBarFill').style.width = total ? ((neu/total)*100) + '%' : '0%';
    document.getElementById('negBarFill').style.width = total ? ((neg/total)*100) + '%' : '0%';

    const overall = pos > neg ? 'POSITIVE' : (neg > pos ? 'NEGATIVE' : 'NEUTRAL');
    const oColor  = pos > neg ? '#22c55e' : (neg > pos ? '#ef4444' : '#f59e0b');
    document.getElementById('overallSentiment').textContent = overall;
    document.getElementById('overallSentiment').style.color = oColor;

    // AI summary
    const uniquePos = [...new Set(allPos)].slice(0, 5);
    const uniqueNeg = [...new Set(allNeg)].slice(0, 5);
    document.getElementById('posWordsUsed').innerHTML = `POS WORDS: <span style="color:var(--pw-green);">${uniquePos.join(', ') || 'none'}</span>`;
    document.getElementById('negWordsUsed').innerHTML = `NEG WORDS: <span style="color:var(--pw-red);">${uniqueNeg.join(', ') || 'none'}</span>`;
    document.getElementById('aiSummaryText').innerHTML =
        `Analysis of ${total} articles: <strong style="color:${oColor};">${overall}</strong> overall sentiment. ` +
        (overall === 'POSITIVE' ? 'Market conditions appear favorable for logistics operations.' :
         overall === 'NEGATIVE' ? 'Elevated risk signals detected. Recommend monitoring supply chain exposure.' :
         'Mixed signals. Continue standard monitoring protocols.');

    // Pie chart
    renderSentimentChart(pos, neu, neg);
}

function renderSentimentChart(pos, neu, neg) {
    if (sentimentChartObj) sentimentChartObj.destroy();
    sentimentChartObj = new Chart(document.getElementById('sentimentChart'), {
        type: 'doughnut',
        data: {
            labels: ['Positive', 'Neutral', 'Negative'],
            datasets: [{
                data: [pos, neu, neg],
                backgroundColor: ['rgba(34,197,94,.7)', 'rgba(245,158,11,.7)', 'rgba(239,68,68,.7)'],
                borderColor:     ['#22c55e', '#f59e0b', '#ef4444'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#7a9ab8', font: { family: 'JetBrains Mono', size: 11 }, padding: 14 } }
            },
            cutout: '60%'
        }
    });
}

function getSampleArticles(q) {
    return [
        { title: `Global ${q} sector sees record growth in Q3`, description: 'Exports and trade volumes increased significantly driven by strong demand.', source: 'Reuters', url: '#', publishedAt: new Date().toISOString() },
        { title: `Supply chain disruptions impact ${q} operations`, description: 'Port congestion and shipping delays cause significant delays across the network.', source: 'Bloomberg', url: '#', publishedAt: new Date().toISOString() },
        { title: `${q} inflation concerns rise amid currency crisis`, description: 'Economic sanctions and currency volatility create uncertainty for trade partners.', source: 'FT', url: '#', publishedAt: new Date().toISOString() },
        { title: `${q} trade agreement boosts export confidence`, description: 'New bilateral agreement improves market access and reduces tariff barriers.', source: 'WSJ', url: '#', publishedAt: new Date().toISOString() },
        { title: `Shipping costs stabilize after months of disruption`, description: 'Freight rates improve as major ports recover from congestion backlog.', source: 'Cargo Journal', url: '#', publishedAt: new Date().toISOString() },
    ];
}

// Auto-fetch on load
window.addEventListener('load', () => fetchNews());

// Re-render on sentiment filter change
document.getElementById('sentimentFilter').addEventListener('change', () => {
    if (allArticles.length) renderNews(allArticles);
});
</script>

<style>
.pw-quick-btn {
    background: var(--pw-bg3); border: 1px solid var(--pw-border);
    color: var(--pw-text-dim); padding: 7px 14px; border-radius: 8px;
    font-size: 12px; cursor: pointer; transition: .2s;
}
.pw-quick-btn:hover { border-color: var(--pw-border2); color: var(--pw-cyan); }
</style>
@endsection
