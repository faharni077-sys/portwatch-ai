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
            {{-- ── Section: Analisis Admin (selalu tampil jika ada) ── --}}
            @if(isset($adminArticles) && $adminArticles->isNotEmpty())
            <div class="pw-admin-articles mb-4">
                <div class="pw-section-title" style="color:#f59e0b;">
                    <i class="bi bi-shield-fill-check me-2" style="color:#f59e0b;"></i>
                    ANALISIS ADMIN
                    <span style="margin-left:8px;font-size:10px;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);
                                 color:#f59e0b;padding:2px 8px;border-radius:10px;letter-spacing:1px;">
                        {{ $adminArticles->count() }} ARTIKEL
                    </span>
                </div>

                @foreach($adminArticles as $art)
                <div class="pw-admin-article-card">
                    <div class="pw-aac-badge">
                        <i class="bi bi-shield-fill-check" style="font-size:10px;"></i>
                        ANALISIS ADMIN
                    </div>
                    <h3 class="pw-aac-title">{{ $art->title }}</h3>
                    <p class="pw-aac-content">{{ Str::limit(strip_tags($art->content), 220) }}</p>
                    <div class="pw-aac-meta">
                        <span>
                            <i class="bi bi-person-fill"></i>
                            {{ $art->author ?? 'Admin PortWatch' }}
                        </span>
                        <span>
                            <i class="bi bi-calendar3"></i>
                            {{ $art->created_at->translatedFormat('d M Y') }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- ── Section: Berita GNews (diisi oleh JS) ── --}}
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

    try {
        const q    = encodeURIComponent(query + ' ' + category);
        const from = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('.')[0] + 'Z';
        const url  = `/api/news?q=${q}&max=10&from=${encodeURIComponent(from)}`;
        const r    = await fetch(url);
        if (!r.ok) throw new Error();
        const data = await r.json();
        allArticles = (data.articles ?? []).map(a => ({
            title:       a.title,
            description: a.description ?? '',
            source:      a.source?.name ?? '—',
            url:         a.url,
            image:       a.image ?? null,
            publishedAt: a.publishedAt,
        }));
        if (!allArticles.length) throw new Error();
        renderNews(allArticles);
    } catch (e) {
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
        const sentLabel = result.sentiment.toUpperCase();

        /* ── tanggal ── */
        let dateStr = '—';
        if (a.publishedAt) {
            try {
                dateStr = new Date(a.publishedAt).toLocaleDateString('id-ID', {
                    day: '2-digit', month: 'short', year: 'numeric'
                });
            } catch (e) { dateStr = a.publishedAt.slice(0, 10); }
        }

        /* ── thumbnail ── */
        const placeholderThumb = `<div class="pw-nc-thumb-wrap pw-nc-thumb-placeholder">
            <i class="bi bi-newspaper"></i>
        </div>`;
        const imgHtml = a.image
            ? `<div class="pw-nc-thumb-wrap">
                   <img src="${a.image}" class="pw-nc-thumb"
                        onerror="this.parentNode.innerHTML='<i class=\'bi bi-newspaper pw-nc-ph-icon\'></i>';this.parentNode.classList.add('pw-nc-thumb-placeholder');" alt="">
               </div>`
            : placeholderThumb;

        const el = document.createElement('div');
        el.className = 'pw-news-card';
        if (a.url && a.url !== '#') {
            el.style.cursor = 'pointer';
            el.addEventListener('click', function(e) {
                if (e.target.closest('a')) return; // let native <a> handle its own clicks
                window.open(a.url, '_blank', 'noopener');
            });
        }
        el.innerHTML = `
            ${imgHtml}
            <div class="pw-nc-body">
                <div class="pw-nc-sentiment" style="background:${sentColor}22;border-color:${sentColor}44;color:${sentColor};">
                    ${sentIcon} ${sentLabel}
                </div>
                <a href="${a.url}" target="_blank" rel="noopener" class="pw-nc-title">
                    ${a.title}
                </a>
                <p class="pw-nc-desc">${a.description ? a.description.slice(0, 160) + (a.description.length > 160 ? '…' : '') : ''}</p>
                <div class="pw-nc-meta">
                    <span><i class="bi bi-rss" style="color:var(--pw-cyan);"></i> ${a.source}</span>
                    <span><i class="bi bi-calendar3" style="color:var(--pw-text-dim);"></i> ${dateStr}</span>
                    <a href="${a.url}" target="_blank" rel="noopener" class="pw-nc-read">
                        Baca selengkapnya <i class="bi bi-box-arrow-up-right"></i>
                    </a>
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
    const placeholder = 'https://placehold.co/480x240/0b1929/29c5ff?text=PortWatch+AI';
    return [
        { title: `Global ${q} sector sees record growth in Q3`, description: 'Exports and trade volumes increased significantly driven by strong demand across major shipping corridors.', source: 'Reuters', url: '#', image: placeholder, publishedAt: new Date().toISOString() },
        { title: `Supply chain disruptions impact ${q} operations`, description: 'Port congestion and shipping delays cause significant backlog across the global logistics network.', source: 'Bloomberg', url: '#', image: placeholder, publishedAt: new Date(Date.now() - 3600000).toISOString() },
        { title: `${q} inflation concerns rise amid currency volatility`, description: 'Economic sanctions and currency fluctuations create uncertainty for international trade partners.', source: 'Financial Times', url: '#', image: null, publishedAt: new Date(Date.now() - 7200000).toISOString() },
        { title: `${q} trade agreement boosts export confidence`, description: 'New bilateral agreement improves market access and reduces tariff barriers for major export commodities.', source: 'WSJ', url: '#', image: placeholder, publishedAt: new Date(Date.now() - 10800000).toISOString() },
        { title: `Shipping costs stabilize after months of disruption`, description: 'Freight rates improve as major ports recover from congestion backlog and vessel supply normalises.', source: 'Cargo Journal', url: '#', image: null, publishedAt: new Date(Date.now() - 14400000).toISOString() },
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

/* ── Admin Article Card ── */
.pw-admin-article-card {
    background: rgba(245,158,11,.05);
    border: 1px solid rgba(245,158,11,.25);
    border-left: 3px solid #f59e0b;
    border-radius: 12px;
    padding: 18px 20px;
    margin-bottom: 12px;
    transition: .22s ease;
}
.pw-admin-article-card:hover {
    background: rgba(245,158,11,.09);
    border-color: rgba(245,158,11,.45);
}
.pw-aac-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(245,158,11,.15); border: 1px solid rgba(245,158,11,.35);
    color: #f59e0b; font-size: 10px; font-weight: 700;
    letter-spacing: 1.5px; font-family: 'JetBrains Mono', monospace;
    padding: 2px 10px; border-radius: 20px; margin-bottom: 10px;
}
.pw-aac-title {
    font-size: 15px; font-weight: 700; color: #fff;
    line-height: 1.4; margin: 0 0 8px;
}
.pw-aac-content {
    font-size: 13px; color: var(--pw-text-dim);
    line-height: 1.65; margin: 0 0 12px;
}
.pw-aac-meta {
    display: flex; align-items: center; gap: 16px;
    flex-wrap: wrap; font-size: 12px; color: var(--pw-text-dim);
    padding-top: 10px; border-top: 1px solid rgba(245,158,11,.15);
}
.pw-aac-meta span { display: flex; align-items: center; gap: 5px; }
.pw-aac-meta i { color: #f59e0b; }

/* ── News Card ── */
.pw-news-card {
    display: flex;
    gap: 0;
    background: var(--pw-bg2);
    border: 1px solid var(--pw-border);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 14px;
    transition: .22s ease;
}
.pw-news-card:hover {
    border-color: var(--pw-border2);
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(0,0,0,.35);
}

/* thumbnail */
.pw-nc-thumb-wrap {
    flex-shrink: 0;
    width: 160px;
    display: flex;
    overflow: hidden;
    align-items: stretch;
}
.pw-nc-thumb-placeholder {
    align-items: center;
    justify-content: center;
    background: var(--pw-bg3);
    border-right: 1px solid var(--pw-border);
}
.pw-nc-thumb-placeholder .bi-newspaper,
.pw-nc-ph-icon {
    font-size: 32px;
    color: var(--pw-border2);
}
.pw-nc-thumb {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .3s ease;
}
.pw-news-card:hover .pw-nc-thumb { transform: scale(1.04); }

/* body */
.pw-nc-body {
    flex: 1;
    padding: 16px 18px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 0;
}

/* sentiment badge */
.pw-nc-sentiment {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    align-self: flex-start;
    padding: 3px 10px;
    border-radius: 20px;
    border: 1px solid transparent;
    font-size: 11px;
    font-weight: 700;
    font-family: 'JetBrains Mono', monospace;
    letter-spacing: 1px;
}

/* title */
.pw-nc-title {
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    text-decoration: none;
    line-height: 1.45;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.pw-nc-title:hover { color: var(--pw-cyan); }

/* description */
.pw-nc-desc {
    font-size: 13px;
    color: var(--pw-text-dim);
    line-height: 1.6;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* meta row */
.pw-nc-meta {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
    font-size: 12px;
    color: var(--pw-text-dim);
    margin-top: auto;
    padding-top: 6px;
    border-top: 1px solid var(--pw-border);
}
.pw-nc-meta span { display: flex; align-items: center; gap: 5px; }
.pw-nc-read {
    margin-left: auto;
    color: var(--pw-cyan);
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: .2s;
}
.pw-nc-read:hover { color: #7dd3fd; }

@media (max-width: 600px) {
    .pw-nc-thumb-wrap { display: none; }
    .pw-nc-body { padding: 14px; }
}
</style>
@endsection
