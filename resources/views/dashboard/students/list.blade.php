@extends('layouts.template')

@section('content')
<main class="gv" aria-label="Consultation des notes">

    {{-- ===== TOPBAR ===== --}}
    <header class="gv-topbar">
        <div class="gv-topbar__left">
            <div class="gv-appmark" aria-hidden="true">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="gv-headings">
                <h1 class="gv-title">Consultation des Notes</h1>
                <p class="gv-subtitle">Sélectionnez la classe et le semestre pour afficher les résultats.</p>
            </div>
        </div>
        <div class="gv-topbar__right">
            @if($classroom && isset($studentsData) && $studentsData->isNotEmpty())
                <button class="gv-btn gv-btn--soft" type="button" id="btnToggleAll">
                    <i class="fas fa-layer-group"></i> Tout ouvrir
                </button>
                <button class="gv-btn gv-btn--primary" type="button" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            @endif
        </div>
    </header>

    {{-- ===== PANNEAU FILTRES (cascade intelligente) ===== --}}
    <section class="gv-panel" aria-label="Filtres">

        {{-- Fil d'Ariane --}}
        <div class="gv-breadcrumb" id="gv-bc">
            <div class="gv-bc-item gv-bc-year" id="gv-bc-year">
                <i class="fas fa-calendar-alt" style="font-size:.7rem"></i>
                <span id="gv-bc-year-lbl">Année</span>
            </div>
            <span class="gv-bc-sep">›</span>
            <div class="gv-bc-item" id="gv-bc-sector"><span id="gv-bc-sector-lbl">Filière</span></div>
            <span class="gv-bc-sep">›</span>
            <div class="gv-bc-item" id="gv-bc-promo"><span id="gv-bc-promo-lbl">Promotion</span></div>
            <span class="gv-bc-sep">›</span>
            <div class="gv-bc-item" id="gv-bc-class"><span id="gv-bc-class-lbl">Classe</span></div>
        </div>

        {{-- Étape 1 : Filière --}}
        <div class="gv-step" id="gv-step-sector">
            <div class="gv-step-head">
                <div class="gv-step-num">1</div>
                <div class="gv-step-info">
                    <div class="gv-step-title">Filière</div>
                    <div class="gv-step-hint">Choisissez la filière d'enseignement</div>
                </div>
                <div class="gv-step-loader" id="gv-loader-sector" style="display:none;">
                    <div class="gv-spin-sm"></div>
                </div>
            </div>
            <div class="gv-step-opts" id="gv-opts-sector">
                <div class="gv-ph">Chargement de l'année active…</div>
            </div>
        </div>

        {{-- Étape 2 : Promotion --}}
        <div class="gv-step gv-step--locked" id="gv-step-promo">
            <div class="gv-step-head">
                <div class="gv-step-num">2</div>
                <div class="gv-step-info">
                    <div class="gv-step-title">Promotion</div>
                    <div class="gv-step-hint">Sélectionnez la promotion dans la filière</div>
                </div>
                <div class="gv-step-loader" id="gv-loader-promo" style="display:none;">
                    <div class="gv-spin-sm"></div>
                </div>
            </div>
            <div class="gv-step-opts" id="gv-opts-promo">
                <div class="gv-ph">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                    Choisissez d'abord une filière
                </div>
            </div>
        </div>

        {{-- Étape 3 : Classe --}}
        <div class="gv-step gv-step--locked" id="gv-step-class">
            <div class="gv-step-head">
                <div class="gv-step-num">3</div>
                <div class="gv-step-info">
                    <div class="gv-step-title">Classe</div>
                    <div class="gv-step-hint">Choisissez la classe à consulter</div>
                </div>
                <div class="gv-step-loader" id="gv-loader-class" style="display:none;">
                    <div class="gv-spin-sm"></div>
                </div>
            </div>
            <div class="gv-step-opts" id="gv-opts-class">
                <div class="gv-ph">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                    Choisissez d'abord une promotion
                </div>
            </div>
        </div>

        {{-- Séparateur --}}
        <div class="gv-sep-line">
            <span>Semestre & Recherche</span>
        </div>

        {{-- Semestre + bouton rechercher --}}
        <div class="gv-config-row">
            <div class="gv-field gv-field--sem">
                <span class="gv-label">
                    <i class="fas fa-layer-group" aria-hidden="true"></i>
                    Semestre
                </span>
                <div class="gv-semtabs" role="tablist" aria-label="Choix du semestre">
                    <button type="button"
                            class="gv-tab {{ request('semester', 1) == 1 ? 'is-active' : '' }}"
                            data-value="1"
                            aria-pressed="{{ request('semester', 1) == 1 ? 'true' : 'false' }}"
                            id="sem-btn-1">
                        <span>S1</span><small>Semestre 1</small>
                    </button>
                    <button type="button"
                            class="gv-tab {{ request('semester') == 2 ? 'is-active' : '' }}"
                            data-value="2"
                            aria-pressed="{{ request('semester') == 2 ? 'true' : 'false' }}"
                            id="sem-btn-2">
                        <span>S2</span><small>Semestre 2</small>
                    </button>
                </div>
                <input type="hidden" id="gv-semester" value="{{ request('semester', 1) }}">
            </div>

            <div class="gv-field gv-field--action">
                <label class="gv-label gv-label--ghost" aria-hidden="true">Action</label>
                <button type="button" id="gv-btn-search" class="gv-btn gv-btn--primary gv-btn--full" disabled>
                    <i class="fas fa-magnifying-glass"></i>
                    Afficher les notes
                </button>
            </div>
        </div>

        {{-- Recherche élève (visible uniquement si résultats chargés) --}}
        <div class="gv-searchbar" id="gv-searchbar" style="display:none;">
            <div class="gv-search">
                <i class="fas fa-magnifying-glass" aria-hidden="true"></i>
                <input type="search" id="studentSearch" class="gv-input"
                       placeholder="Rechercher un élève (nom, prénom, matricule)…"
                       autocomplete="off">
            </div>
            <div class="gv-minihelp" id="gvSearchCount"></div>
        </div>

        {{-- Champs cachés --}}
        <input type="hidden" id="gv-year-id"      value="{{ request('year_id') }}">
        <input type="hidden" id="gv-classroom-id" value="{{ request('classroom_id') }}">

    </section>

    {{-- ===== RÉSULTATS ===== --}}
    {{-- Zone résultats — remplie dynamiquement via fetch OU par le rendu serveur initial --}}
    <div id="gv-results-zone">

    @if($classroom && isset($studentsData) && $studentsData->isNotEmpty())

        @php
            $currentSemester = (int) request('semester', 1);

            $trunc2 = function(?float $v): ?float {
                return $v === null ? null : floor($v * 100) / 100;
            };

            $ordinalFr = function(int $n): string {
                return $n === 1 ? '1er' : $n . 'ème';
            };

            $calculerRangs = function(array $moyennes) use ($ordinalFr): array {
                $avecNote = array_filter($moyennes, fn($m) => $m !== null);
                arsort($avecNote);
                $rangs = [];
                $rangDebut = 1;
                $prevMoy = null;
                $nbDansGroupe = 0;
                foreach ($avecNote as $sid => $moy) {
                    if ($prevMoy !== null && $moy == $prevMoy) {
                        $nbDansGroupe++;
                    } else {
                        $rangDebut += $nbDansGroupe;
                        $nbDansGroupe = 1;
                        $prevMoy = $moy;
                    }
                    $rangs[$sid] = $ordinalFr($rangDebut);
                }
                foreach (array_keys(array_filter($moyennes, fn($m) => $m === null)) as $sid) {
                    $rangs[$sid] = '—';
                }
                return $rangs;
            };

            $moyennesGenerales = $studentsData->mapWithKeys(fn($s) => [$s['student']->id => $s['moyenne_generale']])->toArray();
            $rangsSemestre     = $calculerRangs($moyennesGenerales);

            $totalEleves = $studentsData->count();
            $admis = $studentsData->filter(fn($s) => $s['moyenne_generale'] !== null && $s['moyenne_generale'] >= 10)->count();
            $classAvg = $studentsData->filter(fn($s) => $s['moyenne_generale'] !== null)->avg('moyenne_generale');
            $classAvg = $classAvg ? $trunc2((float)$classAvg) : null;

            $rangsAnnuelsVue = [];
            $moyAnnuellesVue = [];

            if ($currentSemester == 2) {
                foreach ($studentsData as $sRow) {
                    $sid = $sRow['student']->id;
                    $rec = $sRow['student']->recordings
                        ->where('year_id', request('year_id'))
                        ->where('classroom_id', request('classroom_id'))
                        ->first();
                    $ms1 = null;
                    if ($rec) {
                        $nS1s = \App\Models\Note::with(['ratio'])
                            ->where('recording_id', $rec->id)
                            ->where('semester', 1)->get();
                        $tp=0; $tc=0;
                        foreach ($nS1s as $nS1) {
                            $int = is_array($nS1->interros) ? $nS1->interros : (json_decode($nS1->interros, true) ?? []);
                            $m = app(\App\Http\Controllers\NoteController::class)->calculateMoyenne20($int, $nS1->devoir1, $nS1->devoir2);
                            if ($m !== null) {
                                $c = $nS1->ratio->coefficient ?? 1;
                                $tp += $m * $c; $tc += $c;
                            }
                        }
                        $ms1 = $tc > 0 ? $trunc2($tp/$tc) : null;
                    }
                    $ms2 = $sRow['moyenne_generale'];
                    if ($ms1 !== null && $ms2 !== null) $moyAnnuellesVue[$sid] = $trunc2((($ms2 * 2) + $ms1) / 3);
                    elseif ($ms2 !== null) $moyAnnuellesVue[$sid] = $ms2;
                    elseif ($ms1 !== null) $moyAnnuellesVue[$sid] = $ms1;
                    else $moyAnnuellesVue[$sid] = null;
                }
                $rangsAnnuelsVue = $calculerRangs($moyAnnuellesVue);
            }
        @endphp

        {{-- Bandeau classe --}}
        <section class="gv-banner" aria-label="Résumé classe">
            <div class="gv-banner__left">
                <div class="gv-badgeIcon" aria-hidden="true"><i class="fas fa-building-columns"></i></div>
                <div>
                    <div class="gv-banner__title">
                        {{ $classroom->promotionSector->promotion_sector }} — {{ $classroom->name }}
                    </div>
                    <div class="gv-banner__meta">
                        Semestre {{ $currentSemester }}
                        · {{ $classroom->promotionSector->sectorYear->year->year }}
                        · {{ $classroom->promotionSector->sectorYear->sector->name_sector }}
                    </div>
                </div>
            </div>
            <div class="gv-stats" role="list">
                <div class="gv-stat" role="listitem">
                    <span class="gv-stat__k">Élèves</span>
                    <span class="gv-stat__v">{{ $totalEleves }}</span>
                </div>
                <div class="gv-stat" role="listitem">
                    <span class="gv-stat__k">Admis</span>
                    <span class="gv-stat__v is-ok">{{ $admis }}</span>
                </div>
                <div class="gv-stat" role="listitem">
                    <span class="gv-stat__k">Ajournés</span>
                    <span class="gv-stat__v is-bad">{{ $totalEleves - $admis }}</span>
                </div>
                <div class="gv-stat" role="listitem">
                    <span class="gv-stat__k">Moy. classe</span>
                    <span class="gv-stat__v {{ $classAvg !== null ? ($classAvg >= 10 ? 'is-ok' : 'is-bad') : '' }}">
                        {{ $classAvg !== null ? number_format($classAvg, 2) : '—' }}
                    </span>
                </div>
                <div class="gv-stat" role="listitem">
                    <span class="gv-stat__k">Matières</span>
                    <span class="gv-stat__v">{{ $subjects->count() }}</span>
                </div>
            </div>
        </section>

        {{-- Liste élèves --}}
        <section class="gv-list" id="studentsList" aria-label="Liste des élèves">
            @foreach($studentsData as $studentRow)
                @php
                    $student         = $studentRow['student'];
                    $notesParMatiere = $studentRow['notes_par_matiere'];
                    $moyGen          = $studentRow['moyenne_generale'];
                    $sid             = $student->id;
                    $rangSem         = $rangsSemestre[$sid] ?? '—';
                    $moyAnn          = $moyAnnuellesVue[$sid] ?? null;
                    $rangAnn         = $rangsAnnuelsVue[$sid] ?? '—';
                    $statusClass     = $moyGen === null ? 'is-na' : ($moyGen >= 10 ? 'is-pass' : 'is-fail');
                    $statusLabel     = $moyGen === null ? 'Non évalué' : ($moyGen >= 10 ? 'Admis' : 'Ajourné');
                    $fullName        = trim(($student->name ?? '') . ' ' . ($student->surname ?? ''));
                    $mat             = $student->matricule ?? 'N/A';
                @endphp

                <article class="gv-student {{ $statusClass }}" data-student="{{ mb_strtolower($fullName.' '.$mat) }}">
                    <details class="gv-details">
                        <summary class="gv-summary">
                            <div class="gv-avatar" aria-hidden="true">
                                {{ strtoupper(mb_substr($student->name ?? 'E', 0, 1)) }}
                            </div>
                            <div class="gv-id">
                                <div class="gv-name">{{ $fullName }}</div>
                                <div class="gv-meta">
                                    Mat. <span class="gv-mono">{{ $mat }}</span>
                                    @if($student->sex) · <span class="gv-pill gv-pill--soft">{{ $student->sex == 'M' ? 'M' : 'F' }}</span> @endif
                                    · <span class="gv-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                                </div>
                            </div>
                            <div class="gv-kpis" aria-label="Indicateurs élève">
                                <div class="gv-kpi">
                                    <span class="gv-kpi__k">Moy. S{{ $currentSemester }}</span>
                                    <span class="gv-kpi__v {{ $moyGen !== null ? ($moyGen >= 10 ? 'is-ok' : 'is-bad') : '' }}">
                                        {{ $moyGen !== null ? number_format($moyGen, 2) : '—' }}
                                    </span>
                                </div>
                                <div class="gv-kpi">
                                    <span class="gv-kpi__k">Rang</span>
                                    <span class="gv-kpi__v is-brand">
                                        {{ $rangSem !== '—' ? $rangSem . '/' . $totalEleves : '—' }}
                                    </span>
                                </div>
                                @if($currentSemester == 2)
                                    <div class="gv-kpi">
                                        <span class="gv-kpi__k">Moy. Ann.</span>
                                        <span class="gv-kpi__v {{ $moyAnn !== null ? ($moyAnn >= 10 ? 'is-ok' : 'is-bad') : '' }}">
                                            {{ $moyAnn !== null ? number_format($moyAnn, 2) : '—' }}
                                        </span>
                                    </div>
                                    <div class="gv-kpi">
                                        <span class="gv-kpi__k">Rang Ann.</span>
                                        <span class="gv-kpi__v is-brand">
                                            {{ $rangAnn !== '—' ? $rangAnn . '/' . $totalEleves : '—' }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="gv-caret" aria-hidden="true">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </summary>

                        <div class="gv-body">
                            <div class="gv-tableWrap" role="region" aria-label="Détail des matières" tabindex="0">
                                <table class="gv-table">
                                    <thead>
                                        <tr>
                                            <th>Matière</th>
                                            <th class="t-center">Interros</th>
                                            <th class="t-center">Moy. I</th>
                                            <th class="t-center">D1</th>
                                            <th class="t-center">D2</th>
                                            <th class="t-center th-strong">Moy /20</th>
                                            <th class="t-center">Coeff</th>
                                            <th class="t-center th-strong">Moy×C</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subjects as $subjectId => $subjectInfo)
                                            @php
                                                $matiere     = $notesParMatiere[$subjectId] ?? null;
                                                $note        = $matiere['note'] ?? null;
                                                $moyInterros = $matiere['moy_interros'] ?? null;
                                                $moy20       = $matiere['moy_20'] ?? null;
                                                $coef        = $subjectInfo['coefficient'];
                                                $moyCoeff    = $moy20 !== null ? $trunc2($moy20 * $coef) : null;
                                                $interros    = $note ? (is_array($note->interros) ? $note->interros : (json_decode($note->interros, true) ?? [])) : [];
                                            @endphp
                                            <tr>
                                                <td data-label="Matière" class="t-name">{{ $subjectInfo['name'] }}</td>
                                                <td data-label="Interros" class="t-center">
                                                    @if(!empty($interros))
                                                        <div class="gv-chips">
                                                            @foreach($interros as $iv)
                                                                <span class="gv-chip {{ $iv >= 10 ? 'is-ok' : 'is-bad' }}">{{ number_format($iv, 2) }}</span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="gv-dash">—</span>
                                                    @endif
                                                </td>
                                                <td data-label="Moy. I" class="t-center">
                                                    @if($moyInterros !== null)
                                                        <span class="gv-mark {{ $moyInterros >= 10 ? 'is-ok' : 'is-bad' }}">{{ number_format($moyInterros, 2) }}</span>
                                                    @else <span class="gv-dash">—</span> @endif
                                                </td>
                                                <td data-label="D1" class="t-center">
                                                    @if($note?->devoir1 !== null)
                                                        <span class="gv-mark {{ $note->devoir1 >= 10 ? 'is-ok' : 'is-bad' }}">{{ number_format($note->devoir1, 2) }}</span>
                                                    @else <span class="gv-dash">—</span> @endif
                                                </td>
                                                <td data-label="D2" class="t-center">
                                                    @if($note?->devoir2 !== null)
                                                        <span class="gv-mark {{ $note->devoir2 >= 10 ? 'is-ok' : 'is-bad' }}">{{ number_format($note->devoir2, 2) }}</span>
                                                    @else <span class="gv-dash">—</span> @endif
                                                </td>
                                                <td data-label="Moy /20" class="t-center">
                                                    @if($moy20 !== null)
                                                        <span class="gv-final {{ $moy20 >= 10 ? 'is-ok' : 'is-bad' }}">{{ number_format($moy20, 2) }}</span>
                                                    @else <span class="gv-dash">—</span> @endif
                                                </td>
                                                <td data-label="Coeff" class="t-center">
                                                    <span class="gv-coef">{{ $coef }}</span>
                                                </td>
                                                <td data-label="Moy×C" class="t-center">
                                                    @if($moyCoeff !== null)
                                                        <span class="gv-mark {{ $moyCoeff >= 10 * $coef ? 'is-ok' : 'is-warn' }}">{{ number_format($moyCoeff, 2) }}</span>
                                                    @else <span class="gv-dash">—</span> @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </details>
                </article>
            @endforeach
        </section>

    @elseif(request()->has(['year_id','classroom_id','semester']))
        <section class="gv-empty" aria-label="Aucune note">
            <div class="gv-empty__icon"><i class="fas fa-book-open"></i></div>
            <h3>Aucune note disponible</h3>
            <p>Aucune note n'a été enregistrée pour cette classe et ce semestre.</p>
        </section>
    @else
        <section class="gv-empty" id="gv-empty-default" aria-label="Démarrer">
            <div class="gv-empty__icon"><i class="fas fa-magnifying-glass"></i></div>
            <h3>Commencez votre recherche</h3>
            <p>Choisissez une <strong>filière</strong>, une <strong>promotion</strong> et une <strong>classe</strong>, puis cliquez sur <strong>Afficher les notes</strong>.</p>
        </section>
    @endif

    </div>{{-- /#gv-results-zone --}}

</main>
@endsection

@section('another_JS')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const BASE_URL = '{{ rtrim(url('/'), '/') }}';
    const CSRF     = '{{ csrf_token() }}';

    // ── État de la cascade ──────────────────────────────────
    const state = {
        yearId:      null,
        sectorId:    null,
        promotionId: null,
        classroomId: null,
    };

    // ── Utilitaires ─────────────────────────────────────────
    const $  = id  => document.getElementById(id);
    const $$ = sel => document.querySelectorAll(sel);

    async function apiFetch(path) {
        const url = path.startsWith('http') ? path : BASE_URL + path;
        const r   = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        if (!r.ok) throw new Error(`Erreur ${r.status}`);
        return r.json();
    }

    async function apiPost(path, body) {
        const url = path.startsWith('http') ? path : BASE_URL + path;
        const r   = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(body)
        });
        return r.json();
    }

    // ── Semestre toggle ──────────────────────────────────────
    $$('.gv-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            $$('.gv-tab').forEach(b => { b.classList.remove('is-active'); b.setAttribute('aria-pressed', 'false'); });
            btn.classList.add('is-active');
            btn.setAttribute('aria-pressed', 'true');
            $('gv-semester').value = btn.dataset.value;
        });
    });

    // ── Bouton "Afficher les notes" ──────────────────────────
    $('gv-btn-search').addEventListener('click', () => loadResults());

    // ── Étape 0 : Année active ───────────────────────────────
    async function loadActiveYear() {
        try {
            const d = await apiFetch('/api/active-year');
            if (d.success && d.year) {
                state.yearId = d.year.id;
                $('gv-year-id').value = d.year.id;
                setBc('year', d.year.year);
                await loadSectors();
            } else {
                renderOpts('sector', [], 'Aucune année active dans le système.');
            }
        } catch(e) {
            renderOpts('sector', [], 'Erreur de chargement : ' + e.message);
        }
    }

    // ── Étape 1 : Filières ────────────────────────────────────
    async function loadSectors() {
        setLoading('sector', true);
        $('gv-step-sector').classList.remove('gv-step--locked');
        try {
            const d = await apiFetch(`/api/sectors-for-create/${state.yearId}`);
            setLoading('sector', false);
            renderOpts('sector', d.sectors || [], 'Aucune filière disponible.', onSector);
            if (d.auto && d.sectors?.length) await onSector(d.sectors[0], true);
        } catch(e) { setLoading('sector', false); }
    }

    async function onSector(s, auto = false) {
        markActive('sector', s.id);
        state.sectorId = s.id;
        setBc('sector', s.name);
        resetFrom('promo');
        await loadPromotions();
    }

    // ── Étape 2 : Promotions ──────────────────────────────────
    async function loadPromotions() {
        setLoading('promo', true);
        $('gv-step-promo').classList.remove('gv-step--locked');
        try {
            const d = await apiFetch(`/api/promotions-for-create/${state.yearId}/${state.sectorId}`);
            setLoading('promo', false);
            renderOpts('promo', d.promotions || [], 'Aucune promotion disponible.', onPromotion);
            if (d.auto && d.promotions?.length) await onPromotion(d.promotions[0], true);
        } catch(e) { setLoading('promo', false); }
    }

    async function onPromotion(p, auto = false) {
        markActive('promo', p.id);
        state.promotionId = p.id;
        setBc('promo', p.name);
        resetFrom('class');
        await loadClassrooms();
    }

    // ── Étape 3 : Classes ─────────────────────────────────────
    async function loadClassrooms() {
        setLoading('class', true);
        $('gv-step-class').classList.remove('gv-step--locked');
        try {
            const d = await apiFetch(`/api/classrooms-for-create/${state.promotionId}?year_id=${state.yearId}`);
            setLoading('class', false);
            renderOpts('class', d.classrooms || [], 'Aucune classe disponible.', onClassroom);
            if (d.auto && d.classrooms?.length) await onClassroom(d.classrooms[0], true);
        } catch(e) { setLoading('class', false); }
    }

    function onClassroom(c, auto = false) {
        markActive('class', c.id);
        state.classroomId = c.id;
        $('gv-classroom-id').value = c.id;
        setBc('class', c.name);
        $('gv-btn-search').disabled = false;
    }

    // ── Chargement des résultats (navigation vers l'URL filtrée) ──
    function loadResults() {
        const classroomId = state.classroomId;
        const yearId      = state.yearId;
        const semester    = $('gv-semester').value;

        if (!classroomId || !yearId || !semester) return;

        const url = new URL(window.location.href);
        url.searchParams.set('year_id',      yearId);
        url.searchParams.set('classroom_id', classroomId);
        url.searchParams.set('semester',     semester);
        window.location.href = url.toString();
    }

    // ── Helpers UI ───────────────────────────────────────────
    function renderOpts(step, items, emptyMsg, onClick) {
        const container = $(`gv-opts-${step}`);
        if (!container) return;
        if (!items.length) {
            container.innerHTML = `<div class="gv-ph gv-ph--warn">${emptyMsg}</div>`;
            return;
        }
        container.innerHTML = items.map(item => `
            <button type="button" class="gv-opt" data-id="${item.id}" data-step="${step}">
                <span class="gv-opt-label">${escHtml(item.name)}</span>
                <span class="gv-opt-arrow">›</span>
            </button>
        `).join('');
        container.querySelectorAll('.gv-opt').forEach(btn => {
            btn.addEventListener('click', () => {
                const item = items.find(i => String(i.id) === btn.dataset.id);
                if (item) onClick(item);
            });
        });
    }

    function markActive(step, id) {
        const container = $(`gv-opts-${step}`);
        if (!container) return;
        container.querySelectorAll('.gv-opt').forEach(btn => {
            btn.classList.toggle('gv-opt--active', String(btn.dataset.id) === String(id));
        });
    }

    function setLoading(step, on) {
        const el = $(`gv-loader-${step}`);
        if (el) el.style.display = on ? 'flex' : 'none';
    }

    function setBc(part, label) {
        const lbl  = $(`gv-bc-${part}-lbl`); if (lbl) lbl.textContent = label;
        const item = $(`gv-bc-${part}`);      if (item) item.classList.add('active');
    }

    function resetFrom(step) {
        const steps = ['sector', 'promo', 'class'];
        const idx   = steps.indexOf(step);
        for (let i = idx; i < steps.length; i++) {
            const s    = steps[i];
            const stepEl = $(`gv-step-${s}`);
            const optsEl = $(`gv-opts-${s}`);
            if (stepEl) stepEl.classList.add('gv-step--locked');
            if (optsEl) optsEl.innerHTML = `<div class="gv-ph">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                Choisissez d'abord ${s === 'promo' ? 'une filière' : 'une promotion'}
            </div>`;
            const bcLbl = $(`gv-bc-${s}-lbl`);
            if (bcLbl) bcLbl.textContent = s === 'promo' ? 'Promotion' : 'Classe';
            const bcItem = $(`gv-bc-${s}`);
            if (bcItem) bcItem.classList.remove('active');
            if (s === 'promo')  state.promotionId = null;
            if (s === 'class')  { state.classroomId = null; $('gv-classroom-id').value = ''; $('gv-btn-search').disabled = true; }
        }
    }

    function escHtml(str) {
        const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML;
    }

    // ── Pré-remplir la cascade si résultats déjà chargés (retour page) ──
    // Si l'URL contient classroom_id, on recharge l'année active + cascade
    // et on marque visuellement la sélection actuelle
    const urlParams   = new URLSearchParams(window.location.search);
    const urlClassId  = urlParams.get('classroom_id');
    const urlYearId   = urlParams.get('year_id');
    const urlSemester = urlParams.get('semester');

    if (urlSemester) {
        $$('.gv-tab').forEach(b => {
            const isActive = b.dataset.value === urlSemester;
            b.classList.toggle('is-active', isActive);
            b.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
        $('gv-semester').value = urlSemester;
    }

    // Afficher la barre de recherche si résultats présents
    const hasList = document.querySelector('#studentsList');
    if (hasList) {
        $('gv-searchbar').style.display = 'flex';
        initSearch();
    }

    // ── Recherche instantanée ────────────────────────────────
    function initSearch() {
        const qInput  = $('studentSearch');
        const list    = document.getElementById('studentsList');
        const countBox = $('gvSearchCount');
        if (!qInput || !list) return;

        const cards = Array.from(list.querySelectorAll('.gv-student'));
        const total = cards.length;
        refreshCount(total, total);

        qInput.addEventListener('input', () => {
            const q = (qInput.value || '').trim().toLowerCase();
            let visible = 0;
            cards.forEach(card => {
                const hay = card.getAttribute('data-student') || '';
                const ok  = !q || hay.includes(q);
                card.style.display = ok ? '' : 'none';
                if (ok) visible++;
            });
            refreshCount(visible, total);
        });

        function refreshCount(visible, total) {
            if (!countBox) return;
            countBox.textContent = qInput.value
                ? `${visible}/${total} affiché(s)`
                : `${total} élève(s)`;
        }
    }

    // ── Accordéons ──────────────────────────────────────────
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function getAllDetails() {
        const list = document.getElementById('studentsList');
        return Array.from((list || document).querySelectorAll('details.gv-details'));
    }

    function syncToggleAllLabel() {
        const btn = $('btnToggleAll');
        if (!btn) return;
        const anyClosed = getAllDetails().some(d => !d.open);
        btn.innerHTML = anyClosed
            ? '<i class="fas fa-layer-group"></i> Tout ouvrir'
            : '<i class="fas fa-layer-group"></i> Tout fermer';
    }

    function animateDetails(detailsEl, open) {
        if (prefersReducedMotion) { detailsEl.open = open; return Promise.resolve(); }
        if (detailsEl.dataset.animating === '1') return Promise.resolve();
        detailsEl.dataset.animating = '1';
        const summary     = detailsEl.querySelector('summary');
        const body        = detailsEl.querySelector('.gv-body');
        const closedHeight = summary.offsetHeight;
        const startHeight  = detailsEl.offsetHeight;
        if (open) detailsEl.open = true;
        void detailsEl.offsetHeight;
        const endHeight = open ? detailsEl.offsetHeight : closedHeight;
        detailsEl.style.height = `${startHeight}px`;
        if (body) {
            body.animate(
                open
                    ? [{ opacity: 0, transform: 'translateY(-6px)' }, { opacity: 1, transform: 'translateY(0)' }]
                    : [{ opacity: 1, transform: 'translateY(0)' }, { opacity: 0, transform: 'translateY(-4px)' }],
                { duration: open ? 260 : 200, easing: 'cubic-bezier(.2,.8,.2,1)', fill: 'both' }
            );
        }
        const anim = detailsEl.animate(
            [{ height: `${startHeight}px` }, { height: `${endHeight}px` }],
            { duration: open ? 360 : 320, easing: 'cubic-bezier(.22,1,.36,1)' }
        );
        return new Promise(resolve => {
            anim.onfinish = () => {
                if (!open) detailsEl.open = false;
                detailsEl.style.height = '';
                delete detailsEl.dataset.animating;
                resolve();
            };
            anim.oncancel = () => { delete detailsEl.dataset.animating; resolve(); };
        });
    }

    getAllDetails().forEach(detailsEl => {
        const summary = detailsEl.querySelector('summary');
        if (!summary) return;
        summary.addEventListener('click', async e => {
            e.preventDefault();
            await animateDetails(detailsEl, !detailsEl.open);
            syncToggleAllLabel();
        });
        detailsEl.addEventListener('toggle', syncToggleAllLabel);
    });

    getAllDetails().forEach(d => d.open = false);
    syncToggleAllLabel();

    const btnAll = $('btnToggleAll');
    btnAll?.addEventListener('click', async () => {
        const all      = getAllDetails();
        const anyClosed = all.some(d => !d.open);
        for (const d of all) await animateDetails(d, anyClosed);
        syncToggleAllLabel();
    });

    window.addEventListener('beforeprint', () => getAllDetails().forEach(d => d.open = true));
    window.addEventListener('afterprint',  () => { getAllDetails().forEach(d => d.open = false); syncToggleAllLabel(); });

    // ── Démarrage ────────────────────────────────────────────
    loadActiveYear();
});
</script>

