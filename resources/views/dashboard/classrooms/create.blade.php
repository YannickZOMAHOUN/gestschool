@extends('layouts.template')

@section('breadcrumb', 'Gestion des Classes')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,600;0,700&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=JetBrains+Mono:wght@400;500;600&display=swap');
:root{--sg-bg:#f6f7f3;--sg-bg-2:#eef0ea;--sg-surface:#fff;--sg-surface-2:#f1f3ee;--sg-surface-3:#e8ebe3;--sg-accent:#3a6b35;--sg-accent-mid:#5a9a52;--sg-accent-light:#eef4ec;--sg-accent-bg:rgba(58,107,53,.07);--sg-accent-bg2:rgba(58,107,53,.13);--sg-accent-glow:rgba(58,107,53,.18);--sg-text:#1c2318;--sg-text-2:#4a5544;--sg-text-3:#8a9880;--sg-border:#e2e5da;--sg-border-2:#cbd2c2;--sg-border-3:#b5bfaa;--sg-success:#2e7d4f;--sg-success-bg:rgba(46,125,79,.08);--sg-danger:#a84040;--sg-danger-bg:rgba(168,64,64,.07);--sg-warn:#9a6a1a;--sg-warn-bg:rgba(154,106,26,.08);--sg-font-display:'Lora',Georgia,serif;--sg-font-ui:'DM Sans',system-ui,sans-serif;--sg-font-mono:'JetBrains Mono','Fira Mono',monospace;--sg-radius:10px;--sg-radius-sm:7px;--sg-t:.17s cubic-bezier(.4,0,.2,1);}
html[data-theme=dark]{--sg-bg:#131710;--sg-surface:#1e2419;--sg-surface-2:#252c20;--sg-surface-3:#2d3527;--sg-accent:#6abf60;--sg-accent-mid:#85d47a;--sg-accent-light:rgba(106,191,96,.12);--sg-accent-bg:rgba(106,191,96,.09);--sg-accent-bg2:rgba(106,191,96,.16);--sg-accent-glow:rgba(106,191,96,.20);--sg-text:#e4ead8;--sg-text-2:#adb9a0;--sg-text-3:#627057;--sg-border:rgba(255,255,255,.08);--sg-border-2:rgba(255,255,255,.13);}
.sg,.sg *{font-family:var(--sg-font-ui);box-sizing:border-box;}
.sg{max-width:900px;margin:0 auto;padding:0 0 4rem;color:var(--sg-text);}

.sg-hdr{display:flex;align-items:center;gap:.85rem;margin-bottom:1.75rem;flex-wrap:wrap;}
.sg-hdr-icon{width:42px;height:42px;border-radius:var(--sg-radius);flex-shrink:0;display:flex;align-items:center;justify-content:center;background:var(--sg-accent-light);border:1px solid rgba(58,107,53,.18);color:var(--sg-accent);font-size:.95rem;box-shadow:0 4px 12px var(--sg-accent-glow);}
.sg-eyebrow{font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.14em;color:var(--sg-accent);margin-bottom:.15rem;}
.sg-title{font-family:var(--sg-font-display);font-size:1.28rem;font-weight:700;color:var(--sg-text);letter-spacing:-.02em;margin:0;}
.sg-subtitle{font-size:.75rem;color:var(--sg-text-3);margin:.1rem 0 0;}

.sg-stat-pill{display:flex;align-items:center;gap:.9rem;background:var(--sg-surface);border:1px solid var(--sg-border);border-radius:var(--sg-radius);padding:.55rem .9rem;box-shadow:0 1px 3px rgba(28,35,24,.04);margin-left:auto;}
.sg-stat-pill-div{width:1px;height:24px;background:var(--sg-border-2);}
.sg-stat{text-align:center;}
.sg-stat-val{display:block;font-family:var(--sg-font-display);font-size:1.15rem;font-weight:700;color:var(--sg-accent);letter-spacing:-.03em;line-height:1;}
.sg-stat-key{font-size:.57rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--sg-text-3);margin-top:.12rem;}

.sg-alert{display:flex;align-items:center;gap:.65rem;padding:.72rem .9rem;border-radius:var(--sg-radius-sm);font-size:.77rem;font-weight:500;border:1px solid;margin-bottom:1rem;animation:sg-down .2s ease both;}
@keyframes sg-down{from{opacity:0;transform:translateY(-5px)}to{opacity:1;transform:translateY(0)}}
.sg-alert span{flex:1;}
.sg-alert-close{background:transparent;border:none;cursor:pointer;color:inherit;opacity:.55;font-size:.72rem;padding:0;}
.sg-alert-close:hover{opacity:1;}
.sg-alert-success{background:var(--sg-success-bg);border-color:rgba(46,125,79,.2);color:var(--sg-success);}
.sg-alert-danger{background:var(--sg-danger-bg);border-color:rgba(168,64,64,.2);color:var(--sg-danger);}

.sg-card{background:var(--sg-surface);border:1px solid var(--sg-border);border-radius:var(--sg-radius);box-shadow:0 1px 2px rgba(28,35,24,.04);margin-bottom:1rem;animation:sg-up .26s ease both;}
@keyframes sg-up{from{opacity:0;transform:translateY(9px)}to{opacity:1;transform:translateY(0)}}
.sg-card-hdr{display:flex;align-items:center;gap:.75rem;padding:1rem 1.2rem;border-bottom:1px solid var(--sg-border);flex-wrap:wrap;}
.sg-card-body{padding:1.2rem;}
.sg-step{width:27px;height:27px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:var(--sg-accent);color:#fff;font-size:.72rem;font-weight:700;box-shadow:0 3px 9px var(--sg-accent-glow);}
.sg-card-title{font-family:var(--sg-font-display);font-size:.9rem;font-weight:600;color:var(--sg-text);margin:0;}
.sg-card-desc{font-size:.71rem;color:var(--sg-text-3);margin:.08rem 0 0;}

.sg-filters{display:grid;grid-template-columns:1fr 1fr auto;gap:.9rem;align-items:end;}
@media(max-width:640px){.sg-filters{grid-template-columns:1fr;}}

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

/* General counter */
.sg-general-wrap{display:flex;align-items:center;background:var(--sg-surface-2);border:1px solid var(--sg-border-2);border-radius:var(--sg-radius-sm);overflow:hidden;transition:border-color var(--sg-t);}
.sg-general-wrap:focus-within{border-color:var(--sg-accent);}
.sg-gen-btn{width:34px;height:38px;background:transparent;border:none;color:var(--sg-text-3);cursor:pointer;font-size:.62rem;display:flex;align-items:center;justify-content:center;transition:all var(--sg-t);}
.sg-gen-btn:hover:not(:disabled){background:var(--sg-accent-light);color:var(--sg-accent);}
.sg-gen-btn:disabled{opacity:.35;cursor:not-allowed;}
.sg-gen-input{flex:1;min-width:44px;background:transparent;border:none;outline:none;color:var(--sg-text);font-family:var(--sg-font-mono);font-size:.86rem;font-weight:700;text-align:center;}
.sg-gen-input::placeholder{color:var(--sg-text-3);font-weight:400;font-size:.75rem;}
.sg-gen-input:disabled{opacity:.35;}

/* Legend */
.sg-legend{display:flex;align-items:center;gap:.72rem;flex-wrap:wrap;}
.sg-legend-item{display:flex;align-items:center;gap:.3rem;font-size:.67rem;font-weight:600;color:var(--sg-text-3);}
.sg-legend-item i{font-size:.4rem;}
.legend-new i{color:var(--sg-accent);}
.legend-existing i{color:var(--sg-success);}
.legend-modified i{color:var(--sg-warn);}
.legend-locked i{color:var(--sg-warn);font-size:.56rem;}

/* Promo row */
.sg-promo-row{background:var(--sg-surface-2);border:1px solid var(--sg-border);border-left:3px solid var(--sg-border-2);border-radius:0 var(--sg-radius-sm) var(--sg-radius-sm) 0;padding:.85rem 1.05rem;margin-bottom:.45rem;display:flex;align-items:center;gap:.9rem;animation:sg-up .2s ease both;transition:border-color var(--sg-t);flex-wrap:wrap;}
.sg-promo-row:hover{border-color:var(--sg-border-2);}
.sg-promo-row[data-status=new]{border-left-color:var(--sg-accent);}
.sg-promo-row[data-status=existing]{border-left-color:var(--sg-success);}
.sg-promo-row[data-status=modified]{border-left-color:var(--sg-warn);}
.sg-promo-info{display:flex;align-items:center;gap:.65rem;flex:1;min-width:0;}
.sg-promo-icon{width:30px;height:30px;border-radius:8px;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:var(--sg-surface-3);border:1px solid var(--sg-border-2);color:var(--sg-text-3);font-size:.7rem;transition:all var(--sg-t);}
.sg-promo-row[data-status=new] .sg-promo-icon{color:var(--sg-accent);background:var(--sg-accent-bg);border-color:rgba(58,107,53,.2);}
.sg-promo-row[data-status=existing] .sg-promo-icon{color:var(--sg-success);background:var(--sg-success-bg);border-color:rgba(46,125,79,.2);}
.sg-promo-row[data-status=modified] .sg-promo-icon{color:var(--sg-warn);background:var(--sg-warn-bg);border-color:rgba(154,106,26,.2);}
.sg-promo-details{display:flex;flex-direction:column;gap:.16rem;min-width:0;}
.sg-promo-name{font-size:.82rem;font-weight:600;color:var(--sg-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.sg-promo-status{font-size:.64rem;font-weight:700;padding:.1rem .45rem;border-radius:20px;display:inline-block;width:fit-content;border:1px solid;}
.sg-promo-row[data-status=new] .sg-promo-status{background:var(--sg-accent-bg2);color:var(--sg-accent);border-color:rgba(58,107,53,.2);}
.sg-promo-row[data-status=existing] .sg-promo-status{background:var(--sg-success-bg);color:var(--sg-success);border-color:rgba(46,125,79,.2);}
.sg-promo-row[data-status=modified] .sg-promo-status{background:var(--sg-warn-bg);color:var(--sg-warn);border-color:rgba(154,106,26,.2);}

/* Preview tags */
.sg-promo-preview{display:flex;flex-wrap:wrap;gap:.26rem;margin-left:.4rem;}
.sg-class-tag{font-family:var(--sg-font-mono);font-size:.62rem;font-weight:500;padding:.12rem .38rem;border-radius:4px;background:var(--sg-surface-3);border:1px solid var(--sg-border-2);color:var(--sg-text-2);transition:all var(--sg-t);}
.sg-class-tag.is-new{background:var(--sg-accent-bg2);border-color:rgba(58,107,53,.25);color:var(--sg-accent);}
.sg-class-tag.is-removed{background:var(--sg-danger-bg);border-color:rgba(168,64,64,.2);color:var(--sg-danger);text-decoration:line-through;opacity:.6;}
.sg-class-tag.is-locked{background:var(--sg-warn-bg);border-color:rgba(154,106,26,.2);color:var(--sg-warn);}
.sg-class-tag.is-locked::after{content:'\f023';font-family:'Font Awesome 5 Free';font-weight:900;font-size:.5rem;margin-left:.26rem;opacity:.8;}

/* Counter */
.sg-counter{display:flex;align-items:center;background:var(--sg-surface-3);border:1px solid var(--sg-border-2);border-radius:var(--sg-radius-sm);overflow:hidden;flex-shrink:0;}
.sg-counter-btn{width:30px;height:36px;background:transparent;border:none;color:var(--sg-text-3);cursor:pointer;font-size:.6rem;display:flex;align-items:center;justify-content:center;transition:all var(--sg-t);}
.sg-counter-btn:hover:not(:disabled){background:var(--sg-accent-light);color:var(--sg-accent);}
.sg-counter-btn:disabled{opacity:.35;cursor:not-allowed;}
.sg-count-display{display:flex;flex-direction:column;align-items:center;padding:0 .35rem;min-width:56px;}
.sg-count-input{width:42px;background:transparent;border:none;outline:none;color:var(--sg-text);font-family:var(--sg-font-mono);font-size:.92rem;font-weight:700;text-align:center;padding:.26rem 0 0;line-height:1;}
.sg-count-input::-webkit-inner-spin-button,.sg-count-input::-webkit-outer-spin-button{appearance:none;}
.sg-count-unit{font-size:.55rem;color:var(--sg-text-3);letter-spacing:.04em;text-transform:uppercase;font-weight:600;padding-bottom:.2rem;}

/* Actions */
.sg-actions{display:flex;justify-content:flex-end;gap:.6rem;margin-top:.5rem;animation:sg-up .26s .1s ease both;}
.sg-btn-primary{display:inline-flex;align-items:center;gap:.5rem;padding:.5rem 1.1rem;background:var(--sg-accent);border:1px solid var(--sg-accent);border-radius:var(--sg-radius-sm);color:#fff;font-family:var(--sg-font-ui);font-size:.79rem;font-weight:600;cursor:pointer;box-shadow:0 3px 10px var(--sg-accent-glow);transition:all var(--sg-t);}
.sg-btn-primary:hover{background:var(--sg-accent-mid);border-color:var(--sg-accent-mid);transform:translateY(-1px);box-shadow:0 6px 16px var(--sg-accent-glow);}
.sg-btn-secondary{display:inline-flex;align-items:center;gap:.5rem;padding:.5rem 1rem;background:transparent;border:1px solid var(--sg-border-2);border-radius:var(--sg-radius-sm);color:var(--sg-text-3);font-family:var(--sg-font-ui);font-size:.77rem;font-weight:500;cursor:pointer;transition:all var(--sg-t);}
.sg-btn-secondary:hover{background:var(--sg-surface-2);color:var(--sg-text);}
</style>
@endpush

@section('content')
<div class="sg">

    <div class="sg-hdr">
        <div class="sg-hdr-icon"><i class="fas fa-door-open"></i></div>
        <div>
            <div class="sg-eyebrow">Configuration · Année scolaire</div>
            <h1 class="sg-title">Gestion des Classes</h1>
            <p class="sg-subtitle">Configurez le nombre de classes par promotion</p>
        </div>
        <div class="sg-stat-pill" id="headerStats" style="display:none;">
            <div class="sg-stat"><span class="sg-stat-val" id="statPromotions">0</span><span class="sg-stat-key">Promotions</span></div>
            <div class="sg-stat-pill-div"></div>
            <div class="sg-stat"><span class="sg-stat-val" id="statClasses">0</span><span class="sg-stat-key">Classes total</span></div>
        </div>
    </div>

    @if(session('success'))
    <div class="sg-alert sg-alert-success"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span><button class="sg-alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button></div>
    @endif
    @if($errors->any())
    <div class="sg-alert sg-alert-danger"><i class="fas fa-exclamation-circle"></i><span>{{ $errors->first() }}</span><button class="sg-alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button></div>
    @endif

    <form method="POST" action="{{ route('promotion-classrooms.store') }}" id="classroomForm">
        @csrf

        {{-- Étape 1 --}}
        <div class="sg-card">
            <div class="sg-card-hdr">
                <div class="sg-step">1</div>
                <div><div class="sg-card-title">Sélection</div><div class="sg-card-desc">Année scolaire, filière et nombre global</div></div>
            </div>
            <div class="sg-card-body">
                <div class="sg-filters">
                    <div class="sg-field">
                        <label class="sg-label" for="year_id"><i class="fas fa-calendar-alt"></i> Année</label>
                        <div class="sg-sel-wrap">
                            <select name="year_id" id="year_id" class="sg-sel" required>
                                <option value="">— Choisissez —</option>
                                @foreach($years as $year)<option value="{{ $year->id }}">{{ $year->year }}</option>@endforeach
                            </select>
                            <i class="fas fa-chevron-down sg-sel-arrow"></i>
                            <span class="sg-sel-loader" id="sectorLoader"><i class="fas fa-spinner fa-spin"></i></span>
                        </div>
                    </div>
                    <div class="sg-field">
                        <label class="sg-label" for="sector_id"><i class="fas fa-sitemap"></i> Filière</label>
                        <div class="sg-sel-wrap">
                            <select name="sector_id" id="sector_id" class="sg-sel" disabled required>
                                <option value="">—</option>
                            </select>
                            <i class="fas fa-chevron-down sg-sel-arrow"></i>
                            <span class="sg-sel-loader" id="promotionLoader"><i class="fas fa-spinner fa-spin"></i></span>
                        </div>
                    </div>
                    <div class="sg-field">
                        <label class="sg-label"><i class="fas fa-magic"></i> Appliquer à toutes</label>
                        <div class="sg-general-wrap">
                            <button type="button" class="sg-gen-btn" id="generalMinus" disabled><i class="fas fa-minus"></i></button>
                            <input type="number" id="general_count" class="sg-gen-input" min="1" max="26" placeholder="—" disabled>
                            <button type="button" class="sg-gen-btn" id="generalPlus" disabled><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Étape 2 --}}
        <div class="sg-card" id="promotionsCard" style="display:none;">
            <div class="sg-card-hdr">
                <div class="sg-step">2</div>
                <div style="flex:1;"><div class="sg-card-title">Classes par promotion</div><div class="sg-card-desc">Définissez le nombre de classes pour chaque promotion</div></div>
                <div class="sg-legend">
                    <span class="sg-legend-item legend-new"><i class="fas fa-circle"></i> Nouvelles</span>
                    <span class="sg-legend-item legend-existing"><i class="fas fa-circle"></i> Existantes</span>
                    <span class="sg-legend-item legend-modified"><i class="fas fa-circle"></i> Modifiées</span>
                    <span class="sg-legend-item legend-locked"><i class="fas fa-lock"></i> Verrouillées</span>
                </div>
            </div>
            <div class="sg-card-body">
                <div id="promotionRows"></div>
            </div>
        </div>

        <div class="sg-actions" id="actionsBar" style="display:none;">
            <button type="button" class="sg-btn-secondary" onclick="resetForm()"><i class="fas fa-redo"></i> Réinitialiser</button>
            <button type="submit" class="sg-btn-primary" id="submitBtn">
                <i class="fas fa-save"></i>
                <span id="submitLabel">Enregistrer les classes</span>
                <span id="submitLoader" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span>
            </button>
        </div>
    </form>
</div>

<template id="promoRowTpl">
    <div class="sg-promo-row" data-promo-id="" data-original-count="0">
        <div class="sg-promo-info">
            <div class="sg-promo-icon"><i class="fas fa-layer-group"></i></div>
            <div class="sg-promo-details">
                <span class="sg-promo-name"></span>
                <span class="sg-promo-status"></span>
            </div>
            <div class="sg-promo-preview" id="classPreview"></div>
        </div>
        <input type="hidden" name="promotions[]" value="">
        <div class="sg-counter">
            <button type="button" class="sg-counter-btn sg-row-minus"><i class="fas fa-minus"></i></button>
            <div class="sg-count-display">
                <input type="number" name="counts[]" class="sg-count-input" min="0" max="26" required>
                <span class="sg-count-unit">classe(s)</span>
            </div>
            <button type="button" class="sg-counter-btn sg-row-plus"><i class="fas fa-plus"></i></button>
        </div>
    </div>
</template>
@endsection

@section('another_JS')
<script>
(function(){
    const yearSel=document.getElementById('year_id'),sectorSel=document.getElementById('sector_id'),
          sectorLoader=document.getElementById('sectorLoader'),promoLoader=document.getElementById('promotionLoader'),
          promotionsCard=document.getElementById('promotionsCard'),promotionRows=document.getElementById('promotionRows'),
          actionsBar=document.getElementById('actionsBar'),headerStats=document.getElementById('headerStats'),
          generalCount=document.getElementById('general_count'),generalMinus=document.getElementById('generalMinus'),
          generalPlus=document.getElementById('generalPlus'),tpl=document.getElementById('promoRowTpl'),
          form=document.getElementById('classroomForm');

    yearSel.addEventListener('change',()=>{
        const id=yearSel.value; if(!id) return;
        sectorSel.innerHTML=''; sectorSel.disabled=true; sectorLoader.style.display='block';
        fetch(`/api/classroom-sectors-by-year/${id}`).then(r=>r.json()).then(d=>{
            let h='<option value="">— Choisissez une filière —</option>';
            d.forEach(s=>{h+=`<option value="${s.id}">${s.name}</option>`;});
            sectorSel.innerHTML=h; sectorSel.disabled=false;
        }).finally(()=>sectorLoader.style.display='none');
        promotionsCard.style.display='none'; actionsBar.style.display='none'; headerStats.style.display='none'; disableGeneral();
    });

    sectorSel.addEventListener('change',()=>{
        const yId=yearSel.value,sId=sectorSel.value; if(!sId){promotionsCard.style.display='none';return;}
        promotionRows.innerHTML='<div style="text-align:center;padding:1.5rem;color:var(--sg-text-3);font-size:.8rem;"><i class="fas fa-spinner fa-spin" style="color:var(--sg-accent);margin-right:.4rem;"></i>Chargement…</div>';
        promotionsCard.style.display='block'; actionsBar.style.display='none'; promoLoader.style.display='block';
        Promise.all([
            fetch(`/api/classroom-promotions/${yId}/${sId}`).then(r=>r.json()),
            fetch(`/api/existing-classrooms/${yId}/${sId}`).then(r=>r.json())
        ]).then(([promotions,existing])=>{
            promotionRows.innerHTML='';
            if(!promotions.length){promotionRows.innerHTML='<p style="text-align:center;color:var(--sg-text-3);font-size:.8rem;padding:.5rem;">Aucune promotion trouvée.</p>';return;}
            const existingMap={};
            existing.forEach(c=>{if(!existingMap[c.promotion_sector_id])existingMap[c.promotion_sector_id]=[];existingMap[c.promotion_sector_id].push({name:c.name,has_notes:c.has_notes});});
            promotions.forEach((promo,idx)=>{promotionRows.appendChild(buildRow(promo,existingMap[promo.id]||[],idx));});
            actionsBar.style.display='flex'; headerStats.style.display='flex'; enableGeneral(); updateStats();
        }).finally(()=>promoLoader.style.display='none');
    });

    function buildRow(promo,existingClasses,idx){
        const frag=tpl.content.cloneNode(true),row=frag.querySelector('.sg-promo-row');
        const origCount=existingClasses.length,lockedCount=existingClasses.filter(c=>c.has_notes).length;
        row.dataset.promoId=promo.id; row.dataset.originalCount=origCount; row.dataset.lockedCount=lockedCount; row.dataset.promoName=promo.name;
        row.querySelector('.sg-promo-name').textContent=promo.name;
        row.querySelector('input[name="promotions[]"]').value=promo.id;
        const countInput=row.querySelector('.sg-count-input'),minusBtn=row.querySelector('.sg-row-minus'),plusBtn=row.querySelector('.sg-row-plus'),previewEl=row.querySelector('.sg-promo-preview'),statusEl=row.querySelector('.sg-promo-status');
        countInput.value=origCount; countInput.min=lockedCount;
        updateRowStatus(row,origCount,origCount,lockedCount,promo.name,existingClasses,previewEl,statusEl);
        if(origCount<=lockedCount) minusBtn.disabled=true;
        row.style.animationDelay=`${idx*.05}s`;
        minusBtn.addEventListener('click',()=>{const v=Math.max(lockedCount,parseInt(countInput.value||0)-1);countInput.value=v;minusBtn.disabled=(v<=lockedCount);onCountChange(row,v,origCount,lockedCount,promo.name,existingClasses,previewEl,statusEl);});
        plusBtn.addEventListener('click',()=>{const v=Math.min(26,parseInt(countInput.value||0)+1);countInput.value=v;minusBtn.disabled=(v<=lockedCount);onCountChange(row,v,origCount,lockedCount,promo.name,existingClasses,previewEl,statusEl);});
        countInput.addEventListener('input',()=>{let v=parseInt(countInput.value)||0;v=Math.max(lockedCount,Math.min(26,v));countInput.value=v;minusBtn.disabled=(v<=lockedCount);onCountChange(row,v,origCount,lockedCount,promo.name,existingClasses,previewEl,statusEl);});
        return frag;
    }

    function onCountChange(row,n,orig,locked,name,ex,prev,stat){updateRowStatus(row,n,orig,locked,name,ex,prev,stat);updateStats();syncGeneral();}

    function updateRowStatus(row,n,orig,locked,name,ex,prev,stat){
        let status,txt;
        if(orig===0&&n>0){status='new';txt=`Nouvelle — ${n} classe(s)`;}
        else if(orig>0&&n===orig){status='existing';txt=`${orig} existante(s)`;if(locked>0)txt+=` · ${locked} verrouillée(s)`;}
        else if(orig>0&&n!==orig){status='modified';const d=n-orig;txt=`Modifié — ${d>0?'+'+d:d} classe(s)`;if(locked>0)txt+=` · ${locked} verrouillée(s)`;}
        else{status='new';txt=n>0?`${n} classe(s)`:'Aucune classe';}
        row.dataset.status=status; stat.textContent=txt; renderPreview(prev,name,n,ex);
    }

    function renderPreview(el,name,count,existing){
        const base=name.replace(/-[A-Z]$/,''); el.innerHTML='';
        existing.forEach((c,i)=>{const t=document.createElement('span');t.className='sg-class-tag';t.textContent=c.name;if(c.has_notes)t.classList.add('is-locked');else if(i>=count)t.classList.add('is-removed');el.appendChild(t);});
        for(let i=existing.length;i<count;i++){const t=document.createElement('span');t.className='sg-class-tag is-new';t.textContent=count>1?`${base}-${String.fromCharCode(65+i)}`:base;el.appendChild(t);}
        const tags=el.querySelectorAll('.sg-class-tag');
        if(tags.length>5){tags.forEach((t,i)=>{if(i>=4)t.style.display='none';});const more=document.createElement('span');more.className='sg-class-tag';more.style.color='var(--sg-text-3)';more.textContent=`+${tags.length-4}`;el.appendChild(more);}
    }

    function enableGeneral(){generalCount.disabled=false;generalMinus.disabled=false;generalPlus.disabled=false;}
    function disableGeneral(){generalCount.disabled=true;generalMinus.disabled=true;generalPlus.disabled=true;generalCount.value='';}

    generalMinus.addEventListener('click',()=>{const v=Math.max(1,parseInt(generalCount.value||1)-1);generalCount.value=v;applyGeneral(v);});
    generalPlus.addEventListener('click',()=>{const v=Math.min(26,parseInt(generalCount.value||0)+1);generalCount.value=v;applyGeneral(v);});
    generalCount.addEventListener('input',()=>{const v=parseInt(generalCount.value)||0;if(v>0)applyGeneral(v);});

    function applyGeneral(v){
        promotionRows.querySelectorAll('.sg-promo-row').forEach(row=>{
            const ci=row.querySelector('.sg-count-input'),mb=row.querySelector('.sg-row-minus'),prev=row.querySelector('.sg-promo-preview'),stat=row.querySelector('.sg-promo-status');
            const orig=parseInt(row.dataset.originalCount||0),locked=parseInt(row.dataset.lockedCount||0),name=row.dataset.promoName||'';
            const sv=Math.max(locked,v); ci.value=sv; mb.disabled=(sv<=locked);
            const exTags=Array.from(row.querySelectorAll('.sg-class-tag:not(.is-new)')).filter(t=>!t.textContent.startsWith('+')).map(t=>({name:t.textContent.replace(/\s+/g,''),has_notes:t.classList.contains('is-locked')}));
            updateRowStatus(row,sv,orig,locked,name,exTags,prev,stat);
        });
        updateStats();
    }

    function syncGeneral(){
        const inputs=promotionRows.querySelectorAll('.sg-count-input');
        if(!inputs.length) return;
        const vals=Array.from(inputs).map(i=>parseInt(i.value)||0);
        generalCount.value=vals.every(v=>v===vals[0])&&vals[0]>0?vals[0]:'';
    }

    function updateStats(){
        const rows=promotionRows.querySelectorAll('.sg-promo-row');
        let total=0; rows.forEach(r=>{total+=parseInt(r.querySelector('.sg-count-input')?.value||0);});
        document.getElementById('statPromotions').textContent=rows.length;
        document.getElementById('statClasses').textContent=total;
    }

    form.addEventListener('submit',()=>{document.getElementById('submitLabel').style.display='none';document.getElementById('submitLoader').style.display='inline';});
    window.resetForm=function(){yearSel.value='';sectorSel.value='';sectorSel.disabled=true;sectorSel.innerHTML='<option value="">—</option>';promotionsCard.style.display='none';actionsBar.style.display='none';headerStats.style.display='none';promotionRows.innerHTML='';disableGeneral();};
})();
</script>
@endsection
