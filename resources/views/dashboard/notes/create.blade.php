@extends('layouts.template')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">

<div class="nm-root">

    {{-- ── TOPBAR ── --}}
    <header class="nm-topbar">
        <div class="nm-topbar-left">
            <div class="nm-topbar-icon"><i class="fas fa-pen-nib"></i></div>
            <div>
                <h1 class="nm-title">Saisie des Notes</h1>
                <p class="nm-subtitle">Sélectionnez un contexte pour commencer</p>
            </div>
        </div>
        <div class="nm-topbar-right">
            <div class="nm-stat-pill">
                <i class="fas fa-users"></i>
                <span id="student-count">0</span> élève(s)
            </div>
            <div class="nm-stat-pill nm-stat-success">
                <i class="fas fa-check-circle"></i>
                <span id="saved-count">0</span> sauvegardé(s)
            </div>
        </div>
    </header>

    {{-- ── ZONE TOASTS ── --}}
    <div class="nm-toast-area" id="toast-area"></div>

    {{-- ── BANDEAU VERROUILLÉ ── --}}
    <div class="nm-locked-bar d-none" id="locked-banner">
        <i class="fas fa-lock"></i>
        <strong>Notes verrouillées</strong> — Consultation uniquement. Seul le censeur peut modifier.
    </div>

    {{-- ── BANDEAU INFO CHAMPS PARTIELS ── --}}
    <div class="nm-partial-bar d-none" id="partial-banner">
        <i class="fas fa-circle-info"></i>
        <span>Les cases <strong>grisées</strong> <i class="fas fa-lock" style="font-size:.75rem"></i> ont déjà été enregistrées. Vous pouvez remplir les cases vides.</span>
    </div>

    {{-- ── PANNEAU FILTRES ── --}}
    <section class="nm-filters-panel">

        {{-- Ligne 1 : contexte scolaire --}}
        <div class="nm-filters-row">
            <div class="nm-filter-group">
                <label class="nm-label"><i class="fas fa-calendar-alt"></i> Année scolaire</label>
                <select id="year_id" class="nm-select">
                    <option value="">— Choisir —</option>
                    @foreach($years as $year)
                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="nm-filter-sep"><i class="fas fa-chevron-right"></i></div>
            <div class="nm-filter-group">
                <label class="nm-label"><i class="fas fa-layer-group"></i> Filière</label>
                <select id="sector_id" class="nm-select" disabled>
                    <option value="">— Année d'abord —</option>
                </select>
            </div>
            <div class="nm-filter-sep"><i class="fas fa-chevron-right"></i></div>
            <div class="nm-filter-group">
                <label class="nm-label"><i class="fas fa-graduation-cap"></i> Promotion</label>
                <select id="promotion_id" class="nm-select" disabled>
                    <option value="">— Filière d'abord —</option>
                </select>
            </div>
            <div class="nm-filter-sep"><i class="fas fa-chevron-right"></i></div>
            <div class="nm-filter-group">
                <label class="nm-label"><i class="fas fa-door-open"></i> Classe</label>
                <select id="classroom_id" class="nm-select" disabled>
                    <option value="">— Promotion d'abord —</option>
                </select>
            </div>
        </div>

        {{-- Ligne 2 : matière + semestre + action --}}
        <div class="nm-filters-row nm-filters-row--secondary">
            <div class="nm-filter-group nm-filter-group--wide">
                <label class="nm-label"><i class="fas fa-book-open"></i> Matière</label>
                <div class="nm-select-with-badge">
                    <select id="subject_id" class="nm-select" disabled>
                        <option value="">— Classe d'abord —</option>
                    </select>
                    <span class="nm-coeff-badge" id="coeff-badge" style="display:none;">
                        Coeff <strong id="coefficient">—</strong>
                    </span>
                </div>
                <input type="hidden" id="ratio_id">
                <input type="hidden" id="subject_real_id">
            </div>
            <div class="nm-filter-group">
                <label class="nm-label"><i class="fas fa-layer-group"></i> Semestre</label>
                <div class="nm-semester-toggle">
                    <button type="button" class="nm-sem-btn nm-sem-btn--active" data-value="1">
                        <span>S1</span><small>Semestre 1</small>
                    </button>
                    <button type="button" class="nm-sem-btn" data-value="2">
                        <span>S2</span><small>Semestre 2</small>
                    </button>
                </div>
                <input type="hidden" id="semester" value="1">
            </div>
            <div class="nm-filter-group nm-filter-group--action">
                <label class="nm-label nm-label--invisible">Action</label>
                <button id="btn-load" class="nm-btn-load" disabled>
                    <i class="fas fa-bolt"></i>
                    <span id="btn-load-text">Charger les notes</span>
                    <div class="nm-btn-loader" id="btn-load-spinner" style="display:none;">
                        <div class="nm-spinner"></div>
                    </div>
                </button>
            </div>
        </div>

        {{-- Barre de progression --}}
        <div class="nm-progress-track" id="progress-track" style="display:none;">
            <div class="nm-progress-fill" id="progress-fill"></div>
            <span class="nm-progress-label" id="progress-label">0%</span>
        </div>
    </section>

    {{-- ── RACCOURCIS ── --}}
    <div class="nm-shortcuts" id="shortcuts-hint" style="display:none;">
        <span class="nm-shortcut"><kbd>Tab</kbd> / <kbd>↵</kbd> Navigation</span>
        <span class="nm-shortcut"><kbd>Ctrl</kbd> + <kbd>S</kbd> Sauvegarder</span>
        <span class="nm-shortcut"><i class="fas fa-mouse"></i> Double-clic en-tête → moyenne colonne</span>
        <span class="nm-shortcut"><i class="fas fa-hand-point-right"></i> Clic en-tête → remplir colonne</span>
        <span class="nm-shortcut nm-shortcut--lock"><i class="fas fa-lock"></i> Case grisée = déjà enregistrée</span>
    </div>

    {{-- ── ÉTAT VIDE ── --}}
    <div class="nm-empty-state" id="empty-state">
        <div class="nm-empty-icon"><i class="fas fa-table"></i></div>
        <h3>Aucune donnée</h3>
        <p>Sélectionnez une année, filière, promotion, classe et matière,<br>puis cliquez sur <strong>Charger les notes</strong>.</p>
    </div>

    {{-- ── TABLEAU ── --}}
    <div class="nm-table-wrap" id="notes-table-container" style="display:none;">

        <div class="nm-table-toolbar">
            <div class="nm-toolbar-info">
                Classe chargée — <span id="toolbar-subject">—</span>
            </div>
            <div class="nm-toolbar-actions">
                <button class="nm-btn nm-btn--ghost" id="btn-reset">
                    <i class="fas fa-rotate-left"></i> Réinitialiser
                </button>
                <button class="nm-btn nm-btn--primary" id="btn-save" disabled>
                    <i class="fas fa-floppy-disk"></i> Enregistrer
                    <span class="nm-save-count" id="save-pending-count" style="display:none;"></span>
                </button>
            </div>
        </div>

        <div class="nm-table-scroll">
            <table class="nm-table">
                <thead>
                    <tr>
                        <th class="nm-th nm-th--num">#</th>
                        <th class="nm-th nm-th--name">Nom</th>
                        <th class="nm-th nm-th--name">Prénom</th>
                        <th class="nm-th nm-th--note interro-header" data-index="0">
                            <div class="nm-col-header">
                                <span>I1</span>
                                <input type="number" class="nm-header-input" placeholder="—" min="0" max="20" step="0.01" title="Remplir toute la colonne · Double-clic = moyenne">
                            </div>
                        </th>
                        <th class="nm-th nm-th--note interro-header" data-index="1">
                            <div class="nm-col-header">
                                <span>I2</span>
                                <input type="number" class="nm-header-input" placeholder="—" min="0" max="20" step="0.01" title="Remplir toute la colonne · Double-clic = moyenne">
                            </div>
                        </th>
                        <th class="nm-th nm-th--note interro-header" data-index="2">
                            <div class="nm-col-header">
                                <span>I3</span>
                                <input type="number" class="nm-header-input" placeholder="—" min="0" max="20" step="0.01" title="Remplir toute la colonne · Double-clic = moyenne">
                            </div>
                        </th>
                        <th class="nm-th nm-th--avg">
                            <div class="nm-col-header">
                                <span>Moy I</span>
                                <small class="nm-auto-tag">auto</small>
                            </div>
                        </th>
                        <th class="nm-th nm-th--note devoir-header" data-field="devoir1">
                            <div class="nm-col-header">
                                <span>D1</span>
                                <input type="number" class="nm-header-input" placeholder="—" min="0" max="20" step="0.01" title="Remplir toute la colonne · Double-clic = moyenne">
                            </div>
                        </th>
                        <th class="nm-th nm-th--note devoir-header" data-field="devoir2">
                            <div class="nm-col-header">
                                <span>D2</span>
                                <input type="number" class="nm-header-input" placeholder="—" min="0" max="20" step="0.01" title="Remplir toute la colonne · Double-clic = moyenne">
                            </div>
                        </th>
                        <th class="nm-th nm-th--final">
                            <div class="nm-col-header">
                                <span>Moy /20</span>
                                <small class="nm-auto-tag">auto</small>
                            </div>
                        </th>
                        <th class="nm-th nm-th--status"><i class="fas fa-circle-check"></i></th>
                    </tr>
                </thead>
                <tbody id="notes-body"></tbody>
            </table>
        </div>

        {{-- Footer stats temps réel --}}
        <div class="nm-table-footer">
            <div class="nm-footer-stats">
                <div class="nm-footer-stat">
                    <span class="nm-footer-val" id="stat-avg-class">—</span>
                    <span class="nm-footer-key">Moy. classe</span>
                </div>
                <div class="nm-footer-divider"></div>
                <div class="nm-footer-stat">
                    <span class="nm-footer-val nm-footer-val--success" id="stat-pass">—</span>
                    <span class="nm-footer-key">≥ 10</span>
                </div>
                <div class="nm-footer-divider"></div>
                <div class="nm-footer-stat">
                    <span class="nm-footer-val nm-footer-val--danger" id="stat-fail">—</span>
                    <span class="nm-footer-key">< 10</span>
                </div>
                <div class="nm-footer-divider"></div>
                <div class="nm-footer-stat">
                    <span class="nm-footer-val" id="stat-max">—</span>
                    <span class="nm-footer-key">Max</span>
                </div>
                <div class="nm-footer-divider"></div>
                <div class="nm-footer-stat">
                    <span class="nm-footer-val" id="stat-min">—</span>
                    <span class="nm-footer-key">Min</span>
                </div>
            </div>
        </div>
    </div>

