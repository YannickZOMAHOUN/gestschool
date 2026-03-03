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
    --ink-4:     #2c3145;
    --border:    rgba(255,255,255,.07);
    --border-2:  rgba(255,255,255,.12);
    --border-3:  rgba(255,255,255,.18);
    --text:      #e8eaf0;
    --text-2:    #8b91a7;
    --text-3:    #555d78;
    --accent:    #00e5a0;
    --accent-d:  #00b87f;
    --accent-10: rgba(0,229,160,.10);
    --accent-20: rgba(0,229,160,.20);
    --danger:    #ff4d6a;
    --danger-10: rgba(255,77,106,.10);
    --danger-20: rgba(255,77,106,.20);
    --warn:      #ffb347;
    --warn-10:   rgba(255,179,71,.10);
    --f-sans:    'Plus Jakarta Sans', system-ui, sans-serif;
    --f-mono:    'JetBrains Mono', monospace;
    --radius:    10px;
    --radius-lg: 16px;
    --ease:      cubic-bezier(.4,0,.2,1);
}
*,*::before,*::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ── Root ── */
.uc-root {
    font-family: var(--f-sans);
    background: var(--ink);
    min-height: 100vh;
    padding: 2rem 1.5rem;
    color: var(--text);
    position: relative;
}
.uc-root::before {
    content: '';
    position: fixed; inset: 0; pointer-events: none; z-index: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
    background-size: 200px;
}
.uc-inner { position: relative; z-index: 1; max-width: 780px; margin: 0 auto; }

/* ── Breadcrumb ── */
.uc-breadcrumb {
    display: flex; align-items: center; gap: .5rem;
    font-size: .78rem; color: var(--text-3); margin-bottom: 1.75rem;
    opacity: 0; animation: uc-up .4s var(--ease) .05s forwards;
}
.uc-breadcrumb a { color: var(--text-3); text-decoration: none; transition: color .15s; }
.uc-breadcrumb a:hover { color: var(--accent); }
.uc-breadcrumb svg { flex-shrink: 0; }
.uc-breadcrumb span { color: var(--text-2); }

/* ── Header ── */
.uc-header {
    margin-bottom: 2rem;
    opacity: 0; animation: uc-up .45s var(--ease) .1s forwards;
}
.uc-eyebrow {
    font-size: .72rem; font-weight: 600; letter-spacing: .12em;
    text-transform: uppercase; color: var(--accent); margin-bottom: .4rem;
}
.uc-title { font-size: 1.75rem; font-weight: 800; letter-spacing: -.02em; }
.uc-subtitle { font-size: .875rem; color: var(--text-3); margin-top: .4rem; }

/* ── Flash ── */
.uc-flash {
    display: flex; align-items: center; gap: .75rem;
    padding: .875rem 1.25rem; border-radius: var(--radius);
    border: 1px solid; font-size: .875rem; font-weight: 500;
    margin-bottom: 1.25rem;
    opacity: 0; animation: uc-up .4s var(--ease) .15s forwards;
}
.uc-flash--error { background: var(--danger-10); border-color: rgba(255,77,106,.3); color: var(--danger); }

/* ── Avatar preview ── */
.uc-preview-bar {
    display: flex; align-items: center; gap: 1.25rem;
    background: var(--ink-2); border: 1px solid var(--border);
    border-radius: var(--radius-lg); padding: 1.25rem 1.5rem;
    margin-bottom: 1.75rem;
    opacity: 0; animation: uc-up .45s var(--ease) .2s forwards;
}
.uc-avatar-preview {
    width: 54px; height: 54px; border-radius: 14px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-family: var(--f-mono); font-size: 1rem; font-weight: 700;
    background: var(--ink-3); color: var(--text-3);
    border: 1px solid var(--border-2);
    transition: background .3s, color .3s, border-color .3s;
}
.uc-preview-info { flex: 1; min-width: 0; }
.uc-preview-name {
    font-size: 1rem; font-weight: 700; color: var(--text);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    transition: color .2s;
}
.uc-preview-role { font-size: .78rem; color: var(--text-3); margin-top: 3px; }
.uc-preview-pwd {
    font-family: var(--f-mono); font-size: .78rem;
    color: var(--warn); background: var(--warn-10);
    border: 1px solid rgba(255,179,71,.2); border-radius: 6px;
    padding: .3rem .65rem; white-space: nowrap;
    display: flex; align-items: center; gap: .4rem;
}
.uc-preview-pwd svg { flex-shrink: 0; }

