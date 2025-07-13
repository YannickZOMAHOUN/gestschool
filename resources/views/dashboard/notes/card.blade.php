@extends('layouts.template')

@section('content')
<div class="container-fluid py-4">
    <div class="card elegant-card shadow-lg mb-5">
        <!-- Card Header with Gradient Background -->
        <div class="card-header elegant-card-header bg-gradient-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-export header-icon me-3 text-white"></i>
                    <div>
                        <h5 class="mb-0 text-white fw-bold">Exportation des Notes</h5>
                        <small class="text-white-80">Générez des fichiers Excel des notes par classe</small>
                    </div>
                </div>
                <i class="fas fa-file-excel header-secondary-icon text-white-50"></i>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body elegant-card-body bg-light">
            <form method="GET" action="{{ route('notes.exportcard') }}" class="needs-validation" novalidate>
                @csrf
                <!-- Selection Row -->
                <div class="row g-4 mb-4">
                    <!-- Year Selection -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label elegant-label fw-bold">Année scolaire</label>
                            <div class="input-group elegant-input-group">
                                <span class="input-group-text elegant-input-prepend bg-white">
                                    <i class="fas fa-calendar text-primary"></i>
                                </span>
                                <select name="year_id" id="export_year_id" class="form-select elegant-select" required>
                                    <option value="">-- Sélectionnez --</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="invalid-feedback elegant-feedback">Veuillez sélectionner une année scolaire</div>
                        </div>
                    </div>

                    <!-- Sector Selection -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label elegant-label fw-bold">Filière</label>
                            <div class="input-group elegant-input-group">
                                <span class="input-group-text elegant-input-prepend bg-white">
                                    <i class="fas fa-project-diagram text-primary"></i>
                                </span>
                                <select name="sector_id" id="export_sector_id" class="form-select elegant-select" disabled required>
                                    <option value="">-- Choisir année d'abord --</option>
                                </select>
                            </div>
                            <div class="invalid-feedback elegant-feedback">Veuillez sélectionner une filière</div>
                        </div>
                    </div>

                    <!-- Promotion Selection -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label elegant-label fw-bold">Promotion</label>
                            <div class="input-group elegant-input-group">
                                <span class="input-group-text elegant-input-prepend bg-white">
                                    <i class="fas fa-layer-group text-primary"></i>
                                </span>
                                <select name="promotion_id" id="export_promotion_id" class="form-select elegant-select" disabled required>
                                    <option value="">-- Choisir filière d'abord --</option>
                                </select>
                            </div>
                            <div class="invalid-feedback elegant-feedback">Veuillez sélectionner une promotion</div>
                        </div>
                    </div>

                    <!-- Classroom Selection -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label elegant-label fw-bold">Classe</label>
                            <div class="input-group elegant-input-group">
                                <span class="input-group-text elegant-input-prepend bg-white">
                                    <i class="fas fa-school text-primary"></i>
                                </span>
                                <select name="classroom_id" id="export_classroom_id" class="form-select elegant-select" disabled required>
                                    <option value="">-- Choisir promotion d'abord --</option>
                                </select>
                            </div>
                            <div class="invalid-feedback elegant-feedback">Veuillez sélectionner une classe</div>
                        </div>
                    </div>
                </div>

                <!-- Semester Selection -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label elegant-label fw-bold">Semestre</label>
                            <div class="input-group elegant-input-group">
                                <span class="input-group-text elegant-input-prepend bg-white">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </span>
                                <select class="form-select elegant-select" name="semester" id="semester" required>
                                    <option value="" disabled selected>-- Sélectionnez --</option>
                                    <option value="1">Semestre 1</option>
                                    <option value="2">Semestre 2</option>
                                </select>
                            </div>
                            <div class="invalid-feedback elegant-feedback">Veuillez sélectionner un semestre</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-center mt-5">
                    <button type="submit" name="export_type" value="fiche_collation" class="btn btn-success btn-lg shadow-sm">
                        <i class="fas fa-file-excel me-2"></i>Générer la fiche de collation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('another_JS')
