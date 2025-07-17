@extends('layouts.template')

@section('content')
<div class="container-fluid py-4">
    <!-- Import Section -->
    <div class="card shadow-lg mb-5">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-file-import me-2"></i>Importation de la liste des élèves
                </h5>
                <a class="btn btn-light btn-sm" href="{{ route('student.index') }}">
                    <i class="fas fa-list me-1"></i> Liste des Elèves
                </a>
            </div>
        </div>

        <div class="card-body px-4 py-4">
            <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Année scolaire :</label>
                        <select name="year_id" id="import_year_id" class="form-select shadow-sm" required>
                            <option value="">-- Choisissez une année --</option>
                            @foreach($years as $year)
                                <option value="{{ $year->id }}" @selected(old('year_id') == $year->id)>{{ $year->year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filière :</label>
                        <select name="sector_id" id="import_sector_id" class="form-select shadow-sm" disabled required>
                            <option value="">-- Sélectionnez d'abord l'année --</option>
                            @if(old('sector_id'))
                                <option value="{{ old('sector_id') }}" selected>{{ old('sector_name') }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Promotion :</label>
                        <select name="promotion_id" id="import_promotion_id" class="form-select shadow-sm" disabled required>
                            <option value="">-- Sélectionnez d'abord la filière --</option>
                            @if(old('promotion_id'))
                                <option value="{{ old('promotion_id') }}" selected>{{ old('promotion_name') }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Classe :</label>
                        <select name="classroom_id" id="import_classroom_id" class="form-select shadow-sm" disabled required>
                            <option value="">-- Sélectionnez d'abord la promotion --</option>
                            @if(old('classroom_id'))
                                <option value="{{ old('classroom_id') }}" selected>{{ old('classroom_name') }}</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="file" class="form-label fw-bold">Fichier des élèves:</label>
                    <div class="file-upload-area border rounded p-3 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="file-thumbnail me-3">
                                <i class="fas fa-file-excel text-success fa-3x"></i>
                            </div>
                            <div class="file-input flex-grow-1">
                                <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" required accept=".xlsx,.xls">
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Formats acceptés: .xlsx, .xls (Max 5MB)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="reset" class="btn btn-outline-secondary me-3 px-4">
                        <i class="fas fa-undo me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-upload me-2"></i>Importer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Student Section -->
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0">
                <i class="fas fa-user-plus me-2"></i>Ajouter un élève
            </h5>
        </div>

        <div class="card-body px-4 py-4">
            <form id="addStudentForm" action="{{ route('student.store') }}" method="POST">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Année scolaire:</label>
                        <select name="year_id" id="add_year_id" class="form-select shadow-sm @error('year_id') is-invalid @enderror" required>
                            <option value="">-- Choisissez une année --</option>
                            @foreach($years as $year)
                                <option value="{{ $year->id }}" @selected(old('year_id') == $year->id)>{{ $year->year }}</option>
                            @endforeach
                        </select>
                        @error('year_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filière :</label>
                        <select name="sector_id" id="add_sector_id" class="form-select shadow-sm @error('sector_id') is-invalid @enderror" disabled required>
                            <option value="">-- Sélectionnez d'abord l'année --</option>
                            @if(old('sector_id'))
                                <option value="{{ old('sector_id') }}" selected>{{ old('sector_name') }}</option>
                            @endif
                        </select>
                        @error('sector_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Promotion :</label>
                        <select name="promotion_id" id="add_promotion_id" class="form-select shadow-sm @error('promotion_id') is-invalid @enderror" disabled required>
                            <option value="">-- Sélectionnez d'abord la filière --</option>
                            @if(old('promotion_id'))
                                <option value="{{ old('promotion_id') }}" selected>{{ old('promotion_name') }}</option>
                            @endif
                        </select>
                        @error('promotion_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Classe :</label>
                        <select name="classroom_id" id="add_classroom_id" class="form-select shadow-sm @error('classroom_id') is-invalid @enderror" disabled required>
                            <option value="">-- Sélectionnez d'abord la promotion --</option>
                            @if(old('classroom_id'))
                                <option value="{{ old('classroom_id') }}" selected>{{ old('classroom_name') }}</option>
                            @endif
                        </select>
                        @error('classroom_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="matricule" class="form-label fw-bold">Matricule</label>
                        <input type="text" name="matricule" id="matricule"
                               class="form-control shadow-sm @error('matricule') is-invalid @enderror"
                               value="{{ old('matricule') }}" required>
                        @error('matricule')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="sex" class="form-label fw-bold">Sexe</label>
                        <select name="sex" id="sex" class="form-select shadow-sm @error('sex') is-invalid @enderror" required>
                            <option value="M" @selected(old('sex') == 'M')>Masculin</option>
                            <option value="F" @selected(old('sex') == 'F')>Féminin</option>
                        </select>
                        @error('sex')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold">Nom</label>
                        <input type="text" name="name" id="name"
                               class="form-control shadow-sm @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="surname" class="form-label fw-bold">Prénom</label>
                        <input type="text" name="surname" id="surname"
                               class="form-control shadow-sm @error('surname') is-invalid @enderror"
                               value="{{ old('surname') }}" required>
                        @error('surname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="birthday" class="form-label fw-bold">Date de Naissance</label>
                        <div class="input-group">
                            <input type="date" name="birthday" id="birthday"
                                   class="form-control shadow-sm @error('birthday') is-invalid @enderror"
                                   value="{{ old('birthday') }}">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        @error('birthday')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="birthplace" class="form-label fw-bold">Lieu de Naissance</label>
                        <input type="text" name="birthplace" id="birthplace"
                               class="form-control shadow-sm @error('birthplace') is-invalid @enderror"
                               value="{{ old('birthplace') }}">
                        @error('birthplace')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="number" class="form-label fw-bold">Contact</label>
                        <div class="input-group">
                            <span class="input-group-text">+229 01</span>
                            <input type="text" name="number" id="number"
                                   class="form-control shadow-sm @error('number') is-invalid @enderror"
                                   maxlength="11" placeholder="00 00 00 00"
                                   value="{{ old('number') }}"
                                   oninput="formatPhone(this)">
                        </div>
                        @error('number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="aptitude" class="form-label fw-bold">EPS</label>
                        <select name="aptitude" id="aptitude"
                                class="form-select shadow-sm @error('aptitude') is-invalid @enderror" required>
                            <option value="Apte" @selected(old('aptitude') == 'Apte')>Apte</option>
                            <option value="Inapte" @selected(old('aptitude') == 'Inapte')>Inapte</option>
                        </select>
                        @error('aptitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="reset" class="btn btn-outline-secondary me-3 px-4">
                        <i class="fas fa-undo me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('another_JS')
<script>
    // Fonction générique de chargement dynamique
    async function fetchOptions(url, selectElement, placeholder = '-- Choisissez --') {
        const currentValue = selectElement.dataset.currentValue;
        selectElement.innerHTML = `<option value="">${placeholder}</option>`;
        selectElement.disabled = true;

        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error('Erreur réseau');

            const data = await res.json();
            data.forEach(item => {
                const option = new Option(item.name, item.id);
                option.selected = (currentValue && currentValue == item.id);
                selectElement.add(option);
            });
            selectElement.disabled = false;
        } catch (e) {
            console.error('Erreur de chargement :', e);
            showToast('error', 'Erreur de chargement des données');
        }
    }

    // Configuration des écouteurs pour un formulaire
    function setupFormListeners(yearId, sectorId, promotionId, classroomId) {
        const yearSelect = document.getElementById(yearId);
        const sectorSelect = document.getElementById(sectorId);
        const promotionSelect = document.getElementById(promotionId);
        const classroomSelect = document.getElementById(classroomId);

        // Stocker les valeurs actuelles pour le rechargement
        if (sectorSelect.value) sectorSelect.dataset.currentValue = sectorSelect.value;
        if (promotionSelect.value) promotionSelect.dataset.currentValue = promotionSelect.value;
        if (classroomSelect.value) classroomSelect.dataset.currentValue = classroomSelect.value;

        yearSelect.addEventListener('change', () => {
            const yearId = yearSelect.value;
            if (yearId) {
                fetchOptions(`/api/sectors-by-year/${yearId}`, sectorSelect, '-- Choisissez une filière --');
            }
            promotionSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la filière --</option>';
            promotionSelect.disabled = true;
            classroomSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la promotion --</option>';
            classroomSelect.disabled = true;
        });

        sectorSelect.addEventListener('change', () => {
            const yearId = yearSelect.value;
            const sectorId = sectorSelect.value;
            if (yearId && sectorId) {
                fetchOptions(`/api/promotions-by-year-sector/${yearId}/${sectorId}`, promotionSelect, '-- Choisissez une promotion --');
            }
            classroomSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la promotion --</option>';
            classroomSelect.disabled = true;
        });

        promotionSelect.addEventListener('change', () => {
            const promotionId = promotionSelect.value;
            if (promotionId) {
                fetchOptions(`/api/classes-by-promotion/${promotionId}`, classroomSelect, '-- Choisissez une classe --');
            }
        });
    }

    // Initialisation des deux formulaires
    document.addEventListener('DOMContentLoaded', function() {
        // Formulaire d'import
        setupFormListeners(
            'import_year_id',
            'import_sector_id',
            'import_promotion_id',
            'import_classroom_id'
        );

        // Formulaire d'ajout
        setupFormListeners(
            'add_year_id',
            'add_sector_id',
            'add_promotion_id',
            'add_classroom_id'
        );

        // Si des valeurs existent déjà (après validation échouée), recharger les selects
        if (document.getElementById('add_year_id').value) {
            document.getElementById('add_year_id').dispatchEvent(new Event('change'));
        }
        if (document.getElementById('import_year_id').value) {
            document.getElementById('import_year_id').dispatchEvent(new Event('change'));
        }

        // Effacer uniquement le champ de classe après soumission
        document.getElementById('addStudentForm').addEventListener('submit', function() {
            setTimeout(() => {
                document.getElementById('add_classroom_id').value = '';
            }, 100);
        });
    });

    function formatPhone(input) {
        let numbers = input.value.replace(/\D/g, ''); // retire tout ce qui n'est pas chiffre
        if (numbers.length > 8) numbers = numbers.slice(0, 8); // limite à 8 chiffres (4 groupes)

        let formatted = '';
        for (let i = 0; i < numbers.length; i += 2) {
            if (i > 0) formatted += ' ';
            formatted += numbers.substring(i, i + 2);
        }

        input.value = formatted;
    }

    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.style.zIndex = '1100';

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
</script>

<style>
    .card {
        border-radius: 10px;
        border: none;
    }

    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }

    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px 15px;
        border: 1px solid #dee2e6;
        transition: all 0.3s;
    }

    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }

    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }

    .file-upload-area {
        border-radius: 8px;
        transition: all 0.3s;
    }

    .file-upload-area:hover {
        background-color: #f8f9fa;
    }

    .btn {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: white;
    }

    .is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875em;
    }

    .toast {
        min-width: 250px;
    }
</style>
@endsection
