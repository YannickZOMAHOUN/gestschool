@extends('layouts.template')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="rat-wrapper">

    {{-- ── En-tête ── --}}
    <div class="rat-header">
        <div class="rat-header-icon"><i class="fas fa-sliders-h"></i></div>
        <div>
            <h1 class="rat-title">Gestion des Coefficients</h1>
            <p class="rat-subtitle">Définissez les coefficients et semestres par matière, classe et promotion</p>
        </div>
    </div>

    @if (session('success'))
    <div class="rat-alert rat-alert-success">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
        <button class="rat-alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="rat-alert rat-alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ $errors->first() }}</span>
        <button class="rat-alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
    @endif

    <form method="POST" action="{{ route('ratio.store') }}" id="ratioForm">
        @csrf

        {{-- ── Étape 1 : Contexte ── --}}
        <div class="rat-card">
            <div class="rat-card-header">
                <div class="rat-step-badge">1</div>
                <div>
                    <h2 class="rat-card-title">Contexte scolaire</h2>
                    <p class="rat-card-desc">Choisissez l'année, la filière puis la promotion</p>
                </div>
            </div>

            <div class="rat-grid-3">
                <div class="rat-field">
                    <label class="rat-label"><i class="fas fa-calendar-alt"></i> Année scolaire</label>
                    <div class="rat-select-wrap">
                        <select name="year_id" id="year_id" class="rat-select" required>
                            <option value="">— Choisir —</option>
                            @foreach($years as $year)
                                <option value="{{ $year->id }}">{{ $year->year }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down rat-select-icon"></i>
                    </div>
                </div>

                <div class="rat-field">
                    <label class="rat-label"><i class="fas fa-sitemap"></i> Filière</label>
                    <div class="rat-select-wrap">
                        <select id="sector_id" class="rat-select" disabled>
                            <option value="">— Année d'abord —</option>
                        </select>
                        <i class="fas fa-chevron-down rat-select-icon"></i>
                    </div>
                </div>

                <div class="rat-field">
                    <label class="rat-label"><i class="fas fa-graduation-cap"></i> Promotion</label>
                    <div class="rat-select-wrap">
                        <select name="promotion_id" id="promotion_id" class="rat-select" disabled>
                            <option value="">— Filière d'abord —</option>
                        </select>
                        <i class="fas fa-chevron-down rat-select-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Étape 2 : Tableau matières ── --}}
        <div class="rat-card" id="matieresCard" style="display:none;">
            <div class="rat-card-header">
                <div class="rat-step-badge">2</div>
                <div style="flex:1;">
                    <h2 class="rat-card-title">Coefficients &amp; Semestres</h2>
                    <p class="rat-card-desc">Pour chaque matière : choisissez le(s) semestre(s), les classes et le coefficient</p>
                </div>
                <div class="rat-toolbar-actions">
                    <button type="button" class="rat-btn-ghost" onclick="selectAll()">
                        <i class="fas fa-check-double"></i> Tout cocher
                    </button>
                    <button type="button" class="rat-btn-ghost rat-btn-ghost--danger" onclick="deselectAll()">
                        <i class="fas fa-times"></i> Tout décocher
                    </button>
                </div>
            </div>

            {{-- Coefficient global --}}
            <div class="rat-global-coeff">
                <i class="fas fa-magic"></i>
                <label class="rat-label" style="margin:0;white-space:nowrap;">Coefficient commun :</label>
                <input type="number" min="1" max="20" class="rat-coeff-global-input" id="globalCoefficient" placeholder="ex : 2">
                <span class="rat-global-hint">Appliqué à toutes les matières</span>
            </div>

            {{-- Légende semestres --}}
            <div class="rat-sem-legend">
                <span class="rat-sem-pill rat-sem-both"><i class="fas fa-infinity"></i> S1 + S2</span>
                <span class="rat-sem-pill rat-sem-s1">Semestre 1 uniquement</span>
                <span class="rat-sem-pill rat-sem-s2">Semestre 2 uniquement</span>
                <span class="rat-legend-note">— hérité de la fiche matières, modifiable ici</span>
            </div>

            {{-- Tableau --}}
            <div class="rat-table-wrap">
                <table class="rat-table">
                    <thead>
                        <tr>
                            <th class="rat-th rat-th--subject">Matière</th>
                            <th class="rat-th rat-th--sem">Semestre(s)</th>
                            <th class="rat-th rat-th--classes">Classes</th>
                            <th class="rat-th rat-th--coeff">Coefficient</th>
                        </tr>
                    </thead>
                    <tbody id="promotionBody">
                        <tr>
                            <td colspan="4" class="rat-empty-row">
                                <i class="fas fa-table"></i>
                                <span>Sélectionnez une promotion pour afficher les matières</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Actions ── --}}
        <div class="rat-actions" id="actionsBar" style="display:none;">
            <button type="submit" class="rat-btn-primary">
                <i class="fas fa-save"></i> Enregistrer les coefficients
            </button>
        </div>

    </form>
