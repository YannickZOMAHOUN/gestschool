@extends('layouts.template')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg mb-5 border-0" style="border-radius: 15px; overflow: hidden;">
        <!-- En-tête avec dégradé de couleur et icônes -->
        <div class="card-header bg-gradient-primary text-white py-3 position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-light">
                    <i class="fas fa-file-import me-2"></i>Voir les notes
                </h5>
                <div class="position-absolute end-0 top-0 h-100 d-flex align-items-center me-3">
                    <i class="fas fa-star-half-alt fs-4 opacity-75"></i>
                </div>
            </div>
            <div class="wave-shape">
                <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
                    <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="currentColor"></path>
                    <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="currentColor"></path>
                    <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="currentColor"></path>
                </svg>
            </div>
        </div>

        <!-- Corps de la carte avec fond légèrement ombré -->
        <div class="card-body px-4 py-4 bg-light">
            <!-- Formulaire de filtrage avec design moderne -->
            <form method="POST" id="filterForm" class="needs-validation" novalidate>
                @csrf
                <div class="row g-3 mb-4">
                    <!-- Année scolaire -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary d-flex align-items-center">
                            <i class="fas fa-calendar-alt me-2"></i>Année scolaire
                        </label>
                        <div class="input-group">
                            <select name="year_id" id="import_year_id" class="form-select shadow-sm rounded-pill" required>
                                <option value="">-- Choisissez une année --</option>
                                @foreach($years as $year)
                                    <option value="{{ $year->id }}">{{ $year->year }}</option>
                                @endforeach
                            </select>
                            <span class="input-group-text bg-white border-0 position-absolute end-0 h-100" style="z-index: 5; pointer-events: none;">
                                <i class="fas fa-chevron-down text-primary"></i>
                            </span>
                        </div>
                        <div class="invalid-feedback ps-3">Veuillez sélectionner une année scolaire</div>
                    </div>

                    <!-- Filière -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary d-flex align-items-center">
                            <i class="fas fa-graduation-cap me-2"></i>Filière
                        </label>
                        <div class="input-group">
                            <select name="sector_id" id="import_sector_id" class="form-select shadow-sm rounded-pill" disabled required>
                                <option value="">-- Sélectionnez d'abord l'année --</option>
                            </select>
                            <span class="input-group-text bg-white border-0 position-absolute end-0 h-100" style="z-index: 5; pointer-events: none;">
                                <i class="fas fa-chevron-down text-primary"></i>
                            </span>
                        </div>
                        <div class="invalid-feedback ps-3">Veuillez sélectionner une filière</div>
                    </div>

                    <!-- Promotion -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary d-flex align-items-center">
                            <i class="fas fa-users me-2"></i>Promotion
                        </label>
                        <div class="input-group">
                            <select name="promotion_id" id="import_promotion_id" class="form-select shadow-sm rounded-pill" disabled required>
                                <option value="">-- Sélectionnez d'abord la filière --</option>
                            </select>
                            <span class="input-group-text bg-white border-0 position-absolute end-0 h-100" style="z-index: 5; pointer-events: none;">
                                <i class="fas fa-chevron-down text-primary"></i>
                            </span>
                        </div>
                        <div class="invalid-feedback ps-3">Veuillez sélectionner une promotion</div>
                    </div>

                    <!-- Classe -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary d-flex align-items-center">
                            <i class="fas fa-door-open me-2"></i>Classe
                        </label>
                        <div class="input-group">
                            <select name="classroom_id" id="import_classroom_id" class="form-select shadow-sm rounded-pill" disabled required>
                                <option value="">-- Sélectionnez d'abord la promotion --</option>
                            </select>
                            <span class="input-group-text bg-white border-0 position-absolute end-0 h-100" style="z-index: 5; pointer-events: none;">
                                <i class="fas fa-chevron-down text-primary"></i>
                            </span>
                        </div>
                        <div class="invalid-feedback ps-3">Veuillez sélectionner une classe</div>
                    </div>
                </div>

                <!-- Sélection du semestre avec icône -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="semester" class="form-label fw-bold text-primary d-flex align-items-center">
                            <i class="fas fa-calendar-week me-2"></i>Semestre
                        </label>
                        <div class="input-group shadow-sm rounded-pill">
                            <span class="input-group-text bg-primary text-white rounded-start-pill">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <select class="form-select rounded-end-pill" name="semester" id="semester" required>
                                <option value="" disabled selected>Choisissez le semestre</option>
                                <option value="1">Semestre 1</option>
                                <option value="2">Semestre 2</option>
                            </select>
                        </div>
                        <div class="invalid-feedback ps-3">Veuillez sélectionner un semestre</div>
                    </div>
                </div>
            </form>

            <!-- Conteneur pour les notes des étudiants avec animation de chargement -->
            <div id="students-notes-container" class="mt-4">
                <div class="text-center py-5" id="default-message">
                    <div class="icon-container mb-3">
                        <i class="fas fa-search text-muted fs-1"></i>
                    </div>
                    <h5 class="text-muted">Sélectionnez les critères pour afficher les notes</h5>
                    <p class="text-muted small">Choisissez une année, une filière, une promotion, une classe et un semestre</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('another_JS')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Affiche les interros sous forme de liste
