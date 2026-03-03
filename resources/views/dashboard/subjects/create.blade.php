@extends('layouts.template')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="subj-wrapper">

    {{-- ── En-tête ────────────────────────────────────────── --}}
    <div class="subj-header">
        <div class="subj-header-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <div>
            <h1 class="subj-title">Gestion des Matières</h1>
            <p class="subj-subtitle">Associez les matières à chaque promotion par filière et année scolaire</p>
        </div>
    </div>

    @if (session('success'))
    <div class="subj-alert subj-alert-success">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
        <button class="subj-alert-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if ($errors->any())
    <div class="subj-alert subj-alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ $errors->first() }}</span>
        <button class="subj-alert-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <form method="POST" action="{{ route('subject.store') }}" id="subjectForm">
        @csrf

        {{-- ── Étape 1 : Filtres ──────────────────────────── --}}
        <div class="subj-card">
            <div class="subj-card-header">
                <div class="subj-step-badge">1</div>
                <div>
                    <h2 class="subj-card-title">Sélection de la filière</h2>
                    <p class="subj-card-desc">Choisissez l'année scolaire puis la filière</p>
                </div>
            </div>

            <div class="subj-grid-2">
                {{-- Année --}}
                <div class="subj-field">
                    <label class="subj-label" for="year_id">
                        <i class="fas fa-calendar-alt"></i> Année scolaire
                    </label>
                    <div class="subj-select-wrap">
                        <select name="year_id" id="year_id" class="subj-select" required>
                            <option value="">— Choisissez une année —</option>
                            @foreach($years as $year)
                                <option value="{{ $year->id }}">{{ $year->year }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down subj-select-icon"></i>
                    </div>
                </div>

                {{-- Filière --}}
                <div class="subj-field">
                    <label class="subj-label" for="sector_id">
                        <i class="fas fa-sitemap"></i> Filière
                    </label>
                    <div class="subj-select-wrap">
                        <select name="sector_id" id="sector_id" class="subj-select" disabled required>
                            <option value="">— Choisissez d'abord une année —</option>
                        </select>
                        <i class="fas fa-chevron-down subj-select-icon"></i>
                        <div class="subj-select-loader" id="sectorLoader">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Étape 2 : Promotions ────────────────────────── --}}
        <div class="subj-card" id="promotionsCard" style="display:none;">
            <div class="subj-card-header">
                <div class="subj-step-badge">2</div>
                <div style="flex:1;">
                    <h2 class="subj-card-title">Matières par promotion</h2>
                    <p class="subj-card-desc">Ajoutez les matières pour chaque promotion</p>
                </div>
                {{-- Copier vers toutes --}}
                <button type="button" class="subj-btn-ghost" id="copyFirstBtn" style="display:none;">
                    <i class="fas fa-copy"></i>
                    <span>Copier la 1<sup>ère</sup> vers toutes</span>
                </button>
            </div>

            {{-- Suggestions de matières existantes --}}
            <div class="subj-suggestions" id="suggestionsPanel">
                <p class="subj-suggestions-label">
                    <i class="fas fa-lightbulb"></i>
                    <span id="suggestionsLabelText">Cliquez sur un champ de promotion pour activer les suggestions</span>
                </p>
                <div class="subj-suggestions-list" id="suggestionsList">
                    @foreach($allSubjects as $subject)
                        <button type="button" class="subj-suggestion-chip" data-name="{{ $subject->name }}" disabled>
                            {{ $subject->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Tableau des promotions --}}
            <div id="promotionRows"></div>

            {{-- Résumé --}}
            <div class="subj-summary" id="summaryPanel" style="display:none;">
                <i class="fas fa-info-circle"></i>
                <span id="summaryText"></span>
            </div>
        </div>

        {{-- ── Actions ─────────────────────────────────────── --}}
        <div class="subj-actions" id="actionsBar" style="display:none;">
            <button type="button" class="subj-btn-secondary" onclick="resetForm()">
                <i class="fas fa-redo"></i> Réinitialiser
            </button>
            <button type="submit" class="subj-btn-primary" id="submitBtn">
                <i class="fas fa-save"></i>
                <span>Enregistrer les matières</span>
                <div class="subj-btn-loader" id="btnLoader" style="display:none;">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
            </button>
        </div>

    </form>
</div>