</div>

<style>
/* ═══════════════════════════════════════════
   TOKENS
═══════════════════════════════════════════ */
.rat-wrapper, .rat-wrapper * {
    font-family: 'Plus Jakarta Sans', sans-serif;
    box-sizing: border-box;
}
:root {
    --rat-bg:          #080c14;
    --rat-surface:     #0f1520;
    --rat-surface-2:   #141c2e;
    --rat-border:      rgba(255,255,255,.07);
    --rat-border-2:    rgba(255,255,255,.11);
    --rat-accent:      #6366f1;
    --rat-accent-2:    #8b5cf6;
    --rat-accent-bg:   rgba(99,102,241,.1);
    --rat-accent-glow: rgba(99,102,241,.3);
    --rat-success:     #10b981;
    --rat-success-bg:  rgba(16,185,129,.1);
    --rat-danger:      #f87171;
    --rat-danger-bg:   rgba(248,113,113,.1);
    --rat-warn:        #f59e0b;
    --rat-warn-bg:     rgba(245,158,11,.1);
    --rat-text:        #c8d0e0;
    --rat-text-bright: #e8ecf4;
    --rat-muted:       #3d4d6a;
    --rat-muted-2:     #566480;
    --rat-radius:      14px;
    --rat-radius-sm:   8px;
    --rat-transition:  .2s cubic-bezier(.4,0,.2,1);
}

/* ═══════════════════════════════════════════
   WRAPPER
═══════════════════════════════════════════ */
.rat-wrapper {
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 1.5rem 4rem;
    color: var(--rat-text);
}

/* ═══════════════════════════════════════════
   HEADER
═══════════════════════════════════════════ */
.rat-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; }
.rat-header-icon {
    width: 52px; height: 52px;
    background: var(--rat-accent-bg);
    border: 1px solid rgba(99,102,241,.25);
    border-radius: 15px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem; color: var(--rat-accent);
    box-shadow: 0 0 20px rgba(99,102,241,.2);
    flex-shrink: 0;
}
.rat-title    { font-size: 1.5rem; font-weight: 800; color: var(--rat-text-bright); margin: 0 0 .25rem; letter-spacing: -.02em; }
.rat-subtitle { font-size: .83rem; color: var(--rat-muted-2); margin: 0; }

/* ═══════════════════════════════════════════
   ALERTS
═══════════════════════════════════════════ */
.rat-alert {
    display: flex; align-items: center; gap: .75rem;
    padding: .85rem 1rem; border-radius: var(--rat-radius-sm);
    margin-bottom: 1.25rem; font-size: .83rem; font-weight: 500;
    animation: ratSlideDown .25s ease both;
}
@keyframes ratSlideDown {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}
.rat-alert span { flex: 1; }
.rat-alert-close { background: transparent; border: none; cursor: pointer; color: inherit; opacity: .6; padding: 0; font-size: .8rem; }
.rat-alert-close:hover { opacity: 1; }
.rat-alert-success { background: var(--rat-success-bg); border: 1px solid rgba(16,185,129,.2); color: var(--rat-success); }
.rat-alert-danger  { background: var(--rat-danger-bg);  border: 1px solid rgba(248,113,113,.2); color: var(--rat-danger); }

