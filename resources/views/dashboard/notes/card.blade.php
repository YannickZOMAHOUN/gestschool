@extends('layouts.template')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<div class="ec-wrap">
    <div class="ec-bg"></div>

    <div class="ec-container">

        <!-- Header -->
        <div class="ec-header">
            <div class="ec-header-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <div>
                <div class="ec-eyebrow">Génération de documents officiels</div>
                <h1 class="ec-title">Exportation <span>des données</span></h1>
                <p class="ec-desc">Sélectionnez votre classe, choisissez le semestre, puis générez le document souhaité</p>
            </div>
        </div>

        <form method="GET" action="{{ route('notes.exportcard') }}" class="needs-validation" novalidate id="cardForm">
            @csrf

            <div class="ec-layout">

                <!-- Left: Form -->
                <div class="ec-form-panel">
                    <div class="ec-panel-title">
                        <div class="ec-panel-num">01</div>
                        Paramètres de sélection
                    </div>

                    <div class="ec-fields">

                        <div class="ec-field">
                            <label class="ec-lbl">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                Année scolaire
                            </label>
                            <div class="ec-sel-wrap">
                                <select name="year_id" id="export_year_id" class="ec-sel" required>
                                    <option value="">Sélectionnez une année…</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                                    @endforeach
                                </select>
                                <svg class="ec-caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                            <p class="ec-err">Ce champ est requis</p>
                        </div>

                        <div class="ec-field">
                            <label class="ec-lbl">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                Filière
                            </label>
                            <div class="ec-sel-wrap">
                                <select name="sector_id" id="export_sector_id" class="ec-sel" disabled required>
                                    <option value="">Choisir une année d'abord</option>
                                </select>
                                <svg class="ec-caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                            <p class="ec-err">Ce champ est requis</p>
                        </div>

                        <div class="ec-field">
                            <label class="ec-lbl">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
                                Promotion
                            </label>
                            <div class="ec-sel-wrap">
                                <select name="promotion_id" id="export_promotion_id" class="ec-sel" disabled required>
                                    <option value="">Choisir une filière d'abord</option>
                                </select>
                                <svg class="ec-caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                            <p class="ec-err">Ce champ est requis</p>
                        </div>

                        <div class="ec-field">
                            <label class="ec-lbl">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                Classe
                            </label>
                            <div class="ec-sel-wrap">
                                <select name="classroom_id" id="export_classroom_id" class="ec-sel" disabled required>
                                    <option value="">Choisir une promotion d'abord</option>
                                </select>
                                <svg class="ec-caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                            <p class="ec-err">Ce champ est requis</p>
                        </div>

                    </div>

                    <!-- Semester -->
                    <div class="ec-sem-section">
                        <div class="ec-panel-title" style="margin-bottom:1rem">
                            <div class="ec-panel-num">02</div>
                            Période
                        </div>
                        <div class="ec-sem-group">
                            <label class="ec-sem-opt">
                                <input type="radio" name="semester" value="1" required>
                                <span class="ec-sem-box">
                                    <span class="ec-sem-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                    </span>
                                    <span class="ec-sem-info">
                                        <strong>Semestre 1</strong>
                                        <small>1ère période de l'année</small>
                                    </span>
                                    <span class="ec-sem-radio"></span>
                                </span>
                            </label>
                            <label class="ec-sem-opt">
                                <input type="radio" name="semester" value="2">
                                <span class="ec-sem-box">
                                    <span class="ec-sem-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l-3 3"/></svg>
                                    </span>
                                    <span class="ec-sem-info">
                                        <strong>Semestre 2</strong>
                                        <small>2ème période de l'année</small>
                                    </span>
                                    <span class="ec-sem-radio"></span>
                                </span>
                            </label>
                        </div>
                        <p class="ec-err" id="sem-err" style="display:none">Veuillez sélectionner un semestre</p>
                    </div>
                </div>

                <!-- Right: Doc type -->
                <div class="ec-doc-panel">
                    <div class="ec-panel-title">
                        <div class="ec-panel-num">03</div>
                        Type de document
                    </div>

                    <div class="ec-doc-list">

                        <button type="submit" name="export_type" value="fiche_collation" class="ec-doc-card ec-doc-amber">
                            <div class="ec-doc-top">
                                <div class="ec-doc-icon">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M9 12h6M9 16h6M9 8h6M5 4h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1z"/>
                                        <circle cx="7" cy="8" r=".5" fill="currentColor"/>
                                        <circle cx="7" cy="12" r=".5" fill="currentColor"/>
                                        <circle cx="7" cy="16" r=".5" fill="currentColor"/>
                                    </svg>
                                </div>
                                <div class="ec-doc-badge">PDF</div>
                            </div>
                            <div class="ec-doc-body">
                                <h3>Fiche de collation</h3>
                                <p>Tableau récapitulatif de toutes les notes par matière pour la classe</p>
                            </div>
                            <div class="ec-doc-foot">
                                <span>Générer ce document</span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </div>
                        </button>

                        <button type="submit" name="export_type" value="fiche_bulletin" class="ec-doc-card ec-doc-teal">
                            <div class="ec-doc-top">
                                <div class="ec-doc-icon">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <line x1="7" y1="8" x2="17" y2="8"/>
                                        <line x1="7" y1="12" x2="17" y2="12"/>
                                        <line x1="7" y1="16" x2="12" y2="16"/>
                                        <circle cx="20" cy="20" r="4" stroke-width="1.5"/>
                                        <line x1="20" y1="18" x2="20" y2="22" stroke-width="1.5"/>
                                        <line x1="18" y1="20" x2="22" y2="20" stroke-width="1.5"/>
                                    </svg>
                                </div>
                                <div class="ec-doc-badge">PDF</div>
                            </div>
                            <div class="ec-doc-body">
                                <h3>Bulletins de notes</h3>
                                <p>Bulletins individuels pour chaque élève de la classe sélectionnée</p>
                            </div>
                            <div class="ec-doc-foot">
                                <span>Générer ce document</span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </div>
                        </button>

                    </div>

                    <div class="ec-doc-note">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        Remplissez tous les champs avant de générer un document
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.ec-wrap {
    min-height: 100vh;
    background: #fafbff;
    padding: 2.5rem 1.25rem 5rem;
    font-family: 'Plus Jakarta Sans', sans-serif;
    position: relative;
    overflow: hidden;
}

