@extends('layouts.template')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<div class="promo-wrapper">

    {{-- ── En-tête ──────────────────────────────────────────── --}}
    <div class="promo-header">
        <div class="promo-header-icon">
            <i class="fas fa-layer-group"></i>
        </div>
        <div>
            <h1 class="promo-title">Gestion des Promotions</h1>
            <p class="promo-subtitle">Associez les promotions (2nde, 1ère, Tle) à chaque filière</p>
        </div>
        <div class="promo-header-stats" id="headerStats" style="display:none;">
            <div class="promo-stat">
                <span class="promo-stat-value" id="statChecked">0</span>
                <span class="promo-stat-label">Sélectionnées</span>
            </div>
            <div class="promo-stat-divider"></div>
            <div class="promo-stat">
                <span class="promo-stat-value" id="statLocked">0</span>
                <span class="promo-stat-label">Verrouillées</span>
            </div>
        </div>
    </div>

    {{-- ── Alertes ──────────────────────────────────────────── --}}
    @if (session('success'))
    <div class="promo-alert promo-alert-success">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="promo-alert-close"><i class="fas fa-times"></i></button>
    </div>
    @endif

    @if (session('error'))
    <div class="promo-alert promo-alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" class="promo-alert-close"><i class="fas fa-times"></i></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="promo-alert promo-alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ $errors->first() }}</span>
        <button onclick="this.parentElement.remove()" class="promo-alert-close"><i class="fas fa-times"></i></button>
    </div>
    @endif

    <form action="{{ route('promotionbysector.store') }}" method="POST" id="promoForm">
        @csrf

        {{-- ── Étape 1 : Année ─────────────────────────────── --}}
        <div class="promo-card">
            <div class="promo-card-header">
                <div class="promo-step-badge">1</div>
                <div>
                    <h2 class="promo-card-title">Année scolaire</h2>
                    <p class="promo-card-desc">Sélectionnez l'année pour charger les filières</p>
                </div>
            </div>

            <div class="promo-field">
                <label class="promo-label" for="year">
                    <i class="fas fa-calendar-alt"></i> Année scolaire
                </label>
                <div class="promo-select-wrap">
                    <select name="year" id="year" class="promo-select" required>
                        <option value="">— Choisissez une année —</option>
                        @foreach ($years as $year)
                            <option value="{{ $year->id }}">{{ $year->year }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down promo-select-arrow"></i>
                    <span class="promo-select-loader" id="yearLoader" style="display:none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </div>
            </div>
        </div>

        {{-- ── Étape 2 : Promotions par filière ────────────── --}}
        <div class="promo-card" id="sectorsCard" style="display:none;">
            <div class="promo-card-header">
                <div class="promo-step-badge">2</div>
                <div style="flex:1;">
                    <h2 class="promo-card-title">Promotions par filière</h2>
                    <p class="promo-card-desc">Cochez les promotions à activer pour chaque filière</p>
                </div>
                {{-- Légende --}}
                <div class="promo-legend">
                    <span class="promo-legend-item promo-legend-new">
                        <i class="fas fa-circle"></i> Nouvelle
                    </span>
                    <span class="promo-legend-item promo-legend-existing">
                        <i class="fas fa-circle"></i> Existante
                    </span>
                    <span class="promo-legend-item promo-legend-locked">
                        <i class="fas fa-lock"></i> Verrouillée
                    </span>
                </div>
            </div>

            {{-- Actions groupées --}}
            <div class="promo-bulk-actions" id="bulkActions">
                <button type="button" class="promo-bulk-btn" id="selectAll">
                    <i class="fas fa-check-square"></i> Tout sélectionner
                </button>
                <button type="button" class="promo-bulk-btn promo-bulk-danger" id="unselectAll">
                    <i class="fas fa-square"></i> Tout décocher
                </button>
            </div>

            {{-- Grille des filières --}}
            <div id="sectorsGrid"></div>
        </div>

        {{-- ── Actions ─────────────────────────────────────── --}}
        <div class="promo-actions" id="actionsBar" style="display:none;">
            <button type="button" class="promo-btn-secondary" onclick="resetForm()">
                <i class="fas fa-redo"></i> Réinitialiser
            </button>
            <button type="submit" class="promo-btn-primary" id="submitBtn" disabled>
                <i class="fas fa-save"></i>
                <span id="submitLabel">Enregistrer les promotions</span>
                <span id="submitLoader" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span>
            </button>
        </div>

    </form>
</div>

<style>
/* ================================================================
   ROOT & FONTS
   ================================================================ */
.promo-wrapper, .promo-wrapper * {
    font-family: 'DM Sans', sans-serif;
    box-sizing: border-box;
}

:root {
    --promo-bg:          #07090f;
    --promo-surface:     #0e1320;
    --promo-surface-2:   #131929;
    --promo-surface-3:   #192033;
    --promo-border:      rgba(255,255,255,.06);
    --promo-border-2:    rgba(255,255,255,.1);

    /* Violet/emerald — palette distincte */
    --promo-accent:      #a78bfa;
    --promo-accent-2:    #7c3aed;
    --promo-accent-bg:   rgba(167,139,250,.09);
    --promo-accent-glow: rgba(167,139,250,.28);

    --promo-success:     #34d399;
    --promo-success-bg:  rgba(52,211,153,.09);
    --promo-warn:        #fbbf24;
    --promo-warn-bg:     rgba(251,191,36,.09);
    --promo-danger:      #f87171;
    --promo-danger-bg:   rgba(248,113,113,.09);
    --promo-new:         #a78bfa;
    --promo-new-bg:      rgba(167,139,250,.09);

    --promo-text:        #bac4d6;
    --promo-text-bright: #e2e8f0;
    --promo-muted:       #2d3a52;
    --promo-muted-2:     #475872;

    --promo-radius:      16px;
    --promo-radius-sm:   10px;
    --promo-radius-xs:   7px;
    --promo-transition:  .2s cubic-bezier(.4,0,.2,1);
}

/* ================================================================
   WRAPPER
   ================================================================ */
.promo-wrapper {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem 1.5rem 5rem;
    color: var(--promo-text);
}

/* ================================================================
   HEADER
   ================================================================ */
.promo-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}
.promo-header-icon {
    width: 50px; height: 50px;
    background: var(--promo-accent-bg);
    border: 1px solid rgba(167,139,250,.2);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    color: var(--promo-accent);
    box-shadow: 0 0 20px var(--promo-accent-glow);
    flex-shrink: 0;
}
.promo-title {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--promo-text-bright);
    margin: 0 0 .2rem;
    letter-spacing: -.025em;
}
.promo-subtitle {
    font-size: .8rem;
    color: var(--promo-muted-2);
    margin: 0;
}
.promo-header-stats {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--promo-surface);
    border: 1px solid var(--promo-border-2);
    border-radius: var(--promo-radius-sm);
    padding: .65rem 1.1rem;
    margin-left: auto;
    animation: fadeIn .3s ease;
}
.promo-stat { text-align: center; }
.promo-stat-value {
    display: block;
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--promo-accent);
    letter-spacing: -.03em;
    line-height: 1;
}
.promo-stat-label {
    font-size: .68rem;
    color: var(--promo-muted-2);
    text-transform: uppercase;
    letter-spacing: .07em;
    font-weight: 600;
}
.promo-stat-divider { width: 1px; height: 28px; background: var(--promo-border-2); }

