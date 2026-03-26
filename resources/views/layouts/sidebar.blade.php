{{-- ======================================================
     SIDEBAR — Clean Sage
     HTML uniquement. CSS et JS dans template.blade.php.
     ====================================================== --}}

@php
    $user      = auth()->user();
    $isTeacher = $user && method_exists($user, 'isEnseignant') ? $user->isEnseignant() : false;
    $isPP      = $isTeacher && method_exists($user, 'principalClasses') ? $user->principalClasses()->exists() : false;
    $isAdmin   = $user ? !$isTeacher : false;
@endphp

<aside id="lk-sidebar" class="lk-sidebar" aria-label="Menu latéral">

    {{-- ── Brand ─────────────────────────────────────────────── --}}
    <div class="lk-brand">
        <div class="lk-brand-icon" aria-hidden="true">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="lk-brand-text">
            <span class="lk-brand-name">
                School<span class="lk-brand-accent">Manager</span>
            </span>
            <span class="lk-brand-sub">Lycée Tech. Bohicon</span>
        </div>
        {{-- Réduire (desktop) --}}
        <button class="lk-ico-btn lk-collapse-btn"
                id="lkSidebarCollapseBtn"
                type="button"
                aria-label="Réduire le menu">
            <i class="fas fa-angle-double-left"></i>
        </button>
        {{-- Fermer (mobile) --}}
        <button class="lk-ico-btn lk-close-btn"
                id="lkSidebarCloseBtn"
                type="button"
                aria-label="Fermer le menu">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- ── User pill ───────────────────────────────────────────── --}}
    @if($user)
    <div class="lk-user-pill">
        <div class="lk-avatar" aria-hidden="true">
            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr($user->surname, 0, 1)) }}
        </div>
        <div class="lk-user-info">
            <span class="lk-user-name">{{ $user->surname }} {{ $user->name }}</span>
            <span class="lk-user-role">
                <span class="lk-role-dot" aria-hidden="true"></span>
                {{ $user->roles->first()->name ?? '—' }}
            </span>
        </div>
    </div>
    @endif

    {{-- ── Navigation ─────────────────────────────────────────── --}}
    <nav class="lk-nav" id="lkSidebarNav" aria-label="Navigation principale">

        {{-- Tableau de bord (admin seulement) --}}
        @if($isAdmin)
            <a class="lk-nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
               href="{{ route('home') }}"
               title="Tableau de bord">
                <span class="lk-nav-icon"><i class="fas fa-home"></i></span>
                <span class="lk-nav-label">Tableau de bord</span>
            </a>
        @endif

        {{-- ── Notes ── --}}
        <div class="lk-nav-section"><span>Notes</span></div>

        @if($isTeacher)
            {{-- Vue enseignant --}}
            <a class="lk-nav-link {{ request()->routeIs('note.create') ? 'active' : '' }}"
               href="{{ route('note.create') }}"
               title="Saisir les notes">
                <span class="lk-nav-icon"><i class="fas fa-pen-alt"></i></span>
                <span class="lk-nav-label">Saisir les notes</span>
            </a>

            @if($isPP)
                <a class="lk-nav-link {{ request()->routeIs('note.index') ? 'active' : '' }}"
                   href="{{ route('note.index') }}"
                   title="Consulter les notes">
                    <span class="lk-nav-icon"><i class="fas fa-table"></i></span>
                    <span class="lk-nav-label">Consulter les notes</span>
                </a>
                <a class="lk-nav-link {{ request()->routeIs('get.cards') ? 'active' : '' }}"
                   href="{{ route('get.cards') }}"
                   title="Bulletins">
                    <span class="lk-nav-icon"><i class="fas fa-file-alt"></i></span>
                    <span class="lk-nav-label">Bulletins</span>
                    <span class="lk-badge">PP</span>
                </a>
            @endif

        @else
            {{-- Vue admin --}}
            <a class="lk-nav-link {{ request()->routeIs('note.index') ? 'active' : '' }}"
               href="{{ route('note.index') }}"
               title="Consulter les notes">
                <span class="lk-nav-icon"><i class="fas fa-table"></i></span>
                <span class="lk-nav-label">Consulter les notes</span>
            </a>
            <a class="lk-nav-link {{ request()->routeIs('note.create') ? 'active' : '' }}"
               href="{{ route('note.create') }}"
               title="Saisir une note">
                <span class="lk-nav-icon"><i class="fas fa-pen-alt"></i></span>
                <span class="lk-nav-label">Saisir une note</span>
            </a>
            <a class="lk-nav-link {{ request()->routeIs('notes.import') ? 'active' : '' }}"
               href="{{ route('notes.import') }}"
               title="Importer les notes">
                <span class="lk-nav-icon"><i class="fas fa-file-excel"></i></span>
                <span class="lk-nav-label">Importer les notes</span>
            </a>
            <a class="lk-nav-link {{ request()->routeIs('export_view') ? 'active' : '' }}"
               href="{{ route('export_view') }}"
               title="Exporter les notes">
                <span class="lk-nav-icon"><i class="fas fa-file-download"></i></span>
                <span class="lk-nav-label">Exporter les notes</span>
            </a>
            <a class="lk-nav-link {{ request()->routeIs('get.cards') ? 'active' : '' }}"
               href="{{ route('get.cards') }}"
               title="Bulletins">
                <span class="lk-nav-icon"><i class="fas fa-file-alt"></i></span>
                <span class="lk-nav-label">Bulletins</span>
            </a>
        @endif

        {{-- ── Sections admin uniquement ── --}}
        @if($isAdmin)

            {{-- Utilisateurs --}}
            <div class="lk-nav-section"><span>Utilisateurs</span></div>

            <button class="lk-nav-link lk-collapsible
                           {{ request()->routeIs('user.*','teacher-assignments.*','principal-teachers.*') ? 'active open' : '' }}"
                    type="button"
                    data-target="lkSubUsers"
                    aria-expanded="{{ request()->routeIs('user.*','teacher-assignments.*','principal-teachers.*') ? 'true' : 'false' }}">
                <span class="lk-nav-icon"><i class="fas fa-users"></i></span>
                <span class="lk-nav-label">Utilisateurs</span>
                <i class="fas fa-chevron-right lk-chevron"></i>
            </button>

            <div class="lk-sub-menu
                        {{ request()->routeIs('user.*','teacher-assignments.*','principal-teachers.*') ? 'open' : '' }}"
                 id="lkSubUsers">
                <a class="lk-sub-link {{ request()->routeIs('user.create') ? 'active' : '' }}"
                   href="{{ route('user.create') }}">
                    <i class="fas fa-user-plus"></i> Nouvel utilisateur
                </a>
                <a class="lk-sub-link {{ request()->routeIs('user.index') ? 'active' : '' }}"
                   href="{{ route('user.index') }}">
                    <i class="fas fa-list"></i> Liste des utilisateurs
                </a>
                <a class="lk-sub-link {{ request()->routeIs('teacher-assignments.*') ? 'active' : '' }}"
                   href="{{ route('teacher-assignments.index') }}">
                    <i class="fas fa-chalkboard-teacher"></i> Enseignants par classe
                </a>
                <a class="lk-sub-link {{ request()->routeIs('principal-teachers.*') ? 'active' : '' }}"
                   href="{{ route('principal-teachers.index') }}">
                    <i class="fas fa-star"></i> Professeurs Principaux
                </a>
            </div>

            {{-- Élèves --}}
            <div class="lk-nav-section"><span>Élèves</span></div>

            <button class="lk-nav-link lk-collapsible
                           {{ request()->routeIs('student.*') ? 'active open' : '' }}"
                    type="button"
                    data-target="lkSubStudents"
                    aria-expanded="{{ request()->routeIs('student.*') ? 'true' : 'false' }}">
                <span class="lk-nav-icon"><i class="fas fa-user-graduate"></i></span>
                <span class="lk-nav-label">Élèves</span>
                <i class="fas fa-chevron-right lk-chevron"></i>
            </button>

            <div class="lk-sub-menu {{ request()->routeIs('student.*') ? 'open' : '' }}"
                 id="lkSubStudents">
                <a class="lk-sub-link {{ request()->routeIs('student.index') ? 'active' : '' }}"
                   href="{{ route('student.index') }}">
                    <i class="fas fa-list"></i> Liste des élèves
                </a>
                <a class="lk-sub-link {{ request()->routeIs('student.create') ? 'active' : '' }}"
                   href="{{ route('student.create') }}">
                    <i class="fas fa-user-plus"></i> Inscrire un élève
                </a>
            </div>

            {{-- Configuration --}}
            <div class="lk-nav-section"><span>Configuration</span></div>

            <button class="lk-nav-link lk-collapsible
                           {{ request()->routeIs('year.*','sector.*','sectorbyyear.*','promotionbysector.*','promotion-classrooms.*','subject.*','ratio.*') ? 'active open' : '' }}"
                    type="button"
                    data-target="lkSubSettings"
                    aria-expanded="{{ request()->routeIs('year.*','sector.*','sectorbyyear.*','promotionbysector.*','promotion-classrooms.*','subject.*','ratio.*') ? 'true' : 'false' }}">
                <span class="lk-nav-icon"><i class="fas fa-cogs"></i></span>
                <span class="lk-nav-label">Paramètres</span>
                <i class="fas fa-chevron-right lk-chevron"></i>
            </button>

            <div class="lk-sub-menu
                        {{ request()->routeIs('year.*','sector.*','sectorbyyear.*','promotionbysector.*','promotion-classrooms.*','subject.*','ratio.*') ? 'open' : '' }}"
                 id="lkSubSettings">
                <a class="lk-sub-link {{ request()->routeIs('year.create') ? 'active' : '' }}"
                   href="{{ route('year.create') }}">
                    <i class="fas fa-calendar-alt"></i> Année scolaire
                </a>
                <a class="lk-sub-link {{ request()->routeIs('sectorbyyear.create') ? 'active' : '' }}"
                   href="{{ route('sectorbyyear.create') }}">
                    <i class="fas fa-sitemap"></i> Filières
                </a>
                <a class="lk-sub-link {{ request()->routeIs('promotionbysector.create') ? 'active' : '' }}"
                   href="{{ route('promotionbysector.create') }}">
                    <i class="fas fa-layer-group"></i> Promotions
                </a>
                <a class="lk-sub-link {{ request()->routeIs('promotion-classrooms.create') ? 'active' : '' }}"
                   href="{{ route('promotion-classrooms.create') }}">
                    <i class="fas fa-door-open"></i> Classes
                </a>
                <a class="lk-sub-link {{ request()->routeIs('subject.create') ? 'active' : '' }}"
                   href="{{ route('subject.create') }}">
                    <i class="fas fa-book"></i> Matières
                </a>
                <a class="lk-sub-link {{ request()->routeIs('ratio.create') ? 'active' : '' }}"
                   href="{{ route('ratio.create') }}">
                    <i class="fas fa-balance-scale"></i> Coefficients
                </a>
            </div>

        @endif

    </nav>

    {{-- ── Footer / Logout ────────────────────────────────────── --}}
    <div class="lk-sidebar-footer">
        <a href="{{ route('logout') }}"
           class="lk-logout-btn"
           onclick="event.preventDefault(); document.getElementById('lk-logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
        </a>
        <form id="lk-logout-form"
              action="{{ route('logout') }}"
              method="POST"
              class="d-none">
            @csrf
        </form>
    </div>

</aside>

{{-- Overlay mobile --}}
<div class="lk-overlay" id="lkSidebarOverlay" aria-hidden="true"></div>
