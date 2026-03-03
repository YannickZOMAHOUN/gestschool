<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail Parents — Résultats Scolaires</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ─── Tokens ─────────────────────────────────────────── */
        :root {
            --navy:      #0B1F3A;
            --navy-mid:  #122847;
            --navy-soft: #1a3660;
            --gold:      #C9952A;
            --gold-pale: #E8C06A;
            --gold-dim:  rgba(201, 149, 42, .15);
            --cream:     #F7F3EC;
            --cream-mid: #EDE8DF;
            --text:      #1C2B40;
            --text-muted:#6B7C93;
            --success:   #2D9E6B;
            --danger:    #C84040;
            --white:     #FFFFFF;

            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 22px;
            --shadow-sm: 0 2px 8px rgba(11,31,58,.10);
            --shadow-md: 0 8px 32px rgba(11,31,58,.14);
            --shadow-lg: 0 20px 60px rgba(11,31,58,.18);
        }

        /* ─── Reset ──────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--cream);
            font-family: 'Nunito', sans-serif;
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ─── Background décoratif ───────────────────────────── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 50% at 10% 0%, rgba(201,149,42,.07) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 90% 100%, rgba(11,31,58,.05) 0%, transparent 55%);
            pointer-events: none;
            z-index: 0;
        }

        /* ─── Hero header ────────────────────────────────────── */
        .hero {
            background: linear-gradient(160deg, var(--navy) 0%, var(--navy-soft) 100%);
            position: relative;
            overflow: hidden;
            padding: 3.5rem 0 2.5rem;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -1px; left: 0; right: 0;
            height: 60px;
            background: var(--cream);
            clip-path: ellipse(55% 100% at 50% 100%);
        }

        /* Motif géométrique décoratif */
        .hero-pattern {
            position: absolute;
            inset: 0;
            opacity: .04;
            background-image:
                repeating-linear-gradient(45deg, var(--gold) 0, var(--gold) 1px, transparent 0, transparent 50%),
                repeating-linear-gradient(-45deg, var(--gold) 0, var(--gold) 1px, transparent 0, transparent 50%);
            background-size: 28px 28px;
        }

        .hero-content { position: relative; z-index: 1; text-align: center; }

        .school-emblem {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-pale) 100%);
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 1.25rem;
            box-shadow: 0 0 0 4px rgba(201,149,42,.2), 0 8px 24px rgba(11,31,58,.3);
        }

        .school-emblem i { font-size: 2rem; color: var(--navy); }

        .hero h1 {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(1.7rem, 4vw, 2.6rem);
            font-weight: 700;
            color: var(--white);
            letter-spacing: .02em;
            margin: 0 0 .4rem;
        }

        .hero .tagline {
            font-size: .95rem;
            color: rgba(255,255,255,.6);
            font-weight: 300;
            letter-spacing: .04em;
            text-transform: uppercase;
            margin: 0;
        }

        .hero .gold-line {
            width: 48px; height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            margin: .9rem auto .8rem;
        }

        /* ─── Login link ─────────────────────────────────────── */
        .btn-login {
            position: absolute;
            top: 1.2rem; right: 1.5rem;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.2);
            color: rgba(255,255,255,.85);
            border-radius: 50px;
            padding: .4rem 1.1rem;
            font-size: .82rem;
            font-weight: 600;
            letter-spacing: .03em;
            text-decoration: none;
            transition: all .25s;
            backdrop-filter: blur(8px);
        }
        .btn-login:hover {
            background: var(--gold);
            border-color: var(--gold);
            color: var(--navy);
        }

        /* ─── Layout ─────────────────────────────────────────── */
        .page-wrapper {
            position: relative; z-index: 1;
            max-width: 900px;
            margin: 0 auto;
            padding: 2.5rem 1.25rem 4rem;
        }

        /* ─── Section label ──────────────────────────────────── */
        .section-label {
            display: flex; align-items: center; gap: .75rem;
            margin-bottom: 1.5rem;
        }
        .section-label .label-icon {
            width: 36px; height: 36px;
            border-radius: var(--radius-sm);
            background: var(--gold-dim);
            display: flex; align-items: center; justify-content: center;
            color: var(--gold);
            font-size: .9rem;
        }
        .section-label h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--navy);
            margin: 0;
        }

        /* ─── Carte formulaire ───────────────────────────────── */
        .search-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--cream-mid);
            overflow: hidden;
            animation: fadeUp .5s ease both;
        }

        .search-card-header {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-soft) 100%);
            padding: 1.4rem 2rem;
            display: flex; align-items: center; justify-content: space-between;
        }

        .search-card-header h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.25rem;
            color: var(--white);
            margin: 0;
            font-weight: 600;
        }

        .search-card-body { padding: 2rem 2rem 1.75rem; }

        /* ─── Form elements ──────────────────────────────────── */
        .form-label {
            font-size: .8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--text-muted);
            margin-bottom: .45rem;
        }

        .form-select, .form-control {
            border: 1.5px solid var(--cream-mid);
            border-radius: var(--radius-sm);
            padding: .7rem 1rem;
            font-family: 'Nunito', sans-serif;
            font-size: .95rem;
            color: var(--text);
            background: var(--cream);
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .form-select:focus, .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(201,149,42,.15);
            background: var(--white);
            outline: none;
        }
        .form-select:disabled { opacity: .5; cursor: not-allowed; }

        .input-group-text {
            background: var(--cream);
            border: 1.5px solid var(--cream-mid);
            border-right: none;
            border-radius: var(--radius-sm) 0 0 var(--radius-sm);
            color: var(--gold);
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
        }
        .input-group .form-control:focus { border-left: none; }

        /* ─── Bouton principal ───────────────────────────────── */
        .btn-search {
            background: linear-gradient(135deg, var(--gold) 0%, #b07d1a 100%);
            color: var(--navy);
            font-weight: 700;
            font-family: 'Nunito', sans-serif;
            font-size: .95rem;
            letter-spacing: .04em;
            text-transform: uppercase;
            border: none;
            border-radius: var(--radius-sm);
            padding: .8rem 2.5rem;
            position: relative; overflow: hidden;
            transition: transform .2s, box-shadow .2s;
            cursor: pointer;
        }
        .btn-search::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,.25) 50%, transparent 100%);
            transform: translateX(-100%);
            transition: transform .5s;
        }
        .btn-search:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(201,149,42,.4); }
        .btn-search:hover::after { transform: translateX(100%); }
        .btn-search:active { transform: translateY(0); }
        .btn-search:disabled { opacity: .6; transform: none; cursor: not-allowed; }

        /* ─── Alertes ─────────────────────────────────────────── */
        .alert-custom {
            border-radius: var(--radius-sm);
            border: none;
            font-size: .9rem;
            padding: .8rem 1.1rem;
        }
        .alert-error {
            background: rgba(200, 64, 64, .08);
            color: var(--danger);
            border-left: 3px solid var(--danger);
        }

        /* ─── Carte résultats ────────────────────────────────── */
        .results-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--cream-mid);
            overflow: hidden;
            margin-top: 2.5rem;
            animation: fadeUp .5s .15s ease both;
        }

        .results-header {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-soft) 100%);
            padding: 1.6rem 2rem;
        }
        .results-header h3 {
            font-family: 'Cormorant Garamond', serif;
            color: var(--white);
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0 0 .2rem;
        }
        .results-header .semester-badge {
            display: inline-block;
            background: var(--gold-dim);
            color: var(--gold-pale);
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .2rem .75rem;
            border-radius: 50px;
            border: 1px solid rgba(201,149,42,.3);
        }

        /* ─── Fiche élève ────────────────────────────────────── */
        .student-profile {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 1.25rem;
            align-items: center;
            padding: 1.5rem 2rem;
            background: var(--cream);
            border-bottom: 1px solid var(--cream-mid);
        }
        .student-avatar {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, var(--navy-soft) 0%, var(--navy) 100%);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: var(--gold);
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        .student-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--navy);
            margin: 0 0 .25rem;
            line-height: 1.2;
        }
        .student-meta {
            display: flex; gap: 1.25rem; flex-wrap: wrap;
        }
        .student-meta span {
            font-size: .8rem;
            color: var(--text-muted);
            display: flex; align-items: center; gap: .35rem;
        }
        .student-meta i { color: var(--gold); font-size: .75rem; }

        /* ─── Tableau des notes ──────────────────────────────── */
        .table-wrapper { padding: 0 2rem 1.5rem; overflow-x: auto; }

        .notes-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .875rem;
            margin-top: 1.5rem;
        }

        .notes-table thead tr {
            background: linear-gradient(90deg, var(--navy) 0%, var(--navy-soft) 100%);
        }
        .notes-table thead th {
            color: rgba(255,255,255,.85);
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            padding: .85rem .9rem;
            white-space: nowrap;
            border: none;
        }
        .notes-table thead th:first-child { border-radius: var(--radius-sm) 0 0 0; }
        .notes-table thead th:last-child  { border-radius: 0 var(--radius-sm) 0 0; }

        .notes-table tbody tr {
            transition: background .15s;
        }
        .notes-table tbody tr:nth-child(even) { background: var(--cream); }
        .notes-table tbody tr:hover { background: var(--gold-dim); }

        .notes-table td {
            padding: .75rem .9rem;
            border-bottom: 1px solid var(--cream-mid);
            color: var(--text);
            vertical-align: middle;
        }

        .subject-name { font-weight: 600; color: var(--navy); }

        .coeff-badge {
            display: inline-block;
            background: var(--navy);
            color: var(--gold-pale);
            font-size: .7rem;
            font-weight: 700;
            padding: .15rem .5rem;
            border-radius: 4px;
            letter-spacing: .04em;
        }

        .interro-chips { display: flex; flex-wrap: wrap; gap: .3rem; }
        .interro-chip {
            background: rgba(11,31,58,.08);
            color: var(--navy);
            font-size: .7rem;
            font-weight: 600;
            padding: .15rem .45rem;
            border-radius: 4px;
        }

        .note-value {
            font-weight: 600;
            font-size: .9rem;
        }
        .note-value.absent { color: var(--text-muted); font-weight: 400; font-style: italic; }

        .avg-pill {
            display: inline-block;
            font-size: .85rem;
            font-weight: 700;
            padding: .3rem .75rem;
            border-radius: 50px;
            min-width: 52px;
            text-align: center;
        }
        .avg-pill.good    { background: rgba(45,158,107,.12); color: var(--success); }
        .avg-pill.fail    { background: rgba(200,64,64,.10);  color: var(--danger); }
        .avg-pill.neutral { background: rgba(107,124,147,.1); color: var(--text-muted); }

        /* ─── Pied de tableau — Moyenne générale ─────────────── */
        .tfoot-avg td {
            padding: 1rem .9rem;
            background: linear-gradient(90deg, var(--navy) 0%, var(--navy-soft) 100%);
            color: rgba(255,255,255,.75);
            font-size: .8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            border: none;
        }
        .tfoot-avg .avg-display {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--gold-pale);
            line-height: 1;
        }
        .tfoot-avg .avg-display sup { font-size: .75rem; }

        /* ─── Verdict (semestre 2) ───────────────────────────── */
        .verdict-row {
            padding: 1.25rem 2rem;
            display: flex; align-items: center; gap: 1rem;
            border-top: 2px solid;
        }
        .verdict-row.admis {
            background: rgba(45,158,107,.06);
            border-color: var(--success);
        }
        .verdict-row.refuse {
            background: rgba(200,64,64,.06);
            border-color: var(--danger);
        }
        .verdict-icon {
            width: 44px; height: 44px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .admis   .verdict-icon { background: rgba(45,158,107,.15); color: var(--success); }
        .refuse  .verdict-icon { background: rgba(200,64,64,.12);  color: var(--danger); }
        .verdict-text .main  { font-weight: 700; font-size: 1rem; }
        .verdict-text .sub   { font-size: .8rem; color: var(--text-muted); margin-top: .1rem; }
        .admis   .verdict-text .main { color: var(--success); }
        .refuse  .verdict-text .main { color: var(--danger); }

        /* ─── Footer carte résultats ────────────────────────── */
        .results-footer {
            padding: 1.2rem 2rem;
            background: var(--cream);
            border-top: 1px solid var(--cream-mid);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: .75rem;
        }
        .results-footer .note-info {
            font-size: .78rem;
            color: var(--text-muted);
            display: flex; align-items: center; gap: .4rem;
        }
        .results-footer .note-info i { color: var(--gold); }

        .btn-pdf {
            display: inline-flex; align-items: center; gap: .5rem;
            background: var(--navy);
            color: var(--gold-pale);
            font-size: .82rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: .55rem 1.25rem;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            transition: background .2s, transform .2s;
        }
        .btn-pdf:hover { background: var(--navy-soft); transform: translateY(-1px); }
        .btn-pdf i { font-size: .85rem; }

        /* ─── Page footer ────────────────────────────────────── */
        .page-footer {
            text-align: center;
            padding: 1.5rem 0 2rem;
            font-size: .8rem;
            color: var(--text-muted);
            position: relative; z-index: 1;
        }
        .page-footer a { color: var(--gold); text-decoration: none; }
        .page-footer a:hover { text-decoration: underline; }

        /* ─── Spinner ────────────────────────────────────────── */
        .spin { animation: spin .8s linear infinite; display: inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ─── Entrée animée ──────────────────────────────────── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ─── Responsive ─────────────────────────────────────── */
        @media (max-width: 640px) {
            .search-card-body, .table-wrapper, .results-footer { padding-left: 1.2rem; padding-right: 1.2rem; }
            .student-profile { padding: 1.25rem 1.2rem; }
            .results-header  { padding: 1.25rem 1.2rem; }
            .verdict-row     { padding: 1rem 1.2rem; }
            .avg-display     { font-size: 1.35rem !important; }
        }

        /* ─── Print ──────────────────────────────────────────── */
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .search-card { display: none; }
            .results-card { box-shadow: none; border: 1px solid #ddd; margin-top: 0; }
            .hero { display: none; }
        }
    </style>
</head>
<body>

    {{-- ── Hero ─────────────────────────────────────────────── --}}
    <div class="hero">
        <div class="hero-pattern"></div>

        <a href="{{ route('login') }}" class="btn-login no-print">
            <i class="fas fa-sign-in-alt me-1"></i> Espace personnel
        </a>

        <div class="hero-content">
            <div class="school-emblem">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1>LYCÉE TECHNIQUE DE BOHICON</h1>
            <div class="gold-line"></div>
            <p class="tagline">Portail de consultation des résultats scolaires</p>
        </div>
    </div>

    {{-- ── Contenu principal ──────────────────────────────────── --}}
    <div class="page-wrapper">

        {{-- ── Formulaire de recherche ── --}}
        <div class="no-print">
            <div class="section-label mb-4">
                <div class="label-icon"><i class="fas fa-search"></i></div>
                <h2>Rechercher un élève</h2>
            </div>

            <div class="search-card">
                <div class="search-card-header">
                    <h3><i class="fas fa-file-alt me-2" style="color:var(--gold);"></i>Formulaire de consultation</h3>
                </div>

                <div class="search-card-body">

                    {{-- Erreurs --}}
                    @if ($errors->any())
                        <div class="alert-custom alert-error mb-4" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success mb-4 rounded-2 border-0" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('parents.results') }}" id="searchForm" novalidate>
                        @csrf

                        <div class="row g-3 g-lg-4">

                            {{-- Année --}}
                            <div class="col-md-6">
                                <label class="form-label">Année scolaire</label>
                                <select name="year_id" id="year_id" class="form-select" required>
                                    <option value="">— Choisir une année —</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year->id }}"
                                            {{ old('year_id', request('year_id')) == $year->id ? 'selected' : '' }}>
                                            {{ $year->year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filière --}}
                            <div class="col-md-6">
                                <label class="form-label">Filière</label>
                                <select name="sector_id" id="sector_id" class="form-select" disabled required>
                                    <option value="">— Sélectionner l'année d'abord —</option>
                                </select>
                            </div>

                            {{-- Promotion --}}
                            <div class="col-md-6">
                                <label class="form-label">Promotion</label>
                                <select name="promotion_id" id="promotion_id" class="form-select" disabled required>
                                    <option value="">— Sélectionner la filière d'abord —</option>
                                </select>
                            </div>

                            {{-- Classe --}}
                            <div class="col-md-6">
                                <label class="form-label">Classe</label>
                                <select name="classroom_id" id="classroom_id" class="form-select" disabled required>
                                    <option value="">— Sélectionner la promotion d'abord —</option>
                                </select>
                            </div>

                            {{-- Semestre --}}
                            <div class="col-md-6">
                                <label class="form-label">Semestre</label>
                                <select name="semester" id="semester" class="form-select" required>
                                    <option value="" disabled selected>— Choisir —</option>
                                    <option value="1" {{ old('semester', request('semester')) == '1' ? 'selected' : '' }}>Semestre 1</option>
                                    <option value="2" {{ old('semester', request('semester')) == '2' ? 'selected' : '' }}>Semestre 2</option>
                                </select>
                            </div>

                            {{-- Matricule --}}
                            <div class="col-md-6">
                                <label class="form-label">Matricule de l'élève <span style="color:var(--danger);">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                    <input type="text" name="matricule" id="matricule"
                                        class="form-control @error('matricule') is-invalid @enderror"
                                        value="{{ old('matricule', request('matricule')) }}"
                                        placeholder="Ex: 2024A001" required>
                                    @error('matricule')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="text-center mt-4 pt-1">
                            <button type="submit" class="btn-search" id="submitBtn">
                                <i class="fas fa-search me-2" id="submitIcon"></i>
                                <span id="submitText">Consulter les résultats</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Résultats ──────────────────────────────────── --}}
        @if(isset($results))
        <div class="results-card" id="resultsSection">

            {{-- Header --}}
            <div class="results-header">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                    <div>
                        <h3><i class="fas fa-chart-bar me-2" style="color:var(--gold);"></i>Bulletin de Notes</h3>
                        <span class="semester-badge">Semestre {{ $request->semester }}</span>
                    </div>
                    @if($generalAverage !== null)
                    <div class="text-end">
                        <div style="color:rgba(255,255,255,.5); font-size:.7rem; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.2rem;">Moy. Générale</div>
                        <div style="font-family:'Cormorant Garamond',serif; font-size:2rem; font-weight:700; color:{{ $generalAverage >= 10 ? '#6EE0A8' : '#F4897B' }}; line-height:1;">
                            {{ number_format($generalAverage, 2) }}<sup style="font-size:.9rem;">/20</sup>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Fiche élève --}}
            <div class="student-profile">
                <div class="student-avatar">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <div class="student-name">{{ $student->name }} {{ $student->surname }}</div>
                    <div class="student-meta">
                        <span><i class="fas fa-id-card"></i>{{ $student->matricule }}</span>
                        <span><i class="fas fa-school"></i>{{ $classroom->name ?? '—' }}</span>
                        <span><i class="fas fa-calendar-alt"></i>{{ $year->year ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Tableau --}}
            <div class="table-wrapper">
                <table class="notes-table">
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th class="text-center">Coef</th>
                            <th>Interros</th>
                            <th class="text-center">Moy. Interros</th>
                            <th class="text-center">Devoir 1</th>
                            <th class="text-center">Devoir 2</th>
                            <th class="text-center">Moyenne /20</th>
                            <th class="text-center">Moy. Pond.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                        <tr>
                            <td><span class="subject-name">{{ $result['subject'] }}</span></td>

                            <td class="text-center">
                                <span class="coeff-badge">×{{ $result['coefficient'] }}</span>
                            </td>

                            <td>
                                @if(count($result['interros']) > 0)
                                    <div class="interro-chips">
                                        @foreach($result['interros'] as $note)
                                            <span class="interro-chip">{{ number_format($note, 2) }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="note-value absent">—</span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($result['moy_interros'] !== null)
                                    <span class="note-value">{{ number_format($result['moy_interros'], 2) }}</span>
                                @else
                                    <span class="note-value absent">—</span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($result['devoir1'] !== null)
                                    <span class="note-value">{{ number_format($result['devoir1'], 2) }}</span>
                                @else
                                    <span class="note-value absent">—</span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($result['devoir2'] !== null)
                                    <span class="note-value">{{ number_format($result['devoir2'], 2) }}</span>
                                @else
                                    <span class="note-value absent">—</span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($result['subject_average'] !== null)
                                    <span class="avg-pill {{ $result['subject_average'] >= 10 ? 'good' : 'fail' }}">
                                        {{ number_format($result['subject_average'], 2) }}
                                    </span>
                                @else
                                    <span class="avg-pill neutral">—</span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($result['weighted_average'] !== null)
                                    <span class="note-value">{{ number_format($result['weighted_average'], 2) }}</span>
                                @else
                                    <span class="note-value absent">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="tfoot-avg">
                            <td colspan="6">Moyenne Générale Pondérée</td>
                            <td colspan="2" class="text-center">
                                @if($generalAverage !== null)
                                    <span class="avg-display">{{ number_format($generalAverage, 2) }}<sup>/20</sup></span>
                                @else
                                    <span style="color:rgba(255,255,255,.4); font-size:.9rem;">Données incomplètes</span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Verdict semestre 2 --}}
            @if($request->semester == 2 && $isPassed !== null)
            <div class="verdict-row {{ $isPassed ? 'admis' : 'refuse' }}">
                <div class="verdict-icon">
                    <i class="fas fa-{{ $isPassed ? 'check' : 'times' }}"></i>
                </div>
                <div class="verdict-text">
                    <div class="main">
                        {{ $isPassed ? 'ADMIS(E) EN CLASSE SUPÉRIEURE' : 'NON ADMIS(E) — DOIT REDOUBLER' }}
                    </div>
                    <div class="sub">
                        @if($isPassed)
                            Félicitations ! La moyenne de {{ number_format($generalAverage, 2) }}/20 est satisfaisante.
                        @else
                            La moyenne de {{ number_format($generalAverage, 2) }}/20 est insuffisante. Rapprochez-vous de l'établissement.
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Footer carte --}}
            <div class="results-footer no-print">
                <div class="note-info">
                    <i class="fas fa-info-circle"></i>
                    La moyenne pondérée tient compte des coefficients. Les notes manquantes sont exclues du calcul.
                </div>
                <button class="btn-pdf" id="btnPdf">
                    <i class="fas fa-file-pdf"></i> Télécharger PDF
                </button>
            </div>
        </div>
        @endif

        {{-- ── Page footer ──────────────────────────────────── --}}
        <div class="page-footer no-print">
            <p class="mb-1">
                <i class="fas fa-phone-alt me-1"></i>
                Besoin d'aide ? Contactez le service scolaire :
                <a href="tel:+22901683749">+229 01 68 37 49 02</a>
            </p>
            <p class="mb-0">© {{ date('Y') }} School Manager — Tous droits réservés</p>
        </div>

    </div>{{-- /page-wrapper --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // ─── Sélecteurs en cascade ─────────────────────────────────────

    const $ = id => document.getElementById(id);

    const selYear      = $('year_id');
    const selSector    = $('sector_id');
    const selPromotion = $('promotion_id');
    const selClassroom = $('classroom_id');

    function setLoading(select, msg = 'Chargement…') {
        select.innerHTML = `<option value="">${msg}</option>`;
        select.disabled  = true;
    }

    function resetSelect(select, placeholder) {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        select.disabled  = true;
    }

    function populate(select, items, placeholder, oldValue = '') {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        items.forEach(item => {
            const opt     = document.createElement('option');
            opt.value     = item.id;
            opt.textContent = item.name;
            if (String(item.id) === String(oldValue)) opt.selected = true;
            select.appendChild(opt);
        });
        select.disabled = items.length === 0;
    }

    // Charger les filières
    selYear.addEventListener('change', () => {
        resetSelect(selSector,    '— Sélectionner la filière —');
        resetSelect(selPromotion, '— Sélectionner la promotion —');
        resetSelect(selClassroom, '— Sélectionner la classe —');

        const yearId = selYear.value;
        if (!yearId) return;

        setLoading(selSector);
        fetch(`/parents/get-sectors/${yearId}`)
            .then(r => r.json())
            .then(data => {
                populate(selSector, data, '— Choisir une filière —', '{{ old("sector_id", request("sector_id")) }}');
                // Ré-enchaîner si valeur pré-remplie
                if (selSector.value) selSector.dispatchEvent(new Event('change'));
            })
            .catch(() => resetSelect(selSector, '— Erreur de chargement —'));
    });

    // Charger les promotions
    selSector.addEventListener('change', () => {
        resetSelect(selPromotion, '— Sélectionner la promotion —');
        resetSelect(selClassroom, '— Sélectionner la classe —');

        const yearId   = selYear.value;
        const sectorId = selSector.value;
        if (!yearId || !sectorId) return;

        setLoading(selPromotion);
        fetch(`/parents/get-promotions/${yearId}/${sectorId}`)
            .then(r => r.json())
            .then(data => {
                populate(selPromotion, data, '— Choisir une promotion —', '{{ old("promotion_id", request("promotion_id")) }}');
                if (selPromotion.value) selPromotion.dispatchEvent(new Event('change'));
            })
            .catch(() => resetSelect(selPromotion, '— Erreur de chargement —'));
    });

    // Charger les classes
    selPromotion.addEventListener('change', () => {
        resetSelect(selClassroom, '— Sélectionner la classe —');

        const yearId      = selYear.value;
        const sectorId    = selSector.value;
        const promotionId = selPromotion.value;
        if (!yearId || !sectorId || !promotionId) return;

        setLoading(selClassroom);
        fetch(`/parents/get-classes/${yearId}/${sectorId}/${promotionId}`)
            .then(r => r.json())
            .then(data => populate(selClassroom, data, '— Choisir une classe —', '{{ old("classroom_id", request("classroom_id")) }}'))
            .catch(() => resetSelect(selClassroom, '— Erreur de chargement —'));
    });

    // Relancer la cascade au chargement si valeurs déjà sélectionnées (retour form)
    document.addEventListener('DOMContentLoaded', () => {
        if (selYear.value) selYear.dispatchEvent(new Event('change'));

        @if(isset($results))
        // Scroll automatique vers les résultats
        setTimeout(() => {
            const el = document.getElementById('resultsSection');
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 350);
        @endif
    });

    // ─── Submit avec loader ─────────────────────────────────────────
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        // Validation native
        if (!this.checkValidity()) { e.preventDefault(); this.classList.add('was-validated'); return; }

        const btn  = document.getElementById('submitBtn');
        const icon = document.getElementById('submitIcon');
        const text = document.getElementById('submitText');

        btn.disabled       = true;
        icon.className     = 'fas fa-circle-notch spin me-2';
        text.textContent   = 'Recherche en cours…';
    });

    // ─── Export PDF ─────────────────────────────────────────────────
    @if(isset($results))
    document.getElementById('btnPdf')?.addEventListener('click', () => {
        const btn      = document.getElementById('btnPdf');
        const origHtml = btn.innerHTML;

        btn.innerHTML = '<i class="fas fa-circle-notch spin"></i> Génération…';
        btn.disabled  = true;

        const formData = new FormData(document.getElementById('searchForm'));
        const params   = new URLSearchParams(formData).toString();

        fetch(`/parents/export-pdf?${params}`)
            .then(r => { if (!r.ok) throw new Error(); return r.blob(); })
            .then(blob => {
                const url = URL.createObjectURL(blob);
                const a   = document.createElement('a');
                a.href    = url;
                a.download = `resultats-${formData.get('matricule')}-S${formData.get('semester')}.pdf`;
                document.body.appendChild(a);
                a.click();
                URL.revokeObjectURL(url);
                a.remove();
            })
            .catch(() => alert('Erreur lors de la génération du PDF. Veuillez réessayer.'))
            .finally(() => { btn.innerHTML = origHtml; btn.disabled = false; });
    });
    @endif
    </script>
</body>
</html>
