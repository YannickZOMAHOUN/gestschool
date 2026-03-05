@extends('layouts.template')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

<div class="nm-root">

    {{-- ── TOPBAR ── --}}
    <header class="nm-topbar">
        <div class="nm-topbar-left">
            <div class="nm-topbar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            </div>
            <div>
                <h1 class="nm-title">Saisie des Notes</h1>
                <p class="nm-subtitle">Lycée Technique de Bohicon — Système de gestion des évaluations</p>
            </div>
        </div>
        <div class="nm-topbar-right">
            <div class="nm-chip">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/></svg>
                <span id="student-count">0</span> élève(s)
            </div>
            <div class="nm-chip nm-chip--success">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span id="saved-count">0</span> sauvegardé(s)
            </div>
            <div class="nm-chip nm-chip--warning" id="pending-chip" style="display:none;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span id="saved-pending-text">0</span> non sauvegardé(s)
            </div>
        </div>
    </header>

    {{-- ── TOASTS ── --}}
    <div class="nm-toast-zone" id="toast-area"></div>

    {{-- ── BANNIÈRES ── --}}
    <div class="nm-banner nm-banner--locked d-none" id="locked-banner">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <span><strong>Notes verrouillées</strong> — Consultation uniquement. Seul le censeur peut modifier.</span>
    </div>
    <div class="nm-banner nm-banner--info d-none" id="partial-banner">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>Les cases <strong>grisées avec cadenas</strong> ont déjà été enregistrées. Vous pouvez remplir les cases restantes.</span>
    </div>

    {{-- ── PANNEAU DE FILTRES ── --}}
    <section class="nm-panel">
        <div class="nm-panel-header">
            <div class="nm-panel-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Contexte de saisie
            </div>
            <div class="nm-step-track">
                <div class="nm-step" id="step-1" data-active="true">
                    <div class="nm-step-dot">1</div>
                    <span>Année</span>
                </div>
                <div class="nm-step-line"></div>
                <div class="nm-step" id="step-2">
                    <div class="nm-step-dot">2</div>
                    <span>Classe</span>
                </div>
                <div class="nm-step-line"></div>
                <div class="nm-step" id="step-3">
                    <div class="nm-step-dot">3</div>
                    <span>Matière</span>
                </div>
                <div class="nm-step-line"></div>
                <div class="nm-step" id="step-4">
                    <div class="nm-step-dot">4</div>
                    <span>Saisie</span>
                </div>
            </div>
        </div>

        <div class="nm-filters-grid">
            {{-- Colonne 1 : Année --}}
            <div class="nm-fg">
                <label class="nm-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Année scolaire
                </label>
                <select id="year_id" class="nm-select">
                    <option value="">— Choisir —</option>
                    @foreach($years as $year)
                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Colonne 2 : Filière --}}
            <div class="nm-fg">
                <label class="nm-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="3" width="6" height="18"/><rect x="9" y="8" width="6" height="13"/><rect x="16" y="5" width="6" height="16"/></svg>
                    Filière
                </label>
                <select id="sector_id" class="nm-select" disabled>
                    <option value="">— Année d'abord —</option>
                </select>
            </div>

            {{-- Colonne 3 : Promotion --}}
            <div class="nm-fg">
                <label class="nm-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    Promotion
                </label>
                <select id="promotion_id" class="nm-select" disabled>
                    <option value="">— Filière d'abord —</option>
                </select>
            </div>

            {{-- Colonne 4 : Classe --}}
            <div class="nm-fg">
                <label class="nm-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Classe
                </label>
                <select id="classroom_id" class="nm-select" disabled>
                    <option value="">— Promotion d'abord —</option>
                </select>
            </div>
        </div>

        <div class="nm-divider"></div>

        <div class="nm-filters-row2">
            {{-- Semestre --}}
            <div class="nm-fg nm-fg--sm">
                <label class="nm-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M2 12h20"/></svg>
                    Semestre
                </label>
                <div class="nm-sem-toggle">
                    <button type="button" class="nm-sem-btn nm-sem-btn--on" data-value="1">S1</button>
                    <button type="button" class="nm-sem-btn" data-value="2">S2</button>
                </div>
                <input type="hidden" id="semester" value="1">
            </div>

            {{-- Matière --}}
            <div class="nm-fg nm-fg--wide">
                <label class="nm-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    Matière
                </label>
                <div style="display:flex; gap:8px; align-items:center;">
                    <select id="subject_id" class="nm-select" disabled style="flex:1">
                        <option value="">— Classe d'abord —</option>
                    </select>
                    <div class="nm-coeff-pill" id="coeff-pill" style="display:none;">
                        Coeff <strong id="coefficient">—</strong>
                    </div>
                </div>
                <input type="hidden" id="ratio_id">
                <input type="hidden" id="subject_real_id">
            </div>

            {{-- Bouton charger --}}
            <div class="nm-fg nm-fg--auto">
                <label class="nm-label nm-label--ghost">Action</label>
                <button id="btn-load" class="nm-btn-primary" disabled>
                    <span class="btn-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    </span>
                    <span id="btn-load-text">Charger les notes</span>
                    <div class="nm-spin" id="btn-load-spin" style="display:none;"></div>
                </button>
            </div>
        </div>

        {{-- Barre de progression --}}
        <div class="nm-progress-wrap" id="progress-wrap" style="display:none;">
            <div class="nm-progress-bar">
                <div class="nm-progress-fill" id="progress-fill"></div>
            </div>
            <span class="nm-progress-txt" id="progress-label">0%</span>
        </div>
    </section>

    {{-- ── RACCOURCIS ── --}}
    <div class="nm-shortcuts-bar" id="shortcuts-bar" style="display:none;">
        <span class="sc-item"><kbd>Tab</kbd><kbd>↵</kbd> Navigation</span>
        <span class="sc-item"><kbd>Ctrl</kbd><kbd>S</kbd> Sauvegarder</span>
        <span class="sc-item sc-item--blue">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Case grisée = déjà enregistrée
        </span>
        <span class="sc-item">Clic en-tête → remplir colonne · Double-clic → moyenne colonne</span>
    </div>

    {{-- ── ÉTAT VIDE ── --}}
    <div class="nm-empty" id="empty-state">
        <div class="nm-empty-visual">
            <div class="nm-empty-rings">
                <div class="nm-ring nm-ring-1"></div>
                <div class="nm-ring nm-ring-2"></div>
                <div class="nm-ring nm-ring-3"></div>
            </div>
            <div class="nm-empty-icon-center">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="9" x2="9" y2="21"/><line x1="15" y1="9" x2="15" y2="21"/></svg>
            </div>
        </div>
        <h3>Aucune donnée chargée</h3>
        <p>Sélectionnez une année, une filière, une promotion, une classe et une matière,<br>puis cliquez sur <strong>Charger les notes</strong>.</p>
    </div>

    {{-- ── TABLEAU PRINCIPAL ── --}}
    <div class="nm-card" id="notes-table-container" style="display:none;">

        <div class="nm-card-header">
            <div class="nm-card-title" id="toolbar-subject">—</div>
            <div style="display:flex; gap:8px; align-items:center;">
                <button class="nm-btn-ghost" id="btn-reset">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.85"/></svg>
                    Réinitialiser
                </button>
                <button class="nm-btn-save" id="btn-save" disabled>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Enregistrer
                    <span class="nm-save-badge" id="save-pending-badge" style="display:none;"></span>
                </button>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="nm-table">
                <thead>
                    <tr>
                        <th class="th-num">#</th>
                        <th class="th-name" colspan="2">Élève</th>
                        <th class="th-note interro-header" data-index="0">
                            <div class="th-inner">I1<input type="number" class="nm-hdr-inp" placeholder="—" min="0" max="20" step="0.01" title="Entrer une valeur → remplir · Double-clic → moyenne"></div>
                        </th>
                        <th class="th-note interro-header" data-index="1">
                            <div class="th-inner">I2<input type="number" class="nm-hdr-inp" placeholder="—" min="0" max="20" step="0.01"></div>
                        </th>
                        <th class="th-note interro-header" data-index="2">
                            <div class="th-inner">I3<input type="number" class="nm-hdr-inp" placeholder="—" min="0" max="20" step="0.01"></div>
                        </th>
                        <th class="th-avg">
                            <div class="th-inner">Moy I<span class="th-auto">auto</span></div>
                        </th>
                        <th class="th-note devoir-header" data-field="devoir1">
                            <div class="th-inner">D1<input type="number" class="nm-hdr-inp" placeholder="—" min="0" max="20" step="0.01"></div>
                        </th>
                        <th class="th-note devoir-header" data-field="devoir2">
                            <div class="th-inner">D2<input type="number" class="nm-hdr-inp" placeholder="—" min="0" max="20" step="0.01"></div>
                        </th>
                        <th class="th-final">
                            <div class="th-inner">Moy /20<span class="th-auto">auto</span></div>
                        </th>
                        <th class="th-status">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        </th>
                    </tr>
                </thead>
                <tbody id="notes-body"></tbody>
            </table>
        </div>

        {{-- Stats footer --}}
        <div class="nm-stats-bar">
            <div class="nm-stat">
                <span class="nm-stat-val" id="stat-avg">—</span>
                <span class="nm-stat-key">Moy. classe</span>
            </div>
            <div class="nm-stat-sep"></div>
            <div class="nm-stat">
                <span class="nm-stat-val nm-stat-val--g" id="stat-pass">—</span>
                <span class="nm-stat-key">≥ 10 (admis)</span>
            </div>
            <div class="nm-stat-sep"></div>
            <div class="nm-stat">
                <span class="nm-stat-val nm-stat-val--r" id="stat-fail">—</span>
                <span class="nm-stat-key">< 10 (ajourné)</span>
            </div>
            <div class="nm-stat-sep"></div>
            <div class="nm-stat">
                <span class="nm-stat-val nm-stat-val--g" id="stat-max">—</span>
                <span class="nm-stat-key">Meilleure</span>
            </div>
            <div class="nm-stat-sep"></div>
            <div class="nm-stat">
                <span class="nm-stat-val nm-stat-val--r" id="stat-min">—</span>
                <span class="nm-stat-key">Plus faible</span>
            </div>
            <div class="nm-stat-sep"></div>
            <div class="nm-stat">
                <span class="nm-stat-val" id="stat-taux">—</span>
                <span class="nm-stat-key">Taux réussite</span>
            </div>
        </div>
    </div>

