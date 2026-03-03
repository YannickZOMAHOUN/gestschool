@extends('layouts.template')

@section('another_CSS')
<style>
:root {
    --bg-base:     #0f1117;
    --bg-card:     #161b27;
    --bg-elevated: #1e2535;
    --border:      #2a3247;
    --accent:      #4f7df3;
    --accent-dim:  rgba(79,125,243,.12);
    --accent-glow: rgba(79,125,243,.35);
    --success:     #22c55e;
    --danger:      #ef4444;
    --text-primary:   #e8eaf0;
    --text-secondary: #7b8399;
    --text-muted:     #4a5268;
    --radius: 10px; --radius-sm: 6px; --transition: .18s ease;
}
body { background: var(--bg-base) !important; color: var(--text-primary) !important; }
.dk-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; margin-bottom: 1.5rem; }
.dk-card-header { display:flex; align-items:center; justify-content:space-between; padding:.9rem 1.25rem; border-bottom:1px solid var(--border); background:var(--bg-elevated); }
.dk-card-header h5 { margin:0; font-size:.92rem; font-weight:700; color:var(--text-primary); }
.dk-card-body { padding:1.5rem; }
.form-label { font-size:.72rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:.35rem; display:block; }
.form-select, .form-control { background:var(--bg-base) !important; border:1px solid var(--border) !important; color:var(--text-primary) !important; border-radius:var(--radius-sm) !important; padding:.55rem .85rem !important; font-size:.875rem !important; transition:border-color var(--transition), box-shadow var(--transition); }
.form-select:focus, .form-control:focus { border-color:var(--accent) !important; box-shadow:0 0 0 3px var(--accent-glow) !important; outline:none !important; }
.form-select:disabled, .form-control:disabled { background:var(--bg-elevated) !important; color:var(--text-muted) !important; cursor:not-allowed; }
.form-select option { background:var(--bg-card); }
.btn-primary-dk { background:var(--accent); border:none; color:#fff; padding:.5rem 1.3rem; border-radius:20px; font-size:.85rem; font-weight:700; display:inline-flex; align-items:center; gap:.4rem; transition:all var(--transition); cursor:pointer; }
.btn-primary-dk:hover { background:#3d6ce0; box-shadow:0 4px 14px var(--accent-glow); transform:translateY(-1px); color:#fff; }
.btn-primary-dk:disabled { opacity:.5; cursor:not-allowed; transform:none; }
.btn-ghost-dk { background:transparent; border:1px solid var(--border); color:var(--text-secondary); padding:.5rem 1.3rem; border-radius:20px; font-size:.85rem; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; transition:all var(--transition); cursor:pointer; }
.btn-ghost-dk:hover { border-color:var(--text-secondary); color:var(--text-primary); }
.btn-danger-dk { background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.22); color:var(--danger); padding:.3rem .7rem; border-radius:var(--radius-sm); font-size:.78rem; font-weight:700; display:inline-flex; align-items:center; gap:.3rem; transition:all var(--transition); cursor:pointer; }
.btn-danger-dk:hover { background:rgba(239,68,68,.2); }
.dk-table { width:100%; border-collapse:collapse; }
.dk-table thead th { background:var(--bg-base); color:var(--text-muted); font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; padding:.65rem 1rem; border-bottom:1px solid var(--border); }
.dk-table tbody td { padding:.7rem 1rem; border-bottom:1px solid rgba(42,50,71,.5); font-size:.875rem; vertical-align:middle; }
.dk-table tbody tr:last-child td { border-bottom:none; }
.dk-table tbody tr:hover td { background:var(--accent-dim); }
.dk-alert { border-radius:var(--radius-sm); padding:.7rem 1rem; font-size:.85rem; display:flex; align-items:center; gap:.6rem; margin-bottom:1rem; }
.dk-alert-success { background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.25); color:var(--success); }
.dk-alert-danger  { background:rgba(239,68,68,.1);  border:1px solid rgba(239,68,68,.25);  color:var(--danger); }
.empty-state { text-align:center; padding:3rem 1rem; color:var(--text-muted); }
.empty-state i { font-size:1.8rem; margin-bottom:.6rem; display:block; opacity:.3; }
.modal-content { background:var(--bg-card); border:1px solid var(--border); color:var(--text-primary); }
.modal-header, .modal-footer { border-color:var(--border) !important; }
.btn-close { filter:invert(1) opacity(.5); }
#liveClock { font-size:.75rem; color:var(--text-muted); }
.spinner-border { color:var(--accent) !important; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4" style="max-width:1180px;">

    <div class="d-flex align-items-start justify-content-between mb-4">
        <div>
            <h4 style="margin:0;font-size:1.05rem;font-weight:700;">
                <i class="fas fa-chalkboard-teacher me-2" style="color:var(--accent)"></i>Affectation des enseignants
            </h4>
            <p style="margin:.2rem 0 0;font-size:.78rem;color:var(--text-muted);">
                Par classe et par année — une matière = un seul enseignant
            </p>
        </div>
        <span id="liveClock"></span>
    </div>

    <div id="alertContainer"></div>

    {{-- Formulaire --}}
    <div class="dk-card">
        <div class="dk-card-header">
            <h5><i class="fas fa-plus-circle me-2" style="color:var(--accent)"></i>Nouvelle affectation</h5>
        </div>
        <div class="dk-card-body">
            <form id="assignmentForm" method="POST" action="{{ route('teacher-assignments.store') }}">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label">Année</label>
                        <select name="year_id" id="year_id" class="form-select" required>
                            <option value="">— Choisir —</option>
                            @foreach($years as $y)
                                <option value="{{ $y->id }}">{{ $y->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label">Filière</label>
                        <select name="sector_id" id="sector_id" class="form-select" disabled required>
                            <option value="">—</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label">Promotion</label>
                        <select name="promotion_id" id="promotion_id" class="form-select" disabled required>
                            <option value="">—</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label">Classe</label>
                        <select name="classroom_id" id="classroom_id" class="form-select" disabled required>
                            <option value="">—</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">Matière</label>
                        <select name="subject_id" id="subject_id" class="form-select" disabled required>
                            <option value="">—</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Enseignant</label>
                        <select name="teacher_id" id="teacher_id" class="form-select" required>
                            <option value="">— Choisir un enseignant —</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} {{ $t->surname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex justify-content-end gap-2">
                        <button type="reset" class="btn-ghost-dk"><i class="fas fa-undo"></i></button>
                        <button type="submit" id="submitBtn" class="btn-primary-dk">
                            <i class="fas fa-save"></i> Sauvegarder
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="dk-card">
        <div class="dk-card-header">
            <h5><i class="fas fa-list-ul me-2" style="color:var(--accent)"></i>Affectations existantes</h5>
            <span id="classLabel" style="font-size:.78rem;color:var(--text-muted);"></span>
        </div>
        <div style="overflow-x:auto;">
            <table class="dk-table">
                <thead>
                    <tr>
                        <th>Matière</th>
                        <th>Enseignant</th>
                        <th style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="assignmentsTable">
                    <tr><td colspan="3"><div class="empty-state"><i class="fas fa-school"></i><p>Sélectionnez une classe pour voir les affectations</p></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal suppression --}}
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="font-size:.9rem;font-weight:700;">
                    <i class="fas fa-exclamation-triangle me-2" style="color:var(--danger)"></i>Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="font-size:.875rem;color:var(--text-secondary);">
                Voulez-vous vraiment retirer cette affectation ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button class="btn-ghost-dk" data-bs-dismiss="modal">Annuler</button>
                <button id="confirmDelete" class="btn-danger-dk" style="padding:.5rem 1.2rem;border-radius:20px;">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('another_JS')
<script>
// Horloge
(function tick(){
    document.getElementById('liveClock').textContent =
        new Date().toLocaleString('fr-FR',{weekday:'short',day:'numeric',month:'short',hour:'2-digit',minute:'2-digit',second:'2-digit'});
    setTimeout(tick,1000);
})();

const $  = id => document.getElementById(id);
const year  = $('year_id'), sector  = $('sector_id'), promo = $('promotion_id'),
      room  = $('classroom_id'), subj = $('subject_id'), tbody = $('assignmentsTable');
const confirmModal = new bootstrap.Modal($('confirmModal'));
let deleteId = null;

year.onchange  = loadSectors;
sector.onchange = loadPromotions;
promo.onchange  = () => { loadSubjects(); loadClassrooms(); };
room.onchange   = loadAssignments;

$('assignmentForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = $('submitBtn');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    const res  = await fetch(this.action, { method:'POST', body:new FormData(this), headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} });
    const data = await res.json();
    toast(data.success?'success':'danger', data.message);
    if (data.success) { loadAssignments(); subj.value=''; $('teacher_id').value=''; }
    btn.disabled=false; btn.innerHTML='<i class="fas fa-save"></i> Sauvegarder';
});

document.addEventListener('click', e => {
    if (e.target.closest('.del-btn')) { deleteId=e.target.closest('.del-btn').dataset.id; confirmModal.show(); }
});

$('confirmDelete').onclick = async function() {
    if (!deleteId) return;
    this.disabled=true; this.innerHTML='<i class="fas fa-spinner fa-spin"></i>';
    const res  = await fetch(`/teacher-assignments/${deleteId}`, { method:'DELETE', headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content} });
    const data = await res.json();
    if (data.success) { document.querySelector(`tr[data-id="${deleteId}"]`)?.remove(); toast('success',data.message); if(!tbody.querySelector('tr[data-id]')) empty('Aucune affectation pour cette classe.'); }
    else toast('danger',data.message);
    confirmModal.hide(); this.disabled=false; this.innerHTML='<i class="fas fa-trash"></i> Supprimer'; deleteId=null;
};