/* ── Card ── */
.uc-card {
    background: var(--ink-2); border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    opacity: 0; animation: uc-up .5s var(--ease) .25s forwards;
}
.uc-card-section {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border);
}
.uc-card-section:last-child { border-bottom: none; }
.uc-section-title {
    font-size: .72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .1em; color: var(--text-3); margin-bottom: 1.25rem;
    display: flex; align-items: center; gap: .5rem;
}
.uc-section-title::after {
    content: ''; flex: 1; height: 1px; background: var(--border);
}

/* ── Form grid ── */
.uc-grid { display: grid; gap: 1.25rem; }
.uc-grid--2 { grid-template-columns: 1fr 1fr; }
@media(max-width:640px) { .uc-grid--2 { grid-template-columns: 1fr; } }

/* ── Field ── */
.uc-field { display: flex; flex-direction: column; gap: .45rem; }
.uc-label {
    font-size: .78rem; font-weight: 600; color: var(--text-2);
    display: flex; align-items: center; gap: .4rem;
    text-transform: uppercase; letter-spacing: .05em;
}
.uc-label .uc-required { color: var(--danger); font-size: .85em; }

.uc-input-wrap { position: relative; }
.uc-input-icon {
    position: absolute; left: .875rem; top: 50%; transform: translateY(-50%);
    color: var(--text-3); pointer-events: none; transition: color .2s;
}
.uc-input {
    font-family: var(--f-sans); font-size: .9rem;
    width: 100%; height: 46px;
    padding: 0 3rem 0 2.75rem;
    background: var(--ink-3); border: 1px solid var(--border-2);
    border-radius: var(--radius); color: var(--text);
    transition: border-color .18s var(--ease), box-shadow .18s var(--ease), background .18s;
}
.uc-input::placeholder { color: var(--text-3); font-size: .875rem; }
.uc-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-10); background: var(--ink-4); }
.uc-input:focus ~ .uc-input-icon { color: var(--accent); }
.uc-input-wrap:focus-within .uc-input-icon { color: var(--accent); }

/* Validation states */
.uc-field.is-valid   .uc-input { border-color: var(--accent); }
.uc-field.is-invalid .uc-input { border-color: var(--danger); box-shadow: 0 0 0 3px var(--danger-10); }
.uc-field.is-valid   .uc-input-icon { color: var(--accent); }
.uc-field.is-invalid .uc-input-icon { color: var(--danger); }

/* Indicateur droite (check / X) */
.uc-input-status {
    position: absolute; right: .875rem; top: 50%; transform: translateY(-50%);
    transition: opacity .2s;
}
.uc-input-status--valid   { color: var(--accent); }
.uc-input-status--invalid { color: var(--danger); }

/* ── Select custom ── */
.uc-select {
    font-family: var(--f-sans); font-size: .9rem;
    width: 100%; height: 46px;
    padding: 0 2.25rem 0 .875rem;
    background: var(--ink-3)
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%23555d78' d='M8 10.94L2.53 5.47l1.06-1.06L8 8.82l4.41-4.41 1.06 1.06z'/%3E%3C/svg%3E")
        no-repeat right .7rem center / 14px;
    border: 1px solid var(--border-2); border-radius: var(--radius);
    color: var(--text); cursor: pointer; appearance: none;
    transition: border-color .18s, box-shadow .18s, background .18s;
}
.uc-select:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-10); background-color: var(--ink-4); }
.uc-select option { background: var(--ink-3); }

/* Role cards selector */
.uc-role-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
    gap: .75rem;
}
.uc-role-card {
    display: flex; flex-direction: column; align-items: center; gap: .5rem;
    padding: 1rem .75rem; border-radius: var(--radius);
    border: 1px solid var(--border); background: var(--ink-3);
    cursor: pointer; transition: border-color .18s, background .18s, transform .18s;
    user-select: none;
}
.uc-role-card:hover { border-color: var(--border-3); background: var(--ink-4); transform: translateY(-2px); }
.uc-role-card.selected {
    border-color: var(--accent); background: var(--accent-10);
    box-shadow: 0 0 0 1px var(--accent-20), 0 4px 16px var(--accent-10);
}
.uc-role-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; background: var(--ink-2); border: 1px solid var(--border);
    transition: background .18s, border-color .18s;
}
.uc-role-card.selected .uc-role-icon { background: var(--accent-10); border-color: var(--accent-20); }
.uc-role-name { font-size: .8rem; font-weight: 600; text-align: center; color: var(--text-2); transition: color .18s; }
.uc-role-card.selected .uc-role-name { color: var(--accent); }

