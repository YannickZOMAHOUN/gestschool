@extends('layouts.template')

@section('another_CSS')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
@endsection

@section('content')

<style>
:root {
    --ink:       #0f1117;
    --ink-2:     #181c27;
    --ink-3:     #232736;
    --border:    rgba(255,255,255,.07);
    --border-2:  rgba(255,255,255,.12);
    --text:      #e8eaf0;
    --text-2:    #8b91a7;
    --text-3:    #555d78;
    --accent:    #00e5a0;
    --accent-d:  #00b87f;
    --accent-10: rgba(0,229,160,.10);
    --accent-20: rgba(0,229,160,.20);
    --danger:    #ff4d6a;
    --danger-10: rgba(255,77,106,.10);
    --warn:      #ffb347;
    --f-sans:    'Plus Jakarta Sans', system-ui, sans-serif;
    --f-mono:    'JetBrains Mono', 'Fira Mono', monospace;
    --radius:    10px;
    --radius-lg: 16px;
    --ease:      cubic-bezier(.4,0,.2,1);
}

*,*::before,*::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ── Root ── */
.un-root {
    font-family: var(--f-sans);
    background: var(--ink);
    min-height: 100vh;
    padding: 2rem 1.5rem;
    color: var(--text);
    display: flex; flex-direction: column; gap: 1.25rem;
    position: relative;
}
/* Grain texture overlay */
.un-root::before {
    content: '';
    position: fixed; inset: 0; pointer-events: none; z-index: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
    background-size: 200px;
}
.un-root > * { position: relative; z-index: 1; }

/* ── Page header ── */
.un-header {
    display: flex; align-items: flex-end; justify-content: space-between;
    flex-wrap: wrap; gap: 1rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--border);
    opacity: 0; animation: un-fade-up .5s var(--ease) .05s forwards;
}
.un-header-left {}
.un-eyebrow {
    font-size: .72rem; font-weight: 600; letter-spacing: .12em;
    text-transform: uppercase; color: var(--accent); margin-bottom: .35rem;
}
.un-page-title {
    font-size: 1.75rem; font-weight: 800; line-height: 1.1; color: var(--text);
    letter-spacing: -.02em;
}

.un-btn-create {
    display: inline-flex; align-items: center; gap: .5rem;
    background: var(--accent); color: var(--ink); border: none;
    font-family: var(--f-sans); font-size: .875rem; font-weight: 700;
    padding: .6rem 1.25rem; border-radius: var(--radius);
    cursor: pointer; text-decoration: none; letter-spacing: -.01em;
    transition: background .18s var(--ease), transform .18s var(--ease), box-shadow .18s var(--ease);
}
.un-btn-create:hover { background: var(--accent-d); transform: translateY(-2px); box-shadow: 0 6px 24px rgba(0,229,160,.3); }
.un-btn-create svg { flex-shrink: 0; }

/* ── Flash ── */
.un-flash {
    display: flex; align-items: center; gap: .75rem;
    padding: .875rem 1.25rem; border-radius: var(--radius);
    font-size: .875rem; font-weight: 500;
    border: 1px solid;
    opacity: 0; animation: un-fade-up .4s var(--ease) .1s forwards;
    transition: opacity .3s;
}
.un-flash--success { background: rgba(0,229,160,.08); border-color: rgba(0,229,160,.25); color: var(--accent); }
.un-flash--error   { background: var(--danger-10); border-color: rgba(255,77,106,.3); color: var(--danger); }
.un-flash { }
.un-flash button { margin-left: auto; background: none; border: none; cursor: pointer; color: inherit; opacity: .6; }
.un-flash button:hover { opacity: 1; }

/* ── Stats ── */
.un-stats {
    display: flex; align-items: center; gap: 0;
    background: var(--ink-2); border: 1px solid var(--border);
    border-radius: var(--radius-lg); overflow: hidden;
    opacity: 0; animation: un-fade-up .5s var(--ease) .15s forwards;
}
.un-stat {
    display: flex; flex-direction: column; align-items: center;
    padding: 1rem 2rem; flex: 1; text-align: center;
    border-right: 1px solid var(--border);
}
.un-stat:last-child { border-right: none; }
.un-stat-val { font-family: var(--f-mono); font-size: 1.5rem; font-weight: 600; color: var(--text); line-height: 1; }
.un-stat-val--accent { color: var(--accent); }
.un-stat-key { font-size: .72rem; font-weight: 500; text-transform: uppercase; letter-spacing: .08em; color: var(--text-3); margin-top: .35rem; }

