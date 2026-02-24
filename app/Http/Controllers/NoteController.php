<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Recording;
use App\Models\Ratio;
use App\Models\SectorYear;
use App\Models\PromotionSector;
use App\Models\PromotionClassroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Year;
use App\Exports\NotesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class NoteController extends Controller
{
    // =========================================================
    // HELPER — Troncage à 2 décimales (sans arrondi)
    // Ex : 1.6666… → 1.66  (et non 1.67 avec round)
    // =========================================================

    private function trunc2(?float $val): ?float
    {
        if ($val === null) return null;
        return floor($val * 100) / 100;
    }

    // =========================================================
    // VUES PRINCIPALES
    // =========================================================

    /**
     * Page de saisie des notes
     * GET /note/create  →  route('note.create')
     */
    public function create()
    {
        $years = Year::orderBy('year', 'desc')->get();
        return view('dashboard.notes.create', compact('years'));
    }

    /**
     * Page de visualisation des notes
     * GET /note  →  route('note.index')
     */
    public function index(Request $request)
    {
        $years        = Year::orderBy('year', 'desc')->get();
        $notes        = collect();
        $subjects     = collect();
        $classroom    = null;
        $studentsData = collect();

        // Toujours initialisées pour éviter "Undefined variable" dans la vue
        $sectorsForFilter    = collect();
        $promotionsForFilter = collect();
        $classroomsForFilter = collect();

        if ($request->filled('year_id')) {
            $sectorsForFilter = SectorYear::with('sector')
                ->where('year_id', $request->year_id)
                ->get()
                ->pluck('sector')
                ->filter();
        }

        if ($request->filled(['year_id', 'sector_id'])) {
            $sectorYear = SectorYear::where('year_id', $request->year_id)
                ->where('sector_id', $request->sector_id)
                ->first();
            $promotionsForFilter = $sectorYear
                ? PromotionSector::where('sector_year_id', $sectorYear->id)->get()
                : collect();
        }

        if ($request->filled('promotion_id')) {
            $classroomsForFilter = PromotionClassroom::where('promotion_sector_id', $request->promotion_id)->get();
        }

        if ($request->filled(['year_id', 'sector_id', 'promotion_id', 'classroom_id', 'semester'])) {

            $classroom = PromotionClassroom::with([
                'promotionSector.sectorYear.sector',
                'promotionSector.sectorYear.year',
            ])->find($request->classroom_id);

            if ($classroom) {
                $rawNotes = Note::with(['recording.student', 'subject', 'ratio'])
                    ->whereHas('recording', function ($q) use ($request) {
                        $q->where('classroom_id', $request->classroom_id)
                          ->where('year_id', $request->year_id);
                    })
                    ->where('semester', $request->semester)
                    ->get();

                foreach ($rawNotes as $note) {
                    if (!$subjects->has($note->subject_id)) {
                        $subjects->put($note->subject_id, [
                            'name'        => $note->subject->name,
                            'coefficient' => $note->ratio->coefficient,
                        ]);
                    }
                }
                $subjects = $subjects->sortBy('name');

                $notes = $rawNotes->groupBy('recording_id');

                foreach ($notes as $recordingId => $studentNotes) {
                    $totalMoyPonderee = 0;
                    $totalCoef        = 0;
                    $notesParMatiere  = [];

                    foreach ($subjects as $subjectId => $subjectInfo) {
                        $note        = $studentNotes->where('subject_id', $subjectId)->first();
                        $moyInterros = null;
                        $moy20       = null;

                        if ($note) {
                            $interros = is_array($note->interros)
                                ? $note->interros
                                : (json_decode($note->interros, true) ?? []);

                            $moyInterros = count($interros) > 0
                                ? $this->trunc2(array_sum($interros) / count($interros))
                                : null;

                            $moy20 = $this->calculateMoyenne20($interros, $note->devoir1, $note->devoir2);

                            if ($moy20 !== null) {
                                $totalMoyPonderee += $moy20 * $subjectInfo['coefficient'];
                                $totalCoef        += $subjectInfo['coefficient'];
                            }
                        }

                        $notesParMatiere[$subjectId] = [
                            'note'         => $note,
                            'moy_interros' => $moyInterros,
                            'moy_20'       => $moy20,
                        ];
                    }

                    $moyenneGenerale = $totalCoef > 0
                        ? $this->trunc2($totalMoyPonderee / $totalCoef)
                        : null;

                    $recording = $studentNotes->first()->recording;

                    $studentsData->push([
                        'student'           => $recording->student,
                        'notes_par_matiere' => $notesParMatiere,
                        'moyenne_generale'  => $moyenneGenerale,
                    ]);
                }

                $studentsData = $studentsData->sortBy(fn($s) => $s['student']->name)->values();
            }
        }

        return view('dashboard.notes.list', compact(
            'years', 'notes', 'subjects', 'classroom', 'studentsData',
            'sectorsForFilter', 'promotionsForFilter', 'classroomsForFilter'
        ));
    }

    /**
     * Afficher les notes d'un étudiant spécifique
     * GET /note/{student}  →  route('note.show')
     */
    public function show($studentId)
    {
        $student = Student::with([
            'recordings.notes.subject',
            'recordings.notes.ratio',
        ])->findOrFail($studentId);

        return view('dashboard.notes.show', compact('student'));
    }

    // =========================================================
    // EXPORT EXCEL
    // =========================================================

    /**
     * Page de sélection pour l'export Excel
     * GET /notes/export  →  route('export_view')
     */
    public function export_view()
    {
        $years = Year::orderBy('year', 'desc')->get();
        return view('dashboard.notes.export', compact('years'));
    }

    /**
     * Téléchargement du fichier Excel
     * GET /notes/export/download  →  route('notes.export')
     */
    public function export(Request $request)
    {
        $request->validate([
            'year_id'      => 'required|integer|exists:years,id',
            'classroom_id' => 'required|integer|exists:promotion_classrooms,id',
            'semester'     => 'required|integer|in:1,2',
        ]);

        $classroom = PromotionClassroom::findOrFail($request->classroom_id);
        $year      = Year::findOrFail($request->year_id);

        $filename = 'notes_' . $year->year . '_' . $classroom->name . '_S' . $request->semester . '.xlsx';

        return Excel::download(
            new NotesExport($request->year_id, $request->classroom_id),
            $filename
        );
    }

    // =========================================================
    // EXPORT PDF — FICHES & BULLETINS
    // =========================================================

    /**
     * Page de sélection pour les exports PDF (fiches + bulletins)
     * GET /bulletins  →  route('get.cards')
     */
    public function getcards(Request $request)
    {
        $years = Year::orderBy('year', 'desc')->get();
        return view('dashboard.notes.card', compact('years'));
    }

    /**
     * Génère le PDF (fiche de collation OU bulletins) selon export_type
     * GET /notes/export/fiche  →  route('notes.exportcard')
     */
    public function exportcard(Request $request)
    {
        $request->validate([
            'year_id'      => 'required|integer|exists:years,id',
            'classroom_id' => 'required|integer|exists:promotion_classrooms,id',
            'semester'     => 'required|integer|in:1,2',
            'export_type'  => 'required|in:fiche_collation,fiche_bulletin',
        ]);

        $year      = Year::findOrFail($request->year_id);
        $classroom = PromotionClassroom::with('promotionSector.sectorYear')->findOrFail($request->classroom_id);
        $semester  = (int) $request->semester;

        // ── Récupérer les matières (subjects) de cette classe ─────────────
        $ratios = Ratio::with('subject')
            ->where('year_id', $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->get();

        $subjects     = $ratios->pluck('subject')->unique('id')->sortBy('name')->values();
        $coefficients = $ratios->pluck('coefficient', 'subject_id');   // [subject_id => coef]

        // ── Récupérer les étudiants inscrits ──────────────────────────────
        $recordings = Recording::with('student')
            ->where('year_id', $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->get();

        $students = $recordings->pluck('student')->sortBy('name')->values();

        // ── Construire notesData[student_id][semester][subject_id] = moy20 ─
        $notesData = [];

        foreach ($recordings as $recording) {
            $sid = $recording->student_id;

            foreach ($subjects as $subject) {
                $note = Note::where('recording_id', $recording->id)
                    ->where('subject_id', $subject->id)
                    ->where('semester', $semester)
                    ->first();

                if ($note && $note->aptitude === 'dispensé') {
                    $notesData[$sid][$semester][$subject->id] = 'Dispensé(e)';
                } else {
                    $interros = $note
                        ? (is_array($note->interros) ? $note->interros : (json_decode($note->interros, true) ?? []))
                        : [];
                    $moy20 = $note
                        ? $this->calculateMoyenne20($interros, $note->devoir1, $note->devoir2)
                        : null;
                    $notesData[$sid][$semester][$subject->id] = $moy20;
                }
            }
        }

        // ── Calculer les moyennes générales pondérées par semestre ─────────
        $moyennesS1 = [];
        $moyennesS2 = [];

        foreach ($recordings as $recording) {
            $sid = $recording->student_id;

            foreach ([1, 2] as $sem) {
                $totalPond = 0;
                $totalCoef = 0;

                foreach ($subjects as $subject) {
                    $note = Note::where('recording_id', $recording->id)
                        ->where('subject_id', $subject->id)
                        ->where('semester', $sem)
                        ->first();

                    if ($note) {
                        $interros = is_array($note->interros)
                            ? $note->interros
                            : (json_decode($note->interros, true) ?? []);
                        $moy = $this->calculateMoyenne20($interros, $note->devoir1, $note->devoir2);
                        if ($moy !== null) {
                            $coef = $coefficients[$subject->id] ?? 1;
                            $totalPond += $moy * $coef;
                            $totalCoef += $coef;
                        }
                    }
                }

                $moy = $totalCoef > 0 ? $this->trunc2($totalPond / $totalCoef) : null;
                if ($sem === 1) $moyennesS1[$sid] = $moy;
                else           $moyennesS2[$sid] = $moy;
            }
        }

        // ── Moyennes annuelles : (S1 + S2) / 2 ───────────────────────────
        $moyennesAnnuelles = [];
        foreach ($students as $student) {
            $sid = $student->id;
            $m1  = $moyennesS1[$sid] ?? null;
            $m2  = $moyennesS2[$sid] ?? null;
            $moyennesAnnuelles[$sid] = ($m1 !== null && $m2 !== null)
                ? $this->trunc2(($m1 + $m2) / 2)
                : ($m1 ?? $m2);
        }

        // ── Calcul des rangs ──────────────────────────────────────────────
        $rangsS1      = $this->calculerRangs($moyennesS1);
        $rangsS2      = $this->calculerRangs($moyennesS2);
        $rangsAnnuels = $this->calculerRangs($moyennesAnnuelles);

        // ── Rangs par matière (pour les bulletins) ────────────────────────
        $subjectRanks = [];
        foreach ($subjects as $subject) {
            $moysParSubject = [];
            foreach ($recordings as $recording) {
                $note = Note::where('recording_id', $recording->id)
                    ->where('subject_id', $subject->id)
                    ->where('semester', $semester)
                    ->first();
                if ($note) {
                    $interros = is_array($note->interros)
                        ? $note->interros
                        : (json_decode($note->interros, true) ?? []);
                    $moy = $this->calculateMoyenne20($interros, $note->devoir1, $note->devoir2);
                    if ($moy !== null) $moysParSubject[$recording->student_id] = $moy;
                }
            }
            $subjectRanks[$subject->id] = $this->calculerRangs($moysParSubject);
        }

        $data = compact(
            'year', 'classroom', 'semester', 'students', 'subjects',
            'coefficients', 'notesData', 'moyennesS1', 'moyennesS2',
            'moyennesAnnuelles', 'rangsS1', 'rangsS2', 'rangsAnnuels',
            'subjectRanks'
        );
        $data['dateImpression'] = Carbon::now()->format('d/m/Y');

        if ($request->export_type === 'fiche_collation') {
            $pdf = Pdf::loadView('dashboard.notes.exports.fiche', $data)
                ->setPaper('a3', 'landscape');
            return $pdf->download("fiche_collation_{$classroom->name}_S{$semester}.pdf");
        }

        // fiche_bulletin
        $pdf = Pdf::loadView('dashboard.notes.exports.bulletin', $data)
            ->setPaper('a4', 'portrait');
        return $pdf->download("bulletins_{$classroom->name}_S{$semester}.pdf");
    }

    /**
     * Page de consultation des notes d'un étudiant
     * GET /notes/fetch  →  route('get.student.notes')
     */
    public function getStudentNotes(Request $request)
    {
        $years = Year::orderBy('year', 'desc')->get();
        return view('dashboard.notes.student_notes', compact('years'));
    }

    // =========================================================
    // API — Dropdowns hiérarchiques
    // =========================================================

    public function getSectorsByYear($yearId)
    {
        $sectors = SectorYear::with('sector')
            ->where('year_id', $yearId)
            ->get()
            ->map(fn($sy) => [
                'id'   => $sy->sector->id,
                'name' => $sy->sector->name_sector,
            ]);

        return response()->json($sectors);
    }

    public function getPromotionsByYearSector($yearId, $sectorId)
    {
        $sectorYear = SectorYear::where('year_id', $yearId)
            ->where('sector_id', $sectorId)
            ->first();

        if (!$sectorYear) return response()->json([]);

        $promotions = PromotionSector::where('sector_year_id', $sectorYear->id)
            ->get()
            ->map(fn($p) => [
                'id'   => $p->id,
                'name' => $p->promotion_sector,
            ]);

        return response()->json($promotions);
    }

    public function getClassesByPromotion($promotionId)
    {
        $classrooms = PromotionClassroom::where('promotion_sector_id', $promotionId)
            ->get()
            ->map(fn($c) => [
                'id'   => $c->id,
                'name' => $c->name,
            ]);

        return response()->json($classrooms);
    }

    // =========================================================
    // API — Matières + ratios pour une classe
    // =========================================================

    /**
     * POST /api/subjects-by-classroom
     */
    public function getSubjectsByClassroom(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|integer|exists:promotion_classrooms,id',
            'year_id'      => 'required|integer|exists:years,id',
        ]);

        $classroom = PromotionClassroom::findOrFail($request->classroom_id);

        $ratios = Ratio::with('subject')
            ->where('year_id', $request->year_id)
            ->where('promotion_sector_id', $classroom->promotion_sector_id)
            ->where('classroom_id', $request->classroom_id)
            ->get()
            ->map(fn($r) => [
                'ratio_id'     => $r->id,
                'subject_id'   => $r->subject_id,
                'subject_name' => $r->subject->name,
                'coefficient'  => $r->coefficient,
            ]);

        if ($ratios->isEmpty()) {
            return response()->json([
                'success'  => false,
                'message'  => 'Aucune matière trouvée pour cette classe.',
                'subjects' => [],
            ]);
        }

        return response()->json(['success' => true, 'subjects' => $ratios]);
    }

    // =========================================================
    // API — Étudiants avec leurs notes existantes
    // =========================================================

    /**
     * POST /api/students-with-notes
     */
    public function getStudentsWithNotes(Request $request)
    {
        $request->validate([
            'year_id'      => 'required|integer|exists:years,id',
            'classroom_id' => 'required|integer|exists:promotion_classrooms,id',
            'ratio_id'     => 'required|integer|exists:ratios,id',
            'semester'     => 'required|integer|in:1,2',
        ]);

        $ratio = Ratio::findOrFail($request->ratio_id);

        $recordings = Recording::with('student')
            ->where('year_id', $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->get();

        if ($recordings->isEmpty()) {
            return response()->json([
                'success'  => false,
                'message'  => 'Aucun étudiant inscrit dans cette classe.',
                'students' => [],
            ]);
        }

        $students = $recordings->map(function ($recording) use ($request, $ratio) {
            $note = Note::where('recording_id', $recording->id)
                ->where('subject_id', $ratio->subject_id)
                ->where('ratio_id', $request->ratio_id)
                ->where('semester', $request->semester)
                ->first();

            $interros    = $note
                ? (is_array($note->interros) ? $note->interros : (json_decode($note->interros, true) ?? []))
                : [];
            $devoir1     = $note?->devoir1;
            $devoir2     = $note?->devoir2;

            // Troncage (pas d'arrondi) pour la moyenne des interros
            $moyInterros = count($interros) > 0
                ? $this->trunc2(array_sum($interros) / count($interros))
                : 0;

            $moy20 = $this->calculateMoyenne20($interros, $devoir1, $devoir2);

            return [
                'id'           => $recording->student->id,
                'recording_id' => $recording->id,
                'name'         => $recording->student->name,
                'surname'      => $recording->student->surname,
                'is_disabled'  => $recording->student->aptitude === 'dispensé',
                'interros'     => $interros,
                'devoir1'      => $devoir1,
                'devoir2'      => $devoir2,
                'moy_interros' => $moyInterros,
                'moy_20'       => $moy20,
            ];
        })->sortBy('name')->values();

        return response()->json(['success' => true, 'students' => $students]);
    }

    // =========================================================
    // API — Sauvegarde groupée des notes
    // =========================================================

    /**
     * POST /api/notes/bulk
     */
    public function storeBulk(Request $request)
    {
        $request->validate([
            'year_id'              => 'required|integer|exists:years,id',
            'classroom_id'         => 'required|integer|exists:promotion_classrooms,id',
            'ratio_id'             => 'required|integer|exists:ratios,id',
            'semester'             => 'required|integer|in:1,2',
            'notes'                => 'required|array|min:1',
            'notes.*.recording_id' => 'required|integer|exists:recordings,id',
            'notes.*.interros'     => 'nullable|array',
            'notes.*.interros.*'   => 'nullable|numeric|min:0|max:20',
            'notes.*.devoir1'      => 'nullable|numeric|min:0|max:20',
            'notes.*.devoir2'      => 'nullable|numeric|min:0|max:20',
        ]);

        $ratio = Ratio::findOrFail($request->ratio_id);

        DB::beginTransaction();
        try {
            foreach ($request->notes as $noteData) {
                $interros = array_values(array_filter(
                    $noteData['interros'] ?? [],
                    fn($v) => $v !== null && $v !== ''
                ));

                Note::updateOrCreate(
                    [
                        'recording_id' => $noteData['recording_id'],
                        'subject_id'   => $ratio->subject_id,
                        'ratio_id'     => $request->ratio_id,
                        'semester'     => $request->semester,
                    ],
                    [
                        'interros' => count($interros) > 0 ? $interros : null,
                        'devoir1'  => isset($noteData['devoir1']) && $noteData['devoir1'] !== '' ? $noteData['devoir1'] : null,
                        'devoir2'  => isset($noteData['devoir2']) && $noteData['devoir2'] !== '' ? $noteData['devoir2'] : null,
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($request->notes) . ' note(s) enregistrée(s) avec succès.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur storeBulk notes : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement : ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================
    // Helpers
    // =========================================================

    /**
     * Formule : (Moy_interros + D1 + D2) / nb_composantes_présentes
     * Troncage à 2 décimales (pas d'arrondi)
     */
    public function calculateMoyenne20(array $interros, $devoir1, $devoir2): ?float
    {
        // Calcul précis de la moyenne des interros (valeur brute, pas tronquée)
        $moyInterros = count($interros) > 0
            ? array_sum($interros) / count($interros)
            : null;

        $d1 = ($devoir1 !== null && $devoir1 !== '') ? floatval($devoir1) : null;
        $d2 = ($devoir2 !== null && $devoir2 !== '') ? floatval($devoir2) : null;

        $composantes = [];
        if ($moyInterros !== null) $composantes[] = $moyInterros;
        if ($d1          !== null) $composantes[] = $d1;
        if ($d2          !== null) $composantes[] = $d2;

        if (count($composantes) === 0) return null;

        // Troncage final (pas d'arrondi)
        return $this->trunc2(array_sum($composantes) / count($composantes));
    }

    /**
     * Calcule les rangs à partir d'un tableau [student_id => moyenne]
     * Retourne [student_id => rang] — ex-æquo partagent le même rang
     */
    private function calculerRangs(array $moyennes): array
    {
        arsort($moyennes);
        $rangs = [];
        $rang  = 1;
        $prev  = null;
        $tie   = 0;

        foreach ($moyennes as $sid => $moy) {
            if ($moy === null) {
                $rangs[$sid] = '-';
                continue;
            }
            if ($moy === $prev) {
                $rangs[$sid] = $rang - $tie - 1;
                $tie++;
            } else {
                $rang += $tie;
                $tie  = 0;
                $rangs[$sid] = $rang;
                $prev  = $moy;
            }
            $rang++;
        }

        return $rangs;
    }
}
