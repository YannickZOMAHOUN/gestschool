@extends('layouts.template')

@section('content')

<div class="xp-wrap">
    <div class="xp-bg-pattern"></div>

    <div class="xp-container">

        <!-- Header -->
        <div class="xp-header">
            <div class="xp-header-text">
                <div class="xp-pill">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                    Exportation CSV
                </div>
                <h1 class="xp-title">Notes par classe</h1>
                <p class="xp-sub">Configurez vos filtres et exportez les notes au format CSV (compatible Excel)</p>
            </div>
            <div class="xp-header-visual">
                <div class="xp-visual-ring xp-ring-1"></div>
                <div class="xp-visual-ring xp-ring-2"></div>
                <div class="xp-visual-core">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Main card -->
        <div class="xp-card">

            <!-- Breadcrumb sélection -->
            <div class="xp-breadcrumb" id="xp-breadcrumb">
                <div class="xp-bc-item xp-bc-item--year" id="xp-bc-year">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span id="xp-bc-year-label">Année</span>
                </div>
                <span class="xp-bc-sep">›</span>
                <div class="xp-bc-item" id="xp-bc-sector">
                    <span id="xp-bc-sector-label">Filière</span>
                </div>
                <span class="xp-bc-sep">›</span>
                <div class="xp-bc-item" id="xp-bc-promotion">
                    <span id="xp-bc-promotion-label">Promotion</span>
                </div>
                <span class="xp-bc-sep">›</span>
                <div class="xp-bc-item" id="xp-bc-classroom">
                    <span id="xp-bc-classroom-label">Classe</span>
                </div>
            </div>

            <form method="GET" action="{{ route('notes.export') }}" id="exportForm" novalidate>

                <!-- Étape 1 : Filière -->
                <div class="xp-step" id="xp-step-sector">
                    <div class="xp-step-head">
                        <div class="xp-step-num">1</div>
                        <div class="xp-step-info">
                            <div class="xp-step-title">Filière</div>
                            <div class="xp-step-hint">Choisissez la filière d'enseignement</div>
                        </div>
                        <div class="xp-loader" id="xp-loader-sector" style="display:none;">
                            <div class="xp-spin-sm"></div>
                        </div>
                    </div>
                    <div class="xp-step-opts" id="xp-opts-sector">
                        <div class="xp-step-ph">Chargement de l'année active…</div>
                    </div>
                </div>

                <!-- Étape 2 : Promotion -->
                <div class="xp-step xp-step--locked" id="xp-step-promotion">
                    <div class="xp-step-head">
                        <div class="xp-step-num">2</div>
                        <div class="xp-step-info">
                            <div class="xp-step-title">Promotion</div>
                            <div class="xp-step-hint">Sélectionnez la promotion dans la filière</div>
                        </div>
                        <div class="xp-loader" id="xp-loader-promotion" style="display:none;">
                            <div class="xp-spin-sm"></div>
                        </div>
                    </div>
                    <div class="xp-step-opts" id="xp-opts-promotion">
                        <div class="xp-step-ph">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                            Choisissez d'abord une filière
                        </div>
                    </div>
                </div>

                <!-- Étape 3 : Classe -->
                <div class="xp-step xp-step--locked" id="xp-step-classroom">
                    <div class="xp-step-head">
                        <div class="xp-step-num">3</div>
                        <div class="xp-step-info">
                            <div class="xp-step-title">Classe</div>
                            <div class="xp-step-hint">Choisissez la classe à exporter</div>
                        </div>
                        <div class="xp-loader" id="xp-loader-classroom" style="display:none;">
                            <div class="xp-spin-sm"></div>
                        </div>
                    </div>
                    <div class="xp-step-opts" id="xp-opts-classroom">
                        <div class="xp-step-ph">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                            Choisissez d'abord une promotion
                        </div>
                    </div>
                </div>

                <!-- Champs cachés -->
                <input type="hidden" name="year_id"      id="xp-year-id">
                <input type="hidden" name="classroom_id" id="xp-classroom-id">

                <!-- Semestre -->
                <div class="xp-sem-wrap">
                    <label class="xp-lbl">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Semestre
                    </label>
                    <div class="xp-sem-group">
                        <label class="xp-sem-card">
                            <input type="radio" name="semester" value="1" required>
                            <div class="xp-sem-inner">
                                <div class="xp-sem-badge">S1</div>
                                <div class="xp-sem-name">Semestre 1</div>
                                <div class="xp-sem-check">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                            </div>
                        </label>
                        <label class="xp-sem-card">
                            <input type="radio" name="semester" value="2">
                            <div class="xp-sem-inner">
                                <div class="xp-sem-badge">S2</div>
                                <div class="xp-sem-name">Semestre 2</div>
                                <div class="xp-sem-check">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                            </div>
                        </label>
                    </div>
                    <p class="xp-err" id="xp-sem-err" style="display:none">Veuillez sélectionner un semestre</p>
                </div>

                <!-- Actions -->
                <div class="xp-footer">
                    <button type="button" class="xp-btn-ghost" id="xp-btn-reset">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                        Réinitialiser
                    </button>
                    <button type="submit" class="xp-btn-primary" id="xp-btn-submit" disabled>
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Exporter en CSV
                    </button>
                </div>

            </form>
        </div>

        <p class="xp-hint">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Le fichier CSV généré est encodé en UTF-8 (avec BOM) pour une ouverture correcte dans Excel.
            Colonnes : Matricule, Nom, Prénom, Matière, Coefficient, Semestre, Interro 1/2/3, Moy. Interros, Devoir 1, Devoir 2, Moy./20
        </p>

    </div>