/* ═══════════════════════════════════════════
   CARD
═══════════════════════════════════════════ */
.rat-card {
    background: var(--rat-surface);
    border: 1px solid var(--rat-border);
    border-radius: var(--rat-radius);
    padding: 1.5rem;
    margin-bottom: 1.25rem;
    animation: ratFadeUp .3s ease both;
}
@keyframes ratFadeUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
.rat-card-header { display: flex; align-items: center; gap: .85rem; margin-bottom: 1.4rem; flex-wrap: wrap; }
.rat-step-badge {
    width: 32px; height: 32px; border-radius: 50%;
    background: linear-gradient(135deg, var(--rat-accent), var(--rat-accent-2));
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; font-weight: 800; color: #fff; flex-shrink: 0;
    box-shadow: 0 4px 12px var(--rat-accent-glow);
}
.rat-card-title { font-size: 1rem; font-weight: 700; color: var(--rat-text-bright); margin: 0 0 .2rem; letter-spacing: -.01em; }
.rat-card-desc  { font-size: .77rem; color: var(--rat-muted-2); margin: 0; }

/* ═══════════════════════════════════════════
   GRID SELECTS
═══════════════════════════════════════════ */
.rat-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
@media (max-width: 700px) { .rat-grid-3 { grid-template-columns: 1fr; } }

.rat-field { display: flex; flex-direction: column; gap: .45rem; }
.rat-label {
    font-size: .78rem; font-weight: 600; color: var(--rat-muted-2);
    text-transform: uppercase; letter-spacing: .06em;
    display: flex; align-items: center; gap: .4rem;
}
.rat-label i { color: var(--rat-accent); font-size: .75rem; }
.rat-select-wrap { position: relative; }
.rat-select {
    width: 100%; padding: .72rem 2.2rem .72rem .9rem;
    background: var(--rat-surface-2); border: 1px solid var(--rat-border-2);
    border-radius: var(--rat-radius-sm); color: var(--rat-text);
    font-size: .85rem; font-family: inherit; appearance: none;
    cursor: pointer; outline: none;
    transition: border-color var(--rat-transition), box-shadow var(--rat-transition);
}
.rat-select:focus  { border-color: var(--rat-accent); box-shadow: 0 0 0 3px rgba(99,102,241,.15); }
.rat-select:disabled { opacity: .45; cursor: not-allowed; }
.rat-select option { background: var(--rat-surface-2); }
.rat-select-icon {
    position: absolute; right: .8rem; top: 50%; transform: translateY(-50%);
    font-size: .65rem; color: var(--rat-muted); pointer-events: none;
}

/* ═══════════════════════════════════════════
   TOOLBAR
═══════════════════════════════════════════ */
.rat-toolbar-actions { display: flex; gap: .5rem; margin-left: auto; flex-wrap: wrap; }

.rat-btn-ghost {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .4rem .85rem;
    background: var(--rat-accent-bg); color: var(--rat-accent);
    border: 1px solid rgba(99,102,241,.2); border-radius: var(--rat-radius-sm);
    font-size: .76rem; font-family: inherit; font-weight: 600;
    cursor: pointer; transition: all var(--rat-transition); white-space: nowrap;
}
.rat-btn-ghost:hover { background: rgba(99,102,241,.18); border-color: rgba(99,102,241,.4); }
.rat-btn-ghost--danger { background: var(--rat-danger-bg); color: var(--rat-danger); border-color: rgba(248,113,113,.2); }
.rat-btn-ghost--danger:hover { background: rgba(248,113,113,.18); }

/* ═══════════════════════════════════════════
   COEFFICIENT GLOBAL
═══════════════════════════════════════════ */
.rat-global-coeff {
    display: flex; align-items: center; gap: .75rem; flex-wrap: wrap;
    background: var(--rat-accent-bg); border: 1px solid rgba(99,102,241,.15);
    border-radius: var(--rat-radius-sm); padding: .75rem 1rem; margin-bottom: 1rem;
}
.rat-global-coeff i { color: var(--rat-accent); font-size: .85rem; }
.rat-coeff-global-input {
    width: 90px; height: 36px; padding: 0 .6rem;
    background: var(--rat-surface-2); border: 1px solid var(--rat-border-2);
    border-radius: var(--rat-radius-sm); color: var(--rat-text);
    font-size: .875rem; font-family: inherit; text-align: center; outline: none;
    transition: border-color var(--rat-transition);
}
.rat-coeff-global-input:focus { border-color: var(--rat-accent); box-shadow: 0 0 0 3px rgba(99,102,241,.15); }
.rat-global-hint { font-size: .75rem; color: var(--rat-muted-2); }

