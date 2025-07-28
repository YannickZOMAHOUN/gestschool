@extends('layouts.template')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Affectation des enseignants par classe
                    </h5>
                    <div id="liveClock" class="text-white fs-6"></div>
                </div>

                <div class="card-body">
                    <div id="alertContainer"></div>

                    <form method="POST" action="{{ route('teacher-assignments.store') }}" id="assignmentForm">
                        @csrf
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Année scolaire</label>
                                <select name="year_id" id="year_id" class="form-select border-primary" required>
                                    <option value="">-- Choisissez une année --</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Filière</label>
                                <select name="sector_id" id="sector_id" class="form-select border-primary" disabled required>
                                    <option value="">-- Sélectionnez d'abord l'année --</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Promotion</label>
                                <select name="promotion_id" id="promotion_id" class="form-select border-primary" disabled required>
                                    <option value="">-- Sélectionnez d'abord la filière --</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Classe</label>
                                <select name="classroom_id" id="classroom_id" class="form-select border-primary" disabled required>
                                    <option value="">-- Sélectionnez d'abord la promotion --</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Matière</label>
                                <select name="subject_id" id="subject_id" class="form-select border-primary" disabled required>
                                    <option value="">-- Sélectionnez d'abord la promotion --</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Enseignant</label>
                                <select name="teacher_id" id="teacher_id" class="form-select border-primary" required>
                                    <option value="">-- Choisissez un enseignant --</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }} {{ $teacher->surname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_principal" name="is_principal" value="1" style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label fw-bold" for="is_principal">Professeur Principal</label>
                                </div>
                            </div>

                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                            <button type="submit" class="btn btn-primary me-md-2 rounded-pill">
                                <i class="fas fa-save me-1"></i> Enregistrer
                            </button>
                            <button type="reset" class="btn btn-outline-secondary rounded-pill">
                                <i class="fas fa-undo me-1"></i> Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list-check me-2"></i>Affectations existantes
                    </h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th width="20%">Classe</th>
                                    <th width="25%">Matière</th>
                                    <th width="25%">Enseignant</th>
                                    <th width="15%">Professeur Principal</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="assignmentsTable">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-2"></i>Sélectionnez une classe pour afficher les affectations
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmation de suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette affectation ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Annuler
                </button>
                <button type="button" class="btn btn-danger rounded-pill" id="confirmDelete">
                    <i class="fas fa-trash me-1"></i> Confirmer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('another_JS')
<script>
   // Horloge en temps réel
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const dateString = now.toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    document.getElementById('liveClock').innerHTML = `<i class="fas fa-clock me-1"></i>${dateString} - ${timeString}`;
}
setInterval(updateClock, 1000);
updateClock();

// Éléments du DOM
const yearSelect = document.getElementById('year_id');
const sectorSelect = document.getElementById('sector_id');
const promotionSelect = document.getElementById('promotion_id');
const subjectSelect = document.getElementById('subject_id');
const classroomSelect = document.getElementById('classroom_id');
const assignmentsTable = document.getElementById('assignmentsTable');
const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
const isPrincipalCheckbox = document.getElementById('is_principal');
let assignmentToDelete = null;

// Gestionnaires d'événements
yearSelect.addEventListener('change', loadSectors);
sectorSelect.addEventListener('change', loadPromotions);
promotionSelect.addEventListener('change', () => {
    loadSubjects();
    loadClassrooms();
});
classroomSelect.addEventListener('change', loadAssignments);

// Gestion de la soumission du formulaire
document.getElementById('assignmentForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    // Préparation des données
    const formData = new FormData(this);
    // Convertir la checkbox en valeur booléenne
    formData.set('is_principal', isPrincipalCheckbox.checked ? '1' : '0');

    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Enregistrement...';

    try {
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            showAlert('success', data.message);
            loadAssignments();
            // Réinitialiser seulement les champs matière et enseignant
            subjectSelect.value = '';
            document.getElementById('teacher_id').value = '';
            isPrincipalCheckbox.checked = false;
        } else {
            showAlert('danger', data.message);
        }
    } catch (error) {
        showAlert('danger', 'Une erreur est survenue lors de l\'enregistrement');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Enregistrer';
    }
});