/* ── Erreurs ── */
.uc-error {
    display: flex; align-items: center; gap: .35rem;
    font-size: .78rem; color: var(--danger); font-weight: 500;
    height: 0; overflow: hidden; opacity: 0;
    transition: height .2s var(--ease), opacity .2s var(--ease), margin .2s;
}
.uc-error.visible { height: 1.2rem; opacity: 1; }
.uc-error-blade {
    color: var(--danger); font-size: .78rem; font-weight: 500;
    display: flex; align-items: center; gap: .35rem; margin-top: .25rem;
}

/* ── Actions bar ── */
.uc-actions {
    padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: flex-end;
    gap: .75rem; border-top: 1px solid var(--border);
    opacity: 0; animation: uc-up .5s var(--ease) .35s forwards;
}
.uc-btn {
    font-family: var(--f-sans); font-size: .875rem; font-weight: 700;
    height: 44px; padding: 0 1.5rem; border-radius: var(--radius);
    cursor: pointer; display: inline-flex; align-items: center; gap: .5rem;
    transition: background .18s, transform .18s, box-shadow .18s;
    border: 1px solid transparent;
}
.uc-btn--ghost {
    background: var(--ink-3); color: var(--text-2); border-color: var(--border-2);
}
.uc-btn--ghost:hover { background: var(--ink-4); color: var(--text); }
.uc-btn--submit {
    background: var(--accent); color: var(--ink);
}
.uc-btn--submit:hover:not(:disabled) { background: var(--accent-d); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,229,160,.35); }
.uc-btn--submit:disabled { opacity: .5; cursor: not-allowed; }

/* Spinner inline */
@keyframes uc-spin { to { transform: rotate(360deg); } }
.uc-spin {
    width: 16px; height: 16px; border-radius: 50%;
    border: 2px solid rgba(0,0,0,.25); border-top-color: var(--ink);
    animation: uc-spin .7s linear infinite; display: none;
}
.uc-btn--submit.loading .uc-spin   { display: block; }
.uc-btn--submit.loading .uc-btn-txt { display: none; }

