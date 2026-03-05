{{-- ======================================================
     HEADER — Premium UI 2.0
     ====================================================== --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<header id="main-header" class="dk-header">

    {{-- ── Gauche : Toggle + Logo --}}
    <div class="dk-header-left">
        <button class="dk-toggle-btn toggle-sidebar-btn" aria-label="Toggle sidebar">
            <span></span><span></span><span></span>
        </button>

        <div class="dk-header-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="dk-logo">
            <div class="dk-header-divider"></div>
            <span class="dk-header-title">Gestion des Bulletins</span>
        </div>
    </div>

    {{-- ── Centre : Breadcrumb / Titre de page (optionnel) --}}
    <div class="dk-header-center">
        <div class="dk-page-badge">
            <i class="fas fa-graduation-cap"></i>
            <span>SchoolManager</span>
        </div>
    </div>

    {{-- ── Droite : Actions --}}
    <div class="dk-header-right">

        {{-- Notifications --}}
        <div class="dk-action-btn" id="notifBtn" aria-label="Notifications">
            <i class="fas fa-bell"></i>
           <!-- <span class="dk-notif-dot">3</span>-->
        </div>

        {{-- Séparateur --}}
        <div class="dk-h-divider"></div>

        @auth
        {{-- Profile Dropdown --}}
        <div class="dk-profile-dropdown" id="profileDropdown">
            <button class="dk-profile-btn" id="profileBtn">
                <div class="dk-header-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->surname, 0, 1)) }}
                </div>
                <div class="dk-profile-text">
                    <span class="dk-profile-name">{{ auth()->user()->surname }} {{ auth()->user()->name }}</span>
                    <span class="dk-profile-role">{{ auth()->user()->roles->first()->name ?? 'Utilisateur' }}</span>
                </div>
                <i class="fas fa-chevron-down dk-profile-chevron"></i>
            </button>

            {{-- Dropdown menu --}}
            <div class="dk-dropdown-menu" id="profileMenu">
                <div class="dk-dropdown-header">
                    <div class="dk-dd-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->surname, 0, 1)) }}
                    </div>
                    <div>
                        <div class="dk-dd-name">{{ auth()->user()->surname }} {{ auth()->user()->name }}</div>
                        <div class="dk-dd-role">{{ auth()->user()->roles->first()->name ?? 'Utilisateur' }}</div>
                    </div>
                </div>
                <div class="dk-dropdown-divider"></div>

                <a href="#" class="dk-dropdown-item">
                    <i class="fas fa-user-circle"></i>
                    <span>Mon profil</span>
                </a>
                <a href="#" class="dk-dropdown-item">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
                <a href="#" class="dk-dropdown-item">
                    <i class="fas fa-question-circle"></i>
                    <span>Aide</span>
                </a>

                <div class="dk-dropdown-divider"></div>

                <a href="{{ route('logout') }}" class="dk-dropdown-item dk-dropdown-danger"
                   onclick="event.preventDefault(); document.getElementById('logout-form-hdr').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
                <form id="logout-form-hdr" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </div>
        @endauth
    </div>
</header>

<style>
/* ================================================================
   FONTS
   ================================================================ */
#main-header, #main-header * {
    font-family: 'Plus Jakarta Sans', sans-serif;
    box-sizing: border-box;
}

/* ================================================================
   VARIABLES
   ================================================================ */
:root {
    --hdr-bg:           rgba(8, 12, 20, 0.95);
    --hdr-border:       rgba(255,255,255,.07);
    --hdr-accent:       #6366f1;
    --hdr-accent-2:     #8b5cf6;
    --hdr-text:         #c8d0e0;
    --hdr-text-bright:  #e8ecf4;
    --hdr-muted:        #4a5570;
    --hdr-surface:      #0f1520;
    --hdr-surface-2:    #141c2e;
    --hdr-hover:        rgba(255,255,255,.05);
    --hdr-success:      #10b981;
    --hdr-danger:       #f87171;
    --hdr-height:       64px;
    --hdr-transition:   .2s cubic-bezier(.4,0,.2,1);
}

/* ================================================================
   HEADER BASE
   ================================================================ */
.dk-header {
    position: fixed;
    top: 0; left: 0; right: 0;
    height: var(--hdr-height);
    z-index: 1030;
    display: flex;
    align-items: center;
    padding: 0 1.25rem 0 calc(var(--sb-width, 268px) + 1rem);
    gap: 1rem;

    background: var(--hdr-bg);
    border-bottom: 1px solid var(--hdr-border);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);

    /* Subtle gradient line bottom */
    box-shadow:
        0 1px 0 rgba(99,102,241,.12),
        0 4px 24px rgba(0,0,0,.35);

    transition: padding var(--hdr-transition);
}