// Gestion de la suppression
document.addEventListener('click', function(e) {
    if (e.target.closest('.delete-assignment')) {
        assignmentToDelete = e.target.closest('.delete-assignment').dataset.id;
        confirmModal.show();
    }
});

document.getElementById('confirmDelete').addEventListener('click', async function() {
    if (!assignmentToDelete) return;

    const deleteBtn = this;
    deleteBtn.disabled = true;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Suppression...';

    try {
        const response = await fetch(`/teacher-assignments/${assignmentToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            showAlert('success', data.message);
            document.querySelector(`tr[data-id="${assignmentToDelete}"]`).remove();

            // Si le tableau est vide, afficher un message
            if (document.querySelectorAll('#assignmentsTable tr[data-id]').length === 0) {
                assignmentsTable.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-2"></i>Aucune affectation trouvée pour cette classe
                        </td>
                    </tr>
                `;
            }
        } else {
            showAlert('danger', data.message);
        }
    } catch (error) {
        showAlert('danger', 'Une erreur est survenue lors de la suppression');
    } finally {
        confirmModal.hide();
        deleteBtn.disabled = false;
        deleteBtn.innerHTML = '<i class="fas fa-trash me-1"></i> Confirmer';
        assignmentToDelete = null;
    }
});

// Gestion des professeurs principaux
document.addEventListener('change', async function(e) {
    if (e.target.classList.contains('set-principal')) {
        const checkbox = e.target;
        const assignmentId = checkbox.dataset.assignmentId;
        const classroomId = classroomSelect.value;

        if (!checkbox.checked) return;

        checkbox.disabled = true;

        try {
            const response = await fetch('/teacher-assignments/set-principal', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    assignment_id: assignmentId,
                    classroom_id: classroomId
                })
            });

            const data = await response.json();

            if (data.success) {
                // Décocher toutes les autres cases de cette classe
                document.querySelectorAll('.set-principal').forEach(cb => {
                    if (cb.dataset.assignmentId !== assignmentId) {
                        cb.checked = false;
                    }
                });

                showAlert('success', data.message);
            } else {
                checkbox.checked = false;
                showAlert('danger', data.message);
            }
        } catch (error) {
            showAlert('danger', 'Une erreur est survenue');
            checkbox.checked = false;
        } finally {
            checkbox.disabled = false;
        }
    }
});

