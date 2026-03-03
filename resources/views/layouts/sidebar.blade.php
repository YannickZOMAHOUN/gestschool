{{-- ======================================================
     SIDEBAR — Dark theme, filtrage par rôle
     Version: Premium UI 2.0
     ====================================================== --}}

@php
    $user       = auth()->user();
    $isTeacher  = $user->isEnseignant();
    $isPP       = $isTeacher && $user->principalClasses()->exists();
    $isAdmin    = !$isTeacher;
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<aside id="sidebar" class="dk-sidebar">

    {{-- ── Logo ─────────────────────────────────────────── --}}
    <div class="dk-sidebar-brand">
        <div class="brand-icon">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="brand-text">
            <span class="brand-name">School</span><span class="brand-accent">Manager</span>
        </div>
        <button class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Fermer">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- ── User Pill ─────────────────────────────────────── --}}
    <div class="dk-user-pill">
        <div class="dk-avatar-lg">
            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr($user->surname, 0, 1)) }}
        </div>
        <div class="dk-user-info">
            <span class="dk-user-name">{{ $user->surname }} {{ $user->name }}</span>
            <span class="dk-user-role">
                <span class="role-dot"></span>
                {{ $user->roles->first()->name ?? '—' }}
            </span>
        </div>
    </div>

    {{-- ── Navigation ───────────────────────────────────── --}}
    <nav class="dk-nav" id="sidebar-nav">

        @if($isAdmin)
        <a class="dk-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
            <span class="dk-nav-icon"><i class="fas fa-home"></i></span>
            <span class="dk-nav-label">Tableau de bord</span>
            @if(request()->routeIs('home'))
                <span class="active-indicator"></span>
            @endif
        </a>
        @endif

        {{-- ── Section : Notes ────────────────────────── --}}
        <div class="dk-nav-section"><span>Notes</span></div>

        @if($isTeacher)
            <a class="dk-nav-link {{ request()->routeIs('note.create') ? 'active' : '' }}" href="{{ route('note.create') }}">
                <span class="dk-nav-icon"><i class="fas fa-pen-alt"></i></span>
                <span class="dk-nav-label">Saisir les notes</span>
            </a>

            @if($isPP)
                <a class="dk-nav-link {{ request()->routeIs('note.index') ? 'active' : '' }}" href="{{ route('note.index') }}">
                    <span class="dk-nav-icon"><i class="fas fa-table"></i></span>
                    <span class="dk-nav-label">Consulter les notes</span>
                    <span class="dk-badge">PP</span>
                </a>
                <a class="dk-nav-link {{ request()->routeIs('get.cards') ? 'active' : '' }}" href="{{ route('get.cards') }}">
                    <span class="dk-nav-icon"><i class="fas fa-file-alt"></i></span>
                    <span class="dk-nav-label">Bulletins</span>
                    <span class="dk-badge">PP</span>
                </a>
                <a class="dk-nav-link {{ request()->routeIs('export_view') ? 'active' : '' }}" href="{{ route('export_view') }}">
                    <span class="dk-nav-icon"><i class="fas fa-file-excel"></i></span>
                    <span class="dk-nav-label">Exporter les notes</span>
                    <span class="dk-badge">PP</span>
                </a>
            @endif

        @else
            <a class="dk-nav-link {{ request()->routeIs('note.index') ? 'active' : '' }}" href="{{ route('note.index') }}">
                <span class="dk-nav-icon"><i class="fas fa-table"></i></span>
                <span class="dk-nav-label">Consulter les notes</span>
            </a>
            <a class="dk-nav-link {{ request()->routeIs('note.create') ? 'active' : '' }}" href="{{ route('note.create') }}">
                <span class="dk-nav-icon"><i class="fas fa-pen-alt"></i></span>
                <span class="dk-nav-label">Saisir une note</span>
            </a>
            <a class="dk-nav-link {{ request()->routeIs('export_view') ? 'active' : '' }}" href="{{ route('export_view') }}">
                <span class="dk-nav-icon"><i class="fas fa-file-excel"></i></span>
                <span class="dk-nav-label">Exporter les notes</span>
            </a>
            <a class="dk-nav-link {{ request()->routeIs('get.cards') ? 'active' : '' }}" href="{{ route('get.cards') }}">
                <span class="dk-nav-icon"><i class="fas fa-file-alt"></i></span>
                <span class="dk-nav-label">Bulletins</span>
            </a>
        @endif

        @if($isAdmin)

        {{-- ── Section : Utilisateurs ── --}}
        <div class="dk-nav-section"><span>Utilisateurs</span></div>

        <button class="dk-nav-link dk-collapsible {{ request()->routeIs('user.*', 'teacher-assignments.*', 'principal-teachers.*') ? 'active open' : '' }}"
                data-target="users-sub">
            <span class="dk-nav-icon"><i class="fas fa-users"></i></span>
            <span class="dk-nav-label">Utilisateurs</span>
            <i class="fas fa-chevron-right dk-chevron"></i>
        </button>
        <div class="dk-sub-menu" id="users-sub">
            <a class="dk-sub-link {{ request()->routeIs('user.create') ? 'active' : '' }}" href="{{ route('user.create') }}">
                <i class="fas fa-user-plus"></i> Nouvel utilisateur
            </a>
            <a class="dk-sub-link {{ request()->routeIs('user.index') ? 'active' : '' }}" href="{{ route('user.index') }}">
                <i class="fas fa-list"></i> Liste des utilisateurs
            </a>
            <a class="dk-sub-link {{ request()->routeIs('teacher-assignments.*') ? 'active' : '' }}" href="{{ route('teacher-assignments.index') }}">
                <i class="fas fa-chalkboard-teacher"></i> Enseignants par classe
            </a>
            <a class="dk-sub-link {{ request()->routeIs('principal-teachers.*') ? 'active' : '' }}" href="{{ route('principal-teachers.index') }}">
                <i class="fas fa-star"></i> Professeurs Principaux
            </a>
        </div>

        {{-- ── Section : Élèves ──────── --}}
        <div class="dk-nav-section"><span>Élèves</span></div>

        <button class="dk-nav-link dk-collapsible {{ request()->routeIs('student.*') ? 'active open' : '' }}"
                data-target="students-sub">
            <span class="dk-nav-icon"><i class="fas fa-user-graduate"></i></span>
            <span class="dk-nav-label">Élèves</span>
            <i class="fas fa-chevron-right dk-chevron"></i>
        </button>
        <div class="dk-sub-menu" id="students-sub">
            <a class="dk-sub-link {{ request()->routeIs('student.index') ? 'active' : '' }}" href="{{ route('student.index') }}">
                <i class="fas fa-list"></i> Liste des élèves
            </a>
            <a class="dk-sub-link {{ request()->routeIs('student.create') ? 'active' : '' }}" href="{{ route('student.create') }}">
                <i class="fas fa-user-plus"></i> Inscrire un élève
            </a>
        </div>

        {{-- ── Section : Configuration ─ --}}
        <div class="dk-nav-section"><span>Configuration</span></div>

        <button class="dk-nav-link dk-collapsible {{ request()->routeIs('year.*', 'sector.*', 'sectorbyyear.*', 'promotionbysector.*', 'promotion-classrooms.*', 'subject.*', 'ratio.*') ? 'active open' : '' }}"
                data-target="settings-sub">
            <span class="dk-nav-icon"><i class="fas fa-cogs"></i></span>
            <span class="dk-nav-label">Paramètres</span>
            <i class="fas fa-chevron-right dk-chevron"></i>
        </button>
        <div class="dk-sub-menu" id="settings-sub">
            <a class="dk-sub-link {{ request()->routeIs('year.create') ? 'active' : '' }}" href="{{ route('year.create') }}">
                <i class="fas fa-calendar-alt"></i> Année scolaire
            </a>
            <a class="dk-sub-link {{ request()->routeIs('sectorbyyear.create') ? 'active' : '' }}" href="{{ route('sectorbyyear.create') }}">
                <i class="fas fa-sitemap"></i> Filières
            </a>
            <a class="dk-sub-link {{ request()->routeIs('promotionbysector.create') ? 'active' : '' }}" href="{{ route('promotionbysector.create') }}">
                <i class="fas fa-layer-group"></i> Promotions
            </a>
            <a class="dk-sub-link {{ request()->routeIs('promotion-classrooms.create') ? 'active' : '' }}" href="{{ route('promotion-classrooms.create') }}">
                <i class="fas fa-door-open"></i> Classes
            </a>
            <a class="dk-sub-link {{ request()->routeIs('subject.create') ? 'active' : '' }}" href="{{ route('subject.create') }}">
                <i class="fas fa-book"></i> Matières
            </a>
            <a class="dk-sub-link {{ request()->routeIs('ratio.create') ? 'active' : '' }}" href="{{ route('ratio.create') }}">
                <i class="fas fa-balance-scale"></i> Coefficients
            </a>
        </div>

        @endif

        <div class="dk-nav-spacer"></div>

    </nav>

    {{-- ── Footer / Logout ──────────────────────────────── --}}
    <div class="dk-sidebar-footer">
        <a href="{{ route('logout') }}" class="dk-logout-btn"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</aside>

