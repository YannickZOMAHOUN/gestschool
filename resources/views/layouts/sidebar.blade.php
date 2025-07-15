<!-- ======= Sidebar Élégant ======= -->
<aside id="sidebar" class="sidebar elegant-sidebar">
    <div class="sidebar-header text-center py-4">
        <h3 class="sidebar-brand">
            <i class="fas fa-graduation-cap me-2"></i>
            <span class="fw-bold">School</span>Manager
        </h3>
    </div>

    <ul class="sidebar-nav" id="sidebar-nav">
        <!-- Accueil -->
        <li class="nav-item">
            <a class="nav-link text-decoration-none" href="{{ route('home') }}">
                <div class="sidebar-icon">
                    <i class="fas fa-home"></i>
                </div>
                <span class="sidebar-title">Accueil</span>
                <span class="sidebar-hint">Tableau de bord</span>
            </a>
        </li>

        <!-- Divider -->
        <li class="sidebar-divider">
            <span>Gestion Pédagogique</span>
        </li>

        <!-- Les Notes -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#notes-nav" data-bs-toggle="collapse" href="#">
                <div class="sidebar-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <span class="sidebar-title">Gestion des Notes</span>
                <i class="bi bi-chevron-down dropdown-indicator"></i>
            </a>
            <ul id="notes-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="#">
                        <i class="fas fa-circle-notch"></i>
                        <span>Consulter les notes</span>
                    </a>
                </li>
                <li>
                    <a href="">
                        <i class="fas fa-circle-notch"></i>
                        <span>Saisir une note</span>
                    </a>
                </li>
                <li>
                    <a href="">
                        <i class="fas fa-circle-notch"></i>
                        <span>Exporter les notes</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Elèves -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#students-nav" data-bs-toggle="collapse" href="#">
                <div class="sidebar-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <span class="sidebar-title">Gestion des Élèves</span>
                <i class="bi bi-chevron-down dropdown-indicator"></i>
            </a>
            <ul id="students-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="">
                        <i class="fas fa-circle-notch"></i>
                        <span>Liste des élèves</span>
                    </a>
                </li>
                <li>
                    <a href="">
                        <i class="fas fa-circle-notch"></i>
                        <span>Inscrire un élève</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Divider -->
        <li class="sidebar-divider">
            <span>Configuration</span>
        </li>

        <!-- Paramètres -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#settings-nav" data-bs-toggle="collapse" href="#">
                <div class="sidebar-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <span class="sidebar-title">Paramètres</span>
                <i class="bi bi-chevron-down dropdown-indicator"></i>
            </a>
            <ul id="settings-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('year.create') }}">
                        <i class="fas fa-circle-notch"></i>
                        <span>Année scolaire</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('sectorbyyear.create') }}">
                        <i class="fas fa-circle-notch"></i>
                        <span>Filières</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('promotionbysector.create') }}">
                        <i class="fas fa-circle-notch"></i>
                        <span>Promotions</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('promotion-classrooms.create') }}">
                        <i class="fas fa-circle-notch"></i>
                        <span>Classes</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('subject.create') }}">
                        <i class="fas fa-circle-notch"></i>
                        <span>Matières</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('ratio.create') }}">
                        <i class="fas fa-circle-notch"></i>
                        <span>Coefficients</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Divider -->
        <li class="sidebar-divider">
            <span>Session</span>
        </li>

        <!-- Déconnexion -->
        <li class="nav-item logout-item">
            <a class="nav-link text-decoration-none" href="{{ route('logout') }}">
                <div class="sidebar-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <span class="sidebar-title">Déconnexion</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="user-info">
                <span class="user-name">{{ auth()->user()->surname . ' ' . auth()->user()->name }}</span>
                <span class="user-role">Administrateur</span>
            </div>
        </div>
    </div>
</aside>

<style>
/* Styles pour la sidebar élégante */
.elegant-sidebar {
    background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
    color: #fff;
    width: 280px;
    transition: all 0.3s;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 1rem;
}

.sidebar-brand {
    color: #fff;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-link {
    color: rgba(255, 255, 255, 0.8);
    padding: 12px 20px;
    margin: 2px 0;
    border-radius: 5px;
    display: flex;
    align-items: center;
    transition: all 0.3s;
}

.nav-link:hover, .nav-link:focus {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
}

.nav-link.active {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
}

.sidebar-icon {
    width: 24px;
    text-align: center;
    margin-right: 12px;
    font-size: 1.1rem;
}

.sidebar-title {
    flex-grow: 1;
    font-weight: 500;
}

.sidebar-hint {
    font-size: 0.75rem;
    opacity: 0.7;
    margin-left: 10px;
}

.dropdown-indicator {
    transition: transform 0.3s;
}

.nav-link.collapsed .dropdown-indicator {
    transform: rotate(-90deg);
}

.nav-content {
    padding-left: 20px;
}

.nav-content li a {
    padding: 8px 15px;
    color: rgba(255, 255, 255, 0.7);
    display: flex;
    align-items: center;
}

.nav-content li a:hover {
    color: #fff;
}

.nav-content li a i {
    font-size: 0.6rem;
    margin-right: 10px;
}

.sidebar-divider {
    padding: 10px 20px;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(255, 255, 255, 0.5);
    margin-top: 15px;
}

.logout-item {
    margin-top: 10px;
}

.logout-item .nav-link {
    color: rgba(255, 255, 255, 0.7);
}

.logout-item .nav-link:hover {
    color: #ff6b6b;
    background: rgba(255, 107, 107, 0.1);
}

.sidebar-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding: 15px;
    margin-top: auto;
}

.user-profile {
    display: flex;
    align-items: center;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    font-size: 1.5rem;
}

.user-info {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 500;
    font-size: 0.9rem;
}

.user-role {
    font-size: 0.75rem;
    opacity: 0.7;
}
</style>
