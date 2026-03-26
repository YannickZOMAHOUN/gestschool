@extends('layouts.template')

@section('breadcrumb', 'Professeurs Principaux')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,600;0,700&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=JetBrains+Mono:wght@400;500;600&display=swap');
:root{--sg-bg:#f6f7f3;--sg-surface:#fff;--sg-surface-2:#f1f3ee;--sg-surface-3:#e8ebe3;--sg-accent:#3a6b35;--sg-accent-mid:#5a9a52;--sg-accent-light:#eef4ec;--sg-accent-bg:rgba(58,107,53,.07);--sg-accent-bg2:rgba(58,107,53,.13);--sg-accent-glow:rgba(58,107,53,.18);--sg-text:#1c2318;--sg-text-2:#4a5544;--sg-text-3:#8a9880;--sg-border:#e2e5da;--sg-border-2:#cbd2c2;--sg-border-3:#b5bfaa;--sg-success:#2e7d4f;--sg-success-bg:rgba(46,125,79,.08);--sg-danger:#a84040;--sg-danger-bg:rgba(168,64,64,.07);--sg-warn:#9a6a1a;--sg-warn-bg:rgba(154,106,26,.08);--sg-font-display:'Lora',Georgia,serif;--sg-font-ui:'DM Sans',system-ui,sans-serif;--sg-font-mono:'JetBrains Mono','Fira Mono',monospace;--sg-radius:10px;--sg-radius-sm:7px;--sg-t:.17s cubic-bezier(.4,0,.2,1);}
html[data-theme=dark]{--sg-surface:#1e2419;--sg-surface-2:#252c20;--sg-surface-3:#2d3527;--sg-accent:#6abf60;--sg-accent-mid:#85d47a;--sg-accent-light:rgba(106,191,96,.12);--sg-accent-bg:rgba(106,191,96,.09);--sg-accent-bg2:rgba(106,191,96,.16);--sg-accent-glow:rgba(106,191,96,.20);--sg-text:#e4ead8;--sg-text-2:#adb9a0;--sg-text-3:#627057;--sg-border:rgba(255,255,255,.08);--sg-border-2:rgba(255,255,255,.13);}
.sg,.sg *{font-family:var(--sg-font-ui);box-sizing:border-box;}
.sg{max-width:1100px;margin:0 auto;padding:0 0 4rem;color:var(--sg-text);}
@keyframes sg-up{from{opacity:0;transform:translateY(9px)}to{opacity:1;transform:translateY(0)}}

/* Header */
.sg-hdr{display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--sg-border);flex-wrap:wrap;animation:sg-up .38s ease both;}
.sg-hdr-left{display:flex;align-items:center;gap:.9rem;}
.sg-hdr-icon{width:42px;height:42px;border-radius:var(--sg-radius);flex-shrink:0;display:flex;align-items:center;justify-content:center;background:var(--sg-warn-bg);border:1px solid rgba(154,106,26,.22);color:var(--sg-warn);font-size:.95rem;box-shadow:0 4px 12px rgba(154,106,26,.15);}
html[data-theme=dark] .sg-hdr-icon{border-color:rgba(154,106,26,.28);}
.sg-eyebrow{font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.14em;color:var(--sg-accent);margin-bottom:.15rem;}
.sg-title{font-family:var(--sg-font-display);font-size:1.28rem;font-weight:700;color:var(--sg-text);letter-spacing:-.02em;margin:0;}
.sg-subtitle{font-size:.75rem;color:var(--sg-text-3);margin:.1rem 0 0;}

/* Flash */
.sg-flash{display:flex;align-items:center;gap:.68rem;padding:.72rem .95rem;border-radius:var(--sg-radius-sm);font-size:.79rem;font-weight:500;border:1px solid;margin-bottom:1rem;animation:sg-up .28s ease both;}
.sg-flash span{flex:1;}
.sg-flash button{background:none;border:none;cursor:pointer;color:inherit;opacity:.55;padding:0;}
.sg-flash button:hover{opacity:1;}
.sg-flash-success{background:var(--sg-success-bg);border-color:rgba(46,125,79,.2);color:var(--sg-success);}
.sg-flash-danger{background:var(--sg-danger-bg);border-color:rgba(168,64,64,.2);color:var(--sg-danger);}
#alertContainer .sg-flash{animation:sg-up .2s ease both;}

/* Card */
.sg-card{background:var(--sg-surface);border:1px solid var(--sg-border);border-radius:var(--sg-radius);box-shadow:0 1px 2px rgba(28,35,24,.04);overflow:hidden;margin-bottom:1rem;animation:sg-up .3s ease both;}
.sg-card-hdr{display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.88rem 1.15rem;border-bottom:1px solid var(--sg-border);background:var(--sg-surface-2);flex-wrap:wrap;}
.sg-card-title{font-family:var(--sg-font-display);font-size:.88rem;font-weight:600;color:var(--sg-text);display:flex;align-items:center;gap:.5rem;margin:0;}
.sg-card-title i{font-size:.82rem;}
.sg-card-hint{font-size:.7rem;color:var(--sg-text-3);}
.sg-card-body{padding:1.15rem 1.2rem;}

/* Filters */
.sg-filters{display:grid;grid-template-columns:1fr 1fr auto;gap:.85rem;align-items:end;}
@media(max-width:680px){.sg-filters{grid-template-columns:1fr;}}

/* Field */
.sg-field{display:flex;flex-direction:column;gap:.36rem;}
.sg-label{font-size:.68rem;font-weight:700;color:var(--sg-text-3);text-transform:uppercase;letter-spacing:.1em;display:flex;align-items:center;gap:.35rem;}
.sg-label i{color:var(--sg-accent);font-size:.64rem;}
.sg-sel-wrap{position:relative;}
.sg-sel{width:100%;padding:.58rem 1.9rem .58rem .78rem;background:var(--sg-surface-2);border:1px solid var(--sg-border-2);border-radius:var(--sg-radius-sm);color:var(--sg-text);font-family:var(--sg-font-ui);font-size:.79rem;appearance:none;cursor:pointer;outline:none;transition:border-color var(--sg-t),box-shadow var(--sg-t);}
.sg-sel:focus{border-color:var(--sg-accent);box-shadow:0 0 0 3px var(--sg-accent-bg);}
.sg-sel:disabled{opacity:.42;cursor:not-allowed;}
.sg-sel option{background:var(--sg-surface-2);color:var(--sg-text);}
.sg-sel-arrow{position:absolute;right:.68rem;top:50%;transform:translateY(-50%);font-size:.55rem;color:var(--sg-text-3);pointer-events:none;}

/* Buttons */
.sg-btn{display:inline-flex;align-items:center;gap:.45rem;padding:.5rem 1.05rem;border-radius:var(--sg-radius-sm);font-family:var(--sg-font-ui);font-size:.78rem;font-weight:600;cursor:pointer;border:1px solid;text-decoration:none;transition:all var(--sg-t);white-space:nowrap;}
.sg-btn i{font-size:.72rem;}
.sg-btn-primary{background:var(--sg-accent);border-color:var(--sg-accent);color:#fff;box-shadow:0 3px 10px var(--sg-accent-glow);}
.sg-btn-primary:hover:not(:disabled){background:var(--sg-accent-mid);border-color:var(--sg-accent-mid);transform:translateY(-1px);box-shadow:0 6px 16px var(--sg-accent-glow);}
.sg-btn-primary:disabled{opacity:.42;cursor:not-allowed;transform:none;}
.sg-btn-danger{background:var(--sg-danger-bg);border-color:rgba(168,64,64,.2);color:var(--sg-danger);padding:.3rem .6rem;font-size:.72rem;}
.sg-btn-danger:hover:not(:disabled){background:rgba(168,64,64,.14);border-color:rgba(168,64,64,.35);}
.sg-btn-danger:disabled{opacity:.4;cursor:not-allowed;}
.sg-btn-set{display:inline-flex;align-items:center;gap:.38rem;padding:.3rem .75rem;border-radius:var(--sg-radius-sm);font-family:var(--sg-font-ui);font-size:.73rem;font-weight:600;cursor:pointer;border:1px solid;transition:all var(--sg-t);background:var(--sg-accent-bg);border-color:rgba(58,107,53,.2);color:var(--sg-accent);}
.sg-btn-set:hover:not(:disabled){background:var(--sg-accent-bg2);border-color:rgba(58,107,53,.38);}
.sg-btn-set:disabled{opacity:.4;cursor:not-allowed;}

/* Table */
.sg-tbl-wrap{overflow-x:auto;}
.sg-table{width:100%;border-collapse:collapse;font-size:.78rem;}
.sg-th{padding:.6rem .9rem;font-size:.59rem;text-transform:uppercase;letter-spacing:.12em;color:var(--sg-text-3);border-bottom:1px solid var(--sg-border);font-weight:700;text-align:left;white-space:nowrap;}
.sg-tr{border-bottom:1px solid var(--sg-border);transition:background var(--sg-t);}
.sg-tr:last-child{border-bottom:none;}
.sg-tr:hover td{background:var(--sg-surface-2);}
.sg-td{padding:.75rem .9rem;color:var(--sg-text-2);vertical-align:middle;}
.sg-td-promo{font-size:.72rem;color:var(--sg-text-3);}
.sg-td-class{font-weight:700;color:var(--sg-text);font-size:.84rem;}

/* PP badge */
.sg-pp-badge{display:inline-flex;align-items:center;gap:.35rem;background:var(--sg-success-bg);border:1px solid rgba(46,125,79,.2);color:var(--sg-success);border-radius:20px;padding:.22rem .72rem;font-size:.72rem;font-weight:700;}
.sg-pp-badge i{font-size:.6rem;}
.sg-pp-empty{color:var(--sg-text-3);font-size:.76rem;font-style:italic;}

/* Inline PP select */
.sg-pp-sel{background:var(--sg-surface-2);border:1px solid var(--sg-border-2);color:var(--sg-text);border-radius:var(--sg-radius-sm);padding:.34rem .62rem;font-size:.78rem;font-family:var(--sg-font-ui);min-width:195px;cursor:pointer;outline:none;transition:border-color var(--sg-t),box-shadow var(--sg-t);}
.sg-pp-sel:focus{border-color:var(--sg-accent);box-shadow:0 0 0 3px var(--sg-accent-bg);}
.sg-pp-sel option{background:var(--sg-surface-2);}

/* Empty state */
.sg-empty{text-align:center;padding:2.5rem 1rem;color:var(--sg-text-3);font-size:.79rem;}
.sg-empty-icon{font-size:1.6rem;margin-bottom:.5rem;opacity:.26;}
</style>
@endpush

@section('content')
<div class="sg">

    <div class="sg-hdr">
        <div class="sg-hdr-left">
            <div class="sg-hdr-icon"><i class="fas fa-star"></i></div>
            <div>
                <div class="sg-eyebrow">Administration · Enseignants</div>
                <h1 class="sg-title">Professeurs Principaux</h1>
                <p class="sg-subtitle">Un seul PP par classe — peut ne pas enseigner dans cette classe</p>
            </div>
        </div>
    </div>

    <div id="alertContainer"></div>

    {{-- Filtres --}}
    <div class="sg-card" style="animation-delay:.05s">
        <div class="sg-card-hdr">
            <h2 class="sg-card-title"><i class="fas fa-sliders-h" style="color:var(--sg-accent)"></i> Sélectionner l'année et la filière</h2>
        </div>
        <div class="sg-card-body">
            <div class="sg-filters">
                <div class="sg-field">
                    <label class="sg-label" for="year_id"><i class="fas fa-calendar-alt"></i> Année scolaire</label>
                    <div class="sg-sel-wrap">
                        <select id="year_id" class="sg-sel">
                            <option value="">— Choisir —</option>
                            @foreach($years as $y)
                                <option value="{{ $y->id }}">{{ $y->year }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down sg-sel-arrow"></i>
                    </div>
                </div>
                <div class="sg-field">
                    <label class="sg-label" for="sector_id"><i class="fas fa-sitemap"></i> Filière</label>
                    <div class="sg-sel-wrap">
                        <select id="sector_id" class="sg-sel" disabled>
                            <option value="">—</option>
                        </select>
                        <i class="fas fa-chevron-down sg-sel-arrow"></i>
                    </div>
                </div>
                <div style="display:flex;align-items:flex-end;">
                    <button id="loadBtn" class="sg-btn sg-btn-primary" disabled style="width:100%">
                        <i class="fas fa-search"></i>
                        <span id="loadLabel">Afficher les classes</span>
                        <span id="loadLoader" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau PP --}}
    <div class="sg-card" style="animation-delay:.1s">
        <div class="sg-card-hdr">
            <h2 class="sg-card-title"><i class="fas fa-table" style="color:var(--sg-accent)"></i> Classes et Professeurs Principaux</h2>
            <span class="sg-card-hint">Choisissez un enseignant dans la liste et cliquez « Définir »</span>
        </div>
        <div class="sg-tbl-wrap">
            <table class="sg-table">
                <thead>
                    <tr>
                        <th class="sg-th">Promotion</th>
                        <th class="sg-th">Classe</th>
                        <th class="sg-th">PP actuel</th>
                        <th class="sg-th">Nouveau PP</th>
                        <th class="sg-th" style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="ppTable">
                    <tr><td colspan="5"><div class="sg-empty"><div class="sg-empty-icon">⭐</div>Sélectionnez une année et une filière pour afficher les classes</div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('another_JS')
<script>
const TEACHERS = @json($teachers->map(fn($t) => ['id' => $t->id, 'name' => $t->name . ' ' . $t->surname]));

const yearEl   = document.getElementById('year_id');
const sectorEl = document.getElementById('sector_id');
const loadBtn  = document.getElementById('loadBtn');
const ppTable  = document.getElementById('ppTable');

yearEl.onchange = async () => {
    sectorEl.innerHTML = '<option value="">—</option>';
    sectorEl.disabled  = true;
    loadBtn.disabled   = true;
    if (!yearEl.value) return;
    sectorEl.innerHTML = '<option>Chargement…</option>';
    const d = await get(`/teacher-assignments/get-sectors/${yearEl.value}`);
    sectorEl.innerHTML = '<option value="">— Filière —</option>' + d.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
    sectorEl.disabled  = false;
};
sectorEl.onchange = () => { loadBtn.disabled = !sectorEl.value; };
loadBtn.onclick = loadClasses;

async function loadClasses() {
    document.getElementById('loadLabel').style.display = 'none';
    document.getElementById('loadLoader').style.display = 'inline';
    loadBtn.disabled = true;
    ppTable.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:1.5rem;"><i class="fas fa-spinner fa-spin" style="color:var(--sg-accent);font-size:1rem;"></i></td></tr>`;

    const data = await get(`/principal-teachers/classes/${yearEl.value}/${sectorEl.value}`);

    document.getElementById('loadLabel').style.display = '';
    document.getElementById('loadLoader').style.display = 'none';
    loadBtn.disabled = false;

    if (!data.length) {
        ppTable.innerHTML = `<tr><td colspan="5"><div class="sg-empty"><div class="sg-empty-icon">📭</div>Aucune classe trouvée pour cette filière.</div></td></tr>`;
        return;
    }

    const teacherOptions = '<option value="">— Choisir —</option>' +
        TEACHERS.map(t => `<option value="${t.id}">${t.name}</option>`).join('');

    ppTable.innerHTML = data.map((row, idx) => {
        const ppBadge = row.principal
            ? `<span class="sg-pp-badge"><i class="fas fa-star"></i>${row.principal.name}</span>`
            : `<span class="sg-pp-empty">Aucun</span>`;
        const removeBtn = row.principal
            ? `<button class="sg-btn sg-btn-danger remove-pp-btn" data-classroom="${row.classroom_id}" title="Retirer le PP"><i class="fas fa-times"></i></button>`
            : '';
        return `<tr class="sg-tr" data-classroom="${row.classroom_id}" style="animation:sg-up .3s ease ${idx*30}ms both;">
            <td class="sg-td sg-td-promo">${row.promotion}</td>
            <td class="sg-td sg-td-class">${row.classroom_name}</td>
            <td class="sg-td">${ppBadge}</td>
            <td class="sg-td">
                <select class="sg-pp-sel" data-classroom="${row.classroom_id}">${teacherOptions}</select>
            </td>
            <td class="sg-td" style="text-align:right;display:flex;align-items:center;justify-content:flex-end;gap:.45rem;padding-right:.9rem;">
                <button class="sg-btn-set set-pp-btn" data-classroom="${row.classroom_id}"><i class="fas fa-check"></i> Définir</button>
                ${removeBtn}
            </td>
        </tr>`;
    }).join('');
}

/* Délégation événements */
document.addEventListener('click', async e => {
    const setBtn    = e.target.closest('.set-pp-btn');
    const removeBtn = e.target.closest('.remove-pp-btn');

    if (setBtn) {
        const cid = setBtn.dataset.classroom;
        const sel = document.querySelector(`.sg-pp-sel[data-classroom="${cid}"]`);
        if (!sel?.value) { toast('danger', 'Veuillez sélectionner un enseignant.'); return; }
        setBtn.disabled = true; setBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        const res = await post('/principal-teachers/set', { teacher_id: sel.value, classroom_id: cid, year_id: yearEl.value });
        toast(res.success ? 'success' : 'danger', res.message);
        if (res.success) loadClasses();
        else { setBtn.disabled = false; setBtn.innerHTML = '<i class="fas fa-check"></i> Définir'; }
    }

    if (removeBtn) {
        const cid = removeBtn.dataset.classroom;
        removeBtn.disabled = true; removeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        const res = await post('/principal-teachers/remove', { classroom_id: cid, year_id: yearEl.value });
        toast(res.success ? 'success' : 'danger', res.message);
        if (res.success) loadClasses();
        else { removeBtn.disabled = false; removeBtn.innerHTML = '<i class="fas fa-times"></i>'; }
    }
});

async function get(url) {
    try { const r = await fetch(url); return await r.json(); } catch { return []; }
}
async function post(url, body) {
    try {
        const r = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify(body) });
        return await r.json();
    } catch { return { success: false, message: 'Erreur réseau.' }; }
}
function toast(type, msg) {
    const d = document.createElement('div');
    d.className = `sg-flash sg-flash-${type}`;
    d.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${msg}</span><button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>`;
    const c = document.getElementById('alertContainer'); c.innerHTML = ''; c.appendChild(d);
    setTimeout(() => d.remove(), 5500);
}
</script>
@endsection
