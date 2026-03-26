@extends('layouts.template')

@section('breadcrumb', 'Nouvel utilisateur')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,600;0,700&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=JetBrains+Mono:wght@400;500;600&display=swap');
:root{--sg-bg:#f6f7f3;--sg-surface:#fff;--sg-surface-2:#f1f3ee;--sg-surface-3:#e8ebe3;--sg-accent:#3a6b35;--sg-accent-mid:#5a9a52;--sg-accent-light:#eef4ec;--sg-accent-bg:rgba(58,107,53,.07);--sg-accent-bg2:rgba(58,107,53,.13);--sg-accent-glow:rgba(58,107,53,.18);--sg-text:#1c2318;--sg-text-2:#4a5544;--sg-text-3:#8a9880;--sg-border:#e2e5da;--sg-border-2:#cbd2c2;--sg-success:#2e7d4f;--sg-success-bg:rgba(46,125,79,.08);--sg-danger:#a84040;--sg-danger-bg:rgba(168,64,64,.07);--sg-warn:#9a6a1a;--sg-warn-bg:rgba(154,106,26,.08);--sg-font-display:'Lora',Georgia,serif;--sg-font-ui:'DM Sans',system-ui,sans-serif;--sg-font-mono:'JetBrains Mono','Fira Mono',monospace;--sg-radius:10px;--sg-radius-sm:7px;--sg-t:.17s cubic-bezier(.4,0,.2,1);}
html[data-theme=dark]{--sg-surface:#1e2419;--sg-surface-2:#252c20;--sg-surface-3:#2d3527;--sg-accent:#6abf60;--sg-accent-mid:#85d47a;--sg-accent-light:rgba(106,191,96,.12);--sg-accent-bg:rgba(106,191,96,.09);--sg-accent-bg2:rgba(106,191,96,.16);--sg-accent-glow:rgba(106,191,96,.20);--sg-text:#e4ead8;--sg-text-2:#adb9a0;--sg-text-3:#627057;--sg-border:rgba(255,255,255,.08);--sg-border-2:rgba(255,255,255,.13);}
.sg,.sg *{font-family:var(--sg-font-ui);box-sizing:border-box;}
.sg{max-width:800px;margin:0 auto;padding:0 0 4rem;color:var(--sg-text);}

/* Breadcrumb */
.sg-bread{display:flex;align-items:center;gap:.42rem;font-size:.7rem;color:var(--sg-text-3);margin-bottom:1.5rem;}
.sg-bread a{color:var(--sg-text-3);text-decoration:none;transition:color var(--sg-t);}
.sg-bread a:hover{color:var(--sg-accent);}
.sg-bread span{color:var(--sg-text-2);}
.sg-bread i{font-size:.52rem;}

/* Header */
.sg-hdr{margin-bottom:1.75rem;}
.sg-eyebrow{font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.14em;color:var(--sg-accent);margin-bottom:.15rem;}
.sg-title{font-family:var(--sg-font-display);font-size:1.28rem;font-weight:700;color:var(--sg-text);letter-spacing:-.02em;margin:0;}
.sg-subtitle{font-size:.75rem;color:var(--sg-text-3);margin:.1rem 0 0;}

/* Flash */
.sg-flash{display:flex;align-items:center;gap:.68rem;padding:.8rem 1rem;border-radius:var(--sg-radius-sm);font-size:.79rem;font-weight:500;border:1px solid;margin-bottom:1.1rem;}
.sg-flash-danger{background:var(--sg-danger-bg);border-color:rgba(168,64,64,.2);color:var(--sg-danger);}

/* Preview bar */
.sg-preview{display:flex;align-items:center;gap:1.05rem;background:var(--sg-surface);border:1px solid var(--sg-border);border-radius:var(--sg-radius);padding:1.05rem 1.2rem;margin-bottom:1.5rem;box-shadow:0 1px 2px rgba(28,35,24,.04);animation:sg-up .28s ease both;}
@keyframes sg-up{from{opacity:0;transform:translateY(9px)}to{opacity:1;transform:translateY(0)}}
.sg-pv-avatar{width:48px;height:48px;border-radius:12px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-family:var(--sg-font-mono);font-size:.88rem;font-weight:700;background:var(--sg-surface-2);color:var(--sg-text-3);border:1px solid var(--sg-border-2);transition:all .28s;}
.sg-pv-name{font-size:.9rem;font-weight:600;color:var(--sg-text);transition:color .2s;}
.sg-pv-role{font-size:.7rem;color:var(--sg-text-3);margin-top:2px;}
.sg-pv-pwd{font-family:var(--sg-font-mono);font-size:.72rem;color:var(--sg-warn);background:var(--sg-warn-bg);border:1px solid rgba(154,106,26,.18);border-radius:6px;padding:.26rem .6rem;white-space:nowrap;display:flex;align-items:center;gap:.36rem;margin-left:auto;}
.sg-pv-pwd i{font-size:.62rem;}

/* Card */
.sg-card{background:var(--sg-surface);border:1px solid var(--sg-border);border-radius:var(--sg-radius);box-shadow:0 1px 2px rgba(28,35,24,.04);overflow:hidden;animation:sg-up .28s ease both;}
.sg-card-section{padding:1.3rem 1.4rem;border-bottom:1px solid var(--sg-border);}
.sg-card-section:last-child{border-bottom:none;}
.sg-section-title{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--sg-text-3);display:flex;align-items:center;gap:.5rem;margin-bottom:1.1rem;}
.sg-section-title::after{content:'';flex:1;height:1px;background:var(--sg-border);}

/* Grid */
.sg-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
@media(max-width:600px){.sg-grid-2{grid-template-columns:1fr;}}

/* Fields */
.sg-field{display:flex;flex-direction:column;gap:.36rem;}
.sg-label{font-size:.68rem;font-weight:700;color:var(--sg-text-3);text-transform:uppercase;letter-spacing:.1em;display:flex;align-items:center;gap:.35rem;}
.sg-req{color:var(--sg-danger);font-size:.8em;}
.sg-label-note{font-size:.65rem;color:var(--sg-warn);font-weight:500;text-transform:none;letter-spacing:0;}
.sg-input-wrap{position:relative;}
.sg-iicon{position:absolute;left:.78rem;top:50%;transform:translateY(-50%);color:var(--sg-text-3);pointer-events:none;transition:color var(--sg-t);display:flex;align-items:center;}
.sg-iicon svg{width:14px;height:14px;}
.sg-input{font-family:var(--sg-font-ui);font-size:.81rem;width:100%;height:43px;padding:0 2.4rem 0 2.5rem;background:var(--sg-surface-2);border:1px solid var(--sg-border-2);border-radius:var(--sg-radius-sm);color:var(--sg-text);transition:border-color var(--sg-t),box-shadow var(--sg-t);}
.sg-input::placeholder{color:var(--sg-text-3);font-size:.78rem;}
.sg-input:focus{outline:none;border-color:var(--sg-accent);box-shadow:0 0 0 3px var(--sg-accent-bg);}
.sg-input-wrap:focus-within .sg-iicon{color:var(--sg-accent);}
.sg-istatus{position:absolute;right:.78rem;top:50%;transform:translateY(-50%);transition:opacity var(--sg-t);}
.sg-istatus--valid{color:var(--sg-success);}
.sg-istatus--invalid{color:var(--sg-danger);}
.sg-field.is-valid .sg-input{border-color:var(--sg-success);}
.sg-field.is-invalid .sg-input{border-color:var(--sg-danger);box-shadow:0 0 0 3px var(--sg-danger-bg);}
.sg-field-error{font-size:.7rem;color:var(--sg-danger);font-weight:500;display:flex;align-items:center;gap:.3rem;margin-top:.1rem;}
.sg-field-error-blade{color:var(--sg-danger);font-size:.7rem;font-weight:500;display:flex;align-items:center;gap:.3rem;margin-top:.22rem;}
.sg-err{font-size:.7rem;color:var(--sg-danger);font-weight:500;height:0;overflow:hidden;opacity:0;transition:height .2s,opacity .2s,margin .2s;}
.sg-err.visible{height:1.1rem;opacity:1;}

/* Role grid */
.sg-role-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:.62rem;}
.sg-role-card{display:flex;flex-direction:column;align-items:center;gap:.42rem;padding:.85rem .65rem;border-radius:var(--sg-radius-sm);border:1px solid var(--sg-border);background:var(--sg-surface-2);cursor:pointer;transition:all var(--sg-t);user-select:none;}
.sg-role-card:hover{border-color:var(--sg-border-2);background:var(--sg-surface-3);transform:translateY(-2px);}
.sg-role-card.selected{border-color:rgba(58,107,53,.35);background:var(--sg-accent-bg2);box-shadow:0 0 0 1px rgba(58,107,53,.25),0 4px 14px var(--sg-accent-glow);}
.sg-role-icon{width:34px;height:34px;border-radius:9px;font-size:.95rem;display:flex;align-items:center;justify-content:center;background:var(--sg-surface);border:1px solid var(--sg-border);transition:all var(--sg-t);}
.sg-role-card.selected .sg-role-icon{background:var(--sg-accent-light);border-color:rgba(58,107,53,.2);}
.sg-role-name{font-size:.74rem;font-weight:600;text-align:center;color:var(--sg-text-2);transition:color var(--sg-t);}
.sg-role-card.selected .sg-role-name{color:var(--sg-accent);}

