@extends('layouts.template')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">

<div class="nv-root">

    {{-- ── TOPBAR ── --}}
    <header class="nv-topbar">
        <div class="nv-topbar-left">
            <div class="nv-topbar-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div>
                <h1 class="nv-title">Consultation des Notes</h1>
                <p class="nv-subtitle">Sélectionnez un contexte pour afficher les résultats</p>
            </div>
        </div>
        <div class="nv-topbar-right">
            @if($classroom && isset($studentsData) && $studentsData->isNotEmpty())
            <button class="nv-print-btn" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
            @endif
        </div>
    </header>

    {{-- ── FILTRES ── --}}
    <section class="nv-filters-panel">
        <form method="GET" id="filterForm" novalidate>
            @csrf

            <div class="nv-filters-row">
                <div class="nv-filter-group">
                    <label class="nv-label"><i class="fas fa-calendar-alt"></i> Année scolaire</label>
                    <select name="year_id" id="import_year_id" class="nv-select" required>
                        <option value="">— Choisir —</option>
                        @foreach($years as $year)
                            <option value="{{ $year->id }}" {{ request('year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="nv-filter-sep"><i class="fas fa-chevron-right"></i></div>

                <div class="nv-filter-group">
                    <label class="nv-label"><i class="fas fa-layer-group"></i> Filière</label>
                    <select name="sector_id" id="import_sector_id" class="nv-select"
                            {{ !request('year_id') ? 'disabled' : '' }} required>
                        <option value="">— Année d'abord —</option>
                        @if(request('year_id'))
                            @foreach($sectorsForFilter as $sector)
                                <option value="{{ $sector->id }}" {{ request('sector_id') == $sector->id ? 'selected' : '' }}>
                                    {{ $sector->name_sector }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="nv-filter-sep"><i class="fas fa-chevron-right"></i></div>

                <div class="nv-filter-group">
                    <label class="nv-label"><i class="fas fa-graduation-cap"></i> Promotion</label>
                    <select name="promotion_id" id="import_promotion_id" class="nv-select"
                            {{ !request('sector_id') ? 'disabled' : '' }} required>
                        <option value="">— Filière d'abord —</option>
                        @if(request('year_id') && request('sector_id'))
                            @foreach($promotionsForFilter as $promotion)
                                <option value="{{ $promotion->id }}" {{ request('promotion_id') == $promotion->id ? 'selected' : '' }}>
                                    {{ $promotion->promotion_sector }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="nv-filter-sep"><i class="fas fa-chevron-right"></i></div>

                <div class="nv-filter-group">
                    <label class="nv-label"><i class="fas fa-door-open"></i> Classe</label>
                    <select name="classroom_id" id="import_classroom_id" class="nv-select"
                            {{ !request('promotion_id') ? 'disabled' : '' }} required>
                        <option value="">— Promotion d'abord —</option>
                        @if(request('promotion_id'))
                            @foreach($classroomsForFilter as $classroom_opt)
                                <option value="{{ $classroom_opt->id }}" {{ request('classroom_id') == $classroom_opt->id ? 'selected' : '' }}>
                                    {{ $classroom_opt->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="nv-filters-row nv-filters-row--secondary">
                <div class="nv-filter-group">
                    <label class="nv-label"><i class="fas fa-layer-group"></i> Semestre</label>
                    <div class="nv-semester-toggle">
                        <button type="button" class="nv-sem-btn {{ request('semester', 1) == 1 ? 'nv-sem-btn--active' : '' }}"
                                data-value="1" id="sem-btn-1">
                            <span>S1</span><small>Semestre 1</small>
                        </button>
                        <button type="button" class="nv-sem-btn {{ request('semester') == 2 ? 'nv-sem-btn--active' : '' }}"
                                data-value="2" id="sem-btn-2">
                            <span>S2</span><small>Semestre 2</small>
                        </button>
                    </div>
                    <input type="hidden" name="semester" id="semester_input" value="{{ request('semester', 1) }}">
                </div>

                <div class="nv-filter-group nv-filter-group--action">
                    <label class="nv-label nv-label--invisible">Action</label>
                    <button type="submit" class="nv-btn-search">
                        <i class="fas fa-magnifying-glass"></i>
                        <span>Rechercher</span>
                    </button>
                </div>
            </div>
        </form>
    </section>

    {{-- ── RÉSULTATS ── --}}
    @if($classroom && isset($studentsData) && $studentsData->isNotEmpty())

        @php
            $currentSemester = (int) request('semester');

            // ── Troncage 2 décimales (sans arrondi) ──────────────────────────
            $trunc2 = function(?float $v): ?float {
                return $v === null ? null : floor($v * 100) / 100;
            };

            // ── Ordinal français ─────────────────────────────────────────────
            $ordinalFr = function(int $n): string {
                return $n === 1 ? '1er' : $n . 'ème';
            };

            /**
             * Calcule les rangs avec gestion correcte des ex-æquo.
             *
             * Principe : on parcourt les moyennes triées desc. Pour chaque
             * groupe de valeurs identiques, TOUS les membres reçoivent le rang
             * du PREMIER du groupe. Le rang suivant saute autant de places qu'il
             * y a d'ex-æquo.
             *
             * Exemple pour 5 élèves : 16, 14, 14, 12, 10
             * → 1er, 2ème, 2ème, 4ème, 5ème  (le rang 3 est sauté)
             */
            $calculerRangs = function(array $moyennes) use ($ordinalFr): array {
                $avecNote = array_filter($moyennes, fn($m) => $m !== null);
                $sansNote = array_filter($moyennes, fn($m) => $m === null);
                arsort($avecNote);

                $rangs       = [];
                $rangDebut   = 1;   // rang attribué au début du groupe courant
                $prevMoy     = null;
                $nbDansGroupe = 0;  // combien d'élèves ont la même moyenne

                foreach ($avecNote as $sid => $moy) {
                    if ($prevMoy !== null && $moy == $prevMoy) {
                        // Même groupe : même rang que le premier du groupe
                        $nbDansGroupe++;
                    } else {
                        // Nouveau groupe : le rang de départ avance de la taille du groupe précédent
                        $rangDebut  += $nbDansGroupe;
                        $nbDansGroupe = 1;
                        $prevMoy    = $moy;
                    }
                    $rangs[$sid] = $ordinalFr($rangDebut);
                }

                foreach (array_keys($sansNote) as $sid) {
                    $rangs[$sid] = '—';
                }

                return $rangs;
            };

            // ── Rangs semestriels ────────────────────────────────────────────
            $moyennesGenerales = $studentsData->mapWithKeys(
                fn($s) => [$s['student']->id => $s['moyenne_generale']]
            )->toArray();

            $rangsGeneraux = $calculerRangs($moyennesGenerales);
            $totalEleves   = $studentsData->count();
            $admis         = $studentsData->filter(fn($s) => $s['moyenne_generale'] !== null && $s['moyenne_generale'] >= 10)->count();
            $classAvg      = $studentsData->filter(fn($s) => $s['moyenne_generale'] !== null)->avg('moyenne_generale');
            $classAvg      = $classAvg ? $trunc2((float) $classAvg) : null;

            // ── Rangs annuels (calculés ICI, avant le foreach, pour être
            //    disponibles dans les cartes étudiants) ─────────────────────
            $rangsAnnuelsVue   = [];
            $moyAnnuellesVue   = [];

            if ($currentSemester == 2) {
                foreach ($studentsData as $sRow) {
                    $sid = $sRow['student']->id;
                    $rec = $sRow['student']->recordings
                        ->where('year_id',      request('year_id'))
                        ->where('classroom_id', request('classroom_id'))
                        ->first();

                    $ms1 = null;
                    if ($rec) {
                        $nS1s = \App\Models\Note::with(['ratio'])
                            ->where('recording_id', $rec->id)
                            ->where('semester', 1)
                            ->get();
                        $tp = 0; $tc = 0;
                        foreach ($nS1s as $nS1) {
                            $int = is_array($nS1->interros)
                                ? $nS1->interros
                                : (json_decode($nS1->interros, true) ?? []);
                            $m = app(\App\Http\Controllers\NoteController::class)
                                ->calculateMoyenne20($int, $nS1->devoir1, $nS1->devoir2);
                            if ($m !== null) {
                                $coefS1 = $nS1->ratio->coefficient ?? 1;
                                $tp += $m * $coefS1;
                                $tc += $coefS1;
                            }
                        }
                        $ms1 = $tc > 0 ? $trunc2($tp / $tc) : null;
                    }

                    $ms2 = $sRow['moyenne_generale'];

                    if ($ms1 !== null && $ms2 !== null) {
                        $moyAnnuellesVue[$sid] = $trunc2((($ms2 * 2) + $ms1) / 3);
                    } elseif ($ms2 !== null) {
                        $moyAnnuellesVue[$sid] = $ms2;
                    } elseif ($ms1 !== null) {
                        $moyAnnuellesVue[$sid] = $ms1;
                    } else {
                        $moyAnnuellesVue[$sid] = null;
                    }
                }

                $rangsAnnuelsVue = $calculerRangs($moyAnnuellesVue);
            }
        @endphp

        {{-- Bandeau classe --}}
        <div class="nv-class-banner">
            <div class="nv-class-banner-left">
                <div class="nv-class-banner-icon"><i class="fas fa-building-columns"></i></div>
                <div>
                    <div class="nv-class-name">
                        {{ $classroom->promotionSector->promotion_sector }} — {{ $classroom->name }}
                    </div>
                    <div class="nv-class-meta">
                        Semestre {{ $currentSemester }}
                        &middot; {{ $classroom->promotionSector->sectorYear->year->year }}
                        &middot; {{ $classroom->promotionSector->sectorYear->sector->name_sector }}
                    </div>
                </div>
            </div>
            <div class="nv-class-stats">
                <div class="nv-cs">
                    <span class="nv-cs-val">{{ $totalEleves }}</span>
                    <span class="nv-cs-key">Élèves</span>
                </div>
                <div class="nv-cs-div"></div>
                <div class="nv-cs">
                    <span class="nv-cs-val nv-cs-val--success">{{ $admis }}</span>
                    <span class="nv-cs-key">Admis</span>
                </div>
                <div class="nv-cs-div"></div>
                <div class="nv-cs">
                    <span class="nv-cs-val nv-cs-val--danger">{{ $totalEleves - $admis }}</span>
                    <span class="nv-cs-key">Ajourné(s)</span>
                </div>
                <div class="nv-cs-div"></div>
                <div class="nv-cs">
                    <span class="nv-cs-val {{ $classAvg !== null ? ($classAvg >= 10 ? 'nv-cs-val--success' : 'nv-cs-val--danger') : '' }}">
                        {{ $classAvg !== null ? number_format($classAvg, 2) : '—' }}
                    </span>
                    <span class="nv-cs-key">Moy. classe</span>
                </div>
                <div class="nv-cs-div"></div>
                <div class="nv-cs">
                    <span class="nv-cs-val">{{ $subjects->count() }}</span>
                    <span class="nv-cs-key">Matières</span>
                </div>
            </div>
        </div>

        {{-- ── BLOCS ÉTUDIANTS ── --}}
        @foreach($studentsData as $studentRow)
            @php
                $student         = $studentRow['student'];
                $notesParMatiere = $studentRow['notes_par_matiere'];
                $moyGen          = $studentRow['moyenne_generale'];
                $sid             = $student->id;
                $rangSem         = $rangsGeneraux[$sid] ?? '—';

                // Réutilise les valeurs calculées dans le bloc principal (pas de double requête)
                $moyAnnuelle = $moyAnnuellesVue[$sid] ?? null;
                $rangAnn     = $rangsAnnuelsVue[$sid] ?? '—';

                // Pour afficher la Moy S1 dans le récap bas de carte, on la recalcule
                // uniquement si nécessaire (S2) à partir de la moyenne annuelle et S2
                $moyS1 = null;
                if ($currentSemester == 2 && $moyAnnuelle !== null && $moyGen !== null) {
                    // Formule inverse : moyAnn = (S2*2 + S1) / 3  →  S1 = moyAnn*3 - S2*2
                    // Mais on la récupère directement depuis les données déjà calculées
                    $rec = $student->recordings
                        ->where('year_id',      request('year_id'))
                        ->where('classroom_id', request('classroom_id'))
                        ->first();
                    if ($rec) {
                        $nS1s = \App\Models\Note::with(['ratio'])
                            ->where('recording_id', $rec->id)
                            ->where('semester', 1)->get();
                        $tp = 0; $tc = 0;
                        foreach ($nS1s as $nS1) {
                            $int = is_array($nS1->interros)
                                ? $nS1->interros
                                : (json_decode($nS1->interros, true) ?? []);
                            $m = app(\App\Http\Controllers\NoteController::class)
                                ->calculateMoyenne20($int, $nS1->devoir1, $nS1->devoir2);
                            if ($m !== null) {
                                $c = $nS1->ratio->coefficient ?? 1;
                                $tp += $m * $c; $tc += $c;
                            }
                        }
                        $moyS1 = $tc > 0 ? $trunc2($tp / $tc) : null;
                    }
                }
                $hasNote = $moyGen !== null;
            @endphp

            <div class="nv-student-card {{ $hasNote ? ($moyGen >= 10 ? 'nv-student-card--pass' : 'nv-student-card--fail') : '' }}">

                {{-- En-tête étudiant --}}
                <div class="nv-student-header">
                    <div class="nv-student-avatar">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                    <div class="nv-student-info">
                        <div class="nv-student-name">{{ $student->name }} {{ $student->surname }}</div>
                        <div class="nv-student-meta">
                            Mat. {{ $student->matricule ?? 'N/A' }}
                            @if($student->sex)
                                &middot; {{ $student->sex == 'M' ? 'M' : 'F' }}
                            @endif
                        </div>
                    </div>
                    <div class="nv-student-header-stats">
                        <div class="nv-hs">
                            <span class="nv-hs-val {{ $moyGen !== null ? ($moyGen >= 10 ? 'nv-hs-pass' : 'nv-hs-fail') : '' }}">
                                {{ $moyGen !== null ? number_format($moyGen, 2) : '—' }}
                            </span>
                            <span class="nv-hs-key">Moy. S{{ $currentSemester }}</span>
                        </div>
                        <div class="nv-hs-sep"></div>
                        <div class="nv-hs">
                            <span class="nv-hs-val nv-hs-rank">
                                {{ $rangSem !== '—' ? $rangSem . '/' . $totalEleves : '—' }}
                            </span>
                            <span class="nv-hs-key">Rang S{{ $currentSemester }}</span>
                        </div>
                        @if($currentSemester == 2)
                        <div class="nv-hs-sep"></div>
                        <div class="nv-hs">
                            <span class="nv-hs-val {{ $moyAnnuelle !== null ? ($moyAnnuelle >= 10 ? 'nv-hs-pass' : 'nv-hs-fail') : '' }}">
                                {{ $moyAnnuelle !== null ? number_format($moyAnnuelle, 2) : '—' }}
                            </span>
                            <span class="nv-hs-key">Moy. Ann.</span>
                        </div>
                        <div class="nv-hs-sep"></div>
                        <div class="nv-hs">
                            <span class="nv-hs-val nv-hs-rank">
                                {{ $rangAnn !== '—' ? $rangAnn . '/' . $totalEleves : '—' }}
                            </span>
                            <span class="nv-hs-key">Rang Ann.</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Tableau des matières --}}
                <div class="nv-subjects-table-wrap">
                    <table class="nv-subjects-table">
                        <thead>
                            <tr>
                                <th class="nv-sth nv-sth--name">Matière</th>
                                <th class="nv-sth nv-sth--center">Interros</th>
                                <th class="nv-sth nv-sth--center">Moy. I</th>
                                <th class="nv-sth nv-sth--center">D1</th>
                                <th class="nv-sth nv-sth--center">D2</th>
                                <th class="nv-sth nv-sth--center nv-sth--moy">Moy /20</th>
                                <th class="nv-sth nv-sth--center">Coeff</th>
                                <th class="nv-sth nv-sth--center nv-sth--moyc">Moy×C</th>
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
                                    $moyCoeff    = $moy20 !== null ? $trunc2($moy20 * $coef) : null;
                                    $interros    = $note ? (is_array($note->interros) ? $note->interros : (json_decode($note->interros, true) ?? [])) : [];
                                    $hasData     = $moy20 !== null;
                                @endphp
                                <tr class="nv-str {{ !$hasData ? 'nv-str--empty' : '' }}">
                                    <td class="nv-std nv-std--name">{{ $subjectInfo['name'] }}</td>

                                    {{-- Interros --}}
                                    <td class="nv-std nv-std--center">
                                        @if(!empty($interros))
                                            <div class="nv-interros-chips">
                                                @foreach($interros as $iv)
                                                    <span class="nv-chip {{ $iv >= 10 ? 'nv-chip--pass' : 'nv-chip--fail' }}">
                                                        {{ number_format($iv, 2) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="nv-empty-val">—</span>
                                        @endif
                                    </td>

                                    {{-- Moy interros --}}
                                    <td class="nv-std nv-std--center">
                                        @if($moyInterros !== null)
                                            <span class="nv-note {{ $moyInterros >= 10 ? 'nv-note--pass' : 'nv-note--fail' }}">
                                                {{ number_format($moyInterros, 2) }}
                                            </span>
                                        @else
                                            <span class="nv-empty-val">—</span>
                                        @endif
                                    </td>

                                    {{-- D1 --}}
                                    <td class="nv-std nv-std--center">
                                        @if($note?->devoir1 !== null)
                                            <span class="nv-note {{ $note->devoir1 >= 10 ? 'nv-note--pass' : 'nv-note--fail' }}">
                                                {{ number_format($note->devoir1, 2) }}
                                            </span>
                                        @else <span class="nv-empty-val">—</span>
                                        @endif
                                    </td>

                                    {{-- D2 --}}
                                    <td class="nv-std nv-std--center">
                                        @if($note?->devoir2 !== null)
                                            <span class="nv-note {{ $note->devoir2 >= 10 ? 'nv-note--pass' : 'nv-note--fail' }}">
                                                {{ number_format($note->devoir2, 2) }}
                                            </span>
                                        @else <span class="nv-empty-val">—</span>
                                        @endif
                                    </td>

                                    {{-- Moy/20 --}}
                                    <td class="nv-std nv-std--center">
                                        @if($moy20 !== null)
                                            <span class="nv-avg-final {{ $moy20 >= 10 ? 'nv-avg--pass' : 'nv-avg--fail' }}">
                                                {{ number_format($moy20, 2) }}
                                            </span>
                                        @else <span class="nv-empty-val">—</span>
                                        @endif
                                    </td>

                                    {{-- Coeff --}}
                                    <td class="nv-std nv-std--center">
                                        <span class="nv-coeff-badge">{{ $coef }}</span>
                                    </td>

                                    {{-- Moy×Coeff --}}
                                    <td class="nv-std nv-std--center">
                                        @if($moyCoeff !== null)
                                            <span class="nv-moyc {{ $moyCoeff >= 10 * $coef ? 'nv-moyc--pass' : 'nv-moyc--warn' }}">
                                                {{ number_format($moyCoeff, 2) }}
                                            </span>
                                        @else <span class="nv-empty-val">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Récap S1/Annuel si S2 --}}
                @if($currentSemester == 2 && $moyS1 !== null)
                <div class="nv-recaps">
                    <div class="nv-recap-item">
                        <span class="nv-recap-label">Moy. S1</span>
                        <span class="nv-recap-val {{ $moyS1 >= 10 ? 'nv-hs-pass' : 'nv-hs-fail' }}">
                            {{ number_format($moyS1, 2) }}
                        </span>
                    </div>
                    @if($moyAnnuelle !== null)
                    <div class="nv-recap-item">
                        <span class="nv-recap-label">Moy. Annuelle <small>(S2×2+S1)/3</small></span>
                        <span class="nv-recap-val {{ $moyAnnuelle >= 10 ? 'nv-hs-pass' : 'nv-hs-fail' }}">
                            {{ number_format($moyAnnuelle, 2) }}
                        </span>
                    </div>
                    @endif
                </div>
                @endif

            </div>
        @endforeach

    @elseif(request()->has(['year_id','sector_id','promotion_id','classroom_id','semester']))
        <div class="nv-empty-state">
            <div class="nv-empty-icon"><i class="fas fa-book-open"></i></div>
            <h3>Aucune note disponible</h3>
            <p>Aucune note n'a été enregistrée pour cette classe et ce semestre.</p>
        </div>

    @else
        <div class="nv-empty-state">
            <div class="nv-empty-icon"><i class="fas fa-magnifying-glass"></i></div>
            <h3>Commencez votre recherche</h3>
            <p>Choisissez une année, filière, promotion, classe et semestre,<br>puis cliquez sur <strong>Rechercher</strong>.</p>
        </div>
    @endif

</div>

{{-- Rangs annuels calculés dans le bloc @php principal ci-dessus --}}

@endsection

@section('another_JS')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const yearSel   = document.getElementById('import_year_id');
    const secSel    = document.getElementById('import_sector_id');
    const promSel   = document.getElementById('import_promotion_id');
    const classSel  = document.getElementById('import_classroom_id');
    const semInput  = document.getElementById('semester_input');

    // ── Semestre toggle ──────────────────────────────────────────────
    document.querySelectorAll('.nv-sem-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.nv-sem-btn').forEach(b => b.classList.remove('nv-sem-btn--active'));
            btn.classList.add('nv-sem-btn--active');
            semInput.value = btn.dataset.value;
        });
    });

    // ── Helpers select ──────────────────────────────────────────────
    function setLoading(sel, msg) {
        sel.innerHTML = `<option>${msg}</option>`;
        sel.disabled = true;
    }

    async function fetchOptions(url, sel, placeholder) {
        setLoading(sel, 'Chargement…');
        try {
            const data = await fetch(url).then(r => r.json());
            sel.innerHTML = `<option value="">${placeholder}</option>`;
            data.forEach(item => {
                const o = document.createElement('option');
                o.value = item.id;
                o.textContent = item.name ?? item.name_sector ?? item.promotion_sector;
                sel.appendChild(o);
            });
            sel.disabled = data.length === 0;
        } catch {
            sel.innerHTML = '<option value="">— Erreur de chargement —</option>';
            sel.disabled = true;
        }
    }

    function resetBelow(from) {
        if (from <= 1) { setLoading(secSel,  '— Filière —');    }
        if (from <= 2) { setLoading(promSel, '— Promotion —'); }
        if (from <= 3) { setLoading(classSel,'— Classe —');    }
    }

    yearSel.addEventListener('change', function () {
        resetBelow(1);
        if (this.value) fetchOptions(`/api/sectors-by-year/${this.value}`, secSel, '— Choisir une filière —');
    });

    secSel.addEventListener('change', function () {
        resetBelow(2);
        if (this.value && yearSel.value)
            fetchOptions(`/api/promotions-by-year-sector/${yearSel.value}/${this.value}`, promSel, '— Choisir une promotion —');
    });

    promSel.addEventListener('change', function () {
        resetBelow(3);
        if (this.value)
            fetchOptions(`/api/classes-by-promotion/${this.value}`, classSel, '— Choisir une classe —');
    });

    // ── Validation form ─────────────────────────────────────────────
    document.getElementById('filterForm').addEventListener('submit', function (e) {
        if (!classSel.value || !semInput.value) {
            e.preventDefault();
            if (!classSel.value) classSel.closest('.nv-filter-group').classList.add('nv-filter-group--error');
        }
    });
});
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&display=swap');

:root {
    --c-bg:          #f0f2f5;
    --c-surface:     #ffffff;
    --c-surface-2:   #f7f8fa;
    --c-border:      #e2e6eb;
    --c-border-2:    #d1d8e0;
    --c-text:        #1a1d23;
    --c-text-2:      #5a6072;
    --c-text-3:      #9aa0ae;
    --c-primary:     #3b5bdb;
    --c-primary-h:   #2f4bc0;
    --c-primary-10:  rgba(59,91,219,.10);
    --c-primary-20:  rgba(59,91,219,.20);
    --c-success:     #2f9e44;
    --c-success-bg:  #ebfbee;
    --c-danger:      #c92a2a;
    --c-danger-bg:   #fff5f5;
    --c-warning:     #e67700;
    --c-warning-bg:  #fff9db;
    --f-sans:        'DM Sans', system-ui, sans-serif;
    --f-mono:        'DM Mono', 'Fira Mono', monospace;
    --radius:        10px;
    --radius-lg:     16px;
    --shadow:        0 1px 3px rgba(0,0,0,.08), 0 4px 12px rgba(0,0,0,.04);
    --shadow-md:     0 4px 16px rgba(0,0,0,.10);
    --transition:    .18s cubic-bezier(.4,0,.2,1);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.nv-root {
    font-family: var(--f-sans);
    background: var(--c-bg);
    min-height: 100vh;
    padding: 1.5rem;
    color: var(--c-text);
    display: flex; flex-direction: column; gap: 1rem;
}

/* ── Topbar ── */
.nv-topbar {
    display: flex; align-items: center; justify-content: space-between;
    background: var(--c-surface); border: 1px solid var(--c-border);
    border-radius: var(--radius-lg); padding: 1rem 1.5rem;
    box-shadow: var(--shadow); flex-wrap: wrap; gap: .75rem;
}
.nv-topbar-left  { display: flex; align-items: center; gap: 1rem; }
.nv-topbar-icon  {
    width: 44px; height: 44px; border-radius: 12px;
    background: var(--c-primary); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.nv-title    { font-size: 1.2rem; font-weight: 700; }
.nv-subtitle { font-size: .82rem; color: var(--c-text-3); margin-top: 1px; }
.nv-topbar-right { display: flex; align-items: center; gap: .5rem; }
.nv-print-btn {
    font-family: var(--f-sans); font-size: .85rem; font-weight: 600;
    padding: .45rem 1rem; border-radius: 8px; cursor: pointer;
    border: 1px solid var(--c-border); background: var(--c-surface-2);
    color: var(--c-text-2); display: inline-flex; align-items: center; gap: .4rem;
    transition: background var(--transition);
}
.nv-print-btn:hover { background: var(--c-border); }

/* ── Filters ── */
.nv-filters-panel {
    background: var(--c-surface); border: 1px solid var(--c-border);
    border-radius: var(--radius-lg); padding: 1.5rem;
    box-shadow: var(--shadow); display: flex; flex-direction: column; gap: 1.25rem;
}
.nv-filters-row { display: flex; align-items: flex-end; gap: .5rem; flex-wrap: wrap; }
.nv-filters-row--secondary { padding-top: 1rem; border-top: 1px solid var(--c-border); }
.nv-filter-group { display: flex; flex-direction: column; gap: .4rem; flex: 1; min-width: 140px; }
.nv-filter-group--action { flex: 0 0 auto; }
.nv-filter-group--error .nv-select { border-color: var(--c-danger) !important; }
.nv-filter-sep { color: var(--c-text-3); font-size: .75rem; padding-bottom: .5rem; flex-shrink: 0; align-self: flex-end; }
.nv-label {
    font-size: .78rem; font-weight: 600; color: var(--c-text-2);
    letter-spacing: .3px; text-transform: uppercase;
    display: flex; align-items: center; gap: .3rem; white-space: nowrap;
}
.nv-label i { color: var(--c-primary); font-size: .7rem; }
.nv-label--invisible { opacity: 0; pointer-events: none; }
.nv-select {
    font-family: var(--f-sans); font-size: .875rem; height: 42px;
    padding: 0 2rem 0 .875rem; background: var(--c-surface-2);
    border: 1px solid var(--c-border); border-radius: var(--radius);
    color: var(--c-text); cursor: pointer; width: 100%; appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%239aa0ae' d='M8 10.94L2.53 5.47l1.06-1.06L8 8.82l4.41-4.41 1.06 1.06z'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right .6rem center; background-size: 14px;
    transition: border-color var(--transition), box-shadow var(--transition);
}
.nv-select:focus { outline: none; border-color: var(--c-primary); box-shadow: 0 0 0 3px var(--c-primary-10); background-color: var(--c-surface); }
.nv-select:disabled { opacity: .5; cursor: not-allowed; }
.nv-select:not(:disabled):hover { border-color: var(--c-primary); }
.nv-semester-toggle {
    display: flex; border: 1px solid var(--c-border); border-radius: var(--radius);
    overflow: hidden; background: var(--c-surface-2); height: 42px;
}
.nv-sem-btn {
    flex: 1; border: none; background: none; cursor: pointer;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    font-family: var(--f-sans); transition: background var(--transition);
    padding: 0 1rem; line-height: 1;
}
.nv-sem-btn span  { font-size: .9rem; font-weight: 700; }
.nv-sem-btn small { font-size: .65rem; color: var(--c-text-3); margin-top: 2px; }
.nv-sem-btn--active { background: var(--c-primary); color: #fff; }
.nv-sem-btn--active small { color: rgba(255,255,255,.7); }
.nv-sem-btn:not(.nv-sem-btn--active):hover { background: var(--c-primary-10); }
.nv-btn-search {
    font-family: var(--f-sans); height: 42px; padding: 0 1.5rem;
    background: var(--c-primary); color: #fff; border: none;
    border-radius: var(--radius); font-weight: 600; font-size: .9rem;
    cursor: pointer; display: flex; align-items: center; gap: .5rem;
    transition: background var(--transition), transform var(--transition), box-shadow var(--transition);
    white-space: nowrap;
}
.nv-btn-search:hover { background: var(--c-primary-h); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59,91,219,.35); }

/* ── Empty state ── */
.nv-empty-state {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: .75rem; padding: 4rem 2rem;
    background: var(--c-surface); border: 1px dashed var(--c-border-2);
    border-radius: var(--radius-lg); color: var(--c-text-3); text-align: center;
}
.nv-empty-icon {
    width: 64px; height: 64px; border-radius: 16px;
    background: var(--c-surface-2); border: 1px solid var(--c-border);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; color: var(--c-primary);
}
.nv-empty-state h3 { font-size: 1.1rem; font-weight: 600; color: var(--c-text-2); }
.nv-empty-state p  { font-size: .875rem; line-height: 1.6; }

/* ── Bandeau classe ── */
.nv-class-banner {
    background: var(--c-surface); border: 1px solid var(--c-border);
    border-radius: var(--radius-lg); padding: 1.25rem 1.5rem;
    box-shadow: var(--shadow); display: flex; align-items: center;
    justify-content: space-between; gap: 1rem; flex-wrap: wrap;
    border-left: 4px solid var(--c-primary);
}
.nv-class-banner-left { display: flex; align-items: center; gap: 1rem; }
.nv-class-banner-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: var(--c-primary-10); color: var(--c-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.nv-class-name { font-size: 1rem; font-weight: 700; }
.nv-class-meta { font-size: .8rem; color: var(--c-text-3); margin-top: 2px; }
.nv-class-stats { display: flex; align-items: center; gap: 0; flex-wrap: wrap; }
.nv-cs {
    display: flex; flex-direction: column; align-items: center;
    padding: .25rem 1.25rem;
}
.nv-cs-div { width: 1px; height: 28px; background: var(--c-border); flex-shrink: 0; }
.nv-cs-val { font-family: var(--f-mono); font-weight: 700; font-size: 1.05rem; }
.nv-cs-val--success { color: var(--c-success); }
.nv-cs-val--danger  { color: var(--c-danger);  }
.nv-cs-key { font-size: .7rem; color: var(--c-text-3); text-transform: uppercase; letter-spacing: .3px; margin-top: 2px; }

/* ── Cartes étudiants ── */
.nv-student-card {
    background: var(--c-surface); border: 1px solid var(--c-border);
    border-radius: var(--radius-lg); overflow: hidden;
    box-shadow: var(--shadow);
    transition: box-shadow var(--transition);
    border-left: 4px solid var(--c-border-2);
}
.nv-student-card:hover  { box-shadow: var(--shadow-md); }
.nv-student-card--pass  { border-left-color: var(--c-success); }
.nv-student-card--fail  { border-left-color: var(--c-danger);  }

.nv-student-header {
    display: flex; align-items: center; gap: 1rem;
    padding: .875rem 1.25rem;
    background: var(--c-surface-2);
    border-bottom: 1px solid var(--c-border);
    flex-wrap: wrap;
}
.nv-student-avatar {
    width: 40px; height: 40px; border-radius: 10px;
    background: var(--c-primary); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 1rem; flex-shrink: 0;
}
.nv-student-info { flex: 1; min-width: 0; }
.nv-student-name { font-weight: 700; font-size: .95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.nv-student-meta { font-size: .78rem; color: var(--c-text-3); margin-top: 2px; font-family: var(--f-mono); }

.nv-student-header-stats {
    display: flex; align-items: center; gap: 0;
    background: var(--c-surface); border: 1px solid var(--c-border);
    border-radius: var(--radius); overflow: hidden; flex-shrink: 0;
}
.nv-hs { display: flex; flex-direction: column; align-items: center; padding: .35rem 1rem; }
.nv-hs-sep { width: 1px; height: 28px; background: var(--c-border); }
.nv-hs-val { font-family: var(--f-mono); font-weight: 700; font-size: .95rem; }
.nv-hs-key { font-size: .65rem; color: var(--c-text-3); text-transform: uppercase; letter-spacing: .3px; margin-top: 1px; }
.nv-hs-pass { color: var(--c-success); }
.nv-hs-fail { color: var(--c-danger);  }
.nv-hs-rank { color: var(--c-primary); }

/* ── Tableau matières ── */
.nv-subjects-table-wrap { overflow-x: auto; }
.nv-subjects-table { width: 100%; border-collapse: collapse; font-size: .845rem; font-family: var(--f-sans); }
.nv-sth {
    padding: .6rem .75rem;
    background: var(--c-surface-2); border-bottom: 2px solid var(--c-border);
    font-size: .75rem; font-weight: 600; color: var(--c-text-2);
    text-transform: uppercase; letter-spacing: .3px;
}
.nv-sth--name   { text-align: left; min-width: 160px; }
.nv-sth--center { text-align: center; }
.nv-sth--moy    { background: #edf2ff; color: var(--c-primary); }
.nv-sth--moyc   { background: #f3f0ff; color: #6741d9; }
.nv-std { padding: .55rem .75rem; border-bottom: 1px solid var(--c-border); vertical-align: middle; }
.nv-std--name   { font-weight: 600; }
.nv-std--center { text-align: center; }
.nv-str:last-child .nv-std { border-bottom: none; }
.nv-str:hover .nv-std { background: rgba(59,91,219,.025); }
.nv-str--empty .nv-std { opacity: .5; }

/* Interros chips */
.nv-interros-chips { display: flex; gap: 3px; justify-content: center; flex-wrap: wrap; }
.nv-chip {
    display: inline-flex; align-items: center;
    font-family: var(--f-mono); font-size: .72rem; font-weight: 600;
    padding: 1px 6px; border-radius: 5px;
}
.nv-chip--pass { background: var(--c-success-bg); color: var(--c-success); }
.nv-chip--fail { background: var(--c-danger-bg);  color: var(--c-danger);  }
.nv-empty-val  { color: var(--c-text-3); font-family: var(--f-mono); }

/* Notes inline */
.nv-note {
    display: inline-flex; font-family: var(--f-mono); font-weight: 600; font-size: .875rem;
    padding: 2px 8px; border-radius: 6px;
}
.nv-note--pass { background: var(--c-success-bg); color: var(--c-success); }
.nv-note--fail { background: var(--c-danger-bg);  color: var(--c-danger);  }

/* Moy/20 */
.nv-avg-final {
    display: inline-flex; font-family: var(--f-mono); font-weight: 700; font-size: .9rem;
    padding: 3px 10px; border-radius: 6px; min-width: 52px; justify-content: center;
}
.nv-avg--pass { background: var(--c-success-bg); color: var(--c-success); }
.nv-avg--fail { background: var(--c-danger-bg);  color: var(--c-danger);  }

/* Coeff */
.nv-coeff-badge {
    display: inline-flex; font-family: var(--f-mono); font-weight: 500; font-size: .82rem;
    background: var(--c-surface-2); border: 1px solid var(--c-border);
    border-radius: 5px; padding: 1px 8px; color: var(--c-text-2);
}

/* Moy×Coeff */
.nv-moyc {
    display: inline-flex; font-family: var(--f-mono); font-weight: 600; font-size: .875rem;
    padding: 2px 8px; border-radius: 6px;
}
.nv-moyc--pass { background: var(--c-success-bg); color: var(--c-success); }
.nv-moyc--warn { background: var(--c-warning-bg); color: var(--c-warning); }

/* Récaps S1/annuel */
.nv-recaps {
    display: flex; align-items: center; gap: 1.5rem;
    padding: .75rem 1.25rem;
    background: var(--c-surface-2);
    border-top: 1px solid var(--c-border);
    font-size: .85rem;
}
.nv-recap-item { display: flex; align-items: center; gap: .5rem; }
.nv-recap-label { color: var(--c-text-3); font-size: .78rem; }
.nv-recap-label small { display: block; font-size: .65rem; }
.nv-recap-val { font-family: var(--f-mono); font-weight: 700; font-size: 1rem; }

/* ── Print ── */
@media print {
    .nv-filters-panel, .nv-print-btn, .nv-topbar-right { display: none !important; }
    .nv-student-card { break-inside: avoid; margin-bottom: 1.5rem; box-shadow: none !important; }
    .nv-subjects-table { font-size: 10px; }
    .nv-root { padding: 0; background: white; }
    .nv-class-banner { box-shadow: none; }
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .nv-root { padding: .75rem; }
    .nv-filter-group { min-width: 100%; flex: 1 1 100%; }
    .nv-filter-sep { display: none; }
    .nv-filter-group--action { width: 100%; }
    .nv-btn-search { width: 100%; justify-content: center; }
    .nv-student-header-stats { width: 100%; }
    .nv-class-banner { flex-direction: column; align-items: flex-start; }
}

::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--c-border-2); border-radius: 99px; }
</style>
@endsection
