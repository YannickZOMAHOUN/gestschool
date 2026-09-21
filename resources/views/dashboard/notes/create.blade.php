@extends('layouts.template')

{{-- ══════════════════════════════════════════════════════════
     Saisie des Notes — Lycée Technique de Bohicon
     NotesManager v3 · Compatible réseau local (sans Internet)
══════════════════════════════════════════════════════════ --}}

@section('content')

<div class="nm-page">

    {{-- ── TOASTS ── --}}
    <div class="toast-zone" id="toast-area"></div>

    {{-- ══ HERO HEADER ══ --}}
    <header class="site-header">
        <div class="header-bg-shape"></div>
        <div class="header-bg-shape header-bg-shape--2"></div>
        <div class="header-content">
            <div class="header-left">
                <div class="school-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                    Lycée Technique de Bohicon
                </div>
                <h1 class="page-title">Saisie des <em>Notes</em></h1>
                <p class="page-subtitle">Gestion des évaluations · NotesManager v3</p>
            </div>
            <div class="header-right">
                <div class="header-chips">
                    <div class="stat-chip">
                        <div class="stat-chip-icon stat-chip-icon--blue">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/></svg>
                        </div>
                        <div class="stat-chip-body">
                            <span class="stat-chip-val" id="student-count">0</span>
                            <span class="stat-chip-lbl">Élève(s)</span>
                        </div>
                    </div>
                    <div class="stat-chip">
                        <div class="stat-chip-icon stat-chip-icon--green">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="stat-chip-body">
                            <span class="stat-chip-val" id="saved-count">0</span>
                            <span class="stat-chip-lbl">Sauvegardé(s)</span>
                        </div>
                    </div>
                    <div class="stat-chip stat-chip--amber" id="pending-chip" style="display:none;">
                        <div class="stat-chip-icon stat-chip-icon--amber">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                        <div class="stat-chip-body">
                            <span class="stat-chip-val" id="saved-pending-text">0</span>
                            <span class="stat-chip-lbl">En attente</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- ══ BANNIÈRES ══ --}}
    <div class="banner banner--locked d-none" id="locked-banner">
        <div class="banner-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <div class="banner-body">
            <strong>Notes verrouillées</strong>
            <span>Consultation uniquement. Seul le censeur peut modifier.</span>
        </div>
    </div>
    <div class="banner banner--info d-none" id="partial-banner">
        <div class="banner-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="banner-body">
            <strong>Saisie partielle disponible</strong>
            <span>Les cases <strong>grisées avec cadenas</strong> ont déjà été enregistrées. Vous pouvez remplir les cases restantes.</span>
        </div>
    </div>

    {{-- ══ PANNEAU FILTRES ══ --}}
    <div class="nm-panel">
        <div class="panel-head">
            <div class="panel-head-left">
                <div class="panel-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                </div>
                <div>
                    <div class="panel-title">Contexte de saisie</div>
                    <div class="panel-subtitle">Sélectionnez la filière, la promotion, la classe et la matière</div>
                </div>
            </div>
            <div class="ctx-badge" id="ctx-badge"></div>
        </div>

        <div class="panel-body">

            {{-- ── Fil d'Ariane de la sélection ── --}}
            <div class="nm-breadcrumb" id="nm-breadcrumb">
                <div class="bc-item bc-item--year" id="bc-year">
                    <span class="bc-icon">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </span>
                    <span class="bc-label" id="bc-year-label">Année</span>
                </div>
                <span class="bc-sep">›</span>
                <div class="bc-item" id="bc-sector">
                    <span class="bc-icon">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10"/></svg>
                    </span>
                    <span class="bc-label" id="bc-sector-label">Filière</span>
                </div>
                <span class="bc-sep">›</span>
                <div class="bc-item" id="bc-promotion">
                    <span class="bc-icon">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </span>
                    <span class="bc-label" id="bc-promotion-label">Promotion</span>
                </div>
                <span class="bc-sep">›</span>
                <div class="bc-item" id="bc-classroom">
                    <span class="bc-icon">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                    </span>
                    <span class="bc-label" id="bc-classroom-label">Classe</span>
                </div>
            </div>

            {{-- ── Étape 1 : Filière ── --}}
            <div class="nm-step" id="step-sector">
                <div class="step-header">
                    <div class="step-num">1</div>
                    <div class="step-info">
                        <div class="step-title">Filière</div>
                        <div class="step-hint">Choisissez la filière d'enseignement</div>
                    </div>
                    <div class="step-loader" id="loader-sector" style="display:none;">
                        <div class="nm-spin-sm"></div>
                    </div>
                </div>
                <div class="step-options" id="options-sector">
                    {{-- Généré dynamiquement --}}
                </div>
            </div>

            {{-- ── Étape 2 : Promotion ── --}}
            <div class="nm-step nm-step--locked" id="step-promotion">
                <div class="step-header">
                    <div class="step-num">2</div>
                    <div class="step-info">
                        <div class="step-title">Promotion</div>
                        <div class="step-hint">Sélectionnez la promotion dans la filière choisie</div>
                    </div>
                    <div class="step-loader" id="loader-promotion" style="display:none;">
                        <div class="nm-spin-sm"></div>
                    </div>
                </div>
                <div class="step-options" id="options-promotion">
                    <div class="step-placeholder">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                        Choisissez d'abord une filière
                    </div>
                </div>
            </div>

            {{-- ── Étape 3 : Classe ── --}}
            <div class="nm-step nm-step--locked" id="step-classroom">
                <div class="step-header">
                    <div class="step-num">3</div>
                    <div class="step-info">
                        <div class="step-title">Classe</div>
                        <div class="step-hint">Choisissez la classe pour accéder aux notes</div>
                    </div>
                    <div class="step-loader" id="loader-classroom" style="display:none;">
                        <div class="nm-spin-sm"></div>
                    </div>
                </div>
                <div class="step-options" id="options-classroom">
                    <div class="step-placeholder">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                        Choisissez d'abord une promotion
                    </div>
                </div>
            </div>

            <div class="nm-sep">
                <span class="nm-sep-label">Configuration de l'évaluation</span>
            </div>

            {{-- ── Ligne 2 : Semestre + Matière ── --}}
            <div class="filters-row2" id="eval-config">

                <div class="fg">
                    <label>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M2 12h20"/></svg>
                        Semestre
                    </label>
                    <div class="sem-toggle">
                        <button type="button" class="sem-btn sem-btn--on" data-value="1">
                            <span class="sem-dot"></span>S1
                        </button>
                        <button type="button" class="sem-btn" data-value="2">
                            <span class="sem-dot"></span>S2
                        </button>
                    </div>
                    <input type="hidden" id="semester" value="1">
                </div>

                <div class="fg fg--wide">
                    <label>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        Matière
                    </label>
                    <div class="subject-row">
                        <div class="select-wrap" style="flex:1">
                            <select id="subject_id" disabled>
                                <option value="">— Sélectionner la classe d'abord —</option>
                            </select>
                            <div class="select-arrow">
                                <svg width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>
                        <div class="coeff-pill" id="coeff-pill">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                            Coeff <strong id="coefficient">—</strong>
                        </div>
                    </div>
                    <input type="hidden" id="ratio_id">
                    <input type="hidden" id="subject_real_id">
                </div>

            </div>

            {{-- Champs cachés (valeurs de contexte) --}}
            <input type="hidden" id="year_id">
            <input type="hidden" id="classroom_id">

            {{-- Barre de progression --}}
            <div class="progress-wrap" id="progress-wrap">
                <div class="progress-header">
                    <span class="progress-label-text">Progression des saisies</span>
                    <span class="progress-txt" id="progress-label">0%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
            </div>

        </div>
    </div>

    {{-- ══ RACCOURCIS ══ --}}
    <div class="shortcuts-bar" id="shortcuts-bar">
        <div class="sc-group">
            <span class="sc-item"><kbd>Tab</kbd> ou <kbd>↵</kbd> Navigation entre cellules</span>
            <span class="sc-divider"></span>
            <span class="sc-item"><kbd>Ctrl</kbd><kbd>S</kbd> Sauvegarde rapide</span>
            <span class="sc-divider"></span>
            <span class="sc-item">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Case grisée = note déjà enregistrée
            </span>
            <span class="sc-divider"></span>
            <span class="sc-item">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="4 6 4 2 20 2 20 22 4 22 4 18"/><polyline points="16 12 12 8 8 12"/><line x1="12" y1="8" x2="12" y2="21"/></svg>
                Clic en-tête = remplir colonne · Double-clic = moyenne
            </span>
        </div>
    </div>

    {{-- ══ ÉTAT VIDE ══ --}}
    <div class="empty-state" id="empty-state">
        <div class="empty-illustration">
            <div class="empty-rings">
                <div class="empty-ring empty-ring--1"></div>
                <div class="empty-ring empty-ring--2"></div>
                <div class="empty-ring empty-ring--3"></div>
            </div>
            <div class="empty-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="3" y1="15" x2="21" y2="15"/>
                    <line x1="9" y1="9" x2="9" y2="21"/>
                    <line x1="15" y1="9" x2="15" y2="21"/>
                </svg>
            </div>
        </div>
        <h3 id="empty-title">Chargement de l'année active…</h3>
        <p id="empty-desc">Veuillez patienter, le système initialise la saisie.</p>
        <div class="empty-steps" id="empty-steps-visual" style="display:none;">
            <div class="empty-step">
                <div class="es-num">1</div>
                <span>Filière</span>
            </div>
            <div class="es-arrow">→</div>
            <div class="empty-step">
                <div class="es-num">2</div>
                <span>Promotion</span>
            </div>
            <div class="es-arrow">→</div>
            <div class="empty-step">
                <div class="es-num">3</div>
                <span>Classe</span>
            </div>
            <div class="es-arrow">→</div>
            <div class="empty-step empty-step--action">
                <div class="es-num es-num--action">✓</div>
                <span>Matière</span>
            </div>
        </div>
    </div>

    {{-- ══ TABLEAU DES NOTES ══ --}}
    <div class="notes-card" id="notes-table-container">

        <div class="notes-card-header">
            <div class="card-title-area">
                <div class="card-title-dot-wrap">
                    <span class="card-title-dot"></span>
                    <span class="card-title-dot card-title-dot--2"></span>
                </div>
                <div class="card-title-text">
                    <span class="card-title-label">Matière active</span>
                    <span class="card-title-main" id="toolbar-subject">—</span>
                </div>
            </div>
            <div class="card-actions">
                <button class="btn-ghost" id="btn-reset">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.85"/></svg>
                    Changer de matière
                </button>
                <button class="btn-save" id="btn-save" disabled>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <span class="btn-save-text">Enregistrer</span>
                    <span class="save-badge" id="save-pending-badge" style="display:none;"></span>
                </button>
            </div>
        </div>

        <div class="table-scroll">
            <table class="nm-table">
                <thead>
                    <tr>
                        <th class="th-num">#</th>
                        <th class="th-name" colspan="2">
                            <div class="th-inner th-inner--left">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/></svg>
                                Élève
                            </div>
                        </th>
                        <th class="interro-header th-interro" data-index="0">
                            <div class="th-inner">
                                <span class="th-label th-label--i">I<sub>1</sub></span>
                                <input type="number" class="nm-hdr-inp" placeholder="—" min="0" max="20" step="0.01" title="Entrer une valeur → remplir colonne · Double-clic → moyenne">
                            </div>
                        </th>
                        <th class="interro-header th-interro" data-index="1">
                            <div class="th-inner">
                                <span class="th-label th-label--i">I<sub>2</sub></span>
                                <input type="number" class="nm-hdr-inp" placeholder="—" min="0" max="20" step="0.01">
                            </div>
                        </th>
                        <th class="interro-header th-interro" data-index="2">
                            <div class="th-inner">
                                <span class="th-label th-label--i">I<sub>3</sub></span>
                                <input type="number" class="nm-hdr-inp" placeholder="—" min="0" max="20" step="0.01">
                            </div>
                        </th>
                        <th class="th-avg">
                            <div class="th-inner">
                                <span class="th-label">Moy. I</span>
                                <span class="th-auto-badge">auto</span>
                            </div>
                        </th>
                        <th class="devoir-header th-devoir" data-field="devoir1">
                            <div class="th-inner">
                                <span class="th-label th-label--d">D<sub>1</sub></span>
                                <input type="number" class="nm-hdr-inp" placeholder="—" min="0" max="20" step="0.01">
                            </div>
                        </th>
                        <th class="devoir-header th-devoir" data-field="devoir2">
                            <div class="th-inner">
                                <span class="th-label th-label--d">D<sub>2</sub></span>
                                <input type="number" class="nm-hdr-inp" placeholder="—" min="0" max="20" step="0.01">
                            </div>
                        </th>
                        <th class="th-final">
                            <div class="th-inner">
                                <span class="th-label">Moy. /20</span>
                                <span class="th-auto-badge">auto</span>
                            </div>
                        </th>
                        <th style="width:36px;"></th>
                    </tr>
                </thead>
                <tbody id="notes-body"></tbody>
            </table>
        </div>

        {{-- Barre de statistiques --}}
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-item-icon stat-item-icon--neutral">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <span class="stat-val" id="stat-avg">—</span>
                <span class="stat-key">Moy. classe</span>
            </div>
            <div class="stat-item">
                <div class="stat-item-icon stat-item-icon--green">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span class="stat-val stat-val--g" id="stat-pass">—</span>
                <span class="stat-key">≥ 10 admis</span>
            </div>
            <div class="stat-item">
                <div class="stat-item-icon stat-item-icon--red">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </div>
                <span class="stat-val stat-val--r" id="stat-fail">—</span>
                <span class="stat-key">< 10 ajourné</span>
            </div>
            <div class="stat-item">
                <div class="stat-item-icon stat-item-icon--green">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </div>
                <span class="stat-val stat-val--g" id="stat-max">—</span>
                <span class="stat-key">Meilleure note</span>
            </div>
            <div class="stat-item">
                <div class="stat-item-icon stat-item-icon--red">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
                </div>
                <span class="stat-val stat-val--r" id="stat-min">—</span>
                <span class="stat-key">Note la plus faible</span>
            </div>
            <div class="stat-item stat-item--highlight">
                <div class="stat-item-icon stat-item-icon--blue">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <span class="stat-val stat-val--b" id="stat-taux">—</span>
                <span class="stat-key">Taux de réussite</span>
            </div>
        </div>

    </div>{{-- /.notes-card --}}

</div>{{-- /.nm-page --}}

@endsection


@section('another_JS')
<script>
/* ═══════════════════════════════════════════════════════════
   NotesManager v3 — Sélection contextuelle intelligente
   ✅ Année active auto-chargée au démarrage
   ✅ Cascade : Filière → Promotion → Classe (avec auto-sélection)
   ✅ Chargement automatique des notes dès la classe choisie
   ✅ Pas de bouton "Charger" — tout est automatique
   ✅ Compatible réseau local (Laragon)
═══════════════════════════════════════════════════════════ */
class NotesManager {
    constructor() {
        this.data          = null;
        this.canEdit       = true;
        this.canModify     = true;
        this.saved         = new Set();
        this.modified      = new Set();
        this.INTERRO_COUNT = 3;
        this.csrf          = '{{ csrf_token() }}';
        this.baseUrl       = '{{ rtrim(url('/'), '/') }}';

        // État de la cascade
        this.state = {
            yearId:      null,
            yearLabel:   null,
            sectorId:    null,
            sectorLabel: null,
            promotionId: null,
            promoLabel:  null,
            classroomId: null,
            classLabel:  null,
        };

        this._init();
    }

    /* ── Utilitaires ── */
    trunc2(v) { return Math.floor(v * 100) / 100; }
    sid(id)   { return String(id); }
    esc(s)    { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; }
    $(id)     { return document.getElementById(id); }
    $$(sel)   { return document.querySelectorAll(sel); }

    toast(type, msg, dur = 4200) {
        const icons = {
            success: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>',
            error:   '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
            warning: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
            info:    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
        };
        const t = document.createElement('div');
        t.className = `nm-toast nm-toast--${type}`;
        t.innerHTML = `<span class="toast-icon">${icons[type]||icons.info}</span>
            <span class="toast-msg">${msg}</span>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>`;
        this.$('toast-area').appendChild(t);
        requestAnimationFrame(() => t.classList.add('show'));
        if (dur) setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 350); }, dur);
    }

    async get(path) {
        const url = path.startsWith('http') ? path : this.baseUrl + path;
        const r = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        if (!r.ok) throw new Error((await r.json().catch(() => ({}))).message || `Erreur ${r.status}`);
        return r.json();
    }

    async post(path, body) {
        const url = path.startsWith('http') ? path : this.baseUrl + path;
        const r = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(body)
        });
        const j = await r.json().catch(() => ({}));
        if (!r.ok && j.message) throw new Error(j.message);
        return j;
    }

    // ═══════════════════════════════════════════════════════
    // Init
    // ═══════════════════════════════════════════════════════
    _init() {
        // Semestre toggle
        this.$$('.sem-btn').forEach(b => b.addEventListener('click', () => {
            this.$$('.sem-btn').forEach(x => x.classList.remove('sem-btn--on'));
            b.classList.add('sem-btn--on');
            this.$('semester').value = b.dataset.value;
            // Si une classe est déjà choisie, recharger les matières
            if (this.state.classroomId) {
                this._loadSubjects();
            }
        }));

        // Matière
        this.$('subject_id').addEventListener('change', () => {
            this._syncCoeff();
            if (this.$('subject_id').value) {
                this.loadNotes();
            } else {
                this._clearTable();
            }
        });

        // Bouton reset / "Changer de matière"
        this.$('btn-reset').addEventListener('click', () => this._resetToSubjectSelection());

        // Sauvegarde
        this.$('btn-save').addEventListener('click', () => this.saveNotes());

        // Raccourcis clavier
        document.addEventListener('keydown', e => {
            if (e.ctrlKey && e.key === 's') { e.preventDefault(); this.saveNotes(); return; }
            if ((e.key === 'Tab' || e.key === 'Enter') && !e.shiftKey) {
                const a = document.activeElement;
                if (a?.classList.contains('nm-inp')) {
                    e.preventDefault();
                    const all = [...this.$$('.nm-inp:not(:disabled):not([readonly])')];
                    const i   = all.indexOf(a);
                    i < all.length - 1 ? all[i + 1].focus() : this.$('btn-save').focus();
                }
            }
        });

        // Démarrage : charger l'année active
        this._loadActiveYear();
    }

    // ═══════════════════════════════════════════════════════
    // ÉTAPE 0 — Année active
    // ═══════════════════════════════════════════════════════
    async _loadActiveYear() {
        try {
            const d = await this.get('/api/active-year');
            if (d.success && d.year) {
                this.state.yearId    = d.year.id;
                this.state.yearLabel = d.year.year;
                this.$('year_id').value = d.year.id;
                this._updateBreadcrumb('year', d.year.year);
                this._showEmptyWithSteps();
                await this._loadSectors();
            } else {
                this._showEmptyError('Aucune année scolaire active trouvée dans le système.');
            }
        } catch (e) {
            this._showEmptyError('Impossible de charger l\'année active : ' + e.message);
        }
    }

    // ═══════════════════════════════════════════════════════
    // ÉTAPE 1 — Filières
    // ═══════════════════════════════════════════════════════
    async _loadSectors() {
        this._setStepLoading('sector', true);
        this.$('step-sector').classList.remove('nm-step--locked');
        try {
            const d = await this.get(`/api/sectors-for-create/${this.state.yearId}`);
            this._setStepLoading('sector', false);
            if (!d.success || !d.sectors.length) {
                this._renderOptions('sector', [], 'Aucune filière disponible pour cette année.');
                return;
            }
            this._renderOptions('sector', d.sectors, null, (item) => this._onSectorClick(item));
            // Auto-sélection si une seule filière
            if (d.auto) {
                await this._onSectorClick(d.sectors[0], true);
            }
        } catch(e) {
            this._setStepLoading('sector', false);
            this.toast('error', 'Chargement filières : ' + e.message);
        }
    }

    async _onSectorClick(sector, auto = false) {
        // Mettre en évidence l'option sélectionnée
        this._selectOption('sector', sector.id);
        this.state.sectorId    = sector.id;
        this.state.sectorLabel = sector.name;
        this._updateBreadcrumb('sector', sector.name, auto);

        // Réinitialiser en aval
        this._resetFrom('promotion');
        await this._loadPromotions();
    }

    // ═══════════════════════════════════════════════════════
    // ÉTAPE 2 — Promotions
    // ═══════════════════════════════════════════════════════
    async _loadPromotions() {
        this._setStepLoading('promotion', true);
        this.$('step-promotion').classList.remove('nm-step--locked');
        try {
            const d = await this.get(`/api/promotions-for-create/${this.state.yearId}/${this.state.sectorId}`);
            this._setStepLoading('promotion', false);
            if (!d.success || !d.promotions.length) {
                this._renderOptions('promotion', [], 'Aucune promotion disponible.');
                return;
            }
            this._renderOptions('promotion', d.promotions, null, (item) => this._onPromotionClick(item));
            if (d.auto) {
                await this._onPromotionClick(d.promotions[0], true);
            }
        } catch(e) {
            this._setStepLoading('promotion', false);
            this.toast('error', 'Chargement promotions : ' + e.message);
        }
    }

    async _onPromotionClick(promotion, auto = false) {
        this._selectOption('promotion', promotion.id);
        this.state.promotionId = promotion.id;
        this.state.promoLabel  = promotion.name;
        this._updateBreadcrumb('promotion', promotion.name, auto);

        this._resetFrom('classroom');
        await this._loadClassrooms();
    }

    // ═══════════════════════════════════════════════════════
    // ÉTAPE 3 — Classes
    // ═══════════════════════════════════════════════════════
    async _loadClassrooms() {
        this._setStepLoading('classroom', true);
        this.$('step-classroom').classList.remove('nm-step--locked');
        try {
            const d = await this.get(`/api/classrooms-for-create/${this.state.promotionId}?year_id=${this.state.yearId}`);
            this._setStepLoading('classroom', false);
            if (!d.success || !d.classrooms.length) {
                this._renderOptions('classroom', [], 'Aucune classe disponible.');
                return;
            }
            this._renderOptions('classroom', d.classrooms, null, (item) => this._onClassroomClick(item));
            if (d.auto) {
                await this._onClassroomClick(d.classrooms[0], true);
            }
        } catch(e) {
            this._setStepLoading('classroom', false);
            this.toast('error', 'Chargement classes : ' + e.message);
        }
    }

    async _onClassroomClick(classroom, auto = false) {
        this._selectOption('classroom', classroom.id);
        this.state.classroomId = classroom.id;
        this.state.classLabel  = classroom.name;
        this.$('classroom_id').value = classroom.id;
        this._updateBreadcrumb('classroom', classroom.name, auto);

        // Mettre à jour le badge contexte
        const badge = this.$('ctx-badge');
        badge.textContent = `${this.state.sectorLabel} — ${this.state.promoLabel} · ${classroom.name}`;
        badge.style.display = 'inline-flex';

        // Charger les matières et si une seule → charger les notes directement
        await this._loadSubjects();
    }

    // ═══════════════════════════════════════════════════════
    // MATIÈRES
    // ═══════════════════════════════════════════════════════
    async _loadSubjects() {
        const cl  = this.state.classroomId;
        const y   = this.state.yearId;
        const sem = this.$('semester').value;
        if (!cl || !y) return;

        const sel = this.$('subject_id');
        sel.innerHTML = '<option>Chargement…</option>'; sel.disabled = true;
        try {
            const d = await this.post('/api/subjects-by-classroom', { classroom_id: cl, year_id: y, semester: sem ? parseInt(sem) : null });
            sel.innerHTML = '<option value="">— Choisir une matière —</option>';
            if (d.success && d.subjects?.length) {
                d.subjects.forEach(s => {
                    const o = document.createElement('option');
                    o.value = s.ratio_id; o.dataset.coefficient = s.coefficient; o.dataset.subjectId = s.subject_id;
                    const sl = s.semester === 1 ? ' [S1]' : s.semester === 2 ? ' [S2]' : '';
                    o.textContent = `${s.subject_name}${sl}  —  Coeff. ${s.coefficient}`;
                    sel.appendChild(o);
                });
                sel.disabled = false;

                // Auto-sélection si une seule matière
                if (d.subjects.length === 1) {
                    sel.selectedIndex = 1;
                    this._syncCoeff();
                    await this.loadNotes();
                } else {
                    // Montrer l'état vide avec instructions matière
                    this._showEmptySelectSubject();
                }
            } else {
                this.toast('warning', d.message || 'Aucune matière disponible pour ce semestre.');
                this._showEmptySelectSubject();
            }
        } catch (e) {
            sel.innerHTML = '<option value="">— Erreur de chargement —</option>';
            this.toast('error', 'Impossible de charger les matières : ' + e.message);
        }
    }

    _syncCoeff() {
        const o = this.$('subject_id').options[this.$('subject_id').selectedIndex];
        if (o?.value) {
            this.$('coefficient').textContent  = o.dataset.coefficient || '1';
            this.$('ratio_id').value           = o.value;
            this.$('subject_real_id').value    = o.dataset.subjectId || '';
            this.$('coeff-pill').style.display = 'flex';
        } else {
            this.$('ratio_id').value           = '';
            this.$('coeff-pill').style.display = 'none';
        }
    }

    // ═══════════════════════════════════════════════════════
    // Gestion des états de l'empty state
    // ═══════════════════════════════════════════════════════
    _showEmptyWithSteps() {
        const t = this.$('empty-title');
        const d = this.$('empty-desc');
        const s = this.$('empty-steps-visual');
        if (t) t.textContent = 'Sélectionnez votre contexte';
        if (d) d.textContent = 'Choisissez la filière, puis la promotion et la classe pour accéder à la saisie.';
        if (s) s.style.display = 'flex';
        this.$('empty-state').style.display = 'flex';
        this.$('notes-table-container').style.display = 'none';
    }

    _showEmptySelectSubject() {
        const t = this.$('empty-title');
        const d = this.$('empty-desc');
        if (t) t.textContent = 'Classe sélectionnée — Choisissez une matière';
        if (d) d.innerHTML = 'Utilisez le menu <strong>Matière</strong> ci-dessus pour charger les notes.';
        this.$('empty-state').style.display = 'flex';
        this.$('notes-table-container').style.display = 'none';
    }

    _showEmptyError(msg) {
        const t = this.$('empty-title');
        const d = this.$('empty-desc');
        if (t) t.textContent = 'Une erreur est survenue';
        if (d) d.textContent = msg;
        this.$('empty-state').style.display = 'flex';
        this.$('notes-table-container').style.display = 'none';
    }

    // ═══════════════════════════════════════════════════════
    // Helpers UI — Options en cartes cliquables
    // ═══════════════════════════════════════════════════════
    _renderOptions(stepName, items, emptyMsg, onClick) {
        const container = this.$(`options-${stepName}`);
        if (!container) return;
        if (!items.length) {
            container.innerHTML = `<div class="step-placeholder step-placeholder--warn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                ${emptyMsg || 'Aucun élément disponible'}
            </div>`;
            return;
        }
        container.innerHTML = items.map(item => `
            <button type="button" class="opt-card" data-id="${item.id}" data-step="${stepName}">
                <span class="opt-label">${this.esc(item.name)}</span>
                <span class="opt-arrow">›</span>
            </button>
        `).join('');
        container.querySelectorAll('.opt-card').forEach(btn => {
            btn.addEventListener('click', () => {
                const item = items.find(i => String(i.id) === btn.dataset.id);
                if (item) onClick(item);
            });
        });
    }

    _selectOption(stepName, id) {
        const container = this.$(`options-${stepName}`);
        if (!container) return;
        container.querySelectorAll('.opt-card').forEach(btn => {
            btn.classList.toggle('opt-card--active', String(btn.dataset.id) === String(id));
        });
    }

    _setStepLoading(stepName, loading) {
        const loader = this.$(`loader-${stepName}`);
        if (loader) loader.style.display = loading ? 'flex' : 'none';
    }

    _updateBreadcrumb(part, label, auto = false) {
        const el = this.$(`bc-${part}-label`);
        if (el) {
            el.textContent = label + (auto ? '' : '');
        }
        const item = this.$(`bc-${part}`);
        if (item) item.classList.add('bc-item--active');
    }

    // ═══════════════════════════════════════════════════════
    // Reset partiel — revenir à une étape donnée
    // ═══════════════════════════════════════════════════════
    _resetFrom(step) {
        const steps = ['sector', 'promotion', 'classroom'];
        const idx   = steps.indexOf(step);
        if (idx === -1) return;

        for (let i = idx; i < steps.length; i++) {
            const s = steps[i];
            const stepEl = this.$(`step-${s}`);
            const optEl  = this.$(`options-${s}`);
            if (stepEl) stepEl.classList.add('nm-step--locked');
            if (optEl) optEl.innerHTML = `<div class="step-placeholder">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                Choisissez d'abord ${s === 'promotion' ? 'une filière' : 'une promotion'}
            </div>`;

            // Reset état
            if (s === 'promotion') { this.state.promotionId = null; this.state.promoLabel = null; }
            if (s === 'classroom') { this.state.classroomId = null; this.state.classLabel = null; }
        }

        // Reset filière si demandé
        if (idx === 0) { this.state.sectorId = null; this.state.sectorLabel = null; }

        // Reset matières et tableau
        const sel = this.$('subject_id');
        sel.innerHTML = '<option value="">— Sélectionner la classe d\'abord —</option>'; sel.disabled = true;
        this.$('ratio_id').value = ''; this.$('coeff-pill').style.display = 'none';
        this.$('classroom_id').value = '';
        this.$('ctx-badge').style.display = 'none';
        this._clearTable();
    }

    _resetToSubjectSelection() {
        // Revenir à la sélection de matière sans toucher à la cascade filière/promo/classe
        const sel = this.$('subject_id');
        sel.selectedIndex = 0;
        this._syncCoeff();
        this._clearTable();
        if (this.state.classroomId) {
            this._showEmptySelectSubject();
        }
    }

    // ═══════════════════════════════════════════════════════
    // Chargement des notes (plus de bouton — appelé auto)
    // ═══════════════════════════════════════════════════════
    async loadNotes() {
        const cl = this.state.classroomId;
        const y  = this.state.yearId;
        const r  = this.$('ratio_id').value;
        const s  = this.$('semester').value;

        if (!cl || !y || !r || !s) {
            this.toast('warning', 'Contexte incomplet — vérifiez la sélection.');
            return;
        }

        // Feedback visuel sur le select de matières
        const subjectSel = this.$('subject_id');
        const origLabel  = subjectSel.options[subjectSel.selectedIndex]?.text || '';
        subjectSel.disabled = true;

        try {
            const d = await this.post('/api/students-with-notes', { year_id: y, classroom_id: cl, ratio_id: r, semester: s });
            if (d.success) {
                this.data = d.students; this.canEdit = d.can_edit ?? true; this.canModify = d.can_modify ?? true;
                this.saved.clear(); this.modified.clear();
                d.is_locked ? this.$('locked-banner').classList.remove('d-none') : this.$('locked-banner').classList.add('d-none');
                const partial = d.students.some(s => Object.values(s.fields_readonly || {}).some(v => v));
                (partial && !d.is_locked) ? this.$('partial-banner').classList.remove('d-none') : this.$('partial-banner').classList.add('d-none');

                const lbl = this.$('subject_id').options[this.$('subject_id').selectedIndex]?.text || '—';
                this.$('toolbar-subject').textContent = lbl;
                this._render();
                this.toast('success', `${d.students.length} élève(s) — notes chargées.`);
            } else {
                this.toast('warning', d.message || 'Aucun étudiant trouvé.');
                this._showEmptySelectSubject();
            }
        } catch (e) {
            this.toast('error', e.message);
            this._showEmptySelectSubject();
        } finally {
            subjectSel.disabled = false;
        }
    }

    // ═══════════════════════════════════════════════════════
    // Rendu du tableau (identique à l'original)
    // ═══════════════════════════════════════════════════════
    _render() {
        if (!this.data?.length) { this._clearTable(); return; }
        this.$('empty-state').style.display           = 'none';
        this.$('notes-table-container').style.display = 'block';
        this.$('shortcuts-bar').style.display         = 'flex';
        this.$('student-count').textContent           = this.data.length;
        this.$('progress-wrap').style.display         = 'flex';

        const rows = this.data.map((s, i) => {
            const interros = s.interros || [];
            const disabled = s.is_disabled || !this.canEdit;
            const fr       = s.fields_readonly || {};
            let iCells = '';
            for (let k = 0; k < this.INTERRO_COUNT; k++) {
                const locked = disabled ? false : (fr[`interro_${k}`] || false);
                const attr   = disabled ? 'disabled' : locked ? 'readonly' : '';
                const val    = (interros[k] !== undefined && interros[k] !== null) ? this.trunc2(parseFloat(interros[k])).toFixed(2) : '';
                iCells += `<td class="td-note${locked ? ' td-locked' : ''}">
                    <div class="inp-wrap${locked ? ' inp-wrap--lock' : ''}">
                        <input type="number" class="nm-inp" data-idx="${i}" data-ii="${k}" data-id="${s.recording_id}"
                               value="${val}" min="0" max="20" step="0.01" placeholder="—" ${attr}
                               ${locked ? 'title="Note enregistrée"' : ''}>
                        ${locked ? '<span class="inp-lock-ico">🔒</span>' : ''}
                    </div></td>`;
            }
            const moyI = s.moy_interros != null ? this.trunc2(parseFloat(s.moy_interros)).toFixed(2) : '—';
            let dCells = '';
            ['devoir1', 'devoir2'].forEach(f => {
                const locked = disabled ? false : (fr[f] || false);
                const attr   = disabled ? 'disabled' : locked ? 'readonly' : '';
                const val    = s[f] != null ? this.trunc2(parseFloat(s[f])).toFixed(2) : '';
                dCells += `<td class="td-note${locked ? ' td-locked' : ''}">
                    <div class="inp-wrap${locked ? ' inp-wrap--lock' : ''}">
                        <input type="number" class="nm-inp" data-field="${f}" data-idx="${i}" data-id="${s.recording_id}"
                               value="${val}" min="0" max="20" step="0.01" placeholder="—" ${attr}
                               ${locked ? 'title="Note enregistrée"' : ''}>
                        ${locked ? '<span class="inp-lock-ico">🔒</span>' : ''}
                    </div></td>`;
            });
            const moy20 = s.moy_20 != null ? this.trunc2(parseFloat(s.moy_20)) : null;
            const m20s  = moy20 != null ? moy20.toFixed(2) : '—';
            const m20c  = moy20 == null ? '' : moy20 >= 10 ? 'moy-pass' : 'moy-fail';
            const partial = Object.values(fr).some(v => v);
            return `<tr class="nm-row${s.field_readonly ? ' nm-row--lock' : ''}" data-idx="${i}" data-id="${s.recording_id}">
                <td class="td-num">${i + 1}</td>
                <td class="td-name">${this.esc(s.name)}${partial ? '<span class="partial-badge" title="Notes partiellement enregistrées">~</span>' : ''}</td>
                <td class="td-surname">${this.esc(s.surname)}</td>
                ${iCells}
                <td class="td-avg"><span class="nm-moy" data-type="mi" data-idx="${i}">${moyI}</span></td>
                ${dCells}
                <td class="td-final"><span class="nm-moy-final ${m20c}" data-type="m20" data-idx="${i}">${m20s}</span></td>
                <td class="td-status"><span class="nm-status ${s.field_readonly ? 'st-locked' : 'st-pending'}" data-idx="${i}" title="${s.field_readonly ? 'Verrouillé' : 'En attente'}"></span></td>
            </tr>`;
        });

        this.$('notes-body').innerHTML = rows.join('');
        const hasEditable = this.canEdit && this.data.some(s => !s.is_disabled && !s.field_readonly);
        this.$('btn-save').disabled = !hasEditable;
        this._bindInputs(); this._bindHeaders(); this._updateStats(); this._updateProgress();
    }

    _bindInputs() {
        this.$('notes-body').querySelectorAll('.nm-inp').forEach(inp => {
            inp.addEventListener('input', () => this._recalc(inp));
            inp.addEventListener('blur',  () => this._fmt(inp));
            inp.addEventListener('focus', () => inp.select());
        });
    }

    _bindHeaders() {
        this.$$('.nm-hdr-inp').forEach(inp => {
            inp.addEventListener('blur',    () => this._applyHeader(inp));
            inp.addEventListener('dblclick',() => this._fillAvg(inp));
            inp.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); inp.blur(); } });
        });
    }

    _applyHeader(h) {
        const v = parseFloat(h.value.trim());
        if (isNaN(v) || v < 0 || v > 20) return;
        const th = h.closest('th');
        if (th.classList.contains('interro-header')) {
            this.$('notes-body').querySelectorAll(`.nm-inp[data-ii="${th.dataset.index}"]:not(:disabled):not([readonly])`)
                .forEach(inp => { inp.value = this.trunc2(v).toFixed(2); this._recalc(inp); });
        } else if (th.classList.contains('devoir-header')) {
            this.$('notes-body').querySelectorAll(`.nm-inp[data-field="${th.dataset.field}"]:not(:disabled):not([readonly])`)
                .forEach(inp => { inp.value = this.trunc2(v).toFixed(2); this._recalc(inp); });
        }
        this.toast('info', `Colonne remplie avec ${v.toFixed(2)}`);
    }

    _fillAvg(h) {
        const th = h.closest('th');
        let sel = '';
        if (th.classList.contains('interro-header')) sel = `.nm-inp[data-ii="${th.dataset.index}"]:not(:disabled):not([readonly])`;
        else if (th.classList.contains('devoir-header')) sel = `.nm-inp[data-field="${th.dataset.field}"]:not(:disabled):not([readonly])`;
        if (!sel) return;
        const vals = [...this.$('notes-body').querySelectorAll(sel)].map(i => parseFloat(i.value)).filter(v => !isNaN(v));
        if (!vals.length) return;
        const avg = vals.reduce((a, b) => a + b, 0) / vals.length;
        this.$('notes-body').querySelectorAll(sel).forEach(inp => { inp.value = avg.toFixed(2); this._recalc(inp); });
        this.toast('success', `Moyenne colonne appliquée : ${avg.toFixed(2)}`);
    }

    _recalc(inp) {
        if (inp.hasAttribute('readonly')) return;
        const idx = parseInt(inp.dataset.idx);
        const row = this.$('notes-body').querySelector(`tr[data-idx="${idx}"]`);
        if (!row) return;
        const sid = this.sid(inp.dataset.id);
        this.modified.add(sid); this.saved.delete(sid);
        row.classList.add('nm-row--mod'); row.classList.remove('nm-row--saved');
        const st = row.querySelector('.nm-status');
        if (st && !st.classList.contains('st-locked')) st.className = 'nm-status st-mod';
        const interros = [];
        for (let k = 0; k < this.INTERRO_COUNT; k++) {
            const inp2 = row.querySelector(`.nm-inp[data-ii="${k}"]`);
            if (inp2 && inp2.value !== '' && !inp2.disabled) { const v = parseFloat(inp2.value); if (!isNaN(v)) interros.push(v); }
        }
        const gD = f => { const i = row.querySelector(`.nm-inp[data-field="${f}"]`); if (!i || i.value === '' || i.disabled) return null; const v = parseFloat(i.value); return isNaN(v) ? null : v; };
        const d1 = gD('devoir1'), d2 = gD('devoir2');
        const moyI  = interros.length ? this.trunc2(interros.reduce((a, b) => a + b, 0) / interros.length) : null;
        const comp  = [moyI, d1, d2].filter(v => v !== null);
        const moy20 = comp.length ? this.trunc2(comp.reduce((a, b) => a + b, 0) / comp.length) : null;
        const miEl = row.querySelector('[data-type="mi"]');
        if (miEl) miEl.textContent = moyI !== null ? moyI.toFixed(2) : '—';
        const m20El = row.querySelector('[data-type="m20"]');
        if (m20El) { m20El.textContent = moy20 !== null ? moy20.toFixed(2) : '—'; m20El.className = 'nm-moy-final' + (moy20 === null ? '' : moy20 >= 10 ? ' moy-pass' : ' moy-fail'); }
        if (this.data[idx]) Object.assign(this.data[idx], { interros, devoir1: d1, devoir2: d2, moy_interros: moyI, moy_20: moy20 });
        if (this.modified.size > 0 && this.canEdit) this.$('btn-save').disabled = false;
        this._updatePendingChip(); this._updateStats(); this._updateProgress();
    }

    _fmt(inp) {
        if (inp.value !== '' && !inp.disabled && !inp.readOnly) {
            const v = parseFloat(inp.value);
            if (!isNaN(v)) inp.value = this.trunc2(Math.min(Math.max(v, 0), 20)).toFixed(2);
        }
    }

    async saveNotes() {
        if (!this.canEdit) { this.toast('warning', 'Notes verrouillées.'); return; }
        if (!this.data?.length || !this.modified.size) { this.toast('info', 'Aucune modification à sauvegarder.'); return; }
        const modData = this.data.filter(s => this.modified.has(this.sid(s.recording_id)));
        if (!modData.length) return;
        const notes = modData.map(s => {
            const row = this.$('notes-body').querySelector(`tr[data-id="${s.recording_id}"]`);
            const interros = [];
            for (let k = 0; k < this.INTERRO_COUNT; k++) {
                const inp = row?.querySelector(`.nm-inp[data-ii="${k}"]`);
                interros.push((inp && inp.value !== '' && !inp.disabled) ? parseFloat(inp.value) : null);
            }
            const d1i = row?.querySelector('.nm-inp[data-field="devoir1"]');
            const d2i = row?.querySelector('.nm-inp[data-field="devoir2"]');
            return { recording_id: s.recording_id, interros,
                devoir1: (d1i && d1i.value !== '' && !d1i.disabled) ? parseFloat(d1i.value) : null,
                devoir2: (d2i && d2i.value !== '' && !d2i.disabled) ? parseFloat(d2i.value) : null };
        });
        const payload = {
            year_id:      this.state.yearId,
            classroom_id: this.state.classroomId,
            ratio_id:     this.$('ratio_id').value,
            semester:     this.$('semester').value,
            notes
        };
        this.$('btn-save').disabled = true;
        const saveTextEl = this.$('btn-save').querySelector('.btn-save-text');
        if (saveTextEl) saveTextEl.textContent = 'Enregistrement…';
        try {
            const res = await this.post('/api/notes/store-bulk', payload);
            if (res.success) {
                modData.forEach(s => {
                    const id  = this.sid(s.recording_id);
                    const row = this.$('notes-body').querySelector(`tr[data-id="${s.recording_id}"]`);
                    this.saved.add(id); this.modified.delete(id);
                    if (row) { row.classList.remove('nm-row--mod'); row.classList.add('nm-row--saved');
                        const st = row.querySelector('.nm-status'); if (st && !st.classList.contains('st-locked')) st.className = 'nm-status st-saved'; }
                });
                this.$('saved-count').textContent = this.saved.size;
                this._updatePendingChip();
                this.toast('success', res.message || 'Notes enregistrées avec succès.');
                await this.loadNotes();
            } else { this.toast('error', res.message || 'Erreur lors de la sauvegarde.'); this.$('btn-save').disabled = false; }
        } catch (e) { this.toast('error', e.message); this.$('btn-save').disabled = false; }
        finally { if (saveTextEl) saveTextEl.textContent = 'Enregistrer'; }
    }

    _clearTable() {
        this.data = null; this.canEdit = true; this.canModify = true;
        this.saved.clear(); this.modified.clear();
        this.$('notes-body').innerHTML = '';
        this.$('notes-table-container').style.display = 'none';
        this.$('shortcuts-bar').style.display         = 'none';
        this.$('progress-wrap').style.display         = 'none';
        this.$('locked-banner').classList.add('d-none');
        this.$('partial-banner').classList.add('d-none');
        this.$('btn-save').disabled                   = true;
        this.$('student-count').textContent           = '0';
        this.$('saved-count').textContent             = '0';
        this.$('pending-chip').style.display          = 'none';
        this._updateStats();
    }

    _updatePendingChip() {
        const n = this.modified.size, chip = this.$('pending-chip');
        if (n > 0) { chip.style.display = 'flex'; this.$('saved-pending-text').textContent = n; }
        else        { chip.style.display = 'none'; }
        const badge = this.$('save-pending-badge');
        if (badge) { badge.textContent = n || ''; badge.style.display = n > 0 ? 'inline-flex' : 'none'; }
    }

    _updateProgress() {
        if (!this.data?.length) return;
        const total = this.data.filter(s => !s.is_disabled).length;
        const done  = this.data.filter(s => !s.is_disabled && s.moy_20 != null).length;
        const pct   = total > 0 ? Math.round((done / total) * 100) : 0;
        this.$('progress-fill').style.width  = pct + '%';
        this.$('progress-label').textContent = `${pct}% — ${done} / ${total} élèves`;
    }

    _updateStats() {
        const ids = ['stat-avg','stat-pass','stat-fail','stat-max','stat-min','stat-taux'];
        if (!this.data?.length) { ids.forEach(id => { const el = this.$(id); if (el) el.textContent = '—'; }); return; }
        const vals = this.data.filter(s => !s.is_disabled && s.moy_20 != null).map(s => this.trunc2(parseFloat(s.moy_20)));
        if (!vals.length) return;
        const avg  = this.trunc2(vals.reduce((a, b) => a + b, 0) / vals.length);
        const pass = vals.filter(v => v >= 10).length;
        const fail = vals.length - pass;
        const max  = Math.max(...vals), min = Math.min(...vals);
        const taux = Math.round((pass / vals.length) * 100);
        this.$('stat-avg').textContent  = avg.toFixed(2);
        this.$('stat-pass').textContent = pass;
        this.$('stat-fail').textContent = fail;
        this.$('stat-max').textContent  = max.toFixed(2);
        this.$('stat-min').textContent  = min.toFixed(2);
        this.$('stat-taux').textContent = taux + '%';
    }
}