</div>

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --xp-accent: #6366f1;
    --xp-accent-2: #4f46e5;
    --xp-accent-bg: #eef2ff;
    --xp-accent-border: #c7d2fe;
    --xp-border: #e5e7eb;
    --xp-bg: #f0f4ff;
    --xp-surface: #ffffff;
    --xp-ink: #1e1b4b;
    --xp-ink-2: #374151;
    --xp-muted: #6b7280;
    --xp-muted-2: #9ca3af;
    --font: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
    --font-mono: 'Consolas', 'Courier New', monospace;
    --ease: cubic-bezier(.4, 0, .2, 1);
    --ease-spring: cubic-bezier(.34, 1.56, .64, 1);
}

.xp-wrap {
    min-height: 100vh;
    background: var(--xp-bg);
    padding: 2.5rem 1.25rem 5rem;
    font-family: var(--font);
    position: relative;
    overflow: hidden;
}
.xp-bg-pattern {
    position: absolute; inset: 0;
    background-image:
        radial-gradient(circle at 15% 15%, rgba(99,102,241,.08) 0%, transparent 50%),
        radial-gradient(circle at 85% 80%, rgba(16,185,129,.06) 0%, transparent 50%);
    pointer-events: none;
}
.xp-container { max-width: 860px; margin: 0 auto; position: relative; z-index: 1; }

/* Header */
.xp-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; animation: xpFadeUp .55s ease both; }
.xp-pill { display: inline-flex; align-items: center; gap: 6px; background: var(--xp-accent-bg); color: var(--xp-accent); border: 1.5px solid var(--xp-accent-border); font-size: .72rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; padding: 5px 12px; border-radius: 50px; margin-bottom: .9rem; }
.xp-title { font-family: var(--font); font-size: 2.2rem; font-weight: 800; color: var(--xp-ink); line-height: 1.1; letter-spacing: -.02em; margin-bottom: .6rem; }
.xp-sub { color: var(--xp-muted); font-size: .88rem; line-height: 1.6; max-width: 380px; }
.xp-header-visual { position: relative; width: 90px; height: 90px; flex-shrink: 0; }
.xp-visual-ring { position: absolute; border-radius: 50%; border: 1.5px solid; top: 50%; left: 50%; transform: translate(-50%,-50%); }
.xp-ring-1 { width: 100%; height: 100%; border-color: rgba(99,102,241,.15); animation: xpSpin 12s linear infinite; }
.xp-ring-2 { width: 72%; height: 72%; border-color: rgba(99,102,241,.25); animation: xpSpin 8s linear infinite reverse; }
@keyframes xpSpin { to { transform: translate(-50%,-50%) rotate(360deg); } }
.xp-visual-core { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 48px; height: 48px; background: linear-gradient(135deg, var(--xp-accent), var(--xp-accent-2)); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 8px 24px rgba(99,102,241,.35); }