/* ── Animations ── */
@keyframes uc-up {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
::-webkit-scrollbar { width: 5px; }
::-webkit-scrollbar-thumb { background: var(--ink-3); border-radius: 99px; }
</style>

<div class="uc-root">
<div class="uc-inner">

    {{-- Breadcrumb --}}
    <nav class="uc-breadcrumb">
        <a href="{{ route('user.index') }}">Utilisateurs</a>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        <span>Nouveau</span>
    </nav>

    {{-- Header --}}
    <div class="uc-header">
        <div class="uc-eyebrow">Administration · Personnel</div>
        <h1 class="uc-title">Nouvel utilisateur</h1>
        <p class="uc-subtitle">Le mot de passe initial sera automatiquement défini sur le numéro de contact.</p>
    </div>

    {{-- Flash erreur serveur --}}
    @if(session('error'))
    <div class="uc-flash uc-flash--error">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── Preview card temps réel ── --}}
    <div class="uc-preview-bar">
        <div class="uc-avatar-preview" id="pv-avatar">?</div>
        <div class="uc-preview-info">
            <div class="uc-preview-name" id="pv-name">Nom Prénom</div>
            <div class="uc-preview-role" id="pv-role">Aucun rôle sélectionné</div>
        </div>
        <div class="uc-preview-pwd" id="pv-pwd">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <span id="pv-pwd-txt">—</span>
        </div>
    </div>

    {{-- ── Formulaire ── --}}
    <form action="{{ route('user.store') }}" method="POST" id="uc-form" novalidate>
        @csrf

        <div class="uc-card">

            {{-- Section Identité --}}
            <div class="uc-card-section">
                <div class="uc-section-title">Identité</div>
                <div class="uc-grid uc-grid--2">

                    {{-- Nom --}}
                    <div class="uc-field @error('name') is-invalid @enderror" id="field-name">
                        <label class="uc-label" for="name">Nom <span class="uc-required">*</span></label>
                        <div class="uc-input-wrap">
                            <svg class="uc-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                            <input type="text" name="name" id="name" class="uc-input"
                                   value="{{ old('name') }}" placeholder="Ex : Dupont" autocomplete="family-name">
                            <span class="uc-input-status" id="status-name"></span>
                        </div>
                        @error('name')
                            <div class="uc-error-blade"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>
                        @enderror
                        <div class="uc-error" id="err-name"></div>
                    </div>

                    {{-- Prénom --}}
                    <div class="uc-field @error('surname') is-invalid @enderror" id="field-surname">
                        <label class="uc-label" for="surname">Prénoms <span class="uc-required">*</span></label>
                        <div class="uc-input-wrap">
                            <svg class="uc-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                            <input type="text" name="surname" id="surname" class="uc-input"
                                   value="{{ old('surname') }}" placeholder="Ex : Jean-Marie" autocomplete="given-name">
                            <span class="uc-input-status" id="status-surname"></span>
                        </div>
                        @error('surname')
                            <div class="uc-error-blade"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>
                        @enderror
                        <div class="uc-error" id="err-surname"></div>
                    </div>
                </div>
            </div>

            {{-- Section Coordonnées --}}
            <div class="uc-card-section">
                <div class="uc-section-title">Coordonnées</div>
                <div class="uc-grid uc-grid--2">

                    {{-- Email --}}
                    <div class="uc-field @error('email') is-invalid @enderror" id="field-email">
                        <label class="uc-label" for="email">Adresse email <span class="uc-required">*</span></label>
                        <div class="uc-input-wrap">
                            <svg class="uc-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 7 10-7"/></svg>
                            <input type="email" name="email" id="email" class="uc-input"
                                   value="{{ old('email') }}" placeholder="exemple@ecole.bj" autocomplete="email">
                            <span class="uc-input-status" id="status-email"></span>
                        </div>
                        @error('email')
                            <div class="uc-error-blade"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>
                        @enderror
                        <div class="uc-error" id="err-email"></div>
                    </div>

                    {{-- Téléphone --}}
                    <div class="uc-field @error('phone') is-invalid @enderror" id="field-phone">
                        <label class="uc-label" for="phone">
                            Contact
                            <span class="uc-required">*</span>
                            <span style="font-size:.7rem;color:var(--warn);font-weight:500;text-transform:none;letter-spacing:0">
                                = mot de passe initial
                            </span>
                        </label>
                        <div class="uc-input-wrap">
                            <svg class="uc-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.97-.97a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <input type="text" name="phone" id="phone" class="uc-input"
                                   value="{{ old('phone') }}" placeholder="+229 01 XX XX XX XX" autocomplete="tel">
                            <span class="uc-input-status" id="status-phone"></span>
                        </div>
                        @error('phone')
                            <div class="uc-error-blade"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>
                        @enderror
                        <div class="uc-error" id="err-phone"></div>
                    </div>
                </div>
            </div>

            {{-- Section Rôle --}}
            <div class="uc-card-section">
                <div class="uc-section-title">Rôle & Fonction</div>
                <input type="hidden" name="role_id" id="role_id" value="{{ old('role_id') }}">

                <div class="uc-role-grid" id="role-grid">
                    @php
                        $roleIcons = [
                            'censeur'    => '🏛️',
                            'directeur'  => '👑',
                            'professeur' => '📚',
                            'surveillant'=> '👁️',
                            'secretaire' => '📋',
                            'comptable'  => '📊',
                            'default'    => '👤',
                        ];
                    @endphp
                    @foreach($roles as $role)
                    @php
                        $slug = strtolower(trim($role->name));
                        $icon = $roleIcons[$slug] ?? $roleIcons['default'];
                    @endphp
                    <div class="uc-role-card {{ old('role_id') == $role->id ? 'selected' : '' }}"
                         data-id="{{ $role->id }}"
                         data-name="{{ $role->name }}"
                         onclick="ucSelectRole(this)"
                         tabindex="0"
                         role="button"
                         onkeydown="if(event.key==='Enter'||event.key===' ')ucSelectRole(this)">
                        <div class="uc-role-icon">{{ $icon }}</div>
                        <div class="uc-role-name">{{ $role->name }}</div>
                    </div>
                    @endforeach
                </div>

                @error('role_id')
                    <div class="uc-error-blade" style="margin-top:.75rem">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                        {{ $message }}
                    </div>
                @enderror
                <div class="uc-error" id="err-role" style="margin-top:.5rem"></div>
            </div>

            {{-- Actions --}}
            <div class="uc-actions">
                <a href="{{ route('user.index') }}" class="uc-btn uc-btn--ghost">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                    Annuler
                </a>
                <button type="submit" class="uc-btn uc-btn--submit" id="uc-submit">
                    <div class="uc-spin"></div>
                    <span class="uc-btn-txt">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                        Enregistrer
                    </span>
                </button>
            </div>
        </div>
    </form>

</div>
</div>

@endsection

@section('another_JS')
<script>
// ── Palettes avatars ───────────────────────────────────────────────────────
const PALETTE = ['#00e5a0','#63b3ed','#c4b5fd','#ffb347','#ff7eb3','#38bdf8'];
let palIdx = 0;

