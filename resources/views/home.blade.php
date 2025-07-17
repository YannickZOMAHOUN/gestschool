@extends('layouts.template')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- En-tête avec bienvenue -->
    <div class="mb-5 text-center">
        <h2 class="text-gradient-primary display-5 fw-bold mb-3">Bienvenue sur le système de gestion scolaire</h2>
        <p class="lead text-muted">Accédez rapidement aux fonctionnalités principales grâce à ce tableau de bord intuitif</p>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-lg rounded-3 overflow-hidden bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-primary rounded p-3 me-3">
                            <i class="fas fa-calendar-check fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h2 class="text-primary mb-0">{{ \App\Models\Year::where('status', true)->first()?->year ?? '-' }}</h2>
                            <p class="text-muted mb-0">Année active</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-lg rounded-3 overflow-hidden bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-success rounded p-3 me-3">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                        <div>
                            <h2 class="text-success mb-0">{{ \App\Models\Student::count() }}</h2>
                            <p class="text-muted mb-0">Élèves enregistrés</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-lg rounded-3 overflow-hidden bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-info rounded p-3 me-3">
                            <i class="fas fa-project-diagram fa-2x text-info"></i>
                        </div>
                        <div>
                            <h2 class="text-info mb-0">{{ \App\Models\Sector::count() }}</h2>
                            <p class="text-muted mb-0">Filières</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-lg rounded-3 overflow-hidden bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-warning rounded p-3 me-3">
                            <i class="fas fa-chalkboard-teacher fa-2x text-warning"></i>
                        </div>
                        <div>
                            <h2 class="text-warning mb-0">{{ \App\Models\PromotionClassroom::count() }}</h2>
                            <p class="text-muted mb-0">Classes créées</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-lg rounded-3 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-rocket me-2 text-primary"></i> Accès rapide</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush rounded-3">
                        <a href="{{ route('student.create') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3 px-4 border-0">
                            <div class="icon-shape bg-light-primary text-primary rounded-circle p-2 me-3">
                                <i class="fas fa-file-import"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Importer les élèves</h6>
                                <p class="small text-muted mb-0">Importez de nouveaux élèves dans le système</p>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-muted"></i>
                        </a>
                        <a href="{{ route('student.index') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3 px-4 border-0">
                            <div class="icon-shape bg-light-success text-success rounded-circle p-2 me-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Voir la liste des élèves</h6>
                                <p class="small text-muted mb-0">Consultez et gérez tous les élèves</p>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-muted"></i>
                        </a>
                        <a href="{{ route('promotion-classrooms.create') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3 px-4 border-0">
                            <div class="icon-shape bg-light-info text-info rounded-circle p-2 me-3">
                                <i class="fas fa-school"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Créer les classes</h6>
                                <p class="small text-muted mb-0">Organisez les classes pour l'année</p>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-muted"></i>
                        </a>
                        <a href="{{ route('subject.create') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3 px-4 border-0">
                            <div class="icon-shape bg-light-warning text-warning rounded-circle p-2 me-3">
                                <i class="fas fa-book"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Affecter les matières</h6>
                                <p class="small text-muted mb-0">Gérez les matières par classe</p>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-muted"></i>
                        </a>
                        <a href="{{ route('ratio.create') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3 px-4 border-0">
                            <div class="icon-shape bg-light-danger text-danger rounded-circle p-2 me-3">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Gérer les coefficients</h6>
                                <p class="small text-muted mb-0">Définissez les coefficients des matières</p>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-lg rounded-3 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-info"></i> Informations système</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-shape bg-light-primary text-primary rounded-circle p-2 me-3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Dernière mise à jour</h6>
                            <p class="text-muted mb-0">{{ now()->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-shape bg-light-success text-success rounded-circle p-2 me-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Connecté en tant que</h6>
                            <p class="text-muted mb-0">{{ Auth::user()->name ?? 'Admin' }}</p>
                        </div>
                    </div>
                    <div class="progress-container mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Espace disque</span>
                            <span class="text-muted">75%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-gradient-primary" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .text-gradient-primary {
        background: linear-gradient(90deg, #4e73df 0%, #224abe 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .bg-soft-primary { background-color: rgba(78, 115, 223, 0.1) !important; }
    .bg-soft-success { background-color: rgba(28, 200, 138, 0.1) !important; }
    .bg-soft-info { background-color: rgba(54, 185, 204, 0.1) !important; }
    .bg-soft-warning { background-color: rgba(246, 194, 62, 0.1) !important; }
    .bg-soft-danger { background-color: rgba(231, 74, 59, 0.1) !important; }

    .bg-light-primary { background-color: #f0f7ff !important; }
    .bg-light-success { background-color: #f0fff4 !important; }
    .bg-light-info { background-color: #f0f9ff !important; }
    .bg-light-warning { background-color: #fffaf0 !important; }
    .bg-light-danger { background-color: #fff5f5 !important; }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
    }

    .list-group-item-action:hover {
        background-color: #f8f9fa;
    }

    .icon-shape {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush
