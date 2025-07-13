<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

        <!-- Accueil -->
        <li class="nav-item">
            <a class="nav-link text-decoration-none" href="{{ route('home') }}">
                <i class="fas fa-home"></i>
                <span>&nbsp; Accueil</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed " data-bs-target="#settings-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-filter-right"></i><span>&nbsp; Parcelles</span><i
                    class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="settings-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li class="pt-2">
                    <a href="{{ route('year.create') }}" class="text-decoration-none">
                        <i class="fas fa-plus"></i><span>&nbsp; Année Scolaire</span>
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</aside><!-- End Sidebar -->
