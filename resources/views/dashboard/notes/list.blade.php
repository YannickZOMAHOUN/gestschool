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

                @php
                    $currentSemester = (int) request('semester');

                    // ── Helper : troncage à 2 décimales (sans arrondi) ────────
                    // Ex : 1.6666… → 1.66  (et non 1.67 avec round)
                    $trunc2 = function(?float $v): ?float {
                        if ($v === null) return null;
                        return floor($v * 100) / 100;
                    };

                    // ── Helper : formatage ordinal français ───────────────────
                    $ordinalFr = function(int $n): string {
                        return $n === 1 ? '1er' : $n . 'ème';
                    };

                    // ── Helper : calcul des rangs avec gestion des ex-æquo ────
                    $calculerRangs = function(array $moyennes) use ($ordinalFr): array {
                        $avecNote  = array_filter($moyennes, fn($m) => $m !== null);
                        $sansNote  = array_filter($moyennes, fn($m) => $m === null);

                        arsort($avecNote);

                        $rangs       = [];
                        $rangCourant = 1;
                        $prevMoy     = null;
                        $nbExAequo   = 0;

                        foreach ($avecNote as $sid => $moy) {
                            if ($moy == $prevMoy) {
                                $rangs[$sid] = $ordinalFr($rangCourant - $nbExAequo);
                                $nbExAequo++;
                            } else {
                                $rangCourant += $nbExAequo;
                                $nbExAequo    = 0;
                                $rangs[$sid]  = $ordinalFr($rangCourant);
                                $prevMoy      = $moy;
                            }
                            $rangCourant++;
                        }

                        foreach (array_keys($sansNote) as $sid) {
                            $rangs[$sid] = '—';
                        }

                        return $rangs;
                    };

                    // ── Calcul des rangs semestriels ──────────────────────────
                    $moyennesGenerales = $studentsData->mapWithKeys(fn($s) => [
                        $s['student']->id => $s['moyenne_generale']
                    ])->toArray();

                    $rangsGeneraux = $calculerRangs($moyennesGenerales);
                    $totalEleves   = $studentsData->count();
                @endphp

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
                                Semestre {{ $currentSemester }} &nbsp;|&nbsp;
                                {{ $classroom->promotionSector->sectorYear->year->year }} &nbsp;|&nbsp;
                                {{ $classroom->promotionSector->sectorYear->sector->name_sector }}
                            </p>
                        </div>
                        <button class="btn btn-light btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Imprimer
                        </button>
                    </div>
                </div>

                {{-- BLOCS PAR ÉTUDIANT --}}
                @foreach($studentsData as $studentRow)
                    @php
                        $student         = $studentRow['student'];
                        $notesParMatiere = $studentRow['notes_par_matiere'];
                        $moyGen          = $studentRow['moyenne_generale'];
                        $rangSem         = $rangsGeneraux[$student->id] ?? '—';

                        $moyS1       = null;
                        $moyS2       = null;
                        $moyAnnuelle = null;

                        if ($currentSemester == 2) {
                            $recording = $student->recordings
                                ->where('year_id', request('year_id'))
                                ->where('classroom_id', request('classroom_id'))
                                ->first();

                            if ($recording) {
                                $notesS1 = \App\Models\Note::with(['ratio'])
                                    ->where('recording_id', $recording->id)
                                    ->where('semester', 1)
                                    ->get();

                                $totalPondS1 = 0; $totalCoefS1 = 0;
                                foreach ($notesS1 as $noteS1) {
                                    $interrosS1 = is_array($noteS1->interros)
                                        ? $noteS1->interros
                                        : (json_decode($noteS1->interros, true) ?? []);
                                    $moy = app(\App\Http\Controllers\NoteController::class)
                                        ->calculateMoyenne20($interrosS1, $noteS1->devoir1, $noteS1->devoir2);
                                    if ($moy !== null) {
                                        $coef = $noteS1->ratio->coefficient ?? 1;
                                        $totalPondS1 += $moy * $coef;
                                        $totalCoefS1 += $coef;
                                    }
                                }
                                // Troncage (pas d'arrondi)
                                $moyS1 = $totalCoefS1 > 0 ? $trunc2($totalPondS1 / $totalCoefS1) : null;
                            }

                            $moyS2 = $moyGen;

                            // Formule : (S2 × 2 + S1) / 3 — troncage final
                            if ($moyS1 !== null && $moyS2 !== null) {
                                $moyAnnuelle = $trunc2((($moyS2 * 2) + $moyS1) / 3);
                            } elseif ($moyS2 !== null) {
                                $moyAnnuelle = $moyS2;
                            } elseif ($moyS1 !== null) {
                                $moyAnnuelle = $moyS1;
                            }
                        }
                    @endphp

                    <div class="card border-0 shadow-sm mb-4 student-card">
                        {{-- En-tête élève --}}
                        <div class="card-header d-flex justify-content-between align-items-center py-2"
                             style="background: linear-gradient(90deg, #0d6efd11, #0d6efd05); border-left: 4px solid #0d6efd;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                                     style="width:40px;height:40px;font-size:1rem;">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">
                                        {{ $student->name }} {{ $student->surname }}
                                    </h6>
                                    <small class="text-muted">
                                        Matricule : {{ $student->matricule ?? 'N/A' }}
                                        @if($student->sex) &nbsp;|&nbsp; {{ $student->sex == 'M' ? 'Masculin' : 'Féminin' }} @endif
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- Tableau des matières --}}
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle mb-0 small">
                                    <thead style="background-color:#e8f0fe;">
                                        <tr>
                                            <th style="width:22%">Matière</th>
                                            <th class="text-center" style="width:15%">Moy. Interros</th>
                                            <th class="text-center" style="width:13%">Devoir 1</th>
                                            <th class="text-center" style="width:13%">Devoir 2</th>
                                            <th class="text-center" style="width:13%">Moyenne /20</th>
                                            <th class="text-center" style="width:13%">Coefficient</th>
                                            <th class="text-center" style="width:11%">Moy. Coeff.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subjects as $subjectId => $subjectInfo)
                                            @php
                                                $matiere     = $notesParMatiere[$subjectId] ?? null;
                                                $note        = $matiere['note']         ?? null;
                                                $moyInterros = $matiere['moy_interros'] ?? null;
                                                $moy20       = $matiere['moy_20']       ?? null;
                                                $coef        = $subjectInfo['coefficient'];
                                                // Troncage de la moyenne coefficientée
                                                $moyCoeff    = $moy20 !== null ? $trunc2($moy20 * $coef) : null;
                                                $interros    = $note ? (is_array($note->interros) ? $note->interros : (json_decode($note->interros, true) ?? [])) : [];
                                            @endphp
                                            <tr>
                                                <td class="fw-semibold">{{ $subjectInfo['name'] }}</td>

                                                {{-- Moy. Interros --}}
                                                <td class="text-center">
                                                    @if(!empty($interros))
                                                        <span class="d-block small text-muted">
                                                            {{ implode(' | ', array_map(fn($v) => number_format($v, 2), $interros)) }}
                                                        </span>
                                                        <span class="fw-bold {{ $moyInterros >= 10 ? 'text-success' : 'text-danger' }}">
                                                            {{ number_format($moyInterros, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                {{-- Devoir 1 --}}
                                                <td class="text-center">
                                                    @if($note?->devoir1 !== null)
                                                        <span class="fw-semibold {{ $note->devoir1 >= 10 ? 'text-success' : 'text-danger' }}">
                                                            {{ number_format($note->devoir1, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                {{-- Devoir 2 --}}
                                                <td class="text-center">
                                                    @if($note?->devoir2 !== null)
                                                        <span class="fw-semibold {{ $note->devoir2 >= 10 ? 'text-success' : 'text-danger' }}">
                                                            {{ number_format($note->devoir2, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                {{-- Moyenne /20 --}}
                                                <td class="text-center fw-bold {{ $moy20 !== null ? ($moy20 >= 10 ? 'text-success' : 'text-danger') : '' }}">
                                                    {{ $moy20 !== null ? number_format($moy20, 2) : '—' }}
                                                </td>

                                                {{-- Coefficient --}}
                                                <td class="text-center text-muted">{{ $coef }}</td>

                                                {{-- Moy. Coefficiée --}}
                                                <td class="text-center fw-bold {{ $moyCoeff !== null ? ($moyCoeff >= 10 * $coef ? 'text-success' : 'text-warning') : '' }}">
                                                    {{ $moyCoeff !== null ? number_format($moyCoeff, 2) : '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Récapitulatif en bas du tableau --}}
                            <div class="px-3 py-3 border-top" style="background:#f8f9ff;">
                                <div class="row g-2 text-center">

                                    {{-- Moyenne semestrielle --}}
                                    <div class="col">
                                        <div class="rounded p-2 h-100" style="background:#fff;border:1px solid #dee2ff;">
                                            <div class="small text-muted fw-semibold text-uppercase" style="font-size:0.7rem; letter-spacing:.05em;">
                                                Moy. Sem. {{ $currentSemester }}
                                            </div>
                                            <div class="fw-bold fs-5 mt-1 {{ $moyGen !== null ? ($moyGen >= 10 ? 'text-success' : 'text-danger') : 'text-muted' }}">
                                                {{ $moyGen !== null ? number_format($moyGen, 2) : '—' }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Rang semestriel --}}
                                    <div class="col">
                                        <div class="rounded p-2 h-100" style="background:#fff;border:1px solid #dee2ff;">
                                            <div class="small text-muted fw-semibold text-uppercase" style="font-size:0.7rem; letter-spacing:.05em;">
                                                Rang Sem. {{ $currentSemester }}
                                            </div>
                                            <div class="fw-bold fs-5 mt-1 text-primary">
                                                {{ $rangSem !== '—' ? $rangSem . '/' . $totalEleves : '—' }}
                                            </div>
                                        </div>
                                    </div>

                                    @if($currentSemester == 2)
                                        {{-- Moyenne S1 --}}
                                        <div class="col">
                                            <div class="rounded p-2 h-100" style="background:#fff;border:1px solid #dee2ff;">
                                                <div class="small text-muted fw-semibold text-uppercase" style="font-size:0.7rem; letter-spacing:.05em;">
                                                    Moy. Sem. 1
                                                </div>
                                                <div class="fw-bold fs-5 mt-1 {{ $moyS1 !== null ? ($moyS1 >= 10 ? 'text-success' : 'text-danger') : 'text-muted' }}">
                                                    {{ $moyS1 !== null ? number_format($moyS1, 2) : '—' }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Moyenne annuelle --}}
                                        <div class="col">
                                            <div class="rounded p-2 h-100" style="background:#fff;border:1px solid #dee2ff;">
                                                <div class="small text-muted fw-semibold text-uppercase" style="font-size:0.7rem; letter-spacing:.05em;">
                                                    Moy. Annuelle
                                                    <span class="d-block" style="font-size:0.6rem;color:#adb5bd;">(S2×2 + S1) / 3</span>
                                                </div>
                                                <div class="fw-bold fs-5 mt-1 {{ $moyAnnuelle !== null ? ($moyAnnuelle >= 10 ? 'text-success' : 'text-danger') : 'text-muted' }}">
                                                    {{ $moyAnnuelle !== null ? number_format($moyAnnuelle, 2) : '—' }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Rang annuel --}}
                                        <div class="col">
                                            <div class="rounded p-2 h-100" style="background:#fff;border:1px solid #dee2ff;">
                                                <div class="small text-muted fw-semibold text-uppercase" style="font-size:0.7rem; letter-spacing:.05em;">
                                                    Rang Annuel
                                                </div>
                                                <div class="fw-bold fs-5 mt-1 text-primary">
                                                    @php
                                                        echo isset($rangsAnnuelsVue[$student->id])
                                                            ? $rangsAnnuelsVue[$student->id] . '/' . $totalEleves
                                                            : '—';
                                                    @endphp
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

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

                <div class="text-center py-5">
                    <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune note disponible</h5>
                    <p class="text-muted small">Aucune note n'a été enregistrée pour cette classe et ce semestre.</p>
                </div>

            @else

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

{{-- Calcul des rangs annuels AVANT le rendu des blocs (injection PHP côté serveur) --}}
@if($classroom && $studentsData->isNotEmpty() && request('semester') == 2)
@php
    $ordinalFrAnn = function(int $n): string {
        return $n === 1 ? '1er' : $n . 'ème';
    };

    // Helper troncage (dupliqué ici car hors scope du @php principal)
    $trunc2Ann = function(?float $v): ?float {
        if ($v === null) return null;
        return floor($v * 100) / 100;
    };

    // Calculer toutes les moyennes annuelles
    $moyAnnuellesVue = [];
    foreach ($studentsData as $sRow) {
        $sid = $sRow['student']->id;
        $recording = $sRow['student']->recordings
            ->where('year_id', request('year_id'))
            ->where('classroom_id', request('classroom_id'))
            ->first();

        $moyS1tmp = null;
        if ($recording) {
            $notesS1tmp = \App\Models\Note::with(['ratio'])
                ->where('recording_id', $recording->id)
                ->where('semester', 1)->get();
            $tp = 0; $tc = 0;
            foreach ($notesS1tmp as $nS1) {
                $int = is_array($nS1->interros) ? $nS1->interros : (json_decode($nS1->interros, true) ?? []);
                $m = app(\App\Http\Controllers\NoteController::class)->calculateMoyenne20($int, $nS1->devoir1, $nS1->devoir2);
                if ($m !== null) { $tp += $m * ($nS1->ratio->coefficient ?? 1); $tc += ($nS1->ratio->coefficient ?? 1); }
            }
            // Troncage (pas d'arrondi)
            $moyS1tmp = $tc > 0 ? $trunc2Ann($tp / $tc) : null;
        }
        $moyS2tmp = $sRow['moyenne_generale'];

        // Formule annuelle : (S2 × 2 + S1) / 3 — troncage final
        if ($moyS1tmp !== null && $moyS2tmp !== null) {
            $moyAnnuellesVue[$sid] = $trunc2Ann((($moyS2tmp * 2) + $moyS1tmp) / 3);
        } else {
            $moyAnnuellesVue[$sid] = $moyS2tmp ?? $moyS1tmp;
        }
    }

    // Calcul des rangs annuels avec gestion des ex-æquo
    $rangsAnnuelsVue = [];
    $avecNoteAnn  = array_filter($moyAnnuellesVue, fn($m) => $m !== null);
    $sansNoteAnn  = array_filter($moyAnnuellesVue, fn($m) => $m === null);
    arsort($avecNoteAnn);

    $rangCourantAnn = 1;
    $prevMoyAnn     = null;
    $nbExAequoAnn   = 0;

    foreach ($avecNoteAnn as $sid => $moy) {
        if ($moy == $prevMoyAnn) {
            $rangsAnnuelsVue[$sid] = $ordinalFrAnn($rangCourantAnn - $nbExAequoAnn);
            $nbExAequoAnn++;
        } else {
            $rangCourantAnn += $nbExAequoAnn;
            $nbExAequoAnn    = 0;
            $rangsAnnuelsVue[$sid] = $ordinalFrAnn($rangCourantAnn);
            $prevMoyAnn = $moy;
        }
        $rangCourantAnn++;
    }
    foreach (array_keys($sansNoteAnn) as $sid) {
        $rangsAnnuelsVue[$sid] = '—';
    }
@endphp
@endif

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
.student-card {
    border-radius: 12px !important;
    overflow: hidden;
    transition: box-shadow .2s;
}
.student-card:hover {
    box-shadow: 0 6px 24px rgba(13,110,253,.13) !important;
}
@media print {
    .card-header > .d-flex > button,
    form, .alert { display: none !important; }
    .student-card { break-inside: avoid; margin-bottom: 24px; }
    .table { font-size: 10px; }
}
</style>
@endsection
