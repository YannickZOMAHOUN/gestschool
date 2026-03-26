@extends('layouts.template')

@section('breadcrumb', 'Utilisateurs')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,600;0,700&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=JetBrains+Mono:wght@400;500;600&display=swap');
:root{--sg-bg:#f6f7f3;--sg-surface:#fff;--sg-surface-2:#f1f3ee;--sg-surface-3:#e8ebe3;--sg-accent:#3a6b35;--sg-accent-mid:#5a9a52;--sg-accent-light:#eef4ec;--sg-accent-bg:rgba(58,107,53,.07);--sg-accent-bg2:rgba(58,107,53,.13);--sg-accent-glow:rgba(58,107,53,.18);--sg-text:#1c2318;--sg-text-2:#4a5544;--sg-text-3:#8a9880;--sg-border:#e2e5da;--sg-border-2:#cbd2c2;--sg-success:#2e7d4f;--sg-success-bg:rgba(46,125,79,.08);--sg-danger:#a84040;--sg-danger-bg:rgba(168,64,64,.07);--sg-warn:#9a6a1a;--sg-warn-bg:rgba(154,106,26,.08);--sg-info:#2a6090;--sg-info-bg:rgba(42,96,144,.08);--sg-font-display:'Lora',Georgia,serif;--sg-font-ui:'DM Sans',system-ui,sans-serif;--sg-font-mono:'JetBrains Mono','Fira Mono',monospace;--sg-radius:10px;--sg-radius-sm:7px;--sg-t:.17s cubic-bezier(.4,0,.2,1);}
html[data-theme=dark]{--sg-surface:#1e2419;--sg-surface-2:#252c20;--sg-surface-3:#2d3527;--sg-accent:#6abf60;--sg-accent-mid:#85d47a;--sg-accent-light:rgba(106,191,96,.12);--sg-accent-bg:rgba(106,191,96,.09);--sg-accent-bg2:rgba(106,191,96,.16);--sg-accent-glow:rgba(106,191,96,.20);--sg-text:#e4ead8;--sg-text-2:#adb9a0;--sg-text-3:#627057;--sg-border:rgba(255,255,255,.08);--sg-border-2:rgba(255,255,255,.13);}
.sg,.sg *{font-family:var(--sg-font-ui);box-sizing:border-box;}
.sg{max-width:1100px;margin:0 auto;padding:0 0 4rem;color:var(--sg-text);}

/* Header */
.sg-hdr{display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--sg-border);flex-wrap:wrap;animation:sg-up .38s ease both;}
@keyframes sg-up{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
.sg-eyebrow{font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.14em;color:var(--sg-accent);margin-bottom:.15rem;}
.sg-title{font-family:var(--sg-font-display);font-size:1.28rem;font-weight:700;color:var(--sg-text);letter-spacing:-.02em;margin:0;}
.sg-btn-create{display:inline-flex;align-items:center;gap:.48rem;background:var(--sg-accent);border:none;color:#fff;padding:.52rem 1.15rem;border-radius:var(--sg-radius-sm);font-family:var(--sg-font-ui);font-size:.79rem;font-weight:600;cursor:pointer;text-decoration:none;letter-spacing:-.01em;box-shadow:0 3px 10px var(--sg-accent-glow);transition:all var(--sg-t);}
.sg-btn-create:hover{background:var(--sg-accent-mid);transform:translateY(-1px);box-shadow:0 6px 18px var(--sg-accent-glow);color:#fff;}

/* Flash */
.sg-flash{display:flex;align-items:center;gap:.68rem;padding:.75rem .95rem;border-radius:var(--sg-radius-sm);font-size:.79rem;font-weight:500;border:1px solid;margin-bottom:1rem;animation:sg-up .3s ease both;}
.sg-flash span{flex:1;}
.sg-flash button{background:none;border:none;cursor:pointer;color:inherit;opacity:.55;}
.sg-flash button:hover{opacity:1;}
.sg-flash-success{background:var(--sg-success-bg);border-color:rgba(46,125,79,.2);color:var(--sg-success);}
.sg-flash-danger{background:var(--sg-danger-bg);border-color:rgba(168,64,64,.2);color:var(--sg-danger);}

/* Stats */
.sg-stats{display:flex;background:var(--sg-surface);border:1px solid var(--sg-border);border-radius:var(--sg-radius);overflow:hidden;margin-bottom:1.5rem;animation:sg-up .38s .1s ease both;}
.sg-stat{display:flex;flex-direction:column;align-items:center;padding:.9rem 1.75rem;flex:1;text-align:center;border-right:1px solid var(--sg-border);}
.sg-stat:last-child{border-right:none;}
.sg-stat-val{font-family:var(--sg-font-display);font-size:1.4rem;font-weight:700;color:var(--sg-text);line-height:1;}
.sg-stat-val--accent{color:var(--sg-accent);}
.sg-stat-key{font-size:.59rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--sg-text-3);margin-top:.28rem;}
@media(max-width:640px){.sg-stats{flex-direction:column;}.sg-stat{border-right:none;border-bottom:1px solid var(--sg-border);}.sg-stat:last-child{border-bottom:none;}}

/* Card */
.sg-card{background:var(--sg-surface);border:1px solid var(--sg-border);border-radius:var(--sg-radius);box-shadow:0 1px 2px rgba(28,35,24,.04);overflow:hidden;animation:sg-up .4s .2s ease both;}

/* Toolbar */
.sg-toolbar{display:flex;align-items:center;gap:.85rem;padding:.85rem 1.1rem;border-bottom:1px solid var(--sg-border);flex-wrap:wrap;}
.sg-search-wrap{position:relative;flex:1;min-width:210px;max-width:360px;}
.sg-search-icon{position:absolute;left:.78rem;top:50%;transform:translateY(-50%);color:var(--sg-text-3);font-size:.78rem;pointer-events:none;}
.sg-search{font-family:var(--sg-font-ui);font-size:.79rem;width:100%;height:38px;padding:0 1rem 0 2.35rem;background:var(--sg-surface-2);border:1px solid var(--sg-border);border-radius:var(--sg-radius-sm);color:var(--sg-text);transition:border-color var(--sg-t),box-shadow var(--sg-t);}
.sg-search::placeholder{color:var(--sg-text-3);}
.sg-search:focus{outline:none;border-color:var(--sg-accent);box-shadow:0 0 0 3px var(--sg-accent-bg);}
.sg-count-pill{font-size:.72rem;font-family:var(--sg-font-mono);color:var(--sg-text-3);background:var(--sg-surface-2);border:1px solid var(--sg-border);padding:.26rem .68rem;border-radius:5px;margin-left:auto;white-space:nowrap;}

/* Table */
.sg-tbl-wrap{overflow-x:auto;}
.sg-table{width:100%;border-collapse:collapse;font-size:.79rem;}
.sg-th{padding:.8rem .95rem;text-align:left;font-size:.59rem;font-weight:700;text-transform:uppercase;letter-spacing:.11em;color:var(--sg-text-3);border-bottom:1px solid var(--sg-border);white-space:nowrap;cursor:pointer;user-select:none;}
.sg-th:hover{color:var(--sg-text-2);}
.sg-th.no-sort{cursor:default;}
.sg-tr{border-bottom:1px solid var(--sg-border);animation:sg-row-in .35s ease both;transition:background var(--sg-t);}
.sg-tr:last-child{border-bottom:none;}
.sg-tr:hover td{background:var(--sg-surface-2);}
@keyframes sg-row-in{from{opacity:0;transform:translateX(-10px)}to{opacity:1;transform:translateX(0)}}
.sg-td{padding:.82rem .95rem;color:var(--sg-text-2);vertical-align:middle;}

/* Avatar cell */
.sg-av-cell{display:flex;align-items:center;gap:.72rem;}
.sg-avatar{width:36px;height:36px;border-radius:9px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-family:var(--sg-font-mono);font-size:.68rem;font-weight:700;background:color-mix(in srgb,var(--av-color,var(--sg-accent)) 14%,var(--sg-surface-2));color:var(--av-color,var(--sg-accent));border:1px solid color-mix(in srgb,var(--av-color,var(--sg-accent)) 28%,transparent);}
.sg-user-name{font-size:.85rem;font-weight:600;color:var(--sg-text);}
.sg-user-id{font-family:var(--sg-font-mono);font-size:.67rem;color:var(--sg-text-3);margin-top:1px;}
.sg-email{color:var(--sg-text-2);text-decoration:none;font-size:.79rem;}
.sg-email:hover{color:var(--sg-accent);}
.sg-phone{font-family:var(--sg-font-mono);font-size:.76rem;color:var(--sg-text-2);}

/* Role badge */
.sg-role{display:inline-flex;align-items:center;gap:.3rem;font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;padding:.22rem .62rem;border-radius:6px;border:1px solid;}
.sg-role::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;}
.sg-role--admin,.sg-role--censeur,.sg-role--directeur{background:var(--sg-success-bg);color:var(--sg-success);border-color:rgba(46,125,79,.2);}
.sg-role--professeur,.sg-role--teacher{background:var(--sg-info-bg);color:var(--sg-info);border-color:rgba(42,96,144,.2);}
.sg-role--surveillant{background:var(--sg-warn-bg);color:var(--sg-warn);border-color:rgba(154,106,26,.2);}
.sg-role--secretaire,.sg-role--comptable{background:var(--sg-accent-bg2);color:var(--sg-accent);border-color:rgba(58,107,53,.2);}
.sg-role--default{background:var(--sg-surface-2);color:var(--sg-text-3);border-color:var(--sg-border-2);}

/* Actions */
.sg-td-actions{text-align:right;}
.sg-row-actions{display:flex;align-items:center;justify-content:flex-end;gap:.45rem;}
.sg-btn-action{display:inline-flex;align-items:center;gap:.32rem;font-family:var(--sg-font-ui);font-size:.73rem;font-weight:600;padding:.32rem .7rem;border-radius:6px;cursor:pointer;border:1px solid;text-decoration:none;transition:all var(--sg-t);white-space:nowrap;}
.sg-btn-action:active{transform:scale(.97);}
.sg-btn-edit{background:var(--sg-info-bg);color:var(--sg-info);border-color:rgba(42,96,144,.2);}
.sg-btn-edit:hover{background:rgba(42,96,144,.14);box-shadow:0 0 10px rgba(42,96,144,.18);}
.sg-btn-del{background:var(--sg-danger-bg);color:var(--sg-danger);border-color:rgba(168,64,64,.2);}
.sg-btn-del:hover{background:rgba(168,64,64,.13);box-shadow:0 0 10px rgba(168,64,64,.18);}

/* Empty */
.sg-empty{text-align:center;padding:2.5rem 1rem;color:var(--sg-text-3);font-size:.8rem;}
.sg-empty-icon{font-size:1.7rem;margin-bottom:.5rem;opacity:.26;}

/* Modal */
.sg-overlay{position:fixed;inset:0;z-index:8000;background:rgba(28,35,24,.52);backdrop-filter:blur(3px);opacity:0;pointer-events:none;transition:opacity .2s;}
.sg-overlay.on{opacity:1;pointer-events:all;}
.sg-modal{position:fixed;top:50%;left:50%;transform:translate(-50%,-46%);z-index:8001;width:90%;max-width:380px;background:var(--sg-surface);border:1px solid var(--sg-border);border-radius:var(--sg-radius);padding:1.65rem;box-shadow:0 10px 36px rgba(28,35,24,.11);opacity:0;pointer-events:none;transition:opacity .2s,transform .2s;}
.sg-modal.on{opacity:1;pointer-events:all;transform:translate(-50%,-50%);}
.sg-modal-danger-ring{width:46px;height:46px;border-radius:11px;background:var(--sg-danger-bg);border:1px solid rgba(168,64,64,.2);display:flex;align-items:center;justify-content:center;color:var(--sg-danger);margin-bottom:1rem;}
.sg-modal-title{font-family:var(--sg-font-display);font-size:1.02rem;font-weight:600;color:var(--sg-text);margin-bottom:.38rem;}
.sg-modal-desc{font-size:.78rem;color:var(--sg-text-2);line-height:1.65;margin-bottom:1.4rem;}
.sg-modal-desc strong{color:var(--sg-text);}
.sg-modal-actions{display:flex;gap:.6rem;justify-content:flex-end;}
.sg-btn-cancel{font-family:var(--sg-font-ui);font-size:.79rem;font-weight:500;padding:.48rem 1rem;border-radius:var(--sg-radius-sm);cursor:pointer;background:var(--sg-surface-2);color:var(--sg-text-2);border:1px solid var(--sg-border-2);transition:all var(--sg-t);}
.sg-btn-cancel:hover{background:var(--sg-surface-3);color:var(--sg-text);}
.sg-btn-confirm{font-family:var(--sg-font-ui);font-size:.79rem;font-weight:600;padding:.48rem 1.15rem;border-radius:var(--sg-radius-sm);cursor:pointer;background:var(--sg-danger);color:#fff;border:none;transition:all var(--sg-t);}
.sg-btn-confirm:hover{background:#933838;box-shadow:0 4px 16px rgba(168,64,64,.35);}
.sg-btn-confirm:disabled{opacity:.55;cursor:not-allowed;}
</style>
@endpush

@section('content')
<div class="sg">

    <div class="sg-hdr">
        <div>
            <div class="sg-eyebrow">Administration · Personnel</div>
            <h1 class="sg-title">Utilisateurs</h1>
        </div>
        <a href="{{ route('user.create') }}" class="sg-btn-create">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Nouvel utilisateur
        </a>
    </div>

    @if(session('success'))
    <div class="sg-flash sg-flash-success">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
    </div>
    @endif
    @if(session('error'))
    <div class="sg-flash sg-flash-danger">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
        <span>{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
    </div>
    @endif

    {{-- Stats --}}
    @php
        $totalUsers  = $users->count();
        $rolesCount  = $users->map(fn($u)=>optional($u->roles->first())->name)->filter()->unique()->count();
        $thisMonth   = $users->filter(fn($u)=>$u->created_at&&$u->created_at->isCurrentMonth())->count();
    @endphp
    <div class="sg-stats">
        <div class="sg-stat"><span class="sg-stat-val">{{ $totalUsers }}</span><span class="sg-stat-key">Total</span></div>
        <div class="sg-stat"><span class="sg-stat-val sg-stat-val--accent">{{ $rolesCount }}</span><span class="sg-stat-key">Rôles</span></div>
        <div class="sg-stat"><span class="sg-stat-val">{{ $thisMonth }}</span><span class="sg-stat-key">Ce mois</span></div>
    </div>

    <div class="sg-card">
        <div class="sg-toolbar">
            <div class="sg-search-wrap">
                <i class="fas fa-search sg-search-icon"></i>
                <input type="text" id="sgSearch" class="sg-search" placeholder="Nom, email, rôle…" autocomplete="off">
            </div>
            <span class="sg-count-pill" id="sgCount">{{ $totalUsers }} entrée(s)</span>
        </div>

        <div class="sg-tbl-wrap">
            <table class="sg-table">
                <thead>
                    <tr>
                        <th class="sg-th">Utilisateur</th>
                        <th class="sg-th">Email</th>
                        <th class="sg-th">Contact</th>
                        <th class="sg-th">Rôle</th>
                        <th class="sg-th no-sort" style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="sgTbody">
                    @php $palette = ['#3a6b35','#5a9a52','#2a6090','#9a6a1a','#2e7d4f','#5d4a8a']; @endphp
                    @forelse($users as $i => $user)
                    @php
                        $role     = optional($user->roles->first())->name ?? 'N/A';
                        $roleSlug = strtolower(str_replace([' ','é','è','ê','â'],['-','e','e','e','a'],$role));
                        $initials = strtoupper(mb_substr($user->name??'?',0,1).mb_substr($user->surname??'',0,1));
                        $color    = $palette[$user->id % count($palette)];
                    @endphp
                    <tr class="sg-tr" style="animation-delay:{{ $i*30 }}ms"
                        data-name="{{ strtolower($user->name.' '.$user->surname) }}"
                        data-email="{{ strtolower($user->email) }}"
                        data-role="{{ strtolower($role) }}">
                        <td class="sg-td">
                            <div class="sg-av-cell">
                                <div class="sg-avatar" style="--av-color:{{ $color }}">{{ $initials }}</div>
                                <div>
                                    <div class="sg-user-name">{{ $user->name }} {{ $user->surname }}</div>
                                    <div class="sg-user-id">#{{ str_pad($user->id,5,'0',STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="sg-td"><a href="mailto:{{ $user->email }}" class="sg-email">{{ $user->email }}</a></td>
                        <td class="sg-td"><span class="sg-phone">{{ $user->phone ?? '—' }}</span></td>
                        <td class="sg-td"><span class="sg-role sg-role--{{ $roleSlug }}">{{ $role }}</span></td>
                        <td class="sg-td sg-td-actions">
                            <div class="sg-row-actions">
                                <a href="{{ route('user.edit', $user) }}" class="sg-btn-action sg-btn-edit">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
                                    <span>Modifier</span>
                                </a>
                                <button class="sg-btn-action sg-btn-del" onclick="sgConfirmDelete('{{ $user->id }}','{{ addslashes($user->name.' '.$user->surname) }}')">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                    <span>Supprimer</span>
                                </button>
                                <form id="del-{{ $user->id }}" action="{{ route('user.destroy', $user) }}" method="POST" hidden>@csrf @method('DELETE')</form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="sg-td"><div class="sg-empty"><div class="sg-empty-icon">👤</div>Aucun utilisateur enregistré</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Aucun résultat --}}
    <div class="sg-card" id="sgNoResults" style="display:none;">
        <div class="sg-empty"><div class="sg-empty-icon">🔍</div>Aucun résultat pour cette recherche</div>
    </div>
</div>

{{-- Modal --}}
<div class="sg-overlay" id="sgOverlay" onclick="sgClose()"></div>
<div class="sg-modal" id="sgModal" role="dialog" aria-modal="true">
    <div class="sg-modal-danger-ring">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
    </div>
    <div class="sg-modal-title">Confirmer la suppression</div>
    <p class="sg-modal-desc">Vous allez supprimer définitivement <strong id="sgModalName"></strong>. Cette opération est <em>irréversible</em>.</p>
    <div class="sg-modal-actions">
        <button class="sg-btn-cancel" onclick="sgClose()">Annuler</button>
        <button class="sg-btn-confirm" id="sgModalBtn">Supprimer</button>
    </div>
</div>
@endsection

@section('another_JS')
<script>
/* Recherche */
const searchEl=document.getElementById('sgSearch'),countEl=document.getElementById('sgCount'),noResults=document.getElementById('sgNoResults');
searchEl?.addEventListener('input',function(){
    const q=this.value.toLowerCase().trim(); let n=0;
    document.querySelectorAll('#sgTbody .sg-tr').forEach(r=>{const m=!q||r.dataset.name.includes(q)||r.dataset.email.includes(q)||r.dataset.role.includes(q);r.style.display=m?'':'none';if(m)n++;});
    countEl.textContent=`${n} entrée(s)`; noResults.style.display=(n===0&&q)?'block':'none';
});

/* Modal */
let _id=null;
function sgConfirmDelete(id,name){_id=id;document.getElementById('sgModalName').textContent=name;document.getElementById('sgOverlay').classList.add('on');document.getElementById('sgModal').classList.add('on');setTimeout(()=>document.getElementById('sgModalBtn').focus(),50);}
function sgClose(){_id=null;document.getElementById('sgOverlay').classList.remove('on');document.getElementById('sgModal').classList.remove('on');}
document.getElementById('sgModalBtn')?.addEventListener('click',()=>{
    if(!_id) return;
    const btn=document.getElementById('sgModalBtn'); btn.disabled=true; btn.textContent='Suppression…';
    document.getElementById(`del-${_id}`).submit();
});
document.addEventListener('keydown',e=>{if(e.key==='Escape')sgClose();});

/* Auto-dismiss flash */
document.querySelectorAll('.sg-flash').forEach(el=>{setTimeout(()=>{el.style.opacity='0';setTimeout(()=>el.remove(),300);},5000);});
</script>
@endsection
