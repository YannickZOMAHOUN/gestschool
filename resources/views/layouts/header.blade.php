<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

    <!-- Logo Section with More Space -->
    <div class="d-flex align-items-center ">
        <div class="d-flex align-items-center pe-lg-5"> <!-- Adjust padding for more space -->
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-header" style="max-height: 60px;"> <!-- Increase height if needed -->
        </div>
        <i class="bi bi-list toggle-sidebar-btn text-color-avt"></i>
    </div>
    <!-- End Logo -->

    <!-- Application Name -->
    <div class="mx-auto text-center">
        <h3 class="fs-14 text-uppercase font-medium text-color-avt mb-0">GESTION DES BULLETINS</h3>
    </div>

    <!-- Profile and Navigation Section -->
    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            @auth
                <li class="nav-item dropdown pe-3">
                    <!-- Profile Dropdown Toggle -->
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownMenuLink">
                        <span class="pe-1 pe-lg-0">
                            <i class="fas fa-user text-color-avt"></i>
                        </span>
                        <span class="d-none d-md-block dropdown-toggle ps-2 text-color-avt font-light">
                        </span>
                    </a>

                    <!-- Role and School -->
                    <div class="d-none d-lg-block">
                        <h6 class="mt-1 mb-0 text-muted">
                        </h6>
                    </div>

                    <!-- Dropdown Menu -->
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile" aria-labelledby="dropdownMenuLink">
                        <!-- Profile Info -->
                        <li class="dropdown-header">


                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <!-- Profile Link -->

                        

                        <li><hr class="dropdown-divider"></li>

                        <!-- Logout Link -->
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i>&nbsp; Déconnexion
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
            @endauth
        </ul>
    </nav>

    <!-- End Icons Navigation -->
</header><!-- End Header -->