<script>
    function fetchOptions(url, selectElement, placeholder = '-- Sélectionnez --') {
        selectElement.innerHTML = `<option value="">${placeholder}</option>`;
        selectElement.disabled = true;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    data.forEach(item => {
                        selectElement.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });
                } else {
                    selectElement.innerHTML += `<option value="" disabled>Aucune option disponible</option>`;
                }
                selectElement.disabled = false;
            })
            .catch(error => {
                console.error('Error fetching options:', error);
                selectElement.innerHTML += `<option value="" disabled>Erreur de chargement</option>`;
                selectElement.disabled = false;
            });
    }

    function setupFormListeners(yearId, sectorId, promotionId, classroomId) {
        const yearSelect = document.getElementById(yearId);
        const sectorSelect = document.getElementById(sectorId);
        const promotionSelect = document.getElementById(promotionId);
        const classroomSelect = document.getElementById(classroomId);

        yearSelect.addEventListener('change', () => {
            const yearId = yearSelect.value;
            if (yearId) {
                fetchOptions(`/api/sectors-by-year/${yearId}`, sectorSelect);
            } else {
                sectorSelect.innerHTML = '<option value="">-- Choisir année d\'abord --</option>';
                sectorSelect.disabled = true;
            }
            promotionSelect.innerHTML = '<option value="">-- Choisir filière d\'abord --</option>';
            promotionSelect.disabled = true;
            classroomSelect.innerHTML = '<option value="">-- Choisir promotion d\'abord --</option>';
            classroomSelect.disabled = true;
        });

        sectorSelect.addEventListener('change', () => {
            const yearId = yearSelect.value;
            const sectorId = sectorSelect.value;
            if (yearId && sectorId) {
                fetchOptions(`/api/promotions-by-year-sector/${yearId}/${sectorId}`, promotionSelect);
            } else {
                promotionSelect.innerHTML = '<option value="">-- Choisir filière d\'abord --</option>';
                promotionSelect.disabled = true;
            }
            classroomSelect.innerHTML = '<option value="">-- Choisir promotion d\'abord --</option>';
            classroomSelect.disabled = true;
        });

        promotionSelect.addEventListener('change', () => {
            const promotionId = promotionSelect.value;
            if (promotionId) {
                fetchOptions(`/api/classes-by-promotion/${promotionId}`, classroomSelect);
            } else {
                classroomSelect.innerHTML = '<option value="">-- Choisir promotion d\'abord --</option>';
                classroomSelect.disabled = true;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        setupFormListeners('export_year_id', 'export_sector_id', 'export_promotion_id', 'export_classroom_id');

        // Form validation
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>

<style>
    .elegant-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }

    .elegant-card-header {
        padding: 1.5rem;
        color: white;
        border-bottom: none;
    }

    .header-icon {
        font-size: 1.5rem;
    }

    .header-secondary-icon {
        font-size: 2rem;
        opacity: 0.2;
    }

    .elegant-card-body {
        padding: 2rem;
        background-color: #f8fafc;
    }

    .elegant-label {
        color: #4a5568;
        margin-bottom: 0.5rem;
        display: block;
    }

    .elegant-input-group {
        border-radius: 8px;
        overflow: hidden;
    }

    .elegant-input-prepend {
        border-right: none;
        background-color: white;
    }

    .elegant-select {
        border-left: none;
        padding: 0.75rem;
        height: calc(1.5em + 1.5rem + 2px);
    }

    .elegant-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(66, 153, 225, 0.25);
        border-color: #4299e1;
    }

    .elegant-feedback {
        font-size: 0.85rem;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .text-white-80 {
        color: rgba(255, 255, 255, 0.8);
    }

    .btn-success {
        background-color: #38a169;
        border-color: #38a169;
        transition: all 0.3s ease;
        padding: 0.75rem 2rem;
        font-weight: 600;
    }

    .btn-success:hover {
        background-color: #2f855a;
        border-color: #2f855a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(56, 161, 105, 0.3);
    }
</style>
@endsection
