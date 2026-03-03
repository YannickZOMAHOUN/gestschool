@extends('layouts.template')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<div class="cls-wrapper">

    {{-- ── En-tête ──────────────────────────────────────────── --}}
    <div class="cls-header">
        <div class="cls-header-left">
            <div class="cls-header-icon">
                <i class="fas fa-door-open"></i>
            </div>
            <div>
                <h1 class="cls-title">Gestion des Classes</h1>
                <p class="cls-subtitle">Configurez le nombre de classes par promotion</p>
            </div>
        </div>
        <div class="cls-header-stats" id="headerStats" style="display:none;">
            <div class="cls-stat">
                <span class="cls-stat-value" id="statPromotions">0</span>
                <span class="cls-stat-label">Promotions</span>
            </div>
            <div class="cls-stat-divider"></div>
            <div class="cls-stat">
                <span class="cls-stat-value" id="statClasses">0</span>
                <span class="cls-stat-label">Classes total</span>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="cls-alert cls-alert-success">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="cls-alert-close"><i class="fas fa-times"></i></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="cls-alert cls-alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ $errors->first() }}</span>
        <button onclick="this.parentElement.remove()" class="cls-alert-close"><i class="fas fa-times"></i></button>
    </div>
    @endif

    <form method="POST" action="{{ route('promotion-classrooms.store') }}" id="classroomForm">
        @csrf

        {{-- ── Étape 1 : Filtres ──────────────────────────── --}}
        <div class="cls-card">
            <div class="cls-card-header">
                <div class="cls-step-badge">1</div>
                <div>
                    <h2 class="cls-card-title">Sélection</h2>
                    <p class="cls-card-desc">Année scolaire et filière</p>
                </div>
            </div>

            <div class="cls-filters-grid">
                {{-- Année --}}
                <div class="cls-field">
                    <label class="cls-label" for="year_id">
                        <i class="fas fa-calendar-alt"></i> Année scolaire
                    </label>
                    <div class="cls-select-wrap">
                        <select name="year_id" id="year_id" class="cls-select" required>
                            <option value="">— Choisissez une année —</option>
                            @foreach($years as $year)
                                <option value="{{ $year->id }}">{{ $year->year }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down cls-select-arrow"></i>
                        <span class="cls-select-loader" id="sectorLoader" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </div>
                </div>

                {{-- Filière --}}
                <div class="cls-field">
                    <label class="cls-label" for="sector_id">
                        <i class="fas fa-sitemap"></i> Filière
                    </label>
                    <div class="cls-select-wrap">
                        <select name="sector_id" id="sector_id" class="cls-select" disabled required>
                            <option value="">— Choisissez d'abord une année —</option>
                        </select>
                        <i class="fas fa-chevron-down cls-select-arrow"></i>
                        <span class="cls-select-loader" id="promotionLoader" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </div>
                </div>

                {{-- Nombre général --}}
                <div class="cls-field">
                    <label class="cls-label" for="general_count">
                        <i class="fas fa-magic"></i> Appliquer à toutes
                    </label>
                    <div class="cls-general-wrap">
                        <button type="button" class="cls-counter-btn" id="generalMinus" disabled>
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" id="general_count" class="cls-counter-input"
                               min="1" max="26" placeholder="—" disabled>
                        <button type="button" class="cls-counter-btn" id="generalPlus" disabled>
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Étape 2 : Promotions ────────────────────────── --}}
        <div class="cls-card" id="promotionsCard" style="display:none;">
            <div class="cls-card-header">
                <div class="cls-step-badge">2</div>
                <div style="flex:1;">
                    <h2 class="cls-card-title">Classes par promotion</h2>
                    <p class="cls-card-desc">Définissez le nombre de classes pour chaque promotion</p>
                </div>
                {{-- Légende statuts --}}
                <div class="cls-legend">
                    <span class="cls-legend-item cls-legend-new">
                        <i class="fas fa-circle"></i> Nouvelles
                    </span>
                    <span class="cls-legend-item cls-legend-existing">
                        <i class="fas fa-circle"></i> Existantes
                    </span>
                    <span class="cls-legend-item cls-legend-modified">
                        <i class="fas fa-circle"></i> Modifiées
                    </span>
                    <span class="cls-legend-item cls-legend-locked">
                        <i class="fas fa-lock"></i> Verrouillées (notes)
                    </span>
                </div>
            </div>

            <div id="promotionRows"></div>
        </div>

        {{-- ── Actions ─────────────────────────────────────── --}}
        <div class="cls-actions" id="actionsBar" style="display:none;">
            <button type="button" class="cls-btn-secondary" onclick="resetForm()">
                <i class="fas fa-redo"></i> Réinitialiser
            </button>
            <button type="submit" class="cls-btn-primary" id="submitBtn">
                <i class="fas fa-save"></i>
                <span id="submitLabel">Enregistrer les classes</span>
                <span id="submitLoader" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span>
            </button>
        </div>
    </form>