.ec-bg {
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse at 0% 0%, rgba(245,158,11,.06) 0%, transparent 60%),
        radial-gradient(ellipse at 100% 100%, rgba(20,184,166,.06) 0%, transparent 60%),
        url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23f59e0b' fill-opacity='0.025'%3E%3Ccircle cx='20' cy='20' r='1'/%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}

.ec-container { max-width: 1000px; margin: 0 auto; position: relative; z-index: 1; }

/* Header */
.ec-header {
    display: flex; align-items: flex-start; gap: 1.5rem;
    margin-bottom: 2.5rem;
    animation: ecUp .55s ease both;
}
.ec-header-icon {
    width: 64px; height: 64px; flex-shrink: 0;
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 1.5px solid #fcd34d;
    border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    color: #d97706;
    box-shadow: 0 4px 16px rgba(245,158,11,.2);
    margin-top: 4px;
}
.ec-eyebrow {
    font-size: .72rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
    color: #d97706; margin-bottom: .6rem;
    display: flex; align-items: center; gap: 8px;
}
.ec-eyebrow::before {
    content: '';
    display: inline-block; width: 20px; height: 2px;
    background: currentColor; border-radius: 1px;
}
.ec-title {
    font-family: 'Syne', sans-serif;
    font-size: 2.4rem; font-weight: 800; color: #1c1917;
    line-height: 1.1; letter-spacing: -.025em; margin-bottom: .6rem;
}
.ec-title span {
    background: linear-gradient(135deg, #f59e0b, #10b981);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.ec-desc { color: #78716c; font-size: .93rem; line-height: 1.6; }

/* Layout */
.ec-layout {
    display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;
    animation: ecUp .55s .1s ease both;
}

/* Panel shared */
.ec-form-panel, .ec-doc-panel {
    background: #fff;
    border: 1px solid #e7e5e4;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 2px 4px rgba(0,0,0,.04), 0 12px 32px rgba(0,0,0,.05);
}

.ec-panel-title {
    display: flex; align-items: center; gap: 10px;
    font-family: 'Syne', sans-serif; font-size: .88rem; font-weight: 700;
    color: #374151; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: .04em;
}
.ec-panel-num {
    width: 26px; height: 26px; border-radius: 8px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white; font-size: .7rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

/* Fields */
.ec-fields { display: flex; flex-direction: column; gap: 1.1rem; margin-bottom: 2rem; }
.ec-field { display: flex; flex-direction: column; gap: 5px; }
.ec-lbl { display: flex; align-items: center; gap: 6px; font-size: .77rem; font-weight: 600; color: #374151; }
.ec-sel-wrap { position: relative; }
.ec-sel {
    width: 100%; padding: .7rem 2.2rem .7rem .85rem;
    background: #fafaf9; border: 1.5px solid #e7e5e4; border-radius: 10px;
    color: #374151; font-family: 'Plus Jakarta Sans', sans-serif; font-size: .87rem;
    appearance: none; cursor: pointer; outline: none; transition: all .2s;
}
.ec-sel:hover:not(:disabled) { border-color: #fcd34d; background: #fffbeb; }
.ec-sel:focus { border-color: #f59e0b; background: #fff; box-shadow: 0 0 0 3px rgba(245,158,11,.12); }
.ec-sel:disabled { opacity: .4; cursor: not-allowed; background: #f5f5f4; }
.ec-caret { position: absolute; right: 9px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: #a8a29e; pointer-events: none; }
.ec-err { font-size: .74rem; color: #ef4444; display: none; }
.was-validated .ec-sel:invalid { border-color: #fca5a5; background: #fff5f5; }
.was-validated .ec-sel:invalid ~ .ec-err { display: block; }
.was-validated .ec-sel:valid { border-color: #86efac; }

/* Semester */
.ec-sem-section { padding-top: 1.5rem; border-top: 1px solid #f5f5f4; }
.ec-sem-group { display: flex; flex-direction: column; gap: .75rem; }
.ec-sem-opt { cursor: pointer; }
.ec-sem-opt input { display: none; }
.ec-sem-box {
    display: flex; align-items: center; gap: 12px;
    padding: .75rem 1rem;
    background: #fafaf9; border: 1.5px solid #e7e5e4; border-radius: 12px;
    transition: all .22s;
}
.ec-sem-icon {
    width: 36px; height: 36px; border-radius: 10px;
    background: #f5f5f4; display: flex; align-items: center; justify-content: center;
    color: #a8a29e; flex-shrink: 0; transition: all .22s;
}
.ec-sem-icon svg { width: 18px; height: 18px; }
.ec-sem-info { flex: 1; }
.ec-sem-info strong { display: block; font-size: .88rem; font-weight: 600; color: #374151; transition: color .22s; }
.ec-sem-info small { font-size: .76rem; color: #a8a29e; transition: color .22s; }
.ec-sem-radio {
    width: 18px; height: 18px; border-radius: 50%; border: 2px solid #d6d3d1;
    flex-shrink: 0; transition: all .22s; position: relative;
}
.ec-sem-opt:hover .ec-sem-box { border-color: #fcd34d; background: #fffbeb; }
.ec-sem-opt input:checked + .ec-sem-box { border-color: #f59e0b; background: #fffbeb; box-shadow: 0 0 0 3px rgba(245,158,11,.1); }
.ec-sem-opt input:checked + .ec-sem-box .ec-sem-icon { background: #fef3c7; color: #d97706; }
.ec-sem-opt input:checked + .ec-sem-box .ec-sem-info strong { color: #92400e; }
.ec-sem-opt input:checked + .ec-sem-box .ec-sem-info small { color: #d97706; }
.ec-sem-opt input:checked + .ec-sem-box .ec-sem-radio { border-color: #f59e0b; background: #f59e0b; box-shadow: inset 0 0 0 3px white; }

/* Doc cards */
.ec-doc-list { display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem; }

.ec-doc-card {
    display: flex; flex-direction: column; gap: 1rem;
    padding: 1.4rem; border-radius: 16px;
    border: 1.5px solid; cursor: pointer;
    text-align: left; width: 100%;
    transition: all .25s; position: relative; overflow: hidden;
    background: #fff;
}
.ec-doc-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    transition: opacity .25s; opacity: 0;
}
.ec-doc-card:hover { transform: translateY(-3px); }
.ec-doc-card:hover::before { opacity: 1; }

.ec-doc-amber { border-color: #fde68a; }
.ec-doc-amber:hover { border-color: #f59e0b; box-shadow: 0 8px 24px rgba(245,158,11,.15); }
.ec-doc-amber::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.ec-doc-amber .ec-doc-icon { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
.ec-doc-amber .ec-doc-badge { background: #fef3c7; color: #d97706; border-color: #fde68a; }
.ec-doc-amber .ec-doc-foot { color: #d97706; }

.ec-doc-teal { border-color: #99f6e4; }
.ec-doc-teal:hover { border-color: #14b8a6; box-shadow: 0 8px 24px rgba(20,184,166,.15); }
.ec-doc-teal::before { background: linear-gradient(90deg, #14b8a6, #2dd4bf); }
.ec-doc-teal .ec-doc-icon { background: #f0fdfa; color: #0d9488; border: 1px solid #99f6e4; }
.ec-doc-teal .ec-doc-badge { background: #f0fdfa; color: #0d9488; border-color: #99f6e4; }
.ec-doc-teal .ec-doc-foot { color: #0d9488; }

.ec-doc-top { display: flex; justify-content: space-between; align-items: flex-start; }
.ec-doc-icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
}
.ec-doc-badge {
    font-size: .68rem; font-weight: 800; letter-spacing: .08em;
    padding: 3px 8px; border-radius: 6px; border: 1px solid;
}
.ec-doc-body h3 {
    font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700;
    color: #1c1917; margin-bottom: 4px;
}
.ec-doc-body p { font-size: .82rem; color: #78716c; line-height: 1.5; }
.ec-doc-foot {
    display: flex; align-items: center; justify-content: space-between;
    font-size: .82rem; font-weight: 600;
    padding-top: .75rem; border-top: 1px solid #f5f5f4;
    transition: gap .2s;
}
.ec-doc-card:hover .ec-doc-foot { gap: 8px; }

.ec-doc-note {
    display: flex; align-items: center; gap: 7px;
    font-size: .78rem; color: #d6d3d1;
    padding: .75rem 1rem;
    background: #fafaf9; border: 1px solid #f5f5f4; border-radius: 10px;
}

@keyframes ecUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }

@media (max-width: 768px) {
    .ec-layout { grid-template-columns: 1fr; }
    .ec-title { font-size: 1.8rem; }
}
@media (max-width: 480px) {
    .ec-form-panel, .ec-doc-panel { padding: 1.5rem; }
    .ec-header-icon { display: none; }
}
</style>
@endsection

@section('another_JS')
<script>
function fetchOptions(url, el, ph) {
    el.innerHTML = `<option value="">${ph}</option>`; el.disabled = true;
    fetch(url).then(r => r.json()).then(data => {
        if (data.length) data.forEach(i => el.innerHTML += `<option value="${i.id}">${i.name}</option>`);
        else el.innerHTML += `<option disabled>Aucune option disponible</option>`;
        el.disabled = false;
    }).catch(() => { el.innerHTML += `<option disabled>Erreur de chargement</option>`; el.disabled = false; });
}

document.addEventListener('DOMContentLoaded', () => {
    const Y = document.getElementById('export_year_id');
    const S = document.getElementById('export_sector_id');
    const P = document.getElementById('export_promotion_id');
    const C = document.getElementById('export_classroom_id');

    Y.addEventListener('change', () => {
        S.innerHTML = '<option value="">Choisir une année d\'abord</option>'; S.disabled = true;
        P.innerHTML = '<option value="">Choisir une filière d\'abord</option>'; P.disabled = true;
        C.innerHTML = '<option value="">Choisir une promotion d\'abord</option>'; C.disabled = true;
        if (Y.value) fetchOptions(`/api/sectors-by-year/${Y.value}`, S, 'Choisir une filière…');
    });
    S.addEventListener('change', () => {
        P.innerHTML = '<option value="">Choisir une filière d\'abord</option>'; P.disabled = true;
        C.innerHTML = '<option value="">Choisir une promotion d\'abord</option>'; C.disabled = true;
        if (Y.value && S.value) fetchOptions(`/api/promotions-by-year-sector/${Y.value}/${S.value}`, P, 'Choisir une promotion…');
    });
    P.addEventListener('change', () => {
        C.innerHTML = '<option value="">Choisir une promotion d\'abord</option>'; C.disabled = true;
        if (P.value) fetchOptions(`/api/classes-by-promotion/${P.value}`, C, 'Choisir une classe…');
    });

    const form = document.getElementById('cardForm');
    form.querySelectorAll('button[type="submit"]').forEach(btn => {
        btn.addEventListener('click', e => {
            const sem = document.querySelector('input[name="semester"]:checked');
            document.getElementById('sem-err').style.display = sem ? 'none' : 'block';
            if (!sem || !form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
            form.classList.add('was-validated');
        });
    });
});
</script>
@endsection