/* Actions */
.sg-actions{padding:1.1rem 1.4rem;display:flex;align-items:center;justify-content:flex-end;gap:.65rem;border-top:1px solid var(--sg-border);}
.sg-btn{display:inline-flex;align-items:center;gap:.48rem;height:42px;padding:0 1.2rem;border-radius:var(--sg-radius-sm);font-family:var(--sg-font-ui);font-size:.79rem;font-weight:600;cursor:pointer;border:1px solid;text-decoration:none;transition:all var(--sg-t);}
.sg-btn-primary{background:var(--sg-accent);border-color:var(--sg-accent);color:#fff;box-shadow:0 3px 10px var(--sg-accent-glow);}
.sg-btn-primary:hover:not(:disabled){background:var(--sg-accent-mid);border-color:var(--sg-accent-mid);transform:translateY(-1px);box-shadow:0 6px 16px var(--sg-accent-glow);}
.sg-btn-primary:disabled{opacity:.4;cursor:not-allowed;transform:none;}
.sg-btn-ghost{background:var(--sg-surface-2);border-color:var(--sg-border-2);color:var(--sg-text-2);}
.sg-btn-ghost:hover{background:var(--sg-surface-3);color:var(--sg-text);}
@keyframes sg-spin{to{transform:rotate(360deg)}}
.sg-spinner{width:14px;height:14px;border-radius:50%;border:2px solid rgba(255,255,255,.25);border-top-color:#fff;animation:sg-spin .7s linear infinite;display:none;}
.sg-btn-primary.loading .sg-spinner{display:inline-block;}
.sg-btn-primary.loading .sg-btn-txt{display:none;}
</style>
@endpush

@section('content')
<div class="sg">

    <nav class="sg-bread">
        <a href="{{ route('user.index') }}">Utilisateurs</a>
        <i class="fas fa-chevron-right"></i>
        <span>Nouveau</span>
    </nav>

    <div class="sg-hdr">
        <div class="sg-eyebrow">Administration · Personnel</div>
        <h1 class="sg-title">Nouvel utilisateur</h1>
        <p class="sg-subtitle">Le mot de passe initial sera automatiquement défini sur le numéro de contact.</p>
    </div>

    @if(session('error'))
    <div class="sg-flash sg-flash-danger">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Preview temps réel --}}
    <div class="sg-preview">
        <div class="sg-pv-avatar" id="pvAvatar">?</div>
        <div>
            <div class="sg-pv-name" id="pvName">Nom Prénom</div>
            <div class="sg-pv-role" id="pvRole">Aucun rôle sélectionné</div>
        </div>
        <div class="sg-pv-pwd" id="pvPwd"><i class="fas fa-lock"></i><span id="pvPwdTxt">—</span></div>
    </div>

    <form action="{{ route('user.store') }}" method="POST" id="ucForm" novalidate>
        @csrf
        <div class="sg-card">

            {{-- Identité --}}
            <div class="sg-card-section">
                <div class="sg-section-title">Identité</div>
                <div class="sg-grid-2">
                    <div class="sg-field @error('name') is-invalid @enderror" id="field-name">
                        <label class="sg-label" for="name">Nom <span class="sg-req">*</span></label>
                        <div class="sg-input-wrap">
                            <span class="sg-iicon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></span>
                            <input type="text" name="name" id="name" class="sg-input" value="{{ old('name') }}" placeholder="Ex : Dupont" autocomplete="family-name">
                            <span class="sg-istatus" id="status-name"></span>
                        </div>
                        @error('name')<div class="sg-field-error-blade"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>@enderror
                        <div class="sg-err" id="err-name"></div>
                    </div>
                    <div class="sg-field @error('surname') is-invalid @enderror" id="field-surname">
                        <label class="sg-label" for="surname">Prénoms <span class="sg-req">*</span></label>
                        <div class="sg-input-wrap">
                            <span class="sg-iicon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></span>
                            <input type="text" name="surname" id="surname" class="sg-input" value="{{ old('surname') }}" placeholder="Ex : Jean-Marie" autocomplete="given-name">
                            <span class="sg-istatus" id="status-surname"></span>
                        </div>
                        @error('surname')<div class="sg-field-error-blade"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>@enderror
                        <div class="sg-err" id="err-surname"></div>
                    </div>
                </div>
            </div>

            {{-- Coordonnées --}}
            <div class="sg-card-section">
                <div class="sg-section-title">Coordonnées</div>
                <div class="sg-grid-2">
                    <div class="sg-field @error('email') is-invalid @enderror" id="field-email">
                        <label class="sg-label" for="email">Email <span class="sg-req">*</span></label>
                        <div class="sg-input-wrap">
                            <span class="sg-iicon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 7 10-7"/></svg></span>
                            <input type="email" name="email" id="email" class="sg-input" value="{{ old('email') }}" placeholder="exemple@ecole.bj" autocomplete="email">
                            <span class="sg-istatus" id="status-email"></span>
                        </div>
                        @error('email')<div class="sg-field-error-blade"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>@enderror
                        <div class="sg-err" id="err-email"></div>
                    </div>
                    <div class="sg-field @error('phone') is-invalid @enderror" id="field-phone">
                        <label class="sg-label" for="phone">Contact <span class="sg-req">*</span> <span class="sg-label-note">= mot de passe initial</span></label>
                        <div class="sg-input-wrap">
                            <span class="sg-iicon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.97-.97a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg></span>
                            <input type="text" name="phone" id="phone" class="sg-input" value="{{ old('phone') }}" placeholder="+229 01 XX XX XX XX" autocomplete="tel">
                            <span class="sg-istatus" id="status-phone"></span>
                        </div>
                        @error('phone')<div class="sg-field-error-blade"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>@enderror
                        <div class="sg-err" id="err-phone"></div>
                    </div>
                </div>
            </div>

            {{-- Rôle --}}
            <div class="sg-card-section">
                <div class="sg-section-title">Rôle & Fonction</div>
                <input type="hidden" name="role_id" id="role_id" value="{{ old('role_id') }}">
                <div class="sg-role-grid" id="role-grid">
                    @php
                        $icons = ['censeur'=>'🏛️','directeur'=>'👑','professeur'=>'📚','surveillant'=>'👁️','secretaire'=>'📋','comptable'=>'📊','default'=>'👤'];
                    @endphp
                    @foreach($roles as $role)
                    @php $icon = $icons[strtolower(trim($role->name))] ?? $icons['default']; @endphp
                    <div class="sg-role-card {{ old('role_id') == $role->id ? 'selected' : '' }}" data-id="{{ $role->id }}" data-name="{{ $role->name }}" onclick="ucSelectRole(this)" tabindex="0" role="button" onkeydown="if(event.key==='Enter'||event.key===' ')ucSelectRole(this)">
                        <div class="sg-role-icon">{{ $icon }}</div>
                        <div class="sg-role-name">{{ $role->name }}</div>
                    </div>
                    @endforeach
                </div>
                @error('role_id')<div class="sg-field-error-blade" style="margin-top:.65rem"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>@enderror
                <div class="sg-err" id="err-role" style="margin-top:.45rem"></div>
            </div>

            {{-- Actions --}}
            <div class="sg-actions">
                <a href="{{ route('user.index') }}" class="sg-btn sg-btn-ghost">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                    Annuler
                </a>
                <button type="submit" class="sg-btn sg-btn-primary" id="ucSubmit">
                    <div class="sg-spinner"></div>
                    <span class="sg-btn-txt">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                        Enregistrer
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('another_JS')
<script>
const PALETTE=['#3a6b35','#5a9a52','#2e7d4f','#9a6a1a','#2a6090','#5d4a8a'];
let palIdx=0;