/* ================================================================
   LEFT SECTION
   ================================================================ */
.dk-header-left {
    display: flex;
    align-items: center;
    gap: .9rem;
    flex-shrink: 0;
}

/* Hamburger toggle */
.dk-toggle-btn {
    width: 36px; height: 36px;
    background: transparent;
    border: 1px solid var(--hdr-border);
    border-radius: 9px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 0;
    transition: background var(--hdr-transition), border-color var(--hdr-transition);
}
.dk-toggle-btn span {
    display: block;
    height: 2px;
    border-radius: 2px;
    background: var(--hdr-muted);
    transition: all var(--hdr-transition);
    transform-origin: center;
}
.dk-toggle-btn span:nth-child(1) { width: 16px; }
.dk-toggle-btn span:nth-child(2) { width: 20px; }
.dk-toggle-btn span:nth-child(3) { width: 12px; }
.dk-toggle-btn:hover {
    background: var(--hdr-hover);
    border-color: rgba(255,255,255,.12);
}
.dk-toggle-btn:hover span { background: var(--hdr-text); }

/* Sidebar ouverte → animation hamburger */
.sidebar-open .dk-toggle-btn span:nth-child(1) { transform: translateY(6px) rotate(45deg); width: 18px; }
.sidebar-open .dk-toggle-btn span:nth-child(2) { opacity: 0; transform: scaleX(0); }
.sidebar-open .dk-toggle-btn span:nth-child(3) { transform: translateY(-6px) rotate(-45deg); width: 18px; }

/* Logo + Titre */
.dk-header-brand {
    display: flex;
    align-items: center;
    gap: .8rem;
}
.dk-logo {
    max-height: 38px;
    width: auto;
    object-fit: contain;
    filter: brightness(1.05);
}
.dk-header-divider {
    width: 1px;
    height: 22px;
    background: var(--hdr-border);
}
.dk-header-title {
    font-size: .82rem;
    font-weight: 700;
    color: var(--hdr-text);
    text-transform: uppercase;
    letter-spacing: .08em;
    white-space: nowrap;
}

/* ================================================================
   CENTER SECTION
   ================================================================ */
.dk-header-center {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
}

.dk-page-badge {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .38rem .9rem;
    background: rgba(99,102,241,.08);
    border: 1px solid rgba(99,102,241,.18);
    border-radius: 20px;
    color: var(--hdr-accent);
    font-size: .74rem;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    transition: all var(--hdr-transition);
}
.dk-page-badge:hover {
    background: rgba(99,102,241,.14);
    border-color: rgba(99,102,241,.3);
}
.dk-page-badge i { font-size: .75rem; }

/* ================================================================
   RIGHT SECTION
   ================================================================ */
.dk-header-right {
    display: flex;
    align-items: center;
    gap: .6rem;
    flex-shrink: 0;
}

.dk-h-divider {
    width: 1px;
    height: 22px;
    background: var(--hdr-border);
}

/* Action buttons */
.dk-action-btn {
    width: 36px; height: 36px;
    background: transparent;
    border: 1px solid var(--hdr-border);
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    color: var(--hdr-muted);
    font-size: .85rem;
    cursor: pointer;
    position: relative;
    transition: all var(--hdr-transition);
}
.dk-action-btn:hover {
    background: var(--hdr-hover);
    color: var(--hdr-text);
    border-color: rgba(255,255,255,.12);
    transform: translateY(-1px);
}

/* Notification dot */
.dk-notif-dot {
    position: absolute;
    top: 5px; right: 5px;
    width: 16px; height: 16px;
    background: linear-gradient(135deg, #f87171, #ef4444);
    border-radius: 50%;
    font-size: .52rem;
    font-weight: 800;
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    border: 1.5px solid var(--hdr-bg);
    animation: pulseBadge 2s ease-in-out infinite;
    line-height: 1;
}
@keyframes pulseBadge {
    0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,.5); }
    50%       { box-shadow: 0 0 0 5px rgba(239,68,68,0); }
}

/* ================================================================
   PROFILE DROPDOWN
   ================================================================ */
.dk-profile-dropdown {
    position: relative;
}

.dk-profile-btn {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .38rem .65rem .38rem .45rem;
    background: var(--hdr-surface);
    border: 1px solid var(--hdr-border);
    border-radius: 10px;
    cursor: pointer;
    transition: all var(--hdr-transition);
}
.dk-profile-btn:hover {
    background: var(--hdr-surface-2);
    border-color: rgba(99,102,241,.25);
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(0,0,0,.2);
}