<style>
:root{
  --bg:#f3f5f9; --card:#ffffff; --muted:#6b7280; --text:#0f172a; --border:#e6eaf0;
  --brand:#3b5bdb; --brand2:#6d28d9;
  --ok:#16a34a; --bad:#dc2626; --warn:#f59e0b;
  --okBg:#ecfdf3; --badBg:#fff1f2; --warnBg:#fffbeb;
  --radius:16px; --radius2:20px;
  --shadow:0 10px 30px rgba(15,23,42,.06);
  --shadow2:0 14px 45px rgba(15,23,42,.10);
  --mono:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;
  --sans:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
  --tr:.18s cubic-bezier(.4,0,.2,1);
}
*{ box-sizing:border-box; }
body{ background:var(--bg); }
.gv{ font-family:var(--sans); color:var(--text); padding:18px; display:flex; flex-direction:column; gap:14px; }

/* Topbar */
.gv-topbar{ background:linear-gradient(135deg,rgba(59,91,219,.12),rgba(109,40,217,.08)),var(--card); border:1px solid rgba(230,234,240,.9); border-radius:var(--radius2); box-shadow:var(--shadow); padding:14px 16px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }
.gv-topbar__left{ display:flex; align-items:center; gap:12px; }
.gv-appmark{ width:46px; height:46px; border-radius:14px; display:grid; place-items:center; background:linear-gradient(135deg,var(--brand),var(--brand2)); color:white; box-shadow:0 10px 25px rgba(59,91,219,.25); }
.gv-title{ margin:0; font-size:1.2rem; font-weight:800; letter-spacing:.2px; }
.gv-subtitle{ margin:2px 0 0; font-size:.9rem; color:var(--muted); }
.gv-topbar__right{ display:flex; gap:10px; flex-wrap:wrap; }