function updatePreview(){
    const name=(document.getElementById('name')?.value??'').trim(),surname=(document.getElementById('surname')?.value??'').trim(),phone=(document.getElementById('phone')?.value??'').trim();
    const init=((name[0]??'')+(surname[0]??'')).toUpperCase()||'?',full=[name,surname].filter(Boolean).join(' ')||'Nom Prénom',color=PALETTE[palIdx%PALETTE.length];
    const av=document.getElementById('pvAvatar');
    av.textContent=init; av.style.background=`color-mix(in srgb,${color} 14%,var(--sg-surface-2))`; av.style.color=color; av.style.borderColor=`color-mix(in srgb,${color} 28%,transparent)`;
    document.getElementById('pvName').textContent=full;
    document.getElementById('pvName').style.color=full==='Nom Prénom'?'var(--sg-text-3)':'var(--sg-text)';
    document.getElementById('pvPwdTxt').textContent=phone||'—';
}
['name','surname','phone'].forEach(id=>document.getElementById(id)?.addEventListener('input',updatePreview));

function ucSelectRole(card){
    document.querySelectorAll('.sg-role-card').forEach(c=>c.classList.remove('selected'));
    card.classList.add('selected'); document.getElementById('role_id').value=card.dataset.id;
    document.getElementById('pvRole').textContent=card.dataset.name;
    palIdx=parseInt(card.dataset.id)||0; updatePreview();
    const err=document.getElementById('err-role'); if(err){err.textContent='';err.classList.remove('visible');}
}

