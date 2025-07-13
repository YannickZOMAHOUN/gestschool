@extends('layouts.template')

@section('content')
<div class="container-fluid py-4">
    <div class="card elegant-card shadow-lg mb-5">
        <!-- Card Header with Gradient Background -->
        <div class="card-header elegant-card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-export header-icon me-3"></i>
                    <div>
                        <h5 class="mb-0 fw-semibold">Exportation des Notes</h5>
                        <small class="text-white-50">Générez des fichiers Excel des notes par classe</small>
                    </div>
                </div>
                <i class="fas fa-file-excel header-secondary-icon"></i>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body elegant-card-body">
            <form method="GET" action="{{ route('notes.export') }}" class="needs-validation" novalidate>
                <!-- Selection Row -->
                <div class="row g-4 mb-4">
                    <!-- Year Selection -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label elegant-label">Année scolaire</label>
                            <div class="input-group elegant-input-group">
                                <span class="input-group-text elegant-input-prepend">
                                    <i class="fas fa-calendar"></i>
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
                            <label class="form-label elegant-label">Filière</label>
                            <div class="input-group elegant-input-group">
                                <span class="input-group-text elegant-input-prepend">
                                    <i class="fas fa-project-diagram"></i>
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
                            <label class="form-label elegant-label">Promotion</label>
                            <div class="input-group elegant-input-group">
                                <span class="input-group-text elegant-input-prepend">
                                    <i class="fas fa-layer-group"></i>
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
                            <label class="form-label elegant-label">Classe</label>
                            <div class="input-group elegant-input-group">
                                <span class="input-group-text elegant-input-prepend">
                                    <i class="fas fa-school"></i>
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
                            <label class="form-label elegant-label">Semestre</label>
                            <div class="input-group elegant-input-group">
                                <span class="input-group-text elegant-input-prepend">
                                    <i class="fas fa-calendar-alt"></i>
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
                    <button type="reset" class="btn elegant-btn-reset me-3">
                        <i class="fas fa-eraser me-2"></i>Réinitialiser
                    </button>
                    <button type="submit" class="btn elegant-btn-export">
                        <i class="fas fa-file-excel me-2"></i>Exporter les Notes
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
/* Elegant Card Styles */
.elegant-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.elegant-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.elegant-card-header {
    background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
    color: white;
    padding: 1.5rem;
    border-bottom: none;
}

.header-icon {
    font-size: 1.5rem;
    color: rgba(255, 255, 255, 0.9);
}

.header-secondary-icon {
    font-size: 1.8rem;
    color: rgba(255, 255, 255, 0.2);
    transition: all 0.3s;
}

.elegant-card:hover .header-secondary-icon {
    color: rgba(255, 255, 255, 0.4);
}

.elegant-card-body {
    padding: 2rem;
    background-color: #f8fafc;
}

/* Form Elements */
.elegant-label {
    font-weight: 500;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    display: block;
}

.elegant-input-group {
    margin-bottom: 0.5rem;
}

.elegant-input-prepend {
    background-color: #e9ecef;
    border: none;
    color: #3498db;
}

.elegant-select {
    border-left: none;
    padding: 0.5rem 1rem;
    height: calc(2.25rem + 8px);
    border-color: #dee2e6;
    transition: all 0.3s;
}

.elegant-select:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
}

.elegant-feedback {
    font-size: 0.85rem;
    color: #e74c3c;
}

/* Buttons */
.elegant-btn-export {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    color: white;
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 50px;
    font-weight: 500;
    transition: all 0.3s;
    box-shadow: 0 2px 10px rgba(46, 204, 113, 0.3);
}

.elegant-btn-export:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(46, 204, 113, 0.4);
    color: white;
}

.elegant-btn-reset {
    background: white;
    color: #7f8c8d;
    border: 1px solid #dee2e6;
    padding: 0.5rem 1.5rem;
    border-radius: 50px;
    font-weight: 500;
    transition: all 0.3s;
}

.elegant-btn-reset:hover {
    background: #f8f9fa;
    color: #34495e;
    border-color: #ced4da;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .elegant-card-body {
        padding: 1.5rem;
    }

    .d-flex.justify-content-center {
        flex-direction: column;
        gap: 1rem;
    }

    .elegant-btn-export, .elegant-btn-reset {
        width: 100%;
    }

    .elegant-btn-reset {
        margin-right: 0 !important;
    }
}
</style>
@endsection