/* ═══════════════════════════════════════════
   LÉGENDE SEMESTRES
═══════════════════════════════════════════ */
.rat-sem-legend {
    display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
    margin-bottom: 1rem; font-size: .75rem;
}
.rat-sem-pill {
    padding: .22rem .65rem; border-radius: 20px;
    font-size: .72rem; font-weight: 700; white-space: nowrap;
}
.rat-sem-both { background: var(--rat-accent-bg);  border: 1px solid rgba(99,102,241,.25); color: var(--rat-accent); }
.rat-sem-s1   { background: var(--rat-success-bg); border: 1px solid rgba(16,185,129,.25); color: var(--rat-success); }
.rat-sem-s2   { background: var(--rat-warn-bg);    border: 1px solid rgba(245,158,11,.25);  color: var(--rat-warn); }
.rat-legend-note { color: var(--rat-muted-2); font-size: .72rem; }

/* ═══════════════════════════════════════════
   TABLEAU
═══════════════════════════════════════════ */
.rat-table-wrap {
    overflow-x: auto;
    border-radius: var(--rat-radius-sm);
    border: 1px solid var(--rat-border);
}
.rat-table { width: 100%; border-collapse: collapse; font-size: .855rem; }

.rat-th {
    padding: .75rem 1rem;
    background: var(--rat-surface-2); border-bottom: 2px solid var(--rat-border-2);
    font-size: .75rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: var(--rat-muted-2);
    text-align: left; white-space: nowrap;
}
.rat-th--subject { min-width: 160px; }
.rat-th--sem     { min-width: 170px; }
.rat-th--classes { min-width: 220px; }
.rat-th--coeff   { min-width: 130px; }

.rat-table tbody tr {
    border-bottom: 1px solid var(--rat-border);
    transition: background var(--rat-transition);
}
.rat-table tbody tr:last-child { border-bottom: none; }
.rat-table tbody tr:hover { background: rgba(99,102,241,.03); }
.rat-table tbody td { padding: .85rem 1rem; vertical-align: middle; }

/* Nom matière */
.rat-subject-name { font-weight: 700; color: var(--rat-text-bright); font-size: .88rem; }

/* ─── Toggle semestre inline ─── */
.rat-sem-toggle {
    display: inline-flex;
    border: 1px solid var(--rat-border-2);
    border-radius: var(--rat-radius-sm);
    overflow: hidden;
}
.rat-sem-opt {
    padding: .35rem .7rem;
    font-size: .73rem; font-weight: 700;
    cursor: pointer; border: none; background: transparent;
    color: var(--rat-muted-2); font-family: inherit;
    transition: all var(--rat-transition); white-space: nowrap;
}
.rat-sem-opt:not(:last-child) { border-right: 1px solid var(--rat-border-2); }
.rat-sem-opt:hover:not(.active-both):not(.active-s1):not(.active-s2) {
    background: rgba(255,255,255,.05); color: var(--rat-text);
}
.rat-sem-opt.active-both { background: var(--rat-accent-bg);  color: var(--rat-accent);  }
.rat-sem-opt.active-s1   { background: var(--rat-success-bg); color: var(--rat-success); }
.rat-sem-opt.active-s2   { background: var(--rat-warn-bg);    color: var(--rat-warn);    }

/* ─── Classes checkboxes style pill ─── */
.rat-classes-wrap { display: flex; flex-wrap: wrap; gap: .45rem; }
.rat-class-label {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .3rem .75rem;
    background: var(--rat-surface-2); border: 1px solid var(--rat-border-2);
    border-radius: 20px; cursor: pointer; font-size: .78rem; font-weight: 500;
    color: var(--rat-text); transition: all var(--rat-transition);
    user-select: none;
}
.rat-class-label:hover { border-color: rgba(99,102,241,.35); background: var(--rat-accent-bg); color: var(--rat-accent); }
.rat-class-label input[type=checkbox] { display: none; }
.rat-class-label.checked {
    background: var(--rat-accent-bg); border-color: rgba(99,102,241,.4); color: var(--rat-accent);
}
.rat-check-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: var(--rat-muted); flex-shrink: 0;
    transition: background var(--rat-transition);
}
.rat-class-label.checked .rat-check-dot { background: var(--rat-accent); }

