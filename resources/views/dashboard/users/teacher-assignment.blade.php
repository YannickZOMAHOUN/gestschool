@extends('layouts.template')

@section('breadcrumb', 'Affectation des enseignants')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,600;0,700&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=JetBrains+Mono:wght@400;500;600&display=swap');
:root{--sg-bg:#f6f7f3;--sg-surface:#fff;--sg-surface-2:#f1f3ee;--sg-surface-3:#e8ebe3;--sg-accent:#3a6b35;--sg-accent-mid:#5a9a52;--sg-accent-light:#eef4ec;--sg-accent-bg:rgba(58,107,53,.07);--sg-accent-bg2:rgba(58,107,53,.13);--sg-accent-glow:rgba(58,107,53,.18);--sg-text:#1c2318;--sg-text-2:#4a5544;--sg-text-3:#8a9880;--sg-border:#e2e5da;--sg-border-2:#cbd2c2;--sg-border-3:#b5bfaa;--sg-success:#2e7d4f;--sg-success-bg:rgba(46,125,79,.08);--sg-danger:#a84040;--sg-danger-bg:rgba(168,64,64,.07);--sg-warn:#9a6a1a;--sg-warn-bg:rgba(154,106,26,.08);--sg-info:#2a6090;--sg-info-bg:rgba(42,96,144,.08);--sg-font-display:'Lora',Georgia,serif;--sg-font-ui:'DM Sans',system-ui,sans-serif;--sg-font-mono:'JetBrains Mono','Fira Mono',monospace;--sg-radius:10px;--sg-radius-sm:7px;--sg-t:.17s cubic-bezier(.4,0,.2,1);}
html[data-theme=dark]{--sg-surface:#1e2419;--sg-surface-2:#252c20;--sg-surface-3:#2d3527;--sg-accent:#6abf60;--sg-accent-mid:#85d47a;--sg-accent-light:rgba(106,191,96,.12);--sg-accent-bg:rgba(106,191,96,.09);--sg-accent-bg2:rgba(106,191,96,.16);--sg-accent-glow:rgba(106,191,96,.20);--sg-text:#e4ead8;--sg-text-2:#adb9a0;--sg-text-3:#627057;--sg-border:rgba(255,255,255,.08);--sg-border-2:rgba(255,255,255,.13);}
.sg,.sg *{font-family:var(--sg-font-ui);box-sizing:border-box;}
.sg{max-width:1100px;margin:0 auto;padding:0 0 4rem;color:var(--sg-text);}
@keyframes sg-up{from{opacity:0;transform:translateY(9px)}to{opacity:1;transform:translateY(0)}}

/* Header */
.sg-hdr{display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--sg-border);flex-wrap:wrap;animation:sg-up .38s ease both;}
.sg-hdr-left{display:flex;align-items:center;gap:.9rem;}
.sg-hdr-icon{width:42px;height:42px;border-radius:var(--sg-radius);flex-shrink:0;display:flex;align-items:center;justify-content:center;background:var(--sg-accent-light);border:1px solid rgba(58,107,53,.18);color:var(--sg-accent);font-size:.95rem;box-shadow:0 4px 12px var(--sg-accent-glow);}
html[data-theme=dark] .sg-hdr-icon{border-color:rgba(106,191,96,.18);}
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
.sg-flash-warn{background:var(--sg-warn-bg);border-color:rgba(154,106,26,.2);color:var(--sg-warn);}
#alertContainer .sg-flash{animation:sg-up .2s ease both;}

/* Card */
.sg-card{background:var(--sg-surface);border:1px solid var(--sg-border);border-radius:var(--sg-radius);box-shadow:0 1px 2px rgba(28,35,24,.04);overflow:hidden;margin-bottom:1rem;animation:sg-up .3s ease both;}
.sg-card-hdr{display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.88rem 1.15rem;border-bottom:1px solid var(--sg-border);background:var(--sg-surface-2);flex-wrap:wrap;}
.sg-card-title{font-family:var(--sg-font-display);font-size:.88rem;font-weight:600;color:var(--sg-text);display:flex;align-items:center;gap:.5rem;margin:0;}
.sg-card-title i{color:var(--sg-accent);font-size:.82rem;}
.sg-card-body{padding:1.15rem 1.2rem;}

/* Filters grid */
.sg-filters{display:grid;grid-template-columns:repeat(4,1fr);gap:.85rem;align-items:end;}
.sg-filters-row2{display:grid;grid-template-columns:1fr 1fr auto;gap:.85rem;align-items:end;margin-top:.85rem;}
@media(max-width:900px){.sg-filters{grid-template-columns:1fr 1fr;}}
@media(max-width:580px){.sg-filters,.sg-filters-row2{grid-template-columns:1fr;}}

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

/* Clock */
.sg-clock{font-size:.68rem;font-family:var(--sg-font-mono);color:var(--sg-text-3);background:var(--sg-surface-2);border:1px solid var(--sg-border);padding:.28rem .72rem;border-radius:6px;white-space:nowrap;}

/* Buttons */
.sg-btn{display:inline-flex;align-items:center;gap:.45rem;padding:.5rem 1.05rem;border-radius:var(--sg-radius-sm);font-family:var(--sg-font-ui);font-size:.78rem;font-weight:600;cursor:pointer;border:1px solid;text-decoration:none;transition:all var(--sg-t);}
.sg-btn i{font-size:.72rem;}
.sg-btn-primary{background:var(--sg-accent);border-color:var(--sg-accent);color:#fff;box-shadow:0 3px 10px var(--sg-accent-glow);}
.sg-btn-primary:hover:not(:disabled){background:var(--sg-accent-mid);border-color:var(--sg-accent-mid);transform:translateY(-1px);box-shadow:0 6px 16px var(--sg-accent-glow);}
.sg-btn-primary:disabled{opacity:.42;cursor:not-allowed;transform:none;}
.sg-btn-ghost{background:transparent;border-color:var(--sg-border-2);color:var(--sg-text-3);}
.sg-btn-ghost:hover{background:var(--sg-surface-2);color:var(--sg-text);}
.sg-btn-danger{background:var(--sg-danger-bg);border-color:rgba(168,64,64,.2);color:var(--sg-danger);padding:.3rem .62rem;font-size:.72rem;}
.sg-btn-danger:hover{background:rgba(168,64,64,.14);border-color:rgba(168,64,64,.35);}
.sg-btn-danger:disabled{opacity:.4;cursor:not-allowed;}

/* Table */
.sg-tbl-wrap{overflow-x:auto;}
.sg-table{width:100%;border-collapse:collapse;font-size:.78rem;}
.sg-th{padding:.6rem .9rem;font-size:.59rem;text-transform:uppercase;letter-spacing:.12em;color:var(--sg-text-3);border-bottom:1px solid var(--sg-border);font-weight:700;text-align:left;white-space:nowrap;}
.sg-tr{border-bottom:1px solid var(--sg-border);transition:background var(--sg-t);}
.sg-tr:last-child{border-bottom:none;}
.sg-tr:hover td{background:var(--sg-surface-2);}
.sg-td{padding:.7rem .9rem;color:var(--sg-text-2);vertical-align:middle;}
.sg-td-subject{font-weight:600;color:var(--sg-text);}
.sg-td-teacher{display:flex;align-items:center;gap:.55rem;}
.sg-teacher-avatar{width:28px;height:28px;border-radius:7px;display:flex;align-items:center;justify-content:center;background:var(--sg-accent-bg);border:1px solid rgba(58,107,53,.18);color:var(--sg-accent);font-size:.58rem;font-weight:700;flex-shrink:0;font-family:var(--sg-font-mono);}
.sg-class-label{display:flex;align-items:center;gap:.4rem;font-size:.72rem;font-weight:600;color:var(--sg-text-3);background:var(--sg-surface-2);border:1px solid var(--sg-border);padding:.22rem .6rem;border-radius:5px;}
.sg-class-label i{color:var(--sg-accent);font-size:.62rem;}

/* Empty state */
.sg-empty{text-align:center;padding:2.5rem 1rem;color:var(--sg-text-3);font-size:.79rem;}
.sg-empty-icon{font-size:1.6rem;margin-bottom:.5rem;opacity:.26;}

/* Modal */
.sg-overlay{position:fixed;inset:0;z-index:8000;background:rgba(28,35,24,.52);backdrop-filter:blur(3px);opacity:0;pointer-events:none;transition:opacity .2s;}
.sg-overlay.on{opacity:1;pointer-events:all;}
.sg-modal{position:fixed;top:50%;left:50%;transform:translate(-50%,-46%);z-index:8001;width:90%;max-width:380px;background:var(--sg-surface);border:1px solid var(--sg-border);border-radius:var(--sg-radius);padding:1.65rem;box-shadow:0 10px 36px rgba(28,35,24,.11);opacity:0;pointer-events:none;transition:opacity .2s,transform .2s;}
.sg-modal.on{opacity:1;pointer-events:all;transform:translate(-50%,-50%);}
.sg-modal-danger-ring{width:44px;height:44px;border-radius:11px;background:var(--sg-danger-bg);border:1px solid rgba(168,64,64,.2);display:flex;align-items:center;justify-content:center;color:var(--sg-danger);margin-bottom:.95rem;}
.sg-modal-title{font-family:var(--sg-font-display);font-size:1rem;font-weight:600;color:var(--sg-text);margin-bottom:.35rem;}
.sg-modal-desc{font-size:.78rem;color:var(--sg-text-2);line-height:1.62;margin-bottom:1.35rem;}
.sg-modal-actions{display:flex;gap:.6rem;justify-content:flex-end;}
.sg-btn-cancel{font-family:var(--sg-font-ui);font-size:.78rem;font-weight:500;padding:.46rem .95rem;border-radius:var(--sg-radius-sm);cursor:pointer;background:var(--sg-surface-2);color:var(--sg-text-2);border:1px solid var(--sg-border-2);transition:all var(--sg-t);}
.sg-btn-cancel:hover{background:var(--sg-surface-3);color:var(--sg-text);}
.sg-btn-confirm-del{font-family:var(--sg-font-ui);font-size:.78rem;font-weight:600;padding:.46rem 1.1rem;border-radius:var(--sg-radius-sm);cursor:pointer;background:var(--sg-danger);color:#fff;border:none;transition:all var(--sg-t);}
.sg-btn-confirm-del:hover{background:#933838;box-shadow:0 4px 14px rgba(168,64,64,.35);}
.sg-btn-confirm-del:disabled{opacity:.5;cursor:not-allowed;}
</style>
@endpush

@section('content')
<div class="sg">

    <div class="sg-hdr">
        <div class="sg-hdr-left">
            <div class="sg-hdr-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <div>
                <div class="sg-eyebrow">Administration · Enseignants</div>
                <h1 class="sg-title">Affectation des enseignants</h1>
                <p class="sg-subtitle">Par classe et par année — une matière = un seul enseignant</p>
            </div>
        </div>
        <span class="sg-clock" id="liveClock"></span>
    </div>

    <div id="alertContainer"></div>

    {{-- Formulaire nouvelle affectation --}}
    <div class="sg-card" style="animation-delay:.05s">
        <div class="sg-card-hdr">
            <h2 class="sg-card-title"><i class="fas fa-plus-circle"></i> Nouvelle affectation</h2>
        </div>
        <div class="sg-card-body">
            <form id="assignmentForm" method="POST" action="{{ route('teacher-assignments.store') }}">
                @csrf
                <div class="sg-filters">
                    <div class="sg-field">
                        <label class="sg-label" for="year_id"><i class="fas fa-calendar-alt"></i> Année</label>
                        <div class="sg-sel-wrap">
                            <select name="year_id" id="year_id" class="sg-sel" required>
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
                            <select name="sector_id" id="sector_id" class="sg-sel" disabled required>
                                <option value="">—</option>
                            </select>
                            <i class="fas fa-chevron-down sg-sel-arrow"></i>
                        </div>
                    </div>
                    <div class="sg-field">
                        <label class="sg-label" for="promotion_id"><i class="fas fa-layer-group"></i> Promotion</label>
                        <div class="sg-sel-wrap">
                            <select name="promotion_id" id="promotion_id" class="sg-sel" disabled required>
                                <option value="">—</option>
                            </select>
                            <i class="fas fa-chevron-down sg-sel-arrow"></i>
                        </div>
                    </div>
                    <div class="sg-field">
                        <label class="sg-label" for="classroom_id"><i class="fas fa-door-open"></i> Classe</label>
                        <div class="sg-sel-wrap">
                            <select name="classroom_id" id="classroom_id" class="sg-sel" disabled required>
                                <option value="">—</option>
                            </select>
                            <i class="fas fa-chevron-down sg-sel-arrow"></i>
                        </div>
                    </div>
                </div>
                <div class="sg-filters-row2">
                    <div class="sg-field">
                        <label class="sg-label" for="subject_id"><i class="fas fa-book"></i> Matière</label>
                        <div class="sg-sel-wrap">
                            <select name="subject_id" id="subject_id" class="sg-sel" disabled required>
                                <option value="">—</option>
                            </select>
                            <i class="fas fa-chevron-down sg-sel-arrow"></i>
                        </div>
                    </div>
                    <div class="sg-field">
                        <label class="sg-label" for="teacher_id"><i class="fas fa-user-tie"></i> Enseignant</label>
                        <div class="sg-sel-wrap">
                            <select name="teacher_id" id="teacher_id" class="sg-sel" required>
                                <option value="">— Choisir un enseignant —</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} {{ $t->surname }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down sg-sel-arrow"></i>
                        </div>
                    </div>
                    <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                        <button type="reset" class="sg-btn sg-btn-ghost"><i class="fas fa-undo"></i> Réinitialiser</button>
                        <button type="submit" id="submitBtn" class="sg-btn sg-btn-primary">
                            <i class="fas fa-save"></i>
                            <span id="submitLabel">Enregistrer</span>
                            <span id="submitLoader" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tableau des affectations --}}
    <div class="sg-card" style="animation-delay:.1s">
        <div class="sg-card-hdr">
            <h2 class="sg-card-title"><i class="fas fa-list-ul"></i> Affectations existantes</h2>
            <span id="classLabel" class="sg-class-label" style="display:none;"><i class="fas fa-door-open"></i> <span id="classLabelTxt"></span></span>
        </div>
        <div class="sg-tbl-wrap">
            <table class="sg-table">
                <thead>
                    <tr>
                        <th class="sg-th">Matière</th>
                        <th class="sg-th">Enseignant assigné</th>
                        <th class="sg-th" style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody id="assignmentsTable">
                    <tr><td colspan="3"><div class="sg-empty"><div class="sg-empty-icon">🏫</div>Sélectionnez une classe pour voir les affectations</div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal suppression --}}