async function loadSectors(){
    if(!year.value) return;
    reset([sector,promo,room,subj]);
    sector.innerHTML='<option>Chargement…</option>';
    const d = await get(`/teacher-assignments/get-sectors/${year.value}`);
    sector.innerHTML='<option value="">— Filière —</option>'+d.map(s=>`<option value="${s.id}">${s.name}</option>`).join('');
    sector.disabled=false; empty('Sélectionnez une classe pour voir les affectations.');
}

async function loadPromotions(){
    if(!sector.value) return;
    reset([promo,room,subj]);
    promo.innerHTML='<option>Chargement…</option>';
    const d = await get(`/teacher-assignments/get-promotions/${year.value}/${sector.value}`);
    promo.innerHTML='<option value="">— Promotion —</option>'+d.map(p=>`<option value="${p.id}">${p.name}</option>`).join('');
    promo.disabled=false;
}

async function loadSubjects(){
    subj.innerHTML='<option>Chargement…</option>'; subj.disabled=true;
    const d = await get(`/teacher-assignments/get-subjects/${year.value}/${sector.value}/${promo.value}`);
    subj.innerHTML='<option value="">— Matière —</option>'+d.map(s=>`<option value="${s.id}">${s.name}</option>`).join('');
    subj.disabled=false;
}

