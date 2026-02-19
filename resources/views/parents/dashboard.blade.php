<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail Parents - Résultats Scolaires</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #4cc9f0;
            --light-bg: #f8f9fa;
            --dark-text: #2b2d42;
            --success-color: #4caf50;
            --danger-color: #f44336;
        }

        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', 'Roboto', sans-serif;
            color: var(--dark-text);
            line-height: 1.6;
        }

        .card {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 2rem;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 1.5rem;
            border-bottom: none;
        }

        .form-select, .form-control {
            border-radius: 10px;
            padding: 0.75rem 1.25rem;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .form-select:focus, .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(76, 201, 240, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2.5rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .btn-primary:hover::after {
            left: 100%;
        }

        .btn-print {
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            transition: all 0.3s;
        }

        .btn-print:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1rem;
        }

        .table tbody tr {
            transition: background-color 0.2s;
        }

        .table tbody tr:hover {
            background-color: rgba(76, 201, 240, 0.05);
        }

        .table tfoot td {
            font-weight: 600;
            background-color: #f1f3f5;
        }

        .admis {
            background-color: rgba(76, 175, 80, 0.1) !important;
            color: var(--success-color);
        }

        .non-admis {
            background-color: rgba(244, 67, 54, 0.1) !important;
            color: var(--danger-color);
        }

        .loading-spinner {
            display: none;
            width: 1.5rem;
            height: 1.5rem;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .school-header {
            text-align: center;
            margin-bottom: 2.5rem;
            padding-top: 1.5rem;
        }

        .school-logo {
            height: 90px;
            margin-bottom: 1rem;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .school-name {
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 0.25rem;
            font-size: 1.75rem;
        }

        .page-title {
            font-weight: 600;
            color: var(--dark-text);
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .footer {
            text-align: center;
            padding: 1.5rem 0;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .input-group-text {
            background-color: white;
            border-right: none;
        }

        .form-control.is-invalid {
            border-left: none;
        }

        .result-card {
            border-left: 4px solid var(--primary-color);
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: none;
            }

            .card {
                box-shadow: none;
                border: 1px solid #dee2e6;
            }
        }
    </style>
</head>
<body>
    <div class="container py-4 py-lg-5">
        <!-- En-tête de l'école -->
        <div class="school-header">
            <img src="https://via.placeholder.com/90x90" alt="Logo École" class="school-logo">
            <h1 class="school-name">LYCEE TECHNIQUE DE BOHICON</h1>
            <p class="page-title">Portail de consultation des résultats scolaires</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <!-- Carte du formulaire -->
                <div class="card no-print">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 text-white"><i class="fas fa-search me-2"></i>Rechercher les résultats</h4>
                            <a href="{{ route('login') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-user-circle me-1"></i> Se Connnecter
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-4 p-lg-5">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('parents.results') }}" class="needs-validation" novalidate>
                            @csrf
                            <div class="row g-3 g-lg-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Année scolaire</label>
                                    <select name="year_id" id="year_id" class="form-select" required>
                                        <option value="">-- Choisissez une année --</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year->id }}" {{ old('year_id', request('year_id')) == $year->id ? 'selected' : '' }}>{{ $year->year }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Filière</label>
                                    <select name="sector_id" id="sector_id" class="form-select" disabled required>
                                        <option value="">-- Sélectionnez d'abord l'année --</option>
                                        @if(isset($request) && $request->sector_id))
                                            <option value="{{ $request->sector_id }}" selected>{{ $request->sector_id }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Promotion</label>
                                    <select name="promotion_id" id="promotion_id" class="form-select" disabled required>
                                        <option value="">-- Sélectionnez d'abord la filière --</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Classe</label>
                                    <select name="classroom_id" id="classroom_id" class="form-select" disabled required>
                                        <option value="">-- Sélectionnez d'abord la promotion --</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Semestre</label>
                                    <select class="form-select" name="semester" id="semester" required>
                                        <option value="" disabled selected>-- Sélectionnez --</option>
                                        <option value="1" {{ old('semester', request('semester')) == '1' ? 'selected' : '' }}>Semestre 1</option>
                                        <option value="2" {{ old('semester', request('semester')) == '2' ? 'selected' : '' }}>Semestre 2</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Matricule de l'élève <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        <input type="text" name="matricule" id="matricule"
                                            class="form-control @error('matricule') is-invalid @enderror"
                                            value="{{ old('matricule', request('matricule')) }}"
                                            required
                                            placeholder="Ex: 2023A001">
                                        @error('matricule')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4 pt-2">
                                <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                                    <span id="submitText"><i class="fas fa-search me-2"></i> Consulter les résultats</span>
                                    <div class="loading-spinner" id="loadingSpinner"></div>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Section des résultats -->
                @if(isset($results))
                <div class="card result-card mt-4" id="resultsSection">
                    <div class="card-header bg-white">
                        <h4 class="mb-0 text-primary">
                            <i class="fas fa-chart-line me-2"></i>
                            Résultats scolaires
                            <small class="text-muted fs-6">- Semestre {{ $request->semester }}</small>
                        </h4>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="mb-2"><i class="fas fa-user-graduate me-2 text-primary"></i><strong>Élève :</strong> {{ $student->name }} {{ $student->surname }}</p>
                                <p class="mb-2"><i class="fas fa-id-card me-2 text-primary"></i><strong>Matricule :</strong> {{ $student->matricule }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><i class="fas fa-calendar-alt me-2 text-primary"></i><strong>Année :</strong> {{ $year->year ?? 'Non spécifié' }}</p>
                                <p class="mb-2"><i class="fas fa-users me-2 text-primary"></i><strong>Classe :</strong> {{ $classroom->name ?? 'Non spécifié' }}</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Matières</th>
                                        <th class="text-center">Coef</th>
                                        <th>Interros</th>
                                        <th class="text-center">Moy. Interros</th>
                                        <th class="text-center">Devoir 1</th>
                                        <th class="text-center">Devoir 2</th>
                                        <th class="text-center">Moyenne/20</th>
                                        <th class="text-center">Moy. Coef</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $result)
                                    <tr>
                                        <td>{{ $result['subject'] }}</td>
                                        <td class="text-center">{{ $result['coefficient'] }}</td>
                                        <td>
                                            @if(count($result['interros']) > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($result['interros'] as $note)
                                                        <span class="badge bg-primary rounded-pill">{{ $note }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ number_format($result['interros_average'], 2) }}</td>
                                        <td class="text-center">{{ number_format($result['devoir1']) }}</td>
                                        <td class="text-center">{{ number_format($result['devoir2']) }}</td>
                                        <td class="text-center fw-bold">{{ number_format($result['subject_average'], 2) }}</td>
                                        <td class="text-center">{{ number_format($result['weighted_average'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">MOYENNE GENERALE</th>
                                        <th colspan="2" class="text-center fw-bold fs-5 text-primary">
                                            {{ number_format($generalAverage, 2) }}/20
                                        </th>
                                    </tr>
                                    @if($request->semester == 2)
                                    <tr class="{{ $isPassed ? 'admis' : 'non-admis' }}">
                                        <td colspan="6" class="text-center fw-bold py-3">
                                            <i class="fas fa-{{ $isPassed ? 'check-circle' : 'times-circle' }} me-2"></i>
                                            {{ $isPassed ? 'ADMIS(E) AU SEMESTRE SUIVANT' : 'NON ADMIS(E) - DOIT REDOUBLER' }}
                                        </td>
                                    </tr>
                                    @endif
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top">
    <div class="alert alert-light mb-0">
        <i class="fas fa-info-circle me-2 text-primary"></i>
        <small>La moyenne générale est calculée en tenant compte des coefficients de chaque matière.</small>
    </div>
    <button class="btn btn-print no-print" onclick="exportPdf()">
        <i class="fas fa-file-pdf me-2"></i>Exporter PDF
    </button>
</div>
                    </div>
                </div>
                @endif

                <!-- Pied de page -->
                <div class="footer no-print">
                    <p class="mb-1">Besoin d'aide ? Contactez le service scolaire : <a href="tel:+229 0168374902" class="text-decoration-none"><i class="fas fa-phone-alt me-1"></i>+229 01 68 37 49 02</a></p>
                    <p class="mb-0 small">© {{ date('Y') }} School Manager - Tous droits réservés</p>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Gestion des sélecteurs en cascade
        const yearSelect = document.getElementById('year_id');
        const sectorSelect = document.getElementById('sector_id');
        const promotionSelect = document.getElementById('promotion_id');
        const classroomSelect = document.getElementById('classroom_id');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const loadingSpinner = document.getElementById('loadingSpinner');

        // Fonction pour charger les filières
        const loadSectors = () => {
            const yearId = yearSelect.value;
            if (!yearId) return;

            sectorSelect.innerHTML = `<option value="">Chargement...</option>`;
            sectorSelect.disabled = true;

            fetch(`/parents/get-sectors/${yearId}`)
                .then(res => res.json())
                .then(data => {
                    let options = `<option value="">-- Choisissez une filière --</option>`;
                    data.forEach(sector => {
                        const selected = "{{ old('sector_id', request('sector_id')) }}" == sector.id ? 'selected' : '';
                        options += `<option value="${sector.id}" ${selected}>${sector.name}</option>`;
                    });
                    sectorSelect.innerHTML = options;
                    sectorSelect.disabled = false;

                    // Si une valeur est présélectionnée, déclencher le changement
                    if (sectorSelect.value) {
                        sectorSelect.dispatchEvent(new Event('change'));
                    }
                })
                .catch(err => {
                    console.error('Erreur:', err);
                    sectorSelect.innerHTML = `<option value="">Erreur de chargement</option>`;
                });
        };

        // Fonction pour charger les promotions
        const loadPromotions = () => {
            const yearId = yearSelect.value;
            const sectorId = sectorSelect.value;
            if (!yearId || !sectorId) return;

            promotionSelect.innerHTML = '<option value="">Chargement...</option>';
            promotionSelect.disabled = true;

            fetch(`/parents/get-promotions/${yearId}/${sectorId}`)
                .then(res => res.json())
                .then(data => {
                    let options = `<option value="">-- Choisissez une promotion --</option>`;
                    data.forEach(p => {
                        const selected = "{{ old('promotion_id', request('promotion_id')) }}" == p.id ? 'selected' : '';
                        options += `<option value="${p.id}" ${selected}>${p.name}</option>`;
                    });
                    promotionSelect.innerHTML = options;
                    promotionSelect.disabled = false;

                    if (promotionSelect.value) {
                        promotionSelect.dispatchEvent(new Event('change'));
                    }
                })
                .catch(err => {
                    console.error('Erreur:', err);
                    promotionSelect.innerHTML = `<option value="">Erreur de chargement</option>`;
                });
        };

        // Fonction pour charger les classes
        const loadClassrooms = () => {
            const yearId = yearSelect.value;
            const sectorId = sectorSelect.value;
            const promotionId = promotionSelect.value;
            if (!yearId || !sectorId || !promotionId) return;

            classroomSelect.innerHTML = '<option value="">Chargement...</option>';
            classroomSelect.disabled = true;

            fetch(`/parents/get-classes/${yearId}/${sectorId}/${promotionId}`)
                .then(res => res.json())
                .then(data => {
                    let options = `<option value="">-- Choisissez une classe --</option>`;
                    data.forEach(cls => {
                        const selected = "{{ old('classroom_id', request('classroom_id')) }}" == cls.id ? 'selected' : '';
                        options += `<option value="${cls.id}" ${selected}>${cls.name}</option>`;
                    });
                    classroomSelect.innerHTML = options;
                    classroomSelect.disabled = false;
                })
                .catch(err => {
                    console.error('Erreur:', err);
                    classroomSelect.innerHTML = `<option value="">Erreur de chargement</option>`;
                });
        };

        // Écouteurs d'événements
        yearSelect.addEventListener('change', loadSectors);
        sectorSelect.addEventListener('change', loadPromotions);
        promotionSelect.addEventListener('change', loadClassrooms);

        // Gestion du bouton de soumission
        document.querySelector('form').addEventListener('submit', function() {
            submitText.style.display = 'none';
            loadingSpinner.style.display = 'inline-block';
            submitBtn.disabled = true;
        });

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
        })();

        // Au chargement de la page, si des valeurs existent, charger les sélecteurs
        document.addEventListener('DOMContentLoaded', function() {
            if (yearSelect.value) {
                loadSectors();
            }

            // Scroll vers les résultats si présents
            @if(isset($results))
            setTimeout(() => {
                const resultsSection = document.getElementById('resultsSection');
                if (resultsSection) {
                    resultsSection.scrollIntoView({ behavior: 'smooth' });
                }
            }, 300);
            @endif
        });
        function exportPdf() {
    // Afficher un indicateur de chargement
    const btn = document.querySelector('.btn-print');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Génération du PDF...';
    btn.disabled = true;

    // Récupérer les paramètres de recherche
    const formData = new FormData(document.querySelector('form'));
    const params = new URLSearchParams();

    for (const [key, value] of formData.entries()) {
        params.append(key, value);
    }

    // Faire la requête pour générer le PDF
    fetch(`/parents/export-pdf?${params.toString()}`)
        .then(response => {
            if (!response.ok) throw new Error('Erreur lors de la génération');
            return response.blob();
        })
        .then(blob => {
            // Créer un lien pour télécharger le PDF
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `resultats-${formData.get('matricule')}-semestre-${formData.get('semester')}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            a.remove();
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la génération du PDF');
        })
        .finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
}
    </script>
</body>
</html>