<div class="sg-overlay" id="sgOverlay" onclick="sgClose()"></div>
<div class="sg-modal" id="sgModal" role="dialog" aria-modal="true">
    <div class="sg-modal-danger-ring">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
    </div>
    <div class="sg-modal-title">Retirer l'affectation</div>
    <p class="sg-modal-desc">Voulez-vous vraiment retirer cette affectation ? L'action est <em>irréversible</em>.</p>
    <div class="sg-modal-actions">
        <button class="sg-btn-cancel" onclick="sgClose()">Annuler</button>
        <button class="sg-btn-confirm-del" id="sgModalBtn">Retirer</button>
    </div>
</div>
@endsection

@section('another_JS')
<script>
/* Horloge */
(function tick(){
    const d=new Date();
    const el=document.getElementById('liveClock');
    if(el) el.textContent=d.toLocaleString('fr-FR',{weekday:'short',day:'numeric',month:'short',hour:'2-digit',minute:'2-digit'});
    setTimeout(tick,10000);
})();

const $=id=>document.getElementById(id);
const yearEl=$('year_id'),sectorEl=$('sector_id'),promoEl=$('promotion_id'),roomEl=$('classroom_id'),subjEl=$('subject_id'),tbody=$('assignmentsTable');
let _delId=null;

/* Cascades */
yearEl.onchange=loadSectors;
sectorEl.onchange=loadPromotions;
promoEl.onchange=()=>{loadSubjects();loadClassrooms();};
roomEl.onchange=loadAssignments;

