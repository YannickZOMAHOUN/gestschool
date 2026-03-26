@extends('layouts.template')

@section('title', 'Tableau de bord — School Manager')
@section('breadcrumb', 'Tableau de bord')

@push('styles')
<style>
/* ── Tokens ──────────────────────────────────────────────── */
:root {
    --sg-bg:           #f6f7f3;
    --sg-bg-2:         #eef0ea;
    --sg-surface:      #ffffff;
    --sg-surface-2:    #f1f3ee;
    --sg-surface-3:    #e8ebe3;
    --sg-accent:       #3a6b35;
    --sg-accent-mid:   #5a9a52;
    --sg-accent-light: #eef4ec;
    --sg-accent-bg:    rgba(58,107,53,.07);
    --sg-accent-bg2:   rgba(58,107,53,.13);
    --sg-accent-glow:  rgba(58,107,53,.18);
    --sg-text:         #1c2318;
    --sg-text-2:       #4a5544;
    --sg-text-3:       #8a9880;
    --sg-border:       #e2e5da;
    --sg-border-2:     #cbd2c2;
    --sg-success:      #2e7d4f;
    --sg-success-bg:   rgba(46,125,79,.08);
    --sg-danger:       #a84040;
    --sg-danger-bg:    rgba(168,64,64,.07);
    --sg-warn:         #9a6a1a;
    --sg-warn-bg:      rgba(154,106,26,.08);
    --sg-info:         #2a6090;
    --sg-info-bg:      rgba(42,96,144,.08);
    --sg-font-display: Georgia, 'Times New Roman', serif;
    --sg-font-ui:      system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif;
    --sg-font-mono:    'Consolas', 'Courier New', monospace;
    --sg-radius:       10px;
    --sg-radius-sm:    7px;
    --sg-t:            .17s cubic-bezier(.4,0,.2,1);
    --sg-sh-xs:        0 1px 2px rgba(28,35,24,.04);
    --sg-sh-sm:        0 2px 8px rgba(28,35,24,.06);
    --sg-sh-md:        0 4px 18px rgba(28,35,24,.09);
}

/* ── Base ────────────────────────────────────────────────── */
.db, .db * { font-family: var(--sg-font-ui); box-sizing: border-box; }
.db { color: var(--sg-text); }

/* ── Animations ──────────────────────────────────────────── */
@keyframes db-up     { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }
@keyframes db-fade   { from { opacity:0; } to { opacity:1; } }
@keyframes db-pulse  { 0%,100%{ opacity:1; transform:scale(1); } 50%{ opacity:.55; transform:scale(.7); } }
@keyframes db-shimmer{ 0%{ background-position:-400px 0; } 100%{ background-position:400px 0; } }

.db-anim-1 { animation: db-up .38s ease both; }
.db-anim-2 { animation: db-up .38s .07s ease both; }
.db-anim-3 { animation: db-up .38s .14s ease both; }
.db-anim-4 { animation: db-up .38s .21s ease both; }
.db-anim-5 { animation: db-up .38s .28s ease both; }
.db-anim-6 { animation: db-up .38s .35s ease both; }

/* ── Hero banner ─────────────────────────────────────────── */
.db-hero {
    position: relative; overflow: hidden;
    background: linear-gradient(135deg, var(--sg-accent) 0%, var(--sg-accent-mid) 60%, #7bbf70 100%);
    border-radius: var(--sg-radius); padding: 1.75rem 2rem;
    margin-bottom: 1.5rem; color: #fff;
    box-shadow: 0 6px 24px var(--sg-accent-glow);
    animation: db-up .38s ease both;
}
.db-hero::before { content:''; position:absolute; right:-60px; top:-60px; width:260px; height:260px; border-radius:50%; background:rgba(255,255,255,.07); pointer-events:none; }
.db-hero::after  { content:''; position:absolute; right:80px; bottom:-80px; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,.05); pointer-events:none; }
.db-hero-inner   { position:relative; z-index:1; display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap; }
.db-hero-icon    { width:58px; height:58px; border-radius:14px; flex-shrink:0; background:rgba(255,255,255,.18); backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,.25); display:flex; align-items:center; justify-content:center; font-size:1.45rem; }
.db-hero-text    { flex:1; min-width:0; }
.db-hero-greeting{ font-size:.7rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; opacity:.72; margin-bottom:.22rem; }
.db-hero-name    { font-family:var(--sg-font-display); font-size:1.45rem; font-weight:700; letter-spacing:-.02em; margin:0 0 .2rem; line-height:1.2; }
.db-hero-meta    { font-size:.78rem; opacity:.75; display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; }
.db-hero-meta-dot{ width:3px; height:3px; border-radius:50%; background:rgba(255,255,255,.7); }
.db-hero-right   { margin-left:auto; flex-shrink:0; }
.db-hero-date    { background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.25); border-radius:var(--sg-radius-sm); padding:.55rem 1.05rem; font-size:.77rem; font-weight:600; text-align:center; backdrop-filter:blur(6px); }
.db-hero-date-day{ font-family:var(--sg-font-display); font-size:1.6rem; font-weight:700; letter-spacing:-.04em; line-height:1; display:block; }
.db-hero-date-month{ font-size:.62rem; opacity:.8; letter-spacing:.06em; text-transform:uppercase; }