{{-- Overlay mobile --}}
<div class="dk-overlay" id="sidebarOverlay"></div>

<style>
/* ================================================================
   FONTS & RESET
   ================================================================ */
.dk-sidebar, .dk-sidebar * {
    font-family: 'Plus Jakarta Sans', sans-serif;
    box-sizing: border-box;
}

/* ================================================================
   CSS VARIABLES
   ================================================================ */
:root {
    --sb-bg:            #080c14;
    --sb-surface:       #0f1520;
    --sb-surface-2:     #141c2e;
    --sb-border:        rgba(255,255,255,.06);
    --sb-border-strong: rgba(255,255,255,.1);

    --sb-accent:        #6366f1;
    --sb-accent-2:      #8b5cf6;
    --sb-accent-glow:   rgba(99,102,241,.35);
    --sb-accent-bg:     rgba(99,102,241,.1);
    --sb-accent-bg2:    rgba(99,102,241,.18);

    --sb-success:       #10b981;
    --sb-success-bg:    rgba(16,185,129,.1);

    --sb-text:          #c8d0e0;
    --sb-text-bright:   #e8ecf4;
    --sb-muted:         #3d4d6a;
    --sb-muted-2:       #566480;

    --sb-hover-bg:      rgba(255,255,255,.04);
    --sb-active-bg:     linear-gradient(90deg, rgba(99,102,241,.18) 0%, rgba(139,92,246,.08) 100%);

    --sb-width:         268px;
    --sb-radius:        12px;
    --sb-transition:    .22s cubic-bezier(.4,0,.2,1);

    --sb-scrollbar-w:   4px;
}