</div>

{{-- Template ligne promotion --}}
<template id="promoRowTpl">
    <div class="cls-promo-row" data-promo-id="" data-original-count="0">

        <div class="cls-promo-info">
            <div class="cls-promo-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="cls-promo-details">
                <span class="cls-promo-name"></span>
                <span class="cls-promo-status"></span>
            </div>
            <div class="cls-promo-preview" id="classPreview"></div>
        </div>

        <input type="hidden" name="promotions[]" value="">

        <div class="cls-counter">
            <button type="button" class="cls-row-btn cls-row-minus">
                <i class="fas fa-minus"></i>
            </button>
            <div class="cls-count-display">
                <input type="number" name="counts[]" class="cls-count-input"
                       min="0" max="26" required>
                <span class="cls-count-unit">classe(s)</span>
            </div>
            <button type="button" class="cls-row-btn cls-row-plus">
                <i class="fas fa-plus"></i>
            </button>
        </div>

    </div>
</template>

<style>
/* ================================================================
   ROOT & FONTS
   ================================================================ */
.cls-wrapper, .cls-wrapper * {
    font-family: 'DM Sans', sans-serif;
    box-sizing: border-box;
}

:root {
    --cls-bg:           #07090f;
    --cls-surface:      #0e1320;
    --cls-surface-2:    #131929;
    --cls-surface-3:    #192033;
    --cls-border:       rgba(255,255,255,.06);
    --cls-border-2:     rgba(255,255,255,.1);

    --cls-accent:       #38bdf8;   /* sky-400 — couleur différente des autres pages */
    --cls-accent-2:     #0ea5e9;
    --cls-accent-3:     #7dd3fc;
    --cls-accent-bg:    rgba(56,189,248,.09);
    --cls-accent-glow:  rgba(56,189,248,.25);

    --cls-success:      #34d399;
    --cls-success-bg:   rgba(52,211,153,.09);
    --cls-warn:         #fbbf24;
    --cls-warn-bg:      rgba(251,191,36,.09);
    --cls-danger:       #f87171;
    --cls-danger-bg:    rgba(248,113,113,.09);

    --cls-text:         #bac4d6;
    --cls-text-bright:  #e2e8f0;
    --cls-muted:        #2d3a52;
    --cls-muted-2:      #475872;

    --cls-radius:       16px;
    --cls-radius-sm:    10px;
    --cls-radius-xs:    7px;
    --cls-transition:   .2s cubic-bezier(.4,0,.2,1);

    --cls-new-color:    #38bdf8;
    --cls-exist-color:  #34d399;
    --cls-mod-color:    #fbbf24;
}

/* ================================================================
   WRAPPER
   ================================================================ */
.cls-wrapper {
    max-width: 860px;
    margin: 0 auto;
    padding: 2rem 1.5rem 5rem;
    color: var(--cls-text);
}

/* ================================================================
   HEADER
   ================================================================ */
.cls-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    gap: 1rem;
    flex-wrap: wrap;
}
.cls-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.cls-header-icon {
    width: 50px; height: 50px;
    background: var(--cls-accent-bg);
    border: 1px solid rgba(56,189,248,.2);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    color: var(--cls-accent);
    box-shadow: 0 0 20px var(--cls-accent-glow);
    flex-shrink: 0;
}
.cls-title {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--cls-text-bright);
    margin: 0 0 .2rem;
    letter-spacing: -.025em;
}
.cls-subtitle {
    font-size: .8rem;
    color: var(--cls-muted-2);
    margin: 0;
}