/* Buttons */
.gv-btn{ border:1px solid var(--border); background:var(--card); color:var(--text); border-radius:12px; height:42px; padding:0 14px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; font-weight:700; transition:transform var(--tr),box-shadow var(--tr),background var(--tr),border-color var(--tr); }
.gv-btn:hover{ transform:translateY(-1px); box-shadow:var(--shadow); border-color:rgba(59,91,219,.25); }
.gv-btn--primary{ border:none; color:white; background:linear-gradient(135deg,var(--brand),var(--brand2)); }
.gv-btn--primary:hover{ box-shadow:0 12px 35px rgba(59,91,219,.28); }
.gv-btn--primary:disabled{ opacity:.4; cursor:not-allowed; transform:none; box-shadow:none; }
.gv-btn--soft{ background:rgba(15,23,42,.04); }
.gv-btn--full{ width:100%; justify-content:center; height:42px; }

/* Panel */
.gv-panel{ background:var(--card); border:1px solid var(--border); border-radius:var(--radius2); box-shadow:var(--shadow); padding:16px; }

/* Breadcrumb */
.gv-breadcrumb{ display:flex; align-items:center; gap:6px; padding:9px 12px; background:#f8fafc; border:1px solid var(--border); border-radius:10px; margin-bottom:14px; flex-wrap:wrap; }
.gv-bc-item{ display:flex; align-items:center; gap:5px; padding:3px 8px; border-radius:6px; color:#94a3b8; font-size:.74rem; font-weight:600; transition:all .2s; }
.gv-bc-year{ color:var(--brand); font-weight:800; }
.gv-bc-item.active{ color:#334155; background:rgba(59,91,219,.07); }
.gv-bc-sep{ color:#e2e8f0; }

/* Étapes */
.gv-step{ border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:12px; transition:opacity .2s,filter .2s; }
.gv-step--locked{ opacity:.5; pointer-events:none; filter:grayscale(.3); }
.gv-step-head{ display:flex; align-items:center; gap:12px; padding:10px 14px; background:#f8fafc; border-bottom:1px solid var(--border); }
.gv-step-num{ width:24px; height:24px; border-radius:7px; background:rgba(59,91,219,.1); border:1px solid rgba(59,91,219,.2); color:var(--brand); font-weight:800; font-size:.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.gv-step-info{ flex:1; }
.gv-step-title{ font-weight:700; font-size:.82rem; color:var(--text); }
.gv-step-hint{ font-size:.71rem; color:var(--muted); margin-top:1px; }
.gv-step-loader{ display:flex; align-items:center; }
.gv-spin-sm{ width:13px; height:13px; border:2px solid var(--border); border-top-color:var(--brand); border-radius:50%; animation:gvSpin .7s linear infinite; }
@keyframes gvSpin{ to{ transform:rotate(360deg); } }
.gv-step-opts{ padding:10px 14px; display:flex; flex-wrap:wrap; gap:8px; min-height:46px; align-items:center; }
.gv-ph{ display:flex; align-items:center; gap:7px; font-size:.78rem; color:#94a3b8; font-style:italic; }
.gv-ph--warn{ color:var(--warn); }

/* Option cards */
.gv-opt{ display:inline-flex; align-items:center; gap:7px; padding:7px 13px; background:var(--card); border:1.5px solid var(--border); border-radius:10px; cursor:pointer; font-family:var(--sans); font-size:.82rem; font-weight:600; color:#374151; transition:all .18s; white-space:nowrap; }
.gv-opt:hover{ border-color:rgba(59,91,219,.35); background:rgba(59,91,219,.05); color:var(--brand); transform:translateY(-1px); box-shadow:var(--shadow); }
.gv-opt--active{ border-color:var(--brand); background:rgba(59,91,219,.07); color:var(--brand); box-shadow:0 0 0 3px rgba(59,91,219,.1); }
.gv-opt-arrow{ opacity:.4; }
.gv-opt--active .gv-opt-arrow{ opacity:1; }

/* Séparateur */
.gv-sep-line{ display:flex; align-items:center; gap:12px; margin:14px 0; }
.gv-sep-line::before,.gv-sep-line::after{ content:''; flex:1; height:1px; background:var(--border); }
.gv-sep-line span{ font-size:.68rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#94a3b8; white-space:nowrap; }

/* Config row */
.gv-config-row{ display:grid; grid-template-columns:auto 220px; gap:14px; align-items:end; }
.gv-field{ display:flex; flex-direction:column; gap:6px; }
.gv-label{ font-size:.78rem; color:var(--muted); font-weight:800; text-transform:uppercase; letter-spacing:.35px; display:flex; align-items:center; gap:7px; }
.gv-label--ghost{ opacity:0; pointer-events:none; }

/* Semestre tabs */
.gv-semtabs{ height:42px; display:flex; border:1px solid var(--border); border-radius:12px; overflow:hidden; background:#f8fafc; }
.gv-tab{ flex:1; border:none; background:transparent; cursor:pointer; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:2px; font-weight:900; transition:background var(--tr),color var(--tr); }
.gv-tab small{ font-weight:700; color:#8b95a6; font-size:.68rem; }
.gv-tab.is-active{ background:linear-gradient(135deg,var(--brand),var(--brand2)); color:#fff; }
.gv-tab.is-active small{ color:rgba(255,255,255,.75); }

/* Searchbar */
.gv-searchbar{ margin-top:12px; display:flex; gap:12px; align-items:center; justify-content:space-between; flex-wrap:wrap; }
.gv-search{ flex:1; min-width:260px; display:flex; align-items:center; gap:10px; border:1px solid var(--border); background:#fff; border-radius:14px; padding:0 12px; height:44px; }
.gv-search i{ color:#64748b; }
.gv-input{ border:none; box-shadow:none; height:42px; padding:0; background:transparent; outline:none; width:100%; font-size:.9rem; }
.gv-minihelp{ color:var(--muted); font-weight:800; }

/* Banner */
.gv-banner{ background:var(--card); border:1px solid var(--border); border-radius:var(--radius2); box-shadow:var(--shadow); padding:14px 16px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; position:relative; overflow:hidden; }
.gv-banner::before{ content:""; position:absolute; inset:-60px auto auto -60px; width:160px; height:160px; border-radius:50%; background:radial-gradient(circle,rgba(59,91,219,.18),transparent 60%); }
.gv-banner__left{ display:flex; align-items:center; gap:12px; z-index:1; }
.gv-badgeIcon{ width:44px; height:44px; border-radius:14px; display:grid; place-items:center; background:rgba(59,91,219,.10); color:rgba(59,91,219,.95); }
.gv-banner__title{ font-weight:900; }
.gv-banner__meta{ color:var(--muted); font-weight:700; font-size:.9rem; margin-top:2px; }
.gv-stats{ display:flex; gap:10px; flex-wrap:wrap; z-index:1; }
.gv-stat{ border:1px solid var(--border); background:#fff; border-radius:14px; padding:10px 12px; min-width:140px; }
.gv-stat__k{ display:block; color:var(--muted); font-weight:900; font-size:.78rem; text-transform:uppercase; letter-spacing:.35px; }
.gv-stat__v{ display:block; margin-top:4px; font-weight:900; font-family:var(--mono); font-size:1.05rem; }
.gv-stat__v.is-ok{ color:var(--ok); }
.gv-stat__v.is-bad{ color:var(--bad); }

/* Student list */
.gv-list{ display:flex; flex-direction:column; gap:12px; }
.gv-student{ background:var(--card); border:1px solid var(--border); border-radius:var(--radius2); box-shadow:var(--shadow); overflow:hidden; transition:box-shadow var(--tr),transform var(--tr); }
.gv-student:hover{ box-shadow:var(--shadow2); transform:translateY(-1px); }
.gv-student.is-pass{ border-left:5px solid rgba(22,163,74,.95); }
.gv-student.is-fail{ border-left:5px solid rgba(220,38,38,.95); }
.gv-student.is-na{   border-left:5px solid rgba(148,163,184,.9); }
.gv-details > summary{ list-style:none; }
.gv-details > summary::-webkit-details-marker{ display:none; }
.gv-summary{ display:flex; align-items:center; gap:12px; padding:12px 14px; background:linear-gradient(180deg,rgba(248,250,252,.9),rgba(255,255,255,.95)); border-bottom:1px solid var(--border); cursor:pointer; }
.gv-avatar{ width:44px; height:44px; border-radius:14px; display:grid; place-items:center; background:linear-gradient(135deg,rgba(59,91,219,.95),rgba(109,40,217,.95)); color:#fff; font-weight:900; }
.gv-id{ flex:1; min-width:180px; }
.gv-name{ font-weight:1000; letter-spacing:.2px; }
.gv-meta{ margin-top:2px; color:var(--muted); font-weight:800; font-size:.88rem; display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.gv-mono{ font-family:var(--mono); }
.gv-pill{ border-radius:999px; padding:3px 10px; border:1px solid var(--border); background:rgba(15,23,42,.03); font-size:.78rem; font-weight:1000; }
.gv-pill.is-pass{ background:var(--okBg); border-color:rgba(22,163,74,.18); color:var(--ok); }
.gv-pill.is-fail{ background:var(--badBg); border-color:rgba(220,38,38,.18); color:var(--bad); }
.gv-pill.is-na{   background:#f1f5f9; border-color:rgba(148,163,184,.35); color:#475569; }
.gv-pill--soft{ background:rgba(15,23,42,.03); }
.gv-kpis{ display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
.gv-kpi{ border:1px solid var(--border); background:#fff; border-radius:14px; padding:8px 10px; min-width:120px; }
.gv-kpi__k{ display:block; color:var(--muted); font-weight:900; font-size:.72rem; text-transform:uppercase; letter-spacing:.35px; }
.gv-kpi__v{ display:block; margin-top:3px; font-family:var(--mono); font-weight:1000; }
.gv-kpi__v.is-ok{ color:var(--ok); }
.gv-kpi__v.is-bad{ color:var(--bad); }
.gv-kpi__v.is-brand{ color:rgba(59,91,219,.95); }
.gv-caret{ width:36px; height:36px; border-radius:12px; display:grid; place-items:center; background:rgba(15,23,42,.04); border:1px solid var(--border); color:#334155; transition:transform var(--tr); }
details[open] .gv-caret{ transform:rotate(180deg); }
.gv-body{ padding:12px 14px 16px; background:#fff; }

/* Table */
.gv-tableWrap{ overflow:auto; border-radius:16px; border:1px solid var(--border); }
.gv-table{ width:100%; border-collapse:separate; border-spacing:0; min-width:920px; background:#fff; }
.gv-table thead th{ position:sticky; top:0; z-index:2; background:#f8fafc; border-bottom:1px solid var(--border); padding:10px; text-align:left; font-size:.78rem; text-transform:uppercase; letter-spacing:.35px; color:#475569; }
.gv-table tbody td{ border-bottom:1px solid var(--border); padding:10px; vertical-align:middle; font-weight:800; }
.gv-table tbody tr:nth-child(2n) td{ background:rgba(15,23,42,.015); }
.gv-table tbody tr:hover td{ background:rgba(59,91,219,.04); }
.t-center{ text-align:center; }
.t-name{ font-weight:1000; }
.th-strong{ color:rgba(59,91,219,.95); }
.gv-chips{ display:flex; gap:6px; justify-content:center; flex-wrap:wrap; }
.gv-chip{ font-family:var(--mono); font-weight:1000; font-size:.72rem; padding:3px 8px; border-radius:999px; border:1px solid var(--border); background:#fff; }
.gv-chip.is-ok{ background:var(--okBg); border-color:rgba(22,163,74,.18); color:var(--ok); }
.gv-chip.is-bad{ background:var(--badBg); border-color:rgba(220,38,38,.18); color:var(--bad); }
.gv-mark{ display:inline-flex; align-items:center; justify-content:center; min-width:58px; padding:4px 10px; border-radius:12px; font-family:var(--mono); font-weight:1000; border:1px solid var(--border); }
.gv-mark.is-ok{ background:var(--okBg); border-color:rgba(22,163,74,.18); color:var(--ok); }
.gv-mark.is-bad{ background:var(--badBg); border-color:rgba(220,38,38,.18); color:var(--bad); }
.gv-mark.is-warn{ background:var(--warnBg); border-color:rgba(245,158,11,.22); color:#b45309; }
.gv-final{ display:inline-flex; align-items:center; justify-content:center; min-width:66px; padding:5px 12px; border-radius:12px; font-family:var(--mono); font-weight:1100; border:1px solid var(--border); }
.gv-final.is-ok{ background:var(--okBg); border-color:rgba(22,163,74,.18); color:var(--ok); }
.gv-final.is-bad{ background:var(--badBg); border-color:rgba(220,38,38,.18); color:var(--bad); }
.gv-coef{ display:inline-flex; align-items:center; justify-content:center; min-width:44px; padding:4px 10px; border-radius:999px; border:1px solid var(--border); background:rgba(15,23,42,.03); font-family:var(--mono); }
.gv-dash{ color:#94a3b8; font-family:var(--mono); }

/* Empty */
.gv-empty{ background:var(--card); border:1px dashed rgba(148,163,184,.7); border-radius:var(--radius2); padding:52px 18px; text-align:center; color:var(--muted); box-shadow:var(--shadow); }
.gv-empty__icon{ width:68px; height:68px; border-radius:20px; margin:0 auto 10px; display:grid; place-items:center; background:rgba(59,91,219,.08); border:1px solid rgba(59,91,219,.14); color:rgba(59,91,219,.95); font-size:1.55rem; }
.gv-empty h3{ margin:0 0 6px; color:#334155; font-weight:1000; }

/* Accordion animation */
details.gv-details{ overflow:hidden; will-change:height; }
@media(prefers-reduced-motion:reduce){ details.gv-details{ will-change:auto; } }

/* Responsive */
@media(max-width:1100px){
  .gv-config-row{ grid-template-columns:1fr; }
  .gv-kpis{ justify-content:flex-start; }
}
@media(max-width:720px){
  .gv{ padding:12px; }
  .gv-summary{ align-items:flex-start; flex-wrap:wrap; }
  .gv-kpi{ min-width:46%; }
  .gv-table{ min-width:unset; }
  .gv-table thead{ display:none; }
  .gv-table,.gv-table tbody,.gv-table tr,.gv-table td{ display:block; width:100%; }
  .gv-table tr{ border-bottom:1px solid var(--border); padding:10px; }
  .gv-table td{ border:none; display:flex; justify-content:space-between; gap:12px; padding:8px 0; }
  .gv-table td::before{ content:attr(data-label); font-weight:1000; color:#64748b; text-transform:uppercase; letter-spacing:.3px; font-size:.72rem; }
  .t-center{ text-align:right; }
}

/* Print */
@media print{
  .gv-panel,.gv-topbar__right,.gv-searchbar{ display:none !important; }
  .gv-student,.gv-banner{ box-shadow:none !important; }
  .gv-tableWrap{ overflow:visible !important; }
  .gv-table thead th{ position:static !important; }
}
</style>
@endsection@extends('layouts.template')

@section('content')
<main class="gv" aria-label="Consultation des notes">

    {{-- ===== TOPBAR ===== --}}
    <header class="gv-topbar">
        <div class="gv-topbar__left">
            <div class="gv-appmark" aria-hidden="true">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="gv-headings">
                <h1 class="gv-title">Consultation des Notes</h1>
                <p class="gv-subtitle">Sélectionnez la classe et le semestre pour afficher les résultats.</p>
            </div>
        </div>
        <div class="gv-topbar__right">
            @if($classroom && isset($studentsData) && $studentsData->isNotEmpty())
                <button class="gv-btn gv-btn--soft" type="button" id="btnToggleAll">
                    <i class="fas fa-layer-group"></i> Tout ouvrir
                </button>
                <button class="gv-btn gv-btn--primary" type="button" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            @endif
        </div>
    </header>

    {{-- ===== PANNEAU FILTRES (cascade intelligente) ===== --}}
    <section class="gv-panel" aria-label="Filtres">

        {{-- Fil d'Ariane --}}
        <div class="gv-breadcrumb" id="gv-bc">
            <div class="gv-bc-item gv-bc-year" id="gv-bc-year">
                <i class="fas fa-calendar-alt" style="font-size:.7rem"></i>
                <span id="gv-bc-year-lbl">Année</span>
            </div>
            <span class="gv-bc-sep">›</span>
            <div class="gv-bc-item" id="gv-bc-sector"><span id="gv-bc-sector-lbl">Filière</span></div>
            <span class="gv-bc-sep">›</span>
            <div class="gv-bc-item" id="gv-bc-promo"><span id="gv-bc-promo-lbl">Promotion</span></div>
            <span class="gv-bc-sep">›</span>
            <div class="gv-bc-item" id="gv-bc-class"><span id="gv-bc-class-lbl">Classe</span></div>
        </div>

        {{-- Étape 1 : Filière --}}
        <div class="gv-step" id="gv-step-sector">
            <div class="gv-step-head">
                <div class="gv-step-num">1</div>
                <div class="gv-step-info">
                    <div class="gv-step-title">Filière</div>
                    <div class="gv-step-hint">Choisissez la filière d'enseignement</div>
                </div>
                <div class="gv-step-loader" id="gv-loader-sector" style="display:none;">
                    <div class="gv-spin-sm"></div>
                </div>
            </div>
            <div class="gv-step-opts" id="gv-opts-sector">
                <div class="gv-ph">Chargement de l'année active…</div>
            </div>
        </div>

        {{-- Étape 2 : Promotion --}}
        <div class="gv-step gv-step--locked" id="gv-step-promo">
            <div class="gv-step-head">
                <div class="gv-step-num">2</div>
                <div class="gv-step-info">
                    <div class="gv-step-title">Promotion</div>
                    <div class="gv-step-hint">Sélectionnez la promotion dans la filière</div>
                </div>
                <div class="gv-step-loader" id="gv-loader-promo" style="display:none;">
                    <div class="gv-spin-sm"></div>
                </div>
            </div>
            <div class="gv-step-opts" id="gv-opts-promo">
                <div class="gv-ph">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                    Choisissez d'abord une filière
                </div>
            </div>
        </div>

        {{-- Étape 3 : Classe --}}
        <div class="gv-step gv-step--locked" id="gv-step-class">
            <div class="gv-step-head">
                <div class="gv-step-num">3</div>
                <div class="gv-step-info">
                    <div class="gv-step-title">Classe</div>
                    <div class="gv-step-hint">Choisissez la classe à consulter</div>
                </div>
                <div class="gv-step-loader" id="gv-loader-class" style="display:none;">
                    <div class="gv-spin-sm"></div>
                </div>
            </div>
            <div class="gv-step-opts" id="gv-opts-class">
                <div class="gv-ph">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                    Choisissez d'abord une promotion
                </div>
            </div>
        </div>

        {{-- Séparateur --}}
        <div class="gv-sep-line">
            <span>Semestre & Recherche</span>
        </div>

        {{-- Semestre + bouton rechercher --}}
        <div class="gv-config-row">
            <div class="gv-field gv-field--sem">
                <span class="gv-label">
                    <i class="fas fa-layer-group" aria-hidden="true"></i>
                    Semestre
                </span>
                <div class="gv-semtabs" role="tablist" aria-label="Choix du semestre">
                    <button type="button"
                            class="gv-tab {{ request('semester', 1) == 1 ? 'is-active' : '' }}"
                            data-value="1"
                            aria-pressed="{{ request('semester', 1) == 1 ? 'true' : 'false' }}"
                            id="sem-btn-1">
                        <span>S1</span><small>Semestre 1</small>
                    </button>
                    <button type="button"
                            class="gv-tab {{ request('semester') == 2 ? 'is-active' : '' }}"
                            data-value="2"
                            aria-pressed="{{ request('semester') == 2 ? 'true' : 'false' }}"
                            id="sem-btn-2">
                        <span>S2</span><small>Semestre 2</small>
                    </button>
                </div>
                <input type="hidden" id="gv-semester" value="{{ request('semester', 1) }}">
            </div>

            <div class="gv-field gv-field--action">
                <label class="gv-label gv-label--ghost" aria-hidden="true">Action</label>
                <button type="button" id="gv-btn-search" class="gv-btn gv-btn--primary gv-btn--full" disabled>
                    <i class="fas fa-magnifying-glass"></i>
                    Afficher les notes
                </button>
            </div>
        </div>

        {{-- Recherche élève (visible uniquement si résultats chargés) --}}
        <div class="gv-searchbar" id="gv-searchbar" style="display:none;">
            <div class="gv-search">
                <i class="fas fa-magnifying-glass" aria-hidden="true"></i>
                <input type="search" id="studentSearch" class="gv-input"
                       placeholder="Rechercher un élève (nom, prénom, matricule)…"
                       autocomplete="off">
            </div>
            <div class="gv-minihelp" id="gvSearchCount"></div>
        </div>

        {{-- Champs cachés --}}
        <input type="hidden" id="gv-year-id"      value="{{ request('year_id') }}">
        <input type="hidden" id="gv-classroom-id" value="{{ request('classroom_id') }}">

    </section>

    {{-- ===== RÉSULTATS ===== --}}
    {{-- Zone résultats — remplie dynamiquement via fetch OU par le rendu serveur initial --}}
    <div id="gv-results-zone">

    @if($classroom && isset($studentsData) && $studentsData->isNotEmpty())

        @php
            $currentSemester = (int) request('semester', 1);

            $trunc2 = function(?float $v): ?float {
                return $v === null ? null : floor($v * 100) / 100;
            };

            $ordinalFr = function(int $n): string {
                return $n === 1 ? '1er' : $n . 'ème';
            };

            $calculerRangs = function(array $moyennes) use ($ordinalFr): array {
                $avecNote = array_filter($moyennes, fn($m) => $m !== null);
                arsort($avecNote);
                $rangs = [];
                $rangDebut = 1;
                $prevMoy = null;
                $nbDansGroupe = 0;
                foreach ($avecNote as $sid => $moy) {
                    if ($prevMoy !== null && $moy == $prevMoy) {
                        $nbDansGroupe++;
                    } else {
                        $rangDebut += $nbDansGroupe;
                        $nbDansGroupe = 1;
                        $prevMoy = $moy;
                    }
                    $rangs[$sid] = $ordinalFr($rangDebut);
                }
                foreach (array_keys(array_filter($moyennes, fn($m) => $m === null)) as $sid) {
                    $rangs[$sid] = '—';
                }
                return $rangs;
            };

            $moyennesGenerales = $studentsData->mapWithKeys(fn($s) => [$s['student']->id => $s['moyenne_generale']])->toArray();
            $rangsSemestre     = $calculerRangs($moyennesGenerales);

            $totalEleves = $studentsData->count();
            $admis = $studentsData->filter(fn($s) => $s['moyenne_generale'] !== null && $s['moyenne_generale'] >= 10)->count();
            $classAvg = $studentsData->filter(fn($s) => $s['moyenne_generale'] !== null)->avg('moyenne_generale');
            $classAvg = $classAvg ? $trunc2((float)$classAvg) : null;

            $rangsAnnuelsVue = [];
            $moyAnnuellesVue = [];

            if ($currentSemester == 2) {
                foreach ($studentsData as $sRow) {
                    $sid = $sRow['student']->id;
                    $rec = $sRow['student']->recordings
                        ->where('year_id', request('year_id'))
                        ->where('classroom_id', request('classroom_id'))
                        ->first();
                    $ms1 = null;
                    if ($rec) {
                        $nS1s = \App\Models\Note::with(['ratio'])
                            ->where('recording_id', $rec->id)
                            ->where('semester', 1)->get();
                        $tp=0; $tc=0;
                        foreach ($nS1s as $nS1) {
                            $int = is_array($nS1->interros) ? $nS1->interros : (json_decode($nS1->interros, true) ?? []);
                            $m = app(\App\Http\Controllers\NoteController::class)->calculateMoyenne20($int, $nS1->devoir1, $nS1->devoir2);
                            if ($m !== null) {
                                $c = $nS1->ratio->coefficient ?? 1;
                                $tp += $m * $c; $tc += $c;
                            }
                        }
                        $ms1 = $tc > 0 ? $trunc2($tp/$tc) : null;
                    }
                    $ms2 = $sRow['moyenne_generale'];
                    if ($ms1 !== null && $ms2 !== null) $moyAnnuellesVue[$sid] = $trunc2((($ms2 * 2) + $ms1) / 3);
                    elseif ($ms2 !== null) $moyAnnuellesVue[$sid] = $ms2;
                    elseif ($ms1 !== null) $moyAnnuellesVue[$sid] = $ms1;
                    else $moyAnnuellesVue[$sid] = null;
                }
                $rangsAnnuelsVue = $calculerRangs($moyAnnuellesVue);
            }
        @endphp

        {{-- Bandeau classe --}}
        <section class="gv-banner" aria-label="Résumé classe">
            <div class="gv-banner__left">
                <div class="gv-badgeIcon" aria-hidden="true"><i class="fas fa-building-columns"></i></div>
                <div>
                    <div class="gv-banner__title">
                        {{ $classroom->promotionSector->promotion_sector }} — {{ $classroom->name }}
                    </div>
                    <div class="gv-banner__meta">
                        Semestre {{ $currentSemester }}
                        · {{ $classroom->promotionSector->sectorYear->year->year }}
                        · {{ $classroom->promotionSector->sectorYear->sector->name_sector }}
                    </div>
                </div>
            </div>
            <div class="gv-stats" role="list">
                <div class="gv-stat" role="listitem">
                    <span class="gv-stat__k">Élèves</span>
                    <span class="gv-stat__v">{{ $totalEleves }}</span>
                </div>
                <div class="gv-stat" role="listitem">
                    <span class="gv-stat__k">Admis</span>
                    <span class="gv-stat__v is-ok">{{ $admis }}</span>
                </div>
                <div class="gv-stat" role="listitem">
                    <span class="gv-stat__k">Ajournés</span>
                    <span class="gv-stat__v is-bad">{{ $totalEleves - $admis }}</span>
                </div>
                <div class="gv-stat" role="listitem">
                    <span class="gv-stat__k">Moy. classe</span>
                    <span class="gv-stat__v {{ $classAvg !== null ? ($classAvg >= 10 ? 'is-ok' : 'is-bad') : '' }}">
                        {{ $classAvg !== null ? number_format($classAvg, 2) : '—' }}
                    </span>
                </div>
                <div class="gv-stat" role="listitem">
                    <span class="gv-stat__k">Matières</span>
                    <span class="gv-stat__v">{{ $subjects->count() }}</span>
                </div>
            </div>
        </section>

        {{-- Liste élèves --}}
        <section class="gv-list" id="studentsList" aria-label="Liste des élèves">
            @foreach($studentsData as $studentRow)
                @php
                    $student         = $studentRow['student'];
                    $notesParMatiere = $studentRow['notes_par_matiere'];
                    $moyGen          = $studentRow['moyenne_generale'];
                    $sid             = $student->id;
                    $rangSem         = $rangsSemestre[$sid] ?? '—';
                    $moyAnn          = $moyAnnuellesVue[$sid] ?? null;
                    $rangAnn         = $rangsAnnuelsVue[$sid] ?? '—';
                    $statusClass     = $moyGen === null ? 'is-na' : ($moyGen >= 10 ? 'is-pass' : 'is-fail');
                    $statusLabel     = $moyGen === null ? 'Non évalué' : ($moyGen >= 10 ? 'Admis' : 'Ajourné');
                    $fullName        = trim(($student->name ?? '') . ' ' . ($student->surname ?? ''));
                    $mat             = $student->matricule ?? 'N/A';
                @endphp

                <article class="gv-student {{ $statusClass }}" data-student="{{ mb_strtolower($fullName.' '.$mat) }}">
                    <details class="gv-details">
                        <summary class="gv-summary">
                            <div class="gv-avatar" aria-hidden="true">
                                {{ strtoupper(mb_substr($student->name ?? 'E', 0, 1)) }}
                            </div>
                            <div class="gv-id">
                                <div class="gv-name">{{ $fullName }}</div>
                                <div class="gv-meta">
                                    Mat. <span class="gv-mono">{{ $mat }}</span>
                                    @if($student->sex) · <span class="gv-pill gv-pill--soft">{{ $student->sex == 'M' ? 'M' : 'F' }}</span> @endif
                                    · <span class="gv-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                                </div>
                            </div>
                            <div class="gv-kpis" aria-label="Indicateurs élève">
                                <div class="gv-kpi">
                                    <span class="gv-kpi__k">Moy. S{{ $currentSemester }}</span>
                                    <span class="gv-kpi__v {{ $moyGen !== null ? ($moyGen >= 10 ? 'is-ok' : 'is-bad') : '' }}">
                                        {{ $moyGen !== null ? number_format($moyGen, 2) : '—' }}
                                    </span>
                                </div>
                                <div class="gv-kpi">
                                    <span class="gv-kpi__k">Rang</span>
                                    <span class="gv-kpi__v is-brand">
                                        {{ $rangSem !== '—' ? $rangSem . '/' . $totalEleves : '—' }}
                                    </span>
                                </div>
                                @if($currentSemester == 2)
                                    <div class="gv-kpi">
                                        <span class="gv-kpi__k">Moy. Ann.</span>
                                        <span class="gv-kpi__v {{ $moyAnn !== null ? ($moyAnn >= 10 ? 'is-ok' : 'is-bad') : '' }}">
                                            {{ $moyAnn !== null ? number_format($moyAnn, 2) : '—' }}
                                        </span>
                                    </div>
                                    <div class="gv-kpi">
                                        <span class="gv-kpi__k">Rang Ann.</span>
                                        <span class="gv-kpi__v is-brand">
                                            {{ $rangAnn !== '—' ? $rangAnn . '/' . $totalEleves : '—' }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="gv-caret" aria-hidden="true">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </summary>

                        <div class="gv-body">
                            <div class="gv-tableWrap" role="region" aria-label="Détail des matières" tabindex="0">
                                <table class="gv-table">
                                    <thead>
                                        <tr>
                                            <th>Matière</th>
                                            <th class="t-center">Interros</th>
                                            <th class="t-center">Moy. I</th>
                                            <th class="t-center">D1</th>
                                            <th class="t-center">D2</th>
                                            <th class="t-center th-strong">Moy /20</th>
                                            <th class="t-center">Coeff</th>
                                            <th class="t-center th-strong">Moy×C</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subjects as $subjectId => $subjectInfo)
                                            @php
                                                $matiere     = $notesParMatiere[$subjectId] ?? null;
                                                $note        = $matiere['note'] ?? null;
                                                $moyInterros = $matiere['moy_interros'] ?? null;
                                                $moy20       = $matiere['moy_20'] ?? null;
                                                $coef        = $subjectInfo['coefficient'];
                                                $moyCoeff    = $moy20 !== null ? $trunc2($moy20 * $coef) : null;
                                                $interros    = $note ? (is_array($note->interros) ? $note->interros : (json_decode($note->interros, true) ?? [])) : [];
                                            @endphp
                                            <tr>
                                                <td data-label="Matière" class="t-name">{{ $subjectInfo['name'] }}</td>
                                                <td data-label="Interros" class="t-center">
                                                    @if(!empty($interros))
                                                        <div class="gv-chips">
                                                            @foreach($interros as $iv)
                                                                <span class="gv-chip {{ $iv >= 10 ? 'is-ok' : 'is-bad' }}">{{ number_format($iv, 2) }}</span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="gv-dash">—</span>
                                                    @endif
                                                </td>
                                                <td data-label="Moy. I" class="t-center">
                                                    @if($moyInterros !== null)
                                                        <span class="gv-mark {{ $moyInterros >= 10 ? 'is-ok' : 'is-bad' }}">{{ number_format($moyInterros, 2) }}</span>
                                                    @else <span class="gv-dash">—</span> @endif
                                                </td>
                                                <td data-label="D1" class="t-center">
                                                    @if($note?->devoir1 !== null)
                                                        <span class="gv-mark {{ $note->devoir1 >= 10 ? 'is-ok' : 'is-bad' }}">{{ number_format($note->devoir1, 2) }}</span>
                                                    @else <span class="gv-dash">—</span> @endif
                                                </td>
                                                <td data-label="D2" class="t-center">
                                                    @if($note?->devoir2 !== null)
                                                        <span class="gv-mark {{ $note->devoir2 >= 10 ? 'is-ok' : 'is-bad' }}">{{ number_format($note->devoir2, 2) }}</span>
                                                    @else <span class="gv-dash">—</span> @endif
                                                </td>
                                                <td data-label="Moy /20" class="t-center">
                                                    @if($moy20 !== null)
                                                        <span class="gv-final {{ $moy20 >= 10 ? 'is-ok' : 'is-bad' }}">{{ number_format($moy20, 2) }}</span>
                                                    @else <span class="gv-dash">—</span> @endif
                                                </td>
                                                <td data-label="Coeff" class="t-center">
                                                    <span class="gv-coef">{{ $coef }}</span>
                                                </td>
                                                <td data-label="Moy×C" class="t-center">
                                                    @if($moyCoeff !== null)
                                                        <span class="gv-mark {{ $moyCoeff >= 10 * $coef ? 'is-ok' : 'is-warn' }}">{{ number_format($moyCoeff, 2) }}</span>
                                                    @else <span class="gv-dash">—</span> @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </details>
                </article>
            @endforeach
        </section>

    @elseif(request()->has(['year_id','classroom_id','semester']))
        <section class="gv-empty" aria-label="Aucune note">
            <div class="gv-empty__icon"><i class="fas fa-book-open"></i></div>
            <h3>Aucune note disponible</h3>
            <p>Aucune note n'a été enregistrée pour cette classe et ce semestre.</p>
        </section>
    @else
        <section class="gv-empty" id="gv-empty-default" aria-label="Démarrer">
            <div class="gv-empty__icon"><i class="fas fa-magnifying-glass"></i></div>
            <h3>Commencez votre recherche</h3>
            <p>Choisissez une <strong>filière</strong>, une <strong>promotion</strong> et une <strong>classe</strong>, puis cliquez sur <strong>Afficher les notes</strong>.</p>
        </section>
    @endif

    </div>{{-- /#gv-results-zone --}}

</main>
@endsection

@section('another_JS')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const BASE_URL = '{{ rtrim(url('/'), '/') }}';
    const CSRF     = '{{ csrf_token() }}';

    // ── État de la cascade ──────────────────────────────────
    const state = {
        yearId:      null,
        sectorId:    null,
        promotionId: null,
        classroomId: null,
    };

    // ── Utilitaires ─────────────────────────────────────────
    const $  = id  => document.getElementById(id);
    const $$ = sel => document.querySelectorAll(sel);

    async function apiFetch(path) {
        const url = path.startsWith('http') ? path : BASE_URL + path;
        const r   = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        if (!r.ok) throw new Error(`Erreur ${r.status}`);
        return r.json();
    }

    async function apiPost(path, body) {
        const url = path.startsWith('http') ? path : BASE_URL + path;
        const r   = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(body)
        });
        return r.json();
    }

    // ── Semestre toggle ──────────────────────────────────────
    $$('.gv-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            $$('.gv-tab').forEach(b => { b.classList.remove('is-active'); b.setAttribute('aria-pressed', 'false'); });
            btn.classList.add('is-active');
            btn.setAttribute('aria-pressed', 'true');
            $('gv-semester').value = btn.dataset.value;
        });
    });

    // ── Bouton "Afficher les notes" ──────────────────────────
    $('gv-btn-search').addEventListener('click', () => loadResults());

    // ── Étape 0 : Année active ───────────────────────────────
    async function loadActiveYear() {
        try {
            const d = await apiFetch('/api/active-year');
            if (d.success && d.year) {
                state.yearId = d.year.id;
                $('gv-year-id').value = d.year.id;
                setBc('year', d.year.year);
                await loadSectors();
            } else {
                renderOpts('sector', [], 'Aucune année active dans le système.');
            }
        } catch(e) {
            renderOpts('sector', [], 'Erreur de chargement : ' + e.message);
        }
    }

    // ── Étape 1 : Filières ────────────────────────────────────
    async function loadSectors() {
        setLoading('sector', true);
        $('gv-step-sector').classList.remove('gv-step--locked');
        try {
            const d = await apiFetch(`/api/sectors-for-create/${state.yearId}`);
            setLoading('sector', false);
            renderOpts('sector', d.sectors || [], 'Aucune filière disponible.', onSector);
            if (d.auto && d.sectors?.length) await onSector(d.sectors[0], true);
        } catch(e) { setLoading('sector', false); }
    }

    async function onSector(s, auto = false) {
        markActive('sector', s.id);
        state.sectorId = s.id;
        setBc('sector', s.name);
        resetFrom('promo');
        await loadPromotions();
    }

    // ── Étape 2 : Promotions ──────────────────────────────────
    async function loadPromotions() {
        setLoading('promo', true);
        $('gv-step-promo').classList.remove('gv-step--locked');
        try {
            const d = await apiFetch(`/api/promotions-for-create/${state.yearId}/${state.sectorId}`);
            setLoading('promo', false);
            renderOpts('promo', d.promotions || [], 'Aucune promotion disponible.', onPromotion);
            if (d.auto && d.promotions?.length) await onPromotion(d.promotions[0], true);
        } catch(e) { setLoading('promo', false); }
    }

    async function onPromotion(p, auto = false) {
        markActive('promo', p.id);
        state.promotionId = p.id;
        setBc('promo', p.name);
        resetFrom('class');
        await loadClassrooms();
    }

    // ── Étape 3 : Classes ─────────────────────────────────────
    async function loadClassrooms() {
        setLoading('class', true);
        $('gv-step-class').classList.remove('gv-step--locked');
        try {
            const d = await apiFetch(`/api/classrooms-for-create/${state.promotionId}?year_id=${state.yearId}`);
            setLoading('class', false);
            renderOpts('class', d.classrooms || [], 'Aucune classe disponible.', onClassroom);
            if (d.auto && d.classrooms?.length) await onClassroom(d.classrooms[0], true);
        } catch(e) { setLoading('class', false); }
    }

    function onClassroom(c, auto = false) {
        markActive('class', c.id);
        state.classroomId = c.id;
        $('gv-classroom-id').value = c.id;
        setBc('class', c.name);
        $('gv-btn-search').disabled = false;
    }

    // ── Chargement des résultats (navigation vers l'URL filtrée) ──
    function loadResults() {
        const classroomId = state.classroomId;
        const yearId      = state.yearId;
        const semester    = $('gv-semester').value;

        if (!classroomId || !yearId || !semester) return;

        const url = new URL(window.location.href);
        url.searchParams.set('year_id',      yearId);
        url.searchParams.set('classroom_id', classroomId);
        url.searchParams.set('semester',     semester);
        window.location.href = url.toString();
    }

    // ── Helpers UI ───────────────────────────────────────────
    function renderOpts(step, items, emptyMsg, onClick) {
        const container = $(`gv-opts-${step}`);
        if (!container) return;
        if (!items.length) {
            container.innerHTML = `<div class="gv-ph gv-ph--warn">${emptyMsg}</div>`;
            return;
        }
        container.innerHTML = items.map(item => `
            <button type="button" class="gv-opt" data-id="${item.id}" data-step="${step}">
                <span class="gv-opt-label">${escHtml(item.name)}</span>
                <span class="gv-opt-arrow">›</span>
            </button>
        `).join('');
        container.querySelectorAll('.gv-opt').forEach(btn => {
            btn.addEventListener('click', () => {
                const item = items.find(i => String(i.id) === btn.dataset.id);
                if (item) onClick(item);
            });
        });
    }

    function markActive(step, id) {
        const container = $(`gv-opts-${step}`);
        if (!container) return;
        container.querySelectorAll('.gv-opt').forEach(btn => {
            btn.classList.toggle('gv-opt--active', String(btn.dataset.id) === String(id));
        });
    }

    function setLoading(step, on) {
        const el = $(`gv-loader-${step}`);
        if (el) el.style.display = on ? 'flex' : 'none';
    }

    function setBc(part, label) {
        const lbl  = $(`gv-bc-${part}-lbl`); if (lbl) lbl.textContent = label;
        const item = $(`gv-bc-${part}`);      if (item) item.classList.add('active');
    }

    function resetFrom(step) {
        const steps = ['sector', 'promo', 'class'];
        const idx   = steps.indexOf(step);
        for (let i = idx; i < steps.length; i++) {
            const s    = steps[i];
            const stepEl = $(`gv-step-${s}`);
            const optsEl = $(`gv-opts-${s}`);
            if (stepEl) stepEl.classList.add('gv-step--locked');
            if (optsEl) optsEl.innerHTML = `<div class="gv-ph">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                Choisissez d'abord ${s === 'promo' ? 'une filière' : 'une promotion'}
            </div>`;
            const bcLbl = $(`gv-bc-${s}-lbl`);
            if (bcLbl) bcLbl.textContent = s === 'promo' ? 'Promotion' : 'Classe';
            const bcItem = $(`gv-bc-${s}`);
            if (bcItem) bcItem.classList.remove('active');
            if (s === 'promo')  state.promotionId = null;
            if (s === 'class')  { state.classroomId = null; $('gv-classroom-id').value = ''; $('gv-btn-search').disabled = true; }
        }
    }

    function escHtml(str) {
        const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML;
    }

    // ── Pré-remplir la cascade si résultats déjà chargés (retour page) ──
    // Si l'URL contient classroom_id, on recharge l'année active + cascade
    // et on marque visuellement la sélection actuelle
    const urlParams   = new URLSearchParams(window.location.search);
    const urlClassId  = urlParams.get('classroom_id');
    const urlYearId   = urlParams.get('year_id');
    const urlSemester = urlParams.get('semester');

    if (urlSemester) {
        $$('.gv-tab').forEach(b => {
            const isActive = b.dataset.value === urlSemester;
            b.classList.toggle('is-active', isActive);
            b.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
        $('gv-semester').value = urlSemester;
    }

    // Afficher la barre de recherche si résultats présents
    const hasList = document.querySelector('#studentsList');
    if (hasList) {
        $('gv-searchbar').style.display = 'flex';
        initSearch();
    }

    // ── Recherche instantanée ────────────────────────────────
    function initSearch() {
        const qInput  = $('studentSearch');
        const list    = document.getElementById('studentsList');
        const countBox = $('gvSearchCount');
        if (!qInput || !list) return;

        const cards = Array.from(list.querySelectorAll('.gv-student'));
        const total = cards.length;
        refreshCount(total, total);

        qInput.addEventListener('input', () => {
            const q = (qInput.value || '').trim().toLowerCase();
            let visible = 0;
            cards.forEach(card => {
                const hay = card.getAttribute('data-student') || '';
                const ok  = !q || hay.includes(q);
                card.style.display = ok ? '' : 'none';
                if (ok) visible++;
            });
            refreshCount(visible, total);
        });

        function refreshCount(visible, total) {
            if (!countBox) return;
            countBox.textContent = qInput.value
                ? `${visible}/${total} affiché(s)`
                : `${total} élève(s)`;
        }
    }

    // ── Accordéons ──────────────────────────────────────────
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function getAllDetails() {
        const list = document.getElementById('studentsList');
        return Array.from((list || document).querySelectorAll('details.gv-details'));
    }

    function syncToggleAllLabel() {
        const btn = $('btnToggleAll');
        if (!btn) return;
        const anyClosed = getAllDetails().some(d => !d.open);
        btn.innerHTML = anyClosed
            ? '<i class="fas fa-layer-group"></i> Tout ouvrir'
            : '<i class="fas fa-layer-group"></i> Tout fermer';
    }

    function animateDetails(detailsEl, open) {
        if (prefersReducedMotion) { detailsEl.open = open; return Promise.resolve(); }
        if (detailsEl.dataset.animating === '1') return Promise.resolve();
        detailsEl.dataset.animating = '1';
        const summary     = detailsEl.querySelector('summary');
        const body        = detailsEl.querySelector('.gv-body');
        const closedHeight = summary.offsetHeight;
        const startHeight  = detailsEl.offsetHeight;
        if (open) detailsEl.open = true;
        void detailsEl.offsetHeight;
        const endHeight = open ? detailsEl.offsetHeight : closedHeight;
        detailsEl.style.height = `${startHeight}px`;
        if (body) {
            body.animate(
                open
                    ? [{ opacity: 0, transform: 'translateY(-6px)' }, { opacity: 1, transform: 'translateY(0)' }]
                    : [{ opacity: 1, transform: 'translateY(0)' }, { opacity: 0, transform: 'translateY(-4px)' }],
                { duration: open ? 260 : 200, easing: 'cubic-bezier(.2,.8,.2,1)', fill: 'both' }
            );
        }
        const anim = detailsEl.animate(
            [{ height: `${startHeight}px` }, { height: `${endHeight}px` }],
            { duration: open ? 360 : 320, easing: 'cubic-bezier(.22,1,.36,1)' }
        );
        return new Promise(resolve => {
            anim.onfinish = () => {
                if (!open) detailsEl.open = false;
                detailsEl.style.height = '';
                delete detailsEl.dataset.animating;
                resolve();
            };
            anim.oncancel = () => { delete detailsEl.dataset.animating; resolve(); };
        });
    }

    getAllDetails().forEach(detailsEl => {
        const summary = detailsEl.querySelector('summary');
        if (!summary) return;
        summary.addEventListener('click', async e => {
            e.preventDefault();
            await animateDetails(detailsEl, !detailsEl.open);
            syncToggleAllLabel();
        });
        detailsEl.addEventListener('toggle', syncToggleAllLabel);
    });

    getAllDetails().forEach(d => d.open = false);
    syncToggleAllLabel();

    const btnAll = $('btnToggleAll');
    btnAll?.addEventListener('click', async () => {
        const all      = getAllDetails();
        const anyClosed = all.some(d => !d.open);
        for (const d of all) await animateDetails(d, anyClosed);
        syncToggleAllLabel();
    });

    window.addEventListener('beforeprint', () => getAllDetails().forEach(d => d.open = true));
    window.addEventListener('afterprint',  () => { getAllDetails().forEach(d => d.open = false); syncToggleAllLabel(); });

    // ── Démarrage ────────────────────────────────────────────
    loadActiveYear();
});
</script>

<style>
:root{
  --bg:#f3f5f9; --card:#ffffff; --muted:#6b7280; --text:#0f172a; --border:#e6eaf0;
  --brand:#3b5bdb; --brand2:#6d28d9;
  --ok:#16a34a; --bad:#dc2626; --warn:#f59e0b;
  --okBg:#ecfdf3; --badBg:#fff1f2; --warnBg:#fffbeb;
  --radius:16px; --radius2:20px;
  --shadow:0 10px 30px rgba(15,23,42,.06);
  --shadow2:0 14px 45px rgba(15,23,42,.10);
  --mono:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;
  --sans:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
  --tr:.18s cubic-bezier(.4,0,.2,1);
}
*{ box-sizing:border-box; }
body{ background:var(--bg); }
.gv{ font-family:var(--sans); color:var(--text); padding:18px; display:flex; flex-direction:column; gap:14px; }

/* Topbar */
.gv-topbar{ background:linear-gradient(135deg,rgba(59,91,219,.12),rgba(109,40,217,.08)),var(--card); border:1px solid rgba(230,234,240,.9); border-radius:var(--radius2); box-shadow:var(--shadow); padding:14px 16px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }
.gv-topbar__left{ display:flex; align-items:center; gap:12px; }
.gv-appmark{ width:46px; height:46px; border-radius:14px; display:grid; place-items:center; background:linear-gradient(135deg,var(--brand),var(--brand2)); color:white; box-shadow:0 10px 25px rgba(59,91,219,.25); }
.gv-title{ margin:0; font-size:1.2rem; font-weight:800; letter-spacing:.2px; }
.gv-subtitle{ margin:2px 0 0; font-size:.9rem; color:var(--muted); }
.gv-topbar__right{ display:flex; gap:10px; flex-wrap:wrap; }

/* Buttons */
.gv-btn{ border:1px solid var(--border); background:var(--card); color:var(--text); border-radius:12px; height:42px; padding:0 14px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; font-weight:700; transition:transform var(--tr),box-shadow var(--tr),background var(--tr),border-color var(--tr); }
.gv-btn:hover{ transform:translateY(-1px); box-shadow:var(--shadow); border-color:rgba(59,91,219,.25); }
.gv-btn--primary{ border:none; color:white; background:linear-gradient(135deg,var(--brand),var(--brand2)); }
.gv-btn--primary:hover{ box-shadow:0 12px 35px rgba(59,91,219,.28); }
.gv-btn--primary:disabled{ opacity:.4; cursor:not-allowed; transform:none; box-shadow:none; }
.gv-btn--soft{ background:rgba(15,23,42,.04); }
.gv-btn--full{ width:100%; justify-content:center; height:42px; }

/* Panel */
.gv-panel{ background:var(--card); border:1px solid var(--border); border-radius:var(--radius2); box-shadow:var(--shadow); padding:16px; }

/* Breadcrumb */
.gv-breadcrumb{ display:flex; align-items:center; gap:6px; padding:9px 12px; background:#f8fafc; border:1px solid var(--border); border-radius:10px; margin-bottom:14px; flex-wrap:wrap; }
.gv-bc-item{ display:flex; align-items:center; gap:5px; padding:3px 8px; border-radius:6px; color:#94a3b8; font-size:.74rem; font-weight:600; transition:all .2s; }
.gv-bc-year{ color:var(--brand); font-weight:800; }
.gv-bc-item.active{ color:#334155; background:rgba(59,91,219,.07); }
.gv-bc-sep{ color:#e2e8f0; }

/* Étapes */
.gv-step{ border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:12px; transition:opacity .2s,filter .2s; }
.gv-step--locked{ opacity:.5; pointer-events:none; filter:grayscale(.3); }
.gv-step-head{ display:flex; align-items:center; gap:12px; padding:10px 14px; background:#f8fafc; border-bottom:1px solid var(--border); }
.gv-step-num{ width:24px; height:24px; border-radius:7px; background:rgba(59,91,219,.1); border:1px solid rgba(59,91,219,.2); color:var(--brand); font-weight:800; font-size:.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.gv-step-info{ flex:1; }
.gv-step-title{ font-weight:700; font-size:.82rem; color:var(--text); }
.gv-step-hint{ font-size:.71rem; color:var(--muted); margin-top:1px; }
.gv-step-loader{ display:flex; align-items:center; }
.gv-spin-sm{ width:13px; height:13px; border:2px solid var(--border); border-top-color:var(--brand); border-radius:50%; animation:gvSpin .7s linear infinite; }
@keyframes gvSpin{ to{ transform:rotate(360deg); } }
.gv-step-opts{ padding:10px 14px; display:flex; flex-wrap:wrap; gap:8px; min-height:46px; align-items:center; }
.gv-ph{ display:flex; align-items:center; gap:7px; font-size:.78rem; color:#94a3b8; font-style:italic; }
.gv-ph--warn{ color:var(--warn); }

/* Option cards */
.gv-opt{ display:inline-flex; align-items:center; gap:7px; padding:7px 13px; background:var(--card); border:1.5px solid var(--border); border-radius:10px; cursor:pointer; font-family:var(--sans); font-size:.82rem; font-weight:600; color:#374151; transition:all .18s; white-space:nowrap; }
.gv-opt:hover{ border-color:rgba(59,91,219,.35); background:rgba(59,91,219,.05); color:var(--brand); transform:translateY(-1px); box-shadow:var(--shadow); }
.gv-opt--active{ border-color:var(--brand); background:rgba(59,91,219,.07); color:var(--brand); box-shadow:0 0 0 3px rgba(59,91,219,.1); }
.gv-opt-arrow{ opacity:.4; }
.gv-opt--active .gv-opt-arrow{ opacity:1; }

/* Séparateur */
.gv-sep-line{ display:flex; align-items:center; gap:12px; margin:14px 0; }
.gv-sep-line::before,.gv-sep-line::after{ content:''; flex:1; height:1px; background:var(--border); }
.gv-sep-line span{ font-size:.68rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#94a3b8; white-space:nowrap; }

/* Config row */
.gv-config-row{ display:grid; grid-template-columns:auto 220px; gap:14px; align-items:end; }
.gv-field{ display:flex; flex-direction:column; gap:6px; }
.gv-label{ font-size:.78rem; color:var(--muted); font-weight:800; text-transform:uppercase; letter-spacing:.35px; display:flex; align-items:center; gap:7px; }
.gv-label--ghost{ opacity:0; pointer-events:none; }

/* Semestre tabs */
.gv-semtabs{ height:42px; display:flex; border:1px solid var(--border); border-radius:12px; overflow:hidden; background:#f8fafc; }
.gv-tab{ flex:1; border:none; background:transparent; cursor:pointer; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:2px; font-weight:900; transition:background var(--tr),color var(--tr); }
.gv-tab small{ font-weight:700; color:#8b95a6; font-size:.68rem; }
.gv-tab.is-active{ background:linear-gradient(135deg,var(--brand),var(--brand2)); color:#fff; }
.gv-tab.is-active small{ color:rgba(255,255,255,.75); }

/* Searchbar */
.gv-searchbar{ margin-top:12px; display:flex; gap:12px; align-items:center; justify-content:space-between; flex-wrap:wrap; }
.gv-search{ flex:1; min-width:260px; display:flex; align-items:center; gap:10px; border:1px solid var(--border); background:#fff; border-radius:14px; padding:0 12px; height:44px; }
.gv-search i{ color:#64748b; }
.gv-input{ border:none; box-shadow:none; height:42px; padding:0; background:transparent; outline:none; width:100%; font-size:.9rem; }
.gv-minihelp{ color:var(--muted); font-weight:800; }

/* Banner */
.gv-banner{ background:var(--card); border:1px solid var(--border); border-radius:var(--radius2); box-shadow:var(--shadow); padding:14px 16px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; position:relative; overflow:hidden; }
.gv-banner::before{ content:""; position:absolute; inset:-60px auto auto -60px; width:160px; height:160px; border-radius:50%; background:radial-gradient(circle,rgba(59,91,219,.18),transparent 60%); }
.gv-banner__left{ display:flex; align-items:center; gap:12px; z-index:1; }
.gv-badgeIcon{ width:44px; height:44px; border-radius:14px; display:grid; place-items:center; background:rgba(59,91,219,.10); color:rgba(59,91,219,.95); }
.gv-banner__title{ font-weight:900; }
.gv-banner__meta{ color:var(--muted); font-weight:700; font-size:.9rem; margin-top:2px; }
.gv-stats{ display:flex; gap:10px; flex-wrap:wrap; z-index:1; }
.gv-stat{ border:1px solid var(--border); background:#fff; border-radius:14px; padding:10px 12px; min-width:140px; }
.gv-stat__k{ display:block; color:var(--muted); font-weight:900; font-size:.78rem; text-transform:uppercase; letter-spacing:.35px; }
.gv-stat__v{ display:block; margin-top:4px; font-weight:900; font-family:var(--mono); font-size:1.05rem; }
.gv-stat__v.is-ok{ color:var(--ok); }
.gv-stat__v.is-bad{ color:var(--bad); }

/* Student list */
.gv-list{ display:flex; flex-direction:column; gap:12px; }
.gv-student{ background:var(--card); border:1px solid var(--border); border-radius:var(--radius2); box-shadow:var(--shadow); overflow:hidden; transition:box-shadow var(--tr),transform var(--tr); }
.gv-student:hover{ box-shadow:var(--shadow2); transform:translateY(-1px); }
.gv-student.is-pass{ border-left:5px solid rgba(22,163,74,.95); }
.gv-student.is-fail{ border-left:5px solid rgba(220,38,38,.95); }
.gv-student.is-na{   border-left:5px solid rgba(148,163,184,.9); }
.gv-details > summary{ list-style:none; }
.gv-details > summary::-webkit-details-marker{ display:none; }
.gv-summary{ display:flex; align-items:center; gap:12px; padding:12px 14px; background:linear-gradient(180deg,rgba(248,250,252,.9),rgba(255,255,255,.95)); border-bottom:1px solid var(--border); cursor:pointer; }
.gv-avatar{ width:44px; height:44px; border-radius:14px; display:grid; place-items:center; background:linear-gradient(135deg,rgba(59,91,219,.95),rgba(109,40,217,.95)); color:#fff; font-weight:900; }
.gv-id{ flex:1; min-width:180px; }
.gv-name{ font-weight:1000; letter-spacing:.2px; }
.gv-meta{ margin-top:2px; color:var(--muted); font-weight:800; font-size:.88rem; display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.gv-mono{ font-family:var(--mono); }
.gv-pill{ border-radius:999px; padding:3px 10px; border:1px solid var(--border); background:rgba(15,23,42,.03); font-size:.78rem; font-weight:1000; }
.gv-pill.is-pass{ background:var(--okBg); border-color:rgba(22,163,74,.18); color:var(--ok); }
.gv-pill.is-fail{ background:var(--badBg); border-color:rgba(220,38,38,.18); color:var(--bad); }
.gv-pill.is-na{   background:#f1f5f9; border-color:rgba(148,163,184,.35); color:#475569; }
.gv-pill--soft{ background:rgba(15,23,42,.03); }
.gv-kpis{ display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
.gv-kpi{ border:1px solid var(--border); background:#fff; border-radius:14px; padding:8px 10px; min-width:120px; }
.gv-kpi__k{ display:block; color:var(--muted); font-weight:900; font-size:.72rem; text-transform:uppercase; letter-spacing:.35px; }
.gv-kpi__v{ display:block; margin-top:3px; font-family:var(--mono); font-weight:1000; }
.gv-kpi__v.is-ok{ color:var(--ok); }
.gv-kpi__v.is-bad{ color:var(--bad); }
.gv-kpi__v.is-brand{ color:rgba(59,91,219,.95); }
.gv-caret{ width:36px; height:36px; border-radius:12px; display:grid; place-items:center; background:rgba(15,23,42,.04); border:1px solid var(--border); color:#334155; transition:transform var(--tr); }
details[open] .gv-caret{ transform:rotate(180deg); }
.gv-body{ padding:12px 14px 16px; background:#fff; }

/* Table */
.gv-tableWrap{ overflow:auto; border-radius:16px; border:1px solid var(--border); }
.gv-table{ width:100%; border-collapse:separate; border-spacing:0; min-width:920px; background:#fff; }
.gv-table thead th{ position:sticky; top:0; z-index:2; background:#f8fafc; border-bottom:1px solid var(--border); padding:10px; text-align:left; font-size:.78rem; text-transform:uppercase; letter-spacing:.35px; color:#475569; }
.gv-table tbody td{ border-bottom:1px solid var(--border); padding:10px; vertical-align:middle; font-weight:800; }
.gv-table tbody tr:nth-child(2n) td{ background:rgba(15,23,42,.015); }
.gv-table tbody tr:hover td{ background:rgba(59,91,219,.04); }
.t-center{ text-align:center; }
.t-name{ font-weight:1000; }
.th-strong{ color:rgba(59,91,219,.95); }
.gv-chips{ display:flex; gap:6px; justify-content:center; flex-wrap:wrap; }
.gv-chip{ font-family:var(--mono); font-weight:1000; font-size:.72rem; padding:3px 8px; border-radius:999px; border:1px solid var(--border); background:#fff; }
.gv-chip.is-ok{ background:var(--okBg); border-color:rgba(22,163,74,.18); color:var(--ok); }
.gv-chip.is-bad{ background:var(--badBg); border-color:rgba(220,38,38,.18); color:var(--bad); }
.gv-mark{ display:inline-flex; align-items:center; justify-content:center; min-width:58px; padding:4px 10px; border-radius:12px; font-family:var(--mono); font-weight:1000; border:1px solid var(--border); }
.gv-mark.is-ok{ background:var(--okBg); border-color:rgba(22,163,74,.18); color:var(--ok); }
.gv-mark.is-bad{ background:var(--badBg); border-color:rgba(220,38,38,.18); color:var(--bad); }
.gv-mark.is-warn{ background:var(--warnBg); border-color:rgba(245,158,11,.22); color:#b45309; }
.gv-final{ display:inline-flex; align-items:center; justify-content:center; min-width:66px; padding:5px 12px; border-radius:12px; font-family:var(--mono); font-weight:1100; border:1px solid var(--border); }
.gv-final.is-ok{ background:var(--okBg); border-color:rgba(22,163,74,.18); color:var(--ok); }
.gv-final.is-bad{ background:var(--badBg); border-color:rgba(220,38,38,.18); color:var(--bad); }
.gv-coef{ display:inline-flex; align-items:center; justify-content:center; min-width:44px; padding:4px 10px; border-radius:999px; border:1px solid var(--border); background:rgba(15,23,42,.03); font-family:var(--mono); }
.gv-dash{ color:#94a3b8; font-family:var(--mono); }

/* Empty */
.gv-empty{ background:var(--card); border:1px dashed rgba(148,163,184,.7); border-radius:var(--radius2); padding:52px 18px; text-align:center; color:var(--muted); box-shadow:var(--shadow); }
.gv-empty__icon{ width:68px; height:68px; border-radius:20px; margin:0 auto 10px; display:grid; place-items:center; background:rgba(59,91,219,.08); border:1px solid rgba(59,91,219,.14); color:rgba(59,91,219,.95); font-size:1.55rem; }
.gv-empty h3{ margin:0 0 6px; color:#334155; font-weight:1000; }

/* Accordion animation */
details.gv-details{ overflow:hidden; will-change:height; }
@media(prefers-reduced-motion:reduce){ details.gv-details{ will-change:auto; } }

/* Responsive */
@media(max-width:1100px){
  .gv-config-row{ grid-template-columns:1fr; }
  .gv-kpis{ justify-content:flex-start; }
}
@media(max-width:720px){
  .gv{ padding:12px; }
  .gv-summary{ align-items:flex-start; flex-wrap:wrap; }
  .gv-kpi{ min-width:46%; }
  .gv-table{ min-width:unset; }
  .gv-table thead{ display:none; }
  .gv-table,.gv-table tbody,.gv-table tr,.gv-table td{ display:block; width:100%; }
  .gv-table tr{ border-bottom:1px solid var(--border); padding:10px; }
  .gv-table td{ border:none; display:flex; justify-content:space-between; gap:12px; padding:8px 0; }
  .gv-table td::before{ content:attr(data-label); font-weight:1000; color:#64748b; text-transform:uppercase; letter-spacing:.3px; font-size:.72rem; }
  .t-center{ text-align:right; }
}

/* Print */
@media print{
  .gv-panel,.gv-topbar__right,.gv-searchbar{ display:none !important; }
  .gv-student,.gv-banner{ box-shadow:none !important; }
  .gv-tableWrap{ overflow:visible !important; }
  .gv-table thead th{ position:static !important; }
}
</style>
@endsection