{{-- ── Template de ligne promotion ─────────────────────── --}}
<template id="promotionRowTemplate">
    <div class="subj-promo-row" data-promo-id="">

        <div class="subj-promo-header">
            <div class="subj-promo-badge">
                <i class="fas fa-layer-group"></i>
            </div>
            <span class="subj-promo-name"></span>
            <span class="subj-promo-count">0 matière(s)</span>
        </div>

        {{-- Champ caché pour le formulaire --}}
        <input type="hidden" name="promotion_ids[]" value="">

        {{-- Zone de chips --}}
        <div class="subj-chips-zone">
            <div class="subj-chips-list"></div>
            <div class="subj-chips-input-wrap">
                <input type="text"
                       class="subj-chip-input"
                       placeholder="Ajouter une matière… (Entrée pour valider)"
                       autocomplete="off">
                <div class="subj-autocomplete-dropdown"></div>
            </div>
        </div>

        {{-- Champ hidden envoyé au controller --}}
        <input type="hidden" name="subjects_by_promotion[]" value="" class="subj-hidden-subjects">

    </div>
</template>

{{-- Données pour l'autocomplétion --}}
<script>
    const ALL_SUBJECTS = @json($allSubjects->pluck('name'));
</script>

<style>
/* ================================================================
   ROOT & FONTS
   ================================================================ */
.subj-wrapper, .subj-wrapper * {
    font-family: 'Plus Jakarta Sans', sans-serif;
    box-sizing: border-box;
}

:root {
    --subj-bg:          #080c14;
    --subj-surface:     #0f1520;
    --subj-surface-2:   #141c2e;
    --subj-border:      rgba(255,255,255,.07);
    --subj-border-2:    rgba(255,255,255,.11);

    --subj-accent:      #6366f1;
    --subj-accent-2:    #8b5cf6;
    --subj-accent-bg:   rgba(99,102,241,.1);
    --subj-accent-glow: rgba(99,102,241,.3);

    --subj-success:     #10b981;
    --subj-success-bg:  rgba(16,185,129,.1);
    --subj-danger:      #f87171;
    --subj-danger-bg:   rgba(248,113,113,.1);
    --subj-warn:        #f59e0b;
    --subj-warn-bg:     rgba(245,158,11,.1);

    --subj-text:        #c8d0e0;
    --subj-text-bright: #e8ecf4;
    --subj-muted:       #3d4d6a;
    --subj-muted-2:     #566480;

    --subj-radius:      14px;
    --subj-radius-sm:   8px;
    --subj-transition:  .2s cubic-bezier(.4,0,.2,1);
}

/* ================================================================
   WRAPPER
   ================================================================ */
.subj-wrapper {
    max-width: 920px;
    margin: 0 auto;
    padding: 2rem 1.5rem 4rem;
    color: var(--subj-text);
}

/* ================================================================
   HEADER
   ================================================================ */
.subj-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}
.subj-header-icon {
    width: 52px; height: 52px;
    background: var(--subj-accent-bg);
    border: 1px solid rgba(99,102,241,.25);
    border-radius: 15px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
    color: var(--subj-accent);
    box-shadow: 0 0 20px rgba(99,102,241,.2);
    flex-shrink: 0;
}
.subj-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--subj-text-bright);
    margin: 0 0 .25rem;
    letter-spacing: -.02em;
}
.subj-subtitle {
    font-size: .83rem;
    color: var(--subj-muted-2);
    margin: 0;
}

/* ================================================================
   ALERTS
   ================================================================ */
.subj-alert {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .85rem 1rem;
    border-radius: var(--subj-radius-sm);
    margin-bottom: 1.25rem;
    font-size: .83rem;
    font-weight: 500;
    animation: slideDown .25s ease both;
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}
.subj-alert i:first-child { font-size: .9rem; flex-shrink: 0; }
.subj-alert span { flex: 1; }
.subj-alert-close {
    background: transparent; border: none; cursor: pointer;
    color: inherit; opacity: .6; padding: 0; font-size: .8rem;
    transition: opacity var(--subj-transition);
}
.subj-alert-close:hover { opacity: 1; }
.subj-alert-success {
    background: var(--subj-success-bg);
    border: 1px solid rgba(16,185,129,.2);
    color: var(--subj-success);
}
.subj-alert-danger {
    background: var(--subj-danger-bg);
    border: 1px solid rgba(248,113,113,.2);
    color: var(--subj-danger);
}

/* ================================================================
   CARD
   ================================================================ */