/* ================================================================
   ASIDE CONTAINER — scrollable
   ================================================================ */
.dk-sidebar {
    width: var(--sb-width);
    height: 100vh;
    background: var(--sb-bg);
    border-right: 1px solid var(--sb-border);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; left: 0;
    z-index: 1050;

    /* ★ Scroll activé */
    overflow-y: auto;
    overflow-x: hidden;
    overscroll-behavior: contain;

    /* Scrollbar stylée */
    scrollbar-width: thin;
    scrollbar-color: rgba(99,102,241,.3) transparent;

    /* Fond lumineux subtil */
    background-image:
        radial-gradient(ellipse 60% 40% at 50% 0%, rgba(99,102,241,.07) 0%, transparent 70%),
        radial-gradient(ellipse 40% 30% at 80% 100%, rgba(139,92,246,.05) 0%, transparent 60%);
}
.dk-sidebar::-webkit-scrollbar { width: var(--sb-scrollbar-w); }
.dk-sidebar::-webkit-scrollbar-track { background: transparent; }
.dk-sidebar::-webkit-scrollbar-thumb {
    background: rgba(99,102,241,.3);
    border-radius: 99px;
}
.dk-sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(99,102,241,.55);
}

/* ================================================================
   BRAND / LOGO
   ================================================================ */
