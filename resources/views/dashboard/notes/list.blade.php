@extends('layouts.template')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg mb-5 border-0" style="border-radius:15px;overflow:hidden;">

        {{-- EN-TÊTE --}}
        <div class="card-header bg-primary text-white py-3 position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-light">
                    <i class="fas fa-file-import me-2"></i>Voir les notes
                </h5>
            </div>
        </div>

        {{-- CORPS --}}
        <div class="card-body px-4 py-4 bg-light">

            {{-- FORMULAIRE DE FILTRAGE --}}
            <form method="GET" id="filterForm" class="needs-validation" novalidate>
                @csrf
                <div class="row g-3 mb-4">

                    {{-- Année --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary">
                            <i class="fas fa-calendar-alt me-2"></i>Année scolaire
                        </label>
                        <select name="year_id" id="import_year_id" class="form-select shadow-sm" required>
                            <option value="">-- Choisissez une année --</option>
                            @foreach($years as $year)
                                <option value="{{ $year->id }}" {{ request('year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->year }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner une année scolaire.</div>
                    </div>

                    {{-- Filière --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary">
                            <i class="fas fa-graduation-cap me-2"></i>Filière
                        </label>
                        <select name="sector_id" id="import_sector_id" class="form-select shadow-sm"
                                {{ !request('year_id') ? 'disabled' : '' }} required>
                            <option value="">-- Sélectionnez d'abord l'année --</option>
                            @if(request('year_id'))
                                @foreach($sectorsForFilter as $sector)
                                    <option value="{{ $sector->id }}" {{ request('sector_id') == $sector->id ? 'selected' : '' }}>
                                        {{ $sector->name_sector }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner une filière.</div>
                    </div>

                    {{-- Promotion --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary">
                            <i class="fas fa-users me-2"></i>Promotion
                        </label>
                        <select name="promotion_id" id="import_promotion_id" class="form-select shadow-sm"
                                {{ !request('sector_id') ? 'disabled' : '' }} required>
                            <option value="">-- Sélectionnez d'abord la filière --</option>
                            @if(request('year_id') && request('sector_id'))
                                @foreach($promotionsForFilter as $promotion)
                                    <option value="{{ $promotion->id }}" {{ request('promotion_id') == $promotion->id ? 'selected' : '' }}>
                                        {{ $promotion->promotion_sector }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner une promotion.</div>
                    </div>

                    {{-- Classe --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary">
                            <i class="fas fa-door-open me-2"></i>Classe
                        </label>
                        <select name="classroom_id" id="import_classroom_id" class="form-select shadow-sm"
                                {{ !request('promotion_id') ? 'disabled' : '' }} required>
                            <option value="">-- Sélectionnez d'abord la promotion --</option>
                            @if(request('promotion_id'))
                                @foreach($classroomsForFilter as $classroom)
                                    <option value="{{ $classroom->id }}" {{ request('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                        {{ $classroom->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner une classe.</div>
                    </div>
                </div>

                {{-- Semestre + Bouton --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-primary">
                            <i class="fas fa-calendar-week me-2"></i>Semestre
                        </label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-primary text-white">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <select class="form-select" name="semester" id="semester" required>
                                <option value="" disabled {{ !request('semester') ? 'selected' : '' }}>Choisissez le semestre</option>
                                <option value="1" {{ request('semester') == 1 ? 'selected' : '' }}>Semestre 1</option>
                                <option value="2" {{ request('semester') == 2 ? 'selected' : '' }}>Semestre 2</option>
                            </select>
                        </div>
                        <div class="invalid-feedback ps-3">Veuillez sélectionner un semestre.</div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary shadow-sm px-4">
                            <i class="fas fa-search me-2"></i>Rechercher
                        </button>
                    </div>
                </div>
            </form>

            {{-- RÉSULTATS --}}
            @if($classroom && $studentsData->isNotEmpty())

                {{-- Bandeau d'info --}}
                <div class="alert border-0 text-white shadow-sm mb-4"
                     style="background:linear-gradient(135deg,#17a2b8,#117a8b);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">
                                <i class="fas fa-chart-line me-2"></i>
                                {{ $classroom->promotionSector->promotion_sector }} — {{ $classroom->name }}
                            </h5>
                            <p class="mb-0 opacity-75">
                                Semestre {{ request('semester') }} &nbsp;|&nbsp;
                                {{ $classroom->promotionSector->sectorYear->year->year }} &nbsp;|&nbsp;
                                {{ $classroom->promotionSector->sectorYear->sector->name_sector }}
                            </p>
                        </div>
                        <button class="btn btn-light btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Imprimer
                        </button>
                    </div>
                </div>

                {{-- Tableau principal --}}
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-primary">
                            {{-- Ligne 1 : en-têtes regroupés --}}
                            <tr>
                                <th rowspan="2" class="align-middle">Étudiant</th>
                                <th rowspan="2" class="align-middle">Matricule</th>

                                @foreach($subjects as $subjectId => $subjectInfo)
                                    <th colspan="4" class="text-center">
                                        {{ $subjectInfo['name'] }}
                                        <small class="text-muted">(Coef : {{ $subjectInfo['coefficient'] }})</small>
                                    </th>
                                @endforeach

                                <th rowspan="2" class="align-middle text-center">Moy. Gén.</th>
                            </tr>
                            {{-- Ligne 2 : sous-colonnes --}}
                            <tr>
                                @foreach($subjects as $subjectId => $subjectInfo)
                                    <th class="text-center small">Interros</th>
                                    <th class="text-center small">Dev.1</th>
                                    <th class="text-center small">Dev.2</th>
                                    <th class="text-center small">Moy.</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentsData as $studentRow)
                                @php
                                    $student         = $studentRow['student'];
                                    $notesParMatiere = $studentRow['notes_par_matiere'];
                                    $moyGen          = $studentRow['moyenne_generale'];
                                @endphp
                                <tr>
                                    <td class="fw-bold">{{ $student->name }} {{ $student->surname }}</td>
                                    <td>{{ $student->matricule ?? 'N/A' }}</td>

                                    @foreach($subjects as $subjectId => $subjectInfo)
                                        @php
                                            $matiere     = $notesParMatiere[$subjectId] ?? null;
                                            $note        = $matiere['note']         ?? null;
                                            $moyInterros = $matiere['moy_interros'] ?? null;
                                            $moy20       = $matiere['moy_20']       ?? null;
                                            $interros    = $note ? (is_array($note->interros) ? $note->interros : (json_decode($note->interros, true) ?? [])) : [];
                                        @endphp

                                        {{-- Interros --}}
                                        <td class="text-center small">
                                            @if(!empty($interros))
                                                {{ implode(', ', array_map(fn($v) => number_format($v, 2), $interros)) }}
                                                <br><em class="text-muted">Moy : {{ number_format($moyInterros, 2) }}</em>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        {{-- Devoir 1 --}}
                                        <td class="text-center">
                                            {{ $note?->devoir1 !== null ? number_format($note->devoir1, 2) : '—' }}
                                        </td>
                                        {{-- Devoir 2 --}}
                                        <td class="text-center">
                                            {{ $note?->devoir2 !== null ? number_format($note->devoir2, 2) : '—' }}
                                        </td>
                                        {{-- Moyenne /20 --}}
                                        <td class="text-center fw-bold {{ $moy20 !== null ? ($moy20 >= 10 ? 'text-success' : 'text-danger') : '' }}">
                                            {{ $moy20 !== null ? number_format($moy20, 2) : '—' }}
                                        </td>
                                    @endforeach

                                    {{-- Moyenne générale pondérée --}}
                                    <td class="text-center fw-bold {{ $moyGen !== null ? ($moyGen >= 10 ? 'text-success' : 'text-danger') : '' }}">
                                        {{ $moyGen !== null ? number_format($moyGen, 2) : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Statistiques rapides --}}
                <div class="row mt-4 g-3">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm text-center py-3">
                            <h3 class="text-primary mb-0">{{ $studentsData->count() }}</h3>
                            <p class="text-muted mb-0">Étudiants</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm text-center py-3">
                            <h3 class="text-success mb-0">{{ $subjects->count() }}</h3>
                            <p class="text-muted mb-0">Matières</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm text-center py-3">
                            @php
                                $admis = $studentsData->filter(fn($s) => $s['moyenne_generale'] !== null && $s['moyenne_generale'] >= 10)->count();
                            @endphp
                            <h3 class="text-info mb-0">{{ $admis }} / {{ $studentsData->count() }}</h3>
                            <p class="text-muted mb-0">Admis (≥ 10)</p>
                        </div>
                    </div>
                </div>

            @elseif(request()->has(['year_id','sector_id','promotion_id','classroom_id','semester']))

                {{-- Aucune note trouvée --}}
                <div class="text-center py-5">
                    <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune note disponible</h5>
                    <p class="text-muted small">Aucune note n'a été enregistrée pour cette classe et ce semestre.</p>
                </div>

            @else

                {{-- Message par défaut --}}
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Sélectionnez les critères pour afficher les notes</h5>
                    <p class="text-muted small">Choisissez une année, une filière, une promotion, une classe et un semestre.</p>
                </div>

            @endif
        </div>
    </div>
</div>
@endsection

@section('another_JS')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const yearSelect      = document.getElementById('import_year_id');
    const sectorSelect    = document.getElementById('import_sector_id');
    const promotionSelect = document.getElementById('import_promotion_id');
    const classroomSelect = document.getElementById('import_classroom_id');

    async function fetchOptions(url, selectEl, placeholder) {
        selectEl.innerHTML = `<option value="">${placeholder}</option>`;
        selectEl.disabled  = true;
        try {
            const res  = await fetch(url);
            const data = await res.json();
            data.forEach(item => {
                const opt       = document.createElement('option');
                opt.value       = item.id;
                opt.textContent = item.name ?? item.name_sector ?? item.promotion_sector;
                selectEl.appendChild(opt);
            });
            selectEl.disabled = data.length === 0;
        } catch {
            selectEl.innerHTML += '<option disabled>Erreur de chargement</option>';
        }
    }

    yearSelect.addEventListener('change', async function () {
        sectorSelect.innerHTML    = '<option value="">-- Sélectionnez d\'abord l\'année --</option>';
        sectorSelect.disabled     = true;
        promotionSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la filière --</option>';
        promotionSelect.disabled  = true;
        classroomSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la promotion --</option>';
        classroomSelect.disabled  = true;

        if (this.value) {
            await fetchOptions(`/api/sectors-by-year/${this.value}`, sectorSelect, '-- Choisissez une filière --');
        }
    });

    sectorSelect.addEventListener('change', async function () {
        promotionSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la filière --</option>';
        promotionSelect.disabled  = true;
        classroomSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la promotion --</option>';
        classroomSelect.disabled  = true;

        if (this.value && yearSelect.value) {
            await fetchOptions(
                `/api/promotions-by-year-sector/${yearSelect.value}/${this.value}`,
                promotionSelect,
                '-- Choisissez une promotion --'
            );
        }
    });

    promotionSelect.addEventListener('change', async function () {
        classroomSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord la promotion --</option>';
        classroomSelect.disabled  = true;

        if (this.value) {
            await fetchOptions(
                `/api/classes-by-promotion/${this.value}`,
                classroomSelect,
                '-- Choisissez une classe --'
            );
        }
    });

    // Validation du formulaire
    document.getElementById('filterForm').addEventListener('submit', function (e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });
});
</script>

<style>
@media print {
    .card-header, .btn, form, .alert { display: none !important; }
    .table { font-size: 11px; }
}
</style>
@endsection