</div><!-- /.nm-root -->

@endsection

@section('another_JS')
<script>
class NotesManager {
    constructor() {
        this.currentData      = null;
        this.canEdit          = true;
        this.canModify        = true;
        this.INTERRO_COUNT    = 3;
        this.csrfToken        = '{{ csrf_token() }}';
        this.savedStudents    = new Set();
        this.modifiedStudents = new Set();
        // Pas d'auto-save : seulement sur clic manuel

        this._initElements();
        this._bindFilters();
        this._bindKeyboard();
    }

    // ══════════════════════════════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════════════════════════════

    trunc2(v)  { return Math.floor(v * 100) / 100; }
    sid(id)    { return String(id); }

    esc(str) {
        const d = document.createElement('div');
        d.textContent = str ?? '';
        return d.innerHTML;
    }

    toast(type, message, duration = 4500) {
        const area  = document.getElementById('toast-area');
        if (!area) return;
        const icons = {
            success : 'fa-circle-check',
            error   : 'fa-circle-xmark',
            warning : 'fa-triangle-exclamation',
            info    : 'fa-circle-info',
        };
        const t = document.createElement('div');
        t.className = `nm-toast nm-toast--${type}`;
        t.innerHTML = `
            <i class="fas ${icons[type] ?? icons.info}"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()"><i class="fas fa-xmark"></i></button>`;
        area.appendChild(t);
        requestAnimationFrame(() => t.classList.add('nm-toast--visible'));
        if (duration > 0) {
            setTimeout(() => {
                t.classList.remove('nm-toast--visible');
                setTimeout(() => t.remove(), 400);
            }, duration);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // INITIALISATION
    // ══════════════════════════════════════════════════════════════════

    _initElements() {
        this.el = {
            year          : document.getElementById('year_id'),
            sector        : document.getElementById('sector_id'),
            promotion     : document.getElementById('promotion_id'),
            classroom     : document.getElementById('classroom_id'),
            subject       : document.getElementById('subject_id'),
            coefficient   : document.getElementById('coefficient'),
            coeffBadge    : document.getElementById('coeff-badge'),
            ratioId       : document.getElementById('ratio_id'),
            subjectRealId : document.getElementById('subject_real_id'),
            semester      : document.getElementById('semester'),
            btnLoad       : document.getElementById('btn-load'),
            btnLoadText   : document.getElementById('btn-load-text'),
            btnLoadSpinner: document.getElementById('btn-load-spinner'),
            btnSave       : document.getElementById('btn-save'),
            btnReset      : document.getElementById('btn-reset'),
            container     : document.getElementById('notes-table-container'),
            tbody         : document.getElementById('notes-body'),
            emptyState    : document.getElementById('empty-state'),
            shortcutsHint : document.getElementById('shortcuts-hint'),
            lockedBanner  : document.getElementById('locked-banner'),
            partialBanner : document.getElementById('partial-banner'),
            studentCount  : document.getElementById('student-count'),
            savedCount    : document.getElementById('saved-count'),
            savePending   : document.getElementById('save-pending-count'),
            progressTrack : document.getElementById('progress-track'),
            progressFill  : document.getElementById('progress-fill'),
            progressLabel : document.getElementById('progress-label'),
            toolbarSubject: document.getElementById('toolbar-subject'),
        };
    }

    _bindFilters() {
        this.el.year.addEventListener('change',      () => this._loadSectors());
        this.el.sector.addEventListener('change',    () => this._loadPromotions());
        this.el.promotion.addEventListener('change', () => this._loadClassrooms());
        this.el.classroom.addEventListener('change', () => this._loadSubjects());
        this.el.subject.addEventListener('change',   () => { this._updateCoefficient(); this._updateLoadBtn(); });
        this.el.btnLoad.addEventListener('click',    () => this.loadNotes());
        this.el.btnSave.addEventListener('click',    () => this.saveNotes());
        this.el.btnReset.addEventListener('click',   () => this.resetForm());

        document.querySelectorAll('.nm-sem-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.nm-sem-btn').forEach(b => b.classList.remove('nm-sem-btn--active'));
                btn.classList.add('nm-sem-btn--active');
                this.el.semester.value = btn.dataset.value;
                this._updateLoadBtn();
            });
        });
    }

    _bindKeyboard() {
        document.addEventListener('keydown', e => {
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                this.saveNotes();
                return;
            }
            if ((e.key === 'Tab' || e.key === 'Enter') && !e.shiftKey) {
                const active = document.activeElement;
                if (active?.classList.contains('nm-note-input')) {
                    e.preventDefault();
                    const inputs = [...document.querySelectorAll('.nm-note-input:not(:disabled):not([readonly])')];
                    const idx    = inputs.indexOf(active);
                    if (idx < inputs.length - 1) inputs[idx + 1].focus();
                    else this.el.btnSave.focus();
                }
            }
        });
    }

    // ══════════════════════════════════════════════════════════════════
    // HTTP
    // ══════════════════════════════════════════════════════════════════

    async _get(url) {
        const res = await fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const ct = res.headers.get('content-type') ?? '';
        if (!ct.includes('application/json')) {
            if (res.status === 419) throw new Error('Session expirée — rechargez la page.');
            throw new Error(`Erreur serveur (${res.status}).`);
        }
        if (!res.ok) {
            const b = await res.json().catch(() => ({}));
            throw new Error(b.message ?? `Erreur (${res.status})`);
        }
        return res.json();
    }

    async _post(url, body) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type'     : 'application/json',
                'Accept'           : 'application/json',
                'X-CSRF-TOKEN'     : this.csrfToken,
                'X-Requested-With' : 'XMLHttpRequest',
            },
            body: JSON.stringify(body),
        });
        const ct = res.headers.get('content-type') ?? '';
        if (!ct.includes('application/json')) {
            if (res.status === 419) throw new Error('Session expirée — rechargez la page.');
            throw new Error(`Erreur serveur (${res.status}).`);
        }
        const json = await res.json();
        if (!res.ok && json.message) throw new Error(json.message);
        return json;
    }

    // ══════════════════════════════════════════════════════════════════
    // SELECTS EN CASCADE
    // ══════════════════════════════════════════════════════════════════

    _setSelectLoading(sel) {
        sel.innerHTML = '<option>Chargement…</option>';
        sel.disabled  = true;
    }

    _populateSelect(sel, items, placeholder) {
        sel.innerHTML = `<option value="">${placeholder}</option>`;
        (items ?? []).forEach(item => {
            const o       = document.createElement('option');
            o.value       = item.id;
            o.textContent = item.name ?? item.name_sector ?? item.promotion_sector ?? '—';
            sel.appendChild(o);
        });
        sel.disabled = (items ?? []).length === 0;
    }

    _resetDownstream(from) {
        const chains = {
            year      : ['sector', 'promotion', 'classroom', 'subject'],
            sector    : ['promotion', 'classroom', 'subject'],
            promotion : ['classroom', 'subject'],
            classroom : ['subject'],
        };
        const labels = {
            sector    : '— Filière —',
            promotion : '— Promotion —',
            classroom : '— Classe —',
            subject   : '— Matière —',
        };
        (chains[from] ?? []).forEach(key => {
            if (!this.el[key]) return;
            this.el[key].innerHTML = `<option value="">${labels[key] ?? '—'}</option>`;
            this.el[key].disabled  = true;
        });
        this._updateCoefficient();
        this._clearTable();
        this._updateLoadBtn();
    }

    async _loadSectors() {
        const yearId = this.el.year.value;
        this._resetDownstream('year');
        if (!yearId) return;
        this._setSelectLoading(this.el.sector);
        try {
            const data = await this._get(`/api/sectors-by-year/${yearId}`);
            this._populateSelect(this.el.sector, data, '— Choisir une filière —');
        } catch (e) {
            this.toast('error', e.message);
            this.el.sector.innerHTML = '<option value="">— Erreur —</option>';
        }
    }

    async _loadPromotions() {
        const yearId = this.el.year.value, sectorId = this.el.sector.value;
        this._resetDownstream('sector');
        if (!yearId || !sectorId) return;
        this._setSelectLoading(this.el.promotion);
        try {
            const data = await this._get(`/api/promotions-by-year-sector/${yearId}/${sectorId}`);
            this._populateSelect(this.el.promotion, data, '— Choisir une promotion —');
        } catch (e) {
            this.toast('error', e.message);
            this.el.promotion.innerHTML = '<option value="">— Erreur —</option>';
        }
    }

    async _loadClassrooms() {
        const promotionId = this.el.promotion.value, yearId = this.el.year.value;
        this._resetDownstream('promotion');
        if (!promotionId) return;
        this._setSelectLoading(this.el.classroom);
        try {
            const data = await this._get(`/api/classes-by-promotion/${promotionId}?year_id=${yearId}`);
            this._populateSelect(this.el.classroom, data, '— Choisir une classe —');
        } catch (e) {
            this.toast('error', e.message);
            this.el.classroom.innerHTML = '<option value="">— Erreur —</option>';
        }
    }

    async _loadSubjects() {
        const classroomId = this.el.classroom.value, yearId = this.el.year.value;
        this._resetDownstream('classroom');
        if (!classroomId || !yearId) return;
        this._setSelectLoading(this.el.subject);
        try {
            const data = await this._post('/api/subjects-by-classroom', {
                classroom_id: classroomId,
                year_id     : yearId,
            });
            this.el.subject.innerHTML = '<option value="">— Choisir une matière —</option>';
            if (data.success && data.subjects?.length) {
                data.subjects.forEach(s => {
                    const o               = document.createElement('option');
                    o.value               = s.ratio_id;
                    o.textContent         = `${s.subject_name} (Coeff : ${s.coefficient})`;
                    o.dataset.coefficient = s.coefficient;
                    o.dataset.subjectId   = s.subject_id;
                    this.el.subject.appendChild(o);
                });
                this.el.subject.disabled = false;
            } else {
                this.toast('warning', data.message ?? 'Aucune matière disponible.');
                this.el.subject.innerHTML = '<option value="">— Aucune matière —</option>';
            }
        } catch (e) {
            this.toast('error', e.message);
            this.el.subject.innerHTML = '<option value="">— Erreur —</option>';
        }
        this._updateLoadBtn();
    }

    _updateCoefficient() {
        const opt = this.el.subject.options[this.el.subject.selectedIndex];
        if (opt?.value) {
            this.el.coefficient.textContent  = opt.dataset.coefficient ?? '1';
            this.el.ratioId.value            = opt.value;
            this.el.subjectRealId.value      = opt.dataset.subjectId ?? '';
            this.el.coeffBadge.style.display = 'flex';
        } else {
            this.el.coefficient.textContent  = '—';
            this.el.ratioId.value            = '';
            this.el.subjectRealId.value      = '';
            this.el.coeffBadge.style.display = 'none';
        }
    }

    _updateLoadBtn() {
        this.el.btnLoad.disabled = !(
            this.el.classroom.value &&
            this.el.subject.value   &&
            this.el.semester.value
        );
    }

    // ══════════════════════════════════════════════════════════════════
    // ÉTAT VIDE / CLEAR
    // ══════════════════════════════════════════════════════════════════

    _clearTable() {
        this.currentData = null;
        this.canEdit     = true;
        this.canModify   = true;
        this.savedStudents.clear();
        this.modifiedStudents.clear();

        if (this.el.tbody)         this.el.tbody.innerHTML             = '';
        if (this.el.container)     this.el.container.style.display     = 'none';
        if (this.el.emptyState)    this.el.emptyState.style.display    = 'flex';
        if (this.el.shortcutsHint) this.el.shortcutsHint.style.display = 'none';
        if (this.el.progressTrack) this.el.progressTrack.style.display = 'none';
        if (this.el.lockedBanner)  this.el.lockedBanner.classList.add('d-none');
        if (this.el.partialBanner) this.el.partialBanner.classList.add('d-none');
        if (this.el.btnSave)       this.el.btnSave.disabled            = true;
        if (this.el.studentCount)  this.el.studentCount.textContent    = '0';
        if (this.el.savedCount)    this.el.savedCount.textContent      = '0';
        this._updateSavePendingBadge();
        this._updateFooterStats();
    }

    // ══════════════════════════════════════════════════════════════════
    // CHARGEMENT DES NOTES
    // ══════════════════════════════════════════════════════════════════

    async loadNotes() {
        const classroomId = this.el.classroom.value;
        const yearId      = this.el.year.value;
        const ratioId     = this.el.ratioId.value;
        const semester    = this.el.semester.value;

        if (!classroomId || !yearId || !ratioId || !semester) {
            this.toast('warning', 'Veuillez remplir tous les champs.');
            return;
        }

        this.el.btnLoad.disabled             = true;
        this.el.btnLoadText.textContent      = 'Chargement…';
        this.el.btnLoadSpinner.style.display = 'flex';

        try {
            const data = await this._post('/api/students-with-notes', {
                year_id      : yearId,
                classroom_id : classroomId,
                ratio_id     : ratioId,
                semester,
            });

            if (data.success) {
                this.currentData = data.students;
                this.canEdit     = data.can_edit   ?? true;
                this.canModify   = data.can_modify ?? true;
                this.savedStudents.clear();
                this.modifiedStudents.clear();

                data.is_locked
                    ? this.el.lockedBanner.classList.remove('d-none')
                    : this.el.lockedBanner.classList.add('d-none');

                // Bandeau partiel : si au moins un champ est readonly sans que tout soit bloqué
                const hasPartial = data.students.some(s => {
                    const fr = s.fields_readonly ?? {};
                    const vals = Object.values(fr);
                    return vals.some(v => v) && !vals.every(v => v);
                });
                const hasAnyLocked = data.students.some(s =>
                    Object.values(s.fields_readonly ?? {}).some(v => v)
                );
                if (hasAnyLocked && !data.is_locked) {
                    this.el.partialBanner.classList.remove('d-none');
                } else {
                    this.el.partialBanner.classList.add('d-none');
                }

                const subjectLabel = this.el.subject.options[this.el.subject.selectedIndex]?.text ?? '—';
                if (this.el.toolbarSubject) this.el.toolbarSubject.textContent = subjectLabel;

                this._renderTable();
                this.toast('success', `${data.students.length} élève(s) chargé(s).`);
            } else {
                this.toast('warning', data.message ?? 'Aucun étudiant trouvé.');
            }
        } catch (e) {
            this.toast('error', e.message);
        } finally {
            this.el.btnLoadText.textContent      = 'Charger les notes';
            this.el.btnLoadSpinner.style.display = 'none';
            this._updateLoadBtn();
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // RENDU DU TABLEAU — verrouillage champ par champ
    // ══════════════════════════════════════════════════════════════════

    _renderTable() {
        if (!this.currentData?.length) {
            this._clearTable();
            return;
        }

        this.el.emptyState.style.display    = 'none';
        this.el.container.style.display     = 'block';
        this.el.shortcutsHint.style.display = 'flex';
        this.el.studentCount.textContent    = this.currentData.length;
        this.el.progressTrack.style.display = 'flex';

        const rows = this.currentData.map((s, idx) => {
            const interros      = s.interros ?? [];
            const isDisabled    = s.is_disabled || !this.canEdit;
            const fr            = s.fields_readonly ?? {}; // par champ depuis l'API

            // Cellules interros — chaque case potentiellement indépendante
            let interroCells = '';
            for (let i = 0; i < this.INTERRO_COUNT; i++) {
                const fieldLocked = isDisabled ? false : (fr[`interro_${i}`] ?? false);
                const attr        = isDisabled ? 'disabled' : fieldLocked ? 'readonly' : '';
                const cellCls     = fieldLocked ? 'nm-cell--locked' : '';
                const val         = interros[i] !== undefined && interros[i] !== null
                    ? this.trunc2(parseFloat(interros[i])).toFixed(2) : '';
                const lockTip     = fieldLocked ? ' title="Note enregistrée — non modifiable"' : '';
                interroCells += `
                    <td class="${cellCls}">
                        <div class="nm-input-wrap${fieldLocked ? ' nm-input-wrap--locked' : ''}">
                            <input type="number"
                                   class="nm-note-input"
                                   data-index="${idx}"
                                   data-interro-index="${i}"
                                   data-id="${s.recording_id}"
                                   value="${val}"
                                   min="0" max="20" step="0.01"
                                   placeholder="—"
                                   ${attr}${lockTip}>
                            ${fieldLocked ? '<i class="fas fa-lock nm-field-lock-icon"></i>' : ''}
                        </div>
                    </td>`;
            }

            const moyI = s.moy_interros != null
                ? this.trunc2(parseFloat(s.moy_interros)).toFixed(2) : '—';

            // Cellules devoirs
            let devoirCells = '';
            ['devoir1', 'devoir2'].forEach(f => {
                const fieldLocked = isDisabled ? false : (fr[f] ?? false);
                const attr        = isDisabled ? 'disabled' : fieldLocked ? 'readonly' : '';
                const cellCls     = fieldLocked ? 'nm-cell--locked' : '';
                const val         = s[f] != null ? this.trunc2(parseFloat(s[f])).toFixed(2) : '';
                const lockTip     = fieldLocked ? ' title="Note enregistrée — non modifiable"' : '';
                devoirCells += `
                    <td class="${cellCls}">
                        <div class="nm-input-wrap${fieldLocked ? ' nm-input-wrap--locked' : ''}">
                            <input type="number"
                                   class="nm-note-input"
                                   data-field="${f}"
                                   data-index="${idx}"
                                   data-id="${s.recording_id}"
                                   value="${val}"
                                   min="0" max="20" step="0.01"
                                   placeholder="—"
                                   ${attr}${lockTip}>
                            ${fieldLocked ? '<i class="fas fa-lock nm-field-lock-icon"></i>' : ''}
                        </div>
                    </td>`;
            });

            const moy20    = s.moy_20 != null ? this.trunc2(parseFloat(s.moy_20)) : null;
            const moy20Str = moy20 != null ? moy20.toFixed(2) : '—';
            const moy20Cls = moy20 == null ? '' : moy20 >= 10 ? 'nm-avg--pass' : 'nm-avg--fail';

            // Statut de ligne
            const allLocked    = s.field_readonly ?? false; // tout verrouillé
            const someEditable = !isDisabled && !allLocked;
            const statusCls    = allLocked ? 'nm-status--locked fa-lock' : 'nm-status--pending fa-circle';
            const statusIcon   = `<i class="fas ${statusCls} nm-status-icon" data-index="${idx}"></i>`;
            const rowCls       = allLocked ? 'nm-row nm-row--readonly' : 'nm-row';

            // Indicateur cadenas sur le nom si au moins un champ est readonly
            const hasAnyLocked = Object.values(fr).some(v => v);
            const lockBadge    = hasAnyLocked
                ? `<span class="nm-partial-lock" title="Certaines notes déjà enregistrées"><i class="fas fa-lock-open"></i></span>`
                : '';

            return `
                <tr class="${rowCls}" data-index="${idx}" data-id="${s.recording_id}">
                    <td class="nm-td--num">${idx + 1}</td>
                    <td class="nm-td--name"><span>${this.esc(s.name)}</span>${lockBadge}</td>
                    <td class="nm-td--name">${this.esc(s.surname)}</td>
                    ${interroCells}
                    <td>
                        <span class="nm-avg-badge" data-type="moy-interro" data-index="${idx}">${moyI}</span>
                    </td>
                    ${devoirCells}
                    <td>
                        <span class="nm-avg-final ${moy20Cls}" data-type="moy-20" data-index="${idx}">${moy20Str}</span>
                    </td>
                    <td class="nm-td--status">${statusIcon}</td>
                </tr>`;
        });

        this.el.tbody.innerHTML = rows.join('');

        // Le bouton save est actif s'il existe au moins un champ éditable non désactivé
        const hasEditable = this.canEdit && this.currentData.some(s =>
            !s.is_disabled && !s.field_readonly
        );
        this.el.btnSave.disabled = !hasEditable;

        this._bindInputEvents();
        this._bindHeaderEvents();
        this._updateFooterStats();
        this._updateProgressBar();
    }

    // ══════════════════════════════════════════════════════════════════
    // ÉVÉNEMENTS INPUTS
    // ══════════════════════════════════════════════════════════════════

    _bindInputEvents() {
        this.el.tbody.querySelectorAll('.nm-note-input').forEach(inp => {
            inp.addEventListener('input', () => this._recalculate(inp));
            inp.addEventListener('blur',  () => this._formatInput(inp));
            inp.addEventListener('focus', () => inp.select());
        });
    }

    _bindHeaderEvents() {
        document.querySelectorAll('.nm-header-input').forEach(inp => {
            inp.addEventListener('blur',     () => this._applyHeader(inp));
            inp.addEventListener('dblclick', () => this._fillWithAverage(inp));
            inp.addEventListener('keydown',  e => { if (e.key === 'Enter') { e.preventDefault(); inp.blur(); } });
        });
    }

    _applyHeader(header) {
        const raw = header.value.trim();
        if (!raw) return;
        const value = parseFloat(raw);
        if (isNaN(value) || value < 0 || value > 20) return;

        const th = header.closest('th');
        if (th.classList.contains('interro-header')) {
            const i = th.dataset.index;
            this.el.tbody.querySelectorAll(`.nm-note-input[data-interro-index="${i}"]:not(:disabled):not([readonly])`)
                .forEach(inp => { inp.value = this.trunc2(value).toFixed(2); this._recalculate(inp); });
        } else if (th.classList.contains('devoir-header')) {
            const field = th.dataset.field;
            this.el.tbody.querySelectorAll(`.nm-note-input[data-field="${field}"]:not(:disabled):not([readonly])`)
                .forEach(inp => { inp.value = this.trunc2(value).toFixed(2); this._recalculate(inp); });
        }
        this.toast('info', `Colonne remplie avec ${value.toFixed(2)}`);
    }

    _fillWithAverage(header) {
        const th = header.closest('th');
        let selector = '';
        if (th.classList.contains('interro-header')) {
            selector = `.nm-note-input[data-interro-index="${th.dataset.index}"]:not(:disabled):not([readonly])`;
        } else if (th.classList.contains('devoir-header')) {
            selector = `.nm-note-input[data-field="${th.dataset.field}"]:not(:disabled):not([readonly])`;
        }
        if (!selector) return;

        const vals = [...this.el.tbody.querySelectorAll(selector)]
            .map(i => parseFloat(i.value)).filter(v => !isNaN(v));
        if (!vals.length) return;
        const avg = vals.reduce((a, b) => a + b, 0) / vals.length;
        this.el.tbody.querySelectorAll(selector)
            .forEach(inp => { inp.value = avg.toFixed(2); this._recalculate(inp); });
        this.toast('success', `Colonne remplie avec la moyenne (${avg.toFixed(2)})`);
    }

    // ══════════════════════════════════════════════════════════════════
    // RECALCUL EN TEMPS RÉEL
    // ══════════════════════════════════════════════════════════════════

    _recalculate(changedInput) {
        if (changedInput.hasAttribute('readonly')) return;

        const idx = parseInt(changedInput.dataset.index);
        const row = this.el.tbody.querySelector(`tr[data-index="${idx}"]`);
        if (!row) return;

        const studentId = this.sid(changedInput.dataset.id);
        this.modifiedStudents.add(studentId);
        this.savedStudents.delete(studentId);

        row.classList.remove('nm-row--saved');
        row.classList.add('nm-row--modified');

        const icon = row.querySelector('.nm-status-icon');
        if (icon && !icon.classList.contains('nm-status--locked')) {
            icon.className = 'fas fa-circle-half-stroke nm-status-icon nm-status--modified';
        }

        // Calcul interros : on inclut aussi les champs readonly (valeurs déjà enregistrées)
        const interros = [];
        for (let i = 0; i < this.INTERRO_COUNT; i++) {
            const inp = row.querySelector(`.nm-note-input[data-interro-index="${i}"]`);
            if (inp && inp.value !== '' && !inp.disabled) {
                const v = parseFloat(inp.value);
                if (!isNaN(v)) interros.push(v);
            }
        }

        const getD = f => {
            const inp = row.querySelector(`.nm-note-input[data-field="${f}"]`);
            if (!inp || inp.value === '' || inp.disabled) return null;
            const v = parseFloat(inp.value);
            return isNaN(v) ? null : v;
        };
        const d1 = getD('devoir1');
        const d2 = getD('devoir2');

        const moyI = interros.length
            ? this.trunc2(interros.reduce((a, b) => a + b, 0) / interros.length)
            : null;

        const composantes = [moyI, d1, d2].filter(v => v !== null);
        const moy20 = composantes.length
            ? this.trunc2(composantes.reduce((a, b) => a + b, 0) / composantes.length)
            : null;

        const moyISpan = row.querySelector('[data-type="moy-interro"]');
        if (moyISpan) moyISpan.textContent = moyI !== null ? moyI.toFixed(2) : '—';

        const moy20Span = row.querySelector('[data-type="moy-20"]');
        if (moy20Span) {
            moy20Span.textContent = moy20 !== null ? moy20.toFixed(2) : '—';
            moy20Span.className = 'nm-avg-final' +
                (moy20 === null ? '' : moy20 >= 10 ? ' nm-avg--pass' : ' nm-avg--fail');
        }

        if (this.currentData[idx]) {
            // On ne met à jour que les champs modifiés (pas les readonly)
            const fr = this.currentData[idx].fields_readonly ?? {};
            const newInterros = [];
            for (let i = 0; i < this.INTERRO_COUNT; i++) {
                const inp = row.querySelector(`.nm-note-input[data-interro-index="${i}"]`);
                if (inp && inp.value !== '' && !inp.disabled) {
                    newInterros.push(parseFloat(inp.value));
                }
            }
            Object.assign(this.currentData[idx], {
                interros,
                devoir1     : d1,
                devoir2     : d2,
                moy_interros: moyI,
                moy_20      : moy20,
            });
        }

        // Activer le bouton save dès qu'il y a une modification
        if (this.modifiedStudents.size > 0 && this.canEdit) {
            this.el.btnSave.disabled = false;
        }

        this._updateSavePendingBadge();
        this._updateFooterStats();
        this._updateProgressBar();
    }

    _formatInput(input) {
        if (input.value !== '' && !input.disabled && !input.readOnly) {
            const v = parseFloat(input.value);
            if (!isNaN(v)) {
                input.value = this.trunc2(Math.min(Math.max(v, 0), 20)).toFixed(2);
            }
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // SAUVEGARDE — Manuel uniquement, pas d'auto-save
    // ══════════════════════════════════════════════════════════════════

    async saveNotes() {
        if (!this.canEdit) {
            this.toast('warning', 'Notes verrouillées — modification impossible.');
            return;
        }
        if (!this.currentData?.length || this.modifiedStudents.size === 0) {
            this.toast('info', 'Aucune modification à sauvegarder.');
            return;
        }

        const modifiedData = this.currentData.filter(
            s => this.modifiedStudents.has(this.sid(s.recording_id))
        );
        if (!modifiedData.length) return;

        // On n'envoie que les champs non-readonly (champs que l'enseignant a modifiés)
        const notes = modifiedData.map(s => {
            const fr = s.fields_readonly ?? {};
            const row = this.el.tbody.querySelector(`tr[data-id="${s.recording_id}"]`);

            // Interros : on envoie toutes les valeurs visibles (y compris readonly)
            // Le backend se charge de ne pas écraser les cases déjà en BDD
            const interros = [];
            for (let i = 0; i < this.INTERRO_COUNT; i++) {
                const inp = row?.querySelector(`.nm-note-input[data-interro-index="${i}"]`);
                if (inp && inp.value !== '' && !inp.disabled) {
                    interros.push(parseFloat(inp.value));
                } else {
                    interros.push(null); // placeholder pour garder les index
                }
            }

            const d1inp = row?.querySelector('.nm-note-input[data-field="devoir1"]');
            const d2inp = row?.querySelector('.nm-note-input[data-field="devoir2"]');

            return {
                recording_id : s.recording_id,
                interros     : interros,
                devoir1      : d1inp && d1inp.value !== '' && !d1inp.disabled ? parseFloat(d1inp.value) : null,
                devoir2      : d2inp && d2inp.value !== '' && !d2inp.disabled ? parseFloat(d2inp.value) : null,
            };
        });

        const payload = {
            year_id      : this.el.year.value,
            classroom_id : this.el.classroom.value,
            ratio_id     : this.el.ratioId.value,
            semester     : this.el.semester.value,
            notes,
        };

        const origHTML            = this.el.btnSave.innerHTML;
        this.el.btnSave.innerHTML = '<div class="nm-spinner nm-spinner--sm"></div> Sauvegarde…';
        this.el.btnSave.disabled  = true;

        try {
            const data = await this._post('/api/notes/bulk', payload);

            if (data.success) {
                modifiedData.forEach(s => {
                    const id  = this.sid(s.recording_id);
                    this.savedStudents.add(id);
                    this.modifiedStudents.delete(id);

                    const row = this.el.tbody.querySelector(`tr[data-id="${s.recording_id}"]`);
                    if (row) {
                        row.classList.remove('nm-row--modified');
                        row.classList.add('nm-row--saved');
                        const icon = row.querySelector('.nm-status-icon');
                        if (icon && !icon.classList.contains('nm-status--locked')) {
                            icon.className = 'fas fa-circle-check nm-status-icon nm-status--saved';
                        }
                    }
                });

                if (this.el.savedCount) this.el.savedCount.textContent = this.savedStudents.size;
                this._updateSavePendingBadge();
                this._updateFooterStats();
                this._updateProgressBar();
                this.toast('success', `${modifiedData.length} note(s) enregistrée(s).`);

                // Réinitialiser si tout est sauvegardé
                const allDone = this.currentData.every(s =>
                    this.savedStudents.has(this.sid(s.recording_id)) ||
                    s.is_disabled ||
                    s.field_readonly
                );
                if (allDone) {
                    setTimeout(() => this._resetAfterSave(), 900);
                }
            } else {
                this.toast('warning', data.message ?? 'Échec de la sauvegarde.');
            }
        } catch (e) {
            this.toast('error', e.message);
        } finally {
            this.el.btnSave.innerHTML = origHTML;
            // Réactiver le bouton seulement s'il reste des modifs en attente
            this.el.btnSave.disabled = !this.canEdit || this.modifiedStudents.size === 0;
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // RÉINITIALISATION POST-SAUVEGARDE
    // ══════════════════════════════════════════════════════════════════

    _resetAfterSave() {
        if (this.el.subject) {
            this.el.subject.value = '';
            this._updateCoefficient();
        }
        if (this.el.container)     this.el.container.style.display     = 'none';
        if (this.el.emptyState)    this.el.emptyState.style.display    = 'flex';
        if (this.el.shortcutsHint) this.el.shortcutsHint.style.display = 'none';
        if (this.el.progressTrack) this.el.progressTrack.style.display = 'none';
        if (this.el.partialBanner) this.el.partialBanner.classList.add('d-none');

        document.querySelectorAll('.nm-header-input').forEach(inp => { inp.value = ''; });

        this.currentData = null;
        this.savedStudents.clear();
        this.modifiedStudents.clear();
        if (this.el.studentCount) this.el.studentCount.textContent = '0';
        if (this.el.savedCount)   this.el.savedCount.textContent   = '0';
        this._updateSavePendingBadge();
        this._updateFooterStats();
        this._updateLoadBtn();

        this.toast('info', 'Prêt pour une nouvelle saisie.', 3000);
    }

    // ══════════════════════════════════════════════════════════════════
    // STATS & UI
    // ══════════════════════════════════════════════════════════════════

    _updateProgressBar() {
        const total   = this.currentData?.length ?? 0;
        const percent = total ? Math.round(this.savedStudents.size / total * 100) : 0;
        if (this.el.progressFill)  this.el.progressFill.style.width  = `${percent}%`;
        if (this.el.progressLabel) this.el.progressLabel.textContent = `${percent}%`;
    }

    _updateSavePendingBadge() {
        const n = this.modifiedStudents.size;
        if (!this.el.savePending) return;
        if (n > 0) {
            this.el.savePending.textContent   = n;
            this.el.savePending.style.display = 'inline-flex';
        } else {
            this.el.savePending.style.display = 'none';
        }
    }

    _updateFooterStats() {
        const notes = (this.currentData ?? [])
            .map(s => s.moy_20)
            .filter(v => v != null && !isNaN(v))
            .map(v => parseFloat(v));

        const setEl = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        };

        if (!notes.length) {
            ['stat-avg-class','stat-pass','stat-fail','stat-max','stat-min']
                .forEach(id => setEl(id, '—'));
            return;
        }

        const avg = this.trunc2(notes.reduce((a, b) => a + b, 0) / notes.length);
        setEl('stat-avg-class', avg.toFixed(2));
        setEl('stat-pass',      notes.filter(v => v >= 10).length);
        setEl('stat-fail',      notes.filter(v => v < 10).length);
        setEl('stat-max',       Math.max(...notes).toFixed(2));
        setEl('stat-min',       Math.min(...notes).toFixed(2));
    }

    // ══════════════════════════════════════════════════════════════════
    // RESET FORMULAIRE COMPLET
    // ══════════════════════════════════════════════════════════════════

    resetForm() {
        this.el.year.value     = '';
        this.el.semester.value = '1';
        document.querySelectorAll('.nm-sem-btn').forEach((b, i) =>
            b.classList.toggle('nm-sem-btn--active', i === 0)
        );
        this._resetDownstream('year');
        this.toast('info', 'Formulaire réinitialisé.');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.notesManager = new NotesManager();
});
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&display=swap');

/* ════════════════════════════════════════════════════════════
   TOKENS
════════════════════════════════════════════════════════════ */
:root {
    --c-bg:          #f0f2f5;
    --c-surface:     #ffffff;
    --c-surface-2:   #f7f8fa;
    --c-border:      #e2e6eb;
    --c-border-2:    #d1d8e0;
    --c-text:        #1a1d23;
    --c-text-2:      #5a6072;
    --c-text-3:      #9aa0ae;
    --c-primary:     #3b5bdb;
    --c-primary-h:   #2f4bc0;
    --c-primary-10:  rgba(59,91,219,.10);
    --c-primary-20:  rgba(59,91,219,.20);
    --c-success:     #2f9e44;
    --c-success-bg:  #ebfbee;
    --c-danger:      #c92a2a;
    --c-danger-bg:   #fff5f5;
    --c-warning:     #e67700;
    --c-warning-bg:  #fff9db;
    --c-locked-bg:   #f1f3f9;
    --c-locked-bd:   #c8d0e0;
    --c-modified-bg: #fffbe6;
    --c-saved-bg:    #f0fdf4;
    --f-sans:        'DM Sans', system-ui, sans-serif;
    --f-mono:        'DM Mono', 'Fira Mono', monospace;
    --radius:        10px;
    --radius-lg:     16px;
    --shadow:        0 1px 3px rgba(0,0,0,.08), 0 4px 12px rgba(0,0,0,.04);
    --shadow-md:     0 4px 16px rgba(0,0,0,.10);
    --shadow-lg:     0 8px 32px rgba(0,0,0,.12);
    --transition:    .18s cubic-bezier(.4,0,.2,1);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ════════════════════════════════════════════════════════════
   ROOT
════════════════════════════════════════════════════════════ */
.nm-root {
    font-family: var(--f-sans);
    background: var(--c-bg);
    min-height: 100vh;
    color: var(--c-text);
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* ════════════════════════════════════════════════════════════
   TOPBAR
════════════════════════════════════════════════════════════ */
.nm-topbar {
    display: flex; align-items: center; justify-content: space-between;
    background: var(--c-surface); border: 1px solid var(--c-border);
    border-radius: var(--radius-lg); padding: 1rem 1.5rem;
    box-shadow: var(--shadow); flex-wrap: wrap; gap: .75rem;
}
.nm-topbar-left { display: flex; align-items: center; gap: 1rem; }
.nm-topbar-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: var(--c-primary); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.nm-title    { font-size: 1.2rem; font-weight: 700; color: var(--c-text); }
.nm-subtitle { font-size: .82rem; color: var(--c-text-3); margin-top: 1px; }
.nm-topbar-right { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
.nm-stat-pill {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .35rem .75rem;
    background: var(--c-surface-2); border: 1px solid var(--c-border);
    border-radius: 99px; font-size: .82rem; font-weight: 500; color: var(--c-text-2);
}
.nm-stat-pill i { font-size: .75rem; }
.nm-stat-success { background: var(--c-success-bg); border-color: #b2f2bb; color: var(--c-success); }

/* ════════════════════════════════════════════════════════════
   TOASTS
════════════════════════════════════════════════════════════ */
.nm-toast-area {
    position: fixed; top: 1.25rem; right: 1.25rem; z-index: 9999;
    display: flex; flex-direction: column; gap: .5rem; pointer-events: none;
}
.nm-toast {
    display: flex; align-items: center; gap: .75rem;
    background: var(--c-surface); border-radius: var(--radius);
    border-left: 4px solid var(--c-border);
    padding: .75rem 1rem; box-shadow: var(--shadow-lg);
    min-width: 280px; max-width: 380px;
    font-size: .875rem; font-weight: 500;
    pointer-events: all;
    opacity: 0; transform: translateX(24px);
    transition: opacity .3s, transform .3s;
}
.nm-toast--visible   { opacity: 1; transform: translateX(0); }
.nm-toast--success   { border-color: var(--c-success); }
.nm-toast--success i { color: var(--c-success); }
.nm-toast--error     { border-color: var(--c-danger); }
.nm-toast--error   i { color: var(--c-danger); }
.nm-toast--warning   { border-color: var(--c-warning); }
.nm-toast--warning i { color: var(--c-warning); }
.nm-toast--info      { border-color: var(--c-primary); }
.nm-toast--info    i { color: var(--c-primary); }
.nm-toast span       { flex: 1; color: var(--c-text); }
.nm-toast button     { background: none; border: none; cursor: pointer; color: var(--c-text-3); padding: 0; font-size: 1rem; }

/* ════════════════════════════════════════════════════════════
   BANDEAUX
════════════════════════════════════════════════════════════ */
.nm-locked-bar {
    display: flex; align-items: center; gap: .5rem;
    background: var(--c-warning-bg); border: 1px solid #ffe066;
    border-radius: var(--radius); padding: .75rem 1.25rem;
    color: var(--c-warning); font-size: .875rem;
}
.nm-partial-bar {
    display: flex; align-items: center; gap: .5rem;
    background: #e7f5ff; border: 1px solid #74c0fc;
    border-radius: var(--radius); padding: .65rem 1.25rem;
    color: #1971c2; font-size: .85rem;
}
.nm-partial-bar i { color: #1971c2; }

/* ════════════════════════════════════════════════════════════
   FILTRES
════════════════════════════════════════════════════════════ */
.nm-filters-panel {
    background: var(--c-surface); border: 1px solid var(--c-border);
    border-radius: var(--radius-lg); padding: 1.5rem;
    box-shadow: var(--shadow); display: flex; flex-direction: column; gap: 1.25rem;
}
.nm-filters-row { display: flex; align-items: flex-end; gap: .5rem; flex-wrap: wrap; }
.nm-filters-row--secondary { padding-top: 1rem; border-top: 1px solid var(--c-border); }
.nm-filter-group { display: flex; flex-direction: column; gap: .4rem; flex: 1; min-width: 140px; }
.nm-filter-group--wide { flex: 2; min-width: 220px; }
.nm-filter-group--action { flex: 0 0 auto; }
.nm-filter-sep { color: var(--c-text-3); font-size: .75rem; padding-bottom: .5rem; flex-shrink: 0; align-self: flex-end; }
.nm-label {
    font-size: .78rem; font-weight: 600; color: var(--c-text-2);
    letter-spacing: .3px; text-transform: uppercase;
    display: flex; align-items: center; gap: .3rem; white-space: nowrap;
}
.nm-label i { color: var(--c-primary); font-size: .7rem; }
.nm-label--invisible { opacity: 0; pointer-events: none; }
.nm-select {
    font-family: var(--f-sans); font-size: .875rem; height: 42px;
    padding: 0 2rem 0 .875rem;
    background: var(--c-surface-2) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%239aa0ae' d='M8 10.94L2.53 5.47l1.06-1.06L8 8.82l4.41-4.41 1.06 1.06z'/%3E%3C/svg%3E") no-repeat right .6rem center / 14px;
    border: 1px solid var(--c-border); border-radius: var(--radius);
    color: var(--c-text); cursor: pointer; width: 100%; appearance: none;
    transition: border-color var(--transition), box-shadow var(--transition);
}
.nm-select:focus { outline: none; border-color: var(--c-primary); box-shadow: 0 0 0 3px var(--c-primary-10); background-color: var(--c-surface); }
.nm-select:disabled { opacity: .5; cursor: not-allowed; }
.nm-select:not(:disabled):hover { border-color: var(--c-primary); }
.nm-select-with-badge { position: relative; }
.nm-coeff-badge {
    position: absolute; right: 2.25rem; top: 50%; transform: translateY(-50%);
    background: var(--c-primary-10); color: var(--c-primary);
    border: 1px solid var(--c-primary-20); border-radius: 6px;
    padding: 2px 8px; font-size: .72rem; font-weight: 600;
    display: flex; align-items: center; gap: 3px; pointer-events: none;
}
.nm-semester-toggle {
    display: flex; border: 1px solid var(--c-border); border-radius: var(--radius);
    overflow: hidden; background: var(--c-surface-2); height: 42px;
}
.nm-sem-btn {
    flex: 1; border: none; background: none; cursor: pointer;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    font-family: var(--f-sans); padding: 0 1rem; line-height: 1;
    transition: background var(--transition);
}
.nm-sem-btn span  { font-size: .9rem; font-weight: 700; }
.nm-sem-btn small { font-size: .65rem; color: var(--c-text-3); margin-top: 2px; }
.nm-sem-btn--active { background: var(--c-primary); color: #fff; }
.nm-sem-btn--active small { color: rgba(255,255,255,.7); }
.nm-sem-btn:not(.nm-sem-btn--active):hover { background: var(--c-primary-10); }
.nm-btn-load {
    font-family: var(--f-sans); height: 42px; padding: 0 1.5rem;
    background: var(--c-primary); color: #fff; border: none;
    border-radius: var(--radius); font-weight: 600; font-size: .9rem;
    cursor: pointer; display: flex; align-items: center; gap: .5rem;
    transition: background var(--transition), transform var(--transition), box-shadow var(--transition);
    white-space: nowrap;
}
.nm-btn-load:hover:not(:disabled) { background: var(--c-primary-h); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59,91,219,.35); }
.nm-btn-load:disabled { opacity: .5; cursor: not-allowed; }
.nm-progress-track {
    height: 6px; background: var(--c-border); border-radius: 99px;
    overflow: hidden; position: relative; align-items: center;
}
.nm-progress-fill {
    height: 100%; width: 0; background: linear-gradient(90deg, var(--c-primary), #74c0fc);
    border-radius: 99px; transition: width .4s cubic-bezier(.4,0,.2,1);
}
.nm-progress-label {
    position: absolute; right: 0; top: -18px;
    font-size: .72rem; font-weight: 600; color: var(--c-text-2); font-family: var(--f-mono);
}

/* ════════════════════════════════════════════════════════════
   RACCOURCIS
════════════════════════════════════════════════════════════ */
.nm-shortcuts {
    display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;
    padding: .6rem 1rem; background: var(--c-surface-2);
    border: 1px solid var(--c-border); border-radius: var(--radius);
    font-size: .78rem; color: var(--c-text-3);
}
.nm-shortcut { display: flex; align-items: center; gap: .3rem; }
.nm-shortcut kbd {
    background: var(--c-surface); border: 1px solid var(--c-border);
    border-bottom-width: 2px; border-radius: 5px;
    padding: 1px 5px; font-size: .72rem; font-family: var(--f-mono); color: var(--c-text-2);
}
.nm-shortcut--lock { color: #1971c2; }
.nm-shortcut--lock i { font-size: .72rem; }

/* ════════════════════════════════════════════════════════════
   ÉTAT VIDE
════════════════════════════════════════════════════════════ */
.nm-empty-state {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: .75rem; padding: 4rem 2rem;
    background: var(--c-surface); border: 1px dashed var(--c-border-2);
    border-radius: var(--radius-lg); color: var(--c-text-3); text-align: center;
}
.nm-empty-icon {
    width: 64px; height: 64px; border-radius: 16px;
    background: var(--c-surface-2); border: 1px solid var(--c-border);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; color: var(--c-primary);
}
.nm-empty-state h3 { font-size: 1.1rem; font-weight: 600; color: var(--c-text-2); }
.nm-empty-state p  { font-size: .875rem; line-height: 1.6; max-width: 380px; }

/* ════════════════════════════════════════════════════════════
   TABLEAU
════════════════════════════════════════════════════════════ */
.nm-table-wrap {
    background: var(--c-surface); border: 1px solid var(--c-border);
    border-radius: var(--radius-lg); box-shadow: var(--shadow); overflow: hidden;
}
.nm-table-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: .875rem 1.25rem; border-bottom: 1px solid var(--c-border);
    gap: .75rem; flex-wrap: wrap;
}
.nm-toolbar-info { font-size: .85rem; color: var(--c-text-2); font-weight: 500; }
.nm-toolbar-info span { color: var(--c-primary); font-weight: 600; }
.nm-toolbar-actions { display: flex; gap: .5rem; }
.nm-btn {
    font-family: var(--f-sans); font-size: .85rem; font-weight: 600;
    height: 36px; padding: 0 1rem; border-radius: 8px; cursor: pointer;
    display: inline-flex; align-items: center; gap: .4rem;
    transition: background var(--transition), transform var(--transition);
    border: 1px solid transparent; white-space: nowrap;
}
.nm-btn--ghost { background: var(--c-surface-2); color: var(--c-text-2); border-color: var(--c-border); }
.nm-btn--ghost:hover { background: var(--c-border); }
.nm-btn--primary { background: var(--c-primary); color: #fff; }
.nm-btn--primary:hover:not(:disabled) { background: var(--c-primary-h); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59,91,219,.3); }
.nm-btn--primary:disabled { opacity: .45; cursor: not-allowed; }
.nm-save-count {
    background: rgba(255,255,255,.3); border-radius: 99px;
    min-width: 20px; height: 20px; font-size: .72rem; font-weight: 700;
    display: inline-flex; align-items: center; justify-content: center; padding: 0 5px;
}
.nm-table-scroll { overflow-x: auto; }
.nm-table { width: 100%; border-collapse: collapse; font-size: .855rem; font-family: var(--f-sans); }
.nm-th {
    padding: .75rem .5rem; background: var(--c-surface-2);
    border-bottom: 2px solid var(--c-border);
    font-weight: 600; font-size: .775rem; letter-spacing: .2px;
    color: var(--c-text-2); text-align: center; white-space: nowrap; user-select: none;
}
.nm-th--num    { width: 40px; }
.nm-th--name   { text-align: left; min-width: 110px; padding-left: 1rem; }
.nm-th--note   { width: 80px; }
.nm-th--avg    { width: 80px; background: #edf2ff; color: var(--c-primary); }
.nm-th--final  { width: 90px; background: #ebfbee; color: var(--c-success); }
.nm-th--status { width: 40px; }
.interro-header, .devoir-header { cursor: pointer; }
.interro-header:hover, .devoir-header:hover { background: var(--c-primary-10); }
.nm-col-header { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.nm-col-header > span { font-weight: 700; font-size: .82rem; }
.nm-header-input {
    font-family: var(--f-mono); font-size: .78rem; font-weight: 500;
    width: 58px; height: 28px; padding: 0 6px; text-align: center;
    border: 1px solid var(--c-border-2); border-radius: 6px;
    background: var(--c-surface); color: var(--c-text);
    transition: border-color var(--transition), box-shadow var(--transition);
}
.nm-header-input:focus { outline: none; border-color: var(--c-primary); box-shadow: 0 0 0 2px var(--c-primary-10); }
.nm-auto-tag {
    font-size: .65rem; font-weight: 500; background: rgba(0,0,0,.06);
    border-radius: 4px; padding: 1px 5px; color: inherit;
    text-transform: uppercase; letter-spacing: .3px;
}

/* Lignes */
.nm-row td {
    padding: .45rem .5rem; border-bottom: 1px solid var(--c-border);
    vertical-align: middle; text-align: center; transition: background var(--transition);
}
.nm-row:last-child td { border-bottom: none; }
.nm-row:hover td      { background: rgba(59,91,219,.03); }
.nm-row--modified td  { background: var(--c-modified-bg) !important; }
.nm-row--saved td     { background: var(--c-saved-bg) !important; }
.nm-row--readonly td  { background: var(--c-locked-bg); }
.nm-td--num  { color: var(--c-text-3); font-family: var(--f-mono); font-size: .8rem; }
.nm-td--name { text-align: left; padding-left: 1rem; font-weight: 500; white-space: nowrap; }

/* Cellules verrouillées individuellement */
.nm-cell--locked { background: var(--c-locked-bg) !important; }

/* Wrapper input + icône cadenas */
.nm-input-wrap { position: relative; display: inline-flex; align-items: center; }
.nm-input-wrap--locked .nm-note-input { padding-right: 20px; }
.nm-field-lock-icon {
    position: absolute; right: 6px;
    font-size: .6rem; color: var(--c-text-3); pointer-events: none;
}

/* Badge cadenas partiel sur le nom */
.nm-partial-lock {
    display: inline-flex; align-items: center; justify-content: center;
    width: 16px; height: 16px; margin-left: .35rem;
    background: #e7f5ff; border-radius: 4px;
    color: #1971c2; font-size: .58rem; vertical-align: middle;
    cursor: help;
}

/* Inputs notes */
.nm-note-input {
    font-family: var(--f-mono); font-size: .855rem; font-weight: 500;
    width: 64px; height: 32px; text-align: center; padding: 0 4px;
    border: 1px solid transparent; border-radius: 6px;
    background: var(--c-surface-2); color: var(--c-text);
    transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
    display: block; margin: 0 auto;
}
.nm-note-input:hover:not(:disabled):not([readonly]) { border-color: var(--c-border-2); background: var(--c-surface); }
.nm-note-input:focus { outline: none; border-color: var(--c-primary); box-shadow: 0 0 0 3px var(--c-primary-10); background: var(--c-surface); }
.nm-note-input:disabled  { background: transparent; opacity: .4; cursor: not-allowed; }
.nm-note-input[readonly] {
    background: var(--c-locked-bg); color: var(--c-text-3);
    cursor: not-allowed; border-color: var(--c-locked-bd);
    border-style: dashed;
}

/* Badges moyennes */
.nm-avg-badge { display: inline-block; font-family: var(--f-mono); font-weight: 600; font-size: .855rem; color: var(--c-primary); min-width: 48px; text-align: center; }
.nm-avg-final {
    display: inline-flex; align-items: center; justify-content: center;
    font-family: var(--f-mono); font-weight: 700; font-size: .9rem;
    padding: 3px 10px; border-radius: 6px; min-width: 52px;
    background: var(--c-surface-2); color: var(--c-text-2);
    transition: background var(--transition), color var(--transition);
}
.nm-avg--pass { background: var(--c-success-bg); color: var(--c-success); }
.nm-avg--fail { background: var(--c-danger-bg);  color: var(--c-danger);  }

/* Icônes statut */
.nm-status-icon      { font-size: .85rem; transition: color var(--transition); }
.nm-status--pending  { color: var(--c-text-3); font-size: .55rem; }
.nm-status--modified { color: var(--c-warning); }
.nm-status--saved    { color: var(--c-success); font-size: .95rem; }
.nm-status--locked   { color: var(--c-text-3); }

/* Footer stats */
.nm-table-footer  { border-top: 1px solid var(--c-border); padding: .75rem 1.25rem; }
.nm-footer-stats  { display: flex; align-items: center; flex-wrap: wrap; }
.nm-footer-stat   { display: flex; flex-direction: column; align-items: center; padding: .3rem 1.25rem; }
.nm-footer-divider{ width: 1px; height: 28px; background: var(--c-border); flex-shrink: 0; }
.nm-footer-val    { font-family: var(--f-mono); font-weight: 700; font-size: 1rem; color: var(--c-text); }
.nm-footer-val--success { color: var(--c-success); }
.nm-footer-val--danger  { color: var(--c-danger);  }
.nm-footer-key    { font-size: .72rem; color: var(--c-text-3); font-weight: 500; text-transform: uppercase; letter-spacing: .3px; margin-top: 1px; }

/* ════════════════════════════════════════════════════════════
   SPINNER
════════════════════════════════════════════════════════════ */
@keyframes nm-spin { to { transform: rotate(360deg); } }
.nm-spinner {
    width: 20px; height: 20px; border-radius: 50%;
    border: 2px solid rgba(255,255,255,.3); border-top-color: #fff;
    animation: nm-spin .7s linear infinite; flex-shrink: 0;
}
.nm-spinner--sm { width: 14px; height: 14px; }

/* ════════════════════════════════════════════════════════════
   RESPONSIVE
════════════════════════════════════════════════════════════ */
@media (max-width: 768px) {
    .nm-root            { padding: .75rem; gap: .75rem; }
    .nm-topbar          { padding: .875rem 1rem; }
    .nm-filter-sep      { display: none; }
    .nm-filter-group    { min-width: 100%; flex: 1 1 100%; }
    .nm-filter-group--wide { min-width: 100%; }
    .nm-filter-group--action { width: 100%; }
    .nm-btn-load        { width: 100%; justify-content: center; }
    .nm-footer-stats    { justify-content: center; }
    .nm-footer-stat     { padding: .3rem .75rem; }
}

::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--c-border-2); border-radius: 99px; }
::-webkit-scrollbar-thumb:hover { background: var(--c-text-3); }
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; }
input[type=number] { -moz-appearance: textfield; }
</style>
@endsection