/* ── Card ── */
.un-card {
    background: var(--ink-2); border: 1px solid var(--border);
    border-radius: var(--radius-lg); overflow: hidden;
    opacity: 0; animation: un-fade-up .5s var(--ease) .25s forwards;
}

/* ── Toolbar ── */
.un-toolbar {
    display: flex; align-items: center; gap: 1rem;
    padding: 1rem 1.25rem; border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
}
.un-search-wrap {
    position: relative; flex: 1; min-width: 220px; max-width: 380px;
}
.un-search-icon {
    position: absolute; left: .875rem; top: 50%; transform: translateY(-50%);
    color: var(--text-3); pointer-events: none;
}
.un-search-input {
    font-family: var(--f-sans); font-size: .875rem;
    width: 100%; height: 40px;
    padding: 0 1rem 0 2.5rem;
    background: var(--ink-3); border: 1px solid var(--border);
    border-radius: 8px; color: var(--text);
    transition: border-color .18s, box-shadow .18s;
}
.un-search-input::placeholder { color: var(--text-3); }
.un-search-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-10); }
.un-count {
    font-size: .78rem; font-family: var(--f-mono); color: var(--text-3);
    background: var(--ink-3); border: 1px solid var(--border);
    padding: .3rem .75rem; border-radius: 6px; margin-left: auto;
    white-space: nowrap;
}

/* ── Table ── */
.un-scroll { overflow-x: auto; }
.un-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
.un-th {
    padding: .875rem 1rem; text-align: left;
    font-size: .7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .1em; color: var(--text-3);
    border-bottom: 1px solid var(--border);
    background: transparent; white-space: nowrap;
    cursor: pointer; user-select: none;
}
.un-th:hover { color: var(--text-2); }
.un-th--actions { text-align: right; cursor: default; }
.un-th--user { min-width: 220px; }

/* Lignes avec animation séquencée */
@keyframes un-row-in {
    from { opacity: 0; transform: translateX(-12px); }
    to   { opacity: 1; transform: translateX(0); }
}
.un-tr {
    border-bottom: 1px solid var(--border);
    animation: un-row-in .4s var(--ease) both;
    transition: background .15s;
}
.un-tr:last-child { border-bottom: none; }
.un-tr:hover { background: rgba(255,255,255,.025); }
.un-tr[style*="display: none"] { animation: none; }

.un-td {
    padding: .875rem 1rem;
    vertical-align: middle; color: var(--text-2);
}
.un-td--user { padding-left: 1rem; }
.un-td--actions { text-align: right; }

/* Avatar cell */
.un-avatar-cell { display: flex; align-items: center; gap: .875rem; }
.un-avatar {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-family: var(--f-mono); font-size: .8rem; font-weight: 600;
    background: color-mix(in srgb, var(--av-color) 15%, var(--ink-3));
    color: var(--av-color);
    border: 1px solid color-mix(in srgb, var(--av-color) 30%, transparent);
    flex-shrink: 0;
}
.un-user-name { font-size: .9rem; font-weight: 600; color: var(--text); white-space: nowrap; }
.un-user-id   { font-family: var(--f-mono); font-size: .7rem; color: var(--text-3); margin-top: 2px; }

/* Email / Phone */
.un-email { color: var(--text-2); font-size: .875rem; text-decoration: none; }
.un-email:hover { color: var(--accent); }
.un-phone { font-family: var(--f-mono); font-size: .82rem; color: var(--text-2); }