const validators={name:v=>v.trim().length>=2?null:'Au moins 2 caractères requis.',surname:v=>v.trim().length>=2?null:'Au moins 2 caractères requis.',email:v=>/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)?null:'Adresse email invalide.',phone:v=>v.trim().length>=6?null:'Numéro trop court.'};
const checkSvg='<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>';
const crossSvg='<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>';

function validateField(id){
    const input=document.getElementById(id),field=document.getElementById(`field-${id}`),status=document.getElementById(`status-${id}`),err=document.getElementById(`err-${id}`);
    if(!input||!field) return true;
    const val=input.value,msg=validators[id]?validators[id](val):null;
    field.classList.toggle('is-valid',!msg&&val.length>0);
    field.classList.toggle('is-invalid',!!msg&&val.length>0);
    if(status){status.innerHTML=val.length===0?'':(msg?crossSvg:checkSvg);status.className=`sg-istatus sg-istatus--${msg?'invalid':'valid'}`;}
    if(err){err.textContent=msg??'';err.classList.toggle('visible',!!msg&&val.length>0);}
    return !msg;
}
Object.keys(validators).forEach(id=>{const el=document.getElementById(id);el?.addEventListener('input',()=>validateField(id));el?.addEventListener('blur',()=>validateField(id));});

document.getElementById('ucForm')?.addEventListener('submit',function(e){
    const ok=Object.keys(validators).map(id=>validateField(id)).every(Boolean),roleOk=!!document.getElementById('role_id').value;
    if(!roleOk){const err=document.getElementById('err-role');err.textContent='Veuillez sélectionner un rôle.';err.classList.add('visible');}
    if(!ok||!roleOk){e.preventDefault();const first=document.querySelector('.sg-field.is-invalid,.sg-err.visible');first?.scrollIntoView({behavior:'smooth',block:'center'});return;}
    const btn=document.getElementById('ucSubmit'); btn.classList.add('loading'); btn.disabled=true;
});

document.addEventListener('DOMContentLoaded',()=>{
    updatePreview();
    const sel=document.querySelector('.sg-role-card.selected'); if(sel)document.getElementById('pvRole').textContent=sel.dataset.name;
    Object.keys(validators).forEach(id=>{const el=document.getElementById(id);if(el?.value)validateField(id);});
});
</script>
@endsection