/* ─── Input coefficient ─── */
.rat-coeff-input {
    width: 85px; height: 38px; padding: 0 .65rem; text-align: center;
    background: var(--rat-surface-2); border: 1px solid var(--rat-border-2);
    border-radius: var(--rat-radius-sm); color: var(--rat-text);
    font-size: .875rem; font-family: inherit; outline: none;
    transition: border-color var(--rat-transition), box-shadow var(--rat-transition);
}
.rat-coeff-input:focus { border-color: var(--rat-accent); box-shadow: 0 0 0 3px rgba(99,102,241,.15); }

/* Empty row */
.rat-empty-row {
    text-align: center; padding: 3.5rem 1rem !important;
    color: var(--rat-muted-2); font-size: .85rem;
}
.rat-empty-row i { font-size: 1.5rem; display: block; margin-bottom: .6rem; color: var(--rat-muted); }

/* ═══════════════════════════════════════════
   ACTIONS
═══════════════════════════════════════════ */
.rat-actions { display: flex; justify-content: flex-end; margin-top: .5rem; animation: ratFadeUp .3s .1s ease both; }
.rat-btn-primary {
    display: inline-flex; align-items: center; gap: .6rem;
    padding: .75rem 1.75rem;
    background: linear-gradient(135deg, var(--rat-accent), var(--rat-accent-2));
    color: #fff; border: none; border-radius: var(--rat-radius-sm);
    font-size: .875rem; font-family: inherit; font-weight: 700; cursor: pointer;
    box-shadow: 0 4px 16px var(--rat-accent-glow);
    transition: all var(--rat-transition);
}
.rat-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px var(--rat-accent-glow); }
.rat-btn-primary:active { transform: translateY(0); }

input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; }
input[type=number] { -moz-appearance: textfield; }
</style>

@endsection