/* ── Année active badge ───────────────────────────────────── */
.db-year-badge {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(255,255,255,.22); border: 1px solid rgba(255,255,255,.3);
    border-radius: 20px; padding: 3px 10px;
    font-size: .7rem; font-weight: 700; color: #fff;
    backdrop-filter: blur(6px);
}

/* ── Stat cards ──────────────────────────────────────────── */
.db-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem; }
@media(max-width:900px){ .db-stats{ grid-template-columns:repeat(2,1fr); } }
@media(max-width:520px) { .db-stats{ grid-template-columns:1fr; } }

.db-stat { background:var(--sg-surface); border:1px solid var(--sg-border); border-radius:var(--sg-radius); padding:1.15rem 1.2rem; box-shadow:var(--sg-sh-xs); position:relative; overflow:hidden; transition:box-shadow var(--sg-t),border-color var(--sg-t),transform var(--sg-t); cursor:default; }
.db-stat:hover { box-shadow:var(--sg-sh-sm); border-color:var(--sg-border-2); transform:translateY(-2px); }
.db-stat::before { content:''; position:absolute; left:0; top:0; bottom:0; width:3px; border-radius:0 2px 2px 0; background:var(--stat-color,var(--sg-accent)); }
.db-stat-icon-wrap { width:38px; height:38px; border-radius:9px; margin-bottom:.8rem; display:flex; align-items:center; justify-content:center; background:var(--stat-bg,var(--sg-accent-bg)); border:1px solid var(--stat-border,rgba(58,107,53,.15)); color:var(--stat-color,var(--sg-accent)); font-size:.9rem; }
.db-stat-label { font-size:.6rem; font-weight:700; text-transform:uppercase; letter-spacing:.12em; color:var(--sg-text-3); margin-bottom:.32rem; }
.db-stat-value { font-family:var(--sg-font-display); font-size:1.85rem; font-weight:700; color:var(--sg-text); letter-spacing:-.045em; line-height:1; }
.db-stat-value span { color:var(--stat-color,var(--sg-accent)); }
.db-stat-sub { display:flex; align-items:center; gap:.32rem; font-size:.67rem; font-weight:500; margin-top:.38rem; color:var(--sg-text-3); }
.db-stat-sub.up   { color:var(--sg-success); }
.db-stat-sub.down { color:var(--sg-danger); }

/* ── Main grid ───────────────────────────────────────────── */
.db-grid { display:grid; grid-template-columns:1fr 340px; gap:1.25rem; align-items:start; }
@media(max-width:860px){ .db-grid{ grid-template-columns:1fr; } }

/* ── Card ────────────────────────────────────────────────── */
.db-card { background:var(--sg-surface); border:1px solid var(--sg-border); border-radius:var(--sg-radius); box-shadow:var(--sg-sh-xs); overflow:hidden; }
.db-card-hdr { display:flex; align-items:center; justify-content:space-between; gap:.75rem; padding:.9rem 1.1rem; border-bottom:1px solid var(--sg-border); background:var(--sg-surface-2); }
.db-card-title { font-family:var(--sg-font-display); font-size:.88rem; font-weight:600; color:var(--sg-text); display:flex; align-items:center; gap:.48rem; margin:0; }
.db-card-title i { color:var(--sg-accent); font-size:.82rem; }
.db-card-body { padding:1.1rem; }
.db-card-link { font-size:.71rem; font-weight:600; color:var(--sg-accent); text-decoration:none; display:flex; align-items:center; gap:.3rem; transition:color var(--sg-t); }
.db-card-link:hover { color:var(--sg-accent-mid); }

/* ── Live indicator ──────────────────────────────────────── */
.db-live { display:flex; align-items:center; gap:.38rem; font-size:.64rem; font-weight:700; color:var(--sg-success); }
.db-live-dot { width:6px; height:6px; border-radius:50%; background:var(--sg-success); box-shadow:0 0 5px rgba(46,125,79,.55); animation:db-pulse 2.4s ease-in-out infinite; }