.subj-card {
    background: var(--subj-surface);
    border: 1px solid var(--subj-border);
    border-radius: var(--subj-radius);
    padding: 1.5rem;
    margin-bottom: 1.25rem;
    animation: fadeUp .3s ease both;
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
.subj-card-header {
    display: flex;
    align-items: center;
    gap: .85rem;
    margin-bottom: 1.4rem;
    flex-wrap: wrap;
}
.subj-step-badge {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--subj-accent), var(--subj-accent-2));
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem;
    font-weight: 800;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 4px 12px var(--subj-accent-glow);
}
.subj-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--subj-text-bright);
    margin: 0 0 .2rem;
    letter-spacing: -.01em;
}
.subj-card-desc {
    font-size: .77rem;
    color: var(--subj-muted-2);
    margin: 0;
}

/* ================================================================
   GRID
   ================================================================ */
.subj-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
@media (max-width: 600px) { .subj-grid-2 { grid-template-columns: 1fr; } }

/* ================================================================
   FIELDS & SELECTS
   ================================================================ */
.subj-field { display: flex; flex-direction: column; gap: .45rem; }
.subj-label {
    font-size: .78rem;
    font-weight: 600;
    color: var(--subj-muted-2);
    text-transform: uppercase;
    letter-spacing: .06em;
    display: flex;
    align-items: center;
    gap: .4rem;
}
.subj-label i { color: var(--subj-accent); font-size: .75rem; }

.subj-select-wrap { position: relative; }
.subj-select {
    width: 100%;
    padding: .72rem 2.2rem .72rem .9rem;
    background: var(--subj-surface-2);
    border: 1px solid var(--subj-border-2);
    border-radius: var(--subj-radius-sm);
    color: var(--subj-text);
    font-size: .85rem;
    font-family: inherit;
    appearance: none;
    cursor: pointer;
    transition: border-color var(--subj-transition), box-shadow var(--subj-transition);
    outline: none;
}
.subj-select:focus {
    border-color: var(--subj-accent);
    box-shadow: 0 0 0 3px rgba(99,102,241,.15);
}
.subj-select:disabled {
    opacity: .45;
    cursor: not-allowed;
}
.subj-select option { background: var(--subj-surface-2); }
.subj-select-icon {
    position: absolute;
    right: .8rem; top: 50%;
    transform: translateY(-50%);
    font-size: .65rem;
    color: var(--subj-muted);
    pointer-events: none;
}
.subj-select-loader {
    position: absolute;
    right: .8rem; top: 50%;
    transform: translateY(-50%);
    color: var(--subj-accent);
    font-size: .8rem;
    display: none;
}

/* ================================================================
   SUGGESTIONS
   ================================================================ */
.subj-suggestions {
    background: rgba(99,102,241,.04);
    border: 1px solid rgba(99,102,241,.12);
    border-radius: var(--subj-radius-sm);
    padding: .85rem 1rem;
    margin-bottom: 1.25rem;
}
.subj-suggestions-label {
    font-size: .73rem;
    font-weight: 600;
    color: var(--subj-accent);
    margin: 0 0 .65rem;
    text-transform: uppercase;
    letter-spacing: .06em;
    display: flex;
    align-items: center;
    gap: .4rem;
}
.subj-suggestions-list {
    display: flex;
    flex-wrap: wrap;
    gap: .4rem;
}
.subj-suggestion-chip {
    padding: .3rem .75rem;
    background: var(--subj-surface-2);
    border: 1px solid var(--subj-border-2);
    border-radius: 20px;
    color: var(--subj-text);
    font-size: .75rem;
    font-family: inherit;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--subj-transition);
}
.subj-suggestion-chip:hover {
    background: var(--subj-accent-bg);
    border-color: rgba(99,102,241,.3);
    color: var(--subj-accent);
    transform: translateY(-1px);
}
.subj-suggestion-chip:disabled {
    opacity: .3;
    cursor: not-allowed;
    pointer-events: none;
}
.subj-suggestion-chip.already-in {
    opacity: .35;
    text-decoration: line-through;
    cursor: default;
    pointer-events: none;
}

/* ================================================================
   PROMOTION ROWS
   ================================================================ */