// ── Preview temps réel ─────────────────────────────────────────────────────
function updatePreview() {
    const name    = (document.getElementById('name')?.value ?? '').trim();
    const surname = (document.getElementById('surname')?.value ?? '').trim();
    const phone   = (document.getElementById('phone')?.value ?? '').trim();

    const initials = ((name[0] ?? '') + (surname[0] ?? '')).toUpperCase() || '?';
    const fullName = [name, surname].filter(Boolean).join(' ') || 'Nom Prénom';
    const color    = PALETTE[palIdx % PALETTE.length];

    const av = document.getElementById('pv-avatar');
    av.textContent = initials;
    av.style.setProperty('--av-color', color);
    av.style.background  = `color-mix(in srgb, ${color} 15%, #232736)`;
    av.style.color        = color;
    av.style.borderColor  = `color-mix(in srgb, ${color} 30%, transparent)`;

    document.getElementById('pv-name').textContent = fullName;
    document.getElementById('pv-pwd-txt').textContent = phone || '—';
}

['name','surname','phone'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', updatePreview);
});

// ── Sélection rôle ─────────────────────────────────────────────────────────
function ucSelectRole(card) {
    document.querySelectorAll('.uc-role-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('role_id').value = card.dataset.id;
    document.getElementById('pv-role').textContent = card.dataset.name;
    palIdx = parseInt(card.dataset.id) || 0;
    updatePreview();
    // Effacer l'erreur rôle
    const err = document.getElementById('err-role');
    if (err) { err.textContent = ''; err.classList.remove('visible'); }
}

// ── Validation champ ───────────────────────────────────────────────────────
const validators = {
    name:    v => v.trim().length >= 2 ? null : 'Au moins 2 caractères requis.',
    surname: v => v.trim().length >= 2 ? null : 'Au moins 2 caractères requis.',
    email:   v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ? null : 'Adresse email invalide.',
    phone:   v => v.trim().length >= 6 ? null : 'Numéro trop court.',
};

const checkIcon = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>`;
const crossIcon = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>`;

function validateField(id) {
    const input  = document.getElementById(id);
    const field  = document.getElementById(`field-${id}`);
    const status = document.getElementById(`status-${id}`);
    const err    = document.getElementById(`err-${id}`);
    if (!input || !field) return true;

    const val    = input.value;
    const msg    = validators[id] ? validators[id](val) : null;

    field.classList.toggle('is-valid',   !msg && val.length > 0);
    field.classList.toggle('is-invalid',  !!msg && val.length > 0);

    if (status) {
        status.innerHTML   = val.length === 0 ? '' : (msg ? crossIcon : checkIcon);
        status.className   = `uc-input-status uc-input-status--${msg ? 'invalid' : 'valid'}`;
    }
    if (err) {
        err.textContent = msg ?? '';
        err.classList.toggle('visible', !!msg && val.length > 0);
    }
    return !msg;
}

Object.keys(validators).forEach(id => {
    const el = document.getElementById(id);
    el?.addEventListener('input',  () => validateField(id));
    el?.addEventListener('blur',   () => validateField(id));
});

// ── Submit guard ───────────────────────────────────────────────────────────
document.getElementById('uc-form')?.addEventListener('submit', function(e) {
    const fields  = Object.keys(validators);
    const allOk   = fields.map(id => validateField(id)).every(Boolean);
    const roleOk  = !!document.getElementById('role_id').value;

    if (!roleOk) {
        const err = document.getElementById('err-role');
        err.textContent = 'Veuillez sélectionner un rôle.';
        err.classList.add('visible');
    }

    if (!allOk || !roleOk) {
        e.preventDefault();
        // Scroll vers la première erreur
        const firstErr = document.querySelector('.uc-field.is-invalid, .uc-error.visible');
        firstErr?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    // Feedback visuel chargement
    const btn = document.getElementById('uc-submit');
    btn.classList.add('loading');
    btn.disabled = true;
});

// ── Init (valeurs old()) ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    updatePreview();
    // Restaurer le rôle pré-sélectionné (après validation serveur)
    const selectedCard = document.querySelector('.uc-role-card.selected');
    if (selectedCard) {
        document.getElementById('pv-role').textContent = selectedCard.dataset.name;
    }
    // Valider les champs déjà remplis (valeurs old())
    Object.keys(validators).forEach(id => {
        const el = document.getElementById(id);
        if (el?.value) validateField(id);
    });
});
</script>
@endsection