.dk-sidebar-brand {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: 1.3rem 1.1rem 1.1rem;
    border-bottom: 1px solid var(--sb-border);
    flex-shrink: 0;
    position: sticky;
    top: 0;
    background: var(--sb-bg);
    z-index: 10;
    backdrop-filter: blur(12px);
}
.brand-icon {
    width: 38px; height: 38px;
    background: var(--sb-accent-bg);
    border: 1px solid rgba(99,102,241,.3);
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    color: var(--sb-accent);
    font-size: .9rem;
    flex-shrink: 0;
    box-shadow: 0 0 16px rgba(99,102,241,.2);
    transition: box-shadow var(--sb-transition);
}
.dk-sidebar-brand:hover .brand-icon {
    box-shadow: 0 0 24px rgba(99,102,241,.4);
}
.brand-text { line-height: 1.1; flex: 1; }
.brand-name  { font-size: .97rem; font-weight: 800; color: var(--sb-text-bright); letter-spacing: -.01em; }
.brand-accent{ font-size: .97rem; font-weight: 800;
    background: linear-gradient(135deg, var(--sb-accent), var(--sb-accent-2));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Close btn (mobile) */
.sidebar-close-btn {
    display: none;
    width: 28px; height: 28px;
    background: var(--sb-hover-bg);
    border: 1px solid var(--sb-border-strong);
    border-radius: 8px;
    color: var(--sb-muted-2);
    cursor: pointer;
    align-items: center; justify-content: center;
    font-size: .75rem;
    transition: all var(--sb-transition);
    flex-shrink: 0;
}
.sidebar-close-btn:hover { color: var(--sb-text); background: rgba(255,255,255,.08); }

/* ================================================================
   USER PILL
   ================================================================ */
.dk-user-pill {
    display: flex;
    align-items: center;
    gap: .75rem;
    margin: .85rem .9rem .5rem;
    padding: .7rem .85rem;
    background: var(--sb-surface);
    border: 1px solid var(--sb-border-strong);
    border-radius: var(--sb-radius);
    flex-shrink: 0;
    transition: border-color var(--sb-transition);
}
.dk-user-pill:hover { border-color: rgba(99,102,241,.3); }

.dk-avatar-lg {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--sb-accent), var(--sb-accent-2));
    display: flex; align-items: center; justify-content: center;
    font-size: .73rem;
    font-weight: 800;
    color: #fff;
    flex-shrink: 0;
    letter-spacing: .02em;
    box-shadow: 0 4px 12px rgba(99,102,241,.35);
}
.dk-user-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
    gap: .15rem;
}
.dk-user-name {
    font-size: .8rem;
    font-weight: 700;
    color: var(--sb-text-bright);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    letter-spacing: -.01em;
}
.dk-user-role {
    display: flex;
    align-items: center;
    gap: .4rem;
    font-size: .7rem;
    color: var(--sb-muted-2);
}
.role-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--sb-success);
    flex-shrink: 0;
    box-shadow: 0 0 6px rgba(16,185,129,.6);
    animation: pulseDot 2.5s ease-in-out infinite;
}
@keyframes pulseDot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: .6; transform: scale(.8); }
}

/* ================================================================
   NAV
   ================================================================ */
.dk-nav {
    flex: 1;
    padding: .5rem .75rem .75rem;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.dk-nav-spacer { height: .5rem; }

/* Section label */
.dk-nav-section {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: 1rem .5rem .35rem;
    margin-top: .15rem;
}
.dk-nav-section span {
    font-size: .6rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: var(--sb-muted);
}
.dk-nav-section::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--sb-border);
}