.subj-promo-row {
    background: var(--subj-surface-2);
    border: 1px solid var(--subj-border);
    border-radius: var(--subj-radius-sm);
    padding: 1rem 1.1rem;
    margin-bottom: .75rem;
    transition: border-color var(--subj-transition);
}
.subj-promo-row:focus-within,
.subj-promo-row.active-target {
    border-color: rgba(99,102,241,.4);
    background: linear-gradient(135deg, #141c2e 0%, rgba(99,102,241,.04) 100%);
}
.subj-promo-row.active-target .subj-promo-badge {
    background: linear-gradient(135deg, var(--subj-accent), var(--subj-accent-2));
    border-color: transparent;
    box-shadow: 0 4px 12px var(--subj-accent-glow);
}
.subj-promo-row.active-target .subj-promo-badge i { color: #fff; }
.subj-promo-row.active-target .subj-promo-name { color: var(--subj-accent); }

.subj-promo-header {
    display: flex;
    align-items: center;
    gap: .65rem;
    margin-bottom: .85rem;
}
.subj-promo-badge {
    width: 28px; height: 28px;
    background: var(--subj-accent-bg);
    border: 1px solid rgba(99,102,241,.2);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: var(--subj-accent);
    font-size: .7rem;
    flex-shrink: 0;
}
.subj-promo-name {
    font-size: .87rem;
    font-weight: 700;
    color: var(--subj-text-bright);
    flex: 1;
    letter-spacing: -.01em;
}
.subj-promo-count {
    font-size: .72rem;
    font-weight: 600;
    color: var(--subj-muted-2);
    background: var(--subj-surface);
    padding: .2rem .6rem;
    border-radius: 20px;
    border: 1px solid var(--subj-border);
    transition: all var(--subj-transition);
}
.subj-promo-count.has-items {
    color: var(--subj-success);
    background: var(--subj-success-bg);
    border-color: rgba(16,185,129,.2);
}

/* ================================================================
   CHIPS ZONE
   ================================================================ */
.subj-chips-zone {
    min-height: 46px;
    background: var(--subj-surface);
    border: 1px solid var(--subj-border-2);
    border-radius: var(--subj-radius-sm);
    padding: .45rem .55rem;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: .4rem;
    cursor: text;
    transition: border-color var(--subj-transition), box-shadow var(--subj-transition);
}
.subj-chips-zone:focus-within {
    border-color: var(--subj-accent);
    box-shadow: 0 0 0 3px rgba(99,102,241,.12);
}

.subj-chips-list {
    display: contents; /* chips are direct siblings inside zone */
}

/* Chip item */
.subj-chip {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .65rem;
    background: var(--subj-accent-bg);
    border: 1px solid rgba(99,102,241,.25);
    border-radius: 20px;
    color: var(--subj-text-bright);
    font-size: .77rem;
    font-weight: 600;
    animation: chipIn .15s ease both;
    flex-shrink: 0;
}
@keyframes chipIn {
    from { opacity: 0; transform: scale(.85); }
    to   { opacity: 1; transform: scale(1); }
}
.subj-chip-remove {
    background: transparent;
    border: none;
    cursor: pointer;
    color: var(--subj-muted-2);
    padding: 0;
    font-size: .65rem;
    line-height: 1;
    display: flex; align-items: center; justify-content: center;
    width: 14px; height: 14px;
    border-radius: 50%;
    transition: all var(--subj-transition);
}
.subj-chip-remove:hover {
    color: var(--subj-danger);
    background: rgba(248,113,113,.15);
}

/* Input inside chips zone */
.subj-chips-input-wrap {
    position: relative;
    flex: 1;
    min-width: 160px;
}
.subj-chip-input {
    width: 100%;
    background: transparent;
    border: none;
    outline: none;
    color: var(--subj-text);
    font-size: .82rem;
    font-family: inherit;
    padding: .3rem .2rem;
    caret-color: var(--subj-accent);
}
.subj-chip-input::placeholder { color: var(--subj-muted); }

/* Autocomplete dropdown */
.subj-autocomplete-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0; right: 0;
    background: var(--subj-surface);
    border: 1px solid var(--subj-border-2);
    border-radius: var(--subj-radius-sm);
    box-shadow: 0 12px 32px rgba(0,0,0,.45);
    z-index: 100;
    overflow: hidden;
    display: none;
    animation: dropIn .15s ease both;
}
@keyframes dropIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.subj-autocomplete-dropdown.open { display: block; }
.subj-autocomplete-item {
    padding: .55rem .9rem;
    font-size: .82rem;
    color: var(--subj-text);
    cursor: pointer;
    transition: background var(--subj-transition), color var(--subj-transition);
    display: flex;
    align-items: center;
    gap: .5rem;
}
.subj-autocomplete-item:hover,
.subj-autocomplete-item.selected {
    background: var(--subj-accent-bg);
    color: var(--subj-text-bright);
}
.subj-autocomplete-item i {
    font-size: .7rem;
    color: var(--subj-accent);
    flex-shrink: 0;
}
.subj-autocomplete-item em {
    color: var(--subj-accent);
    font-style: normal;
    font-weight: 700;
}

/* ================================================================
   RÉSUMÉ
   ================================================================ */
.subj-summary {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .75rem 1rem;
    background: rgba(245,158,11,.06);
    border: 1px solid rgba(245,158,11,.18);
    border-radius: var(--subj-radius-sm);
    color: var(--subj-warn);
    font-size: .8rem;
    font-weight: 500;
    margin-top: .75rem;
}
.subj-summary.ok {
    background: var(--subj-success-bg);
    border-color: rgba(16,185,129,.2);
    color: var(--subj-success);
}

/* ================================================================
   BOUTONS
   ================================================================ */
.subj-actions {
    display: flex;
    justify-content: flex-end;
    gap: .75rem;
    align-items: center;
    margin-top: .5rem;
    animation: fadeUp .3s .1s ease both;
}

.subj-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: .6rem;
    padding: .72rem 1.5rem;
    background: linear-gradient(135deg, var(--subj-accent), var(--subj-accent-2));
    color: #fff;
    border: none;
    border-radius: var(--subj-radius-sm);
    font-size: .85rem;
    font-family: inherit;
    font-weight: 700;
    cursor: pointer;
    letter-spacing: -.01em;
    box-shadow: 0 4px 16px var(--subj-accent-glow);
    transition: all var(--subj-transition);
}
.subj-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px var(--subj-accent-glow);
}
.subj-btn-primary:active { transform: translateY(0); }