function displayInterros(interros) {
    if (!interros) return '-';
    return Array.isArray(interros) ? interros.join(', ') : interros;
}

// Active/désactive le mode édition
function activateToggleSwitches() {
    document.querySelectorAll('.toggle-edit').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const card = this.closest('.student-card');
            const tableRows = card.querySelectorAll('tbody tr');
            const editable = this.checked;

            // Animation du bouton toggle
            if (editable) {
                this.parentElement.classList.add('active');
            } else {
                this.parentElement.classList.remove('active');
            }

            tableRows.forEach(row => {
                const noteId = row.dataset.noteId;
                const cells = row.querySelectorAll('.note-cell');
                const actionCell = row.querySelector('.note-actions');

                if (editable) {
                    const [interros, devoir1, devoir2] = cells;
                    const interrosVal = interros.innerText.split(',').map(n => n.trim()).join(',');

                    interros.innerHTML = `
                        <input type="text"
                               class="form-control form-control-sm interros-input"
                               value="${interrosVal}"
                               name="interros"
                               placeholder="Saisir les notes séparées par des virgules">`;

                    devoir1.innerHTML = `
                        <input type="number"
                               step="0.01"
                               min="0"
                               max="20"
                               class="form-control form-control-sm note-input"
                               name="devoir1"
                               value="${devoir1.innerText.trim()}">`;

                    devoir2.innerHTML = `
                        <input type="number"
                               step="0.01"
                               min="0"
                               max="20"
                               class="form-control form-control-sm note-input"
                               name="devoir2"
                               value="${devoir2.innerText.trim()}">`;

                    actionCell.innerHTML = `
                        <button class="btn btn-sm btn-outline-primary save-btn" data-note-id="${noteId}">
                            <i class="fas fa-save me-1"></i>Enregistrer
                        </button>`;
                } else {
                    // Sauvegarde automatique en quittant le mode édition
                    saveRowChanges(row);
                    actionCell.innerHTML = `<span class="badge bg-success">Sauvegardé</span>`;
                }
            });

            // Ajout des écouteurs pour les boutons de sauvegarde individuels
            document.querySelectorAll('.save-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    saveRowChanges(row);
                    showSuccessToast('Note sauvegardée avec succès');
                });
            });
        });
    });
}

// Sauvegarde les modifications d'une ligne
async function saveRowChanges(row) {
    const noteId = row.dataset.noteId;
    const interros = row.querySelector('[name="interros"]')?.value.split(',').map(n => n.trim()) || [];
    const devoir1 = row.querySelector('[name="devoir1"]')?.value || '';
    const devoir2 = row.querySelector('[name="devoir2"]')?.value || '';

    await saveNote(noteId, interros, devoir1, devoir2);

    // Mise à jour de l'affichage
    const [cellInterros, cellDevoir1, cellDevoir2] = row.querySelectorAll('.note-cell');
    if (cellInterros) cellInterros.innerHTML = interros.join(', ') || '-';
    if (cellDevoir1) cellDevoir1.innerHTML = devoir1 || '-';
    if (cellDevoir2) cellDevoir2.innerHTML = devoir2 || '-';
}

// Affichage de notification
function showSuccessToast(message) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: 'success',
        title: message
    });
}