/* Stats header */
.cls-header-stats {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--cls-surface);
    border: 1px solid var(--cls-border-2);
    border-radius: var(--cls-radius-sm);
    padding: .65rem 1.1rem;
    animation: fadeIn .3s ease;
}
.cls-stat { text-align: center; }
.cls-stat-value {
    display: block;
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--cls-accent);
    letter-spacing: -.03em;
    line-height: 1;
}
.cls-stat-label {
    font-size: .68rem;
    color: var(--cls-muted-2);
    text-transform: uppercase;
    letter-spacing: .07em;
    font-weight: 600;
}
.cls-stat-divider {
    width: 1px; height: 28px;
    background: var(--cls-border-2);
}

/* ================================================================
   ALERTS
   ================================================================ */
.cls-alert {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .85rem 1rem;
    border-radius: var(--cls-radius-xs);
    margin-bottom: 1.25rem;
    font-size: .82rem;
    font-weight: 500;
    animation: slideDown .25s ease;
}
@keyframes slideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
.cls-alert span { flex: 1; }
.cls-alert-close { background:transparent; border:none; cursor:pointer; color:inherit; opacity:.6; padding:0; font-size:.8rem; transition:opacity var(--cls-transition); }
.cls-alert-close:hover { opacity:1; }
.cls-alert-success { background:var(--cls-success-bg); border:1px solid rgba(52,211,153,.2); color:var(--cls-success); }
.cls-alert-danger  { background:var(--cls-danger-bg);  border:1px solid rgba(248,113,113,.2); color:var(--cls-danger); }

/* ================================================================
   CARD
   ================================================================ */
.cls-card {
    background: var(--cls-surface);
    border: 1px solid var(--cls-border);
    border-radius: var(--cls-radius);
    padding: 1.5rem;
    margin-bottom: 1.25rem;
    animation: fadeUp .3s ease;
}
@keyframes fadeUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
@keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

.cls-card-header {
    display: flex;
    align-items: center;
    gap: .85rem;
    margin-bottom: 1.4rem;
    flex-wrap: wrap;
}
.cls-step-badge {
    width: 30px; height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--cls-accent), var(--cls-accent-2));
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem;
    font-weight: 800;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 4px 12px var(--cls-accent-glow);
}
.cls-card-title {
    font-size: .98rem;
    font-weight: 700;
    color: var(--cls-text-bright);
    margin: 0 0 .15rem;
    letter-spacing: -.01em;
}
.cls-card-desc {
    font-size: .75rem;
    color: var(--cls-muted-2);
    margin: 0;
}

/* ================================================================
   FILTERS GRID
   ================================================================ */
.cls-filters-grid {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 1rem;
    align-items: end;
}
@media (max-width: 640px) { .cls-filters-grid { grid-template-columns: 1fr; } }

.cls-field { display: flex; flex-direction: column; gap: .45rem; }
.cls-label {
    font-size: .73rem;
    font-weight: 700;
    color: var(--cls-muted-2);
    text-transform: uppercase;
    letter-spacing: .07em;
    display: flex; align-items: center; gap: .4rem;
}
.cls-label i { color: var(--cls-accent); font-size: .7rem; }

/* Select */
.cls-select-wrap { position: relative; }
.cls-select {
    width: 100%;
    padding: .72rem 2.2rem .72rem .9rem;
    background: var(--cls-surface-2);
    border: 1px solid var(--cls-border-2);
    border-radius: var(--cls-radius-xs);
    color: var(--cls-text);
    font-size: .84rem;
    font-family: inherit;
    appearance: none;
    cursor: pointer;
    outline: none;
    transition: border-color var(--cls-transition), box-shadow var(--cls-transition);
}
.cls-select:focus { border-color: var(--cls-accent); box-shadow: 0 0 0 3px rgba(56,189,248,.12); }
.cls-select:disabled { opacity: .4; cursor: not-allowed; }
.cls-select option { background: var(--cls-surface-2); }
.cls-select-arrow {
    position: absolute; right: .8rem; top: 50%;
    transform: translateY(-50%);
    font-size: .62rem; color: var(--cls-muted);
    pointer-events: none;
}
.cls-select-loader {
    position: absolute; right: .8rem; top: 50%;
    transform: translateY(-50%);
    color: var(--cls-accent); font-size: .78rem;
}

/* Counter général */
.cls-general-wrap {
    display: flex;
    align-items: center;
    gap: 0;
    background: var(--cls-surface-2);
    border: 1px solid var(--cls-border-2);
    border-radius: var(--cls-radius-xs);
    overflow: hidden;
    transition: border-color var(--cls-transition);
}
.cls-general-wrap:focus-within { border-color: var(--cls-accent); }