.subj-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .72rem 1.2rem;
    background: transparent;
    color: var(--subj-muted-2);
    border: 1px solid var(--subj-border-2);
    border-radius: var(--subj-radius-sm);
    font-size: .83rem;
    font-family: inherit;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--subj-transition);
}
.subj-btn-secondary:hover {
    background: rgba(255,255,255,.04);
    color: var(--subj-text);
    border-color: rgba(255,255,255,.15);
}

.subj-btn-ghost {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .42rem .85rem;
    background: rgba(99,102,241,.08);
    color: var(--subj-accent);
    border: 1px solid rgba(99,102,241,.2);
    border-radius: var(--subj-radius-sm);
    font-size: .76rem;
    font-family: inherit;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--subj-transition);
    white-space: nowrap;
}
.subj-btn-ghost:hover {
    background: rgba(99,102,241,.16);
    border-color: rgba(99,102,241,.4);
}

.subj-btn-loader { display: inline-flex; align-items: center; }
</style>

@endsection

@section('another_JS')
<script>
(function () {
    /* ── Sélecteurs DOM ─────────────────────────────────────── */
    const yearSel        = document.getElementById('year_id');
    const sectorSel      = document.getElementById('sector_id');
    const sectorLoader   = document.getElementById('sectorLoader');
    const promotionsCard = document.getElementById('promotionsCard');
    const promotionRows  = document.getElementById('promotionRows');
    const actionsBar     = document.getElementById('actionsBar');
    const copyFirstBtn   = document.getElementById('copyFirstBtn');
    const summaryPanel   = document.getElementById('summaryPanel');
    const summaryText    = document.getElementById('summaryText');
    const form           = document.getElementById('subjectForm');
    const template       = document.getElementById('promotionRowTemplate');

    /* ── Année → Filières ───────────────────────────────────── */
    yearSel.addEventListener('change', () => {
        const yearId = yearSel.value;
        if (!yearId) return;

        sectorSel.innerHTML = '';
        sectorSel.disabled  = true;
        sectorLoader.style.display = 'block';
        document.querySelector('.subj-select-icon').style.display = 'none';

        fetch(`/api/subject-sectors-by-year/${yearId}`)
            .then(r => r.json())
            .then(data => {
                let html = '<option value="">— Choisissez une filière —</option>';
                data.forEach(s => { html += `<option value="${s.id}">${s.name}</option>`; });
                sectorSel.innerHTML = html;
                sectorSel.disabled  = false;
            })
            .finally(() => {
                sectorLoader.style.display = 'none';
                document.querySelector('.subj-select-icon').style.display = '';
            });

        // Masquer les promotions si on change d'année
        promotionsCard.style.display = 'none';
        actionsBar.style.display     = 'none';
    });

    /* ── Filière → Promotions ───────────────────────────────── */
    sectorSel.addEventListener('change', () => {
        const yearId   = yearSel.value;
        const sectorId = sectorSel.value;
        if (!sectorId) { promotionsCard.style.display = 'none'; return; }

        promotionRows.innerHTML = '<div class="text-center py-3" style="color:var(--subj-muted-2);font-size:.82rem;"><i class="fas fa-spinner fa-spin me-2"></i>Chargement…</div>';
        promotionsCard.style.display = 'block';

        fetch(`/api/subject-promotions/${yearId}/${sectorId}`)
            .then(r => r.json())
            .then(data => {
                promotionRows.innerHTML = '';
                if (!data.length) {
                    promotionRows.innerHTML = '<p style="color:var(--subj-muted-2);font-size:.83rem;text-align:center;padding:.5rem;">Aucune promotion trouvée.</p>';
                    actionsBar.style.display = 'none';
                    copyFirstBtn.style.display = 'none';
                    return;
                }

                data.forEach((promo, idx) => {
                    const row = buildPromotionRow(promo, idx);
                    promotionRows.appendChild(row);
                });

                actionsBar.style.display     = 'flex';
                copyFirstBtn.style.display   = data.length > 1 ? 'flex' : 'none';
                setActiveRow(null);
                updateSummary();
            });
    });

    /* ── Promotion active (cible des suggestions) ───────────── */
    let activeRow = null; // référence à la .subj-promo-row courante

    function setActiveRow(row) {
        // Retirer le style actif de l'ancien
        document.querySelectorAll('.subj-promo-row.active-target').forEach(r => r.classList.remove('active-target'));

        activeRow = row;
        if (!row) {
            document.getElementById('suggestionsLabelText').textContent =
                'Cliquez sur un champ de promotion pour activer les suggestions';
            document.querySelectorAll('.subj-suggestion-chip').forEach(c => {
                c.disabled = true;
                c.classList.remove('already-in');
            });
            return;
        }

        row.classList.add('active-target');
        const promoName = row.querySelector('.subj-promo-name').textContent;
        document.getElementById('suggestionsLabelText').innerHTML =
            `<i class="fas fa-arrow-right" style="font-size:.65rem;"></i> Ajouter à <strong>${escHtml(promoName)}</strong> :`;

        // Activer les chips et marquer celles déjà présentes dans cette promo
        const currentInRow = Array.from(row.querySelectorAll('.subj-chip'))
            .map(c => c.dataset.name.toLowerCase());

        document.querySelectorAll('.subj-suggestion-chip').forEach(c => {
            c.disabled = false;
            c.classList.toggle('already-in', currentInRow.includes(c.dataset.name.toLowerCase()));
        });
    }

    /* ── Construire une ligne promotion ─────────────────────── */
    function buildPromotionRow(promo, idx) {
        const frag = template.content.cloneNode(true);
        const row  = frag.querySelector('.subj-promo-row');

        row.dataset.promoId = promo.id;
        row.querySelector('input[name="promotion_ids[]"]').value = promo.id;
        row.querySelector('.subj-promo-name').textContent        = promo.promotion_sector;

        const chipsList = row.querySelector('.subj-chips-list');
        const hiddenIn  = row.querySelector('.subj-hidden-subjects');
        const countEl   = row.querySelector('.subj-promo-count');
        const inputEl   = row.querySelector('.subj-chip-input');
        const dropdown  = row.querySelector('.subj-autocomplete-dropdown');
        const zone      = row.querySelector('.subj-chips-zone');

        // Pré-remplir les matières existantes
        promo.subjects.forEach(name => addChip(chipsList, hiddenIn, countEl, name));

        // Focus → cette promo devient la cible active des suggestions
        inputEl.addEventListener('focus', () => {
            // On doit accéder au row réel dans le DOM (pas le fragment)
            setActiveRow(inputEl.closest('.subj-promo-row'));
        });

        // Clic sur la zone → focus input
        zone.addEventListener('click', () => inputEl.focus());

        // Input : autocomplétion + ajout au Enter
        inputEl.addEventListener('input', () => {
            const q = inputEl.value.trim().toLowerCase();
            renderDropdown(dropdown, q, chipsList, hiddenIn, countEl, inputEl);
        });

        inputEl.addEventListener('keydown', e => {
            const items = dropdown.querySelectorAll('.subj-autocomplete-item');
            const sel   = dropdown.querySelector('.subj-autocomplete-item.selected');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const next = sel ? sel.nextElementSibling : items[0];
                if (next) { sel?.classList.remove('selected'); next.classList.add('selected'); }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prev = sel ? sel.previousElementSibling : null;
                if (prev) { sel?.classList.remove('selected'); prev.classList.add('selected'); }
            } else if (e.key === 'Enter' || e.key === ';') {
                e.preventDefault();
                const name = sel ? sel.dataset.name : inputEl.value.trim().replace(/;$/, '');
                if (name) {
                    addChip(chipsList, hiddenIn, countEl, name, true);
                    inputEl.value = '';
                    dropdown.classList.remove('open');
                    refreshActiveSuggestions();
                    updateSummary();
                }
            } else if (e.key === 'Backspace' && !inputEl.value) {
                const chips = chipsList.querySelectorAll('.subj-chip');
                if (chips.length) chips[chips.length - 1].remove();
                syncHidden(chipsList, hiddenIn, countEl);
                refreshActiveSuggestions();
                updateSummary();
            } else if (e.key === 'Escape') {
                dropdown.classList.remove('open');
            }
        });

        // Fermer dropdown si clic ailleurs
        document.addEventListener('click', e => {
            if (!zone.contains(e.target)) dropdown.classList.remove('open');
        }, { passive: true });

        return frag;
    }

    /* ── Ajouter un chip ────────────────────────────────────── */
    function addChip(chipsList, hiddenIn, countEl, name, checkDuplicate = false) {
        name = name.trim();
        if (!name) return;

        // Éviter les doublons
        if (checkDuplicate) {
            const existing = Array.from(chipsList.querySelectorAll('.subj-chip'))
                .map(c => c.dataset.name.toLowerCase());
            if (existing.includes(name.toLowerCase())) return;
        }

        const chip = document.createElement('span');
        chip.className    = 'subj-chip';
        chip.dataset.name = name;
        chip.innerHTML    = `${escHtml(name)}<button type="button" class="subj-chip-remove" aria-label="Supprimer"><i class="fas fa-times"></i></button>`;
        chip.querySelector('.subj-chip-remove').addEventListener('click', () => {
            chip.style.animation = 'chipIn .12s ease reverse';
            setTimeout(() => {
                chip.remove();
                syncHidden(chipsList, hiddenIn, countEl);
                refreshActiveSuggestions();
                updateSummary();
            }, 100);
        });

        chipsList.appendChild(chip);
        syncHidden(chipsList, hiddenIn, countEl);
    }

    /* ── Synchroniser le champ hidden ──────────────────────── */
    function syncHidden(chipsList, hiddenIn, countEl) {
        const names = Array.from(chipsList.querySelectorAll('.subj-chip'))
            .map(c => c.dataset.name);
        hiddenIn.value = names.join('; ');
        countEl.textContent = `${names.length} matière(s)`;
        countEl.classList.toggle('has-items', names.length > 0);
    }

    /* ── Autocomplétion ─────────────────────────────────────── */
    function renderDropdown(dropdown, query, chipsList, hiddenIn, countEl, inputEl) {
        if (!query) { dropdown.classList.remove('open'); return; }

        const current = Array.from(chipsList.querySelectorAll('.subj-chip'))
            .map(c => c.dataset.name.toLowerCase());

        const matches = ALL_SUBJECTS.filter(s =>
            s.toLowerCase().includes(query) && !current.includes(s.toLowerCase())
        ).slice(0, 8);

        if (!matches.length) { dropdown.classList.remove('open'); return; }

        const re = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        dropdown.innerHTML = matches.map(s =>
            `<div class="subj-autocomplete-item" data-name="${escHtml(s)}">
                <i class="fas fa-plus"></i>
                <span>${escHtml(s).replace(re, '<em>$1</em>')}</span>
             </div>`
        ).join('');
        dropdown.classList.add('open');

        dropdown.querySelectorAll('.subj-autocomplete-item').forEach(item => {
            item.addEventListener('mousedown', e => {
                e.preventDefault();
                addChip(chipsList, hiddenIn, countEl, item.dataset.name, true);
                inputEl.value = '';
                dropdown.classList.remove('open');
                inputEl.focus();
                refreshActiveSuggestions();
                updateSummary();
            });
        });
    }

    /* ── Rafraîchir l'état des suggestions selon la promo active ─ */
    function refreshActiveSuggestions() {
        if (!activeRow) return;
        const currentInRow = Array.from(activeRow.querySelectorAll('.subj-chip'))
            .map(c => c.dataset.name.toLowerCase());
        document.querySelectorAll('.subj-suggestion-chip').forEach(c => {
            c.classList.toggle('already-in', currentInRow.includes(c.dataset.name.toLowerCase()));
        });
    }

    /* ── Résumé global ──────────────────────────────────────── */
    function updateSummary() {
        const rows     = promotionRows.querySelectorAll('.subj-promo-row');
        let total      = 0;
        let emptyCount = 0;

        rows.forEach(row => {
            const chips = row.querySelectorAll('.subj-chip').length;
            total += chips;
            if (!chips) emptyCount++;
        });

        if (!rows.length) { summaryPanel.style.display = 'none'; return; }

        summaryPanel.style.display = 'flex';
        if (emptyCount > 0) {
            summaryPanel.className = 'subj-summary';
            summaryText.textContent = `${emptyCount} promotion(s) sans matière — elles seront vidées à l'enregistrement.`;
        } else {
            summaryPanel.className = 'subj-summary ok';
            summaryText.textContent = `Tout est renseigné : ${total} matière(s) réparties sur ${rows.length} promotion(s).`;
        }
    }

    /* ── Copier la 1ère promotion vers toutes ───────────────── */
    copyFirstBtn.addEventListener('click', () => {
        const rows     = promotionRows.querySelectorAll('.subj-promo-row');
        if (rows.length < 2) return;

        const firstNames = Array.from(rows[0].querySelectorAll('.subj-chip'))
            .map(c => c.dataset.name);

        if (!firstNames.length) {
            alert('La première promotion n\'a aucune matière à copier.');
            return;
        }

        rows.forEach((row, i) => {
            if (i === 0) return;
            const chipsList = row.querySelector('.subj-chips-list');
            const hiddenIn  = row.querySelector('.subj-hidden-subjects');
            const countEl   = row.querySelector('.subj-promo-count');
            chipsList.innerHTML = '';
            firstNames.forEach(name => addChip(chipsList, hiddenIn, countEl, name));
        });

        refreshActiveSuggestions();
        updateSummary();
    });

    /* ── Suggestions → ajouter à la promo ACTIVE ───────────── */
    document.querySelectorAll('.subj-suggestion-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            if (!activeRow) return;
            const chipsList = activeRow.querySelector('.subj-chips-list');
            const hiddenIn  = activeRow.querySelector('.subj-hidden-subjects');
            const countEl   = activeRow.querySelector('.subj-promo-count');
            addChip(chipsList, hiddenIn, countEl, chip.dataset.name, true);
            refreshActiveSuggestions();
            updateSummary();
            // Refocus l'input de la promo active
            activeRow.querySelector('.subj-chip-input')?.focus();
        });
    });

    /* ── Réinitialiser la promo active si on clique ailleurs ── */
    document.addEventListener('click', e => {
        if (!e.target.closest('.subj-promo-row') && !e.target.closest('.subj-suggestions')) {
            // On garde activeRow en mémoire mais on retire juste le highlight
            // pour que l'utilisateur sache qu'il doit refocuser
        }
    }, { passive: true });

    /* ── Submit : loader ────────────────────────────────────── */
    form.addEventListener('submit', () => {
        document.getElementById('btnLoader').style.display = 'inline-flex';
        document.querySelector('#submitBtn span').style.display = 'none';
    });

    /* ── Reset ──────────────────────────────────────────────── */
    window.resetForm = function () {
        yearSel.value   = '';
        sectorSel.value = '';
        sectorSel.disabled = true;
        sectorSel.innerHTML = '<option value="">— Choisissez d\'abord une année —</option>';
        promotionsCard.style.display = 'none';
        actionsBar.style.display     = 'none';
        promotionRows.innerHTML      = '';
        activeRow = null;
        setActiveRow(null);
    };

    /* ── Utilitaire ─────────────────────────────────────────── */
    function escHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
})();
</script>
@endsection