/* Nav link principal */
.dk-nav-link {
    display: flex;
    align-items: center;
    gap: .65rem;
    padding: .62rem .8rem;
    border-radius: 9px;
    color: var(--sb-text);
    text-decoration: none;
    font-size: .82rem;
    font-weight: 500;
    background: transparent;
    border: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    transition: background var(--sb-transition), color var(--sb-transition), transform var(--sb-transition);
    position: relative;
    letter-spacing: -.005em;
}
.dk-nav-link:hover {
    background: var(--sb-hover-bg);
    color: var(--sb-text-bright);
    text-decoration: none;
    transform: translateX(2px);
}
.dk-nav-link.active {
    background: var(--sb-active-bg);
    color: var(--sb-text-bright);
    font-weight: 600;
    border: 1px solid rgba(99,102,241,.15);
}
.dk-nav-link.active .dk-nav-icon {
    color: var(--sb-accent);
}
/* Barre gauche active */
.dk-nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 20%;
    bottom: 20%;
    width: 3px;
    background: linear-gradient(180deg, var(--sb-accent), var(--sb-accent-2));
    border-radius: 0 4px 4px 0;
}

/* Icône */
.dk-nav-icon {
    width: 20px; height: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: .82rem;
    color: var(--sb-muted-2);
    flex-shrink: 0;
    transition: color var(--sb-transition);
}
.dk-nav-link:hover .dk-nav-icon { color: var(--sb-text); }

/* Label */
.dk-nav-label { flex: 1; }

/* Badge PP */
.dk-badge {
    font-size: .58rem;
    font-weight: 800;
    padding: .18rem .5rem;
    border-radius: 20px;
    background: var(--sb-success-bg);
    border: 1px solid rgba(16,185,129,.2);
    color: var(--sb-success);
    letter-spacing: .06em;
    flex-shrink: 0;
}

/* Active indicator (dot) */
.active-indicator {
    width: 6px; height: 6px;
    background: var(--sb-accent);
    border-radius: 50%;
    box-shadow: 0 0 8px var(--sb-accent-glow);
    flex-shrink: 0;
}

/* Chevron collapsible */
.dk-chevron {
    font-size: .6rem;
    color: var(--sb-muted);
    transition: transform var(--sb-transition);
    flex-shrink: 0;
}
.dk-collapsible.open .dk-chevron { transform: rotate(90deg); }

/* ================================================================
   SOUS-MENU
   ================================================================ */
.dk-sub-menu {
    display: none;
    flex-direction: column;
    gap: 1px;
    padding: .3rem 0 .3rem .4rem;
    margin-left: .9rem;
    border-left: 1px solid var(--sb-border-strong);
    overflow: hidden;
}
.dk-sub-menu.open {
    display: flex;
    animation: subMenuIn .18s ease both;
}
@keyframes subMenuIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.dk-sub-link {
    display: flex;
    align-items: center;
    gap: .55rem;
    padding: .5rem .7rem;
    border-radius: 7px;
    color: color-mix(in srgb, var(--sb-text) 65%, transparent);
    text-decoration: none;
    font-size: .79rem;
    font-weight: 400;
    transition: background var(--sb-transition), color var(--sb-transition), transform var(--sb-transition);
    letter-spacing: -.005em;
}
.dk-sub-link:hover {
    background: var(--sb-hover-bg);
    color: var(--sb-text-bright);
    text-decoration: none;
    transform: translateX(3px);
}
.dk-sub-link.active {
    color: var(--sb-accent);
    background: var(--sb-accent-bg);
    font-weight: 600;
}
.dk-sub-link i {
    font-size: .73rem;
    width: 14px;
    text-align: center;
    color: var(--sb-muted);
    flex-shrink: 0;
    transition: color var(--sb-transition);
}
.dk-sub-link:hover i { color: var(--sb-muted-2); }
.dk-sub-link.active i { color: var(--sb-accent); }