/* Role badge */
.un-role {
    display: inline-flex; align-items: center; gap: .3rem;
    font-size: .72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; padding: .25rem .65rem; border-radius: 6px;
    border: 1px solid;
}
.un-role::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
.un-role--censeur, .un-role--admin, .un-role--administrateur {
    background: var(--accent-10); color: var(--accent); border-color: var(--accent-20);
}
.un-role--professeur, .un-role--teacher {
    background: rgba(99,179,237,.10); color: #63b3ed; border-color: rgba(99,179,237,.25);
}
.un-role--surveillant, .un-role--surveillant-general {
    background: rgba(255,179,71,.10); color: var(--warn); border-color: rgba(255,179,71,.25);
}
.un-role--secretaire, .un-role--secretariat {
    background: rgba(196,181,253,.10); color: #c4b5fd; border-color: rgba(196,181,253,.25);
}
.un-role--default {
    background: rgba(255,255,255,.05); color: var(--text-3); border-color: var(--border);
}

/* Actions */
.un-actions { display: flex; align-items: center; justify-content: flex-end; gap: .5rem; }
.un-action {
    display: inline-flex; align-items: center; gap: .35rem;
    font-family: var(--f-sans); font-size: .78rem; font-weight: 600;
    padding: .35rem .75rem; border-radius: 7px; cursor: pointer;
    border: 1px solid; text-decoration: none;
    transition: background .15s, transform .15s, box-shadow .15s;
    white-space: nowrap;
}
.un-action:active { transform: scale(.97); }
.un-action--edit {
    background: rgba(99,179,237,.08); color: #63b3ed;
    border-color: rgba(99,179,237,.2);
}
.un-action--edit:hover { background: rgba(99,179,237,.18); box-shadow: 0 0 12px rgba(99,179,237,.2); }
.un-action--del {
    background: var(--danger-10); color: var(--danger);
    border-color: rgba(255,77,106,.2);
}
.un-action--del:hover { background: rgba(255,77,106,.2); box-shadow: 0 0 12px rgba(255,77,106,.2); }

/* Empty */
.un-empty {
    padding: 3.5rem 1rem; text-align: center;
    color: var(--text-3); font-size: .875rem;
}
.un-empty-icon { font-size: 2rem; margin-bottom: .75rem; opacity: .3; }

/* ── Modal ── */
.un-overlay {
    position: fixed; inset: 0; z-index: 8000;
    background: rgba(0,0,0,.75); backdrop-filter: blur(4px);
    opacity: 0; pointer-events: none;
    transition: opacity .25s var(--ease);
}
.un-overlay--on { opacity: 1; pointer-events: all; }
.un-modal {
    position: fixed; top: 50%; left: 50%; transform: translate(-50%, -46%);
    z-index: 8001; width: 90%; max-width: 400px;
    background: var(--ink-2); border: 1px solid var(--border-2);
    border-radius: var(--radius-lg); padding: 2rem;
    box-shadow: 0 24px 64px rgba(0,0,0,.6);
    opacity: 0; pointer-events: none;
    transition: opacity .25s var(--ease), transform .25s var(--ease);
}
.un-modal--on { opacity: 1; pointer-events: all; transform: translate(-50%, -50%); }
.un-modal-danger-ring {
    width: 52px; height: 52px; border-radius: 14px;
    background: var(--danger-10); border: 1px solid rgba(255,77,106,.25);
    display: flex; align-items: center; justify-content: center;
    color: var(--danger); margin-bottom: 1.25rem;
}
.un-modal-title { font-size: 1.15rem; font-weight: 700; color: var(--text); margin-bottom: .5rem; }
.un-modal-desc { font-size: .875rem; color: var(--text-2); line-height: 1.6; margin-bottom: 1.75rem; }
.un-modal-desc strong { color: var(--text); }
.un-modal-actions { display: flex; gap: .75rem; justify-content: flex-end; }
.un-modal-cancel {
    font-family: var(--f-sans); font-size: .875rem; font-weight: 600;
    padding: .55rem 1.1rem; border-radius: 8px; cursor: pointer;
    background: var(--ink-3); color: var(--text-2); border: 1px solid var(--border-2);
    transition: background .15s;
}
.un-modal-cancel:hover { background: var(--ink-3); color: var(--text); }
.un-modal-confirm {
    font-family: var(--f-sans); font-size: .875rem; font-weight: 700;
    padding: .55rem 1.25rem; border-radius: 8px; cursor: pointer;
    background: var(--danger); color: #fff; border: none;
    transition: background .15s, box-shadow .15s;
}
.un-modal-confirm:hover { background: #e6364f; box-shadow: 0 4px 18px rgba(255,77,106,.4); }
.un-modal-confirm:disabled { opacity: .6; cursor: not-allowed; }

/* ── Animations ── */
@keyframes un-fade-up {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
@media (max-width: 640px) {
    .un-stats { flex-direction: column; }
    .un-stat  { border-right: none; border-bottom: 1px solid var(--border); }
    .un-stat:last-child { border-bottom: none; }
    .un-page-title { font-size: 1.35rem; }
    .un-action span { display: none; }
}
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--ink-3); border-radius: 99px; }
</style>