</div><!-- /.nm-root -->

@endsection

@section('another_JS')
<script>
/* ═══════════════════════════════════════════════════════════
   NotesManager v2
═══════════════════════════════════════════════════════════ */
class NotesManager {
    constructor() {
        this.data             = null;
        this.canEdit          = true;
        this.canModify        = true;
        this.saved            = new Set();
        this.modified         = new Set();
        this.INTERRO_COUNT    = 3;
        this.csrf             = '{{ csrf_token() }}';
        this._init();
    }

    /* ── Helpers ── */
    trunc2(v)   { return Math.floor(v * 100) / 100; }
    sid(id)     { return String(id); }
    esc(s)      { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; }

    $ (id)      { return document.getElementById(id); }
    $$ (sel)    { return document.querySelectorAll(sel); }

    /* ── Toasts ── */
    toast(type, msg, dur = 4200) {
        const icons = { success:'✓', error:'✗', warning:'⚠', info:'ℹ' };
        const t = document.createElement('div');
        t.className = `nm-toast nm-toast--${type}`;
        t.innerHTML = `<span class="toast-icon">${icons[type]||'ℹ'}</span><span>${msg}</span>
            <button onclick="this.parentElement.remove()">×</button>`;
        this.$('toast-area').appendChild(t);
        requestAnimationFrame(() => t.classList.add('show'));
        if (dur) setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 350); }, dur);
    }

    /* ── HTTP ── */
    async get(url) {
        const r = await fetch(url, { headers:{ 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' } });
        if (!r.ok) throw new Error((await r.json().catch(()=>({}))).message || `Erreur ${r.status}`);
        return r.json();
    }
    async post(url, body) {
        const r = await fetch(url, {
            method:'POST',
            headers:{ 'Content-Type':'application/json','Accept':'application/json',
                      'X-CSRF-TOKEN':this.csrf,'X-Requested-With':'XMLHttpRequest' },
            body: JSON.stringify(body)
        });
        const j = await r.json().catch(() => ({}));
        if (!r.ok && j.message) throw new Error(j.message);
        return j;
    }

    /* ── Init ── */
    _init() {
        this.$('year_id').addEventListener('change', () => this._loadSectors());
        this.$('sector_id').addEventListener('change', () => this._loadPromotions());
        this.$('promotion_id').addEventListener('change', () => this._loadClassrooms());
        this.$('classroom_id').addEventListener('change', () => this._loadSubjects());
        this.$('subject_id').addEventListener('change', () => { this._syncCoeff(); this._checkLoadBtn(); });
        this.$('btn-load').addEventListener('click', () => this.loadNotes());
        this.$('btn-save').addEventListener('click', () => this.saveNotes());
        this.$('btn-reset').addEventListener('click', () => this.resetAll());

        this.$$('.nm-sem-btn').forEach(b => b.addEventListener('click', () => {
            this.$$('.nm-sem-btn').forEach(x => x.classList.remove('nm-sem-btn--on'));
            b.classList.add('nm-sem-btn--on');
            this.$('semester').value = b.dataset.value;
            this._checkLoadBtn();
            if (this.$('classroom_id').value) this._loadSubjects();
        }));

        document.addEventListener('keydown', e => {
            if (e.ctrlKey && e.key === 's') { e.preventDefault(); this.saveNotes(); return; }
            if ((e.key === 'Tab' || e.key === 'Enter') && !e.shiftKey) {
                const a = document.activeElement;
                if (a?.classList.contains('nm-inp')) {
                    e.preventDefault();
                    const all = [...this.$$('.nm-inp:not(:disabled):not([readonly])')];
                    const i   = all.indexOf(a);
                    i < all.length - 1 ? all[i+1].focus() : this.$('btn-save').focus();
                }
            }
        });
    }

    /* ── Selects ── */
    _loading(sel) { sel.innerHTML='<option>Chargement…</option>'; sel.disabled=true; }
    _fill(sel, items, ph) {
        sel.innerHTML = `<option value="">${ph}</option>`;
        (items||[]).forEach(it => {
            const o = document.createElement('option');
            o.value = it.id; o.textContent = it.name || it.name_sector || it.promotion_sector || '—';
            sel.appendChild(o);
        });
        sel.disabled = !items?.length;
    }
    _resetFrom(from) {
        const chains = { year:['sector','promotion','classroom','subject'], sector:['promotion','classroom','subject'], promotion:['classroom','subject'], classroom:['subject'] };
        const phs    = { sector:'— Filière —', promotion:'— Promotion —', classroom:'— Classe —', subject:'— Matière —' };
        (chains[from]||[]).forEach(k => {
            const el = this.$(k+'_id') || this.$(k === 'subject' ? 'subject_id' : null);
            if (!el) return;
            el.innerHTML = `<option value="">${phs[k]||'—'}</option>`;
            el.disabled  = true;
        });
        this._syncCoeff(); this._clearTable(); this._checkLoadBtn();
        this._updateSteps(1);
    }

    async _loadSectors() {
        const y = this.$('year_id').value;
        this._resetFrom('year');
        if (!y) return;
        this._loading(this.$('sector_id'));
        try { this._fill(this.$('sector_id'), await this.get(`/api/sectors-by-year/${y}`), '— Choisir une filière —'); this._updateSteps(2); }
        catch(e) { this.toast('error', e.message); }
    }
    async _loadPromotions() {
        const y=this.$('year_id').value, s=this.$('sector_id').value;
        this._resetFrom('sector');
        if (!y||!s) return;
        this._loading(this.$('promotion_id'));
        try { this._fill(this.$('promotion_id'), await this.get(`/api/promotions-by-year-sector/${y}/${s}`), '— Choisir une promotion —'); }
        catch(e) { this.toast('error', e.message); }
    }
    async _loadClassrooms() {
        const p=this.$('promotion_id').value, y=this.$('year_id').value;
        this._resetFrom('promotion');
        if (!p) return;
        this._loading(this.$('classroom_id'));
        try { this._fill(this.$('classroom_id'), await this.get(`/api/classes-by-promotion/${p}?year_id=${y}`), '— Choisir une classe —'); }
        catch(e) { this.toast('error', e.message); }
    }
    async _loadSubjects() {
        const cl=this.$('classroom_id').value, y=this.$('year_id').value, sem=this.$('semester').value;
        this._resetFrom('classroom');
        if (!cl||!y) return;
        this._loading(this.$('subject_id'));
        try {
            const d = await this.post('/api/subjects-by-classroom', { classroom_id:cl, year_id:y, semester:sem?parseInt(sem):null });
            this.$('subject_id').innerHTML = '<option value="">— Choisir une matière —</option>';
            if (d.success && d.subjects?.length) {
                d.subjects.forEach(s => {
                    const o = document.createElement('option');
                    o.value = s.ratio_id;
                    o.dataset.coefficient = s.coefficient;
                    o.dataset.subjectId   = s.subject_id;
                    const sl = s.semester===1?' [S1]':s.semester===2?' [S2]':'';
                    o.textContent = `${s.subject_name}${sl}  —  Coeff. ${s.coefficient}`;
                    this.$('subject_id').appendChild(o);
                });
                this.$('subject_id').disabled = false;
                this._updateSteps(3);
            } else {
                this.toast('warning', d.message || 'Aucune matière disponible pour ce semestre.');
            }
        } catch(e) { this.toast('error', e.message); }
        this._checkLoadBtn();
    }
    _syncCoeff() {
        const o = this.$('subject_id').options[this.$('subject_id').selectedIndex];
        if (o?.value) {
            this.$('coefficient').textContent = o.dataset.coefficient || '1';
            this.$('ratio_id').value          = o.value;
            this.$('subject_real_id').value   = o.dataset.subjectId || '';
            this.$('coeff-pill').style.display = 'flex';
        } else {
            this.$('ratio_id').value = '';
            this.$('coeff-pill').style.display = 'none';
        }
    }
    _checkLoadBtn() {
        this.$('btn-load').disabled = !(
            this.$('classroom_id').value && this.$('subject_id').value && this.$('semester').value
        );
    }

    /* ── Step tracker ── */
    _updateSteps(active) {
        for (let i = 1; i <= 4; i++) {
            const el = this.$(`step-${i}`);
            if (!el) continue;
            el.classList.toggle('step-done',   i < active);
            el.classList.toggle('step-active', i === active);
        }
    }

    /* ── Clear table ── */
    _clearTable() {
        this.data = null; this.canEdit = true; this.canModify = true;
        this.saved.clear(); this.modified.clear();
        this.$('notes-body').innerHTML = '';
        this.$('notes-table-container').style.display = 'none';
        this.$('empty-state').style.display            = 'flex';
        this.$('shortcuts-bar').style.display          = 'none';
        this.$('progress-wrap').style.display          = 'none';
        this.$('locked-banner').classList.add('d-none');
        this.$('partial-banner').classList.add('d-none');
        this.$('btn-save').disabled = true;
        this.$('student-count').textContent = '0';
        this.$('saved-count').textContent   = '0';
        this.$('pending-chip').style.display = 'none';
        this._updateStats();
    }

    /* ── Load notes ── */
    async loadNotes() {
        const cl=this.$('classroom_id').value, y=this.$('year_id').value,
              r=this.$('ratio_id').value, s=this.$('semester').value;
        if (!cl||!y||!r||!s) { this.toast('warning','Veuillez remplir tous les champs.'); return; }

        const btn = this.$('btn-load');
        btn.disabled = true;
        this.$('btn-load-text').textContent = 'Chargement…';
        this.$('btn-load-spin').style.display = 'block';

        try {
            const d = await this.post('/api/students-with-notes', { year_id:y, classroom_id:cl, ratio_id:r, semester:s });
            if (d.success) {
                this.data = d.students; this.canEdit = d.can_edit??true; this.canModify = d.can_modify??true;
                this.saved.clear(); this.modified.clear();
                d.is_locked
                    ? this.$('locked-banner').classList.remove('d-none')
                    : this.$('locked-banner').classList.add('d-none');

                const partial = d.students.some(s => Object.values(s.fields_readonly||{}).some(v=>v));
                (partial && !d.is_locked)
                    ? this.$('partial-banner').classList.remove('d-none')
                    : this.$('partial-banner').classList.add('d-none');

                const lbl = this.$('subject_id').options[this.$('subject_id').selectedIndex]?.text || '—';
                this.$('toolbar-subject').textContent = '✎  ' + lbl;
                this._render();
                this._updateSteps(4);
                this.toast('success', `${d.students.length} élève(s) chargé(s) avec succès.`);
            } else {
                this.toast('warning', d.message || 'Aucun étudiant trouvé.');
            }
        } catch(e) { this.toast('error', e.message); }
        finally {
            this.$('btn-load-text').textContent = 'Charger les notes';
            this.$('btn-load-spin').style.display = 'none';
            this._checkLoadBtn();
        }
    }

    /* ── Render ── */
    _render() {
        if (!this.data?.length) { this._clearTable(); return; }
        this.$('empty-state').style.display = 'none';
        this.$('notes-table-container').style.display = 'block';
        this.$('shortcuts-bar').style.display = 'flex';
        this.$('student-count').textContent = this.data.length;
        this.$('progress-wrap').style.display = 'flex';

        const rows = this.data.map((s, i) => {
            const interros  = s.interros || [];
            const disabled  = s.is_disabled || !this.canEdit;
            const fr        = s.fields_readonly || {};

            /* Interros */
            let iCells = '';
            for (let k = 0; k < this.INTERRO_COUNT; k++) {
                const locked = disabled ? false : (fr[`interro_${k}`] || false);
                const attr   = disabled ? 'disabled' : locked ? 'readonly' : '';
                const val    = (interros[k] !== undefined && interros[k] !== null)
                    ? this.trunc2(parseFloat(interros[k])).toFixed(2) : '';
                iCells += `<td class="${locked?'td-locked':''}">
                    <div class="inp-wrap${locked?' inp-wrap--lock':''}">
                        <input type="number" class="nm-inp" data-idx="${i}" data-ii="${k}"
                               data-id="${s.recording_id}" value="${val}"
                               min="0" max="20" step="0.01" placeholder="—" ${attr}${locked?' title="Note enregistrée"':''}>
                        ${locked?'<span class="inp-lock-ico">🔒</span>':''}
                    </div>
                </td>`;
            }

            const moyI = s.moy_interros != null ? this.trunc2(parseFloat(s.moy_interros)).toFixed(2) : '—';

            /* Devoirs */
            let dCells = '';
            ['devoir1','devoir2'].forEach(f => {
                const locked = disabled ? false : (fr[f] || false);
                const attr   = disabled ? 'disabled' : locked ? 'readonly' : '';
                const val    = s[f] != null ? this.trunc2(parseFloat(s[f])).toFixed(2) : '';
                dCells += `<td class="${locked?'td-locked':''}">
                    <div class="inp-wrap${locked?' inp-wrap--lock':''}">
                        <input type="number" class="nm-inp" data-field="${f}" data-idx="${i}"
                               data-id="${s.recording_id}" value="${val}"
                               min="0" max="20" step="0.01" placeholder="—" ${attr}${locked?' title="Note enregistrée"':''}>
                        ${locked?'<span class="inp-lock-ico">🔒</span>':''}
                    </div>
                </td>`;
            });

            const moy20 = s.moy_20 != null ? this.trunc2(parseFloat(s.moy_20)) : null;
            const m20s  = moy20 != null ? moy20.toFixed(2) : '—';
            const m20c  = moy20==null?'':moy20>=10?'moy-pass':'moy-fail';

            const statusCls = s.field_readonly ? 'st-locked' : 'st-pending';
            const partial   = Object.values(fr).some(v=>v);

            return `<tr class="nm-row${s.field_readonly?' nm-row--lock':''}" data-idx="${i}" data-id="${s.recording_id}">
                <td class="td-num">${i+1}</td>
                <td class="td-name">${this.esc(s.name)}${partial?'<span class="partial-badge" title="Notes partiellement enregistrées">~</span>':''}</td>
                <td class="td-name td-surname">${this.esc(s.surname)}</td>
                ${iCells}
                <td><span class="nm-moy" data-type="mi" data-idx="${i}">${moyI}</span></td>
                ${dCells}
                <td><span class="nm-moy-final ${m20c}" data-type="m20" data-idx="${i}">${m20s}</span></td>
                <td class="td-status"><span class="nm-status ${statusCls}" data-idx="${i}"></span></td>
            </tr>`;
        });
        this.$('notes-body').innerHTML = rows.join('');

        const hasEditable = this.canEdit && this.data.some(s => !s.is_disabled && !s.field_readonly);
        this.$('btn-save').disabled = !hasEditable;

        this._bindInputs();
        this._bindHeaders();
        this._updateStats();
        this._updateProgress();
    }

    /* ── Events ── */
    _bindInputs() {
        this.$('notes-body').querySelectorAll('.nm-inp').forEach(inp => {
            inp.addEventListener('input', () => this._recalc(inp));
            inp.addEventListener('blur',  () => this._fmt(inp));
            inp.addEventListener('focus', () => inp.select());
        });
    }
    _bindHeaders() {
        this.$$('.nm-hdr-inp').forEach(inp => {
            inp.addEventListener('blur',     () => this._applyHeader(inp));
            inp.addEventListener('dblclick', () => this._fillAvg(inp));
            inp.addEventListener('keydown',  e => { if (e.key==='Enter') { e.preventDefault(); inp.blur(); } });
        });
    }
    _applyHeader(h) {
        const v = parseFloat(h.value.trim()); if (isNaN(v)||v<0||v>20) return;
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
        const vals = [...this.$('notes-body').querySelectorAll(sel)].map(i=>parseFloat(i.value)).filter(v=>!isNaN(v));
        if (!vals.length) return;
        const avg = vals.reduce((a,b)=>a+b,0)/vals.length;
        this.$('notes-body').querySelectorAll(sel).forEach(inp => { inp.value=avg.toFixed(2); this._recalc(inp); });
        this.toast('success', `Moyenne colonne appliquée : ${avg.toFixed(2)}`);
    }

    /* ── Recalcul ── */
    _recalc(inp) {
        if (inp.hasAttribute('readonly')) return;
        const idx = parseInt(inp.dataset.idx);
        const row = this.$('notes-body').querySelector(`tr[data-idx="${idx}"]`);
        if (!row) return;
        const sid = this.sid(inp.dataset.id);
        this.modified.add(sid); this.saved.delete(sid);
        row.classList.add('nm-row--mod'); row.classList.remove('nm-row--saved');
        const st = row.querySelector('.nm-status');
        if (st && !st.classList.contains('st-locked')) { st.className='nm-status st-mod'; }

        const interros = [];
        for (let k=0;k<this.INTERRO_COUNT;k++) {
            const i = row.querySelector(`.nm-inp[data-ii="${k}"]`);
            if (i && i.value!=='' && !i.disabled) { const v=parseFloat(i.value); if (!isNaN(v)) interros.push(v); }
        }
        const gD = f => { const i=row.querySelector(`.nm-inp[data-field="${f}"]`); if(!i||i.value===''||i.disabled)return null; const v=parseFloat(i.value); return isNaN(v)?null:v; };
        const d1=gD('devoir1'), d2=gD('devoir2');
        const moyI = interros.length ? this.trunc2(interros.reduce((a,b)=>a+b,0)/interros.length) : null;
        const comp = [moyI,d1,d2].filter(v=>v!==null);
        const moy20 = comp.length ? this.trunc2(comp.reduce((a,b)=>a+b,0)/comp.length) : null;

        const miEl = row.querySelector('[data-type="mi"]');
        if (miEl) miEl.textContent = moyI!==null?moyI.toFixed(2):'—';
        const m20El = row.querySelector('[data-type="m20"]');
        if (m20El) {
            m20El.textContent = moy20!==null?moy20.toFixed(2):'—';
            m20El.className   = 'nm-moy-final'+(moy20===null?'':moy20>=10?' moy-pass':' moy-fail');
        }
        if (this.data[idx]) Object.assign(this.data[idx], { interros, devoir1:d1, devoir2:d2, moy_interros:moyI, moy_20:moy20 });

        if (this.modified.size>0 && this.canEdit) this.$('btn-save').disabled = false;
        this._updatePendingChip();
        this._updateStats();
        this._updateProgress();
    }
    _fmt(inp) {
        if (inp.value!==''&&!inp.disabled&&!inp.readOnly) {
            const v=parseFloat(inp.value);
            if (!isNaN(v)) inp.value = this.trunc2(Math.min(Math.max(v,0),20)).toFixed(2);
        }
    }

    /* ── Save ── */
    async saveNotes() {
        if (!this.canEdit) { this.toast('warning','Notes verrouillées.'); return; }
        if (!this.data?.length||!this.modified.size) { this.toast('info','Aucune modification à sauvegarder.'); return; }

        const modData = this.data.filter(s => this.modified.has(this.sid(s.recording_id)));
        if (!modData.length) return;

        const notes = modData.map(s => {
            const row = this.$('notes-body').querySelector(`tr[data-id="${s.recording_id}"]`);
            const interros = [];
            for (let k=0;k<this.INTERRO_COUNT;k++) {
                const inp = row?.querySelector(`.nm-inp[data-ii="${k}"]`);
                interros.push((inp && inp.value!=='' && !inp.disabled) ? parseFloat(inp.value) : null);
            }
            const d1i = row?.querySelector('.nm-inp[data-field="devoir1"]');
            const d2i = row?.querySelector('.nm-inp[data-field="devoir2"]');
            return {
                recording_id: s.recording_id, interros,
                devoir1: (d1i&&d1i.value!==''&&!d1i.disabled)?parseFloat(d1i.value):null,
                devoir2: (d2i&&d2i.value!==''&&!d2i.disabled)?parseFloat(d2i.value):null,
            };
        });

        const payload = {
            year_id:this.$('year_id').value, classroom_id:this.$('classroom_id').value,
            ratio_id:this.$('ratio_id').value, semester:this.$('semester').value, notes,
        };

        const btn = this.$('btn-save');
        const orig = btn.innerHTML;
        btn.innerHTML = '<div class="nm-spin nm-spin--sm"></div> Sauvegarde…';
        btn.disabled  = true;

        try {
            const d = await this.post('/api/notes/bulk', payload);
            if (d.success) {
                modData.forEach(s => {
                    const id  = this.sid(s.recording_id);
                    this.saved.add(id); this.modified.delete(id);
                    const row = this.$('notes-body').querySelector(`tr[data-id="${s.recording_id}"]`);
                    if (row) {
                        row.classList.remove('nm-row--mod'); row.classList.add('nm-row--saved');
                        const st = row.querySelector('.nm-status');
                        if (st && !st.classList.contains('st-locked')) st.className='nm-status st-saved';
                    }
                });
                this.$('saved-count').textContent = this.saved.size;
                this._updatePendingChip();
                this._updateProgress();
                this._updateStats();
                this.toast('success', `${modData.length} note(s) enregistrée(s) avec succès.`);
                const allDone = this.data.every(s => this.saved.has(this.sid(s.recording_id))||s.is_disabled||s.field_readonly);
                if (allDone) setTimeout(() => this._afterSave(), 900);
            } else {
                this.toast('warning', d.message || 'Échec de la sauvegarde.');
            }
        } catch(e) { this.toast('error', e.message); }
        finally {
            btn.innerHTML = orig;
            btn.disabled  = !this.canEdit || !this.modified.size;
        }
    }

    _afterSave() {
        this.$('subject_id').value = ''; this._syncCoeff();
        this.$('notes-table-container').style.display='none';
        this.$('empty-state').style.display='flex';
        this.$('shortcuts-bar').style.display='none';
        this.$('progress-wrap').style.display='none';
        this.$('partial-banner').classList.add('d-none');
        this.$$('.nm-hdr-inp').forEach(i=>i.value='');
        this.data=null; this.saved.clear(); this.modified.clear();
        this.$('student-count').textContent='0'; this.$('saved-count').textContent='0';
        this._updatePendingChip(); this._updateStats(); this._checkLoadBtn();
        this._updateSteps(3);
        this.toast('info','Prêt pour une nouvelle saisie.', 3000);
    }

    /* ── UI utils ── */
    _updateProgress() {
        const total=this.data?.length||0;
        const pct = total?Math.round(this.saved.size/total*100):0;
        this.$('progress-fill').style.width = pct+'%';
        this.$('progress-label').textContent = pct+'%';
    }
    _updatePendingChip() {
        const n = this.modified.size;
        const ch = this.$('pending-chip');
        if (n>0) { this.$('saved-pending-text').textContent=n; ch.style.display='flex'; }
        else ch.style.display='none';
        const b = this.$('save-pending-badge');
        if (b) { n>0?(b.textContent=n,b.style.display='flex'):(b.style.display='none'); }
    }
    _updateStats() {
        const notes = (this.data||[]).map(s=>s.moy_20).filter(v=>v!=null&&!isNaN(v)).map(v=>parseFloat(v));
        const set = (id, v) => { const e=this.$(id); if(e)e.textContent=v; };
        if (!notes.length) { ['stat-avg','stat-pass','stat-fail','stat-max','stat-min','stat-taux'].forEach(id=>set(id,'—')); return; }
        const avg  = this.trunc2(notes.reduce((a,b)=>a+b,0)/notes.length);
        const pass = notes.filter(v=>v>=10).length;
        set('stat-avg',  avg.toFixed(2));
        set('stat-pass', pass);
        set('stat-fail', notes.length-pass);
        set('stat-max',  Math.max(...notes).toFixed(2));
        set('stat-min',  Math.min(...notes).toFixed(2));
        set('stat-taux', (pass/notes.length*100).toFixed(1)+'%');
    }

    resetAll() {
        this.$('year_id').value = '';
        this.$('semester').value = '1';
        this.$$('.nm-sem-btn').forEach((b,i) => b.classList.toggle('nm-sem-btn--on',i===0));
        this._resetFrom('year');
        this._updateSteps(1);
        this.toast('info','Formulaire réinitialisé.');
    }
}

document.addEventListener('DOMContentLoaded', () => { window.nm = new NotesManager(); });
</script>

<style>
/* ════════════════════════════════════════════════════════
   TOKENS & BASE
════════════════════════════════════════════════════════ */
:root {
    /* Couleurs */
    --bg:          #f0f2f6;
    --surface:     #ffffff;
    --s2:          #f7f8fc;
    --s3:          #eef0f6;
    --border:      #dde1ea;
    --border2:     #c8cdd9;
    --text:        #141622;
    --text2:       #4b5068;
    --text3:       #8c92a8;
    --blue:        #1d4ed8;
    --blue-h:      #1839b8;
    --blue-10:     rgba(29,78,216,.10);
    --blue-20:     rgba(29,78,216,.20);
    --green:       #15803d;
    --green-bg:    #dcfce7;
    --red:         #b91c1c;
    --red-bg:      #fee2e2;
    --amber:       #b45309;
    --amber-bg:    #fef3c7;
    --locked-bg:   #f1f2f7;
    --locked-bd:   #bec5d9;
    --mod-bg:      #fffbeb;
    --saved-bg:    #f0fdf4;

    /* Typography */
    --ff:          'Plus Jakarta Sans', system-ui, sans-serif;
    --mono:        'JetBrains Mono', 'Fira Code', monospace;

    /* Misc */
    --r:           10px;
    --rl:          16px;
    --sh:          0 1px 3px rgba(0,0,0,.07), 0 4px 14px rgba(0,0,0,.05);
    --shd:         0 4px 20px rgba(0,0,0,.10);
    --t:           .18s cubic-bezier(.4,0,.2,1);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ── Root ── */
.nm-root {
    font-family: var(--ff);
    background: var(--bg);
    min-height: 100vh;
    color: var(--text);
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* ── Topbar ── */
.nm-topbar {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .75rem;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--rl); padding: 1rem 1.5rem; box-shadow: var(--sh);
}
.nm-topbar-left { display: flex; align-items: center; gap: .9rem; }
.nm-topbar-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: linear-gradient(135deg, var(--blue), #3b82f6);
    color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(29,78,216,.35);
}
.nm-title    { font-size: 1.15rem; font-weight: 800; letter-spacing: -.3px; }
.nm-subtitle { font-size: .78rem; color: var(--text3); margin-top: 2px; }
.nm-topbar-right { display: flex; gap: .5rem; flex-wrap: wrap; }
.nm-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .3rem .75rem; border-radius: 99px; font-size: .78rem; font-weight: 600;
    background: var(--s2); border: 1px solid var(--border); color: var(--text2);
    transition: all var(--t);
}
.nm-chip--success { background: var(--green-bg); border-color: #86efac; color: var(--green); }
.nm-chip--warning { background: var(--amber-bg); border-color: #fcd34d; color: var(--amber); }

/* ── Toasts ── */
.nm-toast-zone {
    position: fixed; top: 1rem; right: 1rem; z-index: 9999;
    display: flex; flex-direction: column; gap: .4rem; pointer-events: none;
}
.nm-toast {
    display: flex; align-items: center; gap: .6rem; pointer-events: all;
    background: var(--surface); border-radius: var(--r); border-left: 4px solid var(--border);
    padding: .7rem 1rem; box-shadow: var(--shd); font-size: .85rem; font-weight: 500;
    max-width: 360px; min-width: 260px;
    opacity: 0; transform: translateX(20px); transition: all .3s;
}
.nm-toast.show { opacity: 1; transform: translateX(0); }
.nm-toast--success { border-color: var(--green); }
.nm-toast--success .toast-icon { color: var(--green); }
.nm-toast--error   { border-color: var(--red); }
.nm-toast--error   .toast-icon { color: var(--red); }
.nm-toast--warning { border-color: var(--amber); }
.nm-toast--warning .toast-icon { color: var(--amber); }
.nm-toast--info    { border-color: var(--blue); }
.nm-toast--info    .toast-icon { color: var(--blue); }
.nm-toast span:not(.toast-icon) { flex: 1; color: var(--text); }
.nm-toast button { background: none; border: none; cursor: pointer; color: var(--text3); font-size: 1.1rem; padding: 0; }
.toast-icon { font-size: 1rem; font-weight: 700; flex-shrink: 0; }

/* ── Bannières ── */
.nm-banner {
    display: flex; align-items: center; gap: .65rem;
    padding: .75rem 1.25rem; border-radius: var(--r); font-size: .85rem;
}
.nm-banner--locked { background: var(--amber-bg); border: 1px solid #fcd34d; color: var(--amber); }
.nm-banner--info   { background: #eff6ff; border: 1px solid #93c5fd; color: #1e40af; }
.d-none { display: none !important; }

/* ── Panel filtres ── */
.nm-panel {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--rl); padding: 1.25rem 1.5rem; box-shadow: var(--sh);
    display: flex; flex-direction: column; gap: 1rem;
}
.nm-panel-header {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .75rem;
}
.nm-panel-title {
    display: flex; align-items: center; gap: .45rem;
    font-size: .9rem; font-weight: 700; color: var(--text);
}
.nm-panel-title svg { color: var(--blue); }

/* ── Step tracker ── */
.nm-step-track { display: flex; align-items: center; gap: 0; }
.nm-step {
    display: flex; align-items: center; gap: .35rem;
    font-size: .75rem; font-weight: 600; color: var(--text3);
}
.nm-step-dot {
    width: 24px; height: 24px; border-radius: 50%;
    background: var(--s3); border: 2px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-size: .7rem; font-weight: 700; color: var(--text3);
    transition: all var(--t);
}
.nm-step.step-active .nm-step-dot { background: var(--blue); border-color: var(--blue); color: #fff; box-shadow: 0 0 0 3px var(--blue-10); }
.nm-step.step-active { color: var(--blue); }
.nm-step.step-done   .nm-step-dot { background: var(--green); border-color: var(--green); color: #fff; }
.nm-step.step-done   { color: var(--green); }
.nm-step-line { width: 28px; height: 2px; background: var(--border); margin: 0 2px; }

/* ── Filters grid ── */
.nm-filters-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: .75rem;
}
@media (max-width: 900px) { .nm-filters-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 540px) { .nm-filters-grid { grid-template-columns: 1fr; } }

.nm-filters-row2 {
    display: flex; gap: .75rem; align-items: flex-end; flex-wrap: wrap;
}
.nm-fg { display: flex; flex-direction: column; gap: .35rem; flex: 1; min-width: 120px; }
.nm-fg--sm   { flex: 0 0 auto; }
.nm-fg--wide { flex: 3; }
.nm-fg--auto { flex: 0 0 auto; }
.nm-label {
    font-size: .72rem; font-weight: 700; color: var(--text2);
    text-transform: uppercase; letter-spacing: .5px;
    display: flex; align-items: center; gap: .3rem;
}
.nm-label svg { color: var(--blue); }
.nm-label--ghost { opacity: 0; pointer-events: none; }
.nm-select {
    font-family: var(--ff); font-size: .875rem; height: 40px; padding: 0 2.2rem 0 .8rem;
    background: var(--s2) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%238c92a8' d='M8 11L3 6h10z'/%3E%3C/svg%3E") no-repeat right .65rem center / 12px;
    border: 1.5px solid var(--border); border-radius: var(--r);
    color: var(--text); width: 100%; appearance: none; cursor: pointer;
    transition: border-color var(--t), box-shadow var(--t);
}
.nm-select:focus { outline:none; border-color:var(--blue); box-shadow:0 0 0 3px var(--blue-10); background-color:var(--surface); }
.nm-select:not(:disabled):hover { border-color: #a0a8bf; }
.nm-select:disabled { opacity: .45; cursor: not-allowed; }

.nm-coeff-pill {
    display: flex; align-items: center; gap: 4px;
    background: var(--blue-10); color: var(--blue);
    border: 1.5px solid var(--blue-20); border-radius: 8px;
    padding: 2px 10px; font-size: .78rem; font-weight: 700; white-space: nowrap;
}

/* ── Semestre toggle ── */
.nm-sem-toggle {
    display: flex; height: 40px; border: 1.5px solid var(--border);
    border-radius: var(--r); overflow: hidden;
}
.nm-sem-btn {
    flex: 1; min-width: 56px; border: none; background: var(--s2);
    font-family: var(--ff); font-size: .875rem; font-weight: 700;
    cursor: pointer; color: var(--text2);
    transition: background var(--t), color var(--t);
}
.nm-sem-btn--on { background: var(--blue); color: #fff; }
.nm-sem-btn:not(.nm-sem-btn--on):hover { background: var(--blue-10); color: var(--blue); }

/* ── Bouton principal ── */
.nm-btn-primary {
    font-family: var(--ff); height: 40px; padding: 0 1.35rem;
    background: linear-gradient(135deg, var(--blue), #3b82f6);
    color: #fff; border: none; border-radius: var(--r);
    font-size: .875rem; font-weight: 700; cursor: pointer;
    display: flex; align-items: center; gap: .5rem; white-space: nowrap;
    box-shadow: 0 2px 8px rgba(29,78,216,.35);
    transition: transform var(--t), box-shadow var(--t), opacity var(--t);
}
.nm-btn-primary:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(29,78,216,.40); }
.nm-btn-primary:disabled { opacity: .45; cursor: not-allowed; }
.btn-icon { display:flex; flex-shrink:0; }

/* ── Progress ── */
.nm-progress-wrap {
    display: flex; align-items: center; gap: .65rem;
}
.nm-progress-bar {
    flex: 1; height: 6px; background: var(--s3);
    border-radius: 99px; overflow: hidden;
}
.nm-progress-fill {
    height: 100%; width: 0;
    background: linear-gradient(90deg, var(--blue), #60a5fa);
    border-radius: 99px;
    transition: width .4s cubic-bezier(.4,0,.2,1);
}
.nm-progress-txt {
    font-family: var(--mono); font-size: .75rem; font-weight: 600;
    color: var(--text2); min-width: 32px; text-align: right;
}
.nm-divider { height: 1px; background: var(--border); }

/* ── Shortcuts bar ── */
.nm-shortcuts-bar {
    display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--r); padding: .6rem 1rem;
    font-size: .76rem; color: var(--text3);
}
.sc-item { display: flex; align-items: center; gap: .3rem; }
.sc-item--blue { color: #1e40af; }
.sc-item kbd {
    background: var(--s3); border: 1px solid var(--border); border-bottom-width: 2px;
    border-radius: 5px; padding: 1px 5px; font-size: .7rem; font-family: var(--mono); color: var(--text2);
}

/* ── Empty state ── */
.nm-empty {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: .9rem; padding: 5rem 2rem;
    background: var(--surface); border: 1.5px dashed var(--border);
    border-radius: var(--rl); text-align: center; color: var(--text3);
}
.nm-empty-visual { position: relative; width: 90px; height: 90px; }
.nm-ring {
    position: absolute; border-radius: 50%;
    border: 2px solid transparent; border-top-color: var(--blue);
    animation: spin linear infinite;
}
.nm-ring-1 { inset:0;       opacity:.15; animation-duration:3s; }
.nm-ring-2 { inset:12px;    opacity:.25; animation-duration:4.5s; border-top-color: #3b82f6; }
.nm-ring-3 { inset:24px;    opacity:.4;  animation-duration:2.5s; animation-direction: reverse; }
.nm-empty-icon-center {
    position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
    color: var(--blue);
}
@keyframes spin { to { transform: rotate(360deg); } }
.nm-empty h3 { font-size: 1.05rem; font-weight: 700; color: var(--text2); }
.nm-empty p  { font-size: .875rem; line-height: 1.65; max-width: 360px; }

/* ── Card / Table ── */
.nm-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--rl); box-shadow: var(--sh); overflow: hidden;
}
.nm-card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: .875rem 1.25rem; border-bottom: 1px solid var(--border); gap: .75rem; flex-wrap: wrap;
}
.nm-card-title { font-size: .9rem; font-weight: 700; color: var(--text); }

.nm-btn-ghost {
    font-family: var(--ff); height: 36px; padding: 0 1rem;
    background: var(--s2); border: 1.5px solid var(--border);
    border-radius: 8px; font-size: .82rem; font-weight: 600;
    color: var(--text2); cursor: pointer; display: flex; align-items: center; gap: .35rem;
    transition: background var(--t);
}
.nm-btn-ghost:hover { background: var(--s3); }

.nm-btn-save {
    font-family: var(--ff); height: 36px; padding: 0 1.2rem;
    background: linear-gradient(135deg, var(--blue), #3b82f6);
    color: #fff; border: none; border-radius: 8px;
    font-size: .85rem; font-weight: 700; cursor: pointer;
    display: flex; align-items: center; gap: .35rem;
    box-shadow: 0 2px 8px rgba(29,78,216,.3);
    transition: transform var(--t), box-shadow var(--t), opacity var(--t);
}
.nm-btn-save:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 5px 14px rgba(29,78,216,.38); }
.nm-btn-save:disabled { opacity:.4; cursor:not-allowed; }
.nm-save-badge {
    background: rgba(255,255,255,.3); border-radius: 99px;
    min-width: 18px; height: 18px; font-size: .7rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center; padding: 0 4px;
}

/* ── Table ── */
.nm-table { width: 100%; border-collapse: collapse; font-size: .845rem; font-family: var(--ff); }
.nm-table thead th {
    padding: .7rem .45rem; background: var(--s2);
    border-bottom: 2px solid var(--border);
    font-weight: 700; font-size: .75rem; letter-spacing: .2px;
    color: var(--text2); text-align: center; white-space: nowrap; user-select: none;
}
.th-num    { width: 40px; }
.th-name   { text-align: left; padding-left: .9rem !important; }
.th-note   { width: 82px; cursor: pointer; }
.th-note:hover { background: var(--blue-10); }
.th-avg    { width: 78px; background: #eff6ff !important; color: var(--blue) !important; }
.th-final  { width: 90px; background: #f0fdf4 !important; color: var(--green) !important; }
.th-status { width: 40px; }
.th-inner {
    display: flex; flex-direction: column; align-items: center; gap: 4px;
    font-size: .78rem;
}
.th-auto {
    font-size: .62rem; background: rgba(0,0,0,.07); border-radius: 4px;
    padding: 1px 5px; letter-spacing: .3px; text-transform: uppercase; color: inherit;
}
.nm-hdr-inp {
    font-family: var(--mono); font-size: .76rem; font-weight: 500;
    width: 58px; height: 26px; padding: 0 5px; text-align: center;
    border: 1.5px solid var(--border); border-radius: 6px;
    background: var(--surface); color: var(--text);
    transition: border-color var(--t);
}
.nm-hdr-inp:focus { outline:none; border-color:var(--blue); }

.nm-row td {
    padding: .4rem .45rem; border-bottom: 1px solid var(--border);
    vertical-align: middle; text-align: center;
}
.nm-row:last-child td { border-bottom: none; }
.nm-row:hover td { background: rgba(29,78,216,.025); }
.nm-row--mod  td { background: var(--mod-bg) !important; }
.nm-row--saved td { background: var(--saved-bg) !important; }
.nm-row--lock td { background: var(--locked-bg); }
.td-num    { color: var(--text3); font-family: var(--mono); font-size: .78rem; }
.td-name   { text-align: left; padding-left: .9rem !important; font-weight: 600; white-space: nowrap; }
.td-surname { font-weight: 400; }
.td-locked { background: var(--locked-bg) !important; }
.inp-wrap { position: relative; display: inline-flex; align-items: center; }
.inp-wrap--lock .nm-inp { padding-right: 20px; }
.inp-lock-ico { position: absolute; right: 5px; font-size: .62rem; pointer-events: none; opacity: .5; }
.partial-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 15px; height: 15px; margin-left: 4px;
    background: #dbeafe; border-radius: 4px; color: #1e40af; font-size: .68rem; font-weight: 700;
    vertical-align: middle; cursor: help;
}

.nm-inp {
    font-family: var(--mono); font-size: .845rem; font-weight: 500;
    width: 62px; height: 32px; text-align: center; padding: 0 4px;
    border: 1.5px solid transparent; border-radius: 7px;
    background: var(--s2); color: var(--text);
    display: block; margin: 0 auto;
    transition: border-color var(--t), box-shadow var(--t), background var(--t);
}
.nm-inp:hover:not(:disabled):not([readonly]) { border-color: var(--border2); background: var(--surface); }
.nm-inp:focus { outline:none; border-color:var(--blue); box-shadow:0 0 0 3px var(--blue-10); background:var(--surface); }
.nm-inp:disabled { background:transparent; opacity:.3; cursor:not-allowed; }
.nm-inp[readonly] {
    background: var(--locked-bg); color: var(--text3);
    cursor: not-allowed; border-color: var(--locked-bd); border-style: dashed;
}

.nm-moy { font-family: var(--mono); font-weight: 700; font-size: .84rem; color: var(--blue); }
.nm-moy-final {
    display: inline-flex; align-items: center; justify-content: center;
    font-family: var(--mono); font-weight: 700; font-size: .875rem;
    padding: 3px 9px; border-radius: 7px; min-width: 52px;
    background: var(--s2); color: var(--text2);
}
.moy-pass { background: var(--green-bg); color: var(--green); }
.moy-fail { background: var(--red-bg); color: var(--red); }

.nm-status {
    display: inline-block; width: 10px; height: 10px; border-radius: 50%;
    transition: background var(--t);
}
.st-pending  { background: var(--border2); }
.st-mod      { background: var(--amber); box-shadow: 0 0 0 3px var(--amber-bg); }
.st-saved    { background: var(--green); box-shadow: 0 0 0 3px var(--green-bg); }
.st-locked   { background: var(--text3); }

/* ── Stats bar ── */
.nm-stats-bar {
    display: flex; align-items: center; flex-wrap: wrap;
    border-top: 1px solid var(--border); padding: .65rem 1.25rem;
}
.nm-stat { display: flex; flex-direction: column; align-items: center; padding: .25rem 1rem; }
.nm-stat-sep { width: 1px; height: 28px; background: var(--border); flex-shrink: 0; }
.nm-stat-val { font-family: var(--mono); font-weight: 700; font-size: .98rem; color: var(--text); }
.nm-stat-val--g { color: var(--green); }
.nm-stat-val--r { color: var(--red); }
.nm-stat-key { font-size: .7rem; color: var(--text3); font-weight: 600; text-transform: uppercase; letter-spacing: .3px; margin-top: 2px; }

/* ── Spinner ── */
@keyframes spin2 { to { transform: rotate(360deg); } }
.nm-spin {
    width: 16px; height: 16px; border-radius: 50%;
    border: 2px solid rgba(255,255,255,.3); border-top-color: #fff;
    animation: spin2 .7s linear infinite; flex-shrink: 0;
}
.nm-spin--sm { width: 13px; height: 13px; border-width: 2px; }

/* ── Scrollbar ── */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 99px; }
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; }
input[type=number] { -moz-appearance: textfield; }

@media (max-width: 640px) {
    .nm-root { padding: .75rem; }
    .nm-panel-header { flex-direction: column; align-items: flex-start; }
    .nm-step-track { display: none; }
    .nm-filters-row2 .nm-fg { min-width: 100%; }
    .nm-btn-primary { width: 100%; justify-content: center; }
    .nm-stats-bar { justify-content: center; }
    .nm-stat { padding: .25rem .6rem; }
}
</style>
@endsection