/* Card */
.xp-card { background: var(--xp-surface); border: 1px solid var(--xp-border); border-radius: 20px; padding: 28px; box-shadow: 0 4px 6px rgba(0,0,0,.04), 0 20px 50px rgba(99,102,241,.07); animation: xpFadeUp .55s .1s ease both; }
@keyframes xpFadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }

/* Breadcrumb */
.xp-breadcrumb { display: flex; align-items: center; gap: 6px; padding: 10px 14px; background: #f9fafb; border: 1px solid var(--xp-border); border-radius: 10px; margin-bottom: 22px; flex-wrap: wrap; }
.xp-bc-item { display: flex; align-items: center; gap: 5px; padding: 3px 8px; border-radius: 6px; color: var(--xp-muted-2); font-size: .74rem; font-weight: 600; transition: all .2s; }
.xp-bc-item--year { color: var(--xp-accent); font-weight: 800; }
.xp-bc-item.active { color: var(--xp-ink-2); background: var(--xp-accent-bg); }
.xp-bc-sep { color: #d1d5db; }

/* Étapes */
.xp-step { border: 1px solid var(--xp-border); border-radius: 14px; overflow: hidden; margin-bottom: 14px; transition: opacity .2s, filter .2s; }
.xp-step--locked { opacity: .5; pointer-events: none; filter: grayscale(.3); }
.xp-step-head { display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: #f9fafb; border-bottom: 1px solid #f3f4f6; }
.xp-step-num { width: 26px; height: 26px; border-radius: 8px; background: var(--xp-accent-bg); border: 1px solid var(--xp-accent-border); color: var(--xp-accent); font-weight: 800; font-size: .78rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.xp-step-info { flex: 1; }
.xp-step-title { font-weight: 700; font-size: .83rem; color: var(--xp-ink-2); }
.xp-step-hint { font-size: .72rem; color: var(--xp-muted); margin-top: 1px; }
.xp-loader { display: flex; align-items: center; }
.xp-spin-sm { width: 14px; height: 14px; border: 2px solid #e5e7eb; border-top-color: var(--xp-accent); border-radius: 50%; animation: xpSpin2 .7s linear infinite; }
@keyframes xpSpin2 { to { transform: rotate(360deg); } }
.xp-step-opts { padding: 12px 16px; display: flex; flex-wrap: wrap; gap: 8px; min-height: 50px; align-items: center; }
.xp-step-ph { display: flex; align-items: center; gap: 7px; font-size: .79rem; color: var(--xp-muted-2); font-style: italic; }

/* Cartes options */
.xp-opt-card { display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; background: var(--xp-surface); border: 1.5px solid var(--xp-border); border-radius: 10px; cursor: pointer; font-family: var(--font); font-size: .83rem; font-weight: 600; color: var(--xp-ink-2); transition: all .18s var(--ease); white-space: nowrap; }
.xp-opt-card:hover { border-color: var(--xp-accent-border); background: var(--xp-accent-bg); color: var(--xp-accent); transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,.06); }
.xp-opt-card--active { border-color: var(--xp-accent); background: var(--xp-accent-bg); color: var(--xp-accent); box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
.xp-opt-arrow { opacity: .4; }
.xp-opt-card--active .xp-opt-arrow { opacity: 1; }

/* Semestre */
.xp-sem-wrap { display: flex; flex-direction: column; gap: 10px; margin-top: 20px; margin-bottom: 24px; padding-top: 20px; border-top: 1px solid #f3f4f6; }
.xp-lbl { display: flex; align-items: center; gap: 6px; font-size: .78rem; font-weight: 600; color: var(--xp-ink-2); }
.xp-sem-group { display: flex; gap: 1rem; max-width: 360px; }
.xp-sem-card { cursor: pointer; flex: 1; }
.xp-sem-card input { display: none; }
.xp-sem-inner { display: flex; align-items: center; gap: 10px; padding: .8rem 1.1rem; background: #f9fafb; border: 1.5px solid var(--xp-border); border-radius: 12px; transition: all .22s; position: relative; }
.xp-sem-badge { font-family: var(--font); font-size: 1.1rem; font-weight: 800; color: var(--xp-muted-2); transition: color .22s; min-width: 28px; }
.xp-sem-name { font-size: .82rem; font-weight: 500; color: var(--xp-muted); transition: color .22s; }
.xp-sem-check { margin-left: auto; width: 20px; height: 20px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; transition: all .22s; }
.xp-sem-check svg { width: 10px; height: 10px; color: var(--xp-muted-2); }
.xp-sem-card:hover .xp-sem-inner { border-color: var(--xp-accent-border); background: var(--xp-accent-bg); }
.xp-sem-card input:checked + .xp-sem-inner { border-color: var(--xp-accent); background: var(--xp-accent-bg); box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
.xp-sem-card input:checked + .xp-sem-inner .xp-sem-badge { color: var(--xp-accent); }
.xp-sem-card input:checked + .xp-sem-inner .xp-sem-name { color: var(--xp-accent-2); }
.xp-sem-card input:checked + .xp-sem-inner .xp-sem-check { background: var(--xp-accent); }
.xp-sem-card input:checked + .xp-sem-inner .xp-sem-check svg { color: white; }
.xp-err { font-size: .75rem; color: #ef4444; }

/* Footer */
.xp-footer { display: flex; align-items: center; justify-content: flex-end; gap: 1rem; padding-top: 20px; border-top: 1px solid #f3f4f6; }
.xp-btn-ghost { display: inline-flex; align-items: center; gap: 7px; padding: .7rem 1.4rem; background: transparent; border: 1.5px solid var(--xp-border); border-radius: 10px; color: var(--xp-muted); font-family: var(--font); font-size: .88rem; font-weight: 500; cursor: pointer; transition: all .2s; }
.xp-btn-ghost:hover { border-color: #d1d5db; color: var(--xp-ink-2); background: #f9fafb; }
.xp-btn-primary { display: inline-flex; align-items: center; gap: 9px; padding: .75rem 1.75rem; background: linear-gradient(135deg, var(--xp-accent), var(--xp-accent-2)); border: none; border-radius: 10px; color: white; font-family: var(--font); font-size: .9rem; font-weight: 700; cursor: pointer; transition: all .25s; box-shadow: 0 4px 14px rgba(99,102,241,.35); }
.xp-btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(99,102,241,.45); }
.xp-btn-primary:disabled { opacity: .35; cursor: not-allowed; box-shadow: none; transform: none; }

.xp-hint { display: flex; align-items: flex-start; gap: 7px; margin-top: 1.5rem; color: var(--xp-muted-2); font-size: .79rem; animation: xpFadeUp .55s .25s ease both; line-height: 1.6; max-width: 860px; }
.xp-hint svg { flex-shrink: 0; margin-top: 2px; }

@media (max-width: 600px) {
    .xp-card { padding: 1.25rem; }
    .xp-footer { flex-direction: column; }
    .xp-btn-ghost, .xp-btn-primary { width: 100%; justify-content: center; }
    .xp-header-visual { display: none; }
    .xp-title { font-size: 1.7rem; }
}
</style>

@endsection

@section('another_JS')
<script>
/* ════════════════════════════════════════════════════
   Export — Cascade intelligente (même logique que create)
   Année active auto, auto-sélection si option unique
════════════════════════════════════════════════════ */
const XP = {
    baseUrl: '{{ rtrim(url('/'), '/') }}',
    state: { yearId: null, sectorId: null, promotionId: null, classroomId: null },

    async get(path) {
        const r = await fetch(this.baseUrl + path, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        if (!r.ok) throw new Error(`Erreur ${r.status}`);
        return r.json();
    },

    $(id) { return document.getElementById(id); },

    init() {
        this.$('xp-btn-reset').addEventListener('click', () => this.reset());
        this.$('exportForm').addEventListener('submit', e => {
            const sem = document.querySelector('input[name="semester"]:checked');
            if (!sem || !this.state.classroomId) {
                e.preventDefault();
                if (!sem) { this.$('xp-sem-err').style.display = 'block'; }
                return;
            }
            this.$('xp-sem-err').style.display = 'none';
        });
        document.querySelectorAll('input[name="semester"]').forEach(r => {
            r.addEventListener('change', () => {
                this.$('xp-sem-err').style.display = 'none';
                this._updateSubmit();
            });
        });
        this._loadActiveYear();
    },

    async _loadActiveYear() {
        try {
            const d = await this.get('/api/active-year');
            if (d.success && d.year) {
                this.state.yearId = d.year.id;
                this.$('xp-year-id').value = d.year.id;
                this._setBc('year', d.year.year);
                await this._loadSectors();
            } else {
                this._renderOpts('sector', [], 'Aucune année active.');
            }
        } catch(e) {
            this._renderOpts('sector', [], 'Erreur : ' + e.message);
        }
    },

    async _loadSectors() {
        this._setLoading('sector', true);
        this.$('xp-step-sector').classList.remove('xp-step--locked');
        try {
            const d = await this.get(`/api/sectors-for-create/${this.state.yearId}`);
            this._setLoading('sector', false);
            this._renderOpts('sector', d.sectors || [], 'Aucune filière.', item => this._onSector(item));
            if (d.auto && d.sectors?.length) await this._onSector(d.sectors[0], true);
        } catch(e) { this._setLoading('sector', false); }
    },

    async _onSector(s, auto = false) {
        this._markActive('sector', s.id);
        this.state.sectorId = s.id;
        this._setBc('sector', s.name);
        this._resetFrom('promotion');
        await this._loadPromotions();
    },

    async _loadPromotions() {
        this._setLoading('promotion', true);
        this.$('xp-step-promotion').classList.remove('xp-step--locked');
        try {
            const d = await this.get(`/api/promotions-for-create/${this.state.yearId}/${this.state.sectorId}`);
            this._setLoading('promotion', false);
            this._renderOpts('promotion', d.promotions || [], 'Aucune promotion.', item => this._onPromotion(item));
            if (d.auto && d.promotions?.length) await this._onPromotion(d.promotions[0], true);
        } catch(e) { this._setLoading('promotion', false); }
    },

    async _onPromotion(p, auto = false) {
        this._markActive('promotion', p.id);
        this.state.promotionId = p.id;
        this._setBc('promotion', p.name);
        this._resetFrom('classroom');
        await this._loadClassrooms();
    },

    async _loadClassrooms() {
        this._setLoading('classroom', true);
        this.$('xp-step-classroom').classList.remove('xp-step--locked');
        try {
            const d = await this.get(`/api/classrooms-for-create/${this.state.promotionId}?year_id=${this.state.yearId}`);
            this._setLoading('classroom', false);
            this._renderOpts('classroom', d.classrooms || [], 'Aucune classe.', item => this._onClassroom(item));
            if (d.auto && d.classrooms?.length) await this._onClassroom(d.classrooms[0], true);
        } catch(e) { this._setLoading('classroom', false); }
    },

    _onClassroom(c, auto = false) {
        this._markActive('classroom', c.id);
        this.state.classroomId = c.id;
        this.$('xp-classroom-id').value = c.id;
        this._setBc('classroom', c.name);
        this._updateSubmit();
    },

    _renderOpts(step, items, empty, onClick) {
        const el = this.$(`xp-opts-${step}`);
        if (!items.length) {
            el.innerHTML = `<div class="xp-step-ph">${empty}</div>`;
            return;
        }
        el.innerHTML = items.map(i => `
            <button type="button" class="xp-opt-card" data-id="${i.id}">
                <span>${i.name}</span><span class="xp-opt-arrow">›</span>
            </button>
        `).join('');
        el.querySelectorAll('.xp-opt-card').forEach(btn => {
            btn.addEventListener('click', () => {
                const item = items.find(i => String(i.id) === btn.dataset.id);
                if (item) onClick(item);
            });
        });
    },

    _markActive(step, id) {
        const el = this.$(`xp-opts-${step}`);
        el?.querySelectorAll('.xp-opt-card').forEach(btn => {
            btn.classList.toggle('xp-opt-card--active', String(btn.dataset.id) === String(id));
        });
    },

    _setLoading(step, on) {
        const el = this.$(`xp-loader-${step}`);
        if (el) el.style.display = on ? 'flex' : 'none';
    },

    _setBc(part, label) {
        const el = this.$(`xp-bc-${part}-label`);
        if (el) el.textContent = label;
        const item = this.$(`xp-bc-${part}`);
        if (item) item.classList.add('active');
    },

    _resetFrom(step) {
        const steps = ['sector', 'promotion', 'classroom'];
        const idx = steps.indexOf(step);
        for (let i = idx; i < steps.length; i++) {
            const s = steps[i];
            this.$(`xp-step-${s}`)?.classList.add('xp-step--locked');
            const opts = this.$(`xp-opts-${s}`);
            if (opts) opts.innerHTML = `<div class="xp-step-ph">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                Choisissez d'abord ${s === 'promotion' ? 'une filière' : 'une promotion'}
            </div>`;
            const bcEl = this.$(`xp-bc-${s}`);
            if (bcEl) { bcEl.classList.remove('active'); }
            const lbEl = this.$(`xp-bc-${s}-label`);
            if (lbEl) lbEl.textContent = s === 'promotion' ? 'Promotion' : 'Classe';
            if (s === 'promotion') { this.state.promotionId = null; }
            if (s === 'classroom') { this.state.classroomId = null; this.$('xp-classroom-id').value = ''; }
        }
        this._updateSubmit();
    },

    _updateSubmit() {
        const sem = document.querySelector('input[name="semester"]:checked');
        this.$('xp-btn-submit').disabled = !(this.state.classroomId && sem);
    },

    reset() {
        this.state = { yearId: this.state.yearId, sectorId: null, promotionId: null, classroomId: null };
        this.$('xp-classroom-id').value = '';
        document.querySelectorAll('input[name="semester"]').forEach(r => r.checked = false);
        this.$('xp-sem-err').style.display = 'none';
        this.$('xp-btn-submit').disabled = true;
        // Reset breadcrumb
        ['sector','promotion','classroom'].forEach(s => {
            const item = this.$(`xp-bc-${s}`); if (item) item.classList.remove('active');
            const lbl  = this.$(`xp-bc-${s}-label`);
            if (lbl) lbl.textContent = s === 'sector' ? 'Filière' : s === 'promotion' ? 'Promotion' : 'Classe';
        });
        // Relancer cascade depuis filières
        this._resetFrom('sector');
        this.$('xp-step-sector').classList.remove('xp-step--locked');
        this._loadSectors();
    }
};

document.addEventListener('DOMContentLoaded', () => XP.init());
</script>
@endsection