/* ================================================================
   ALERTS
   ================================================================ */
.promo-alert {
    display: flex; align-items: center; gap: .75rem;
    padding: .85rem 1rem; border-radius: var(--promo-radius-xs);
    margin-bottom: 1.25rem; font-size: .82rem; font-weight: 500;
    animation: slideDown .25s ease;
}
@keyframes slideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
@keyframes fadeIn    { from { opacity:0; } to { opacity:1; } }
@keyframes fadeUp    { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
.promo-alert span { flex: 1; }
.promo-alert-close { background:transparent; border:none; cursor:pointer; color:inherit; opacity:.6; padding:0; font-size:.8rem; }
.promo-alert-close:hover { opacity:1; }
.promo-alert-success { background:var(--promo-success-bg); border:1px solid rgba(52,211,153,.2);  color:var(--promo-success); }
.promo-alert-danger  { background:var(--promo-danger-bg);  border:1px solid rgba(248,113,113,.2); color:var(--promo-danger); }

/* ================================================================
   CARD
   ================================================================ */
.promo-card {
    background: var(--promo-surface);
    border: 1px solid var(--promo-border);
    border-radius: var(--promo-radius);
    padding: 1.5rem;
    margin-bottom: 1.25rem;
    animation: fadeUp .3s ease;
}
.promo-card-header {
    display: flex; align-items: center; gap: .85rem;
    margin-bottom: 1.4rem; flex-wrap: wrap;
}
.promo-step-badge {
    width: 30px; height: 30px; border-radius: 50%;
    background: linear-gradient(135deg, var(--promo-accent), var(--promo-accent-2));
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem; font-weight: 800; color: #fff; flex-shrink: 0;
    box-shadow: 0 4px 12px var(--promo-accent-glow);
}
.promo-card-title { font-size: .98rem; font-weight: 700; color: var(--promo-text-bright); margin: 0 0 .15rem; letter-spacing: -.01em; }
.promo-card-desc  { font-size: .75rem; color: var(--promo-muted-2); margin: 0; }

/* ================================================================
   FIELD & SELECT
   ================================================================ */
.promo-field { max-width: 380px; }
.promo-label {
    display: flex; align-items: center; gap: .4rem;
    font-size: .73rem; font-weight: 700; color: var(--promo-muted-2);
    text-transform: uppercase; letter-spacing: .07em; margin-bottom: .45rem;
}
.promo-label i { color: var(--promo-accent); font-size: .7rem; }
.promo-select-wrap { position: relative; }
.promo-select {
    width: 100%; padding: .72rem 2.2rem .72rem .9rem;
    background: var(--promo-surface-2); border: 1px solid var(--promo-border-2);
    border-radius: var(--promo-radius-xs); color: var(--promo-text);
    font-size: .84rem; font-family: inherit; appearance: none; cursor: pointer; outline: none;
    transition: border-color var(--promo-transition), box-shadow var(--promo-transition);
}
.promo-select:focus { border-color: var(--promo-accent); box-shadow: 0 0 0 3px rgba(167,139,250,.15); }
.promo-select option { background: var(--promo-surface-2); }
.promo-select-arrow { position: absolute; right: .8rem; top: 50%; transform: translateY(-50%); font-size: .62rem; color: var(--promo-muted); pointer-events: none; }
.promo-select-loader { position: absolute; right: .8rem; top: 50%; transform: translateY(-50%); color: var(--promo-accent); font-size: .78rem; }

/* ================================================================
   LÉGENDE
   ================================================================ */
.promo-legend { display: flex; align-items: center; gap: .85rem; flex-wrap: wrap; }
.promo-legend-item { display: flex; align-items: center; gap: .35rem; font-size: .71rem; font-weight: 600; color: var(--promo-muted-2); }
.promo-legend-item i { font-size: .45rem; }
.promo-legend-new      i { color: var(--promo-new);     font-size: .45rem; }
.promo-legend-existing i { color: var(--promo-success); font-size: .45rem; }
.promo-legend-locked   i { color: var(--promo-warn);    font-size: .6rem; }

/* ================================================================
   BULK ACTIONS
   ================================================================ */
.promo-bulk-actions {
    display: flex; gap: .6rem; margin-bottom: 1.1rem; flex-wrap: wrap;
}
.promo-bulk-btn {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .42rem .9rem;
    background: var(--promo-accent-bg); color: var(--promo-accent);
    border: 1px solid rgba(167,139,250,.2); border-radius: var(--promo-radius-xs);
    font-size: .76rem; font-family: inherit; font-weight: 600; cursor: pointer;
    transition: all var(--promo-transition);
}
.promo-bulk-btn:hover { background: rgba(167,139,250,.16); border-color: rgba(167,139,250,.4); }
.promo-bulk-danger {
    background: var(--promo-danger-bg); color: var(--promo-danger);
    border-color: rgba(248,113,113,.2);
}
.promo-bulk-danger:hover { background: rgba(248,113,113,.16); border-color: rgba(248,113,113,.4); }

/* ================================================================
   SECTOR ROW
   ================================================================ */
.promo-sector-row {
    background: var(--promo-surface-2);
    border: 1px solid var(--promo-border);
    border-radius: var(--promo-radius-sm);
    padding: 1rem 1.2rem;
    margin-bottom: .65rem;
    display: flex; align-items: center; gap: 1.25rem;
    animation: fadeUp .25s ease both;
    transition: border-color var(--promo-transition);
    flex-wrap: wrap;
}
.promo-sector-row:hover { border-color: var(--promo-border-2); }

.promo-sector-name {
    font-size: .88rem; font-weight: 700;
    color: var(--promo-text-bright); letter-spacing: -.01em;
    min-width: 120px; flex-shrink: 0;
}

.promo-checkboxes {
    display: flex; gap: .65rem; flex-wrap: wrap; flex: 1;
}

/* Checkbox item */
.promo-check-item {
    position: relative;
}
.promo-check-item input[type="checkbox"] {
    position: absolute; opacity: 0; width: 0; height: 0;
}
.promo-check-label {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .45rem .9rem;
    background: var(--promo-surface-3);
    border: 1px solid var(--promo-border-2);
    border-radius: 20px;
    font-size: .8rem; font-weight: 500;
    color: var(--promo-muted-2);
    cursor: pointer;
    user-select: none;
    transition: all var(--promo-transition);
}
.promo-check-label .check-icon { font-size: .65rem; opacity: 0; transition: opacity var(--promo-transition); }

/* Checked state */
.promo-check-item input:checked + .promo-check-label {
    background: var(--promo-new-bg);
    border-color: rgba(167,139,250,.35);
    color: var(--promo-accent);
    font-weight: 600;
}
.promo-check-item input:checked + .promo-check-label .check-icon { opacity: 1; }

/* Existing (coché + déjà en base) */
.promo-check-item.is-existing input:checked + .promo-check-label {
    background: var(--promo-success-bg);
    border-color: rgba(52,211,153,.3);
    color: var(--promo-success);
}

/* Locked */
.promo-check-item.is-locked .promo-check-label {
    background: var(--promo-warn-bg);
    border-color: rgba(251,191,36,.25);
    color: var(--promo-warn);
    cursor: not-allowed;
    opacity: .85;
}
.promo-check-item.is-locked input:checked + .promo-check-label {
    background: var(--promo-warn-bg);
    border-color: rgba(251,191,36,.35);
    color: var(--promo-warn);
}
.promo-check-item.is-locked .lock-icon { font-size: .6rem; }

/* Hover sur non-locked */
.promo-check-item:not(.is-locked) .promo-check-label:hover {
    border-color: rgba(167,139,250,.3);
    color: var(--promo-text);
    transform: translateY(-1px);
}

/* ================================================================
   ACTIONS
   ================================================================ */
.promo-actions {
    display: flex; justify-content: flex-end; gap: .75rem; margin-top: .5rem;
    animation: fadeUp .3s .1s ease both;
}
.promo-btn-primary {
    display: inline-flex; align-items: center; gap: .6rem;
    padding: .72rem 1.5rem;
    background: linear-gradient(135deg, var(--promo-accent), var(--promo-accent-2));
    color: #fff; border: none; border-radius: var(--promo-radius-xs);
    font-size: .85rem; font-family: inherit; font-weight: 700; cursor: pointer;
    box-shadow: 0 4px 16px var(--promo-accent-glow);
    transition: all var(--promo-transition); letter-spacing: -.01em;
}
.promo-btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 24px var(--promo-accent-glow); }
.promo-btn-primary:disabled { opacity: .45; cursor: not-allowed; transform: none; }
.promo-btn-secondary {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .72rem 1.2rem; background: transparent; color: var(--promo-muted-2);
    border: 1px solid var(--promo-border-2); border-radius: var(--promo-radius-xs);
    font-size: .83rem; font-family: inherit; font-weight: 600; cursor: pointer;
    transition: all var(--promo-transition);
}
.promo-btn-secondary:hover { background: rgba(255,255,255,.04); color: var(--promo-text); border-color: rgba(255,255,255,.14); }
</style>