.cls-counter-btn {
    width: 38px; height: 38px;
    background: transparent;
    border: none;
    color: var(--cls-muted-2);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .7rem;
    transition: all var(--cls-transition);
    flex-shrink: 0;
}
.cls-counter-btn:hover:not(:disabled) { background: rgba(56,189,248,.1); color: var(--cls-accent); }
.cls-counter-btn:disabled { opacity: .35; cursor: not-allowed; }

.cls-counter-input {
    flex: 1;
    min-width: 48px;
    background: transparent;
    border: none;
    outline: none;
    color: var(--cls-text-bright);
    font-size: .92rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
    text-align: center;
    padding: 0;
}
.cls-counter-input::placeholder { color: var(--cls-muted); font-weight: 400; font-size: .8rem; }
.cls-counter-input:disabled { opacity: .35; }

/* ================================================================
   LÉGENDE
   ================================================================ */
.cls-legend {
    display: flex;
    align-items: center;
    gap: .85rem;
    flex-wrap: wrap;
}
.cls-legend-item {
    display: flex;
    align-items: center;
    gap: .35rem;
    font-size: .71rem;
    font-weight: 600;
    color: var(--cls-muted-2);
}
.cls-legend-item i { font-size: .45rem; }
.cls-legend-new i      { color: var(--cls-new-color); }
.cls-legend-existing i { color: var(--cls-exist-color); }
.cls-legend-modified i { color: var(--cls-mod-color); }
.cls-legend-locked i   { color: var(--cls-warn); font-size: .6rem; }

/* ================================================================
   PROMOTION ROW
   ================================================================ */
.cls-promo-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.1rem;
    background: var(--cls-surface-2);
    border: 1px solid var(--cls-border);
    border-radius: var(--cls-radius-sm);
    margin-bottom: .65rem;
    transition: border-color var(--cls-transition), background var(--cls-transition);
    animation: fadeUp .25s ease both;
    flex-wrap: wrap;
}
.cls-promo-row:hover { border-color: var(--cls-border-2); }

/* Statuts visuels */
.cls-promo-row[data-status="new"] {
    border-left: 3px solid var(--cls-new-color);
}
.cls-promo-row[data-status="existing"] {
    border-left: 3px solid var(--cls-exist-color);
}
.cls-promo-row[data-status="modified"] {
    border-left: 3px solid var(--cls-mod-color);
    background: linear-gradient(90deg, rgba(251,191,36,.04) 0%, var(--cls-surface-2) 40%);
}

.cls-promo-info {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex: 1;
    min-width: 0;
}
.cls-promo-icon {
    width: 34px; height: 34px;
    background: var(--cls-surface-3);
    border: 1px solid var(--cls-border-2);
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: .75rem;
    color: var(--cls-muted-2);
    flex-shrink: 0;
    transition: all var(--cls-transition);
}
.cls-promo-row[data-status="new"]      .cls-promo-icon { color: var(--cls-new-color);   background: rgba(56,189,248,.08);   border-color: rgba(56,189,248,.2);   }
.cls-promo-row[data-status="existing"] .cls-promo-icon { color: var(--cls-exist-color); background: rgba(52,211,153,.08);   border-color: rgba(52,211,153,.2);   }
.cls-promo-row[data-status="modified"] .cls-promo-icon { color: var(--cls-mod-color);   background: rgba(251,191,36,.08);   border-color: rgba(251,191,36,.2);   }