.dk-header-avatar {
    width: 30px; height: 30px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--hdr-accent), var(--hdr-accent-2));
    display: flex; align-items: center; justify-content: center;
    font-size: .68rem;
    font-weight: 800;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(99,102,241,.35);
}

.dk-profile-text {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: .05rem;
}
.dk-profile-name {
    font-size: .78rem;
    font-weight: 700;
    color: var(--hdr-text-bright);
    white-space: nowrap;
    letter-spacing: -.01em;
}
.dk-profile-role {
    font-size: .65rem;
    color: var(--hdr-muted);
    white-space: nowrap;
}

.dk-profile-chevron {
    font-size: .6rem;
    color: var(--hdr-muted);
    transition: transform var(--hdr-transition);
    flex-shrink: 0;
}
.dk-profile-dropdown.open .dk-profile-chevron { transform: rotate(180deg); }

/* Dropdown Menu */
.dk-dropdown-menu {
    position: absolute;
    top: calc(100% + .65rem);
    right: 0;
    min-width: 220px;
    background: var(--hdr-surface);
    border: 1px solid rgba(255,255,255,.09);
    border-radius: 14px;
    padding: .5rem;
    box-shadow: 0 20px 60px rgba(0,0,0,.5), 0 0 0 1px rgba(99,102,241,.08);
    z-index: 9999;

    display: none;
    transform-origin: top right;
    animation: dropIn .18s cubic-bezier(.4,0,.2,1) both;
}
.dk-dropdown-menu.open { display: block; }
@keyframes dropIn {
    from { opacity: 0; transform: scale(.94) translateY(-8px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

/* Dropdown header */
.dk-dropdown-header {
    display: flex;
    align-items: center;
    gap: .7rem;
    padding: .6rem .7rem .7rem;
}
.dk-dd-avatar {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--hdr-accent), var(--hdr-accent-2));
    display: flex; align-items: center; justify-content: center;
    font-size: .76rem;
    font-weight: 800;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(99,102,241,.35);
}
.dk-dd-name {
    font-size: .82rem;
    font-weight: 700;
    color: var(--hdr-text-bright);
    letter-spacing: -.01em;
}
.dk-dd-role {
    font-size: .7rem;
    color: var(--hdr-muted);
    margin-top: .1rem;
}

.dk-dropdown-divider {
    height: 1px;
    background: var(--hdr-border);
    margin: .35rem .2rem;
}

.dk-dropdown-item {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .55rem .75rem;
    border-radius: 8px;
    color: var(--hdr-text);
    font-size: .8rem;
    font-weight: 500;
    text-decoration: none;
    transition: all var(--hdr-transition);
    letter-spacing: -.005em;
}
.dk-dropdown-item:hover {
    background: var(--hdr-hover);
    color: var(--hdr-text-bright);
    text-decoration: none;
    transform: translateX(2px);
}
.dk-dropdown-item i {
    width: 16px;
    text-align: center;
    font-size: .8rem;
    color: var(--hdr-muted);
    flex-shrink: 0;
    transition: color var(--hdr-transition);
}
.dk-dropdown-item:hover i { color: var(--hdr-text); }

.dk-dropdown-danger { color: var(--hdr-danger) !important; }
.dk-dropdown-danger:hover {
    background: rgba(248,113,113,.07) !important;
    color: var(--hdr-danger) !important;
}
.dk-dropdown-danger i { color: var(--hdr-danger) !important; }

/* ================================================================
   RESPONSIVE
   ================================================================ */
@media (max-width: 992px) {
    .dk-header {
        padding: 0 1rem;
    }
    .dk-header-brand .dk-header-divider,
    .dk-header-brand .dk-header-title { display: none; }
    .dk-page-badge span { display: none; }
    .dk-page-badge { padding: .38rem .55rem; }
    .dk-profile-text { display: none; }
    .dk-profile-chevron { display: none; }
}

@media (max-width: 576px) {
    .dk-page-badge { display: none; }
    .dk-header-center { display: none; }
}
</style>

<script>
(function () {
    // ── Profile dropdown ───────────────────────────────────────
    const profileDropdown = document.getElementById('profileDropdown');
    const profileBtn      = document.getElementById('profileBtn');
    const profileMenu     = document.getElementById('profileMenu');

    if (profileBtn && profileMenu) {
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = profileDropdown.classList.toggle('open');
            profileMenu.classList.toggle('open', isOpen);
        });
        document.addEventListener('click', (e) => {
            if (!profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('open');
                profileMenu.classList.remove('open');
            }
        });
    }

    // ── Toggle sidebar (state CSS class) ──────────────────────
    const toggleBtn = document.querySelector('.dk-toggle-btn');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-open');
        });
    }
})();
</script>