<div class="un-root">

    {{-- ── Header ── --}}
    <div class="un-header">
        <div class="un-header-left">
            <div class="un-eyebrow">Administration · Personnel</div>
            <h1 class="un-page-title">Utilisateurs</h1>
        </div>
        <a href="{{ route('user.create') }}" class="un-btn-create">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Nouvel utilisateur
        </a>
    </div>

    {{-- ── Flash ── --}}
    @if(session('success'))
    <div class="un-flash un-flash--success" id="flash-msg">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
        {{ session('success') }}
        <button onclick="this.parentElement.remove()" aria-label="Fermer">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
    </div>
    @endif
    @if(session('error'))
    <div class="un-flash un-flash--error" id="flash-msg">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
        {{ session('error') }}
        <button onclick="this.parentElement.remove()" aria-label="Fermer">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    {{-- ── Stats ── --}}
    <div class="un-stats">
        @php
            $totalUsers = $users->count();
            $rolesList  = $users->map(fn($u) => optional($u->roles->first())->name)->filter()->unique()->values();
        @endphp
        <div class="un-stat">
            <span class="un-stat-val">{{ $totalUsers }}</span>
            <span class="un-stat-key">Total</span>
        </div>
        <div class="un-stat">
            <span class="un-stat-val un-stat-val--accent">{{ $rolesList->count() }}</span>
            <span class="un-stat-key">Rôles</span>
        </div>
        <div class="un-stat">
            <span class="un-stat-val">
                {{ $users->filter(fn($u) => $u->created_at && $u->created_at->isCurrentMonth())->count() }}
            </span>
            <span class="un-stat-key">Ce mois</span>
        </div>
    </div>

    {{-- ── Table card ── --}}
    <div class="un-card">

        {{-- Toolbar --}}
        <div class="un-toolbar">
            <div class="un-search-wrap">
                <svg class="un-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" id="un-search" class="un-search-input" placeholder="Nom, email, rôle…" autocomplete="off">
            </div>
            <span class="un-count" id="un-count">{{ $totalUsers }} entrée(s)</span>
        </div>

        {{-- Table --}}
        <div class="un-scroll">
            <table class="un-table" id="un-table">
                <thead>
                    <tr>
                        <th class="un-th un-th--user" data-sort="name">Utilisateur</th>
                        <th class="un-th" data-sort="email">Email</th>
                        <th class="un-th">Contact</th>
                        <th class="un-th" data-sort="role">Rôle</th>
                        <th class="un-th un-th--actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="un-tbody">
                    @forelse($users as $i => $user)
                    @php
                        $role     = optional($user->roles->first())->name ?? 'N/A';
                        $roleSlug = strtolower(str_replace([' ', 'é', 'è', 'ê', 'â'], ['-', 'e', 'e', 'e', 'a'], $role));
                        $initials = strtoupper(mb_substr($user->name ?? '?', 0, 1) . mb_substr($user->surname ?? '', 0, 1));
                        $palette  = ['#00e5a0','#63b3ed','#c4b5fd','#ffb347','#ff7eb3','#38bdf8'];
                        $color    = $palette[$user->id % count($palette)];
                    @endphp
                    <tr class="un-tr" style="animation-delay: {{ $i * 35 }}ms"
                        data-name="{{ strtolower($user->name . ' ' . $user->surname) }}"
                        data-email="{{ strtolower($user->email) }}"
                        data-role="{{ strtolower($role) }}">
                        <td class="un-td un-td--user">
                            <div class="un-avatar-cell">
                                <div class="un-avatar" style="--av-color:{{ $color }}">{{ $initials }}</div>
                                <div>
                                    <div class="un-user-name">{{ $user->name }} {{ $user->surname }}</div>
                                    <div class="un-user-id">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="un-td">
                            <a href="mailto:{{ $user->email }}" class="un-email">{{ $user->email }}</a>
                        </td>
                        <td class="un-td">
                            <span class="un-phone">{{ $user->phone ?? '—' }}</span>
                        </td>
                        <td class="un-td">
                            <span class="un-role un-role--{{ $roleSlug }}">{{ $role }}</span>
                        </td>
                        <td class="un-td un-td--actions">
                            <div class="un-actions">
                                <a href="{{ route('user.edit', $user) }}" class="un-action un-action--edit">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    <span>Modifier</span>
                                </a>
                                <button class="un-action un-action--del"
                                        onclick="unConfirmDelete('{{ $user->id }}', '{{ addslashes($user->name . ' ' . $user->surname) }}')">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    <span>Supprimer</span>
                                </button>
                                <form id="del-{{ $user->id }}" action="{{ route('user.destroy', $user) }}" method="POST" hidden>
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="un-empty">
                            <div class="un-empty-icon">👤</div>
                            Aucun utilisateur enregistré
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Ligne vide filtre ── --}}
    <div class="un-card" id="un-no-results" style="display:none;">
        <div class="un-empty">
            <div class="un-empty-icon">🔍</div>
            Aucun résultat pour cette recherche
        </div>
    </div>

