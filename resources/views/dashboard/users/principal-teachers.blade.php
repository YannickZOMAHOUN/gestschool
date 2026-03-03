@extends('layouts.template')

@section('another_CSS')
<style>
:root {
    --bg-base:#0f1117; --bg-card:#161b27; --bg-elevated:#1e2535;
    --border:#2a3247; --accent:#4f7df3; --accent-dim:rgba(79,125,243,.12);
    --accent-glow:rgba(79,125,243,.35); --success:#22c55e; --danger:#ef4444;
    --warning:#f59e0b;
    --text-primary:#e8eaf0; --text-secondary:#7b8399; --text-muted:#4a5268;
    --radius:10px; --radius-sm:6px; --transition:.18s ease;
}
body { background:var(--bg-base) !important; color:var(--text-primary) !important; }
.dk-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; margin-bottom:1.5rem; }
.dk-card-header { display:flex; align-items:center; justify-content:space-between; padding:.9rem 1.25rem; border-bottom:1px solid var(--border); background:var(--bg-elevated); }
.dk-card-header h5 { margin:0; font-size:.92rem; font-weight:700; }
.dk-card-body { padding:1.5rem; }
.form-label { font-size:.72rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:.35rem; display:block; }
.form-select { background:var(--bg-base) !important; border:1px solid var(--border) !important; color:var(--text-primary) !important; border-radius:var(--radius-sm) !important; padding:.55rem .85rem !important; font-size:.875rem !important; transition:border-color var(--transition),box-shadow var(--transition); }
.form-select:focus { border-color:var(--accent) !important; box-shadow:0 0 0 3px var(--accent-glow) !important; outline:none !important; }
.form-select:disabled { background:var(--bg-elevated) !important; color:var(--text-muted) !important; cursor:not-allowed; }
.form-select option { background:var(--bg-card); }
.dk-table { width:100%; border-collapse:collapse; }
.dk-table thead th { background:var(--bg-base); color:var(--text-muted); font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; padding:.65rem 1rem; border-bottom:1px solid var(--border); white-space:nowrap; }
.dk-table tbody td { padding:.75rem 1rem; border-bottom:1px solid rgba(42,50,71,.5); font-size:.875rem; vertical-align:middle; }
.dk-table tbody tr:last-child td { border-bottom:none; }
.dk-table tbody tr:hover td { background:var(--accent-dim); }
.btn-primary-dk { background:var(--accent); border:none; color:#fff; padding:.45rem 1rem; border-radius:20px; font-size:.82rem; font-weight:700; display:inline-flex; align-items:center; gap:.35rem; cursor:pointer; transition:all var(--transition); }
.btn-primary-dk:hover { background:#3d6ce0; box-shadow:0 4px 14px var(--accent-glow); transform:translateY(-1px); }
.btn-primary-dk:disabled { opacity:.45; cursor:not-allowed; transform:none; }
.btn-danger-sm { background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.22); color:var(--danger); padding:.28rem .55rem; border-radius:var(--radius-sm); font-size:.75rem; cursor:pointer; transition:all var(--transition); }
.btn-danger-sm:hover { background:rgba(239,68,68,.2); }
.badge-pp { display:inline-flex; align-items:center; gap:.3rem; background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.22); color:var(--success); border-radius:20px; padding:.2rem .65rem; font-size:.72rem; font-weight:700; }
.badge-empty { color:var(--text-muted); font-size:.8rem; font-style:italic; }
.dk-alert { border-radius:var(--radius-sm); padding:.7rem 1rem; font-size:.85rem; display:flex; align-items:center; gap:.6rem; margin-bottom:1rem; }
.dk-alert-success { background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.25); color:var(--success); }
.dk-alert-danger  { background:rgba(239,68,68,.1);  border:1px solid rgba(239,68,68,.25);  color:var(--danger); }
.empty-state { text-align:center; padding:2.5rem 1rem; color:var(--text-muted); }
.empty-state i { font-size:1.8rem; margin-bottom:.6rem; display:block; opacity:.3; }
.spinner-border { color:var(--accent) !important; }
.pp-inline-select { background:var(--bg-elevated) !important; border:1px solid var(--border) !important; color:var(--text-primary) !important; border-radius:var(--radius-sm); padding:.3rem .6rem; font-size:.82rem; min-width:200px; cursor:pointer; }
.pp-inline-select option { background:var(--bg-card); }
</style>
@endsection

