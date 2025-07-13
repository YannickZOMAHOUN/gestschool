@extends('layouts.template')

@section('content')
{{-- SECTION PRINCIPALE --}}
<div class="container-fluid py-4">
    {{-- CARTE PRINCIPALE --}}
    <div class="card shadow-lg mb-5 border-0" style="border-radius: 15px; overflow: hidden;">

        {{-- EN-TÊTE DE LA CARTE --}}
        <div class="card-header bg-gradient-primary text-white py-3" style="border-radius: 15px 15px 0 0 !important;">
            <div class="d-flex justify-content-between align-items-center">
                {{-- Titre principal --}}
                <h5 class="mb-0 fw-light">
                    <i class="fas fa-file-import me-2"></i>Gestion des Notes
                </h5>
                {{-- Icône décorative --}}
                <i class="fas fa-star-half-alt fs-4"></i>
            </div>
        </div>

        {{-- CORPS DE LA CARTE --}}
        <div class="card-body px-4 py-4 bg-light">
            {{-- FORMULAIRE PRINCIPAL --}}
            <form method="POST" id="importForm" class="needs-validation" novalidate>
                @csrf

                {{-- SECTION DE FILTRES (Année, Filière, Promotion, Classe) --}}
                <div class="row g-3 mb-4">
                    {{-- Année scolaire --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary">Année scolaire :</label>
                        <select name="year_id" id="import_year_id" class="form-select shadow-sm rounded-pill" required>
                            <option value="">-- Choisissez une année --</option>
                            @foreach($years as $year)
                                <option value="{{ $year->id }}">{{ $year->year }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner une année scolaire</div>
                    </div>

                    {{-- Filière --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary">Filière :</label>
                        <select name="sector_id" id="import_sector_id" class="form-select shadow-sm rounded-pill" disabled required>
                            <option value="">-- Sélectionnez d'abord l'année --</option>
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner une filière</div>
                    </div>

                    {{-- Promotion --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary">Promotion :</label>
                        <select name="promotion_id" id="import_promotion_id" class="form-select shadow-sm rounded-pill" disabled required>
                            <option value="">-- Sélectionnez d'abord la filière --</option>
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner une promotion</div>
                    </div>

                    {{-- Classe --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary">Classe :</label>
                        <select name="classroom_id" id="import_classroom_id" class="form-select shadow-sm rounded-pill" disabled required>
                            <option value="">-- Sélectionnez d'abord la promotion --</option>
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner une classe</div>
                    </div>
                </div>

                {{-- SECTION MATIÈRE ET PARAMÈTRES --}}
                <div class="row mb-4">
                    {{-- Sélection de la matière --}}
                    <div class="col-md-4">
                        <label for="subject" class="form-label fw-bold text-primary">Matière</label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white"><i class="fas fa-book"></i></span>
                            <select class="form-select rounded-end" name="subject" id="subject" required>
                                <option value="">-- Choisissez une matière --</option>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner une matière</div>
                        </div>
                    </div>

                    {{-- Coefficient --}}
                    <div class="col-md-4">
                        <label for="ratio" class="form-label fw-bold text-primary">Coefficient</label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white"><i class="fas fa-weight-hanging"></i></span>
                            <input type="number" name="ratio" id="ratio" class="form-control" readonly>
                            <input type="hidden" name="ratio_id" id="ratio_id">
                        </div>
                    </div>

                    {{-- Semestre --}}
                    <div class="col-md-4">
                        <label for="semester" class="form-label fw-bold text-primary">Semestre</label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white"><i class="fas fa-calendar-alt"></i></span>
                            <select class="form-select rounded-end" name="semester" id="semester" required>
                                <option value="" disabled selected>Choisissez le semestre</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner un semestre</div>
                        </div>
                    </div>
                </div>

                {{-- SECTION TYPE DE NOTE ET NOTE GLOBALE --}}
                <div class="row mb-4">
                    {{-- Type de note --}}
                    <div class="col-md-8">
                        <label for="type" class="form-label fw-bold text-primary">Type de Note</label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white"><i class="fas fa-tasks"></i></span>
                            <select class="form-select rounded-end" name="type" id="type" required>
                                <option value="" disabled selected>Choisissez le type de note</option>
                                <option value="interro">Interrogation(s)</option>
                                <option value="devoir1">Devoir 1</option>
                                <option value="devoir2">Devoir 2</option>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner un type de note</div>
                        </div>
                    </div>

                    {{-- Note globale --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-primary">Note générale <small class="text-muted">(remplit tous les champs)</small></label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">/20</span>
                            <input type="number" id="global_note" class="form-control" min="0" max="20" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                </div>

                {{-- TABLEAU DES ÉTUDIANTS ET NOTES --}}
                <div class="table-responsive rounded-3 shadow-sm mt-4">
                    <table class="table table-hover mb-0" id="notes-table">
                        <thead class="table-primary">
                            <tr>
                                <th class="rounded-start"><i class="fas fa-user me-2"></i>Nom</th>
                                <th><i class="fas fa-user-tag me-2"></i>Prénom(s)</th>
                                <th class="rounded-end"><i class="fas fa-marker me-2"></i>Note</th>
                                <th class="rounded-end"><i class="fas fa-info-circle me-2"></i>Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white"></tbody>
                    </table>
                </div>

                {{-- BOUTONS D'ACTION --}}
                <div class="d-flex justify-content-center mt-4 gap-3">
                    <button type="reset" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="fas fa-undo me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('another_JS')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    /**
     * Affiche une notification toast
     * @param {string} icon - Icône (success, error, etc.)
     * @param {string} title - Message à afficher
     * @param {string} position - Position de la notification
     */
    function showToast(icon, title, position = 'top-end') {
        const Toast = Swal.mixin({
            toast: true,
            position: position,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        Toast.fire({ icon, title });
    }

    /**
     * Charge les options pour un select via API
     * @param {string} url - Endpoint API
     * @param {HTMLElement} selectElement - Élément select à remplir
     * @param {string} placeholder - Texte par défaut
     */
    async function fetchOptions(url, selectElement, placeholder = '-- Choisissez --') {
        selectElement.innerHTML = `<option value="">${placeholder}</option>`;
        selectElement.disabled = true;
        try {
            const res = await fetch(url);
            const data = await res.json();
            data.forEach(item => {
                selectElement.innerHTML += `<option value="${item.id}">${item.name}</option>`;
            });
            selectElement.disabled = false;
            showToast('success', 'Données chargées avec succès');
        } catch (e) {
            console.error('Erreur de chargement :', e);
            showToast('error', 'Erreur lors du chargement des données');
        }
    }

    /**
     * Configure les écouteurs pour les sélecteurs hiérarchiques
     * @param {string} yearId - ID du select année
     * @param {string} sectorId - ID du select filière
     * @param {string} promotionId - ID du select promotion
     * @param {string} classroomId - ID du select classe
     */
    function setupFormListeners(yearId, sectorId, promotionId, classroomId) {
        const yearSelect = document.getElementById(yearId);
        const sectorSelect = document.getElementById(sectorId);
        const promotionSelect = document.getElementById(promotionId);
        const classroomSelect = document.getElementById(classroomId);

        // Changement d'année
        yearSelect.addEventListener('change', () => {
            const yearId = yearSelect.value;
            if (yearId) fetchOptions(`/api/sectors-by-year/${yearId}`, sectorSelect, '-- Choisissez une filière --');
            promotionSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la filière --</option>';
            promotionSelect.disabled = true;
            classroomSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la promotion --</option>';
            classroomSelect.disabled = true;
        });

        // Changement de filière
        sectorSelect.addEventListener('change', () => {
            const yearId = yearSelect.value;
            const sectorId = sectorSelect.value;
            if (yearId && sectorId) fetchOptions(`/api/promotions-by-year-sector/${yearId}/${sectorId}`, promotionSelect);
            classroomSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la promotion --</option>';
            classroomSelect.disabled = true;
        });

        // Changement de promotion
        promotionSelect.addEventListener('change', () => {
            const promotionId = promotionSelect.value;
            if (promotionId) fetchOptions(`/api/classes-by-promotion/${promotionId}`, classroomSelect);
        });
    }

    // Initialisation quand le DOM est chargé
    document.addEventListener('DOMContentLoaded', function () {
        // Validation Bootstrap
        (function () {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        // Configuration des sélecteurs hiérarchiques
        setupFormListeners('import_year_id', 'import_sector_id', 'import_promotion_id', 'import_classroom_id');

        // Références aux éléments DOM
        const classroomSelect = document.getElementById('import_classroom_id');
        const yearSelect = document.getElementById('import_year_id');
        const subjectSelect = document.getElementById('subject');
        const ratioInput = document.getElementById('ratio');
        const ratioIdInput = document.getElementById('ratio_id');
        const semesterSelect = document.getElementById('semester');
        const typeSelect = document.getElementById('type');
        const notesTableBody = document.querySelector('#notes-table tbody');
        const globalNoteInput = document.getElementById('global_note');

        // Changement de classe
        classroomSelect.addEventListener('change', async function () {
            const classroomId = this.value;
            const yearId = yearSelect.value;
            if (!classroomId || !yearId) return;

            // Affichage du loader
            notesTableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="mt-2 text-muted">Chargement des étudiants...</p>
                    </td>
                </tr>`;

            try {
                // Chargement parallèle des étudiants et des matières
                const [studentsRes, ratiosRes] = await Promise.all([
                    fetch(`/api/students-by-class/${classroomId}/${yearId}`),
                    fetch(`/api/subjects-with-ratios/${classroomId}/${yearId}`)
                ]);

                const students = await studentsRes.json();
                const ratios = await ratiosRes.json();

                // Remplissage du tableau des étudiants
                notesTableBody.innerHTML = '';
                students.forEach(student => {
                    const row = document.createElement('tr');
                    row.className = 'align-middle';
                    row.innerHTML = `
                        <td>${student.name}</td>
                        <td>${student.surname}</td>
                        <td>
                            <input type="hidden" name="students[]" value="${student.recording_id}">
                            <div class="input-group">
                                <input type="number" name="grades[]" class="form-control grade-input" step="0.01" min="0" max="20" placeholder="0.00">
                                <span class="input-group-text">/20</span>
                            </div>
                        </td>
                        <td class="status-cell">
                            <span class="badge bg-secondary">Actif</span>
                        </td>`;
                    notesTableBody.appendChild(row);
                });

                // Remplissage des matières
                subjectSelect.innerHTML = `<option value="">-- Choisissez une matière --</option>`;
                ratios.forEach(r => {
                    const option = document.createElement('option');
                    option.value = r.id;
                    option.textContent = r.subject.name;
                    option.dataset.coefficient = r.coefficient;
                    option.dataset.subjectId = r.subject.id;
                    option.dataset.subjectName = r.subject.name.toLowerCase(); // Pour vérifier EPS
                    subjectSelect.appendChild(option);
                });

                showToast('success', 'Étudiants et matières chargés avec succès');
            } catch (error) {
                console.error('Erreur:', error);
                notesTableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-danger py-4">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <p>Erreur lors du chargement des données</p>
                        </td>
                    </tr>`;
                showToast('error', 'Erreur lors du chargement des données');
            }
        });

        // Changement de matière
        subjectSelect.addEventListener('change', async function () {
            const opt = this.options[this.selectedIndex];
            ratioInput.value = opt.getAttribute('data-coefficient');
            ratioIdInput.value = opt.value;

            // Vérifier si la matière est EPS
            const isEPS = opt.getAttribute('data-subject-name') === 'eps';

            // Récupérer les dispenses si c'est EPS
            if (isEPS) {
                try {
                    const classroomId = classroomSelect.value;
                    const yearId = yearSelect.value;
                    const res = await fetch(`/api/students-dispensations/${classroomId}/${yearId}`);
                    const dispensations = await res.json();

                    // Mettre à jour le statut des étudiants
                    document.querySelectorAll('tr').forEach((row, index) => {
                        if (index > 0) { // Skip header row
                            const recordingId = row.querySelector('input[name="students[]"]').value;
                            const statusCell = row.querySelector('.status-cell');
                            const gradeInput = row.querySelector('.grade-input');

                            if (dispensations.includes(parseInt(recordingId))) {
                                statusCell.innerHTML = '<span class="badge bg-warning text-dark">Dispensé</span>';
                                gradeInput.value = '';
                                gradeInput.disabled = true;
                                gradeInput.placeholder = 'Dispensé';
                                gradeInput.classList.add('bg-light');
                            } else {
                                statusCell.innerHTML = '<span class="badge bg-success">Actif</span>';
                                gradeInput.disabled = false;
                                gradeInput.placeholder = '0.00';
                                gradeInput.classList.remove('bg-light');
                            }
                        }
                    });

                    showToast('info', 'Statuts des étudiants mis à jour pour EPS');
                } catch (error) {
                    console.error('Erreur:', error);
                    showToast('error', 'Erreur lors du chargement des dispenses');
                }
            } else {
                // Réinitialiser tous les champs si ce n'est pas EPS
                document.querySelectorAll('.grade-input').forEach(input => {
                    input.disabled = false;
                    input.placeholder = '0.00';
                    input.classList.remove('bg-light');
                });
                document.querySelectorAll('.status-cell').forEach(cell => {
                    cell.innerHTML = '<span class="badge bg-success">Actif</span>';
                });
            }

            // Préremplissage des notes existantes pour les devoirs
            const type = typeSelect.value;
            if (type === 'devoir1' || type === 'devoir2') {
                const recording_ids = [...document.querySelectorAll('input[name="students[]"]')].map(el => el.value);

                try {
                    const res = await fetch('/api/notes/existing', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: JSON.stringify({
                            semester: semesterSelect.value,
                            type: type,
                            ratio_id: opt.value,
                            subject_id: opt.getAttribute('data-subject-id'),
                            recording_ids: recording_ids
                        })
                    });

                    if (!res.ok) throw new Error('Erreur réseau');

                    const data = await res.json();
                    document.querySelectorAll('input[name="grades[]"]').forEach((input, index) => {
                        if (!input.disabled) { // Ne pas remplir pour les dispensés
                            const rid = recording_ids[index];
                            if (data[rid]) input.value = data[rid];
                        }
                    });

                    showToast('info', 'Notes existantes chargées');
                } catch (error) {
                    console.error('Erreur:', error);
                    showToast('error', 'Erreur lors du chargement des notes existantes');
                }
            }
        });

        // Navigation au clavier dans les notes
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const inputs = [...document.querySelectorAll('.grade-input:not(:disabled)')];
                const index = inputs.indexOf(document.activeElement);

                if (index > -1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                } else if (index === inputs.length - 1) {
                    document.getElementById('importForm').dispatchEvent(new Event('submit'));
                }
            }
        });

        // Note globale
        globalNoteInput.addEventListener('input', () => {
            const val = globalNoteInput.value;
            if (val >= 0 && val <= 20) {
                document.querySelectorAll('input[name="grades[]"]').forEach(input => {
                    if (!input.disabled) { // Ne pas remplir pour les dispensés
                        input.value = val;
                        input.classList.add('bg-success-light');
                        setTimeout(() => input.classList.remove('bg-success-light'), 1000);
                    }
                });
            }
        });

        // Soumission du formulaire
        document.getElementById('importForm').addEventListener('submit', function (e) {
            e.preventDefault();

            // Validation des notes
            const grades = [...document.querySelectorAll('input[name="grades[]"]:not(:disabled)')];
            const isValid = grades.every(input => {
                const val = parseFloat(input.value);
                return !isNaN(val) && val >= 0 && val <= 20;
            });

            if (!isValid) {
                showToast('error', 'Veuillez entrer des notes valides (entre 0 et 20)');
                return;
            }

            // Animation pendant l'envoi
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Enregistrement...';
            submitBtn.disabled = true;

            const formData = new FormData(this);

            // Envoi des données
            fetch('/note', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès',
                        text: data.message,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });

                    // Réinitialisation partielle
                    subjectSelect.selectedIndex = 0;
                    ratioInput.value = '';
                    ratioIdInput.value = '';
                    globalNoteInput.value = '';
                    document.querySelectorAll('input[name="grades[]"]').forEach(input => input.value = '');
                } else {
                    throw new Error(data.message || 'Erreur inconnue.');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: error.message,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    });
</script>

{{-- STYLES CSS --}}
<style>
    /* Dégradé de couleur pour l'en-tête */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #3a7bd5 0%, #00d2ff 100%) !important;
    }

    /* Style pour les notes mises à jour */
    .bg-success-light {
        background-color: rgba(40, 167, 69, 0.1) !important;
        transition: background-color 0.5s ease;
    }

    /* Style de base pour les formulaires */
    .bg-form {
        background-color: #f8f9fa;
    }

    /* Style pour l'en-tête du tableau */
    .table-primary th {
        background-color: #3a7bd5;
        color: white;
    }

    /* Bordures arrondies */
    .rounded-start {
        border-top-left-radius: 10px !important;
        border-bottom-left-radius: 10px !important;
    }

    .rounded-end {
        border-top-right-radius: 10px !important;
        border-bottom-right-radius: 10px !important;
    }

    /* Effet de carte */
    .card {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    /* Focus sur les champs de formulaire */
    .form-control:focus, .form-select:focus {
        border-color: #3a7bd5;
        box-shadow: 0 0 0 0.25rem rgba(58, 123, 213, 0.25);
    }

    /* Animation pour les groupes d'input */
    .input-group-text {
        transition: all 0.3s ease;
    }

    .input-group:focus-within .input-group-text {
        background-color: #2c5fb3;
    }

    /* Style spécifique pour les champs de note */
    .grade-input:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }

    /* Style pour les badges de statut */
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
        font-weight: 500;
    }

    /* Style pour les champs désactivés */
    .grade-input:disabled {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }
</style>
@endsection
