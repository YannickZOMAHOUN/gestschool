<!-- ======= Enhanced Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center shadow-sm" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">

    <!-- Logo Section with Elegant Spacing -->
    <div class="d-flex align-items-center ms-3">
        <div class="d-flex align-items-center pe-lg-4 me-lg-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-header" style="max-height: 65px; transition: all 0.3s ease;">
        </div>
        <i class="bi bi-list toggle-sidebar-btn text-color-avt fs-5" style="cursor: pointer;"></i>
    </div>

    <!-- Application Name with Stylish Typography -->
    <div class="mx-auto text-center">
        <h3 class="fs-5 text-uppercase font-semibold text-color-avt mb-0 letter-spacing-1" style="letter-spacing: 1.5px;">
            GESTION DES BULLETINS
        </h3>
    </div>

    <!-- Enhanced Profile Navigation -->
    <nav class="header-nav ms-auto me-4">
        <ul class="d-flex align-items-center" style="gap: 1.5rem;">
            @auth
                <li class="nav-item dropdown">
                    <!-- Profile Dropdown with Hover Effects -->
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownMenuLink" style="transition: all 0.3s ease;">
                        <span class="pe-1 pe-lg-0">
                            <i class="fas fa-user-circle text-color-avt fs-4"></i>
                        </span>
                        <span class="d-none d-md-block dropdown-toggle ps-2 text-color-avt font-medium" style="font-weight: 500;">
                            {{ auth()->user()->surname . ' ' . auth()->user()->name }}
                        </span>
                    </a>

                    <!-- Enhanced Dropdown Menu with Subtle Animation -->
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile animate__animated animate__fadeIn" aria-labelledby="dropdownMenuLink" style="border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                        <!-- Profile Info with Better Spacing -->
                        <!--<li class="dropdown-header py-2">
                            <h6 class="fw-semibold mb-0">{{ auth()->user()->surname . ' ' . auth()->user()->name }}</h6>
                            <small class="text-muted">{{ auth()->user()->role->name ?? 'Utilisateur' }}</small>
                        </li>-->

                        <li><hr class="dropdown-divider my-1"></li>

                        <!-- Profile Link with Hover Effect -->
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="#" style="transition: all 0.2s;">
                                <i class="fas fa-user me-2"></i>
                                <span>Mon Profil</span>
                            </a>
                        </li>

                        <!-- Settings Link (Added new item) -->
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="#" style="transition: all 0.2s;">
                                <i class="fas fa-cog me-2"></i>
                                <span>Paramètres</span>
                            </a>
                        </li>

                        <li><hr class="dropdown-divider my-1"></li>

                        <!-- Logout Link with Warning Color -->
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2 text-danger" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="transition: all 0.2s;">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                <span>Déconnexion</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
            @endauth

            <!-- Notification Bell (Added new feature) -->
            <li class="nav-item">
                <a class="nav-link" href="#" style="position: relative;">
                    <i class="fas fa-bell text-color-avt fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6em;">
                        3
                    </span>
                </a>
            </li>
        </ul>
    </nav>
</header><!-- End Header -->