/* ================================================================
   FOOTER
   ================================================================ */
.dk-sidebar-footer {
    border-top: 1px solid var(--sb-border);
    padding: .85rem .9rem;
    flex-shrink: 0;
    position: sticky;
    bottom: 0;
    background: var(--sb-bg);
    backdrop-filter: blur(12px);
}

.dk-logout-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .6rem;
    width: 100%;
    padding: .65rem;
    border-radius: 9px;
    color: var(--sb-muted-2);
    font-size: .8rem;
    font-weight: 600;
    text-decoration: none;
    background: transparent;
    border: 1px solid var(--sb-border);
    transition: all var(--sb-transition);
    letter-spacing: .01em;
}
.dk-logout-btn:hover {
    color: #f87171;
    border-color: rgba(248,113,113,.3);
    background: rgba(248,113,113,.07);
    text-decoration: none;
    transform: translateY(-1px);
}
.dk-logout-btn i { font-size: .85rem; }

/* ================================================================
   OVERLAY (mobile)
   ================================================================ */
.dk-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.55);
    z-index: 1040;
    backdrop-filter: blur(2px);
    animation: fadeIn .2s ease;
}
.dk-overlay.active { display: block; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

/* ================================================================
   RESPONSIVE
   ================================================================ */
.main-content-offset {
    margin-left: var(--sb-width);
    transition: margin-left var(--sb-transition);
}

@media (max-width: 992px) {
    .dk-sidebar {
        transform: translateX(-100%);
        transition: transform var(--sb-transition), box-shadow var(--sb-transition);
        box-shadow: none;
    }
    .dk-sidebar.open {
        transform: translateX(0);
        box-shadow: 20px 0 60px rgba(0,0,0,.5);
    }
    .main-content-offset { margin-left: 0; }
    .sidebar-close-btn { display: flex; }
}
</style>

<script>
(function () {
    // ── Collapsible sous-menus ─────────────────────────────────
    document.querySelectorAll('.dk-collapsible').forEach(btn => {
        const targetId = btn.dataset.target;
        const sub      = document.getElementById(targetId);
        if (!sub) return;

        if (btn.classList.contains('open')) sub.classList.add('open');

        btn.addEventListener('click', () => {
            const isOpen = sub.classList.contains('open');
            document.querySelectorAll('.dk-sub-menu.open').forEach(s => s.classList.remove('open'));
            document.querySelectorAll('.dk-collapsible.open').forEach(b => b.classList.remove('open'));
            if (!isOpen) {
                sub.classList.add('open');
                btn.classList.add('open');
            }
        });
    });

    // ── Marquer le lien actif ──────────────────────────────────
    (function markActive() {
        const path = window.location.pathname;
        document.querySelectorAll('.dk-sub-link, .dk-nav-link:not(.dk-collapsible)').forEach(link => {
            if (link.href && link.href !== '#') {
                try {
                    if (path.startsWith(new URL(link.href, location.origin).pathname)) {
                        link.classList.add('active');
                    }
                } catch(e) {}
            }
        });
    })();

    // ── Mobile toggle ──────────────────────────────────────────
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const closeBtn = document.getElementById('sidebarCloseBtn');
    const toggleBtn = document.querySelector('.toggle-sidebar-btn');

    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('active'); }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); }

    if (toggleBtn) toggleBtn.addEventListener('click', () => {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    if (closeBtn)  closeBtn.addEventListener('click', closeSidebar);
    if (overlay)   overlay.addEventListener('click', closeSidebar);

    // ── Scroll to active link ──────────────────────────────────
    const activeLink = sidebar.querySelector('.dk-nav-link.active, .dk-sub-link.active');
    if (activeLink) {
        setTimeout(() => {
            activeLink.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }, 300);
    }
})();
</script>