/* ── Table ───────────────────────────────────────────────── */
.db-table { width:100%; border-collapse:collapse; font-size:.77rem; }
.db-table thead th { font-size:.58rem; text-transform:uppercase; letter-spacing:.12em; color:var(--sg-text-3); padding:.45rem .75rem; border-bottom:1px solid var(--sg-border); font-weight:700; text-align:left; }
.db-table tbody td { padding:.62rem .75rem; color:var(--sg-text-2); border-bottom:1px solid var(--sg-bg-2); vertical-align:middle; }
.db-table tbody tr:last-child td { border-bottom:none; }
.db-table tbody tr:hover td { background:var(--sg-surface-2); }

/* Avatar */
.db-av-cell { display:flex; align-items:center; gap:.6rem; }
.db-av { width:30px; height:30px; border-radius:8px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:.6rem; font-weight:700; }

/* Badge */
.db-badge { display:inline-flex; align-items:center; gap:.28rem; font-size:.61rem; font-weight:700; padding:2px 7px; border-radius:20px; border:1px solid; }
.db-badge-success { background:var(--sg-success-bg); border-color:rgba(46,125,79,.2);  color:var(--sg-success); }
.db-badge-warn    { background:var(--sg-warn-bg);    border-color:rgba(154,106,26,.2); color:var(--sg-warn); }
.db-badge-danger  { background:var(--sg-danger-bg);  border-color:rgba(168,64,64,.2);  color:var(--sg-danger); }
.db-badge-info    { background:var(--sg-info-bg);    border-color:rgba(42,96,144,.2);  color:var(--sg-info); }
.db-badge-accent  { background:var(--sg-accent-bg2); border-color:rgba(58,107,53,.2);  color:var(--sg-accent); }
.db-badge-neutral { background:var(--sg-surface-3);  border-color:var(--sg-border-2);  color:var(--sg-text-3); }

/* Donut */
.db-donut-wrap { display:flex; align-items:center; gap:1.25rem; padding:.85rem 0; }
.db-donut { position:relative; width:110px; height:110px; flex-shrink:0; }
.db-donut svg { transform:rotate(-90deg); }
.db-donut-center { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.db-donut-val { font-family:var(--sg-font-display); font-size:1.45rem; font-weight:700; color:var(--sg-text); line-height:1; letter-spacing:-.04em; }
.db-donut-label { font-size:.58rem; color:var(--sg-text-3); text-transform:uppercase; letter-spacing:.1em; margin-top:2px; }
.db-donut-legend { flex:1; display:flex; flex-direction:column; gap:.52rem; }
.db-legend-item { display:flex; align-items:center; gap:.55rem; font-size:.74rem; }
.db-legend-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.db-legend-label { color:var(--sg-text-2); flex:1; }
.db-legend-val { font-family:var(--sg-font-mono); font-size:.72rem; color:var(--sg-text); font-weight:600; }

/* Progress */
.db-progress-wrap { margin-bottom:.85rem; }
.db-progress-header { display:flex; justify-content:space-between; align-items:baseline; margin-bottom:.35rem; }
.db-progress-label { font-size:.74rem; font-weight:500; color:var(--sg-text-2); }
.db-progress-pct { font-family:var(--sg-font-mono); font-size:.7rem; color:var(--sg-text-3); font-weight:500; }
.db-progress-track { height:6px; background:var(--sg-surface-3); border-radius:99px; overflow:hidden; }
.db-progress-fill { height:100%; border-radius:99px; background:var(--prog-color,var(--sg-accent)); transition:width 1s cubic-bezier(.4,0,.2,1) .3s; width:0; }

/* Quick actions */
.db-actions { display:grid; grid-template-columns:1fr 1fr; gap:.65rem; }
.db-action-btn { display:flex; align-items:center; gap:.65rem; padding:.78rem .9rem; border-radius:var(--sg-radius-sm); border:1px solid var(--sg-border); background:var(--sg-surface-2); text-decoration:none; color:var(--sg-text-2); transition:all var(--sg-t); cursor:pointer; }
.db-action-btn:hover { border-color:rgba(58,107,53,.28); background:var(--sg-accent-light); color:var(--sg-accent); transform:translateY(-1px); box-shadow:0 3px 10px var(--sg-accent-glow); }
.db-action-icon { width:34px; height:34px; border-radius:8px; flex-shrink:0; display:flex; align-items:center; justify-content:center; background:var(--sg-surface); border:1px solid var(--sg-border); font-size:.82rem; color:var(--sg-accent); transition:all var(--sg-t); }
.db-action-btn:hover .db-action-icon { background:var(--sg-accent); color:#fff; border-color:var(--sg-accent); box-shadow:0 3px 8px var(--sg-accent-glow); }
.db-action-text { display:flex; flex-direction:column; gap:2px; min-width:0; }
.db-action-label { font-size:.77rem; font-weight:600; line-height:1.2; }
.db-action-sub   { font-size:.63rem; color:var(--sg-text-3); }
.db-action-btn:hover .db-action-sub { color:var(--sg-accent-mid); }

/* Feed */
.db-feed { display:flex; flex-direction:column; gap:0; }
.db-feed-item { display:flex; gap:.72rem; padding:.72rem 0; border-bottom:1px solid var(--sg-border); }
.db-feed-item:last-child { border-bottom:none; padding-bottom:0; }
.db-feed-timeline { display:flex; flex-direction:column; align-items:center; flex-shrink:0; width:28px; }
.db-feed-dot { width:28px; height:28px; border-radius:8px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:.7rem; color:#fff; background:var(--feed-color,var(--sg-accent)); }
.db-feed-line { flex:1; width:1px; background:var(--sg-border); margin:3px 0; min-height:8px; }
.db-feed-content { flex:1; min-width:0; }
.db-feed-action { font-size:.77rem; font-weight:500; color:var(--sg-text); margin-bottom:.12rem; line-height:1.4; }
.db-feed-action strong { color:var(--sg-accent); }
.db-feed-time { font-size:.65rem; color:var(--sg-text-3); display:flex; align-items:center; gap:.3rem; }
.db-feed-empty { text-align:center; padding:1.5rem; color:var(--sg-text-3); font-size:.8rem; }

/* Semester */
.db-sem-card { background:var(--sg-surface); border:1px solid var(--sg-border); border-radius:var(--sg-radius); padding:1rem 1.1rem; box-shadow:var(--sg-sh-xs); margin-bottom:1.25rem; }
.db-sem-hdr  { display:flex; justify-content:space-between; align-items:baseline; margin-bottom:.6rem; }
.db-sem-title{ font-family:var(--sg-font-display); font-size:.84rem; font-weight:600; color:var(--sg-text); }
.db-sem-pct  { font-family:var(--sg-font-mono); font-size:.72rem; color:var(--sg-accent); font-weight:700; }
.db-sem-track{ height:8px; background:var(--sg-surface-3); border-radius:99px; overflow:hidden; }
.db-sem-fill { height:100%; border-radius:99px; background:linear-gradient(90deg,var(--sg-accent) 0%,var(--sg-accent-mid) 100%); transition:width 1.2s cubic-bezier(.4,0,.2,1) .4s; width:0; }
.db-sem-sub  { display:flex; justify-content:space-between; margin-top:.4rem; font-size:.64rem; color:var(--sg-text-3); }

/* Aucune donnée */
.db-no-year { text-align:center; padding:2rem; color:var(--sg-text-3); font-size:.82rem; }
.db-no-year i { font-size:1.5rem; display:block; margin-bottom:.5rem; color:var(--sg-warn); }
</style>
@endpush

@section('content')
@php
    $palette = ['#3a6b35','#5a9a52','#2a6090','#9a6a1a','#2e7d4f','#a84040'];
    $userName = $user ? trim("{$user->surname} {$user->name}") : 'Utilisateur';
@endphp

<div class="db">

    {{-- ── Hero ──────────────────────────────────────────────── --}}
    <div class="db-hero db-anim-1">
        <div class="db-hero-inner">
            <div class="db-hero-icon">🏫</div>
            <div class="db-hero-text">
                <div class="db-hero-greeting">
                    @php $h = $now->hour; echo $h < 12 ? 'Bonjour' : ($h < 18 ? 'Bon après-midi' : 'Bonsoir'); @endphp,
                </div>
                <h1 class="db-hero-name">{{ $userName }}</h1>
                <div class="db-hero-meta">
                    <span>{{ $now->isoFormat('dddd D MMMM YYYY') }}</span>
                    <span class="db-hero-meta-dot"></span>
                    <span>Lycée Technique de Bohicon</span>
                    @if($activeYear)
                        <span class="db-hero-meta-dot"></span>
                        <span class="db-year-badge">
                            <i class="fas fa-calendar-check" style="font-size:.6rem;"></i>
                            Année {{ $activeYear->year }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="db-hero-right">
                <div class="db-hero-date">
                    <span class="db-hero-date-day" id="heroClock">{{ $now->format('H:i') }}</span>
                    <span class="db-hero-date-month">{{ strtoupper($now->isoFormat('ddd')) }}</span>
                </div>
            </div>
        </div>
    </div>

    @if(!$activeYear)
        <div class="db-no-year">
            <i class="fas fa-exclamation-triangle"></i>
            Aucune année scolaire active. Veuillez en <a href="{{ route('year.create') }}">créer une</a>.
        </div>
    @else

    {{-- ── Barre avancement semestre ────────────────────────── --}}
    <div class="db-sem-card db-anim-2">
        <div class="db-sem-hdr">
            <span class="db-sem-title">
                <i class="fas fa-hourglass-half" style="color:var(--sg-accent);margin-right:.4rem;font-size:.78rem;"></i>
                Progression — {{ $semLabel }} · {{ $activeYear->year }}
            </span>
            <span class="db-sem-pct" id="semPctLabel">{{ $semPct }}%</span>
        </div>
        <div class="db-sem-track">
            <div class="db-sem-fill" id="semFill" data-pct="{{ $semPct }}"></div>
        </div>
        <div class="db-sem-sub">
            <span>Début : {{ $semStart->isoFormat('D MMM YYYY') }}</span>
            <span>{{ $daysLeft }} jour(s) restant(s)</span>
            <span>Fin : {{ $semEnd->isoFormat('D MMM YYYY') }}</span>
        </div>
    </div>

    {{-- ── 4 Stat cards ─────────────────────────────────────── --}}
    <div class="db-stats">

        {{-- Élèves --}}
        <a href="{{ route('student.index') }}" style="text-decoration:none;">
            <div class="db-stat db-anim-2" style="--stat-color:var(--sg-accent);--stat-bg:var(--sg-accent-bg);--stat-border:rgba(58,107,53,.15);">
                <div class="db-stat-icon-wrap"><i class="fas fa-user-graduate"></i></div>
                <div class="db-stat-label">Élèves inscrits</div>
                <div class="db-stat-value"><span>{{ number_format($totalStudents) }}</span></div>
                <div class="db-stat-sub up">
                    <i class="fas fa-circle" style="font-size:.45rem;"></i>
                    Année {{ $activeYear->year }}
                </div>
            </div>
        </a>

        {{-- Enseignants --}}
        <a href="{{ route('teacher-assignments.index') }}" style="text-decoration:none;">
            <div class="db-stat db-anim-3" style="--stat-color:var(--sg-info);--stat-bg:var(--sg-info-bg);--stat-border:rgba(42,96,144,.15);">
                <div class="db-stat-icon-wrap" style="color:var(--sg-info);"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="db-stat-label">Enseignants actifs</div>
                <div class="db-stat-value"><span style="color:var(--sg-info)">{{ number_format($totalTeachers) }}</span></div>
                <div class="db-stat-sub">
                    <i class="fas fa-circle" style="font-size:.45rem;color:var(--sg-success);"></i>
                    Affectés cette année
                </div>
            </div>
        </a>

        {{-- Classes --}}
        <a href="{{ route('promotion-classrooms.create') }}" style="text-decoration:none;">
            <div class="db-stat db-anim-4" style="--stat-color:var(--sg-warn);--stat-bg:var(--sg-warn-bg);--stat-border:rgba(154,106,26,.15);">
                <div class="db-stat-icon-wrap" style="color:var(--sg-warn);"><i class="fas fa-door-open"></i></div>
                <div class="db-stat-label">Classes actives</div>
                <div class="db-stat-value"><span style="color:var(--sg-warn)">{{ number_format($totalClassrooms) }}</span></div>
                <div class="db-stat-sub">
                    <i class="fas fa-sitemap" style="font-size:.5rem;"></i>
                    Toutes filières
                </div>
            </div>
        </a>

        {{-- Matières --}}
        <a href="{{ route('ratio.index') }}" style="text-decoration:none;">
            <div class="db-stat db-anim-5" style="--stat-color:var(--sg-success);--stat-bg:var(--sg-success-bg);--stat-border:rgba(46,125,79,.15);">
                <div class="db-stat-icon-wrap" style="color:var(--sg-success);"><i class="fas fa-book-open"></i></div>
                <div class="db-stat-label">Matières enseignées</div>
                <div class="db-stat-value"><span style="color:var(--sg-success)">{{ number_format($totalSubjects) }}</span></div>
                <div class="db-stat-sub">
                    <i class="fas fa-layer-group" style="font-size:.5rem;"></i>
                    Tous niveaux
                </div>
            </div>
        </a>

    </div>

    {{-- ── Grille principale ────────────────────────────────── --}}
    <div class="db-grid">

        {{-- ═══ Colonne gauche ═══ --}}
        <div style="display:flex;flex-direction:column;gap:1.25rem;">

            {{-- Tableau derniers utilisateurs --}}
            <div class="db-card db-anim-3">
                <div class="db-card-hdr">
                    <h2 class="db-card-title">
                        <i class="fas fa-users"></i> Utilisateurs récents
                        <span class="db-live"><span class="db-live-dot"></span>Live</span>
                    </h2>
                    <a href="{{ route('user.index') }}" class="db-card-link">
                        Voir tous <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div style="overflow-x:auto;">
                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Rôle</th>
                                <th>Contact</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestUsers as $i => $u)
                            @php
                                $role      = optional($u->roles->first())->name ?? 'N/A';
                                $roleSlug  = strtolower($role);
                                $initials  = strtoupper(mb_substr($u->name??'?',0,1).mb_substr($u->surname??'',0,1));
                                $avColor   = $palette[$u->id % count($palette)];
                                $badgeClass = match(true) {
                                    str_contains($roleSlug,'enseignant') || str_contains($roleSlug,'professeur') => 'db-badge-info',
                                    str_contains($roleSlug,'censeur') || str_contains($roleSlug,'proviseur') || str_contains($roleSlug,'directeur') => 'db-badge-success',
                                    str_contains($roleSlug,'surveillant') => 'db-badge-warn',
                                    str_contains($roleSlug,'admin') => 'db-badge-danger',
                                    default => 'db-badge-accent',
                                };
                            @endphp
                            <tr style="animation:db-up .3s ease {{ $i*40 }}ms both;">
                                <td>
                                    <div class="db-av-cell">
                                        <div class="db-av" style="background:color-mix(in srgb,{{ $avColor }} 14%,#f1f3ee);color:{{ $avColor }};border:1px solid color-mix(in srgb,{{ $avColor }} 26%,transparent);">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <div style="font-size:.8rem;font-weight:600;color:var(--sg-text);">{{ $u->name }} {{ $u->surname }}</div>
                                            <div style="font-family:var(--sg-font-mono);font-size:.66rem;color:var(--sg-text-3);">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="db-badge {{ $badgeClass }}">{{ $role }}</span></td>
                                <td style="font-family:var(--sg-font-mono);font-size:.72rem;color:var(--sg-text-3);">{{ $u->phone ?? '—' }}</td>
                                <td><span class="db-badge db-badge-success"><i class="fas fa-circle" style="font-size:.4rem;"></i> Actif</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="text-align:center;padding:2rem;color:var(--sg-text-3);font-size:.79rem;">
                                    Aucun utilisateur enregistré
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Répartition par rôle --}}
            <div class="db-card db-anim-4">
                <div class="db-card-hdr">
                    <h2 class="db-card-title"><i class="fas fa-chart-pie"></i> Répartition du personnel</h2>
                    <a href="{{ route('user.index') }}" class="db-card-link">Gérer <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="db-card-body">
                    <div class="db-donut-wrap">
                        <div class="db-donut">
                            <svg width="110" height="110" viewBox="0 0 110 110">
                                @php $cx=55;$cy=55;$r=40;$stroke=14;$circumference=2*M_PI*$r;$offset=0; @endphp
                                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none" stroke="var(--sg-surface-3)" stroke-width="{{ $stroke }}"/>
                                @foreach($roleList as $idx => $item)
                                @php
                                    $dash = $circumference * $item['pct'] / 100;
                                    $gap  = $circumference - $dash;
                                    $color = $roleColors[$idx % count($roleColors)];
                                @endphp
                                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none"
                                    stroke="{{ $color }}" stroke-width="{{ $stroke }}"
                                    stroke-dasharray="{{ number_format($dash,2) }} {{ number_format($gap,2) }}"
                                    stroke-dashoffset="{{ number_format(-$offset,2) }}"
                                    stroke-linecap="round"/>
                                @php $offset += $dash; @endphp
                                @endforeach
                            </svg>
                            <div class="db-donut-center">
                                <span class="db-donut-val">{{ $totalUsers }}</span>
                                <span class="db-donut-label">Total</span>
                            </div>
                        </div>
                        <div class="db-donut-legend">
                            @foreach($roleList as $idx => $item)
                            <div class="db-legend-item">
                                <div class="db-legend-dot" style="background:{{ $roleColors[$idx % count($roleColors)] }}"></div>
                                <span class="db-legend-label">{{ $item['name'] }}</span>
                                <span class="db-legend-val">{{ $item['count'] }}</span>
                            </div>
                            @endforeach
                            @if($roleList->isEmpty())
                            <div style="color:var(--sg-text-3);font-size:.78rem;">Aucun utilisateur</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ═══ Colonne droite ═══ --}}
        <div style="display:flex;flex-direction:column;gap:1.25rem;">

            {{-- Accès rapides --}}
            <div class="db-card db-anim-3">
                <div class="db-card-hdr">
                    <h2 class="db-card-title"><i class="fas fa-bolt"></i> Accès rapides</h2>
                </div>
                <div class="db-card-body">
                    <div class="db-actions">

                        <a href="{{ route('note.create') }}" class="db-action-btn">
                            <div class="db-action-icon"><i class="fas fa-pen-alt"></i></div>
                            <div class="db-action-text">
                                <span class="db-action-label">Saisir des notes</span>
                                <span class="db-action-sub">{{ $activeYear->year }}</span>
                            </div>
                        </a>

                        <a href="{{ route('note.index') }}" class="db-action-btn">
                            <div class="db-action-icon"><i class="fas fa-list-ol"></i></div>
                            <div class="db-action-text">
                                <span class="db-action-label">Consulter les notes</span>
                                <span class="db-action-sub">Résultats & rangs</span>
                            </div>
                        </a>

                        <a href="{{ route('get.cards') }}" class="db-action-btn">
                            <div class="db-action-icon"><i class="fas fa-file-alt"></i></div>
                            <div class="db-action-text">
                                <span class="db-action-label">Bulletins</span>
                                <span class="db-action-sub">Générer / imprimer</span>
                            </div>
                        </a>

                        <a href="{{ route('export_view') }}" class="db-action-btn">
                            <div class="db-action-icon"><i class="fas fa-file-excel"></i></div>
                            <div class="db-action-text">
                                <span class="db-action-label">Exporter les notes</span>
                                <span class="db-action-sub">Format XLSX</span>
                            </div>
                        </a>

                        <a href="{{ route('notes.import') }}" class="db-action-btn">
                            <div class="db-action-icon"><i class="fas fa-file-import"></i></div>
                            <div class="db-action-text">
                                <span class="db-action-label">Importer les notes</span>
                                <span class="db-action-sub">CSV / XLS / XLSX</span>
                            </div>
                        </a>

                        <a href="{{ route('teacher-assignments.index') }}" class="db-action-btn">
                            <div class="db-action-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                            <div class="db-action-text">
                                <span class="db-action-label">Affectations</span>
                                <span class="db-action-sub">Enseignants / classes</span>
                            </div>
                        </a>

                        <a href="{{ route('principal-teachers.index') }}" class="db-action-btn">
                            <div class="db-action-icon"><i class="fas fa-star"></i></div>
                            <div class="db-action-text">
                                <span class="db-action-label">Profs principaux</span>
                                <span class="db-action-sub">Désigner / retirer</span>
                            </div>
                        </a>

                        <a href="{{ route('student.index') }}" class="db-action-btn">
                            <div class="db-action-icon"><i class="fas fa-user-graduate"></i></div>
                            <div class="db-action-text">
                                <span class="db-action-label">Élèves</span>
                                <span class="db-action-sub">Gérer les inscriptions</span>
                            </div>
                        </a>

                        <a href="{{ route('user.create') }}" class="db-action-btn">
                            <div class="db-action-icon"><i class="fas fa-user-plus"></i></div>
                            <div class="db-action-text">
                                <span class="db-action-label">Nouvel utilisateur</span>
                                <span class="db-action-sub">Créer un compte</span>
                            </div>
                        </a>

                        <a href="{{ route('ratio.index') }}" class="db-action-btn">
                            <div class="db-action-icon"><i class="fas fa-layer-group"></i></div>
                            <div class="db-action-text">
                                <span class="db-action-label">Coefficients</span>
                                <span class="db-action-sub">Ratios matières</span>
                            </div>
                        </a>

                    </div>
                </div>
            </div>

            {{-- Progression des notes par filière --}}
            <div class="db-card db-anim-4">
                <div class="db-card-hdr">
                    <h2 class="db-card-title">
                        <i class="fas fa-chart-bar"></i> Notes saisies par filière
                    </h2>
                    <span style="font-size:.68rem;color:var(--sg-text-3);">{{ $activeYear->year }}</span>
                </div>
                <div class="db-card-body">
                    @forelse($filieresProgress as $idx => $f)
                    <div class="db-progress-wrap" style="animation:db-up .3s ease {{ $idx*60 }}ms both;">
                        <div class="db-progress-header">
                            <span class="db-progress-label">{{ $f['name'] }}</span>
                            <span class="db-progress-pct">{{ $f['pct'] }}%</span>
                        </div>
                        <div class="db-progress-track">
                            <div class="db-progress-fill"
                                 data-pct="{{ $f['pct'] }}"
                                 style="--prog-color:{{ $progressColors[$idx % count($progressColors)] }};">
                            </div>
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:1.5rem;color:var(--sg-text-3);font-size:.8rem;">
                        <i class="fas fa-inbox" style="display:block;font-size:1.3rem;margin-bottom:.5rem;"></i>
                        Aucune filière configurée pour cette année.
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Activité récente : vraies dernières notes --}}
            <div class="db-card db-anim-5">
                <div class="db-card-hdr">
                    <h2 class="db-card-title"><i class="fas fa-history"></i> Dernières saisies</h2>
                    <a href="{{ route('note.index') }}" class="db-card-link">
                        Voir tout <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="db-card-body" style="padding-top:.6rem;padding-bottom:.6rem;">
                    <div class="db-feed">
                        @forelse($recentNotes as $i => $note)
                        @php
                            $student   = $note->recording->student ?? null;
                            $classroom = $note->recording->classroom ?? null;
                            $subject   = $note->subject ?? null;
                            $teacher   = null; // pas de relation directe Note→User dans le modèle fourni
                            $interros  = is_array($note->interros) ? $note->interros : (json_decode($note->interros, true) ?? []);
                            $moyI      = count($interros) > 0 ? round(array_sum($interros)/count($interros),2) : null;
                            $feedColors = ['#3a6b35','#2a6090','#2e7d4f','#9a6a1a','#5d4a8a'];
                            $color = $feedColors[$i % count($feedColors)];
                        @endphp
                        <div class="db-feed-item" style="animation:db-up .3s ease {{ $i*55 }}ms both;">
                            <div class="db-feed-timeline">
                                <div class="db-feed-dot" style="--feed-color:{{ $color }}">
                                    <i class="fas fa-pen-alt" style="font-size:.55rem;"></i>
                                </div>
                                @if(!$loop->last)<div class="db-feed-line"></div>@endif
                            </div>
                            <div class="db-feed-content">
                                <div class="db-feed-action">
                                    Note saisie —
                                    <strong>{{ $student?->name }} {{ $student?->surname }}</strong>
                                    @if($subject) · {{ $subject->name }} @endif
                                    @if($note->devoir1 !== null)
                                        <span style="font-family:var(--sg-font-mono);font-size:.7rem;background:var(--sg-accent-bg);color:var(--sg-accent);padding:1px 5px;border-radius:4px;">
                                            D1 : {{ number_format($note->devoir1,2) }}
                                        </span>
                                    @endif
                                    @if($moyI !== null)
                                        <span style="font-family:var(--sg-font-mono);font-size:.7rem;background:var(--sg-info-bg);color:var(--sg-info);padding:1px 5px;border-radius:4px;">
                                            Moy.I : {{ $moyI }}
                                        </span>
                                    @endif
                                </div>
                                <div class="db-feed-time">
                                    <i class="fas fa-clock" style="font-size:.55rem;"></i>
                                    S{{ $note->semester }} · {{ $note->updated_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="db-feed-empty">
                            <i class="fas fa-inbox" style="display:block;font-size:1.2rem;margin-bottom:.4rem;"></i>
                            Aucune note saisie pour l'année {{ $activeYear->year }}.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    @endif {{-- $activeYear --}}

</div>
@endsection

@section('another_JS')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Horloge hero ─────────────────────────────────────────
    function tickClock() {
        const el = document.getElementById('heroClock');
        if (el) {
            const d = new Date();
            el.textContent = d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        }
    }
    tickClock();
    setInterval(tickClock, 30000);

    // ── Animer la barre du semestre ───────────────────────────
    const semFill = document.getElementById('semFill');
    if (semFill) {
        setTimeout(() => {
            semFill.style.width = semFill.dataset.pct + '%';
        }, 300);
    }

    // ── Animer toutes les barres de progression ───────────────
    document.querySelectorAll('.db-progress-fill[data-pct]').forEach((el, i) => {
        setTimeout(() => {
            el.style.width = el.dataset.pct + '%';
        }, 400 + i * 80);
    });

});
</script>
@endsection