@section('another_JS')
<script>
(function () {

/* ── Sélecteurs ── */
const yearSel      = document.getElementById('year_id');
const sectorSel    = document.getElementById('sector_id');
const promotionSel = document.getElementById('promotion_id');
const promotionBody= document.getElementById('promotionBody');
const globalCoeff  = document.getElementById('globalCoefficient');
const matieresCard = document.getElementById('matieresCard');
const actionsBar   = document.getElementById('actionsBar');

/* ── Utilitaire XSS ── */
function escHtml(s) {
    const d = document.createElement('div');
    d.textContent = s ?? '';
    return d.innerHTML;
}

/* ════════════════════════════════════════════
   CHARGEMENT DES FILIÈRES
════════════════════════════════════════════ */
yearSel.addEventListener('change', () => {
    const yearId = yearSel.value;
    sectorSel.innerHTML = '<option>Chargement…</option>';
    sectorSel.disabled  = true;
    promotionSel.innerHTML = '<option value="">— Filière d\'abord —</option>';
    promotionSel.disabled  = true;
    matieresCard.style.display = 'none';
    actionsBar.style.display   = 'none';
    resetBody();

    if (!yearId) return;

    fetch(`/api/ratios/sectors/${yearId}`)
        .then(r => r.json())
        .then(data => {
            sectorSel.innerHTML = '<option value="">— Choisir une filière —</option>';
            data.forEach(s => {
                sectorSel.innerHTML += `<option value="${s.id}">${escHtml(s.name)}</option>`;
            });
            sectorSel.disabled = false;
        })
        .catch(() => {
            sectorSel.innerHTML = '<option value="">— Erreur de chargement —</option>';
        });
});

/* ════════════════════════════════════════════
   CHARGEMENT DES PROMOTIONS
════════════════════════════════════════════ */
sectorSel.addEventListener('change', () => {
    const yearId   = yearSel.value;
    const sectorId = sectorSel.value;
    promotionSel.innerHTML = '<option>Chargement…</option>';
    promotionSel.disabled  = true;
    matieresCard.style.display = 'none';
    actionsBar.style.display   = 'none';
    resetBody();

    if (!sectorId) return;

    fetch(`/api/ratios/promotions/${yearId}/${sectorId}`)
        .then(r => r.json())
        .then(data => {
            promotionSel.innerHTML = '<option value="">— Choisir une promotion —</option>';
            data.forEach(p => {
                promotionSel.innerHTML += `<option value="${p.id}">${escHtml(p.name)}</option>`;
            });
            promotionSel.disabled = false;
        })
        .catch(() => {
            promotionSel.innerHTML = '<option value="">— Erreur de chargement —</option>';
        });
});

/* ════════════════════════════════════════════
   CHARGEMENT DES MATIÈRES + CLASSES + RATIOS
════════════════════════════════════════════ */
promotionSel.addEventListener('change', () => {
    const yearId      = yearSel.value;
    const promotionId = promotionSel.value;

    if (!promotionId) {
        matieresCard.style.display = 'none';
        actionsBar.style.display   = 'none';
        return;
    }

    promotionBody.innerHTML = `
        <tr><td colspan="4" class="rat-empty-row">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Chargement…</span>
        </td></tr>`;
    matieresCard.style.display = 'block';
    actionsBar.style.display   = 'none';

    fetch(`/api/ratios/data/${promotionId}/${yearId}`)
        .then(r => r.json())
        .then(data => renderTable(data))
        .catch(() => {
            promotionBody.innerHTML = `
                <tr><td colspan="4" class="rat-empty-row">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Erreur lors du chargement des données.</span>
                </td></tr>`;
        });
});

/* ════════════════════════════════════════════
   RENDU DU TABLEAU
════════════════════════════════════════════ */
function renderTable(data) {
    const { subjects, classes, ratios } = data;

    if (!subjects || !subjects.length) {
        promotionBody.innerHTML = `
            <tr><td colspan="4" class="rat-empty-row">
                <i class="fas fa-exclamation-circle"></i>
                <span>Aucune matière enregistrée pour cette promotion.<br>
                Ajoutez d'abord les matières dans <em>Gestion des matières</em>.</span>
            </td></tr>`;
        actionsBar.style.display = 'none';
        return;
    }

    /*
     * Index des ratios existants :
     * key = "subjectId_classroomId" → { coefficient, semester }
     */
    const ratioIndex = {};
    (ratios || []).forEach(r => {
        ratioIndex[`${r.subject_id}_${r.classroom_id}`] = r;
    });

    let html = '';

    subjects.forEach((subject, idx) => {
        /*
         * Semestre par défaut : depuis promotion_subjects (subject.semester).
         * Si un ratio existe déjà pour cette matière, on prend son semester.
         */
        const existingForSubject = (ratios || []).filter(r => r.subject_id === subject.id);
        const rawSem = existingForSubject.length > 0
            ? existingForSubject[0].semester   // valeur enregistrée dans ratios
            : subject.semester;                 // valeur depuis promotion_subjects

        // Normalisation : null | 1 | 2
        const currentSem = (rawSem === 1 || rawSem === '1') ? 1
            : ((rawSem === 2 || rawSem === '2') ? 2 : null);

        /* Coefficient sauvegardé */
        const savedCoeff = existingForSubject[0]?.coefficient ?? '';

        /* ─── Boutons toggle semestre ─── */
        const semButtons = [
            { val: 'null', label: '<i class="fas fa-infinity" style="font-size:.6rem;"></i> S1+S2', active: currentSem === null ? 'active-both' : '' },
            { val: '1',    label: 'S1',  active: currentSem === 1    ? 'active-s1'   : '' },
            { val: '2',    label: 'S2',  active: currentSem === 2    ? 'active-s2'   : '' },
        ].map(b => `
            <button type="button"
                    class="rat-sem-opt ${b.active}"
                    data-sem="${b.val}"
                    data-sem-idx="${idx}"
                    onclick="setSemester(this)">
                ${b.label}
            </button>`).join('');

        /* ─── Cases à cocher des classes ─── */
        const classCheckboxes = (classes || []).map(c => {
            const key     = `${subject.id}_${c.id}`;
            const checked = ratioIndex[key] !== undefined;
            return `
                <label class="rat-class-label${checked ? ' checked' : ''}" onclick="toggleLabel(this)">
                    <input type="checkbox"
                           name="ratios[${idx}][classroom_ids][]"
                           value="${c.id}"
                           ${checked ? 'checked' : ''}>
                    <span class="rat-check-dot"></span>
                    ${escHtml(c.name)}
                </label>`;
        }).join('');

        /* Valeur du champ hidden semester */
        const semHiddenVal = currentSem !== null ? currentSem : '';

        html += `
            <tr>
                <td>
                    <div class="rat-subject-name">${escHtml(subject.name)}</div>
                    <input type="hidden" name="ratios[${idx}][subject_id]" value="${subject.id}">
                </td>
                <td>
                    <div class="rat-sem-toggle">${semButtons}</div>
                    <input type="hidden"
                           name="ratios[${idx}][semester]"
                           id="sem_input_${idx}"
                           value="${semHiddenVal}">
                </td>
                <td>
                    <div class="rat-classes-wrap">
                        ${classCheckboxes || '<span style="color:var(--rat-muted-2);font-size:.8rem;">Aucune classe</span>'}
                    </div>
                </td>
                <td>
                    <input type="number"
                           name="ratios[${idx}][coefficient]"
                           class="rat-coeff-input coeff-field"
                           min="1" max="20"
                           value="${escHtml(String(savedCoeff))}"
                           placeholder="1">
                </td>
            </tr>`;
    });

    promotionBody.innerHTML = html;
    actionsBar.style.display = 'flex';
}

/* ════════════════════════════════════════════
   TOGGLE SEMESTRE
   Cycle : S1+S2 → S1 → S2 → S1+S2
   Ou clic direct sur un bouton
════════════════════════════════════════════ */
window.setSemester = function (btn) {
    const idx    = btn.dataset.semIdx;
    const semVal = btn.dataset.sem; // 'null' | '1' | '2'

    /* Mettre à jour les boutons du groupe */
    document.querySelectorAll(`.rat-sem-opt[data-sem-idx="${idx}"]`).forEach(b => {
        b.classList.remove('active-both', 'active-s1', 'active-s2');
        if (b.dataset.sem === semVal) {
            if      (semVal === '1')    b.classList.add('active-s1');
            else if (semVal === '2')    b.classList.add('active-s2');
            else                        b.classList.add('active-both');
        }
    });

    /* Mettre à jour le champ hidden */
    const input = document.getElementById(`sem_input_${idx}`);
    if (input) input.value = semVal === 'null' ? '' : semVal;
};

/* ════════════════════════════════════════════
   TOGGLE CLASSE (pill cliquable)
════════════════════════════════════════════ */
window.toggleLabel = function (label) {
    const cb  = label.querySelector('input[type=checkbox]');
    cb.checked = !cb.checked;
    label.classList.toggle('checked', cb.checked);
};

/* ════════════════════════════════════════════
   COEFFICIENT GLOBAL
════════════════════════════════════════════ */
globalCoeff.addEventListener('input', () => {
    const v = globalCoeff.value;
    if (!v) return;
    document.querySelectorAll('.coeff-field').forEach(inp => { inp.value = v; });
});

/* ════════════════════════════════════════════
   TOUT COCHER / DÉCOCHER
════════════════════════════════════════════ */
window.selectAll = function () {
    document.querySelectorAll('.rat-class-label').forEach(label => {
        label.querySelector('input[type=checkbox]').checked = true;
        label.classList.add('checked');
    });
};

window.deselectAll = function () {
    document.querySelectorAll('.rat-class-label').forEach(label => {
        label.querySelector('input[type=checkbox]').checked = false;
        label.classList.remove('checked');
    });
};

/* ════════════════════════════════════════════
   RESET BODY
════════════════════════════════════════════ */
function resetBody() {
    promotionBody.innerHTML = `
        <tr><td colspan="4" class="rat-empty-row">
            <i class="fas fa-table"></i>
            <span>Sélectionnez une promotion pour afficher les matières</span>
        </td></tr>`;
}

})();
</script>
@endsection
