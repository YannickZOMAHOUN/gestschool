@extends('layouts.template')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">

<div class="db-root">

    {{-- ░░░ FOND AMBIANT ░░░ --}}
    <div class="db-bg" aria-hidden="true">
        <div class="db-orb db-orb-1"></div>
        <div class="db-orb db-orb-2"></div>
        <div class="db-orb db-orb-3"></div>
        <div class="db-grid-lines"></div>
    </div>

    {{-- ░░░ HERO ░░░ --}}
    <section class="db-hero">
        <div class="db-hero-left">
            <div class="db-hello-badge">
                <span class="db-hello-dot"></span>
                <span>Système en ligne</span>
            </div>
            <h1 class="db-hero-title">
                Bonjour,<br>
                <span class="db-hero-name"><!-- Auth::user()->name --></span>
            </h1>
            <p class="db-hero-sub">
                <!-- date du jour -->
                &nbsp;·&nbsp;
                Année scolaire active&nbsp;:
                <strong class="db-year-chip"><!-- année active --></strong>
            </p>
            <div class="db-hero-clock" id="heroClock">--:--:--</div>
        </div>

        <div class="db-hero-right">
            <div class="db-kpi-strip">
                <div class="db-kpi" style="--d:.05s">
                    <div class="db-kpi-icon db-kpi-blue"><i class="fas fa-users"></i></div>
                    <div class="db-kpi-body">
                        <span class="db-kpi-value"><!-- Student::count() --></span>
                        <span class="db-kpi-label">Élèves</span>
                    </div>
                </div>
                <div class="db-kpi" style="--d:.12s">
                    <div class="db-kpi-icon db-kpi-teal"><i class="fas fa-sitemap"></i></div>
                    <div class="db-kpi-body">
                        <span class="db-kpi-value"><!-- Sector::count() --></span>
                        <span class="db-kpi-label">Filières</span>
                    </div>
                </div>
                <div class="db-kpi" style="--d:.19s">
                    <div class="db-kpi-icon db-kpi-violet"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="db-kpi-body">
                        <span class="db-kpi-value"><!-- PromotionClassroom::count() --></span>
                        <span class="db-kpi-label">Classes</span>
                    </div>
                </div>
                <div class="db-kpi" style="--d:.26s">
                    <div class="db-kpi-icon db-kpi-amber"><i class="fas fa-book-open"></i></div>
                    <div class="db-kpi-body">
                        <span class="db-kpi-value"><!-- Subject::count() --></span>
                        <span class="db-kpi-label">Matières</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ░░░ CORPS PRINCIPAL ░░░ --}}
    <div class="db-body">

        {{-- ── Accès rapide ──────────────────────────────── --}}
        <section class="db-section">
            <div class="db-section-head">
                <h2 class="db-section-title"><i class="fas fa-bolt"></i> Accès rapide</h2>
                <span class="db-section-line"></span>
            </div>

            <div class="db-quick-grid">

                {{-- Carte 1 --}}
                <a href="#" class="db-quick-card" style="--c:#6366f1;--cb:rgba(99,102,241,.12);--d:.00s">
                    <div class="db-quick-icon"><i class="fas fa-file-import"></i></div>
                    <div class="db-quick-info">
                        <span class="db-quick-name">Importer élèves</span>
                        <span class="db-quick-desc">Ajout par fichier ou formulaire</span>
                    </div>
                    <i class="fas fa-arrow-right db-quick-arrow"></i>
                </a>

                {{-- Carte 2 --}}
                <a href="#" class="db-quick-card" style="--c:#22d3ee;--cb:rgba(34,211,238,.12);--d:.04s">
                    <div class="db-quick-icon"><i class="fas fa-users"></i></div>
                    <div class="db-quick-info">
                        <span class="db-quick-name">Liste des élèves</span>
                        <span class="db-quick-desc">Consulter et gérer tous les élèves</span>
                    </div>
                    <i class="fas fa-arrow-right db-quick-arrow"></i>
                </a>

                {{-- Carte 3 --}}
                <a href="#" class="db-quick-card" style="--c:#2dd4bf;--cb:rgba(45,212,191,.12);--d:.08s">
                    <div class="db-quick-icon"><i class="fas fa-sitemap"></i></div>
                    <div class="db-quick-info">
                        <span class="db-quick-name">Gérer les filières</span>
                        <span class="db-quick-desc">Associer filières à l'année</span>
                    </div>
                    <i class="fas fa-arrow-right db-quick-arrow"></i>
                </a>

                {{-- Carte 4 --}}
                <a href="#" class="db-quick-card" style="--c:#a78bfa;--cb:rgba(167,139,250,.12);--d:.12s">
                    <div class="db-quick-icon"><i class="fas fa-layer-group"></i></div>
                    <div class="db-quick-info">
                        <span class="db-quick-name">Promotions</span>
                        <span class="db-quick-desc">Créer les promotions par filière</span>
                    </div>
                    <i class="fas fa-arrow-right db-quick-arrow"></i>
                </a>

                {{-- Carte 5 --}}
                <a href="#" class="db-quick-card" style="--c:#34d399;--cb:rgba(52,211,153,.12);--d:.16s">
                    <div class="db-quick-icon"><i class="fas fa-school"></i></div>
                    <div class="db-quick-info">
                        <span class="db-quick-name">Créer les classes</span>
                        <span class="db-quick-desc">Organiser les classes de l'année</span>
                    </div>
                    <i class="fas fa-arrow-right db-quick-arrow"></i>
                </a>

                {{-- Carte 6 --}}
                <a href="#" class="db-quick-card" style="--c:#fbbf24;--cb:rgba(251,191,36,.12);--d:.20s">
                    <div class="db-quick-icon"><i class="fas fa-book"></i></div>
                    <div class="db-quick-info">
                        <span class="db-quick-name">Affecter matières</span>
                        <span class="db-quick-desc">Gérer les matières par classe</span>
                    </div>
                    <i class="fas fa-arrow-right db-quick-arrow"></i>
                </a>

                {{-- Carte 7 --}}
                <a href="#" class="db-quick-card" style="--c:#f87171;--cb:rgba(248,113,113,.12);--d:.24s">
                    <div class="db-quick-icon"><i class="fas fa-calculator"></i></div>
                    <div class="db-quick-info">
                        <span class="db-quick-name">Coefficients</span>
                        <span class="db-quick-desc">Définir les coefficients des matières</span>
                    </div>
                    <i class="fas fa-arrow-right db-quick-arrow"></i>
                </a>

                {{-- Carte 8 --}}
                <a href="#" class="db-quick-card" style="--c:#fb923c;--cb:rgba(251,146,60,.12);--d:.28s">
                    <div class="db-quick-icon"><i class="fas fa-pen-nib"></i></div>
                    <div class="db-quick-info">
                        <span class="db-quick-name">Saisir les notes</span>
                        <span class="db-quick-desc">Enregistrer les évaluations</span>
                    </div>
                    <i class="fas fa-arrow-right db-quick-arrow"></i>
                </a>

            </div>
        </section>

        {{-- ── Bas : Activité + Système ─────────────────────── --}}
        <div class="db-bottom-row">

            {{-- Activité récente --}}
            <section class="db-card db-activity">
                <div class="db-card-head">
                    <h3 class="db-card-title"><i class="fas fa-history"></i> Activité récente</h3>
                </div>
                <div class="db-timeline">
                    {{-- @forelse($recentStudents as $i => $student) --}}
                    <div class="db-tl-item" style="--d:.00s">
                        <div class="db-tl-dot"></div>
                        <div class="db-tl-content">
                            <span class="db-tl-label">Élève ajouté</span>
                            <span class="db-tl-name"><!-- nom élève --></span>
                            <span class="db-tl-time"><!-- created_at->diffForHumans() --></span>
                        </div>
                    </div>
                    <div class="db-tl-item" style="--d:.07s">
                        <div class="db-tl-dot"></div>
                        <div class="db-tl-content">
                            <span class="db-tl-label">Élève ajouté</span>
                            <span class="db-tl-name"><!-- nom élève --></span>
                            <span class="db-tl-time"><!-- created_at->diffForHumans() --></span>
                        </div>
                    </div>
                    <div class="db-tl-item" style="--d:.14s">
                        <div class="db-tl-dot"></div>
                        <div class="db-tl-content">
                            <span class="db-tl-label">Élève ajouté</span>
                            <span class="db-tl-name"><!-- nom élève --></span>
                            <span class="db-tl-time"><!-- created_at->diffForHumans() --></span>
                        </div>
                    </div>
                    <div class="db-tl-item" style="--d:.21s">
                        <div class="db-tl-dot"></div>
                        <div class="db-tl-content">
                            <span class="db-tl-label">Élève ajouté</span>
                            <span class="db-tl-name"><!-- nom élève --></span>
                            <span class="db-tl-time"><!-- created_at->diffForHumans() --></span>
                        </div>
                    </div>
                    {{-- @empty
                    <div class="db-tl-empty">
                        <i class="fas fa-inbox"></i>
                        <span>Aucune activité récente</span>
                    </div>
                    @endforelse --}}
                </div>
            </section>

            {{-- Informations système --}}
            <section class="db-card db-sysinfo">
                <div class="db-card-head">
                    <h3 class="db-card-title"><i class="fas fa-server"></i> Système</h3>
                </div>

                <div class="db-sys-list">
                    <div class="db-sys-row">
                        <div class="db-sys-icon db-kpi-blue"><i class="fas fa-user-shield"></i></div>
                        <div class="db-sys-body">
                            <span class="db-sys-key">Connecté en tant que</span>
                            <span class="db-sys-val"><!-- Auth::user()->name --></span>
                        </div>
                    </div>
                    <div class="db-sys-row">
                        <div class="db-sys-icon db-kpi-teal"><i class="fas fa-calendar-check"></i></div>
                        <div class="db-sys-body">
                            <span class="db-sys-key">Dernière connexion</span>
                            <span class="db-sys-val"><!-- now()->format('d/m/Y à H:i') --></span>
                        </div>
                    </div>
                    <div class="db-sys-row">
                        <div class="db-sys-icon db-kpi-violet"><i class="fas fa-code-branch"></i></div>
                        <div class="db-sys-body">
                            <span class="db-sys-key">Version Laravel</span>
                            <span class="db-sys-val"><!-- app()->version() --></span>
                        </div>
                    </div>
                    <div class="db-sys-row">
                        <div class="db-sys-icon db-kpi-amber"><i class="fas fa-globe"></i></div>
                        <div class="db-sys-body">
                            <span class="db-sys-key">Environnement</span>
                            <span class="db-sys-val"><span class="db-env-badge"><!-- app()->environment() --></span></span>
                        </div>
                    </div>
                </div>

                {{-- Barre de progression --}}
                <div class="db-progress-block">
                    <div class="db-progress-head">
                        <span>Taux de remplissage des classes</span>
                        <span class="db-progress-pct"><!-- $pct -->%</span>
                    </div>
                    <div class="db-progress-track">
                        {{-- style="--w:{{ $pct }}%" --}}
                        <div class="db-progress-fill" style="--w:0%"></div>
                    </div>
                    <p class="db-progress-hint"><!-- $classesWithStudents --> / <!-- $totalClasses --> classes ont des élèves affectés</p>
                </div>
            </section>

        </div>
    </div>
