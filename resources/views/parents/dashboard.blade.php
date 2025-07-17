@extends('layouts.guest')

@section('content')
<div class="parent-dashboard">
    <div class="welcome-section">
        <div class="student-info">
            <div class="student-avatar">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="student-details">
                <h2>{{ $student->name }} {{ $student->surname }}</h2>
                <p class="student-class">
                    @if($recording)
                        {{ $recording->classroom->name }} - Matricule: {{ $student->matricule }}
                    @endif
                </p>
                <div class="student-meta">
                    <span><i class="fas fa-birthday-cake"></i> {{ $student->birthday }} à {{ $student->birthplace }}</span>
                    <span><i class="fas fa-phone"></i> {{ $student->number }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Carte Informations de base -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informations de l'élève</h3>
                <div class="card-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
            </div>
            <div class="student-basic-info">
                <div class="info-item">
                    <span class="info-label">Date de naissance</span>
                    <span class="info-value">{{ $student->birthday }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Lieu de naissance</span>
                    <span class="info-value">{{ $student->birthplace }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Contact</span>
                    <span class="info-value">{{ $student->number }}</span>
                </div>
                @if($recording)
                <div class="info-item">
                    <span class="info-label">Classe actuelle</span>
                    <span class="info-value">{{ $recording->classroom->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Professeur principal</span>
                    <span class="info-value">Mme. Dubois</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Carte Résumé des performances -->
        <div class="card performance-summary">
            <div class="card-header">
                <h3 class="card-title">Résumé des performances</h3>
                <div class="card-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="performance-metrics">
                <div class="metric-item">
                    <div class="metric-value">15.8</div>
                    <div class="metric-label">Moyenne générale</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value">4</div>
                    <div class="metric-label">Matières au-dessus de 16</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value">Maths</div>
                    <div class="metric-label">Meilleure matière (17.2)</div>
                </div>
            </div>
        </div>

        <!-- Carte Dernières notes -->
        <div class="card recent-grades">
            <div class="card-header">
                <h3 class="card-title">Dernières notes</h3>
                <div class="card-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
            <div class="grades-list">
               
            </div>
        </div>
    </div>

    <!-- Tableau des notes détaillées -->
    <div class="card grades-table-card">
        <div class="card-header">
            <h3 class="card-title">Détail des notes par matière</h3>
            <div class="card-icon">
                <i class="fas fa-table"></i>
            </div>
        </div>
        <div class="table-responsive">
            <table class="grades-table">
                <thead>
                    <tr>
                        <th>Matière</th>
                        <th>Interro 1</th>
                        <th>Interro 2</th>
                        <th>Interro 3</th>
                        <th>Devoir 1</th>
                        <th>Devoir 2</th>
                        <th>Moyenne</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Section commentaires -->
    <div class="card teacher-comments">
        <div class="card-header">
            <h3 class="card-title">Commentaires des enseignants</h3>
            <div class="card-icon">
                <i class="fas fa-comment-dots"></i>
            </div>
        </div>
        <div class="comments-list">

        </div>
    </div>
</div>

<style>
    .parent-dashboard {
        padding: 20px;
    }

    .welcome-section {
        background-color: white;
        border-radius: 10px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .student-info {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .student-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: #f0f4f8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: #4361ee;
    }

    .student-details h2 {
        margin: 0;
        color: #212529;
        font-size: 24px;
    }

    .student-class {
        color: #6c757d;
        margin: 5px 0 10px;
    }

    .student-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        font-size: 14px;
    }

    .student-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .card {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .card-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        color: #212529;
        margin: 0;
        font-size: 18px;
    }

    .card-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f0f4f8;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4361ee;
    }

    .student-basic-info {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 500;
        color: #6c757d;
    }

    .info-value {
        font-weight: 600;
    }

    .performance-metrics {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        text-align: center;
    }

    .metric-item {
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .metric-value {
        font-size: 24px;
        font-weight: 700;
        color: #4361ee;
        margin-bottom: 5px;
    }

    .metric-label {
        font-size: 13px;
        color: #6c757d;
    }

    .grades-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .grade-item {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .grade-item:last-child {
        border-bottom: none;
    }

    .grade-subject {
        font-weight: 500;
    }

    .grade-value {
        font-weight: 700;
        color: #4361ee;
        text-align: center;
    }

    .grade-date, .grade-type {
        font-size: 13px;
        color: #6c757d;
        text-align: center;
    }

    .view-all-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 15px;
        color: #4895ef;
        text-decoration: none;
        font-weight: 500;
    }

    .grades-table-card {
        margin-bottom: 30px;
    }

    .grades-table {
        width: 100%;
        border-collapse: collapse;
    }

    .grades-table th, .grades-table td {
        padding: 12px 15px;
        text-align: center;
        border-bottom: 1px solid #eee;
    }

    .grades-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
    }

    .grades-table tr:hover {
        background-color: #f8f9fa;
    }

    .average-cell {
        font-weight: 700;
        color: #4361ee;
    }

    .teacher-comments {
        margin-bottom: 30px;
    }

    .comments-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .comment-item {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
    }

    .comment-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .comment-teacher {
        font-weight: 600;
        color: #4361ee;
    }

    .comment-date {
        font-size: 13px;
        color: #6c757d;
    }

    .comment-content {
        line-height: 1.5;
    }

    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .performance-metrics {
            grid-template-columns: 1fr;
        }

        .grade-item {
            grid-template-columns: 1fr 1fr;
            grid-template-rows: auto auto;
            gap: 5px;
        }

        .grade-subject {
            grid-column: 1 / 3;
        }

        .grades-table {
            display: block;
            overflow-x: auto;
        }
    }
</style>
@endsection