new NotesManager();
</script>

<style>
/* ── Variables (identiques à l'original) ── */
:root {
    --c-bg:           #f4f6fb;
    --c-surface:      #ffffff;
    --c-surface-2:    #f8f9fc;
    --c-border:       #e3e8f0;
    --c-border-2:     #edf0f7;
    --c-ink:          #111827;
    --c-ink-2:        #374151;
    --c-ink-3:        #6b7280;
    --c-ink-4:        #9ca3af;
    --c-ink-5:        #d1d5db;
    --c-accent:       #4f46e5;
    --c-accent-2:     #6366f1;
    --c-accent-light: #eef2ff;
    --c-accent-mid:   #c7d2fe;
    --c-green:        #059669;
    --c-green-bg:     #ecfdf5;
    --c-green-border: #a7f3d0;
    --c-red:          #dc2626;
    --c-red-bg:       #fef2f2;
    --c-red-border:   #fecaca;
    --c-amber:        #d97706;
    --c-amber-bg:     #fffbeb;
    --c-amber-border: #fde68a;
    --c-blue:         #2563eb;
    --c-blue-bg:      #eff6ff;
    --c-blue-border:  #bfdbfe;
    --sh-xs:    0 1px 2px rgba(0,0,0,.05);
    --sh-sm:    0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
    --sh-md:    0 4px 12px rgba(0,0,0,.06), 0 2px 4px rgba(0,0,0,.04);
    --sh-lg:    0 10px 30px rgba(0,0,0,.08), 0 4px 8px rgba(0,0,0,.04);
    --sh-xl:    0 20px 60px rgba(0,0,0,.1),  0 8px 16px rgba(0,0,0,.06);
    --r-sm:  6px; --r-md:  10px; --r-lg:  16px; --r-xl:  20px;
    --font:       -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
    --font-mono:  'Consolas', 'Courier New', monospace;
    --ease: cubic-bezier(.4, 0, .2, 1);
    --ease-spring: cubic-bezier(.34, 1.56, .64, 1);
}
.nm-page *, .nm-page *::before, .nm-page *::after { box-sizing: border-box; margin: 0; padding: 0; }
.nm-page { font-family: var(--font); font-size: 14px; color: var(--c-ink); background: var(--c-bg); min-height: 100vh; padding: 0 0 80px; line-height: 1.5; }
.d-none { display: none !important; }