@endsection

@section('another_JS')
<script>
(function () {

    const yearSel     = document.getElementById('year');
    const yearLoader  = document.getElementById('yearLoader');
    const sectorsCard = document.getElementById('sectorsCard');
    const sectorsGrid = document.getElementById('sectorsGrid');
    const actionsBar  = document.getElementById('actionsBar');
    const headerStats = document.getElementById('headerStats');
    const submitBtn   = document.getElementById('submitBtn');
    const selectAll   = document.getElementById('selectAll');
    const unselectAll = document.getElementById('unselectAll');
    const form        = document.getElementById('promoForm');

    /* ── Helpers ────────────────────────────────────────────── */
    function slugify(text) {
        return text.toLowerCase()
            .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
            .replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
    }

    function updateStats() {
        const all    = document.querySelectorAll('.promotion-checkbox');
        const checked = document.querySelectorAll('.promotion-checkbox:checked');
        const locked  = document.querySelectorAll('.promo-check-item.is-locked');
        document.getElementById('statChecked').textContent = checked.length;
        document.getElementById('statLocked').textContent  = locked.length;
        submitBtn.disabled = checked.length === 0;
    }

    /* ── Année → Secteurs ───────────────────────────────────── */
    yearSel.addEventListener('change', () => {
        const yearId = yearSel.value;
        if (!yearId) return;

        sectorsGrid.innerHTML = `
            <div style="text-align:center;padding:1.5rem;color:var(--promo-muted-2);font-size:.83rem;">
                <i class="fas fa-spinner fa-spin" style="margin-right:.5rem;color:var(--promo-accent);"></i>Chargement…
            </div>`;
        sectorsCard.style.display = 'block';
        actionsBar.style.display  = 'none';
        yearLoader.style.display  = 'block';
        document.querySelector('.promo-select-arrow').style.display = 'none';

        fetch(`/promotion-sectors/${yearId}`)
            .then(r => r.json())
            .then(({ sectors, registeredPromotions }) => {

                sectorsGrid.innerHTML = '';

                if (!sectors.length) {
                    sectorsGrid.innerHTML = '<p style="text-align:center;color:var(--promo-muted-2);font-size:.83rem;padding:.5rem;">Aucune filière pour cette année.</p>';
                    return;
                }

                // Indexer les promotions enregistrées : label → { locked, reason }
                const registeredMap = {};
                registeredPromotions.forEach(p => { registeredMap[p.label] = p; });

                sectors.forEach((sector, idx) => {
                    const row = document.createElement('div');
                    row.className = 'promo-sector-row';
                    row.style.animationDelay = `${idx * 0.05}s`;

                    const nameEl = document.createElement('span');
                    nameEl.className   = 'promo-sector-name';
                    nameEl.textContent = sector.name_sector;
                    row.appendChild(nameEl);

                    const checkboxes = document.createElement('div');
                    checkboxes.className = 'promo-checkboxes';

                    ['2nde', '1ère', 'Tle'].forEach(level => {
                        const value    = `${level} ${sector.name_sector}`;
                        const inputId  = slugify(value + '-' + sector.id);
                        const regInfo  = registeredMap[value];
                        const isExisting = !!regInfo;
                        const isLocked   = regInfo?.locked === true;
                        const lockReason = regInfo?.reason;

                        const item  = document.createElement('div');
                        item.className = 'promo-check-item';
                        if (isLocked)   item.classList.add('is-locked');
                        if (isExisting && !isLocked) item.classList.add('is-existing');

                        const input = document.createElement('input');
                        input.type      = 'checkbox';
                        input.className = 'promotion-checkbox';
                        input.name      = `sector_year_ids[${sector.id}][]`;
                        input.id        = inputId;
                        input.value     = value;
                        input.checked   = isExisting;
                        if (isLocked) input.disabled = true; // Ne peut pas être décoché

                        const label = document.createElement('label');
                        label.htmlFor   = inputId;
                        label.className = 'promo-check-label';

                        // Icône de statut
                        const icon = document.createElement('i');
                        icon.className = isLocked
                            ? 'fas fa-lock lock-icon'
                            : 'fas fa-check check-icon';
                        label.appendChild(icon);
                        label.appendChild(document.createTextNode(` ${level}`));

                        // Tooltip raison du verrouillage
                        if (isLocked) {
                            label.title = lockReason === 'notes'
                                ? 'Verrouillée : contient des notes'
                                : 'Verrouillée : des classes lui sont associées';
                        }

                        item.appendChild(input);
                        item.appendChild(label);
                        checkboxes.appendChild(item);
                    });

                    row.appendChild(checkboxes);
                    sectorsGrid.appendChild(row);
                });

                actionsBar.style.display  = 'flex';
                headerStats.style.display = 'flex';
                updateStats();
            })
            .finally(() => {
                yearLoader.style.display = 'none';
                document.querySelector('.promo-select-arrow').style.display = '';
            });
    });

    /* ── Tout sélectionner / décocher ───────────────────────── */
    selectAll.addEventListener('click', () => {
        document.querySelectorAll('.promotion-checkbox:not(:disabled)').forEach(cb => cb.checked = true);
        updateStats();
    });
    unselectAll.addEventListener('click', () => {
        document.querySelectorAll('.promotion-checkbox:not(:disabled)').forEach(cb => cb.checked = false);
        updateStats();
    });

    /* ── Changement d'une checkbox ──────────────────────────── */
    document.addEventListener('change', e => {
        if (e.target.classList.contains('promotion-checkbox')) updateStats();
    });

    /* ── Submit loader ──────────────────────────────────────── */
    form.addEventListener('submit', () => {
        document.getElementById('submitLabel').style.display = 'none';
        document.getElementById('submitLoader').style.display = 'inline';
    });

    /* ── Reset ──────────────────────────────────────────────── */
    window.resetForm = function () {
        yearSel.value = '';
        sectorsCard.style.display = 'none';
        actionsBar.style.display  = 'none';
        headerStats.style.display = 'none';
        sectorsGrid.innerHTML     = '';
    };

})();
</script>
@endsection
