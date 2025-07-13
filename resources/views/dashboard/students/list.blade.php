@extends('layouts.template')

@section('another_CSS')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Custom Styles */
    .card-header-gradient {
        background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
        border-bottom: none;
    }

    .selection-card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }

    .select2-container .select2-selection--single {
        height: 45px;
        border-radius: 8px;
        border: 1px solid #dfe7f1;
        padding: 8px 12px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px;
    }

    .students-table-container {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .students-table {
        width: 100%;
        margin-bottom: 0;
    }

    .students-table thead th {
        background-color: #3498db;
        color: white;
        font-weight: 500;
        padding: 15px;
        border: none;
    }

    .students-table tbody tr {
        transition: all 0.2s;
    }

    .students-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .students-table tbody td {
        padding: 15px;
        vertical-align: middle;
        border-top: 1px solid #f1f3f5;
    }

    .action-buttons a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        margin: 0 3px;
        transition: all 0.2s;
    }

    .view-btn {
        color: #3498db;
        background-color: rgba(52, 152, 219, 0.1);
    }

    .edit-btn {
        color: #2ecc71;
        background-color: rgba(46, 204, 113, 0.1);
    }

    .delete-btn {
        color: #e74c3c;
        background-color: rgba(231, 76, 60, 0.1);
    }

    .action-buttons a:hover {
        transform: scale(1.1);
        text-decoration: none;
    }

    .empty-state {
        padding: 40px 0;
        text-align: center;
    }

    .empty-state-icon {
        font-size: 60px;
        color: #b8c4cc;
        margin-bottom: 20px;
    }

    .empty-state-text {
        color: #7f8c8d;
        font-size: 18px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .students-table thead {
            display: none;
        }

        .students-table tbody tr {
            display: block;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .students-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            text-align: right;
            border-top: none;
        }

        .students-table tbody td::before {
            content: attr(data-label);
            font-weight: 500;
            color: #7f8c8d;
            margin-right: auto;
            padding-right: 15px;
        }

        .action-buttons {
            justify-content: flex-end;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg mb-5">
        <!-- Card Header -->
        <div class="card-header card-header-gradient text-white py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fas fa-user-graduate me-3 fs-4"></i>
                <div>
                    <h5 class="mb-0 fw-semibold">Gestion des Élèves</h5>
                    <small class="opacity-75">Recherche et gestion des élèves par classe</small>
                </div>
            </div>
            <a href="{{ route('student.create') }}" class="btn btn-light btn-sm rounded-pill">
                <i class="fas fa-plus me-1"></i> Nouvel Élève
            </a>
        </div>

        <!-- Selection Filters -->
        <div class="card-body px-4 py-4">
            <div class="row g-3 mb-4">
                <!-- Year -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Année scolaire</label>
                    <select id="year_id" class="form-select select2">
                        <option value="">-- Sélectionnez --</option>
                        @foreach($years as $year)
                            <option value="{{ $year->id }}">{{ $year->year }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sector -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Filière</label>
                    <select id="sector_id" class="form-select select2" disabled>
                        <option value="">-- Choisir année --</option>
                    </select>
                </div>

                <!-- Promotion -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Promotion</label>
                    <select id="promotion_id" class="form-select select2" disabled>
                        <option value="">-- Choisir filière --</option>
                    </select>
                </div>

                <!-- Classroom -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Classe</label>
                    <select id="classroom_id" class="form-select select2" disabled>
                        <option value="">-- Choisir promotion --</option>
                    </select>
                </div>
            </div>

            <!-- Students Table -->
            <div class="students-table-container" id="studentsCard" style="display:none;">
                <div class="table-responsive">
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom</th>
                                <th>Prénom(s)</th>
                                <th>Sexe</th>
                                <th>Date de Naissance</th>
                                <th>Lieu de Naissance</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody">
                            <!-- Students will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div class="empty-state" id="emptyState">
                <div class="empty-state-icon">
                    <i class="fas fa-search"></i>
                </div>
                <p class="empty-state-text">Sélectionnez une classe pour afficher la liste des élèves</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('another_JS')
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(document).ready(function () {
    // Initialize Select2
    $('.select2').select2({
        placeholder: "Sélectionnez une option",
        allowClear: true
    });

    const sectorSelect = $('#sector_id');
    const promotionSelect = $('#promotion_id');
    const classroomSelect = $('#classroom_id');
    const studentsCard = $('#studentsCard');
    const emptyState = $('#emptyState');
    const studentsTableBody = $('#studentsTableBody');

    function formatDate(dateStr) {
        if (!dateStr) return 'N/A';
        const date = new Date(dateStr);
        if (isNaN(date)) return dateStr;
        return date.toLocaleDateString('fr-FR');
    }

    // When year changes, load sectors
    $('#year_id').on('change', function () {
        const yearId = $(this).val();

        sectorSelect.val(null).prop('disabled', true).trigger('change');
        promotionSelect.val(null).prop('disabled', true).trigger('change');
        classroomSelect.val(null).prop('disabled', true).trigger('change');
        studentsCard.hide();
        emptyState.show();
        studentsTableBody.empty();

        if (!yearId) {
            sectorSelect.html('<option value="">-- Choisir année --</option>');
            return;
        }

        // Show loading state
        sectorSelect.html('<option value="">Chargement...</option>').prop('disabled', true).trigger('change');

        $.getJSON(`/api/sectors-by-year/${yearId}`)
            .done(function (data) {
                let options = '<option value="">-- Toutes les filières --</option>';
                data.forEach(function (sector) {
                    options += `<option value="${sector.id}">${sector.name}</option>`;
                });
                sectorSelect.html(options).prop('disabled', false).trigger('change');
            })
            .fail(function () {
                sectorSelect.html('<option value="">Erreur de chargement</option>').prop('disabled', true).trigger('change');
            });
    });

    // When sector changes, load promotions
    sectorSelect.on('change', function () {
        const yearId = $('#year_id').val();
        const sectorId = $(this).val();

        promotionSelect.val(null).prop('disabled', true).trigger('change');
        classroomSelect.val(null).prop('disabled', true).trigger('change');
        studentsCard.hide();
        emptyState.show();
        studentsTableBody.empty();

        if (!sectorId) return;

        // Show loading state
        promotionSelect.html('<option value="">Chargement...</option>').prop('disabled', true).trigger('change');

        $.getJSON(`/api/promotions-by-year-sector/${yearId}/${sectorId}`)
            .done(function (data) {
                let options = '<option value="">-- Toutes les promotions --</option>';
                data.forEach(function (promotion) {
                    options += `<option value="${promotion.id}">${promotion.name}</option>`;
                });
                promotionSelect.html(options).prop('disabled', false).trigger('change');
            })
            .fail(function () {
                promotionSelect.html('<option value="">Erreur de chargement</option>').prop('disabled', true).trigger('change');
            });
    });

    // When promotion changes, load classrooms
    promotionSelect.on('change', function () {
        const promotionId = $(this).val();

        classroomSelect.val(null).prop('disabled', true).trigger('change');
        studentsCard.hide();
        emptyState.show();
        studentsTableBody.empty();

        if (!promotionId) return;

        // Show loading state
        classroomSelect.html('<option value="">Chargement...</option>').prop('disabled', true).trigger('change');

        $.getJSON(`/api/classes-by-promotion/${promotionId}`)
            .done(function (data) {
                let options = '<option value="">-- Toutes les classes --</option>';
                data.forEach(function (classroom) {
                    options += `<option value="${classroom.id}">${classroom.name}</option>`;
                });
                classroomSelect.html(options).prop('disabled', false).trigger('change');
            })
            .fail(function () {
                classroomSelect.html('<option value="">Erreur de chargement</option>').prop('disabled', true).trigger('change');
            });
    });

    // When classroom changes, load students
    classroomSelect.on('change', function () {
        const yearId = $('#year_id').val();
        const sectorId = $('#sector_id').val();
        const promotionId = $('#promotion_id').val();
        const classroomId = $(this).val();

        studentsCard.hide();
        emptyState.show();
        studentsTableBody.empty();

        if (!yearId || !sectorId || !promotionId || !classroomId) return;

        // Show loading state
        emptyState.html(`
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <p class="mt-2">Chargement des élèves...</p>
        `);

        $.getJSON(`/students/${yearId}/${sectorId}/${promotionId}/${classroomId}`)
            .done(function (response) {
                if (response.data && response.data.length > 0) {
                    let rows = '';
                    response.data.forEach(function (student) {
                        let showUrl = '{{ route("note.show", [":id"]) }}'.replace(':id', student.id) + `?year=${yearId}&classroom=${classroomId}`;
                        let editUrl = '{{ route("student.edit", ":id") }}'.replace(':id', student.id);

                        rows += `
                            <tr>
                                <td data-label="Matricule">${student.matricule}</td>
                                <td data-label="Nom">${student.name}</td>
                                <td data-label="Prénom">${student.surname}</td>
                                <td data-label="Sexe">${student.sex === 'M' ? 'Masculin' : 'Féminin'}</td>
                                <td data-label="Naissance">${formatDate(student.birthday)}</td>
                                <td data-label="Lieu de naissance">${student.birthplace || 'N/A'}</td>
                                <td data-label="Actions" class="action-buttons">
                                    <a href="${showUrl}" class="view-btn" title="Voir les notes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="${editUrl}" class="edit-btn" title="Modifier">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                    studentsTableBody.html(rows);
                    emptyState.hide();
                    studentsCard.show();
                } else {
                    emptyState.html(`
                        <div class="empty-state-icon">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <p class="empty-state-text">Aucun élève trouvé dans cette classe</p>
                    `);
                }
            })
            .fail(function () {
                emptyState.html(`
                    <div class="empty-state-icon text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <p class="empty-state-text">Erreur lors du chargement des élèves</p>
                `);
            });
    });

    // Initialize tooltips
    $(document).on('mouseover', '[title]', function() {
        $(this).tooltip({
            trigger: 'hover',
            placement: 'top'
        });
    });
});
</script>
@endsection