.cls-promo-details {
    display: flex;
    flex-direction: column;
    gap: .2rem;
    min-width: 0;
}
.cls-promo-name {
    font-size: .88rem;
    font-weight: 700;
    color: var(--cls-text-bright);
    letter-spacing: -.01em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cls-promo-status {
    font-size: .69rem;
    font-weight: 600;
    padding: .12rem .5rem;
    border-radius: 20px;
    display: inline-block;
    width: fit-content;
}
.cls-promo-row[data-status="new"]      .cls-promo-status { background: rgba(56,189,248,.12);  color: var(--cls-new-color);   border: 1px solid rgba(56,189,248,.2); }
.cls-promo-row[data-status="existing"] .cls-promo-status { background: rgba(52,211,153,.12);  color: var(--cls-exist-color); border: 1px solid rgba(52,211,153,.2); }
.cls-promo-row[data-status="modified"] .cls-promo-status { background: rgba(251,191,36,.12);  color: var(--cls-mod-color);   border: 1px solid rgba(251,191,36,.2); }

/* Aperçu des noms de classes */
.cls-promo-preview {
    display: flex;
    flex-wrap: wrap;
    gap: .3rem;
    margin-left: .5rem;
}
.cls-class-tag {
    font-family: 'DM Mono', monospace;
    font-size: .68rem;
    font-weight: 500;
    padding: .15rem .45rem;
    background: var(--cls-surface-3);
    border: 1px solid var(--cls-border-2);
    border-radius: 5px;
    color: var(--cls-text);
    transition: all var(--cls-transition);
}
.cls-class-tag.is-new {
    background: rgba(56,189,248,.1);
    border-color: rgba(56,189,248,.25);
    color: var(--cls-accent-3);
}
.cls-class-tag.is-removed {
    background: rgba(248,113,113,.08);
    border-color: rgba(248,113,113,.2);
    color: var(--cls-danger);
    text-decoration: line-through;
    opacity: .6;
}
.cls-class-tag.is-locked {
    background: rgba(251,191,36,.08);
    border-color: rgba(251,191,36,.25);
    color: var(--cls-warn);
}
.cls-class-tag.is-locked::after {
    content: '\f023';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    font-size: .55rem;
    margin-left: .3rem;
    opacity: .8;
}

/* Counter par ligne */
.cls-counter {
    display: flex;
    align-items: center;
    gap: 0;
    background: var(--cls-surface-3);
    border: 1px solid var(--cls-border-2);
    border-radius: var(--cls-radius-xs);
    overflow: hidden;
    flex-shrink: 0;
}
.cls-row-btn {
    width: 34px; height: 40px;
    background: transparent;
    border: none;
    color: var(--cls-muted-2);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .65rem;
    transition: all var(--cls-transition);
}
.cls-row-btn:hover { background: rgba(56,189,248,.1); color: var(--cls-accent); }
.cls-count-display {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0 .4rem;
    min-width: 64px;
}
.cls-count-input {
    width: 48px;
    background: transparent;
    border: none;
    outline: none;
    color: var(--cls-text-bright);
    font-size: 1.05rem;
    font-weight: 800;
    font-family: 'DM Mono', monospace;
    text-align: center;
    padding: .3rem 0 0;
    line-height: 1;
}
.cls-count-input::-webkit-inner-spin-button,
.cls-count-input::-webkit-outer-spin-button { appearance: none; }
.cls-count-unit {
    font-size: .6rem;
    color: var(--cls-muted-2);
    letter-spacing: .04em;
    text-transform: uppercase;
    font-weight: 600;
    padding-bottom: .25rem;
}

/* ================================================================
   ACTIONS
   ================================================================ */
.cls-actions {
    display: flex;
    justify-content: flex-end;
    gap: .75rem;
    margin-top: .5rem;
    animation: fadeUp .3s .1s ease both;
}
.cls-btn-primary {
    display: inline-flex; align-items: center; gap: .6rem;
    padding: .72rem 1.5rem;
    background: linear-gradient(135deg, var(--cls-accent), var(--cls-accent-2));
    color: #fff;
    border: none; border-radius: var(--cls-radius-xs);
    font-size: .85rem; font-family: inherit; font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 16px var(--cls-accent-glow);
    transition: all var(--cls-transition);
    letter-spacing: -.01em;
}
.cls-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px var(--cls-accent-glow); }
.cls-btn-secondary {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .72rem 1.2rem;
    background: transparent; color: var(--cls-muted-2);
    border: 1px solid var(--cls-border-2); border-radius: var(--cls-radius-xs);
    font-size: .83rem; font-family: inherit; font-weight: 600;
    cursor: pointer; transition: all var(--cls-transition);
}
.cls-btn-secondary:hover { background: rgba(255,255,255,.04); color: var(--cls-text); border-color: rgba(255,255,255,.14); }
</style>

@endsection