async function loadSectors(){
    if(!yearEl.value) return;
    resetSels([sectorEl,promoEl,roomEl,subjEl]);
    sectorEl.innerHTML='<option>Chargement…</option>';
    const d=await get(`/teacher-assignments/get-sectors/${yearEl.value}`);
    sectorEl.innerHTML='<option value="">— Filière —</option>'+d.map(s=>`<option value="${s.id}">${s.name}</option>`).join('');
    sectorEl.disabled=false; emptyTable('Sélectionnez une classe pour voir les affectations.');
}
async function loadPromotions(){
    if(!sectorEl.value) return;
    resetSels([promoEl,roomEl,subjEl]);
    promoEl.innerHTML='<option>Chargement…</option>';
    const d=await get(`/teacher-assignments/get-promotions/${yearEl.value}/${sectorEl.value}`);
    promoEl.innerHTML='<option value="">— Promotion —</option>'+d.map(p=>`<option value="${p.id}">${p.name}</option>`).join('');
    promoEl.disabled=false;
}
async function loadSubjects(){
    subjEl.innerHTML='<option>Chargement…</option>'; subjEl.disabled=true;
    const d=await get(`/teacher-assignments/get-subjects/${yearEl.value}/${sectorEl.value}/${promoEl.value}`);
    subjEl.innerHTML='<option value="">— Matière —</option>'+d.map(s=>`<option value="${s.id}">${s.name}</option>`).join('');
    subjEl.disabled=false;
}
async function loadClassrooms(){
    roomEl.innerHTML='<option>Chargement…</option>'; roomEl.disabled=true;
    const d=await get(`/teacher-assignments/get-classes/${yearEl.value}/${sectorEl.value}/${promoEl.value}`);
    roomEl.innerHTML='<option value="">— Classe —</option>'+d.map(c=>`<option value="${c.id}">${c.name}</option>`).join('');
    roomEl.disabled=false;
}
async function loadAssignments(){
    if(!yearEl.value||!roomEl.value) return;
    const lbl=roomEl.options[roomEl.selectedIndex]?.text||'';
    $('classLabelTxt').textContent=lbl; $('classLabel').style.display='flex';
    tbody.innerHTML=`<tr><td colspan="3" style="text-align:center;padding:1.5rem;"><i class="fas fa-spinner fa-spin" style="color:var(--sg-accent);font-size:1rem;"></i></td></tr>`;
    const data=await get(`/teacher-assignments/get-assignments/${yearEl.value}/${roomEl.value}`);
    const list=data.assignments??[];
    if(!list.length){emptyTable('Aucune affectation pour cette classe.');return;}
    tbody.innerHTML=list.map(a=>{
        const ini=(a.teacher||'?')[0].toUpperCase()+(a.teacher||'?').split(' ')[1]?.[0]?.toUpperCase()||'';
        return `<tr class="sg-tr" data-id="${a.id}">
            <td class="sg-td sg-td-subject">${a.subject}</td>
            <td class="sg-td"><div class="sg-td-teacher"><div class="sg-teacher-avatar">${ini}</div>${a.teacher}</div></td>
            <td class="sg-td" style="text-align:right;"><button class="sg-btn sg-btn-danger del-btn" data-id="${a.id}"><i class="fas fa-times"></i> Retirer</button></td>
        </tr>`;
    }).join('');
}