// Sauvegarde une note via API
async function saveNote(id, interros, devoir1, devoir2) {
    try {
        const res = await fetch(`/api/notes/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ interros, devoir1, devoir2 })
        });

        if (!res.ok) {
            throw new Error('Erreur de sauvegarde');
        }

        return await res.json();
    } catch (error) {
        console.error('Erreur de sauvegarde :', error);
        Swal.fire('Erreur', 'Impossible de sauvegarder les notes', 'error');
        throw error;
    }
}

// Configuration des écouteurs pour les menus déroulants
function setupFormListeners(yearId, sectorId, promotionId, classroomId) {
    const yearSelect = document.getElementById(yearId);
    const sectorSelect = document.getElementById(sectorId);
    const promotionSelect = document.getElementById(promotionId);
    const classroomSelect = document.getElementById(classroomId);

    yearSelect.addEventListener('change', async () => {
        const yearId = yearSelect.value;
        if (yearId) {
            await fetchOptions(`/api/sectors-by-year/${yearId}`, sectorSelect, '-- Choisissez une filière --');
            sectorSelect.disabled = false;
        } else {
            sectorSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord l\'année --</option>';
            sectorSelect.disabled = true;
        }

        promotionSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la filière --</option>';
        promotionSelect.disabled = true;
        classroomSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la promotion --</option>';
        classroomSelect.disabled = true;
        resetNotesContainer();
    });

    sectorSelect.addEventListener('change', async () => {
        const yearId = yearSelect.value;
        const sectorId = sectorSelect.value;
        if (yearId && sectorId) {
            await fetchOptions(`/api/promotions-by-year-sector/${yearId}/${sectorId}`, promotionSelect);
            promotionSelect.disabled = false;
        } else {
            promotionSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la filière --</option>';
            promotionSelect.disabled = true;
        }

        classroomSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la promotion --</option>';
        classroomSelect.disabled = true;
        resetNotesContainer();
    });

    promotionSelect.addEventListener('change', async () => {
        const promotionId = promotionSelect.value;
        if (promotionId) {
            await fetchOptions(`/api/classes-by-promotion/${promotionId}`, classroomSelect);
            classroomSelect.disabled = false;
        } else {
            classroomSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la promotion --</option>';
            classroomSelect.disabled = true;
        }
        resetNotesContainer();
    });
}

// Réinitialise le conteneur des notes
function resetNotesContainer() {
    const container = document.getElementById('students-notes-container');
    container.innerHTML = `
        <div class="text-center py-5">
            <div class="icon-container mb-3">
                <i class="fas fa-search text-muted fs-1"></i>
            </div>
            <h5 class="text-muted">Sélectionnez les critères pour afficher les notes</h5>
            <p class="text-muted small">Choisissez une année, une filière, une promotion, une classe et un semestre</p>
        </div>`;
}

// Charge les options pour un select
async function fetchOptions(url, selectElement, placeholder = '-- Choisissez --') {
    selectElement.innerHTML = `<option value="">${placeholder}</option>`;
    selectElement.disabled = true;

    try {
        const res = await fetch(url);
        if (!res.ok) throw new Error('Erreur de chargement');

        const data = await res.json();
        if (data.length === 0) {
            selectElement.innerHTML += `<option value="" disabled>Aucune option disponible</option>`;
        } else {
            data.forEach(item => {
                selectElement.innerHTML += `<option value="${item.id}">${item.name || item.year}</option>`;
            });
        }
        selectElement.disabled = false;
    } catch (e) {
        console.error('Erreur de chargement :', e);
        selectElement.innerHTML += `<option value="" disabled>Erreur de chargement</option>`;
    }
}

// Charge les notes des étudiants
async function loadStudentNotes(classroomId, yearId, semester) {
    const container = document.getElementById('students-notes-container');
    container.innerHTML = `
        <div class="text-center my-5">
            <div class="spinner-border text-primary mb-3"></div>
            <p class="text-primary">Chargement des notes en cours...</p>
        </div>`;

    try {
        const res = await fetch(`/api/notes-by-class-semester/${classroomId}/${yearId}/${semester}`);
        if (!res.ok) throw new Error('Erreur de chargement');

        const data = await res.json();

        if (data.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-book-open fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune note disponible</h5>
                    <p class="text-muted small">Aucune note n'a été enregistrée pour cette classe et ce semestre</p>
                </div>`;
            return;
        }

        container.innerHTML = '';
        data.forEach(student => {
            const card = document.createElement('div');
            card.className = 'card mb-4 shadow-sm student-card';
            card.innerHTML = `
                <div class="card-header bg-gradient-light-primary text-primary d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <span class="avatar-initial bg-primary text-white rounded-circle">${student.name.charAt(0)}${student.surname.charAt(0)}</span>
                        </div>
                        <div>
                            <h6 class="mb-0">${student.name} ${student.surname} </h6>
                            <small class="text-muted">${student.matricule || 'N/A'}</small>
                        </div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input toggle-edit" type="checkbox" id="edit-${student.id}">
                        <label class="form-check-label" for="edit-${student.id}">Mode édition</label>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Matière</th>
                                    <th width="20%">Interros</th>
                                    <th width="15%">Devoir 1</th>
                                    <th width="15%">Devoir 2</th>
                                    <th width="20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${student.notes.map(note => `
                                    <tr data-note-id="${note.id}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-book text-primary me-2"></i>
                                                <span>${note.subject}</span>
                                            </div>
                                        </td>
                                        <td class="note-cell">${displayInterros(note.interros)}</td>
                                        <td class="note-cell">${note.devoir1 || '-'}</td>
                                        <td class="note-cell">${note.devoir2 || '-'}</td>
                                        <td class="note-actions">
                                            <span class="text-muted">-</span>
                                        </td>
                                    </tr>`).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                    <small class="text-muted">${student.notes.length} matières</small>
                </div>
            `;
            container.appendChild(card);
        });

        activateToggleSwitches();

    } catch (error) {
        console.error(error);
        container.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Erreur lors du chargement des notes. Veuillez réessayer.
            </div>`;
    }
}

// Calcule la moyenne d'un étudiant
function calculateAverage(studentId) {
    Swal.fire({
        title: 'Calcul de la moyenne',
        text: 'Cette fonctionnalité sera implémentée prochainement',
        icon: 'info',
        confirmButtonText: 'OK'
    });
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Configuration des sélecteurs
    setupFormListeners('import_year_id', 'import_sector_id', 'import_promotion_id', 'import_classroom_id');

    // Écouteur pour le changement de semestre
    document.getElementById('semester').addEventListener('change', function() {
        const classroomId = document.getElementById('import_classroom_id').value;
        const semester = this.value;
        const yearId = document.getElementById('import_year_id').value;

        if (classroomId && semester && yearId) {
            loadStudentNotes(classroomId, yearId, semester);
        }
    });

    // Validation du formulaire
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
        }
    });
});

// Animation pour les entrées
function animateCSS(element, animationName, callback) {
    const node = document.querySelector(element);
    node.classList.add('animated', animationName);

    function handleAnimationEnd() {
        node.classList.remove('animated', animationName);
        node.removeEventListener('animationend', handleAnimationEnd);
        if (typeof callback === 'function') callback();
    }

    node.addEventListener('animationend', handleAnimationEnd);
}
</script>

<style>
/* Styles personnalisés */
.card {
    transition: all 0.3s ease;
    border: none;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.card-header {
    position: relative;
    overflow: hidden;
}

.wave-shape {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 30px;
    color: rgba(255,255,255,0.2);
}

.bg-gradient-light-primary {
    background: linear-gradient(135deg, #f6f9ff 0%, #e7f1ff 100%);
}

.avatar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-initial {
    font-weight: 600;
}

.toggle-edit {
    cursor: pointer;
}

.form-switch .form-check-input:checked {
    background-color: #4e73df;
    border-color: #4e73df;
}

.note-input {
    max-width: 80px;
    display: inline-block;
}

.interros-input {
    min-width: 150px;
}

.table-hover tbody tr:hover {
    background-color: rgba(78, 115, 223, 0.05);
}

.calculate-btn:hover {
    background-color: #4e73df;
    color: white;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.student-card {
    animation: fadeIn 0.5s ease-out forwards;
    opacity: 0;
}

.student-card:nth-child(1) { animation-delay: 0.1s; }
.student-card:nth-child(2) { animation-delay: 0.2s; }
.student-card:nth-child(3) { animation-delay: 0.3s; }
/* ... etc pour autant d'étudiants que nécessaire */
</style>
@endsection