@section('another_JS')
<script>
(function () {

    /* ── DOM ──────────────────────────────────────────────── */
    const yearSel        = document.getElementById('year_id');
    const sectorSel      = document.getElementById('sector_id');
    const sectorLoader   = document.getElementById('sectorLoader');
    const promoLoader    = document.getElementById('promotionLoader');
    const promotionsCard = document.getElementById('promotionsCard');
    const promotionRows  = document.getElementById('promotionRows');
    const actionsBar     = document.getElementById('actionsBar');
    const headerStats    = document.getElementById('headerStats');
    const generalCount   = document.getElementById('general_count');
    const generalMinus   = document.getElementById('generalMinus');
    const generalPlus    = document.getElementById('generalPlus');
    const tpl            = document.getElementById('promoRowTpl');
    const form           = document.getElementById('classroomForm');

    /* ── Année → Filières ─────────────────────────────────── */
    yearSel.addEventListener('change', () => {
        const yearId = yearSel.value;
        if (!yearId) return;

        sectorSel.innerHTML = '';
        sectorSel.disabled  = true;
        sectorLoader.style.display = 'block';
        document.querySelector('.cls-select-arrow').style.display = 'none';

        fetch(`/api/classroom-sectors-by-year/${yearId}`)
            .then(r => r.json())
            .then(data => {
                let html = '<option value="">— Choisissez une filière —</option>';
                data.forEach(s => { html += `<option value="${s.id}">${s.name}</option>`; });
                sectorSel.innerHTML = html;
                sectorSel.disabled  = false;
            })
            .finally(() => {
                sectorLoader.style.display = 'none';
                document.querySelector('.cls-select-arrow').style.display = '';
            });

        promotionsCard.style.display = 'none';
        actionsBar.style.display     = 'none';
        headerStats.style.display    = 'none';
        disableGeneralCounter();
    });

    /* ── Filière → Promotions + classes existantes ────────── */
    sectorSel.addEventListener('change', () => {
        const yearId   = yearSel.value;
        const sectorId = sectorSel.value;
        if (!sectorId) { promotionsCard.style.display = 'none'; return; }

        promotionRows.innerHTML = `
            <div style="text-align:center;padding:1.5rem;color:var(--cls-muted-2);font-size:.83rem;">
                <i class="fas fa-spinner fa-spin" style="margin-right:.5rem;color:var(--cls-accent);"></i>Chargement…
            </div>`;
        promotionsCard.style.display = 'block';
        actionsBar.style.display     = 'none';
        promoLoader.style.display    = 'block';

        // Charger promotions ET classes existantes en parallèle
        Promise.all([
            fetch(`/api/classroom-promotions/${yearId}/${sectorId}`).then(r => r.json()),
            fetch(`/api/existing-classrooms/${yearId}/${sectorId}`).then(r => r.json())
        ]).then(([promotions, existing]) => {

            promotionRows.innerHTML = '';

            if (!promotions.length) {
                promotionRows.innerHTML = '<p style="text-align:center;color:var(--cls-muted-2);font-size:.83rem;padding:.5rem;">Aucune promotion trouvée.</p>';
                return;
            }

            // Indexer les classes existantes par promotion_sector_id
            // On conserve has_notes pour chaque classe
            const existingMap = {};
            existing.forEach(cls => {
                if (!existingMap[cls.promotion_sector_id]) existingMap[cls.promotion_sector_id] = [];
                existingMap[cls.promotion_sector_id].push({ name: cls.name, has_notes: cls.has_notes });
            });

            promotions.forEach((promo, idx) => {
                const existingClasses = existingMap[promo.id] || [];
                const row = buildRow(promo, existingClasses, idx);
                promotionRows.appendChild(row);
            });

            actionsBar.style.display  = 'flex';
            headerStats.style.display = 'flex';
            enableGeneralCounter();
            updateStats();

        }).finally(() => {
            promoLoader.style.display = 'none';
        });
    });

    /* ── Construire une ligne promotion ─────────────────────── */
    function buildRow(promo, existingClasses, idx) {
        const frag = tpl.content.cloneNode(true);
        const row  = frag.querySelector('.cls-promo-row');

        const originalCount = existingClasses.length;
        // Nombre minimum imposé = nombre de classes ayant des notes (ne peuvent pas être supprimées)
        const lockedCount = existingClasses.filter(c => c.has_notes).length;

        row.dataset.promoId       = promo.id;
        row.dataset.originalCount = originalCount;
        row.dataset.lockedCount   = lockedCount;
        row.dataset.promoName     = promo.name;

        row.querySelector('.cls-promo-name').textContent = promo.name;
        row.querySelector('input[name="promotions[]"]').value = promo.id;

        const countInput = row.querySelector('.cls-count-input');
        const minusBtn   = row.querySelector('.cls-row-minus');
        const plusBtn    = row.querySelector('.cls-row-plus');
        const previewEl  = row.querySelector('.cls-promo-preview');
        const statusEl   = row.querySelector('.cls-promo-status');

        // Valeur initiale = nombre de classes existantes
        countInput.value = originalCount;
        // Minimum = classes verrouillées par des notes
        countInput.min   = lockedCount;

        // Style initial
        updateRowStatus(row, originalCount, originalCount, lockedCount, promo.name, existingClasses, previewEl, statusEl);

        // Désactiver le bouton moins si déjà au minimum verrouillé
        if (originalCount <= lockedCount) minusBtn.disabled = true;

        // Animation décalée
        row.style.animationDelay = `${idx * 0.05}s`;

        // Événements counter
        minusBtn.addEventListener('click', () => {
            const current = parseInt(countInput.value || 0);
            const v = Math.max(lockedCount, current - 1);
            countInput.value = v;
            minusBtn.disabled = (v <= lockedCount);
            onCountChange(row, v, originalCount, lockedCount, promo.name, existingClasses, previewEl, statusEl);
        });
        plusBtn.addEventListener('click', () => {
            const v = Math.min(26, parseInt(countInput.value || 0) + 1);
            countInput.value = v;
            minusBtn.disabled = (v <= lockedCount);
            onCountChange(row, v, originalCount, lockedCount, promo.name, existingClasses, previewEl, statusEl);
        });
        countInput.addEventListener('input', () => {
            let v = parseInt(countInput.value) || 0;
            v = Math.max(lockedCount, Math.min(26, v));
            countInput.value = v;
            minusBtn.disabled = (v <= lockedCount);
            onCountChange(row, v, originalCount, lockedCount, promo.name, existingClasses, previewEl, statusEl);
        });

        return frag;
    }

    /* ── Changement de valeur ────────────────────────────────── */
    function onCountChange(row, newCount, originalCount, lockedCount, promoName, existingClasses, previewEl, statusEl) {
        updateRowStatus(row, newCount, originalCount, lockedCount, promoName, existingClasses, previewEl, statusEl);
        updateStats();
        syncGeneralCounter();
    }

    /* ── Mettre à jour le statut + aperçu d'une ligne ────────── */
    function updateRowStatus(row, newCount, originalCount, lockedCount, promoName, existingClasses, previewEl, statusEl) {
        let status, statusText;
        if (originalCount === 0 && newCount > 0) {
            status = 'new';
            statusText = `Nouvelle — ${newCount} classe(s)`;
        } else if (originalCount > 0 && newCount === originalCount) {
            status = 'existing';
            statusText = `${originalCount} classe(s) existante(s)`;
            if (lockedCount > 0) statusText += ` · ${lockedCount} verrouillée(s) 🔒`;
        } else if (originalCount > 0 && newCount !== originalCount) {
            status = 'modified';
            const diff = newCount - originalCount;
            statusText = diff > 0
                ? `Modifié — +${diff} classe(s)`
                : `Modifié — ${diff} classe(s)`;
            if (lockedCount > 0) statusText += ` · ${lockedCount} verrouillée(s) 🔒`;
        } else {
            status = 'new';
            statusText = newCount > 0 ? `${newCount} classe(s)` : 'Aucune classe';
        }

        row.dataset.status = status;
        statusEl.textContent = statusText;

        renderPreview(previewEl, promoName, newCount, existingClasses);
    }

    /* ── Aperçu des noms (ex: Terminale-A, Terminale-B) ──────── */
    function renderPreview(previewEl, promoName, count, existingClasses) {
        const baseName = promoName.replace(/-[A-Z]$/, '');
        previewEl.innerHTML = '';

        // Classes existantes (objets {name, has_notes})
        existingClasses.forEach((cls, i) => {
            const tag = document.createElement('span');
            tag.className = 'cls-class-tag';
            tag.textContent = cls.name;

            if (cls.has_notes) {
                // Verrouillée — ne peut pas être supprimée
                tag.classList.add('is-locked');
            } else if (i >= count) {
                // Sera supprimée
                tag.classList.add('is-removed');
            }
            previewEl.appendChild(tag);
        });

        // Nouvelles classes à créer
        for (let i = existingClasses.length; i < count; i++) {
            const tag = document.createElement('span');
            tag.className = 'cls-class-tag is-new';
            tag.textContent = count > 1
                ? `${baseName}-${String.fromCharCode(65 + i)}`
                : baseName;
            previewEl.appendChild(tag);
        }

        // Si plus de 5 tags, masquer le surplus avec un +N
        const tags = previewEl.querySelectorAll('.cls-class-tag');
        if (tags.length > 5) {
            tags.forEach((t, i) => { if (i >= 4) t.style.display = 'none'; });
            const more = document.createElement('span');
            more.className = 'cls-class-tag';
            more.style.color = 'var(--cls-muted-2)';
            more.textContent = `+${tags.length - 4}`;
            previewEl.appendChild(more);
        }
    }

    /* ── Counter général ─────────────────────────────────────── */
    function enableGeneralCounter() {
        generalCount.disabled = false;
        generalMinus.disabled = false;
        generalPlus.disabled  = false;
    }
    function disableGeneralCounter() {
        generalCount.disabled = true;
        generalMinus.disabled = true;
        generalPlus.disabled  = true;
        generalCount.value    = '';
    }

    generalMinus.addEventListener('click', () => {
        const v = Math.max(1, parseInt(generalCount.value || 1) - 1);
        generalCount.value = v;
        applyGeneralCount(v);
    });
    generalPlus.addEventListener('click', () => {
        const v = Math.min(26, parseInt(generalCount.value || 0) + 1);
        generalCount.value = v;
        applyGeneralCount(v);
    });
    generalCount.addEventListener('input', () => {
        const v = parseInt(generalCount.value) || 0;
        if (v > 0) applyGeneralCount(v);
    });

    function applyGeneralCount(v) {
        promotionRows.querySelectorAll('.cls-promo-row').forEach(row => {
            const countInput  = row.querySelector('.cls-count-input');
            const minusBtn    = row.querySelector('.cls-row-minus');
            const previewEl   = row.querySelector('.cls-promo-preview');
            const statusEl    = row.querySelector('.cls-promo-status');
            const originalCount = parseInt(row.dataset.originalCount || 0);
            const lockedCount   = parseInt(row.dataset.lockedCount   || 0);
            const promoName     = row.dataset.promoName || '';

            // Ne pas descendre en dessous du nombre de classes verrouillées
            const safeV = Math.max(lockedCount, v);
            countInput.value  = safeV;
            minusBtn.disabled = (safeV <= lockedCount);

            // Reconstruire existingClasses depuis les tags existants (non "is-new")
            const existingTags = Array.from(row.querySelectorAll('.cls-class-tag:not(.is-new)'))
                .filter(t => !t.textContent.startsWith('+'))
                .map(t => ({
                    name: t.textContent.replace(/\s+/g, ''),
                    has_notes: t.classList.contains('is-locked')
                }));

            updateRowStatus(row, safeV, originalCount, lockedCount, promoName, existingTags, previewEl, statusEl);
        });
        updateStats();
    }

    function syncGeneralCounter() {
        const inputs = promotionRows.querySelectorAll('.cls-count-input');
        if (!inputs.length) return;
        const vals = Array.from(inputs).map(i => parseInt(i.value) || 0);
        const allSame = vals.every(v => v === vals[0]);
        if (allSame && vals[0] > 0) {
            generalCount.value = vals[0];
        } else {
            generalCount.value = '';
        }
    }

    /* ── Stats header ─────────────────────────────────────────── */
    function updateStats() {
        const rows = promotionRows.querySelectorAll('.cls-promo-row');
        let totalClasses = 0;
        rows.forEach(row => {
            totalClasses += parseInt(row.querySelector('.cls-count-input')?.value || 0);
        });
        document.getElementById('statPromotions').textContent = rows.length;
        document.getElementById('statClasses').textContent    = totalClasses;
    }

    /* ── Submit loader ─────────────────────────────────────────── */
    form.addEventListener('submit', () => {
        document.getElementById('submitLabel').style.display = 'none';
        document.getElementById('submitLoader').style.display = 'inline';
    });

    /* ── Reset ─────────────────────────────────────────────────── */
    window.resetForm = function () {
        yearSel.value  = '';
        sectorSel.value = '';
        sectorSel.disabled = true;
        sectorSel.innerHTML = '<option value="">— Choisissez d\'abord une année —</option>';
        promotionsCard.style.display = 'none';
        actionsBar.style.display     = 'none';
        headerStats.style.display    = 'none';
        promotionRows.innerHTML      = '';
        disableGeneralCounter();
    };

})();
</script>
@endsection