// Fonctions de chargement des données
async function loadSectors() {
    const yearId = yearSelect.value;
    sectorSelect.innerHTML = '<option value="">Chargement...</option>';
    sectorSelect.disabled = true;
    promotionSelect.disabled = true;
    classroomSelect.disabled = true;
    subjectSelect.disabled = true;

    try {
        const response = await fetch(`/teacher-assignments/get-sectors/${yearId}`);
        const sectors = await response.json();

        let options = '<option value="">-- Choisissez une filière --</option>';
        sectors.forEach(sector => {
            options += `<option value="${sector.id}">${sector.name}</option>`;
        });

        sectorSelect.innerHTML = options;
        sectorSelect.disabled = false;

        // Réinitialiser les autres selects
        promotionSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la filière --</option>';
        classroomSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la promotion --</option>';
        subjectSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la promotion --</option>';
        assignmentsTable.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="fas fa-info-circle me-2"></i>Sélectionnez une classe pour afficher les affectations
                </td>
            </tr>
        `;
    } catch (error) {
        showAlert('danger', 'Erreur lors du chargement des filières');
        sectorSelect.innerHTML = '<option value="">-- Erreur de chargement --</option>';
    }
}

async function loadPromotions() {
    const yearId = yearSelect.value;
    const sectorId = sectorSelect.value;
    promotionSelect.innerHTML = '<option value="">Chargement...</option>';
    promotionSelect.disabled = true;
    classroomSelect.disabled = true;
    subjectSelect.disabled = true;

    try {
        const response = await fetch(`/teacher-assignments/get-promotions/${yearId}/${sectorId}`);
        const promotions = await response.json();

        let options = '<option value="">-- Choisissez une promotion --</option>';
        promotions.forEach(p => {
            options += `<option value="${p.id}">${p.name}</option>`;
        });

        promotionSelect.innerHTML = options;
        promotionSelect.disabled = false;

        // Réinitialiser les autres selects
        classroomSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la promotion --</option>';
        subjectSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la promotion --</option>';
    } catch (error) {
        showAlert('danger', 'Erreur lors du chargement des promotions');
        promotionSelect.innerHTML = '<option value="">-- Erreur de chargement --</option>';
    }
}

async function loadSubjects() {
    const yearId = yearSelect.value;
    const sectorId = sectorSelect.value;
    const promotionId = promotionSelect.value;
    subjectSelect.innerHTML = '<option value="">Chargement...</option>';
    subjectSelect.disabled = true;

    try {
        const response = await fetch(`/teacher-assignments/get-subjects/${yearId}/${sectorId}/${promotionId}`);
        const subjects = await response.json();

        let options = '<option value="">-- Choisissez une matière --</option>';
        subjects.forEach(subject => {
            options += `<option value="${subject.id}">${subject.name}</option>`;
        });

        subjectSelect.innerHTML = options;
        subjectSelect.disabled = false;
    } catch (error) {
        showAlert('danger', 'Erreur lors du chargement des matières');
        subjectSelect.innerHTML = '<option value="">-- Erreur de chargement --</option>';
    }
}

async function loadClassrooms() {
    const yearId = yearSelect.value;
    const sectorId = sectorSelect.value;
    const promotionId = promotionSelect.value;
    classroomSelect.innerHTML = '<option value="">Chargement...</option>';
    classroomSelect.disabled = true;

    try {
        const response = await fetch(`/teacher-assignments/get-classes/${yearId}/${sectorId}/${promotionId}`);
        const classrooms = await response.json();

        let options = '<option value="">-- Choisissez une classe --</option>';
        classrooms.forEach(cls => {
            options += `<option value="${cls.id}">${cls.name}</option>`;
        });

        classroomSelect.innerHTML = options;
        classroomSelect.disabled = false;
    } catch (error) {
        showAlert('danger', 'Erreur lors du chargement des classes');
        classroomSelect.innerHTML = '<option value="">-- Erreur de chargement --</option>';
    }
}

async function loadAssignments() {
    const yearId = yearSelect.value;
    const classroomId = classroomSelect.value;

    if (!yearId || !classroomId) return;

    assignmentsTable.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </td>
        </tr>
    `;

    try {
        const response = await fetch(`/teacher-assignments/get-assignments/${yearId}/${classroomId}`);
        const data = await response.json();

        if (data.length === 0) {
            assignmentsTable.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-info-circle me-2"></i>Aucune affectation trouvée pour cette classe
                    </td>
                </tr>
            `;
            return;
        }

        let rows = '';
        data.forEach(assignment => {
            rows += `
                <tr data-id="${assignment.id}">
                    <td>${classroomSelect.options[classroomSelect.selectedIndex].text}</td>
                    <td>${assignment.subject}</td>
                    <td>${assignment.teacher}</td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input set-principal" type="checkbox"
                                data-assignment-id="${assignment.id}"
                                ${assignment.is_principal ? 'checked' : ''}>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-danger delete-assignment"
                                data-id="${assignment.id}"
                                title="Supprimer cette affectation">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        assignmentsTable.innerHTML = rows;
    } catch (error) {
        assignmentsTable.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>Erreur lors du chargement des affectations
                </td>
            </tr>
        `;
    }
}

// Fonction pour afficher les alertes
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show mb-4`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    const alertContainer = document.getElementById('alertContainer');
    alertContainer.innerHTML = '';
    alertContainer.appendChild(alertDiv);

    // Supprimer l'alerte après 5 secondes
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
@endsection