@section('content')
<div class="container-fluid py-4" style="max-width:1100px;">

    <div class="mb-4">
        <h4 style="margin:0;font-size:1.05rem;font-weight:700;">
            <i class="fas fa-star me-2" style="color:var(--warning)"></i>Gestion des Professeurs Principaux
        </h4>
        <p style="margin:.2rem 0 0;font-size:.78rem;color:var(--text-muted);">
            Un seul PP par classe — l'enseignant désigné peut ne pas enseigner dans cette classe
        </p>
    </div>

    <div id="alertContainer"></div>

    {{-- Filtres --}}
    <div class="dk-card">
        <div class="dk-card-header">
            <h5><i class="fas fa-sliders-h me-2" style="color:var(--accent)"></i>Sélectionner l'année et la filière</h5>
        </div>
        <div class="dk-card-body">
            <div class="row g-3 align-items-end">
                <div class="col-sm-6 col-md-4">
                    <label class="form-label">Année scolaire</label>
                    <select id="year_id" class="form-select">
                        <option value="">— Choisir —</option>
                        @foreach($years as $y)
                            <option value="{{ $y->id }}">{{ $y->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-md-4">
                    <label class="form-label">Filière</label>
                    <select id="sector_id" class="form-select" disabled>
                        <option value="">—</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button id="loadBtn" class="btn-primary-dk w-100" disabled>
                        <i class="fas fa-search"></i> Afficher les classes
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau PP --}}
    <div class="dk-card">
        <div class="dk-card-header">
            <h5><i class="fas fa-table me-2" style="color:var(--accent)"></i>Classes et Professeurs Principaux</h5>
            <span style="font-size:.75rem;color:var(--text-muted);">Choisissez directement dans le tableau et cliquez « Définir »</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="dk-table">
                <thead>
                    <tr>
                        <th>Promotion</th>
                        <th>Classe</th>
                        <th>PP actuel</th>
                        <th>Nouveau PP</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="ppTable">
                    <tr><td colspan="5"><div class="empty-state">
                        <i class="fas fa-star"></i>
                        <p>Sélectionnez une année et une filière pour afficher les classes</p>
                    </div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('another_JS')
<script>
const teachers = @json($teachers->map(fn($t) => ['id' => $t->id, 'name' => $t->name . ' ' . $t->surname]));

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
    sectorEl.onchange  = () => { loadBtn.disabled = !sectorEl.value; };
};

loadBtn.onclick = loadClasses;

async function loadClasses() {
    ppTable.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:2rem"><div class="spinner-border spinner-border-sm"></div></td></tr>`;
    const data = await get(`/principal-teachers/classes/${yearEl.value}/${sectorEl.value}`);

    if (!data.length) {
        ppTable.innerHTML = `<tr><td colspan="5"><div class="empty-state"><i class="fas fa-inbox"></i><p>Aucune classe trouvée pour cette filière.</p></div></td></tr>`;
        return;
    }

    const teacherOptions = '<option value="">— Choisir —</option>' +
        teachers.map(t => `<option value="${t.id}">${t.name}</option>`).join('');

    ppTable.innerHTML = data.map(row => {
        const ppBadge = row.principal
            ? `<span class="badge-pp"><i class="fas fa-star"></i> ${row.principal.name}</span>`
            : `<span class="badge-empty">Aucun</span>`;

        const removBtn = row.principal
            ? `<button class="btn-danger-sm remove-pp-btn ms-1" data-classroom="${row.classroom_id}" title="Retirer le PP"><i class="fas fa-times"></i></button>`
            : '';

        return `
        <tr data-classroom="${row.classroom_id}">
            <td style="color:var(--text-secondary);font-size:.82rem;">${row.promotion}</td>
            <td style="font-weight:600;">${row.classroom_name}</td>
            <td>${ppBadge}</td>
            <td>
                <select class="pp-inline-select" data-classroom="${row.classroom_id}">
                    ${teacherOptions}
                </select>
            </td>
            <td style="display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;">
                <button class="btn-primary-dk set-pp-btn" data-classroom="${row.classroom_id}" style="font-size:.78rem;padding:.3rem .85rem;">
                    <i class="fas fa-check"></i> Définir
                </button>
                ${removBtn}
            </td>
        </tr>`;
    }).join('');
}

// Délégation d'événements
document.addEventListener('click', async e => {
    const setPpBtn    = e.target.closest('.set-pp-btn');
    const removePpBtn = e.target.closest('.remove-pp-btn');

    if (setPpBtn) {
        const classroomId = setPpBtn.dataset.classroom;
        const sel         = document.querySelector(`.pp-inline-select[data-classroom="${classroomId}"]`);
        if (!sel?.value) { toast('danger', 'Veuillez sélectionner un enseignant.'); return; }
        setPpBtn.disabled = true;
        const res = await post('/principal-teachers/set', { teacher_id: sel.value, classroom_id: classroomId, year_id: yearEl.value });
        toast(res.success ? 'success' : 'danger', res.message);
        if (res.success) loadClasses();
        setPpBtn.disabled = false;
    }

    if (removePpBtn) {
        const classroomId = removePpBtn.dataset.classroom;
        removePpBtn.disabled = true;
        const res = await post('/principal-teachers/remove', { classroom_id: classroomId, year_id: yearEl.value });
        toast(res.success ? 'success' : 'danger', res.message);
        if (res.success) loadClasses();
        removePpBtn.disabled = false;
    }
});

async function get(url) {
    try { const r = await fetch(url); return await r.json(); }
    catch { return []; }
}

async function post(url, body) {
    try {
        const r = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify(body)
        });
        return await r.json();
    } catch { return { success: false, message: 'Erreur réseau.' }; }
}

function toast(type, msg) {
    const d = document.createElement('div');
    d.className = `dk-alert dk-alert-${type}`;
    d.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${msg}`;
    const c = document.getElementById('alertContainer');
    c.innerHTML = ''; c.appendChild(d);
    setTimeout(() => d.remove(), 5000);
}
</script>
@endsection
