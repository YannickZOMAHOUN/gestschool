{{-- ======================================================
     HEADER — Clean Sage
     HTML uniquement. CSS et JS dans template.blade.php.
     ====================================================== --}}

<header class="lk-header" id="lkHeader" role="banner">

    {{-- ── Gauche ──────────────────────────────────────────────── --}}
    <div class="lk-hdr-left">

        {{-- Bouton hamburger (ouvre/ferme la sidebar) --}}
        <button class="lk-toggle-btn toggle-sidebar-btn"
                type="button"
                aria-label="Ouvrir ou fermer le menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        {{-- Fil d'Ariane --}}
        <div class="lk-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fas fa-graduation-cap" aria-hidden="true"></i>
            <span>@yield('breadcrumb', 'Tableau de bord')</span>
        </div>

    </div>

    {{-- ── Centre ───────────────────────────────────────────────── --}}
    <div class="lk-hdr-center">
        <div class="lk-app-badge" title="SchoolManager — Lycée Technique de Bohicon">
            <i class="fas fa-school" aria-hidden="true"></i>
            <span>SchoolManager</span>
        </div>
    </div>

    {{-- ── Droite ───────────────────────────────────────────────── --}}
    <div class="lk-hdr-right">

        {{-- Notifications --}}
        <button class="lk-action-btn"
                id="lkNotifBtn"
                type="button"
                aria-label="Notifications">
            <i class="fas fa-bell"></i>
        </button>

        <div class="lk-hdiv" aria-hidden="true"></div>

        {{-- Bascule thème light / dark --}}
        <button class="lk-action-btn"
                id="lkThemeBtn"
                type="button"
                aria-label="Changer le thème"
                title="Thème clair / sombre">
            <i class="fas fa-moon"></i>
        </button>

        {{-- Profil utilisateur --}}
        @auth
        <div class="lk-profile-wrap" id="lkProfileWrap">

            <button class="lk-profile-btn"
                    id="lkProfileBtn"
                    type="button"
                    aria-haspopup="menu"
                    aria-expanded="false"
                    aria-controls="lkProfileMenu">
                <div class="lk-hdr-avatar" aria-hidden="true">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->surname, 0, 1)) }}
                </div>
                <div class="lk-profile-text">
                    <span class="lk-profile-name">
                        {{ auth()->user()->surname }} {{ auth()->user()->name }}
                    </span>
                    <span class="lk-profile-role">
                        {{ auth()->user()->roles->first()->name ?? 'Utilisateur' }}
                    </span>
                </div>
                <i class="fas fa-chevron-down lk-chevron-hdr" aria-hidden="true"></i>
            </button>

            {{-- Dropdown --}}
            <div class="lk-dropdown"
                 id="lkProfileMenu"
                 role="menu"
                 aria-labelledby="lkProfileBtn">

                <div class="lk-dd-header">
                    <div class="lk-dd-avatar" aria-hidden="true">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->surname, 0, 1)) }}
                    </div>
                    <div>
                        <div class="lk-dd-name">
                            {{ auth()->user()->surname }} {{ auth()->user()->name }}
                        </div>
                        <div class="lk-dd-role">
                            {{ auth()->user()->roles->first()->name ?? 'Utilisateur' }}
                        </div>
                    </div>
                </div>

                <div class="lk-dd-divider" role="separator"></div>

                <a href="#" class="lk-dd-item" role="menuitem">
                    <i class="fas fa-user-circle" aria-hidden="true"></i>
                    Mon profil
                </a>
                <a href="#" class="lk-dd-item" role="menuitem">
                    <i class="fas fa-cog" aria-hidden="true"></i>
                    Paramètres
                </a>
                <a href="#" class="lk-dd-item" role="menuitem">
                    <i class="fas fa-question-circle" aria-hidden="true"></i>
                    Aide
                </a>

                <div class="lk-dd-divider" role="separator"></div>

                <a href="{{ route('logout') }}"
                   class="lk-dd-item lk-dd-danger"
                   role="menuitem"
                   onclick="event.preventDefault(); document.getElementById('lk-logout-form-hdr').submit();">
                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                    Déconnexion
                </a>
                <form id="lk-logout-form-hdr"
                      action="{{ route('logout') }}"
                      method="POST"
                      class="d-none">
                    @csrf
                </form>

            </div>{{-- /.lk-dropdown --}}
        </div>{{-- /.lk-profile-wrap --}}
        @endauth

    </div>{{-- /.lk-hdr-right --}}

</header>