async function loadClassrooms(){
    room.innerHTML='<option>Chargement…</option>'; room.disabled=true;
    const d = await get(`/teacher-assignments/get-classes/${year.value}/${sector.value}/${promo.value}`);
    room.innerHTML='<option value="">— Classe —</option>'+d.map(c=>`<option value="${c.id}">${c.name}</option>`).join('');
    room.disabled=false;
}

async function loadAssignments(){
    if(!year.value||!room.value) return;
    $('classLabel').textContent = room.options[room.selectedIndex]?.text??'';
    tbody.innerHTML='<tr><td colspan="3" style="text-align:center;padding:2rem"><div class="spinner-border spinner-border-sm"></div></td></tr>';
    const data = await get(`/teacher-assignments/get-assignments/${year.value}/${room.value}`);
    const list = data.assignments ?? [];
    if(!list.length){ empty('Aucune affectation pour cette classe.'); return; }
    tbody.innerHTML = list.map(a=>`
        <tr data-id="${a.id}">
            <td>${a.subject}</td>
            <td>${a.teacher}</td>
            <td><button class="btn-danger-dk del-btn" data-id="${a.id}"><i class="fas fa-times"></i> Retirer</button></td>
        </tr>`).join('');
}

async function get(url){ try{ const r=await fetch(url); return await r.json(); }catch{ return []; } }

function reset(selects){ selects.forEach(s=>{ s.innerHTML='<option value="">—</option>'; s.disabled=true; }); }

function empty(msg){ tbody.innerHTML=`<tr><td colspan="3"><div class="empty-state"><i class="fas fa-inbox"></i><p>${msg}</p></div></td></tr>`; }

function toast(type, msg){
    const d=document.createElement('div');
    d.className=`dk-alert dk-alert-${type}`;
    d.innerHTML=`<i class="fas fa-${type==='success'?'check-circle':'exclamation-triangle'}"></i> ${msg}`;
    const c=$('alertContainer'); c.innerHTML=''; c.appendChild(d);
    setTimeout(()=>d.remove(),5000);
}
</script>
@endsection