</div>

<style>
/* ================================================================
   ROOT
   ================================================================ */
*, *::before, *::after { box-sizing: border-box; }
.db-root {
    font-family: 'DM Sans', sans-serif;
    min-height: 100vh;
    padding: 2rem 1.75rem 4rem;
    position: relative;
    overflow: hidden;
    color: #c8d3e8;
}
.db-root * { font-family: inherit; }

/* ================================================================
   FOND AMBIANT
   ================================================================ */
.db-bg {
    position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden;
}
.db-orb {
    position: absolute; border-radius: 50%;
    filter: blur(90px); opacity: .35;
}
.db-orb-1 {
    width: 600px; height: 600px;
    background: radial-gradient(circle, #4f46e5 0%, transparent 70%);
    top: -200px; left: -150px;
    animation: orbDrift1 18s ease-in-out infinite alternate;
}
.db-orb-2 {
    width: 500px; height: 500px;
    background: radial-gradient(circle, #0d9488 0%, transparent 70%);
    bottom: -150px; right: -100px;
    animation: orbDrift2 22s ease-in-out infinite alternate;
}
.db-orb-3 {
    width: 350px; height: 350px;
    background: radial-gradient(circle, #7c3aed 0%, transparent 70%);
    top: 40%; left: 55%;
    animation: orbDrift3 14s ease-in-out infinite alternate;
}
@keyframes orbDrift1 { to { transform: translate(60px, 40px); } }
@keyframes orbDrift2 { to { transform: translate(-40px, -60px); } }
@keyframes orbDrift3 { to { transform: translate(-50px, 50px); } }
.db-grid-lines {
    position: absolute; inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
    background-size: 60px 60px;
}

/* ================================================================
   Z-INDEX
   ================================================================ */
.db-hero, .db-body { position: relative; z-index: 1; }

/* ================================================================
   HERO
   ================================================================ */
.db-hero {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 2rem; flex-wrap: wrap;
    margin-bottom: 2.5rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid rgba(255,255,255,.06);
}
.db-hero-left  { flex: 1; min-width: 260px; }
.db-hero-right { display: flex; align-items: center; }

.db-hello-badge {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .3rem .85rem; border-radius: 30px;
    background: rgba(99,102,241,.12); border: 1px solid rgba(99,102,241,.25);
    font-size: .72rem; font-weight: 700; color: #818cf8;
    text-transform: uppercase; letter-spacing: .08em;
    margin-bottom: 1rem;
    animation: fadeDown .4s ease both;
}
.db-hello-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: #4ade80; box-shadow: 0 0 6px #4ade80;
    animation: blink 1.8s ease-in-out infinite;
}
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

.db-hero-title {
    font-family: 'Syne', sans-serif;
    font-size: clamp(1.8rem, 4vw, 2.8rem);
    font-weight: 800; line-height: 1.1;
    color: #f0f4ff; margin: 0 0 .75rem;
    letter-spacing: -.03em;
    animation: fadeDown .4s .05s ease both;
}
.db-hero-name {
    background: linear-gradient(135deg, #818cf8 0%, #2dd4bf 100%);
    -webkit-background-clip: text; background-clip: text;
    -webkit-text-fill-color: transparent;
}
.db-hero-sub {
    font-size: .84rem; color: #64748b; margin: 0 0 1.25rem;
    animation: fadeDown .4s .1s ease both;
}
.db-year-chip {
    display: inline-block;
    background: rgba(45,212,191,.12); border: 1px solid rgba(45,212,191,.25);
    color: #2dd4bf; border-radius: 6px; padding: .05rem .45rem;
    font-size: .78rem; font-weight: 700;
}
.db-hero-clock {
    font-family: 'Syne', sans-serif;
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 700; letter-spacing: -.05em;
    color: rgba(240,244,255,.12);
    line-height: 1; user-select: none;
    animation: fadeDown .4s .15s ease both;
}

/* ── KPI strip ── */
.db-kpi-strip {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: .75rem;
    animation: fadeDown .4s .08s ease both;
}
.db-kpi {
    display: flex; align-items: center; gap: .85rem;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 14px; padding: 1rem 1.25rem;
    animation: fadeDown .4s calc(.05s + var(--d)) ease both;
    transition: background .2s, border-color .2s, transform .2s;
}
.db-kpi:hover {
    background: rgba(255,255,255,.07);
    border-color: rgba(255,255,255,.12);
    transform: translateY(-2px);
}
.db-kpi-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem; flex-shrink: 0;
}
.db-kpi-blue   { background: rgba(99,102,241,.15);  color: #818cf8; }
.db-kpi-teal   { background: rgba(45,212,191,.15);  color: #2dd4bf; }
.db-kpi-violet { background: rgba(167,139,250,.15); color: #a78bfa; }
.db-kpi-amber  { background: rgba(251,191,36,.15);  color: #fbbf24; }
.db-kpi-value {
    display: block;
    font-family: 'Syne', sans-serif;
    font-size: 1.5rem; font-weight: 800;
    color: #f0f4ff; line-height: 1; letter-spacing: -.03em;
}
.db-kpi-label {
    font-size: .7rem; color: #64748b;
    font-weight: 600; text-transform: uppercase; letter-spacing: .07em;
}

/* ================================================================
   SECTION HEAD
   ================================================================ */
.db-section-head {
    display: flex; align-items: center; gap: .9rem; margin-bottom: 1.25rem;
}
.db-section-title {
    font-family: 'Syne', sans-serif;
    font-size: .9rem; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .08em;
    display: flex; align-items: center; gap: .5rem; white-space: nowrap; margin: 0;
}
.db-section-title i { color: #6366f1; font-size: .8rem; }
.db-section-line { flex: 1; height: 1px; background: rgba(255,255,255,.06); }

/* ================================================================
   ACCÈS RAPIDE
   ================================================================ */
.db-quick-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    gap: .7rem;
}
.db-quick-card {
    display: flex; align-items: center; gap: .9rem;
    padding: .9rem 1rem;
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.06);
    border-radius: 14px;
    text-decoration: none; color: inherit;
    transition: all .2s cubic-bezier(.4,0,.2,1);
    animation: fadeUp .35s calc(var(--d)) ease both;
    position: relative; overflow: hidden;
}
.db-quick-card::before {
    content: ''; position: absolute; inset: 0;
    background: var(--cb); opacity: 0;
    transition: opacity .2s; border-radius: inherit;
}
.db-quick-card:hover {
    border-color: color-mix(in srgb, var(--c) 40%, transparent);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,.25);
    text-decoration: none; color: inherit;
}
.db-quick-card:hover::before { opacity: 1; }
.db-quick-card:hover .db-quick-icon  { transform: scale(1.1); }
.db-quick-card:hover .db-quick-arrow { opacity: 1; transform: translateX(0); color: var(--c); }
.db-quick-card:hover .db-quick-name  { color: var(--c); }
.db-quick-icon {
    width: 38px; height: 38px; border-radius: 10px;
    background: var(--cb); color: var(--c);
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0;
    transition: transform .2s; position: relative; z-index: 1;
    border: 1px solid color-mix(in srgb, var(--c) 20%, transparent);
}
.db-quick-info { flex: 1; min-width: 0; position: relative; z-index: 1; }
.db-quick-name {
    display: block; font-size: .84rem; font-weight: 700; color: #dde4f0;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    transition: color .2s;
}
.db-quick-desc {
    display: block; font-size: .7rem; color: #475569;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: .1rem;
}
.db-quick-arrow {
    font-size: .65rem; color: #334155; flex-shrink: 0;
    opacity: 0; transform: translateX(-4px);
    transition: all .2s; position: relative; z-index: 1;
}

/* ================================================================
   BAS DE PAGE
   ================================================================ */
.db-bottom-row {
    display: grid; grid-template-columns: 1fr 1.4fr;
    gap: 1rem; margin-top: 1.5rem;
}
.db-card {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 18px; padding: 1.4rem;
    animation: fadeUp .4s .15s ease both;
}
.db-card-head { margin-bottom: 1.1rem; }
.db-card-title {
    font-family: 'Syne', sans-serif;
    font-size: .82rem; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: .09em;
    display: flex; align-items: center; gap: .5rem; margin: 0;
}
.db-card-title i { color: #6366f1; font-size: .75rem; }

/* ── Timeline ── */
.db-timeline { display: flex; flex-direction: column; gap: .1rem; }
.db-tl-item {
    display: flex; align-items: flex-start; gap: .85rem;
    padding: .6rem 0; border-bottom: 1px solid rgba(255,255,255,.04);
    animation: fadeUp .3s calc(var(--d)) ease both;
}
.db-tl-item:last-child { border-bottom: none; }
.db-tl-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: #4f46e5; flex-shrink: 0; margin-top: .35rem;
    box-shadow: 0 0 8px rgba(99,102,241,.5);
}
.db-tl-content { flex: 1; }
.db-tl-label { font-size: .67rem; text-transform: uppercase; letter-spacing: .08em; color: #475569; font-weight: 700; }
.db-tl-name  { display: block; font-size: .84rem; font-weight: 600; color: #c8d3e8; margin: .1rem 0; }
.db-tl-time  { font-size: .7rem; color: #334155; }
.db-tl-empty {
    text-align: center; padding: 1.5rem; color: #334155;
    font-size: .82rem; display: flex; flex-direction: column; align-items: center; gap: .5rem;
}
.db-tl-empty i { font-size: 1.4rem; }

/* ── Sys info ── */
.db-sys-list { display: flex; flex-direction: column; gap: .1rem; margin-bottom: 1.4rem; }
.db-sys-row {
    display: flex; align-items: center; gap: .85rem;
    padding: .65rem 0; border-bottom: 1px solid rgba(255,255,255,.04);
}
.db-sys-row:last-child { border-bottom: none; }
.db-sys-icon {
    width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .8rem;
}
.db-sys-key { display: block; font-size: .7rem; color: #475569; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }
.db-sys-val { display: block; font-size: .84rem; font-weight: 600; color: #c8d3e8; margin-top: .1rem; }
.db-env-badge {
    display: inline-block; padding: .1rem .5rem; border-radius: 20px;
    background: rgba(52,211,153,.12); border: 1px solid rgba(52,211,153,.2);
    color: #34d399; font-size: .72rem;
}

/* ── Progress ── */
.db-progress-block { margin-top: .5rem; }
.db-progress-head {
    display: flex; justify-content: space-between;
    font-size: .75rem; color: #475569; font-weight: 600; margin-bottom: .45rem;
}
.db-progress-pct { color: #818cf8; font-weight: 700; }
.db-progress-track {
    height: 6px; background: rgba(255,255,255,.07);
    border-radius: 10px; overflow: hidden;
}
.db-progress-fill {
    height: 100%; width: var(--w);
    background: linear-gradient(90deg, #6366f1, #2dd4bf);
    border-radius: 10px;
    animation: progressIn 1s .5s ease both;
}
@keyframes progressIn { from { width: 0; } to { width: var(--w); } }
.db-progress-hint { font-size: .68rem; color: #334155; margin-top: .35rem; }

/* ================================================================
   ANIMATIONS
   ================================================================ */
@keyframes fadeDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
@keyframes fadeUp   { from { opacity:0; transform:translateY(12px);  } to { opacity:1; transform:translateY(0); } }

/* ================================================================
   RESPONSIVE
   ================================================================ */
@media (max-width: 900px) {
    .db-bottom-row { grid-template-columns: 1fr; }
    .db-kpi-strip  { grid-template-columns: 1fr 1fr; }
    .db-hero       { flex-direction: column; }
}
@media (max-width: 560px) {
    .db-root       { padding: 1.25rem 1rem 3rem; }
    .db-quick-grid { grid-template-columns: 1fr; }
    .db-kpi-strip  { grid-template-columns: 1fr 1fr; }
}
</style>

@push('scripts')
<script>
(function () {
    /* ── Horloge temps réel ── */
    const clock = document.getElementById('heroClock');
    if (clock) {
        function tick() {
            const now = new Date();
            clock.textContent = [now.getHours(), now.getMinutes(), now.getSeconds()]
                .map(n => String(n).padStart(2, '0')).join(':');
        }
        tick();
        setInterval(tick, 1000);
    }

    /* ── Compteurs animés ── */
    document.querySelectorAll('.db-kpi-value').forEach(el => {
        const target = parseInt(el.textContent, 10);
        if (isNaN(target) || target === 0) return;
        let start = 0;
        const duration = 900, step = 16;
        const inc = target / (duration / step);
        const t = setInterval(() => {
            start += inc;
            if (start >= target) { el.textContent = target; clearInterval(t); }
            else el.textContent = Math.floor(start);
        }, step);
    });
})();
</script>
@endpush

@endsection