/* ══ Hero header (identique) ══ */
.site-header { position: relative; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4f46e5 75%, #6366f1 100%); padding: 0; overflow: hidden; margin-bottom: 32px; }
.header-bg-shape { position: absolute; border-radius: 50%; background: rgba(255,255,255,.04); pointer-events: none; }
.header-bg-shape:nth-child(1) { width: 400px; height: 400px; top: -150px; right: -80px; }
.header-bg-shape--2 { width: 200px; height: 200px; bottom: -60px; left: 120px; background: rgba(255,255,255,.06); }
.header-content { position: relative; z-index: 1; max-width: 1200px; margin: 0 auto; padding: 40px 40px 36px; display: flex; align-items: flex-end; justify-content: space-between; gap: 32px; flex-wrap: wrap; }
.school-badge { display: inline-flex; align-items: center; gap: 7px; font-size: .72rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: rgba(255,255,255,.6); background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15); padding: .3rem .8rem; border-radius: 99px; margin-bottom: 14px; backdrop-filter: blur(8px); }
.page-title { font-size: 3rem; font-weight: 700; line-height: 1.1; color: #ffffff; letter-spacing: -.03em; margin-bottom: 8px; }
.page-title em { font-style: italic; font-weight: 300; color: rgba(255,255,255,.75); }
.page-subtitle { font-size: .8rem; color: rgba(255,255,255,.5); letter-spacing: .04em; }
.header-chips { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; padding-bottom: 4px; }
.stat-chip { display: flex; align-items: center; gap: 10px; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15); backdrop-filter: blur(10px); border-radius: var(--r-lg); padding: .6rem 1rem; min-width: 100px; transition: background .2s var(--ease), transform .2s var(--ease); }
.stat-chip:hover { background: rgba(255,255,255,.15); transform: translateY(-2px); }
.stat-chip--amber { border-color: rgba(251,191,36,.4); background: rgba(251,191,36,.12); }
.stat-chip-icon { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.stat-chip-icon--blue  { background: rgba(99,102,241,.3); color: #c7d2fe; }
.stat-chip-icon--green { background: rgba(16,185,129,.25); color: #6ee7b7; }
.stat-chip-icon--amber { background: rgba(251,191,36,.25); color: #fde68a; }
.stat-chip-body { display: flex; flex-direction: column; }
.stat-chip-val { font-size: 1.3rem; font-weight: 700; color: #fff; line-height: 1; }
.stat-chip-lbl { font-size: .65rem; font-weight: 500; color: rgba(255,255,255,.55); text-transform: uppercase; letter-spacing: .06em; margin-top: 2px; }

/* ══ Wrapper central ══ */
.nm-page > *:not(.site-header):not(.toast-zone) { max-width: 1200px; margin-left: auto; margin-right: auto; padding-left: 40px; padding-right: 40px; }

/* ══ Bannières ══ */
.banner { display: flex; align-items: flex-start; gap: 12px; padding: 14px 18px; border-radius: var(--r-md); font-size: .83rem; margin-bottom: 12px; border: 1px solid; animation: slideDown .3s var(--ease); }
@keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
.banner--locked { background: var(--c-amber-bg); border-color: var(--c-amber-border); color: #92400e; }
.banner--info   { background: var(--c-blue-bg); border-color: var(--c-blue-border); color: #1e40af; }
.banner-icon { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.banner--locked .banner-icon { background: var(--c-amber-border); }
.banner--info   .banner-icon { background: var(--c-blue-border); }
.banner-body { display: flex; flex-direction: column; gap: 2px; }
.banner-body strong { font-weight: 600; }
.banner-body span   { opacity: .85; }

/* ══ Panneau filtres ══ */
.nm-panel { background: var(--c-surface); border: 1px solid var(--c-border); border-radius: var(--r-xl); overflow: hidden; margin-bottom: 20px; box-shadow: var(--sh-md); }
.nm-panel:hover { box-shadow: var(--sh-lg); }
.panel-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--c-border-2); background: linear-gradient(to right, var(--c-surface-2), var(--c-surface)); }
.panel-head-left { display: flex; align-items: center; gap: 12px; }
.panel-icon { width: 36px; height: 36px; background: var(--c-accent-light); border: 1px solid var(--c-accent-mid); border-radius: var(--r-sm); display: flex; align-items: center; justify-content: center; color: var(--c-accent); flex-shrink: 0; }
.panel-title { font-size: .85rem; font-weight: 700; color: var(--c-ink); }
.panel-subtitle { font-size: .72rem; color: var(--c-ink-3); margin-top: 1px; }
.ctx-badge { display: none; font-size: .72rem; font-weight: 700; padding: .3rem .8rem; border-radius: 99px; background: var(--c-accent-light); border: 1px solid var(--c-accent-mid); color: var(--c-accent); animation: popIn .2s var(--ease-spring); }
@keyframes popIn { from { transform: scale(.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.panel-body { padding: 24px; }

/* ══ Fil d'Ariane ══ */
.nm-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 14px;
    background: var(--c-surface-2);
    border: 1px solid var(--c-border-2);
    border-radius: var(--r-md);
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.bc-item {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 8px;
    color: var(--c-ink-4);
    font-size: .75rem;
    font-weight: 600;
    transition: all .2s;
}
.bc-item--active { color: var(--c-ink-2); background: rgba(79,70,229,.07); }
.bc-item--year { color: var(--c-accent); font-weight: 800; }
.bc-icon { display: flex; align-items: center; }
.bc-sep { color: var(--c-ink-5); font-size: .9rem; }

/* ══ Étapes ══ */
.nm-step {
    border: 1px solid var(--c-border);
    border-radius: var(--r-lg);
    overflow: hidden;
    margin-bottom: 14px;
    transition: opacity .2s, filter .2s;
}
.nm-step--locked {
    opacity: .55;
    pointer-events: none;
    filter: grayscale(.3);
}
.step-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: var(--c-surface-2);
    border-bottom: 1px solid var(--c-border-2);
}
.step-num {
    width: 26px; height: 26px;
    border-radius: 8px;
    background: var(--c-accent-light);
    border: 1px solid var(--c-accent-mid);
    color: var(--c-accent);
    font-weight: 800;
    font-size: .78rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.step-info { flex: 1; }
.step-title { font-weight: 700; font-size: .83rem; }
.step-hint  { font-size: .72rem; color: var(--c-ink-3); margin-top: 1px; }
.step-loader { display: flex; align-items: center; }

.step-options {
    padding: 12px 16px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-height: 54px;
    align-items: center;
}

.step-placeholder {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--c-ink-4);
    font-size: .8rem;
    font-style: italic;
}
.step-placeholder--warn { color: var(--c-amber); }

/* Cartes options (filières / promotions / classes) */
.opt-card {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    background: var(--c-surface);
    border: 1.5px solid var(--c-border);
    border-radius: 10px;
    cursor: pointer;
    font-family: var(--font);
    font-size: .83rem;
    font-weight: 600;
    color: var(--c-ink-2);
    transition: all .18s var(--ease);
    white-space: nowrap;
}
.opt-card:hover {
    border-color: var(--c-accent-mid);
    background: var(--c-accent-light);
    color: var(--c-accent);
    transform: translateY(-1px);
    box-shadow: var(--sh-sm);
}
.opt-card--active {
    border-color: var(--c-accent);
    background: var(--c-accent-light);
    color: var(--c-accent);
    box-shadow: 0 0 0 3px rgba(79,70,229,.1);
}
.opt-arrow { opacity: .4; font-size: .9rem; }
.opt-card--active .opt-arrow { opacity: 1; }

/* Spin small */
.nm-spin-sm {
    width: 14px; height: 14px;
    border: 2px solid var(--c-border);
    border-top-color: var(--c-accent);
    border-radius: 50%;
    animation: nm-spin .7s linear infinite;
}
@keyframes nm-spin { to { transform: rotate(360deg); } }

/* ══ Séparateur ══ */
.nm-sep { display: flex; align-items: center; gap: 12px; margin: 20px 0; }
.nm-sep::before, .nm-sep::after { content: ''; flex: 1; height: 1px; background: var(--c-border-2); }
.nm-sep-label { font-size: .68rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--c-ink-4); white-space: nowrap; }

/* ══ Ligne éval ══ */
.filters-row2 { display: grid; grid-template-columns: auto 1fr; gap: 16px; align-items: end; }
.fg { display: flex; flex-direction: column; gap: 8px; }
.fg--wide { flex: 1; }
.nm-page label { display: flex; align-items: center; gap: 6px; font-size: .7rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--c-ink-3); }
.nm-page label svg { color: var(--c-accent); opacity: .7; }

/* Select matière */
.select-wrap { position: relative; width: 100%; }
.select-wrap select { appearance: none; width: 100%; background: var(--c-surface); border: 1.5px solid var(--c-border); border-radius: var(--r-md); padding: .6rem 2.5rem .6rem 1rem; font-family: var(--font); font-size: .88rem; color: var(--c-ink); outline: none; cursor: pointer; transition: border-color .18s var(--ease), box-shadow .18s var(--ease); }
.select-wrap select:focus { border-color: var(--c-accent); box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
.select-wrap select:disabled { opacity: .4; cursor: not-allowed; }
.select-arrow { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--c-ink-3); }

/* Semestre toggle */
.sem-toggle { display: flex; background: var(--c-surface-2); border: 1.5px solid var(--c-border); border-radius: var(--r-md); padding: 3px; gap: 3px; width: fit-content; }
.sem-btn { display: flex; align-items: center; gap: 6px; border: none; background: transparent; padding: .45rem 1.2rem; font-family: var(--font); font-size: .85rem; font-weight: 600; color: var(--c-ink-3); cursor: pointer; border-radius: 8px; transition: all .2s var(--ease); }
.sem-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; opacity: .3; transition: opacity .2s; }
.sem-btn--on { background: var(--c-surface); color: var(--c-accent); box-shadow: var(--sh-sm); }
.sem-btn--on .sem-dot { opacity: 1; background: var(--c-accent); }

/* Coeff pill */
.subject-row { display: flex; gap: 10px; align-items: center; }
.coeff-pill { display: none; align-items: center; gap: 5px; font-size: .72rem; font-weight: 600; padding: .5rem .8rem; border-radius: var(--r-md); border: 1.5px solid var(--c-accent-mid); background: var(--c-accent-light); color: var(--c-accent); white-space: nowrap; flex-shrink: 0; }
.coeff-pill strong { font-weight: 800; font-size: .85rem; }

/* Progression */
.progress-wrap { display: none; flex-direction: column; gap: 8px; margin-top: 20px; padding: 16px; background: var(--c-surface-2); border: 1px solid var(--c-border-2); border-radius: var(--r-md); }
.progress-header { display: flex; justify-content: space-between; align-items: center; }
.progress-label-text { font-size: .72rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--c-ink-3); }
.progress-track { height: 6px; background: var(--c-border); border-radius: 99px; overflow: hidden; }
.progress-fill { height: 100%; background: linear-gradient(90deg, var(--c-accent), var(--c-accent-2)); border-radius: 99px; transition: width .5s var(--ease); width: 0%; }
.progress-txt { font-family: var(--font-mono); font-size: .75rem; font-weight: 600; color: var(--c-accent); }

/* ══ Barre raccourcis ══ */
.shortcuts-bar { display: none; align-items: center; gap: 6px; padding: 10px 16px; margin-bottom: 16px; background: var(--c-surface); border: 1px solid var(--c-border); border-radius: var(--r-lg); box-shadow: var(--sh-xs); flex-wrap: wrap; }
.sc-group { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; width: 100%; }
.sc-divider { width: 1px; height: 14px; background: var(--c-border); flex-shrink: 0; }
.sc-item { display: flex; align-items: center; gap: 4px; font-size: .71rem; color: var(--c-ink-3); white-space: nowrap; }
.nm-page kbd { display: inline-flex; align-items: center; justify-content: center; padding: .1rem .4rem; background: var(--c-surface-2); border: 1px solid var(--c-border); border-bottom-width: 2px; border-radius: 5px; font-family: var(--font-mono); font-size: .65rem; font-weight: 600; color: var(--c-ink-2); line-height: 1.4; }

/* ══ Empty state ══ */
.empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 20px; text-align: center; gap: 16px; background: var(--c-surface); border: 1px solid var(--c-border); border-radius: var(--r-xl); box-shadow: var(--sh-sm); margin-bottom: 24px; }
.empty-illustration { position: relative; width: 80px; height: 80px; }
.empty-rings { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; }
.empty-ring { position: absolute; border-radius: 50%; border: 1.5px solid var(--c-accent-mid); opacity: .25; animation: pulse 2.5s ease-in-out infinite; }
.empty-ring--1 { width: 80px; height: 80px; animation-delay: 0s; }
.empty-ring--2 { width: 60px; height: 60px; animation-delay: .4s; opacity: .35; }
.empty-ring--3 { width: 40px; height: 40px; animation-delay: .8s; opacity: .5; }
@keyframes pulse { 0%,100% { transform: scale(1); } 50% { transform: scale(1.08); } }
.empty-icon { position: relative; z-index: 1; width: 48px; height: 48px; background: var(--c-accent-light); border: 2px solid var(--c-accent-mid); border-radius: var(--r-md); display: flex; align-items: center; justify-content: center; color: var(--c-accent); }
.empty-state h3 { font-size: 1.4rem; font-weight: 700; color: var(--c-ink); }
.empty-state p { font-size: .87rem; color: var(--c-ink-3); line-height: 1.7; max-width: 380px; }
.empty-steps { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
.empty-step { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.empty-step span { font-size: .68rem; font-weight: 600; color: var(--c-ink-4); text-transform: uppercase; letter-spacing: .06em; }
.es-num { width: 28px; height: 28px; border-radius: 8px; background: var(--c-surface-2); border: 1.5px solid var(--c-border); display: flex; align-items: center; justify-content: center; font-size: .78rem; font-weight: 700; color: var(--c-ink-3); }
.es-num--action { background: linear-gradient(135deg, var(--c-accent), var(--c-accent-2)); border-color: transparent; color: white; }
.empty-step--action span { color: var(--c-accent); }
.es-arrow { font-size: .9rem; color: var(--c-ink-5); }

/* ══ Notes card (identique) ══ */
.notes-card { display: none; background: var(--c-surface); border: 1px solid var(--c-border); border-radius: var(--r-xl); overflow: hidden; box-shadow: var(--sh-lg); animation: slideUp .35s var(--ease); }
@keyframes slideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
.notes-card-header { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--c-border-2); background: var(--c-surface-2); }
.card-title-area { display: flex; align-items: center; gap: 14px; }
.card-title-dot-wrap { display: flex; gap: 4px; align-items: center; }
.card-title-dot { width: 10px; height: 10px; background: var(--c-accent); border-radius: 50%; }
.card-title-dot--2 { width: 7px; height: 7px; background: var(--c-accent-mid); }
.card-title-text { display: flex; flex-direction: column; gap: 2px; }
.card-title-label { font-size: .65rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--c-ink-4); }
.card-title-main { font-size: .95rem; font-weight: 700; color: var(--c-ink); }
.card-actions { display: flex; gap: 10px; align-items: center; }
.btn-ghost { display: inline-flex; align-items: center; gap: 6px; height: 36px; padding: 0 14px; background: transparent; border: 1.5px solid var(--c-border); border-radius: var(--r-md); font-family: var(--font); font-size: .82rem; font-weight: 600; color: var(--c-ink-3); cursor: pointer; transition: all .2s var(--ease); }
.btn-ghost:hover { border-color: var(--c-ink-3); color: var(--c-ink); background: var(--c-surface-2); }
.btn-save { display: inline-flex; align-items: center; gap: 7px; height: 36px; padding: 0 18px; background: linear-gradient(135deg, var(--c-accent), var(--c-accent-2)); border: none; border-radius: var(--r-md); font-family: var(--font); font-size: .82rem; font-weight: 600; color: white; cursor: pointer; box-shadow: 0 4px 10px rgba(79,70,229,.3); transition: all .2s var(--ease); position: relative; }
.btn-save:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(79,70,229,.4); }
.btn-save:disabled { opacity: .35; cursor: not-allowed; box-shadow: none; }
.save-badge { position: absolute; top: -7px; right: -7px; min-width: 18px; height: 18px; background: #ef4444; color: white; border-radius: 99px; font-size: .6rem; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; padding: 0 5px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,.15); }

/* Table */
.table-scroll { overflow-x: auto; }
.nm-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
.nm-table thead tr { border-bottom: 2px solid var(--c-border); }
.nm-table th { padding: 12px 10px; text-align: center; font-weight: 700; font-size: .68rem; letter-spacing: .07em; text-transform: uppercase; color: var(--c-ink-3); background: var(--c-surface-2); white-space: nowrap; }
.th-inner { display: flex; flex-direction: column; align-items: center; gap: 6px; }
.th-inner--left { flex-direction: row; justify-content: flex-start; }
.th-interro { background: #fdf4ff !important; color: #7e22ce !important; }
.th-devoir  { background: #fff7ed !important; color: #c2410c !important; }
.th-final   { background: var(--c-accent-light) !important; color: var(--c-accent) !important; }
.th-label { font-size: .78rem; font-weight: 700; }
.th-label--i { color: #7c3aed; }
.th-label--d { color: #ea580c; }
.nm-hdr-inp { width: 58px; height: 28px; border: 1.5px solid var(--c-border); border-radius: var(--r-sm); text-align: center; font-family: var(--font-mono); font-size: .75rem; font-weight: 600; background: var(--c-surface); color: var(--c-ink); outline: none; transition: border-color .15s, box-shadow .15s; }
.nm-hdr-inp:focus { border-color: var(--c-accent); box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
.th-auto-badge { font-size: .55rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--c-ink-4); background: var(--c-border); border-radius: 4px; padding: 1px 6px; }
.nm-table td { padding: 8px 10px; text-align: center; border-bottom: 1px solid var(--c-border-2); vertical-align: middle; transition: background .15s; }
.td-name    { text-align: left; font-weight: 600; }
.td-surname { text-align: left; font-weight: 400; color: var(--c-ink-3); }
.td-num     { font-family: var(--font-mono); font-size: .72rem; color: var(--c-ink-4); width: 40px; }
.td-note    { padding: 5px 6px; }
.td-avg     { background: rgba(243,244,246,.5); }
.td-final   { background: var(--c-accent-light); }
.nm-row:nth-child(even) td { background: rgba(248,249,252,.6); }
.nm-row:hover td { background: #f0f4ff !important; }
.nm-row--mod td  { background: #fffbeb !important; }
.nm-row--saved td { background: #f0fdf4 !important; }
.inp-wrap { display: flex; align-items: center; justify-content: center; position: relative; }
.inp-wrap--lock { opacity: .65; }
.nm-inp { width: 66px; height: 34px; border: 1.5px solid var(--c-border); border-radius: var(--r-md); text-align: center; font-family: var(--font-mono); font-size: .83rem; font-weight: 600; background: var(--c-surface); color: var(--c-ink); outline: none; transition: border-color .15s var(--ease), box-shadow .15s var(--ease); }
.nm-inp:focus { border-color: var(--c-accent); box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
.nm-inp:disabled, .nm-inp[readonly] { background: var(--c-surface-2); color: var(--c-ink-4); cursor: not-allowed; border-color: var(--c-border); font-weight: 400; }
.inp-lock-ico { position: absolute; right: -2px; top: -5px; font-size: .55rem; }
.td-locked { background: var(--c-surface-2) !important; }
.nm-moy, .nm-moy-final { font-family: var(--font-mono); font-size: .83rem; font-weight: 700; padding: .3rem .65rem; border-radius: var(--r-sm); display: inline-block; background: var(--c-surface-2); color: var(--c-ink-3); border: 1px solid var(--c-border); min-width: 52px; text-align: center; }
.nm-moy-final { font-size: .88rem; min-width: 58px; background: var(--c-accent-light); border-color: var(--c-accent-mid); color: var(--c-accent); }
.moy-pass { background: var(--c-green-bg); border-color: var(--c-green-border); color: var(--c-green); }
.moy-fail { background: var(--c-red-bg); border-color: var(--c-red-border); color: var(--c-red); }
.td-status { width: 36px; }
.nm-status { display: inline-flex; width: 10px; height: 10px; border-radius: 50%; background: var(--c-border); }
.st-mod  { background: var(--c-amber); box-shadow: 0 0 0 3px rgba(217,119,6,.2); animation: pulse-dot 1.5s ease infinite; }
.st-saved { background: var(--c-green); }
.st-locked { background: var(--c-ink-4); }
@keyframes pulse-dot { 0%,100% { box-shadow: 0 0 0 2px rgba(217,119,6,.2); } 50% { box-shadow: 0 0 0 5px rgba(217,119,6,.1); } }
.partial-badge { display: inline-flex; align-items: center; justify-content: center; width: 15px; height: 15px; background: var(--c-amber-bg); border: 1px solid var(--c-amber-border); border-radius: 4px; font-size: .62rem; color: var(--c-amber); margin-left: 6px; font-weight: 800; vertical-align: middle; }

/* Stats bar */
.stats-bar { display: flex; align-items: stretch; border-top: 1px solid var(--c-border); background: linear-gradient(to bottom, var(--c-surface-2), var(--c-surface)); }
.stat-item { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; padding: 18px 12px; border-right: 1px solid var(--c-border-2); }
.stat-item:last-child { border-right: none; }
.stat-item--highlight { background: var(--c-accent-light); }
.stat-item-icon { width: 26px; height: 26px; border-radius: 7px; display: flex; align-items: center; justify-content: center; margin-bottom: 2px; }
.stat-item-icon--neutral { background: var(--c-surface-2); border: 1px solid var(--c-border); color: var(--c-ink-3); }
.stat-item-icon--green   { background: var(--c-green-bg); border: 1px solid var(--c-green-border); color: var(--c-green); }
.stat-item-icon--red     { background: var(--c-red-bg); border: 1px solid var(--c-red-border); color: var(--c-red); }
.stat-item-icon--blue    { background: var(--c-blue-bg); border: 1px solid var(--c-blue-border); color: var(--c-blue); }
.stat-val { font-family: -apple-system, sans-serif; font-size: 1.6rem; font-weight: 300; line-height: 1; color: var(--c-ink); }
.stat-val--g { color: var(--c-green); }
.stat-val--r { color: var(--c-red); }
.stat-val--b { color: var(--c-accent); font-weight: 700; font-size: 1.3rem; }
.stat-key { font-size: .62rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--c-ink-4); text-align: center; }

/* ══ Toasts (identiques) ══ */
.toast-zone { position: fixed; bottom: 28px; right: 28px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
.nm-toast { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: var(--r-lg); font-size: .83rem; font-weight: 500; background: var(--c-ink); color: white; box-shadow: var(--sh-xl); pointer-events: all; transform: translateX(calc(100% + 30px)); transition: transform .35s var(--ease-spring); max-width: 340px; min-width: 260px; border: 1px solid rgba(255,255,255,.07); }
.nm-toast.show { transform: translateX(0); }
.toast-icon { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.nm-toast--success { background: #052e16; border-left: 3px solid #22c55e; }
.nm-toast--success .toast-icon { background: rgba(34,197,94,.2); color: #4ade80; }
.nm-toast--error   { background: #1c0a0a; border-left: 3px solid #ef4444; }
.nm-toast--error   .toast-icon { background: rgba(239,68,68,.2); color: #f87171; }
.nm-toast--warning { background: #1c1200; border-left: 3px solid #f59e0b; }
.nm-toast--warning .toast-icon { background: rgba(245,158,11,.2); color: #fbbf24; }
.nm-toast--info    { background: #030f1e; border-left: 3px solid #3b82f6; }
.nm-toast--info    .toast-icon { background: rgba(59,130,246,.2); color: #60a5fa; }
.toast-msg { flex: 1; line-height: 1.4; }
.toast-close { display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: rgba(255,255,255,.08); border: none; border-radius: 6px; color: rgba(255,255,255,.5); cursor: pointer; flex-shrink: 0; padding: 0; }

/* ══ Responsive ══ */
@media (max-width: 900px) {
    .nm-page > *:not(.site-header):not(.toast-zone) { padding-left: 20px; padding-right: 20px; }
    .header-content { padding: 28px 20px 24px; }
    .filters-row2 { grid-template-columns: 1fr; }
    .page-title { font-size: 2.2rem; }
    .stats-bar { flex-wrap: wrap; }
    .stat-item { min-width: 33%; border-bottom: 1px solid var(--c-border-2); }
    .nm-breadcrumb { gap: 4px; }
}
@media (max-width: 600px) {
    .page-title { font-size: 1.8rem; }
    .stat-item { min-width: 50%; }
    .step-options { gap: 6px; }
    .opt-card { font-size: .78rem; padding: 7px 10px; }
    .nm-spin-sm { display: inline-block; }
}
</style>
@endsection@extends('layouts.template')

{{-- ══════════════════════════════════════════════════════════
     Saisie des Notes — Lycée Technique de Bohicon
     NotesManager v3 · Compatible réseau local (sans Internet)
══════════════════════════════════════════════════════════ --}}

@section('content')

<div class="nm-page">

    {{-- ── TOASTS ── --}}
    <div class="toast-zone" id="toast-area"></div>

    {{-- ══ HERO HEADER ══ --}}
    <header class="site-header">
        <div class="header-bg-shape"></div>
        <div class="header-bg-shape header-bg-shape--2"></div>
        <div class="header-content">
            <div class="header-left">
                <div class="school-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                    Lycée Technique de Bohicon
                </div>
                <h1 class="page-title">Saisie des <em>Notes</em></h1>
                <p class="page-subtitle">Gestion des évaluations · NotesManager v3</p>
            </div>
            <div class="header-right">
                <div class="header-chips">
                    <div class="stat-chip">
                        <div class="stat-chip-icon stat-chip-icon--blue">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/></svg>
                        </div>
                        <div class="stat-chip-body">
                            <span class="stat-chip-val" id="student-count">0</span>
                            <span class="stat-chip-lbl">Élève(s)</span>
                        </div>
                    </div>
                    <div class="stat-chip">
                        <div class="stat-chip-icon stat-chip-icon--green">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="stat-chip-body">
                            <span class="stat-chip-val" id="saved-count">0</span>
                            <span class="stat-chip-lbl">Sauvegardé(s)</span>
                        </div>
                    </div>
                    <div class="stat-chip stat-chip--amber" id="pending-chip" style="display:none;">
                        <div class="stat-chip-icon stat-chip-icon--amber">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                        <div class="stat-chip-body">
                            <span class="stat-chip-val" id="saved-pending-text">0</span>
                            <span class="stat-chip-lbl">En attente</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- ══ BANNIÈRES ══ --}}
    <div class="banner banner--locked d-none" id="locked-banner">
        <div class="banner-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <div class="banner-body">
            <strong>Notes verrouillées</strong>
            <span>Consultation uniquement. Seul le censeur peut modifier.</span>
        </div>
    </div>
    <div class="banner banner--info d-none" id="partial-banner">
        <div class="banner-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="banner-body">
            <strong>Saisie partielle disponible</strong>
            <span>Les cases <strong>grisées avec cadenas</strong> ont déjà été enregistrées. Vous pouvez remplir les cases restantes.</span>
        </div>
    </div>

    {{-- ══ PANNEAU FILTRES ══ --}}
    <div class="nm-panel">
        <div class="panel-head">
            <div class="panel-head-left">
                <div class="panel-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                </div>
                <div>
                    <div class="panel-title">Contexte de saisie</div>
                    <div class="panel-subtitle">Sélectionnez la filière, la promotion, la classe et la matière</div>
                </div>
            </div>
            <div class="ctx-badge" id="ctx-badge"></div>
        </div>

        <div class="panel-body">

            {{-- ── Fil d'Ariane de la sélection ── --}}
            <div class="nm-breadcrumb" id="nm-breadcrumb">
                <div class="bc-item bc-item--year" id="bc-year">
                    <span class="bc-icon">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </span>
                    <span class="bc-label" id="bc-year-label">Année</span>
                </div>
                <span class="bc-sep">›</span>
                <div class="bc-item" id="bc-sector">
                    <span class="bc-icon">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10"/></svg>
                    </span>
                    <span class="bc-label" id="bc-sector-label">Filière</span>
                </div>
                <span class="bc-sep">›</span>
                <div class="bc-item" id="bc-promotion">
                    <span class="bc-icon">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </span>
                    <span class="bc-label" id="bc-promotion-label">Promotion</span>
                </div>
                <span class="bc-sep">›</span>
                <div class="bc-item" id="bc-classroom">
                    <span class="bc-icon">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                    </span>
                    <span class="bc-label" id="bc-classroom-label">Classe</span>
                </div>
            </div>

            {{-- ── Étape 1 : Filière ── --}}
            <div class="nm-step" id="step-sector">
                <div class="step-header">
                    <div class="step-num">1</div>
                    <div class="step-info">
                        <div class="step-title">Filière</div>
                        <div class="step-hint">Choisissez la filière d'enseignement</div>
                    </div>
                    <div class="step-loader" id="loader-sector" style="display:none;">
                        <div class="nm-spin-sm"></div>
                    </div>
                </div>
                <div class="step-options" id="options-sector">
                    {{-- Généré dynamiquement --}}
                </div>
            </div>

            {{-- ── Étape 2 : Promotion ── --}}
            <div class="nm-step nm-step--locked" id="step-promotion">
                <div class="step-header">
                    <div class="step-num">2</div>
                    <div class="step-info">
                        <div class="step-title">Promotion</div>
                        <div class="step-hint">Sélectionnez la promotion dans la filière choisie</div>
                    </div>
                    <div class="step-loader" id="loader-promotion" style="display:none;">
                        <div class="nm-spin-sm"></div>
                    </div>
                </div>
                <div class="step-options" id="options-promotion">
                    <div class="step-placeholder">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                        Choisissez d'abord une filière
                    </div>
                </div>
            </div>

            {{-- ── Étape 3 : Classe ── --}}
            <div class="nm-step nm-step--locked" id="step-classroom">
                <div class="step-header">
                    <div class="step-num">3</div>
                    <div class="step-info">
                        <div class="step-title">Classe</div>
                        <div class="step-hint">Choisissez la classe pour accéder aux notes</div>
                    </div>
                    <div class="step-loader" id="loader-classroom" style="display:none;">
                        <div class="nm-spin-sm"></div>
                    </div>
                </div>
                <div class="step-options" id="options-classroom">
                    <div class="step-placeholder">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                        Choisissez d'abord une promotion
                    </div>
                </div>
            </div>

            <div class="nm-sep">
                <span class="nm-sep-label">Configuration de l'évaluation</span>
            </div>

            {{-- ── Ligne 2 : Semestre + Matière ── --}}
            <div class="filters-row2" id="eval-config">

                <div class="fg">
                    <label>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M2 12h20"/></svg>
                        Semestre
                    </label>
                    <div class="sem-toggle">
                        <button type="button" class="sem-btn sem-btn--on" data-value="1">
                            <span class="sem-dot"></span>S1
                        </button>
                        <button type="button" class="sem-btn" data-value="2">
                            <span class="sem-dot"></span>S2
                        </button>
                    </div>
                    <input type="hidden" id="semester" value="1">
                </div>

                <div class="fg fg--wide">
                    <label>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        Matière
                    </label>
                    <div class="subject-row">
                        <div class="select-wrap" style="flex:1">
                            <select id="subject_id" disabled>
                                <option value="">— Sélectionner la classe d'abord —</option>
                            </select>
                            <div class="select-arrow">
                                <svg width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>
                        <div class="coeff-pill" id="coeff-pill">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                            Coeff <strong id="coefficient">—</strong>
                        </div>
                    </div>
                    <input type="hidden" id="ratio_id">
                    <input type="hidden" id="subject_real_id">
                </div>

            </div>

            {{-- Champs cachés (valeurs de contexte) --}}
            <input type="hidden" id="year_id">
            <input type="hidden" id="classroom_id">

            {{-- Barre de progression --}}
            <div class="progress-wrap" id="progress-wrap">
                <div class="progress-header">
                    <span class="progress-label-text">Progression des saisies</span>
                    <span class="progress-txt" id="progress-label">0%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
            </div>

        </div>
    </div>

    {{-- ══ RACCOURCIS ══ --}}
    <div class="shortcuts-bar" id="shortcuts-bar">
        <div class="sc-group">
            <span class="sc-item"><kbd>Tab</kbd> ou <kbd>↵</kbd> Navigation entre cellules</span>
            <span class="sc-divider"></span>
            <span class="sc-item"><kbd>Ctrl</kbd><kbd>S</kbd> Sauvegarde rapide</span>
            <span class="sc-divider"></span>
            <span class="sc-item">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Case grisée = note déjà enregistrée
            </span>
            <span class="sc-divider"></span>
            <span class="sc-item">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="4 6 4 2 20 2 20 22 4 22 4 18"/><polyline points="16 12 12 8 8 12"/><line x1="12" y1="8" x2="12" y2="21"/></svg>
                Clic en-tête = remplir colonne · Double-clic = moyenne
            </span>
        </div>
    </div>

    {{-- ══ ÉTAT VIDE ══ --}}
    <div class="empty-state" id="empty-state">
        <div class="empty-illustration">
            <div class="empty-rings">
                <div class="empty-ring empty-ring--1"></div>
                <div class="empty-ring empty-ring--2"></div>
                <div class="empty-ring empty-ring--3"></div>
            </div>
            <div class="empty-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="3" y1="15" x2="21" y2="15"/>
                    <line x1="9" y1="9" x2="9" y2="21"/>
                    <line x1="15" y1="9" x2="15" y2="21"/>
                </svg>
            </div>
        </div>
        <h3 id="empty-title">Chargement de l'année active…</h3>
        <p id="empty-desc">Veuillez patienter, le système initialise la saisie.</p>
        <div class="empty-steps" id="empty-steps-visual" style="display:none;">
            <div class="empty-step">
                <div class="es-num">1</div>
                <span>Filière</span>
            </div>
            <div class="es-arrow">→</div>
            <div class="empty-step">
                <div class="es-num">2</div>
                <span>Promotion</span>
            </div>
            <div class="es-arrow">→</div>
            <div class="empty-step">
                <div class="es-num">3</div>
                <span>Classe</span>
            </div>
            <div class="es-arrow">→</div>
            <div class="empty-step empty-step--action">
                <div class="es-num es-num--action">✓</div>
                <span>Matière</span>
            </div>
        </div>
    </div>

    {{-- ══ TABLEAU DES NOTES ══ --}}
    <div class="notes-card" id="notes-table-container">

        <div class="notes-card-header">
            <div class="card-title-area">
                <div class="card-title-dot-wrap">
                    <span class="card-title-dot"></span>
                    <span class="card-title-dot card-title-dot--2"></span>
                </div>
                <div class="card-title-text">
                    <span class="card-title-label">Matière active</span>
                    <span class="card-title-main" id="toolbar-subject">—</span>
                </div>
            </div>
            <div class="card-actions">
                <button class="btn-ghost" id="btn-reset">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.85"/></svg>
                    Changer de matière
                </button>
                <button class="btn-save" id="btn-save" disabled>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <span class="btn-save-text">Enregistrer</span>
                    <span class="save-badge" id="save-pending-badge" style="display:none;"></span>
                </button>
            </div>
        </div>

        <div class="table-scroll">
            <table class="nm-table">
                <thead>
                    <tr>
                        <th class="th-num">#</th>
                        <th class="th-name" colspan="2">
                            <div class="th-inner th-inner--left">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/></svg>
                                Élève
                            </div>
                        </th>
                        <th class="interro-header th-interro" data-index="0">
                            <div class="th-inner">
                                <span class="th-label th-label--i">I<sub>1</sub></span>
                                <input type="number" class="nm-hdr-inp" placeholder="—" min="0" max="20" step="0.01" title="Entrer une valeur → remplir colonne · Double-clic → moyenne">
                            </div>
                        </th>
                        <th class="interro-header th-interro" data-index="1">
                            <div class="th-inner">
                                <span class="th-label th-label--i">I<sub>2</sub></span>
                                <input type="number" class="nm-hdr-inp" placeholder="—" min="0" max="20" step="0.01">
                            </div>
                        </th>
                        <th class="interro-header th-interro" data-index="2">
                            <div class="th-inner">
                                <span class="th-label th-label--i">I<sub>3</sub></span>
                                <input type="number" class="nm-hdr-inp" placeholder="—" min="0" max="20" step="0.01">
                            </div>
                        </th>
                        <th class="th-avg">
                            <div class="th-inner">
                                <span class="th-label">Moy. I</span>
                                <span class="th-auto-badge">auto</span>
                            </div>
                        </th>
                        <th class="devoir-header th-devoir" data-field="devoir1">
                            <div class="th-inner">
                                <span class="th-label th-label--d">D<sub>1</sub></span>
                                <input type="number" class="nm-hdr-inp" placeholder="—" min="0" max="20" step="0.01">
                            </div>
                        </th>
                        <th class="devoir-header th-devoir" data-field="devoir2">
                            <div class="th-inner">
                                <span class="th-label th-label--d">D<sub>2</sub></span>
                                <input type="number" class="nm-hdr-inp" placeholder="—" min="0" max="20" step="0.01">
                            </div>
                        </th>
                        <th class="th-final">
                            <div class="th-inner">
                                <span class="th-label">Moy. /20</span>
                                <span class="th-auto-badge">auto</span>
                            </div>
                        </th>
                        <th style="width:36px;"></th>
                    </tr>
                </thead>
                <tbody id="notes-body"></tbody>
            </table>
        </div>

        {{-- Barre de statistiques --}}
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-item-icon stat-item-icon--neutral">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <span class="stat-val" id="stat-avg">—</span>
                <span class="stat-key">Moy. classe</span>
            </div>
            <div class="stat-item">
                <div class="stat-item-icon stat-item-icon--green">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span class="stat-val stat-val--g" id="stat-pass">—</span>
                <span class="stat-key">≥ 10 admis</span>
            </div>
            <div class="stat-item">
                <div class="stat-item-icon stat-item-icon--red">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </div>
                <span class="stat-val stat-val--r" id="stat-fail">—</span>
                <span class="stat-key">< 10 ajourné</span>
            </div>
            <div class="stat-item">
                <div class="stat-item-icon stat-item-icon--green">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </div>
                <span class="stat-val stat-val--g" id="stat-max">—</span>
                <span class="stat-key">Meilleure note</span>
            </div>
            <div class="stat-item">
                <div class="stat-item-icon stat-item-icon--red">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
                </div>
                <span class="stat-val stat-val--r" id="stat-min">—</span>
                <span class="stat-key">Note la plus faible</span>
            </div>
            <div class="stat-item stat-item--highlight">
                <div class="stat-item-icon stat-item-icon--blue">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <span class="stat-val stat-val--b" id="stat-taux">—</span>
                <span class="stat-key">Taux de réussite</span>
            </div>
        </div>

    </div>{{-- /.notes-card --}}

</div>{{-- /.nm-page --}}

@endsection


@section('another_JS')
<script>
/* ═══════════════════════════════════════════════════════════
   NotesManager v3 — Sélection contextuelle intelligente
   ✅ Année active auto-chargée au démarrage
   ✅ Cascade : Filière → Promotion → Classe (avec auto-sélection)
   ✅ Chargement automatique des notes dès la classe choisie
   ✅ Pas de bouton "Charger" — tout est automatique
   ✅ Compatible réseau local (Laragon)
═══════════════════════════════════════════════════════════ */
class NotesManager {
    constructor() {
        this.data          = null;
        this.canEdit       = true;
        this.canModify     = true;
        this.saved         = new Set();
        this.modified      = new Set();
        this.INTERRO_COUNT = 3;
        this.csrf          = '{{ csrf_token() }}';
        this.baseUrl       = '{{ rtrim(url('/'), '/') }}';

        // État de la cascade
        this.state = {
            yearId:      null,
            yearLabel:   null,
            sectorId:    null,
            sectorLabel: null,
            promotionId: null,
            promoLabel:  null,
            classroomId: null,
            classLabel:  null,
        };

        this._init();
    }

    /* ── Utilitaires ── */
    trunc2(v) { return Math.floor(v * 100) / 100; }
    sid(id)   { return String(id); }
    esc(s)    { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; }
    $(id)     { return document.getElementById(id); }
    $$(sel)   { return document.querySelectorAll(sel); }

    toast(type, msg, dur = 4200) {
        const icons = {
            success: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>',
            error:   '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
            warning: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
            info:    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
        };
        const t = document.createElement('div');
        t.className = `nm-toast nm-toast--${type}`;
        t.innerHTML = `<span class="toast-icon">${icons[type]||icons.info}</span>
            <span class="toast-msg">${msg}</span>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>`;
        this.$('toast-area').appendChild(t);
        requestAnimationFrame(() => t.classList.add('show'));
        if (dur) setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 350); }, dur);
    }

    async get(path) {
        const url = path.startsWith('http') ? path : this.baseUrl + path;
        const r = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        if (!r.ok) throw new Error((await r.json().catch(() => ({}))).message || `Erreur ${r.status}`);
        return r.json();
    }

    async post(path, body) {
        const url = path.startsWith('http') ? path : this.baseUrl + path;
        const r = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(body)
        });
        const j = await r.json().catch(() => ({}));
        if (!r.ok && j.message) throw new Error(j.message);
        return j;
    }

    // ═══════════════════════════════════════════════════════
    // Init
    // ═══════════════════════════════════════════════════════
    _init() {
        // Semestre toggle
        this.$$('.sem-btn').forEach(b => b.addEventListener('click', () => {
            this.$$('.sem-btn').forEach(x => x.classList.remove('sem-btn--on'));
            b.classList.add('sem-btn--on');
            this.$('semester').value = b.dataset.value;
            // Si une classe est déjà choisie, recharger les matières
            if (this.state.classroomId) {
                this._loadSubjects();
            }
        }));

        // Matière
        this.$('subject_id').addEventListener('change', () => {
            this._syncCoeff();
            if (this.$('subject_id').value) {
                this.loadNotes();
            } else {
                this._clearTable();
            }
        });

        // Bouton reset / "Changer de matière"
        this.$('btn-reset').addEventListener('click', () => this._resetToSubjectSelection());

        // Sauvegarde
        this.$('btn-save').addEventListener('click', () => this.saveNotes());

        // Raccourcis clavier
        document.addEventListener('keydown', e => {
            if (e.ctrlKey && e.key === 's') { e.preventDefault(); this.saveNotes(); return; }
            if ((e.key === 'Tab' || e.key === 'Enter') && !e.shiftKey) {
                const a = document.activeElement;
                if (a?.classList.contains('nm-inp')) {
                    e.preventDefault();
                    const all = [...this.$$('.nm-inp:not(:disabled):not([readonly])')];
                    const i   = all.indexOf(a);
                    i < all.length - 1 ? all[i + 1].focus() : this.$('btn-save').focus();
                }
            }
        });

        // Démarrage : charger l'année active
        this._loadActiveYear();
    }

    // ═══════════════════════════════════════════════════════
    // ÉTAPE 0 — Année active
    // ═══════════════════════════════════════════════════════
    async _loadActiveYear() {
        try {
            const d = await this.get('/api/active-year');
            if (d.success && d.year) {
                this.state.yearId    = d.year.id;
                this.state.yearLabel = d.year.year;
                this.$('year_id').value = d.year.id;
                this._updateBreadcrumb('year', d.year.year);
                this._showEmptyWithSteps();
                await this._loadSectors();
            } else {
                this._showEmptyError('Aucune année scolaire active trouvée dans le système.');
            }
        } catch (e) {
            this._showEmptyError('Impossible de charger l\'année active : ' + e.message);
        }
    }

    // ═══════════════════════════════════════════════════════
    // ÉTAPE 1 — Filières
    // ═══════════════════════════════════════════════════════
    async _loadSectors() {
        this._setStepLoading('sector', true);
        this.$('step-sector').classList.remove('nm-step--locked');
        try {
            const d = await this.get(`/api/sectors-for-create/${this.state.yearId}`);
            this._setStepLoading('sector', false);
            if (!d.success || !d.sectors.length) {
                this._renderOptions('sector', [], 'Aucune filière disponible pour cette année.');
                return;
            }
            this._renderOptions('sector', d.sectors, null, (item) => this._onSectorClick(item));
            // Auto-sélection si une seule filière
            if (d.auto) {
                await this._onSectorClick(d.sectors[0], true);
            }
        } catch(e) {
            this._setStepLoading('sector', false);
            this.toast('error', 'Chargement filières : ' + e.message);
        }
    }

    async _onSectorClick(sector, auto = false) {
        // Mettre en évidence l'option sélectionnée
        this._selectOption('sector', sector.id);
        this.state.sectorId    = sector.id;
        this.state.sectorLabel = sector.name;
        this._updateBreadcrumb('sector', sector.name, auto);

        // Réinitialiser en aval
        this._resetFrom('promotion');
        await this._loadPromotions();
    }

    // ═══════════════════════════════════════════════════════
    // ÉTAPE 2 — Promotions
    // ═══════════════════════════════════════════════════════
    async _loadPromotions() {
        this._setStepLoading('promotion', true);
        this.$('step-promotion').classList.remove('nm-step--locked');
        try {
            const d = await this.get(`/api/promotions-for-create/${this.state.yearId}/${this.state.sectorId}`);
            this._setStepLoading('promotion', false);
            if (!d.success || !d.promotions.length) {
                this._renderOptions('promotion', [], 'Aucune promotion disponible.');
                return;
            }
            this._renderOptions('promotion', d.promotions, null, (item) => this._onPromotionClick(item));
            if (d.auto) {
                await this._onPromotionClick(d.promotions[0], true);
            }
        } catch(e) {
            this._setStepLoading('promotion', false);
            this.toast('error', 'Chargement promotions : ' + e.message);
        }
    }

    async _onPromotionClick(promotion, auto = false) {
        this._selectOption('promotion', promotion.id);
        this.state.promotionId = promotion.id;
        this.state.promoLabel  = promotion.name;
        this._updateBreadcrumb('promotion', promotion.name, auto);

        this._resetFrom('classroom');
        await this._loadClassrooms();
    }

    // ═══════════════════════════════════════════════════════
    // ÉTAPE 3 — Classes
    // ═══════════════════════════════════════════════════════
    async _loadClassrooms() {
        this._setStepLoading('classroom', true);
        this.$('step-classroom').classList.remove('nm-step--locked');
        try {
            const d = await this.get(`/api/classrooms-for-create/${this.state.promotionId}?year_id=${this.state.yearId}`);
            this._setStepLoading('classroom', false);
            if (!d.success || !d.classrooms.length) {
                this._renderOptions('classroom', [], 'Aucune classe disponible.');
                return;
            }
            this._renderOptions('classroom', d.classrooms, null, (item) => this._onClassroomClick(item));
            if (d.auto) {
                await this._onClassroomClick(d.classrooms[0], true);
            }
        } catch(e) {
            this._setStepLoading('classroom', false);
            this.toast('error', 'Chargement classes : ' + e.message);
        }
    }

    async _onClassroomClick(classroom, auto = false) {
        this._selectOption('classroom', classroom.id);
        this.state.classroomId = classroom.id;
        this.state.classLabel  = classroom.name;
        this.$('classroom_id').value = classroom.id;
        this._updateBreadcrumb('classroom', classroom.name, auto);

        // Mettre à jour le badge contexte
        const badge = this.$('ctx-badge');
        badge.textContent = `${this.state.sectorLabel} — ${this.state.promoLabel} · ${classroom.name}`;
        badge.style.display = 'inline-flex';

        // Charger les matières et si une seule → charger les notes directement
        await this._loadSubjects();
    }

    // ═══════════════════════════════════════════════════════
    // MATIÈRES
    // ═══════════════════════════════════════════════════════
    async _loadSubjects() {
        const cl  = this.state.classroomId;
        const y   = this.state.yearId;
        const sem = this.$('semester').value;
        if (!cl || !y) return;

        const sel = this.$('subject_id');
        sel.innerHTML = '<option>Chargement…</option>'; sel.disabled = true;
        try {
            const d = await this.post('/api/subjects-by-classroom', { classroom_id: cl, year_id: y, semester: sem ? parseInt(sem) : null });
            sel.innerHTML = '<option value="">— Choisir une matière —</option>';
            if (d.success && d.subjects?.length) {
                d.subjects.forEach(s => {
                    const o = document.createElement('option');
                    o.value = s.ratio_id; o.dataset.coefficient = s.coefficient; o.dataset.subjectId = s.subject_id;
                    const sl = s.semester === 1 ? ' [S1]' : s.semester === 2 ? ' [S2]' : '';
                    o.textContent = `${s.subject_name}${sl}  —  Coeff. ${s.coefficient}`;
                    sel.appendChild(o);
                });
                sel.disabled = false;

                // Auto-sélection si une seule matière
                if (d.subjects.length === 1) {
                    sel.selectedIndex = 1;
                    this._syncCoeff();
                    await this.loadNotes();
                } else {
                    // Montrer l'état vide avec instructions matière
                    this._showEmptySelectSubject();
                }
            } else {
                this.toast('warning', d.message || 'Aucune matière disponible pour ce semestre.');
                this._showEmptySelectSubject();
            }
        } catch (e) {
            sel.innerHTML = '<option value="">— Erreur de chargement —</option>';
            this.toast('error', 'Impossible de charger les matières : ' + e.message);
        }
    }

    _syncCoeff() {
        const o = this.$('subject_id').options[this.$('subject_id').selectedIndex];
        if (o?.value) {
            this.$('coefficient').textContent  = o.dataset.coefficient || '1';
            this.$('ratio_id').value           = o.value;
            this.$('subject_real_id').value    = o.dataset.subjectId || '';
            this.$('coeff-pill').style.display = 'flex';
        } else {
            this.$('ratio_id').value           = '';
            this.$('coeff-pill').style.display = 'none';
        }
    }

    // ═══════════════════════════════════════════════════════
    // Gestion des états de l'empty state
    // ═══════════════════════════════════════════════════════
    _showEmptyWithSteps() {
        const t = this.$('empty-title');
        const d = this.$('empty-desc');
        const s = this.$('empty-steps-visual');
        if (t) t.textContent = 'Sélectionnez votre contexte';
        if (d) d.textContent = 'Choisissez la filière, puis la promotion et la classe pour accéder à la saisie.';
        if (s) s.style.display = 'flex';
        this.$('empty-state').style.display = 'flex';
        this.$('notes-table-container').style.display = 'none';
    }

    _showEmptySelectSubject() {
        const t = this.$('empty-title');
        const d = this.$('empty-desc');
        if (t) t.textContent = 'Classe sélectionnée — Choisissez une matière';
        if (d) d.innerHTML = 'Utilisez le menu <strong>Matière</strong> ci-dessus pour charger les notes.';
        this.$('empty-state').style.display = 'flex';
        this.$('notes-table-container').style.display = 'none';
    }

    _showEmptyError(msg) {
        const t = this.$('empty-title');
        const d = this.$('empty-desc');
        if (t) t.textContent = 'Une erreur est survenue';
        if (d) d.textContent = msg;
        this.$('empty-state').style.display = 'flex';
        this.$('notes-table-container').style.display = 'none';
    }

    // ═══════════════════════════════════════════════════════
    // Helpers UI — Options en cartes cliquables
    // ═══════════════════════════════════════════════════════
    _renderOptions(stepName, items, emptyMsg, onClick) {
        const container = this.$(`options-${stepName}`);
        if (!container) return;
        if (!items.length) {
            container.innerHTML = `<div class="step-placeholder step-placeholder--warn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                ${emptyMsg || 'Aucun élément disponible'}
            </div>`;
            return;
        }
        container.innerHTML = items.map(item => `
            <button type="button" class="opt-card" data-id="${item.id}" data-step="${stepName}">
                <span class="opt-label">${this.esc(item.name)}</span>
                <span class="opt-arrow">›</span>
            </button>
        `).join('');
        container.querySelectorAll('.opt-card').forEach(btn => {
            btn.addEventListener('click', () => {
                const item = items.find(i => String(i.id) === btn.dataset.id);
                if (item) onClick(item);
            });
        });
    }

    _selectOption(stepName, id) {
        const container = this.$(`options-${stepName}`);
        if (!container) return;
        container.querySelectorAll('.opt-card').forEach(btn => {
            btn.classList.toggle('opt-card--active', String(btn.dataset.id) === String(id));
        });
    }

    _setStepLoading(stepName, loading) {
        const loader = this.$(`loader-${stepName}`);
        if (loader) loader.style.display = loading ? 'flex' : 'none';
    }

    _updateBreadcrumb(part, label, auto = false) {
        const el = this.$(`bc-${part}-label`);
        if (el) {
            el.textContent = label + (auto ? '' : '');
        }
        const item = this.$(`bc-${part}`);
        if (item) item.classList.add('bc-item--active');
    }

    // ═══════════════════════════════════════════════════════
    // Reset partiel — revenir à une étape donnée
    // ═══════════════════════════════════════════════════════
    _resetFrom(step) {
        const steps = ['sector', 'promotion', 'classroom'];
        const idx   = steps.indexOf(step);
        if (idx === -1) return;

        for (let i = idx; i < steps.length; i++) {
            const s = steps[i];
            const stepEl = this.$(`step-${s}`);
            const optEl  = this.$(`options-${s}`);
            if (stepEl) stepEl.classList.add('nm-step--locked');
            if (optEl) optEl.innerHTML = `<div class="step-placeholder">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                Choisissez d'abord ${s === 'promotion' ? 'une filière' : 'une promotion'}
            </div>`;

            // Reset état
            if (s === 'promotion') { this.state.promotionId = null; this.state.promoLabel = null; }
            if (s === 'classroom') { this.state.classroomId = null; this.state.classLabel = null; }
        }

        // Reset filière si demandé
        if (idx === 0) { this.state.sectorId = null; this.state.sectorLabel = null; }

        // Reset matières et tableau
        const sel = this.$('subject_id');
        sel.innerHTML = '<option value="">— Sélectionner la classe d\'abord —</option>'; sel.disabled = true;
        this.$('ratio_id').value = ''; this.$('coeff-pill').style.display = 'none';
        this.$('classroom_id').value = '';
        this.$('ctx-badge').style.display = 'none';
        this._clearTable();
    }

    _resetToSubjectSelection() {
        // Revenir à la sélection de matière sans toucher à la cascade filière/promo/classe
        const sel = this.$('subject_id');
        sel.selectedIndex = 0;
        this._syncCoeff();
        this._clearTable();
        if (this.state.classroomId) {
            this._showEmptySelectSubject();
        }
    }

    // ═══════════════════════════════════════════════════════
    // Chargement des notes (plus de bouton — appelé auto)
    // ═══════════════════════════════════════════════════════
    async loadNotes() {
        const cl = this.state.classroomId;
        const y  = this.state.yearId;
        const r  = this.$('ratio_id').value;
        const s  = this.$('semester').value;

        if (!cl || !y || !r || !s) {
            this.toast('warning', 'Contexte incomplet — vérifiez la sélection.');
            return;
        }

        // Feedback visuel sur le select de matières
        const subjectSel = this.$('subject_id');
        const origLabel  = subjectSel.options[subjectSel.selectedIndex]?.text || '';
        subjectSel.disabled = true;

        try {
            const d = await this.post('/api/students-with-notes', { year_id: y, classroom_id: cl, ratio_id: r, semester: s });
            if (d.success) {
                this.data = d.students; this.canEdit = d.can_edit ?? true; this.canModify = d.can_modify ?? true;
                this.saved.clear(); this.modified.clear();
                d.is_locked ? this.$('locked-banner').classList.remove('d-none') : this.$('locked-banner').classList.add('d-none');
                const partial = d.students.some(s => Object.values(s.fields_readonly || {}).some(v => v));
                (partial && !d.is_locked) ? this.$('partial-banner').classList.remove('d-none') : this.$('partial-banner').classList.add('d-none');

                const lbl = this.$('subject_id').options[this.$('subject_id').selectedIndex]?.text || '—';
                this.$('toolbar-subject').textContent = lbl;
                this._render();
                this.toast('success', `${d.students.length} élève(s) — notes chargées.`);
            } else {
                this.toast('warning', d.message || 'Aucun étudiant trouvé.');
                this._showEmptySelectSubject();
            }
        } catch (e) {
            this.toast('error', e.message);
            this._showEmptySelectSubject();
        } finally {
            subjectSel.disabled = false;
        }
    }

    // ═══════════════════════════════════════════════════════
    // Rendu du tableau (identique à l'original)
    // ═══════════════════════════════════════════════════════
    _render() {
        if (!this.data?.length) { this._clearTable(); return; }
        this.$('empty-state').style.display           = 'none';
        this.$('notes-table-container').style.display = 'block';
        this.$('shortcuts-bar').style.display         = 'flex';
        this.$('student-count').textContent           = this.data.length;
        this.$('progress-wrap').style.display         = 'flex';

        const rows = this.data.map((s, i) => {
            const interros = s.interros || [];
            const disabled = s.is_disabled || !this.canEdit;
            const fr       = s.fields_readonly || {};
            let iCells = '';
            for (let k = 0; k < this.INTERRO_COUNT; k++) {
                const locked = disabled ? false : (fr[`interro_${k}`] || false);
                const attr   = disabled ? 'disabled' : locked ? 'readonly' : '';
                const val    = (interros[k] !== undefined && interros[k] !== null) ? this.trunc2(parseFloat(interros[k])).toFixed(2) : '';
                iCells += `<td class="td-note${locked ? ' td-locked' : ''}">
                    <div class="inp-wrap${locked ? ' inp-wrap--lock' : ''}">
                        <input type="number" class="nm-inp" data-idx="${i}" data-ii="${k}" data-id="${s.recording_id}"
                               value="${val}" min="0" max="20" step="0.01" placeholder="—" ${attr}
                               ${locked ? 'title="Note enregistrée"' : ''}>
                        ${locked ? '<span class="inp-lock-ico">🔒</span>' : ''}
                    </div></td>`;
            }
            const moyI = s.moy_interros != null ? this.trunc2(parseFloat(s.moy_interros)).toFixed(2) : '—';
            let dCells = '';
            ['devoir1', 'devoir2'].forEach(f => {
                const locked = disabled ? false : (fr[f] || false);
                const attr   = disabled ? 'disabled' : locked ? 'readonly' : '';
                const val    = s[f] != null ? this.trunc2(parseFloat(s[f])).toFixed(2) : '';
                dCells += `<td class="td-note${locked ? ' td-locked' : ''}">
                    <div class="inp-wrap${locked ? ' inp-wrap--lock' : ''}">
                        <input type="number" class="nm-inp" data-field="${f}" data-idx="${i}" data-id="${s.recording_id}"
                               value="${val}" min="0" max="20" step="0.01" placeholder="—" ${attr}
                               ${locked ? 'title="Note enregistrée"' : ''}>
                        ${locked ? '<span class="inp-lock-ico">🔒</span>' : ''}
                    </div></td>`;
            });
            const moy20 = s.moy_20 != null ? this.trunc2(parseFloat(s.moy_20)) : null;
            const m20s  = moy20 != null ? moy20.toFixed(2) : '—';
            const m20c  = moy20 == null ? '' : moy20 >= 10 ? 'moy-pass' : 'moy-fail';
            const partial = Object.values(fr).some(v => v);
            return `<tr class="nm-row${s.field_readonly ? ' nm-row--lock' : ''}" data-idx="${i}" data-id="${s.recording_id}">
                <td class="td-num">${i + 1}</td>
                <td class="td-name">${this.esc(s.name)}${partial ? '<span class="partial-badge" title="Notes partiellement enregistrées">~</span>' : ''}</td>
                <td class="td-surname">${this.esc(s.surname)}</td>
                ${iCells}
                <td class="td-avg"><span class="nm-moy" data-type="mi" data-idx="${i}">${moyI}</span></td>
                ${dCells}
                <td class="td-final"><span class="nm-moy-final ${m20c}" data-type="m20" data-idx="${i}">${m20s}</span></td>
                <td class="td-status"><span class="nm-status ${s.field_readonly ? 'st-locked' : 'st-pending'}" data-idx="${i}" title="${s.field_readonly ? 'Verrouillé' : 'En attente'}"></span></td>
            </tr>`;
        });

        this.$('notes-body').innerHTML = rows.join('');
        const hasEditable = this.canEdit && this.data.some(s => !s.is_disabled && !s.field_readonly);
        this.$('btn-save').disabled = !hasEditable;
        this._bindInputs(); this._bindHeaders(); this._updateStats(); this._updateProgress();
    }

    _bindInputs() {
        this.$('notes-body').querySelectorAll('.nm-inp').forEach(inp => {
            inp.addEventListener('input', () => this._recalc(inp));
            inp.addEventListener('blur',  () => this._fmt(inp));
            inp.addEventListener('focus', () => inp.select());
        });
    }

    _bindHeaders() {
        this.$$('.nm-hdr-inp').forEach(inp => {
            inp.addEventListener('blur',    () => this._applyHeader(inp));
            inp.addEventListener('dblclick',() => this._fillAvg(inp));
            inp.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); inp.blur(); } });
        });
    }

    _applyHeader(h) {
        const v = parseFloat(h.value.trim());
        if (isNaN(v) || v < 0 || v > 20) return;
        const th = h.closest('th');
        if (th.classList.contains('interro-header')) {
            this.$('notes-body').querySelectorAll(`.nm-inp[data-ii="${th.dataset.index}"]:not(:disabled):not([readonly])`)
                .forEach(inp => { inp.value = this.trunc2(v).toFixed(2); this._recalc(inp); });
        } else if (th.classList.contains('devoir-header')) {
            this.$('notes-body').querySelectorAll(`.nm-inp[data-field="${th.dataset.field}"]:not(:disabled):not([readonly])`)
                .forEach(inp => { inp.value = this.trunc2(v).toFixed(2); this._recalc(inp); });
        }
        this.toast('info', `Colonne remplie avec ${v.toFixed(2)}`);
    }

    _fillAvg(h) {
        const th = h.closest('th');
        let sel = '';
        if (th.classList.contains('interro-header')) sel = `.nm-inp[data-ii="${th.dataset.index}"]:not(:disabled):not([readonly])`;
        else if (th.classList.contains('devoir-header')) sel = `.nm-inp[data-field="${th.dataset.field}"]:not(:disabled):not([readonly])`;
        if (!sel) return;
        const vals = [...this.$('notes-body').querySelectorAll(sel)].map(i => parseFloat(i.value)).filter(v => !isNaN(v));
        if (!vals.length) return;
        const avg = vals.reduce((a, b) => a + b, 0) / vals.length;
        this.$('notes-body').querySelectorAll(sel).forEach(inp => { inp.value = avg.toFixed(2); this._recalc(inp); });
        this.toast('success', `Moyenne colonne appliquée : ${avg.toFixed(2)}`);
    }

    _recalc(inp) {
        if (inp.hasAttribute('readonly')) return;
        const idx = parseInt(inp.dataset.idx);
        const row = this.$('notes-body').querySelector(`tr[data-idx="${idx}"]`);
        if (!row) return;
        const sid = this.sid(inp.dataset.id);
        this.modified.add(sid); this.saved.delete(sid);
        row.classList.add('nm-row--mod'); row.classList.remove('nm-row--saved');
        const st = row.querySelector('.nm-status');
        if (st && !st.classList.contains('st-locked')) st.className = 'nm-status st-mod';
        const interros = [];
        for (let k = 0; k < this.INTERRO_COUNT; k++) {
            const inp2 = row.querySelector(`.nm-inp[data-ii="${k}"]`);
            if (inp2 && inp2.value !== '' && !inp2.disabled) { const v = parseFloat(inp2.value); if (!isNaN(v)) interros.push(v); }
        }
        const gD = f => { const i = row.querySelector(`.nm-inp[data-field="${f}"]`); if (!i || i.value === '' || i.disabled) return null; const v = parseFloat(i.value); return isNaN(v) ? null : v; };
        const d1 = gD('devoir1'), d2 = gD('devoir2');
        const moyI  = interros.length ? this.trunc2(interros.reduce((a, b) => a + b, 0) / interros.length) : null;
        const comp  = [moyI, d1, d2].filter(v => v !== null);
        const moy20 = comp.length ? this.trunc2(comp.reduce((a, b) => a + b, 0) / comp.length) : null;
        const miEl = row.querySelector('[data-type="mi"]');
        if (miEl) miEl.textContent = moyI !== null ? moyI.toFixed(2) : '—';
        const m20El = row.querySelector('[data-type="m20"]');
        if (m20El) { m20El.textContent = moy20 !== null ? moy20.toFixed(2) : '—'; m20El.className = 'nm-moy-final' + (moy20 === null ? '' : moy20 >= 10 ? ' moy-pass' : ' moy-fail'); }
        if (this.data[idx]) Object.assign(this.data[idx], { interros, devoir1: d1, devoir2: d2, moy_interros: moyI, moy_20: moy20 });
        if (this.modified.size > 0 && this.canEdit) this.$('btn-save').disabled = false;
        this._updatePendingChip(); this._updateStats(); this._updateProgress();
    }

    _fmt(inp) {
        if (inp.value !== '' && !inp.disabled && !inp.readOnly) {
            const v = parseFloat(inp.value);
            if (!isNaN(v)) inp.value = this.trunc2(Math.min(Math.max(v, 0), 20)).toFixed(2);
        }
    }

    async saveNotes() {
        if (!this.canEdit) { this.toast('warning', 'Notes verrouillées.'); return; }
        if (!this.data?.length || !this.modified.size) { this.toast('info', 'Aucune modification à sauvegarder.'); return; }
        const modData = this.data.filter(s => this.modified.has(this.sid(s.recording_id)));
        if (!modData.length) return;
        const notes = modData.map(s => {
            const row = this.$('notes-body').querySelector(`tr[data-id="${s.recording_id}"]`);
            const interros = [];
            for (let k = 0; k < this.INTERRO_COUNT; k++) {
                const inp = row?.querySelector(`.nm-inp[data-ii="${k}"]`);
                interros.push((inp && inp.value !== '' && !inp.disabled) ? parseFloat(inp.value) : null);
            }
            const d1i = row?.querySelector('.nm-inp[data-field="devoir1"]');
            const d2i = row?.querySelector('.nm-inp[data-field="devoir2"]');
            return { recording_id: s.recording_id, interros,
                devoir1: (d1i && d1i.value !== '' && !d1i.disabled) ? parseFloat(d1i.value) : null,
                devoir2: (d2i && d2i.value !== '' && !d2i.disabled) ? parseFloat(d2i.value) : null };
        });
        const payload = {
            year_id:      this.state.yearId,
            classroom_id: this.state.classroomId,
            ratio_id:     this.$('ratio_id').value,
            semester:     this.$('semester').value,
            notes
        };
        this.$('btn-save').disabled = true;
        const saveTextEl = this.$('btn-save').querySelector('.btn-save-text');
        if (saveTextEl) saveTextEl.textContent = 'Enregistrement…';
        try {
            const res = await this.post('/api/notes/store-bulk', payload);
            if (res.success) {
                modData.forEach(s => {
                    const id  = this.sid(s.recording_id);
                    const row = this.$('notes-body').querySelector(`tr[data-id="${s.recording_id}"]`);
                    this.saved.add(id); this.modified.delete(id);
                    if (row) { row.classList.remove('nm-row--mod'); row.classList.add('nm-row--saved');
                        const st = row.querySelector('.nm-status'); if (st && !st.classList.contains('st-locked')) st.className = 'nm-status st-saved'; }
                });
                this.$('saved-count').textContent = this.saved.size;
                this._updatePendingChip();
                this.toast('success', res.message || 'Notes enregistrées avec succès.');
                await this.loadNotes();
            } else { this.toast('error', res.message || 'Erreur lors de la sauvegarde.'); this.$('btn-save').disabled = false; }
        } catch (e) { this.toast('error', e.message); this.$('btn-save').disabled = false; }
        finally { if (saveTextEl) saveTextEl.textContent = 'Enregistrer'; }
    }

    _clearTable() {
        this.data = null; this.canEdit = true; this.canModify = true;
        this.saved.clear(); this.modified.clear();
        this.$('notes-body').innerHTML = '';
        this.$('notes-table-container').style.display = 'none';
        this.$('shortcuts-bar').style.display         = 'none';
        this.$('progress-wrap').style.display         = 'none';
        this.$('locked-banner').classList.add('d-none');
        this.$('partial-banner').classList.add('d-none');
        this.$('btn-save').disabled                   = true;
        this.$('student-count').textContent           = '0';
        this.$('saved-count').textContent             = '0';
        this.$('pending-chip').style.display          = 'none';
        this._updateStats();
    }

    _updatePendingChip() {
        const n = this.modified.size, chip = this.$('pending-chip');
        if (n > 0) { chip.style.display = 'flex'; this.$('saved-pending-text').textContent = n; }
        else        { chip.style.display = 'none'; }
        const badge = this.$('save-pending-badge');
        if (badge) { badge.textContent = n || ''; badge.style.display = n > 0 ? 'inline-flex' : 'none'; }
    }

    _updateProgress() {
        if (!this.data?.length) return;
        const total = this.data.filter(s => !s.is_disabled).length;
        const done  = this.data.filter(s => !s.is_disabled && s.moy_20 != null).length;
        const pct   = total > 0 ? Math.round((done / total) * 100) : 0;
        this.$('progress-fill').style.width  = pct + '%';
        this.$('progress-label').textContent = `${pct}% — ${done} / ${total} élèves`;
    }

    _updateStats() {
        const ids = ['stat-avg','stat-pass','stat-fail','stat-max','stat-min','stat-taux'];
        if (!this.data?.length) { ids.forEach(id => { const el = this.$(id); if (el) el.textContent = '—'; }); return; }
        const vals = this.data.filter(s => !s.is_disabled && s.moy_20 != null).map(s => this.trunc2(parseFloat(s.moy_20)));
        if (!vals.length) return;
        const avg  = this.trunc2(vals.reduce((a, b) => a + b, 0) / vals.length);
        const pass = vals.filter(v => v >= 10).length;
        const fail = vals.length - pass;
        const max  = Math.max(...vals), min = Math.min(...vals);
        const taux = Math.round((pass / vals.length) * 100);
        this.$('stat-avg').textContent  = avg.toFixed(2);
        this.$('stat-pass').textContent = pass;
        this.$('stat-fail').textContent = fail;
        this.$('stat-max').textContent  = max.toFixed(2);
        this.$('stat-min').textContent  = min.toFixed(2);
        this.$('stat-taux').textContent = taux + '%';
    }
}

new NotesManager();
</script>

<style>
/* ── Variables (identiques à l'original) ── */
:root {
    --c-bg:           #f4f6fb;
    --c-surface:      #ffffff;
    --c-surface-2:    #f8f9fc;
    --c-border:       #e3e8f0;
    --c-border-2:     #edf0f7;
    --c-ink:          #111827;
    --c-ink-2:        #374151;
    --c-ink-3:        #6b7280;
    --c-ink-4:        #9ca3af;
    --c-ink-5:        #d1d5db;
    --c-accent:       #4f46e5;
    --c-accent-2:     #6366f1;
    --c-accent-light: #eef2ff;
    --c-accent-mid:   #c7d2fe;
    --c-green:        #059669;
    --c-green-bg:     #ecfdf5;
    --c-green-border: #a7f3d0;
    --c-red:          #dc2626;
    --c-red-bg:       #fef2f2;
    --c-red-border:   #fecaca;
    --c-amber:        #d97706;
    --c-amber-bg:     #fffbeb;
    --c-amber-border: #fde68a;
    --c-blue:         #2563eb;
    --c-blue-bg:      #eff6ff;
    --c-blue-border:  #bfdbfe;
    --sh-xs:    0 1px 2px rgba(0,0,0,.05);
    --sh-sm:    0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
    --sh-md:    0 4px 12px rgba(0,0,0,.06), 0 2px 4px rgba(0,0,0,.04);
    --sh-lg:    0 10px 30px rgba(0,0,0,.08), 0 4px 8px rgba(0,0,0,.04);
    --sh-xl:    0 20px 60px rgba(0,0,0,.1),  0 8px 16px rgba(0,0,0,.06);
    --r-sm:  6px; --r-md:  10px; --r-lg:  16px; --r-xl:  20px;
    --font:       -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
    --font-mono:  'Consolas', 'Courier New', monospace;
    --ease: cubic-bezier(.4, 0, .2, 1);
    --ease-spring: cubic-bezier(.34, 1.56, .64, 1);
}
.nm-page *, .nm-page *::before, .nm-page *::after { box-sizing: border-box; margin: 0; padding: 0; }
.nm-page { font-family: var(--font); font-size: 14px; color: var(--c-ink); background: var(--c-bg); min-height: 100vh; padding: 0 0 80px; line-height: 1.5; }
.d-none { display: none !important; }

/* ══ Hero header (identique) ══ */
.site-header { position: relative; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4f46e5 75%, #6366f1 100%); padding: 0; overflow: hidden; margin-bottom: 32px; }
.header-bg-shape { position: absolute; border-radius: 50%; background: rgba(255,255,255,.04); pointer-events: none; }
.header-bg-shape:nth-child(1) { width: 400px; height: 400px; top: -150px; right: -80px; }
.header-bg-shape--2 { width: 200px; height: 200px; bottom: -60px; left: 120px; background: rgba(255,255,255,.06); }
.header-content { position: relative; z-index: 1; max-width: 1200px; margin: 0 auto; padding: 40px 40px 36px; display: flex; align-items: flex-end; justify-content: space-between; gap: 32px; flex-wrap: wrap; }
.school-badge { display: inline-flex; align-items: center; gap: 7px; font-size: .72rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: rgba(255,255,255,.6); background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15); padding: .3rem .8rem; border-radius: 99px; margin-bottom: 14px; backdrop-filter: blur(8px); }
.page-title { font-size: 3rem; font-weight: 700; line-height: 1.1; color: #ffffff; letter-spacing: -.03em; margin-bottom: 8px; }
.page-title em { font-style: italic; font-weight: 300; color: rgba(255,255,255,.75); }
.page-subtitle { font-size: .8rem; color: rgba(255,255,255,.5); letter-spacing: .04em; }
.header-chips { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; padding-bottom: 4px; }
.stat-chip { display: flex; align-items: center; gap: 10px; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15); backdrop-filter: blur(10px); border-radius: var(--r-lg); padding: .6rem 1rem; min-width: 100px; transition: background .2s var(--ease), transform .2s var(--ease); }
.stat-chip:hover { background: rgba(255,255,255,.15); transform: translateY(-2px); }
.stat-chip--amber { border-color: rgba(251,191,36,.4); background: rgba(251,191,36,.12); }
.stat-chip-icon { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.stat-chip-icon--blue  { background: rgba(99,102,241,.3); color: #c7d2fe; }
.stat-chip-icon--green { background: rgba(16,185,129,.25); color: #6ee7b7; }
.stat-chip-icon--amber { background: rgba(251,191,36,.25); color: #fde68a; }
.stat-chip-body { display: flex; flex-direction: column; }
.stat-chip-val { font-size: 1.3rem; font-weight: 700; color: #fff; line-height: 1; }
.stat-chip-lbl { font-size: .65rem; font-weight: 500; color: rgba(255,255,255,.55); text-transform: uppercase; letter-spacing: .06em; margin-top: 2px; }

/* ══ Wrapper central ══ */
.nm-page > *:not(.site-header):not(.toast-zone) { max-width: 1200px; margin-left: auto; margin-right: auto; padding-left: 40px; padding-right: 40px; }

/* ══ Bannières ══ */
.banner { display: flex; align-items: flex-start; gap: 12px; padding: 14px 18px; border-radius: var(--r-md); font-size: .83rem; margin-bottom: 12px; border: 1px solid; animation: slideDown .3s var(--ease); }
@keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
.banner--locked { background: var(--c-amber-bg); border-color: var(--c-amber-border); color: #92400e; }
.banner--info   { background: var(--c-blue-bg); border-color: var(--c-blue-border); color: #1e40af; }
.banner-icon { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.banner--locked .banner-icon { background: var(--c-amber-border); }
.banner--info   .banner-icon { background: var(--c-blue-border); }
.banner-body { display: flex; flex-direction: column; gap: 2px; }
.banner-body strong { font-weight: 600; }
.banner-body span   { opacity: .85; }

/* ══ Panneau filtres ══ */
.nm-panel { background: var(--c-surface); border: 1px solid var(--c-border); border-radius: var(--r-xl); overflow: hidden; margin-bottom: 20px; box-shadow: var(--sh-md); }
.nm-panel:hover { box-shadow: var(--sh-lg); }
.panel-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--c-border-2); background: linear-gradient(to right, var(--c-surface-2), var(--c-surface)); }
.panel-head-left { display: flex; align-items: center; gap: 12px; }
.panel-icon { width: 36px; height: 36px; background: var(--c-accent-light); border: 1px solid var(--c-accent-mid); border-radius: var(--r-sm); display: flex; align-items: center; justify-content: center; color: var(--c-accent); flex-shrink: 0; }
.panel-title { font-size: .85rem; font-weight: 700; color: var(--c-ink); }
.panel-subtitle { font-size: .72rem; color: var(--c-ink-3); margin-top: 1px; }
.ctx-badge { display: none; font-size: .72rem; font-weight: 700; padding: .3rem .8rem; border-radius: 99px; background: var(--c-accent-light); border: 1px solid var(--c-accent-mid); color: var(--c-accent); animation: popIn .2s var(--ease-spring); }
@keyframes popIn { from { transform: scale(.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.panel-body { padding: 24px; }

/* ══ Fil d'Ariane ══ */
.nm-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 14px;
    background: var(--c-surface-2);
    border: 1px solid var(--c-border-2);
    border-radius: var(--r-md);
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.bc-item {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 8px;
    color: var(--c-ink-4);
    font-size: .75rem;
    font-weight: 600;
    transition: all .2s;
}
.bc-item--active { color: var(--c-ink-2); background: rgba(79,70,229,.07); }
.bc-item--year { color: var(--c-accent); font-weight: 800; }
.bc-icon { display: flex; align-items: center; }
.bc-sep { color: var(--c-ink-5); font-size: .9rem; }

/* ══ Étapes ══ */
.nm-step {
    border: 1px solid var(--c-border);
    border-radius: var(--r-lg);
    overflow: hidden;
    margin-bottom: 14px;
    transition: opacity .2s, filter .2s;
}
.nm-step--locked {
    opacity: .55;
    pointer-events: none;
    filter: grayscale(.3);
}
.step-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: var(--c-surface-2);
    border-bottom: 1px solid var(--c-border-2);
}
.step-num {
    width: 26px; height: 26px;
    border-radius: 8px;
    background: var(--c-accent-light);
    border: 1px solid var(--c-accent-mid);
    color: var(--c-accent);
    font-weight: 800;
    font-size: .78rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.step-info { flex: 1; }
.step-title { font-weight: 700; font-size: .83rem; }
.step-hint  { font-size: .72rem; color: var(--c-ink-3); margin-top: 1px; }
.step-loader { display: flex; align-items: center; }

.step-options {
    padding: 12px 16px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-height: 54px;
    align-items: center;
}

.step-placeholder {0
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--c-ink-4);
    font-size: .8rem;
    font-style: italic;
}
.step-placeholder--warn { color: var(--c-amber); }

/* Cartes options (filières / promotions / classes) */
.opt-card {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    background: var(--c-surface);
    border: 1.5px solid var(--c-border);
    border-radius: 10px;
    cursor: pointer;
    font-family: var(--font);
    font-size: .83rem;
    font-weight: 600;
    color: var(--c-ink-2);
    transition: all .18s var(--ease);
    white-space: nowrap;
}
.opt-card:hover {
    border-color: var(--c-accent-mid);
    background: var(--c-accent-light);
    color: var(--c-accent);
    transform: translateY(-1px);
    box-shadow: var(--sh-sm);
}
.opt-card--active {
    border-color: var(--c-accent);
    background: var(--c-accent-light);
    color: var(--c-accent);
    box-shadow: 0 0 0 3px rgba(79,70,229,.1);
}
.opt-arrow { opacity: .4; font-size: .9rem; }
.opt-card--active .opt-arrow { opacity: 1; }

/* Spin small */
.nm-spin-sm {
    width: 14px; height: 14px;
    border: 2px solid var(--c-border);
    border-top-color: var(--c-accent);
    border-radius: 50%;
    animation: nm-spin .7s linear infinite;
}
@keyframes nm-spin { to { transform: rotate(360deg); } }

/* ══ Séparateur ══ */
.nm-sep { display: flex; align-items: center; gap: 12px; margin: 20px 0; }
.nm-sep::before, .nm-sep::after { content: ''; flex: 1; height: 1px; background: var(--c-border-2); }
.nm-sep-label { font-size: .68rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--c-ink-4); white-space: nowrap; }

/* ══ Ligne éval ══ */
.filters-row2 { display: grid; grid-template-columns: auto 1fr; gap: 16px; align-items: end; }
.fg { display: flex; flex-direction: column; gap: 8px; }
.fg--wide { flex: 1; }
.nm-page label { display: flex; align-items: center; gap: 6px; font-size: .7rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--c-ink-3); }
.nm-page label svg { color: var(--c-accent); opacity: .7; }

/* Select matière */
.select-wrap { position: relative; width: 100%; }
.select-wrap select { appearance: none; width: 100%; background: var(--c-surface); border: 1.5px solid var(--c-border); border-radius: var(--r-md); padding: .6rem 2.5rem .6rem 1rem; font-family: var(--font); font-size: .88rem; color: var(--c-ink); outline: none; cursor: pointer; transition: border-color .18s var(--ease), box-shadow .18s var(--ease); }
.select-wrap select:focus { border-color: var(--c-accent); box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
.select-wrap select:disabled { opacity: .4; cursor: not-allowed; }
.select-arrow { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--c-ink-3); }

/* Semestre toggle */
.sem-toggle { display: flex; background: var(--c-surface-2); border: 1.5px solid var(--c-border); border-radius: var(--r-md); padding: 3px; gap: 3px; width: fit-content; }
.sem-btn { display: flex; align-items: center; gap: 6px; border: none; background: transparent; padding: .45rem 1.2rem; font-family: var(--font); font-size: .85rem; font-weight: 600; color: var(--c-ink-3); cursor: pointer; border-radius: 8px; transition: all .2s var(--ease); }
.sem-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; opacity: .3; transition: opacity .2s; }
.sem-btn--on { background: var(--c-surface); color: var(--c-accent); box-shadow: var(--sh-sm); }
.sem-btn--on .sem-dot { opacity: 1; background: var(--c-accent); }

/* Coeff pill */
.subject-row { display: flex; gap: 10px; align-items: center; }
.coeff-pill { display: none; align-items: center; gap: 5px; font-size: .72rem; font-weight: 600; padding: .5rem .8rem; border-radius: var(--r-md); border: 1.5px solid var(--c-accent-mid); background: var(--c-accent-light); color: var(--c-accent); white-space: nowrap; flex-shrink: 0; }
.coeff-pill strong { font-weight: 800; font-size: .85rem; }

/* Progression */
.progress-wrap { display: none; flex-direction: column; gap: 8px; margin-top: 20px; padding: 16px; background: var(--c-surface-2); border: 1px solid var(--c-border-2); border-radius: var(--r-md); }
.progress-header { display: flex; justify-content: space-between; align-items: center; }
.progress-label-text { font-size: .72rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--c-ink-3); }
.progress-track { height: 6px; background: var(--c-border); border-radius: 99px; overflow: hidden; }
.progress-fill { height: 100%; background: linear-gradient(90deg, var(--c-accent), var(--c-accent-2)); border-radius: 99px; transition: width .5s var(--ease); width: 0%; }
.progress-txt { font-family: var(--font-mono); font-size: .75rem; font-weight: 600; color: var(--c-accent); }

/* ══ Barre raccourcis ══ */
.shortcuts-bar { display: none; align-items: center; gap: 6px; padding: 10px 16px; margin-bottom: 16px; background: var(--c-surface); border: 1px solid var(--c-border); border-radius: var(--r-lg); box-shadow: var(--sh-xs); flex-wrap: wrap; }
.sc-group { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; width: 100%; }
.sc-divider { width: 1px; height: 14px; background: var(--c-border); flex-shrink: 0; }
.sc-item { display: flex; align-items: center; gap: 4px; font-size: .71rem; color: var(--c-ink-3); white-space: nowrap; }
.nm-page kbd { display: inline-flex; align-items: center; justify-content: center; padding: .1rem .4rem; background: var(--c-surface-2); border: 1px solid var(--c-border); border-bottom-width: 2px; border-radius: 5px; font-family: var(--font-mono); font-size: .65rem; font-weight: 600; color: var(--c-ink-2); line-height: 1.4; }

/* ══ Empty state ══ */
.empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 20px; text-align: center; gap: 16px; background: var(--c-surface); border: 1px solid var(--c-border); border-radius: var(--r-xl); box-shadow: var(--sh-sm); margin-bottom: 24px; }
.empty-illustration { position: relative; width: 80px; height: 80px; }
.empty-rings { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; }
.empty-ring { position: absolute; border-radius: 50%; border: 1.5px solid var(--c-accent-mid); opacity: .25; animation: pulse 2.5s ease-in-out infinite; }
.empty-ring--1 { width: 80px; height: 80px; animation-delay: 0s; }
.empty-ring--2 { width: 60px; height: 60px; animation-delay: .4s; opacity: .35; }
.empty-ring--3 { width: 40px; height: 40px; animation-delay: .8s; opacity: .5; }
@keyframes pulse { 0%,100% { transform: scale(1); } 50% { transform: scale(1.08); } }
.empty-icon { position: relative; z-index: 1; width: 48px; height: 48px; background: var(--c-accent-light); border: 2px solid var(--c-accent-mid); border-radius: var(--r-md); display: flex; align-items: center; justify-content: center; color: var(--c-accent); }
.empty-state h3 { font-size: 1.4rem; font-weight: 700; color: var(--c-ink); }
.empty-state p { font-size: .87rem; color: var(--c-ink-3); line-height: 1.7; max-width: 380px; }
.empty-steps { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
.empty-step { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.empty-step span { font-size: .68rem; font-weight: 600; color: var(--c-ink-4); text-transform: uppercase; letter-spacing: .06em; }
.es-num { width: 28px; height: 28px; border-radius: 8px; background: var(--c-surface-2); border: 1.5px solid var(--c-border); display: flex; align-items: center; justify-content: center; font-size: .78rem; font-weight: 700; color: var(--c-ink-3); }
.es-num--action { background: linear-gradient(135deg, var(--c-accent), var(--c-accent-2)); border-color: transparent; color: white; }
.empty-step--action span { color: var(--c-accent); }
.es-arrow { font-size: .9rem; color: var(--c-ink-5); }

/* ══ Notes card (identique) ══ */
.notes-card { display: none; background: var(--c-surface); border: 1px solid var(--c-border); border-radius: var(--r-xl); overflow: hidden; box-shadow: var(--sh-lg); animation: slideUp .35s var(--ease); }
@keyframes slideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
.notes-card-header { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--c-border-2); background: var(--c-surface-2); }
.card-title-area { display: flex; align-items: center; gap: 14px; }
.card-title-dot-wrap { display: flex; gap: 4px; align-items: center; }
.card-title-dot { width: 10px; height: 10px; background: var(--c-accent); border-radius: 50%; }
.card-title-dot--2 { width: 7px; height: 7px; background: var(--c-accent-mid); }
.card-title-text { display: flex; flex-direction: column; gap: 2px; }
.card-title-label { font-size: .65rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--c-ink-4); }
.card-title-main { font-size: .95rem; font-weight: 700; color: var(--c-ink); }
.card-actions { display: flex; gap: 10px; align-items: center; }
.btn-ghost { display: inline-flex; align-items: center; gap: 6px; height: 36px; padding: 0 14px; background: transparent; border: 1.5px solid var(--c-border); border-radius: var(--r-md); font-family: var(--font); font-size: .82rem; font-weight: 600; color: var(--c-ink-3); cursor: pointer; transition: all .2s var(--ease); }
.btn-ghost:hover { border-color: var(--c-ink-3); color: var(--c-ink); background: var(--c-surface-2); }
.btn-save { display: inline-flex; align-items: center; gap: 7px; height: 36px; padding: 0 18px; background: linear-gradient(135deg, var(--c-accent), var(--c-accent-2)); border: none; border-radius: var(--r-md); font-family: var(--font); font-size: .82rem; font-weight: 600; color: white; cursor: pointer; box-shadow: 0 4px 10px rgba(79,70,229,.3); transition: all .2s var(--ease); position: relative; }
.btn-save:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(79,70,229,.4); }
.btn-save:disabled { opacity: .35; cursor: not-allowed; box-shadow: none; }
.save-badge { position: absolute; top: -7px; right: -7px; min-width: 18px; height: 18px; background: #ef4444; color: white; border-radius: 99px; font-size: .6rem; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; padding: 0 5px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,.15); }

/* Table */
.table-scroll { overflow-x: auto; }
.nm-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
.nm-table thead tr { border-bottom: 2px solid var(--c-border); }
.nm-table th { padding: 12px 10px; text-align: center; font-weight: 700; font-size: .68rem; letter-spacing: .07em; text-transform: uppercase; color: var(--c-ink-3); background: var(--c-surface-2); white-space: nowrap; }
.th-inner { display: flex; flex-direction: column; align-items: center; gap: 6px; }
.th-inner--left { flex-direction: row; justify-content: flex-start; }
.th-interro { background: #fdf4ff !important; color: #7e22ce !important; }
.th-devoir  { background: #fff7ed !important; color: #c2410c !important; }
.th-final   { background: var(--c-accent-light) !important; color: var(--c-accent) !important; }
.th-label { font-size: .78rem; font-weight: 700; }
.th-label--i { color: #7c3aed; }
.th-label--d { color: #ea580c; }
.nm-hdr-inp { width: 58px; height: 28px; border: 1.5px solid var(--c-border); border-radius: var(--r-sm); text-align: center; font-family: var(--font-mono); font-size: .75rem; font-weight: 600; background: var(--c-surface); color: var(--c-ink); outline: none; transition: border-color .15s, box-shadow .15s; }
.nm-hdr-inp:focus { border-color: var(--c-accent); box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
.th-auto-badge { font-size: .55rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--c-ink-4); background: var(--c-border); border-radius: 4px; padding: 1px 6px; }
.nm-table td { padding: 8px 10px; text-align: center; border-bottom: 1px solid var(--c-border-2); vertical-align: middle; transition: background .15s; }
.td-name    { text-align: left; font-weight: 600; }
.td-surname { text-align: left; font-weight: 400; color: var(--c-ink-3); }
.td-num     { font-family: var(--font-mono); font-size: .72rem; color: var(--c-ink-4); width: 40px; }
.td-note    { padding: 5px 6px; }
.td-avg     { background: rgba(243,244,246,.5); }
.td-final   { background: var(--c-accent-light); }
.nm-row:nth-child(even) td { background: rgba(248,249,252,.6); }
.nm-row:hover td { background: #f0f4ff !important; }
.nm-row--mod td  { background: #fffbeb !important; }
.nm-row--saved td { background: #f0fdf4 !important; }
.inp-wrap { display: flex; align-items: center; justify-content: center; position: relative; }
.inp-wrap--lock { opacity: .65; }
.nm-inp { width: 66px; height: 34px; border: 1.5px solid var(--c-border); border-radius: var(--r-md); text-align: center; font-family: var(--font-mono); font-size: .83rem; font-weight: 600; background: var(--c-surface); color: var(--c-ink); outline: none; transition: border-color .15s var(--ease), box-shadow .15s var(--ease); }
.nm-inp:focus { border-color: var(--c-accent); box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
.nm-inp:disabled, .nm-inp[readonly] { background: var(--c-surface-2); color: var(--c-ink-4); cursor: not-allowed; border-color: var(--c-border); font-weight: 400; }
.inp-lock-ico { position: absolute; right: -2px; top: -5px; font-size: .55rem; }
.td-locked { background: var(--c-surface-2) !important; }
.nm-moy, .nm-moy-final { font-family: var(--font-mono); font-size: .83rem; font-weight: 700; padding: .3rem .65rem; border-radius: var(--r-sm); display: inline-block; background: var(--c-surface-2); color: var(--c-ink-3); border: 1px solid var(--c-border); min-width: 52px; text-align: center; }
.nm-moy-final { font-size: .88rem; min-width: 58px; background: var(--c-accent-light); border-color: var(--c-accent-mid); color: var(--c-accent); }
.moy-pass { background: var(--c-green-bg); border-color: var(--c-green-border); color: var(--c-green); }
.moy-fail { background: var(--c-red-bg); border-color: var(--c-red-border); color: var(--c-red); }
.td-status { width: 36px; }
.nm-status { display: inline-flex; width: 10px; height: 10px; border-radius: 50%; background: var(--c-border); }
.st-mod  { background: var(--c-amber); box-shadow: 0 0 0 3px rgba(217,119,6,.2); animation: pulse-dot 1.5s ease infinite; }
.st-saved { background: var(--c-green); }
.st-locked { background: var(--c-ink-4); }
@keyframes pulse-dot { 0%,100% { box-shadow: 0 0 0 2px rgba(217,119,6,.2); } 50% { box-shadow: 0 0 0 5px rgba(217,119,6,.1); } }
.partial-badge { display: inline-flex; align-items: center; justify-content: center; width: 15px; height: 15px; background: var(--c-amber-bg); border: 1px solid var(--c-amber-border); border-radius: 4px; font-size: .62rem; color: var(--c-amber); margin-left: 6px; font-weight: 800; vertical-align: middle; }

/* Stats bar */
.stats-bar { display: flex; align-items: stretch; border-top: 1px solid var(--c-border); background: linear-gradient(to bottom, var(--c-surface-2), var(--c-surface)); }
.stat-item { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; padding: 18px 12px; border-right: 1px solid var(--c-border-2); }
.stat-item:last-child { border-right: none; }
.stat-item--highlight { background: var(--c-accent-light); }
.stat-item-icon { width: 26px; height: 26px; border-radius: 7px; display: flex; align-items: center; justify-content: center; margin-bottom: 2px; }
.stat-item-icon--neutral { background: var(--c-surface-2); border: 1px solid var(--c-border); color: var(--c-ink-3); }
.stat-item-icon--green   { background: var(--c-green-bg); border: 1px solid var(--c-green-border); color: var(--c-green); }
.stat-item-icon--red     { background: var(--c-red-bg); border: 1px solid var(--c-red-border); color: var(--c-red); }
.stat-item-icon--blue    { background: var(--c-blue-bg); border: 1px solid var(--c-blue-border); color: var(--c-blue); }
.stat-val { font-family: -apple-system, sans-serif; font-size: 1.6rem; font-weight: 300; line-height: 1; color: var(--c-ink); }
.stat-val--g { color: var(--c-green); }
.stat-val--r { color: var(--c-red); }
.stat-val--b { color: var(--c-accent); font-weight: 700; font-size: 1.3rem; }
.stat-key { font-size: .62rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--c-ink-4); text-align: center; }

/* ══ Toasts (identiques) ══ */
.toast-zone { position: fixed; bottom: 28px; right: 28px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
.nm-toast { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: var(--r-lg); font-size: .83rem; font-weight: 500; background: var(--c-ink); color: white; box-shadow: var(--sh-xl); pointer-events: all; transform: translateX(calc(100% + 30px)); transition: transform .35s var(--ease-spring); max-width: 340px; min-width: 260px; border: 1px solid rgba(255,255,255,.07); }
.nm-toast.show { transform: translateX(0); }
.toast-icon { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.nm-toast--success { background: #052e16; border-left: 3px solid #22c55e; }
.nm-toast--success .toast-icon { background: rgba(34,197,94,.2); color: #4ade80; }
.nm-toast--error   { background: #1c0a0a; border-left: 3px solid #ef4444; }
.nm-toast--error   .toast-icon { background: rgba(239,68,68,.2); color: #f87171; }
.nm-toast--warning { background: #1c1200; border-left: 3px solid #f59e0b; }
.nm-toast--warning .toast-icon { background: rgba(245,158,11,.2); color: #fbbf24; }
.nm-toast--info    { background: #030f1e; border-left: 3px solid #3b82f6; }
.nm-toast--info    .toast-icon { background: rgba(59,130,246,.2); color: #60a5fa; }
.toast-msg { flex: 1; line-height: 1.4; }
.toast-close { display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: rgba(255,255,255,.08); border: none; border-radius: 6px; color: rgba(255,255,255,.5); cursor: pointer; flex-shrink: 0; padding: 0; }

/* ══ Responsive ══ */
@media (max-width: 900px) {
    .nm-page > *:not(.site-header):not(.toast-zone) { padding-left: 20px; padding-right: 20px; }
    .header-content { padding: 28px 20px 24px; }
    .filters-row2 { grid-template-columns: 1fr; }
    .page-title { font-size: 2.2rem; }
    .stats-bar { flex-wrap: wrap; }
    .stat-item { min-width: 33%; border-bottom: 1px solid var(--c-border-2); }
    .nm-breadcrumb { gap: 4px; }
}
@media (max-width: 600px) {
    .page-title { font-size: 1.8rem; }
    .stat-item { min-width: 50%; }
    .step-options { gap: 6px; }
    .opt-card { font-size: .78rem; padding: 7px 10px; }
    .nm-spin-sm { display: inline-block; }
}
</style>
@endsection
