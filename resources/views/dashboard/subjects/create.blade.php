@extends('layouts.template')

@section('breadcrumb', 'Gestion des Matières')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,600;0,700&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap');
:root{--sg-bg:#f6f7f3;--sg-surface:#fff;--sg-surface-2:#f1f3ee;--sg-surface-3:#e8ebe3;--sg-accent:#3a6b35;--sg-accent-mid:#5a9a52;--sg-accent-light:#eef4ec;--sg-accent-bg:rgba(58,107,53,.07);--sg-accent-bg2:rgba(58,107,53,.13);--sg-accent-glow:rgba(58,107,53,.18);--sg-text:#1c2318;--sg-text-2:#4a5544;--sg-text-3:#8a9880;--sg-border:#e2e5da;--sg-border-2:#cbd2c2;--sg-border-3:#b5bfaa;--sg-success:#2e7d4f;--sg-success-bg:rgba(46,125,79,.08);--sg-danger:#a84040;--sg-danger-bg:rgba(168,64,64,.07);--sg-warn:#9a6a1a;--sg-warn-bg:rgba(154,106,26,.08);--sg-font-display:'Lora',Georgia,serif;--sg-font-ui:'DM Sans',system-ui,sans-serif;--sg-radius:10px;--sg-radius-sm:7px;--sg-t:.17s cubic-bezier(.4,0,.2,1);}
html[data-theme=dark]{--sg-surface:#1e2419;--sg-surface-2:#252c20;--sg-surface-3:#2d3527;--sg-accent:#6abf60;--sg-accent-mid:#85d47a;--sg-accent-light:rgba(106,191,96,.12);--sg-accent-bg:rgba(106,191,96,.09);--sg-accent-bg2:rgba(106,191,96,.16);--sg-accent-glow:rgba(106,191,96,.20);--sg-text:#e4ead8;--sg-text-2:#adb9a0;--sg-text-3:#627057;--sg-border:rgba(255,255,255,.08);--sg-border-2:rgba(255,255,255,.13);}
.sg,.sg *{font-family:var(--sg-font-ui);box-sizing:border-box;}
.sg{max-width:940px;margin:0 auto;padding:0 0 4rem;color:var(--sg-text);}
.sg-hdr{display:flex;align-items:center;gap:.85rem;margin-bottom:1.75rem;flex-wrap:wrap;}
.sg-hdr-icon{width:42px;height:42px;border-radius:var(--sg-radius);flex-shrink:0;display:flex;align-items:center;justify-content:center;background:var(--sg-accent-light);border:1px solid rgba(58,107,53,.18);color:var(--sg-accent);font-size:.95rem;box-shadow:0 4px 12px var(--sg-accent-glow);}
.sg-eyebrow{font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.14em;color:var(--sg-accent);margin-bottom:.15rem;}
.sg-title{font-family:var(--sg-font-display);font-size:1.28rem;font-weight:700;color:var(--sg-text);letter-spacing:-.02em;margin:0;}
.sg-subtitle{font-size:.75rem;color:var(--sg-text-3);margin:.1rem 0 0;}
.sg-alert{display:flex;align-items:center;gap:.65rem;padding:.72rem .9rem;border-radius:var(--sg-radius-sm);font-size:.77rem;font-weight:500;border:1px solid;margin-bottom:1rem;}
.sg-alert span{flex:1;}
.sg-alert-close{background:transparent;border:none;cursor:pointer;color:inherit;opacity:.55;font-size:.72rem;padding:0;}
.sg-alert-success{background:var(--sg-success-bg);border-color:rgba(46,125,79,.2);color:var(--sg-success);}
.sg-alert-danger{background:var(--sg-danger-bg);border-color:rgba(168,64,64,.2);color:var(--sg-danger);}
.sg-card{background:var(--sg-surface);border:1px solid var(--sg-border);border-radius:var(--sg-radius);box-shadow:0 1px 2px rgba(28,35,24,.04);margin-bottom:1rem;animation:sg-up .26s ease both;}
@keyframes sg-up{from{opacity:0;transform:translateY(9px)}to{opacity:1;transform:translateY(0)}}
.sg-card-hdr{display:flex;align-items:center;gap:.75rem;padding:1rem 1.2rem;border-bottom:1px solid var(--sg-border);flex-wrap:wrap;}
.sg-card-body{padding:1.2rem;}
.sg-step{width:27px;height:27px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:var(--sg-accent);color:#fff;font-size:.72rem;font-weight:700;box-shadow:0 3px 9px var(--sg-accent-glow);}
.sg-card-title{font-family:var(--sg-font-display);font-size:.9rem;font-weight:600;color:var(--sg-text);margin:0;}
.sg-card-desc{font-size:.71rem;color:var(--sg-text-3);margin:.08rem 0 0;}
.sg-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:.9rem;}
@media(max-width:600px){.sg-grid-2{grid-template-columns:1fr;}}
.sg-field{display:flex;flex-direction:column;gap:.36rem;}
.sg-label{font-size:.68rem;font-weight:700;color:var(--sg-text-3);text-transform:uppercase;letter-spacing:.1em;display:flex;align-items:center;gap:.35rem;}
.sg-label i{color:var(--sg-accent);font-size:.64rem;}
.sg-sel-wrap{position:relative;}
.sg-sel{width:100%;padding:.6rem 1.9rem .6rem .78rem;background:var(--sg-surface-2);border:1px solid var(--sg-border-2);border-radius:var(--sg-radius-sm);color:var(--sg-text);font-family:var(--sg-font-ui);font-size:.8rem;appearance:none;cursor:pointer;outline:none;transition:border-color var(--sg-t),box-shadow var(--sg-t);}
.sg-sel:focus{border-color:var(--sg-accent);box-shadow:0 0 0 3px var(--sg-accent-bg);}
.sg-sel:disabled{opacity:.45;cursor:not-allowed;}
.sg-sel option{background:var(--sg-surface-2);color:var(--sg-text);}
.sg-sel-arrow{position:absolute;right:.68rem;top:50%;transform:translateY(-50%);font-size:.56rem;color:var(--sg-text-3);pointer-events:none;}
.sg-sel-loader{position:absolute;right:.68rem;top:50%;transform:translateY(-50%);color:var(--sg-accent);font-size:.72rem;display:none;}

/* Sem legend */
.sg-sem-legend{display:flex;align-items:center;gap:.36rem;flex-wrap:wrap;padding:.55rem .82rem;margin-bottom:.9rem;background:var(--sg-accent-bg);border:1px solid rgba(58,107,53,.12);border-radius:var(--sg-radius-sm);font-size:.7rem;color:var(--sg-text-3);}
.sg-sem-tag{font-size:.63rem;font-weight:800;padding:.12rem .48rem;border-radius:20px;}
.sg-sem-tag--both{background:var(--sg-accent-bg2);border:1px solid rgba(58,107,53,.2);color:var(--sg-accent);}
.sg-sem-tag--1{background:var(--sg-success-bg);border:1px solid rgba(46,125,79,.2);color:var(--sg-success);}
.sg-sem-tag--2{background:var(--sg-warn-bg);border:1px solid rgba(154,106,26,.2);color:var(--sg-warn);}

/* Suggestions */
.sg-suggestions{background:var(--sg-accent-light);border:1px solid rgba(58,107,53,.14);border-radius:var(--sg-radius-sm);padding:.72rem .88rem;margin-bottom:.9rem;}
html[data-theme=dark] .sg-suggestions{border-color:rgba(106,191,96,.14);}
.sg-sug-label{font-size:.65rem;font-weight:700;color:var(--sg-accent);text-transform:uppercase;letter-spacing:.07em;margin:0 0 .52rem;display:flex;align-items:center;gap:.36rem;}
.sg-sug-list{display:flex;flex-wrap:wrap;gap:.36rem;}
.sg-sug-chip{padding:.26rem .68rem;background:var(--sg-surface);border:1px solid var(--sg-border-2);border-radius:20px;color:var(--sg-text-2);font-family:var(--sg-font-ui);font-size:.71rem;font-weight:500;cursor:pointer;transition:all var(--sg-t);}
.sg-sug-chip:hover{background:var(--sg-accent-bg2);border-color:rgba(58,107,53,.28);color:var(--sg-accent);transform:translateY(-1px);}
.sg-sug-chip:disabled{opacity:.3;cursor:not-allowed;pointer-events:none;}
.sg-sug-chip.in{opacity:.32;text-decoration:line-through;cursor:default;pointer-events:none;}

/* Promo row matières */
.sg-subj-row{background:var(--sg-surface-2);border:1px solid var(--sg-border);border-radius:var(--sg-radius-sm);padding:.88rem 1.05rem;margin-bottom:.6rem;transition:border-color var(--sg-t);}
.sg-subj-row:focus-within,.sg-subj-row.active{border-color:rgba(58,107,53,.35);background:linear-gradient(135deg,var(--sg-surface-2) 0%,var(--sg-accent-light) 100%);}
html[data-theme=dark] .sg-subj-row.active{background:linear-gradient(135deg,var(--sg-surface-2) 0%,var(--sg-accent-bg) 100%);}
.sg-subj-hdr{display:flex;align-items:center;gap:.58rem;margin-bottom:.72rem;}
.sg-subj-badge{width:25px;height:25px;background:var(--sg-accent-bg);border:1px solid rgba(58,107,53,.18);border-radius:7px;display:flex;align-items:center;justify-content:center;color:var(--sg-accent);font-size:.65rem;flex-shrink:0;transition:all var(--sg-t);}
.sg-subj-row.active .sg-subj-badge{background:var(--sg-accent);border-color:var(--sg-accent);color:#fff;box-shadow:0 3px 8px var(--sg-accent-glow);}
.sg-subj-name{font-size:.82rem;font-weight:600;color:var(--sg-text);flex:1;}
.sg-subj-row.active .sg-subj-name{color:var(--sg-accent);}
.sg-subj-count{font-size:.66rem;font-weight:600;color:var(--sg-text-3);background:var(--sg-surface);padding:.16rem .52rem;border-radius:20px;border:1px solid var(--sg-border);transition:all var(--sg-t);}
.sg-subj-count.has-items{color:var(--sg-success);background:var(--sg-success-bg);border-color:rgba(46,125,79,.2);}

/* Chips zone */
.sg-chips-zone{min-height:42px;background:var(--sg-surface);border:1px solid var(--sg-border-2);border-radius:var(--sg-radius-sm);padding:.4rem .5rem;display:flex;flex-wrap:wrap;align-items:flex-start;gap:.36rem;cursor:text;transition:border-color var(--sg-t),box-shadow var(--sg-t);}
.sg-chips-zone:focus-within{border-color:var(--sg-accent);box-shadow:0 0 0 3px var(--sg-accent-bg);}
.sg-chips-list{display:contents;}
.sg-chip{display:inline-flex;align-items:center;gap:.26rem;padding:.24rem .5rem .24rem .58rem;background:var(--sg-accent-light);border:1px solid rgba(58,107,53,.22);border-radius:20px;color:var(--sg-text);font-size:.72rem;font-weight:600;animation:chip-in .13s ease both;flex-shrink:0;}
@keyframes chip-in{from{opacity:0;transform:scale(.85)}to{opacity:1;transform:scale(1)}}
.sg-chip-sem{font-size:.57rem;font-weight:800;padding:.1rem .34rem;border-radius:99px;cursor:pointer;border:none;line-height:1.4;flex-shrink:0;transition:all var(--sg-t);}
.sg-chip-sem[data-sem=null]{background:var(--sg-accent-bg2);color:var(--sg-accent);}
.sg-chip-sem[data-sem="1"]{background:var(--sg-success-bg);color:var(--sg-success);}
.sg-chip-sem[data-sem="2"]{background:var(--sg-warn-bg);color:var(--sg-warn);}
.sg-chip-sem:hover{filter:brightness(.9);transform:scale(1.08);}
.sg-chip-remove{background:transparent;border:none;cursor:pointer;color:var(--sg-text-3);padding:0;font-size:.58rem;width:13px;height:13px;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:all var(--sg-t);}
.sg-chip-remove:hover{color:var(--sg-danger);background:var(--sg-danger-bg);}
.sg-chip-input-wrap{position:relative;flex:1;min-width:148px;}
.sg-chip-input{width:100%;background:transparent;border:none;outline:none;color:var(--sg-text);font-family:var(--sg-font-ui);font-size:.78rem;padding:.28rem .18rem;caret-color:var(--sg-accent);}
.sg-chip-input::placeholder{color:var(--sg-text-3);}
.sg-autocomplete{position:absolute;top:calc(100% + 3px);left:0;right:0;z-index:200;background:var(--sg-surface);border:1px solid var(--sg-border-2);border-radius:var(--sg-radius-sm);box-shadow:0 4px 18px rgba(28,35,24,.09);overflow:hidden;display:none;animation:drop-in .12s ease both;}
@keyframes drop-in{from{opacity:0;transform:translateY(-5px)}to{opacity:1;transform:translateY(0)}}
.sg-autocomplete.open{display:block;}
.sg-ac-item{display:flex;align-items:center;gap:.48rem;padding:.48rem .82rem;font-size:.77rem;color:var(--sg-text-2);cursor:pointer;transition:background var(--sg-t),color var(--sg-t);}
.sg-ac-item:hover,.sg-ac-item.sel{background:var(--sg-accent-bg);color:var(--sg-text);}
.sg-ac-item i{font-size:.64rem;color:var(--sg-accent);flex-shrink:0;}
.sg-ac-item em{color:var(--sg-accent);font-style:normal;font-weight:700;}

/* Summary */
.sg-summary{display:flex;align-items:center;gap:.55rem;padding:.65rem .88rem;border-radius:var(--sg-radius-sm);font-size:.76rem;font-weight:500;border:1px solid;margin-top:.65rem;background:var(--sg-warn-bg);border-color:rgba(154,106,26,.2);color:var(--sg-warn);}
.sg-summary.ok{background:var(--sg-success-bg);border-color:rgba(46,125,79,.2);color:var(--sg-success);}

/* Buttons */
.sg-actions{display:flex;justify-content:flex-end;align-items:center;gap:.6rem;margin-top:.5rem;}
.sg-btn-primary{display:inline-flex;align-items:center;gap:.5rem;padding:.5rem 1.1rem;background:var(--sg-accent);border:1px solid var(--sg-accent);border-radius:var(--sg-radius-sm);color:#fff;font-family:var(--sg-font-ui);font-size:.79rem;font-weight:600;cursor:pointer;box-shadow:0 3px 10px var(--sg-accent-glow);transition:all var(--sg-t);}
.sg-btn-primary:hover{background:var(--sg-accent-mid);transform:translateY(-1px);box-shadow:0 6px 16px var(--sg-accent-glow);}
.sg-btn-secondary{display:inline-flex;align-items:center;gap:.5rem;padding:.5rem 1rem;background:transparent;border:1px solid var(--sg-border-2);border-radius:var(--sg-radius-sm);color:var(--sg-text-3);font-family:var(--sg-font-ui);font-size:.77rem;font-weight:500;cursor:pointer;transition:all var(--sg-t);}
.sg-btn-secondary:hover{background:var(--sg-surface-2);color:var(--sg-text);}
.sg-btn-ghost{display:inline-flex;align-items:center;gap:.48rem;padding:.38rem .8rem;background:var(--sg-accent-bg);border:1px solid rgba(58,107,53,.2);border-radius:var(--sg-radius-sm);color:var(--sg-accent);font-family:var(--sg-font-ui);font-size:.73rem;font-weight:600;cursor:pointer;transition:all var(--sg-t);white-space:nowrap;}
.sg-btn-ghost:hover{background:var(--sg-accent-bg2);}
</style>
@endpush

@section('content')
<div class="sg">

    <div class="sg-hdr">
        <div class="sg-hdr-icon"><i class="fas fa-book-open"></i></div>
        <div>
            <div class="sg-eyebrow">Configuration · Filières</div>
            <h1 class="sg-title">Gestion des Matières</h1>
            <p class="sg-subtitle">Associez les matières à chaque promotion, avec leur(s) semestre(s) d'enseignement</p>
        </div>
    </div>

    @if(session('success'))
    <div class="sg-alert sg-alert-success"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span><button class="sg-alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button></div>
    @endif
    @if($errors->any())
    <div class="sg-alert sg-alert-danger"><i class="fas fa-exclamation-circle"></i><span>{{ $errors->first() }}</span><button class="sg-alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button></div>
    @endif

    <form method="POST" action="{{ route('subject.store') }}" id="subjectForm">
        @csrf

        {{-- Étape 1 --}}
        <div class="sg-card">
            <div class="sg-card-hdr">
                <div class="sg-step">1</div>
                <div><div class="sg-card-title">Sélection de la filière</div><div class="sg-card-desc">Choisissez l'année scolaire puis la filière</div></div>
            </div>
            <div class="sg-card-body">
                <div class="sg-grid-2">
                    <div class="sg-field">
                        <label class="sg-label" for="year_id"><i class="fas fa-calendar-alt"></i> Année scolaire</label>
                        <div class="sg-sel-wrap">
                            <select name="year_id" id="year_id" class="sg-sel" required>
                                <option value="">— Choisissez une année —</option>
                                @foreach($years as $year)<option value="{{ $year->id }}">{{ $year->year }}</option>@endforeach
                            </select>
                            <i class="fas fa-chevron-down sg-sel-arrow"></i>
                        </div>
                    </div>
                    <div class="sg-field">
                        <label class="sg-label" for="sector_id"><i class="fas fa-sitemap"></i> Filière</label>
                        <div class="sg-sel-wrap">
                            <select name="sector_id" id="sector_id" class="sg-sel" disabled required>
                                <option value="">— Choisissez d'abord une année —</option>
                            </select>
                            <i class="fas fa-chevron-down sg-sel-arrow"></i>
                            <div class="sg-sel-loader" id="sectorLoader"><i class="fas fa-spinner fa-spin"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Étape 2 --}}
        <div class="sg-card" id="promotionsCard" style="display:none;">
            <div class="sg-card-hdr">
                <div class="sg-step">2</div>
                <div style="flex:1;"><div class="sg-card-title">Matières par promotion</div><div class="sg-card-desc">Ajoutez les matières et définissez leur(s) semestre(s) en cliquant sur le badge coloré</div></div>
                <button type="button" class="sg-btn-ghost" id="copyFirstBtn" style="display:none;"><i class="fas fa-copy"></i> Copier la 1<sup>ère</sup> vers toutes</button>
            </div>
            <div class="sg-card-body">
                <div class="sg-sem-legend">
                    <span class="sg-sem-tag sg-sem-tag--both"><i class="fas fa-infinity"></i> S1+S2</span>
                    <span>= toute l'année</span>
                    <span class="sg-sem-tag sg-sem-tag--1">S1</span>
                    <span>= semestre 1</span>
                    <span class="sg-sem-tag sg-sem-tag--2">S2</span>
                    <span>= semestre 2 — Cliquez sur le badge pour changer</span>
                </div>
                <div class="sg-suggestions" id="suggestionsPanel">
                    <p class="sg-sug-label"><i class="fas fa-lightbulb"></i><span id="suggestionsLabelText">Cliquez sur un champ pour activer les suggestions</span></p>
                    <div class="sg-sug-list" id="suggestionsList">
                        @foreach($allSubjects as $subject)
                        <button type="button" class="sg-sug-chip" data-name="{{ $subject->name }}" disabled>{{ $subject->name }}</button>
                        @endforeach
                    </div>
                </div>
                <div id="promotionRows"></div>
                <div class="sg-summary" id="summaryPanel" style="display:none;"><i class="fas fa-info-circle"></i><span id="summaryText"></span></div>
            </div>
        </div>

        <div class="sg-actions" id="actionsBar" style="display:none;">
            <button type="button" class="sg-btn-secondary" onclick="resetForm()"><i class="fas fa-redo"></i> Réinitialiser</button>
            <button type="submit" class="sg-btn-primary" id="submitBtn">
                <i class="fas fa-save"></i>
                <span>Enregistrer les matières</span>
                <span id="btnLoader" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span>
            </button>
        </div>
    </form>
</div>

<template id="promotionRowTemplate">
    <div class="sg-subj-row" data-promo-id="">
        <div class="sg-subj-hdr">
            <div class="sg-subj-badge"><i class="fas fa-layer-group"></i></div>
            <span class="sg-subj-name"></span>
            <span class="sg-subj-count">0 matière(s)</span>
        </div>
        <input type="hidden" name="promotion_ids[]" value="">
        <div class="sg-chips-zone">
            <div class="sg-chips-list"></div>
            <div class="sg-chip-input-wrap">
                <input type="text" class="sg-chip-input" placeholder="Ajouter une matière… (Entrée pour valider)" autocomplete="off">
                <div class="sg-autocomplete"></div>
            </div>
        </div>
        <input type="hidden" name="subjects_by_promotion[]" value="[]" class="sg-hidden-subjects">
    </div>
</template>

<script>const ALL_SUBJECTS = @json($allSubjects->pluck('name'));</script>
@endsection

@section('another_JS')
<script>
(function(){
    const yearSel=document.getElementById('year_id'),sectorSel=document.getElementById('sector_id'),
          sectorLoader=document.getElementById('sectorLoader'),promotionsCard=document.getElementById('promotionsCard'),
          promotionRows=document.getElementById('promotionRows'),actionsBar=document.getElementById('actionsBar'),
          copyFirstBtn=document.getElementById('copyFirstBtn'),summaryPanel=document.getElementById('summaryPanel'),
          summaryText=document.getElementById('summaryText'),form=document.getElementById('subjectForm'),
          template=document.getElementById('promotionRowTemplate');

    function nextSem(v){if(v==='null'||v===null)return '1';if(v==='1'||v===1)return '2';return 'null';}
    function semLabel(v){if(v==='1'||v===1)return 'S1';if(v==='2'||v===2)return 'S2';return 'S1+S2';}
    function semNorm(v){if(v===1||v==='1')return '1';if(v===2||v==='2')return '2';return 'null';}
    function esc(s){const d=document.createElement('div');d.textContent=s??'';return d.innerHTML;}

    let activeRow=null;

    yearSel.addEventListener('change',()=>{
        const id=yearSel.value; if(!id) return;
        sectorSel.innerHTML=''; sectorSel.disabled=true; sectorLoader.style.display='block';
        fetch(`/api/subject-sectors-by-year/${id}`).then(r=>r.json()).then(d=>{
            let h='<option value="">— Choisissez une filière —</option>';
            d.forEach(s=>{h+=`<option value="${s.id}">${s.name}</option>`;});
            sectorSel.innerHTML=h; sectorSel.disabled=false;
        }).finally(()=>sectorLoader.style.display='none');
        promotionsCard.style.display='none'; actionsBar.style.display='none';
    });

    sectorSel.addEventListener('change',()=>{
        const yId=yearSel.value,sId=sectorSel.value; if(!sId){promotionsCard.style.display='none';return;}
        promotionRows.innerHTML='<div style="color:var(--sg-text-3);font-size:.8rem;padding:.5rem;"><i class="fas fa-spinner fa-spin" style="margin-right:.4rem;"></i>Chargement…</div>';
        promotionsCard.style.display='block';
        fetch(`/api/subject-promotions/${yId}/${sId}`).then(r=>r.json()).then(data=>{
            promotionRows.innerHTML='';
            if(!data.length){promotionRows.innerHTML='<p style="color:var(--sg-text-3);font-size:.8rem;text-align:center;padding:.5rem;">Aucune promotion trouvée.</p>';actionsBar.style.display='none';copyFirstBtn.style.display='none';return;}
            data.forEach((promo,idx)=>promotionRows.appendChild(buildRow(promo,idx)));
            actionsBar.style.display='flex'; copyFirstBtn.style.display=data.length>1?'flex':'none'; setActiveRow(null); updateSummary();
        });
    });

    function setActiveRow(row){
        document.querySelectorAll('.sg-subj-row.active').forEach(r=>r.classList.remove('active'));
        activeRow=row;
        if(!row){
            document.getElementById('suggestionsLabelText').textContent='Cliquez sur un champ de promotion pour activer les suggestions';
            document.querySelectorAll('.sg-sug-chip').forEach(c=>{c.disabled=true;c.classList.remove('in');}); return;
        }
        row.classList.add('active');
        const name=row.querySelector('.sg-subj-name').textContent;
        document.getElementById('suggestionsLabelText').innerHTML=`<i class="fas fa-arrow-right" style="font-size:.62rem;"></i> Ajouter à <strong>${esc(name)}</strong> :`;
        const cur=Array.from(row.querySelectorAll('.sg-chip')).map(c=>c.dataset.name.toLowerCase());
        document.querySelectorAll('.sg-sug-chip').forEach(c=>{c.disabled=false;c.classList.toggle('in',cur.includes(c.dataset.name.toLowerCase()));});
    }

    function buildRow(promo,idx){
        const frag=template.content.cloneNode(true),row=frag.querySelector('.sg-subj-row');
        row.dataset.promoId=promo.id;
        row.querySelector('input[name="promotion_ids[]"]').value=promo.id;
        row.querySelector('.sg-subj-name').textContent=promo.promotion_sector;
        const chipsList=row.querySelector('.sg-chips-list'),hiddenIn=row.querySelector('.sg-hidden-subjects'),countEl=row.querySelector('.sg-subj-count'),inputEl=row.querySelector('.sg-chip-input'),dropdown=row.querySelector('.sg-autocomplete'),zone=row.querySelector('.sg-chips-zone');
        (promo.subjects||[]).forEach(s=>addChip(chipsList,hiddenIn,countEl,s.name,semNorm(s.semester)));
        inputEl.addEventListener('focus',()=>setActiveRow(inputEl.closest('.sg-subj-row')));
        zone.addEventListener('click',()=>inputEl.focus());
        inputEl.addEventListener('input',()=>renderDropdown(dropdown,inputEl.value.trim().toLowerCase(),chipsList,hiddenIn,countEl,inputEl));
        inputEl.addEventListener('keydown',e=>{
            const items=dropdown.querySelectorAll('.sg-ac-item'),sel=dropdown.querySelector('.sg-ac-item.sel');
            if(e.key==='ArrowDown'){e.preventDefault();const n=sel?sel.nextElementSibling:items[0];if(n){sel?.classList.remove('sel');n.classList.add('sel');}}
            else if(e.key==='ArrowUp'){e.preventDefault();const p=sel?sel.previousElementSibling:null;if(p){sel?.classList.remove('sel');p.classList.add('sel');}}
            else if(e.key==='Enter'||e.key===';'){e.preventDefault();const name=sel?sel.dataset.name:inputEl.value.trim().replace(/;$/,'');if(name){addChip(chipsList,hiddenIn,countEl,name,'null',true);inputEl.value='';dropdown.classList.remove('open');refreshSuggestions();updateSummary();}}
            else if(e.key==='Backspace'&&!inputEl.value){const chips=chipsList.querySelectorAll('.sg-chip');if(chips.length)chips[chips.length-1].remove();syncHidden(chipsList,hiddenIn,countEl);refreshSuggestions();updateSummary();}
            else if(e.key==='Escape')dropdown.classList.remove('open');
        });
        document.addEventListener('click',e=>{if(!zone.contains(e.target))dropdown.classList.remove('open');},{passive:true});
        return frag;
    }

    function addChip(chipsList,hiddenIn,countEl,name,sem='null',check=false){
        name=name.trim(); if(!name) return;
        sem=semNorm(sem);
        if(check){const ex=Array.from(chipsList.querySelectorAll('.sg-chip')).map(c=>c.dataset.name.toLowerCase());if(ex.includes(name.toLowerCase()))return;}
        const chip=document.createElement('span');
        chip.className='sg-chip'; chip.dataset.name=name; chip.dataset.sem=sem;
        chip.innerHTML=`${esc(name)}<button type="button" class="sg-chip-sem" data-sem="${sem}" title="Cliquer pour changer">${semLabel(sem)}</button><button type="button" class="sg-chip-remove" aria-label="Supprimer"><i class="fas fa-times"></i></button>`;
        chip.querySelector('.sg-chip-sem').addEventListener('click',e=>{
            e.stopPropagation(); const n=nextSem(chip.dataset.sem); chip.dataset.sem=n; const b=chip.querySelector('.sg-chip-sem'); b.dataset.sem=n; b.textContent=semLabel(n); syncHidden(chipsList,hiddenIn,countEl);
        });
        chip.querySelector('.sg-chip-remove').addEventListener('click',()=>{chip.style.animation='chip-in .12s ease reverse';setTimeout(()=>{chip.remove();syncHidden(chipsList,hiddenIn,countEl);refreshSuggestions();updateSummary();},100);});
        chipsList.appendChild(chip); syncHidden(chipsList,hiddenIn,countEl);
    }

    function syncHidden(chipsList,hiddenIn,countEl){
        const entries=Array.from(chipsList.querySelectorAll('.sg-chip')).map(c=>({name:c.dataset.name,semester:c.dataset.sem==='null'?null:parseInt(c.dataset.sem)}));
        hiddenIn.value=JSON.stringify(entries); countEl.textContent=`${entries.length} matière(s)`; countEl.classList.toggle('has-items',entries.length>0);
    }

    function renderDropdown(dropdown,query,chipsList,hiddenIn,countEl,inputEl){
        if(!query){dropdown.classList.remove('open');return;}
        const cur=Array.from(chipsList.querySelectorAll('.sg-chip')).map(c=>c.dataset.name.toLowerCase());
        const matches=ALL_SUBJECTS.filter(s=>s.toLowerCase().includes(query)&&!cur.includes(s.toLowerCase())).slice(0,8);
        if(!matches.length){dropdown.classList.remove('open');return;}
        const re=new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')})`, 'gi');
        dropdown.innerHTML=matches.map(s=>`<div class="sg-ac-item" data-name="${esc(s)}"><i class="fas fa-plus"></i><span>${esc(s).replace(re,'<em>$1</em>')}</span></div>`).join('');
        dropdown.classList.add('open');
        dropdown.querySelectorAll('.sg-ac-item').forEach(item=>{item.addEventListener('mousedown',e=>{e.preventDefault();addChip(chipsList,hiddenIn,countEl,item.dataset.name,'null',true);inputEl.value='';dropdown.classList.remove('open');inputEl.focus();refreshSuggestions();updateSummary();});});
    }

    function refreshSuggestions(){
        if(!activeRow) return;
        const cur=Array.from(activeRow.querySelectorAll('.sg-chip')).map(c=>c.dataset.name.toLowerCase());
        document.querySelectorAll('.sg-sug-chip').forEach(c=>c.classList.toggle('in',cur.includes(c.dataset.name.toLowerCase())));
    }

    function updateSummary(){
        const rows=promotionRows.querySelectorAll('.sg-subj-row');
        let total=0,empty=0;
        rows.forEach(r=>{const n=r.querySelectorAll('.sg-chip').length;total+=n;if(!n)empty++;});
        if(!rows.length){summaryPanel.style.display='none';return;}
        summaryPanel.style.display='flex';
        if(empty>0){summaryPanel.className='sg-summary';summaryText.textContent=`${empty} promotion(s) sans matière — elles seront vidées à l'enregistrement.`;}
        else{summaryPanel.className='sg-summary ok';summaryText.textContent=`Tout est renseigné : ${total} matière(s) sur ${rows.length} promotion(s).`;}
    }

    copyFirstBtn.addEventListener('click',()=>{
        const rows=promotionRows.querySelectorAll('.sg-subj-row'); if(rows.length<2) return;
        const first=Array.from(rows[0].querySelectorAll('.sg-chip')).map(c=>({name:c.dataset.name,sem:c.dataset.sem}));
        if(!first.length){alert('La première promotion n\'a aucune matière à copier.');return;}
        rows.forEach((row,i)=>{if(i===0)return;const cl=row.querySelector('.sg-chips-list'),hi=row.querySelector('.sg-hidden-subjects'),co=row.querySelector('.sg-subj-count');cl.innerHTML='';first.forEach(c=>addChip(cl,hi,co,c.name,c.sem));});
        refreshSuggestions(); updateSummary();
    });

    document.querySelectorAll('.sg-sug-chip').forEach(chip=>{
        chip.addEventListener('click',()=>{
            if(!activeRow) return;
            const cl=activeRow.querySelector('.sg-chips-list'),hi=activeRow.querySelector('.sg-hidden-subjects'),co=activeRow.querySelector('.sg-subj-count');
            addChip(cl,hi,co,chip.dataset.name,'null',true); refreshSuggestions(); updateSummary(); activeRow.querySelector('.sg-chip-input')?.focus();
        });
    });

    form.addEventListener('submit',()=>{document.getElementById('btnLoader').style.display='inline';document.querySelector('#submitBtn span').style.display='none';});
    window.resetForm=function(){yearSel.value='';sectorSel.value='';sectorSel.disabled=true;sectorSel.innerHTML='<option value="">—</option>';promotionsCard.style.display='none';actionsBar.style.display='none';promotionRows.innerHTML='';activeRow=null;setActiveRow(null);};
})();
</script>
@endsection
