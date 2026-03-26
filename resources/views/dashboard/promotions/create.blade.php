@extends('layouts.template')

@section('breadcrumb', 'Gestion des Promotions')

@push('styles')
<style>
/* Fonts */
@import url('https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,600;0,700;1,500&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap');

/* ── Tokens partagés Clean Sage ── */
:root{--sg-bg:#f6f7f3;--sg-bg-2:#eef0ea;--sg-surface:#fff;--sg-surface-2:#f1f3ee;--sg-surface-3:#e8ebe3;--sg-accent:#3a6b35;--sg-accent-mid:#5a9a52;--sg-accent-light:#eef4ec;--sg-accent-bg:rgba(58,107,53,.07);--sg-accent-bg2:rgba(58,107,53,.13);--sg-accent-glow:rgba(58,107,53,.18);--sg-text:#1c2318;--sg-text-2:#4a5544;--sg-text-3:#8a9880;--sg-border:#e2e5da;--sg-border-2:#cbd2c2;--sg-border-3:#b5bfaa;--sg-success:#2e7d4f;--sg-success-bg:rgba(46,125,79,.08);--sg-danger:#a84040;--sg-danger-bg:rgba(168,64,64,.07);--sg-warn:#9a6a1a;--sg-warn-bg:rgba(154,106,26,.08);--sg-font-display:'Lora',Georgia,serif;--sg-font-ui:'DM Sans',system-ui,sans-serif;--sg-radius:10px;--sg-radius-sm:7px;--sg-t:.17s cubic-bezier(.4,0,.2,1);}
html[data-theme=dark]{--sg-bg:#131710;--sg-surface:#1e2419;--sg-surface-2:#252c20;--sg-surface-3:#2d3527;--sg-accent:#6abf60;--sg-accent-mid:#85d47a;--sg-accent-light:rgba(106,191,96,.12);--sg-accent-bg:rgba(106,191,96,.09);--sg-accent-bg2:rgba(106,191,96,.16);--sg-accent-glow:rgba(106,191,96,.20);--sg-text:#e4ead8;--sg-text-2:#adb9a0;--sg-text-3:#627057;--sg-border:rgba(255,255,255,.08);--sg-border-2:rgba(255,255,255,.13);}

.sg,.sg *{font-family:var(--sg-font-ui);box-sizing:border-box;}
.sg{max-width:900px;margin:0 auto;padding:0 0 4rem;color:var(--sg-text);}

/* Header */
.sg-hdr{display:flex;align-items:center;gap:.85rem;margin-bottom:1.75rem;flex-wrap:wrap;}
.sg-hdr-icon{width:42px;height:42px;border-radius:var(--sg-radius);flex-shrink:0;display:flex;align-items:center;justify-content:center;background:var(--sg-accent-light);border:1px solid rgba(58,107,53,.18);color:var(--sg-accent);font-size:.95rem;box-shadow:0 4px 12px var(--sg-accent-glow);}
html[data-theme=dark] .sg-hdr-icon{border-color:rgba(106,191,96,.18);}
.sg-eyebrow{font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.14em;color:var(--sg-accent);margin-bottom:.15rem;}
.sg-title{font-family:var(--sg-font-display);font-size:1.28rem;font-weight:700;color:var(--sg-text);letter-spacing:-.02em;margin:0;}
.sg-subtitle{font-size:.75rem;color:var(--sg-text-3);margin:.1rem 0 0;}

/* Stat pill */
.sg-stat-pill{display:flex;align-items:center;gap:.9rem;background:var(--sg-surface);border:1px solid var(--sg-border);border-radius:var(--sg-radius);padding:.55rem .9rem;box-shadow:0 1px 3px rgba(28,35,24,.04);margin-left:auto;}
.sg-stat-pill-div{width:1px;height:24px;background:var(--sg-border-2);}
.sg-stat{text-align:center;}
.sg-stat-val{display:block;font-family:var(--sg-font-display);font-size:1.15rem;font-weight:700;color:var(--sg-accent);letter-spacing:-.03em;line-height:1;}
.sg-stat-key{font-size:.57rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--sg-text-3);margin-top:.12rem;}

/* Alert */
.sg-alert{display:flex;align-items:center;gap:.65rem;padding:.72rem .9rem;border-radius:var(--sg-radius-sm);font-size:.77rem;font-weight:500;border:1px solid;margin-bottom:1rem;animation:sg-down .2s ease both;}
@keyframes sg-down{from{opacity:0;transform:translateY(-5px)}to{opacity:1;transform:translateY(0)}}
.sg-alert span{flex:1;}
.sg-alert-close{background:transparent;border:none;cursor:pointer;color:inherit;opacity:.55;font-size:.72rem;padding:0;}
.sg-alert-close:hover{opacity:1;}
.sg-alert-success{background:var(--sg-success-bg);border-color:rgba(46,125,79,.2);color:var(--sg-success);}
.sg-alert-danger{background:var(--sg-danger-bg);border-color:rgba(168,64,64,.2);color:var(--sg-danger);}

/* Card */
.sg-card{background:var(--sg-surface);border:1px solid var(--sg-border);border-radius:var(--sg-radius);box-shadow:0 1px 2px rgba(28,35,24,.04);margin-bottom:1rem;animation:sg-up .26s ease both;}
@keyframes sg-up{from{opacity:0;transform:translateY(9px)}to{opacity:1;transform:translateY(0)}}
.sg-card-hdr{display:flex;align-items:center;gap:.75rem;padding:1rem 1.2rem;border-bottom:1px solid var(--sg-border);flex-wrap:wrap;}
.sg-card-body{padding:1.2rem;}
.sg-step{width:27px;height:27px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:var(--sg-accent);color:#fff;font-size:.72rem;font-weight:700;box-shadow:0 3px 9px var(--sg-accent-glow);}
.sg-card-title{font-family:var(--sg-font-display);font-size:.9rem;font-weight:600;color:var(--sg-text);margin:0;}
.sg-card-desc{font-size:.71rem;color:var(--sg-text-3);margin:.08rem 0 0;}

/* Field */
.sg-field{display:flex;flex-direction:column;gap:.36rem;max-width:380px;}
.sg-label{font-size:.68rem;font-weight:700;color:var(--sg-text-3);text-transform:uppercase;letter-spacing:.1em;display:flex;align-items:center;gap:.35rem;}
.sg-label i{color:var(--sg-accent);font-size:.64rem;}
.sg-sel-wrap{position:relative;}
.sg-sel{width:100%;padding:.6rem 1.9rem .6rem .78rem;background:var(--sg-surface-2);border:1px solid var(--sg-border-2);border-radius:var(--sg-radius-sm);color:var(--sg-text);font-family:var(--sg-font-ui);font-size:.8rem;appearance:none;cursor:pointer;outline:none;transition:border-color var(--sg-t),box-shadow var(--sg-t);}
.sg-sel:focus{border-color:var(--sg-accent);box-shadow:0 0 0 3px var(--sg-accent-bg);}
.sg-sel:disabled{opacity:.45;cursor:not-allowed;}
.sg-sel option{background:var(--sg-surface-2);color:var(--sg-text);}
.sg-sel-arrow{position:absolute;right:.68rem;top:50%;transform:translateY(-50%);font-size:.56rem;color:var(--sg-text-3);pointer-events:none;}
.sg-sel-loader{position:absolute;right:.68rem;top:50%;transform:translateY(-50%);color:var(--sg-accent);font-size:.72rem;display:none;}

/* Legend */
.sg-legend{display:flex;align-items:center;gap:.72rem;flex-wrap:wrap;}
.sg-legend-item{display:flex;align-items:center;gap:.3rem;font-size:.67rem;font-weight:600;color:var(--sg-text-3);}
.sg-legend-item i{font-size:.4rem;}
.legend-new i{color:var(--sg-accent);}
.legend-existing i{color:var(--sg-success);}
.legend-locked i{color:var(--sg-warn);font-size:.56rem;}

/* Bulk */
.sg-bulk{display:flex;gap:.45rem;margin-bottom:.85rem;flex-wrap:wrap;}
.sg-bulk-btn{display:inline-flex;align-items:center;gap:.42rem;padding:.38rem .82rem;background:var(--sg-accent-bg);color:var(--sg-accent);border:1px solid rgba(58,107,53,.2);border-radius:var(--sg-radius-sm);font-size:.73rem;font-family:var(--sg-font-ui);font-weight:600;cursor:pointer;transition:all var(--sg-t);}
.sg-bulk-btn:hover{background:var(--sg-accent-bg2);border-color:rgba(58,107,53,.35);}
.sg-bulk-danger{background:var(--sg-danger-bg);color:var(--sg-danger);border-color:rgba(168,64,64,.2);}
.sg-bulk-danger:hover{background:rgba(168,64,64,.13);border-color:rgba(168,64,64,.35);}

/* Sector row */
.sg-sector-row{background:var(--sg-surface-2);border:1px solid var(--sg-border);border-radius:var(--sg-radius-sm);padding:.82rem 1.05rem;margin-bottom:.45rem;display:flex;align-items:center;gap:.9rem;animation:sg-up .2s ease both;transition:border-color var(--sg-t);flex-wrap:wrap;}
.sg-sector-row:hover{border-color:var(--sg-border-2);}
.sg-sector-name{font-size:.82rem;font-weight:600;color:var(--sg-text);min-width:100px;flex-shrink:0;}
.sg-checkboxes{display:flex;gap:.45rem;flex-wrap:wrap;flex:1;}
.sg-check-item{position:relative;}
.sg-check-item input[type=checkbox]{position:absolute;opacity:0;width:0;height:0;}
.sg-check-label{display:inline-flex;align-items:center;gap:.42rem;padding:.4rem .82rem;background:var(--sg-surface-3);border:1px solid var(--sg-border-2);border-radius:20px;font-size:.76rem;font-weight:500;color:var(--sg-text-3);cursor:pointer;user-select:none;transition:all var(--sg-t);}
.sg-check-label .ci{font-size:.6rem;opacity:0;transition:opacity var(--sg-t);}
.sg-check-item input:checked+.sg-check-label{background:var(--sg-accent-bg2);border-color:rgba(58,107,53,.3);color:var(--sg-accent);font-weight:600;}
.sg-check-item input:checked+.sg-check-label .ci{opacity:1;}
.sg-check-item.is-existing input:checked+.sg-check-label{background:var(--sg-success-bg);border-color:rgba(46,125,79,.25);color:var(--sg-success);}
.sg-check-item.is-locked .sg-check-label{background:var(--sg-warn-bg);border-color:rgba(154,106,26,.2);color:var(--sg-warn);cursor:not-allowed;opacity:.82;}
.sg-check-item:not(.is-locked) .sg-check-label:hover{border-color:rgba(58,107,53,.28);color:var(--sg-text-2);transform:translateY(-1px);}

/* Actions */
.sg-actions{display:flex;justify-content:flex-end;gap:.6rem;margin-top:.5rem;animation:sg-up .26s .1s ease both;}
.sg-btn-primary{display:inline-flex;align-items:center;gap:.5rem;padding:.5rem 1.1rem;background:var(--sg-accent);border:1px solid var(--sg-accent);border-radius:var(--sg-radius-sm);color:#fff;font-family:var(--sg-font-ui);font-size:.79rem;font-weight:600;cursor:pointer;box-shadow:0 3px 10px var(--sg-accent-glow);transition:all var(--sg-t);}
.sg-btn-primary:hover:not(:disabled){background:var(--sg-accent-mid);border-color:var(--sg-accent-mid);transform:translateY(-1px);box-shadow:0 6px 16px var(--sg-accent-glow);}
.sg-btn-primary:disabled{opacity:.42;cursor:not-allowed;transform:none;}
.sg-btn-secondary{display:inline-flex;align-items:center;gap:.5rem;padding:.5rem 1rem;background:transparent;border:1px solid var(--sg-border-2);border-radius:var(--sg-radius-sm);color:var(--sg-text-3);font-family:var(--sg-font-ui);font-size:.77rem;font-weight:500;cursor:pointer;transition:all var(--sg-t);}
.sg-btn-secondary:hover{background:var(--sg-surface-2);color:var(--sg-text);border-color:var(--sg-border-3);}
</style>
@endpush

@section('content')
<div class="sg">

    {{-- Header --}}
    <div class="sg-hdr">
        <div class="sg-hdr-icon"><i class="fas fa-layer-group"></i></div>
        <div>
            <div class="sg-eyebrow">Configuration · Année scolaire</div>
            <h1 class="sg-title">Gestion des Promotions</h1>
            <p class="sg-subtitle">Associez les promotions (2nde, 1ère, Tle) à chaque filière</p>
        </div>
        <div class="sg-stat-pill" id="headerStats" style="display:none;">
            <div class="sg-stat">
                <span class="sg-stat-val" id="statChecked">0</span>
                <span class="sg-stat-key">Sélectionnées</span>
            </div>
            <div class="sg-stat-pill-div"></div>
            <div class="sg-stat">
                <span class="sg-stat-val" id="statLocked">0</span>
                <span class="sg-stat-key">Verrouillées</span>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="sg-alert sg-alert-success"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span><button class="sg-alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button></div>
    @endif
    @if(session('error'))
    <div class="sg-alert sg-alert-danger"><i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span><button class="sg-alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button></div>
    @endif
    @if($errors->any())
    <div class="sg-alert sg-alert-danger"><i class="fas fa-exclamation-circle"></i><span>{{ $errors->first() }}</span><button class="sg-alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button></div>
    @endif

    <form action="{{ route('promotionbysector.store') }}" method="POST" id="promoForm">
        @csrf

        {{-- Étape 1 --}}
        <div class="sg-card">
            <div class="sg-card-hdr">
                <div class="sg-step">1</div>
                <div>
                    <div class="sg-card-title">Année scolaire</div>
                    <div class="sg-card-desc">Sélectionnez l'année pour charger les filières</div>
                </div>
            </div>
            <div class="sg-card-body">
                <div class="sg-field">
                    <label class="sg-label" for="year"><i class="fas fa-calendar-alt"></i> Année scolaire</label>
                    <div class="sg-sel-wrap">
                        <select name="year" id="year" class="sg-sel" required>
                            <option value="">— Choisissez une année —</option>
                            @foreach($years as $year)
                                <option value="{{ $year->id }}">{{ $year->year }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down sg-sel-arrow"></i>
                        <span class="sg-sel-loader" id="yearLoader"><i class="fas fa-spinner fa-spin"></i></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Étape 2 --}}
        <div class="sg-card" id="sectorsCard" style="display:none;">
            <div class="sg-card-hdr">
                <div class="sg-step">2</div>
                <div style="flex:1;">
                    <div class="sg-card-title">Promotions par filière</div>
                    <div class="sg-card-desc">Cochez les promotions à activer pour chaque filière</div>
                </div>
                <div class="sg-legend">
                    <span class="sg-legend-item legend-new"><i class="fas fa-circle"></i> Nouvelle</span>
                    <span class="sg-legend-item legend-existing"><i class="fas fa-circle"></i> Existante</span>
                    <span class="sg-legend-item legend-locked"><i class="fas fa-lock"></i> Verrouillée</span>
                </div>
            </div>
            <div class="sg-card-body">
                <div class="sg-bulk">
                    <button type="button" class="sg-bulk-btn" id="selectAll"><i class="fas fa-check-square"></i> Tout sélectionner</button>
                    <button type="button" class="sg-bulk-btn sg-bulk-danger" id="unselectAll"><i class="fas fa-square"></i> Tout décocher</button>
                </div>
                <div id="sectorsGrid"></div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="sg-actions" id="actionsBar" style="display:none;">
            <button type="button" class="sg-btn-secondary" onclick="resetForm()"><i class="fas fa-redo"></i> Réinitialiser</button>
            <button type="submit" class="sg-btn-primary" id="submitBtn" disabled>
                <i class="fas fa-save"></i>
                <span id="submitLabel">Enregistrer les promotions</span>
                <span id="submitLoader" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span>
            </button>
        </div>
    </form>
</div>
@endsection

@section('another_JS')
<script>
(function(){
    const yearSel=document.getElementById('year'),yearLoader=document.getElementById('yearLoader'),
          sectorsCard=document.getElementById('sectorsCard'),sectorsGrid=document.getElementById('sectorsGrid'),
          actionsBar=document.getElementById('actionsBar'),headerStats=document.getElementById('headerStats'),
          submitBtn=document.getElementById('submitBtn'),form=document.getElementById('promoForm');

    function slugify(t){return t.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g,"").replace(/\s+/g,'-').replace(/[^a-z0-9-]/g,'');}

    function updateStats(){
        const checked=document.querySelectorAll('.promo-cb:checked'),locked=document.querySelectorAll('.sg-check-item.is-locked');
        document.getElementById('statChecked').textContent=checked.length;
        document.getElementById('statLocked').textContent=locked.length;
        submitBtn.disabled=checked.length===0;
    }

    yearSel.addEventListener('change',()=>{
        const id=yearSel.value; if(!id) return;
        sectorsGrid.innerHTML='<div style="text-align:center;padding:1.5rem;color:var(--sg-text-3);font-size:.8rem;"><i class="fas fa-spinner fa-spin" style="color:var(--sg-accent);margin-right:.4rem;"></i>Chargement…</div>';
        sectorsCard.style.display='block'; actionsBar.style.display='none';
        yearLoader.style.display='block'; document.querySelector('.sg-sel-arrow').style.display='none';
        fetch(`/promotion-sectors/${id}`).then(r=>r.json()).then(({sectors,registeredPromotions})=>{
            sectorsGrid.innerHTML='';
            if(!sectors.length){sectorsGrid.innerHTML='<p style="text-align:center;color:var(--sg-text-3);font-size:.8rem;padding:.5rem;">Aucune filière pour cette année.</p>';return;}
            const regMap={};
            registeredPromotions.forEach(p=>{regMap[p.label]=p;});
            sectors.forEach((sector,idx)=>{
                const row=document.createElement('div');
                row.className='sg-sector-row'; row.style.animationDelay=`${idx*.05}s`;
                const nameEl=document.createElement('span'); nameEl.className='sg-sector-name'; nameEl.textContent=sector.name_sector; row.appendChild(nameEl);
                const boxes=document.createElement('div'); boxes.className='sg-checkboxes';
                ['2nde','1ère','Tle'].forEach(level=>{
                    const value=`${level} ${sector.name_sector}`,inputId=slugify(value+'-'+sector.id),regInfo=regMap[value],isExisting=!!regInfo,isLocked=regInfo?.locked===true;
                    const item=document.createElement('div'); item.className='sg-check-item';
                    if(isLocked) item.classList.add('is-locked');
                    if(isExisting&&!isLocked) item.classList.add('is-existing');
                    const input=document.createElement('input'); input.type='checkbox'; input.className='promo-cb'; input.name=`sector_year_ids[${sector.id}][]`; input.id=inputId; input.value=value; input.checked=isExisting; if(isLocked) input.disabled=true;
                    const label=document.createElement('label'); label.htmlFor=inputId; label.className='sg-check-label';
                    const icon=document.createElement('i'); icon.className=isLocked?'fas fa-lock lock-icon':'fas fa-check ci'; label.appendChild(icon); label.appendChild(document.createTextNode(` ${level}`));
                    if(isLocked) label.title=regInfo.reason==='notes'?'Verrouillée : contient des notes':'Verrouillée : des classes lui sont associées';
                    item.appendChild(input); item.appendChild(label); boxes.appendChild(item);
                });
                row.appendChild(boxes); sectorsGrid.appendChild(row);
            });
            actionsBar.style.display='flex'; headerStats.style.display='flex'; updateStats();
        }).finally(()=>{yearLoader.style.display='none';document.querySelector('.sg-sel-arrow').style.display='';});
    });

    document.getElementById('selectAll').addEventListener('click',()=>{document.querySelectorAll('.promo-cb:not(:disabled)').forEach(c=>c.checked=true);updateStats();});
    document.getElementById('unselectAll').addEventListener('click',()=>{document.querySelectorAll('.promo-cb:not(:disabled)').forEach(c=>c.checked=false);updateStats();});
    document.addEventListener('change',e=>{if(e.target.classList.contains('promo-cb'))updateStats();});
    form.addEventListener('submit',()=>{document.getElementById('submitLabel').style.display='none';document.getElementById('submitLoader').style.display='inline';});
    window.resetForm=function(){yearSel.value='';sectorsCard.style.display='none';actionsBar.style.display='none';headerStats.style.display='none';sectorsGrid.innerHTML='';};
})();
</script>
@endsection