/* Submit AJAX */
$('assignmentForm').addEventListener('submit',async function(e){
    e.preventDefault();
    const btn=$('submitBtn'); btn.disabled=true; $('submitLabel').style.display='none'; $('submitLoader').style.display='inline';
    const res=await fetch(this.action,{method:'POST',body:new FormData(this),headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});
    const data=await res.json();
    toast(data.success?'success':'danger',data.message);
    if(data.success){loadAssignments();subjEl.value='';$('teacher_id').value='';}
    btn.disabled=false; $('submitLabel').style.display=''; $('submitLoader').style.display='none';
});

/* Délégation suppression */
document.addEventListener('click',e=>{const b=e.target.closest('.del-btn');if(b){_delId=b.dataset.id;$('sgOverlay').classList.add('on');$('sgModal').classList.add('on');setTimeout(()=>$('sgModalBtn').focus(),50);}});
function sgClose(){_delId=null;$('sgOverlay').classList.remove('on');$('sgModal').classList.remove('on');}
document.addEventListener('keydown',e=>{if(e.key==='Escape')sgClose();});
$('sgOverlay').onclick=sgClose;

$('sgModalBtn').addEventListener('click',async function(){
    if(!_delId) return;
    this.disabled=true; this.textContent='Suppression…';
    const res=await fetch(`/teacher-assignments/${_delId}`,{method:'DELETE',headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}});
    const data=await res.json();
    if(data.success){const row=tbody.querySelector(`tr[data-id="${_delId}"]`);row?.remove();toast('success',data.message);if(!tbody.querySelector('tr[data-id]'))emptyTable('Aucune affectation pour cette classe.');}
    else toast('danger',data.message);
    sgClose(); this.disabled=false; this.textContent='Retirer';
});

/* Helpers */
async function get(url){try{const r=await fetch(url);return await r.json();}catch{return [];}}
function resetSels(sels){sels.forEach(s=>{s.innerHTML='<option value="">—</option>';s.disabled=true;});}
function emptyTable(msg){tbody.innerHTML=`<tr><td colspan="3"><div class="sg-empty"><div class="sg-empty-icon">🏫</div>${msg}</div></td></tr>`;}
function toast(type,msg){
    const d=document.createElement('div');
    d.className=`sg-flash sg-flash-${type}`;
    d.innerHTML=`<i class="fas fa-${type==='success'?'check-circle':'exclamation-circle'}"></i><span>${msg}</span><button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>`;
    const c=$('alertContainer');c.innerHTML='';c.appendChild(d);setTimeout(()=>d.remove(),5500);
}
</script>
@endsection