</div>

{{-- ── Modal suppression ── --}}
<div class="un-overlay" id="un-overlay" onclick="unClose()"></div>
<div class="un-modal" id="un-modal" role="dialog" aria-modal="true" aria-labelledby="un-modal-title">
    <div class="un-modal-danger-ring">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
    </div>
    <h3 class="un-modal-title" id="un-modal-title">Confirmer la suppression</h3>
    <p class="un-modal-desc">
        Vous allez supprimer définitivement <strong id="un-modal-name"></strong>.
        Cette opération est <em>irréversible</em>.
    </p>
    <div class="un-modal-actions">
        <button class="un-modal-cancel" onclick="unClose()">Annuler</button>
        <button class="un-modal-confirm" id="un-modal-btn">Supprimer</button>
    </div>
</div>

@endsection

@section('another_JS')
<script>
/* ── Recherche live ────────────────────────────────────── */
const searchEl    = document.getElementById('un-search');
const countEl     = document.getElementById('un-count');
const noResults   = document.getElementById('un-no-results');
const rows        = () => document.querySelectorAll('#un-tbody .un-tr');

searchEl?.addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    let n = 0;
    rows().forEach(r => {
        const match = !q
            || r.dataset.name.includes(q)
            || r.dataset.email.includes(q)
            || r.dataset.role.includes(q);
        r.style.display = match ? '' : 'none';
        if (match) n++;
    });
    countEl.textContent  = `${n} entrée(s)`;
    noResults.style.display = (n === 0 && q) ? 'block' : 'none';
});

/* ── Modal ────────────────────────────────────────────── */
let _pendingId = null;

function unConfirmDelete(id, name) {
    _pendingId = id;
    document.getElementById('un-modal-name').textContent = name;
    document.getElementById('un-overlay').classList.add('un-overlay--on');
    document.getElementById('un-modal').classList.add('un-modal--on');
    setTimeout(() => document.getElementById('un-modal-btn').focus(), 50);
}

function unClose() {
    _pendingId = null;
    document.getElementById('un-overlay').classList.remove('un-overlay--on');
    document.getElementById('un-modal').classList.remove('un-modal--on');
}

document.getElementById('un-modal-btn')?.addEventListener('click', () => {
    if (!_pendingId) return;
    const btn = document.getElementById('un-modal-btn');
    btn.disabled    = true;
    btn.textContent = 'Suppression…';
    document.getElementById(`del-${_pendingId}`).submit();
});

document.addEventListener('keydown', e => { if (e.key === 'Escape') unClose(); });

/* ── Flash auto-dismiss ───────────────────────────────── */
document.querySelectorAll('.un-flash').forEach(el => {
    setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 350); }, 5000);
});
</script>
@endsection
