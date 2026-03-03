<?php

namespace App\Http\Controllers;

use App\Models\ClassSubjectTeacher;
use App\Models\Note;
use App\Models\PrincipalClassTeacher;
use App\Models\PromotionClassroom;
use App\Models\PromotionSector;
use App\Models\Ratio;
use App\Models\Recording;
use App\Models\SectorYear;
use App\Models\Student;
use App\Models\Year;
use App\Exports\NotesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class NoteController extends Controller
{
    // =========================================================
    // HELPER — Troncage à 2 décimales (sans arrondi)
    // =========================================================
    private function trunc2(?float $val): ?float
    {
        if ($val === null) return null;
        return floor($val * 100) / 100;
    }

    // =========================================================
    // HELPER — Permissions
    // =========================================================

    /**
     * Retourne true si l'utilisateur peut MODIFIER une note déjà existante.
     * Enseignant     → NON  (saisie initiale uniquement, champ par champ)
     * Admin/Censeur/Proviseur → OUI
     */
    private function canModifyExistingNote(): bool
    {
        return !Auth::user()->isEnseignant();
    }

    // =========================================================
    // VUES PRINCIPALES
    // =========================================================

    /**
     * GET /note/create — Page de saisie des notes.
     * Enseignant : filtre les années où il a des affectations.
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->isEnseignant()) {
            $yearIds = ClassSubjectTeacher::where('user_id', $user->id)
                ->pluck('year_id')->unique();
            $years = Year::whereIn('id', $yearIds)->orderBy('year', 'desc')->get();
        } else {
            $years = Year::orderBy('year', 'desc')->get();
        }

        return view('dashboard.notes.create', compact('years'));
    }

    /**
     * GET /note — Visualisation des notes.
     * Enseignant non-PP → refusé.
     * PP → restreint à ses classes.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isEnseignant()) {
            $isPP = $user->principalClasses()->exists();
            if (!$isPP) {
                return redirect()->route('note.create')
                    ->with('warning', 'Accès réservé aux professeurs principaux.');
            }
        }

        $years               = Year::orderBy('year', 'desc')->get();
        $notes               = collect();
        $subjects            = collect();
        $classroom           = null;
        $studentsData        = collect();
        $sectorsForFilter    = collect();
        $promotionsForFilter = collect();
        $classroomsForFilter = collect();

        if ($request->filled('year_id')) {
            $sectorsForFilter = SectorYear::with('sector')
                ->where('year_id', $request->year_id)
                ->get()->pluck('sector')->filter();
        }

        if ($request->filled(['year_id', 'sector_id'])) {
            $sectorYear = SectorYear::where('year_id', $request->year_id)
                ->where('sector_id', $request->sector_id)->first();
            $promotionsForFilter = $sectorYear
                ? PromotionSector::where('sector_year_id', $sectorYear->id)->get()
                : collect();
        }

        if ($request->filled('promotion_id')) {
            $query = PromotionClassroom::where('promotion_sector_id', $request->promotion_id);
            if ($user->isEnseignant()) {
                $ppClassroomIds = $user->principalClasses()
                    ->where('year_id', $request->year_id)
                    ->pluck('classroom_id')->toArray();
                $query->whereIn('id', $ppClassroomIds);
            }
            $classroomsForFilter = $query->get();
        }

        if ($request->filled(['year_id', 'sector_id', 'promotion_id', 'classroom_id', 'semester'])) {
            if ($user->isEnseignant()) {
                $hasAccess = $user->principalClasses()
                    ->where('classroom_id', $request->classroom_id)
                    ->where('year_id', $request->year_id)
                    ->exists();
                if (!$hasAccess) {
                    abort(403, 'Vous n\'êtes pas le professeur principal de cette classe.');
                }
            }

            $classroom = PromotionClassroom::with([
                'promotionSector.sectorYear.sector',
                'promotionSector.sectorYear.year',
            ])->find($request->classroom_id);

            if ($classroom) {
                $rawNotes = Note::with(['recording.student', 'subject', 'ratio'])
                    ->whereHas('recording', fn($q) => $q
                        ->where('classroom_id', $request->classroom_id)
                        ->where('year_id', $request->year_id))
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
                $notes    = $rawNotes->groupBy('recording_id');

                foreach ($notes as $recordingId => $studentNotes) {
                    $totalMoyPonderee = 0;
                    $totalCoef        = 0;
                    $notesParMatiere  = [];

                    foreach ($subjects as $subjectId => $subjectInfo) {
                        $note        = $studentNotes->where('subject_id', $subjectId)->first();
                        $moyInterros = null;
                        $moy20       = null;

                        if ($note) {
                            $interros    = is_array($note->interros)
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
                            'note'        => $note,
                            'moy_interros'=> $moyInterros,
                            'moy_20'      => $moy20,
                        ];
                    }

                    $moyenneGenerale = $totalCoef > 0
                        ? $this->trunc2($totalMoyPonderee / $totalCoef)
                        : null;

                    $recording = $studentNotes->first()->recording;
                    $studentsData->push([
                        'student'          => $recording->student,
                        'notes_par_matiere'=> $notesParMatiere,
                        'moyenne_generale' => $moyenneGenerale,
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

    public function show($studentId)
    {
        $student = Student::with([
            'recordings.notes.subject',
            'recordings.notes.ratio',
        ])->findOrFail($studentId);

        return view('dashboard.notes.show', compact('student'));
    }

    // =========================================================
    // EXPORT
    // =========================================================

    public function export_view()
    {
        $user = Auth::user();
        if ($user->isEnseignant() && !$user->principalClasses()->exists()) {
            abort(403, 'Accès réservé aux professeurs principaux.');
        }
        $years = Year::orderBy('year', 'desc')->get();
        return view('dashboard.notes.export', compact('years'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'year_id'      => 'required|integer|exists:years,id',
            'classroom_id' => 'required|integer|exists:promotion_classrooms,id',
            'semester'     => 'required|integer|in:1,2',
        ]);

        $user = Auth::user();
        if ($user->isEnseignant()) {
            $hasAccess = $user->principalClasses()
                ->where('classroom_id', $request->classroom_id)
                ->where('year_id', $request->year_id)
                ->exists();
            if (!$hasAccess) abort(403);
        }

        $classroom = PromotionClassroom::findOrFail($request->classroom_id);
        $year      = Year::findOrFail($request->year_id);

        return Excel::download(
            new NotesExport($request->year_id, $request->classroom_id),
            'notes_' . $year->year . '_' . $classroom->name . '_S' . $request->semester . '.xlsx'
        );
    }

    public function getcards(Request $request)
    {
        $user = Auth::user();
        if ($user->isEnseignant() && !$user->principalClasses()->exists()) abort(403);
        $years = Year::orderBy('year', 'desc')->get();
        return view('dashboard.notes.card', compact('years'));
    }

    public function exportcard(Request $request)
    {
        $request->validate([
            'year_id'      => 'required|integer|exists:years,id',
            'classroom_id' => 'required|integer|exists:promotion_classrooms,id',
            'semester'     => 'required|integer|in:1,2',
            'export_type'  => 'required|in:fiche_collation,fiche_bulletin',
        ]);

        $user = Auth::user();
        if ($user->isEnseignant()) {
            if (!$user->principalClasses()
                    ->where('classroom_id', $request->classroom_id)
                    ->where('year_id', $request->year_id)
                    ->exists()) {
                abort(403);
            }
        }

        $year      = Year::findOrFail($request->year_id);
        $classroom = PromotionClassroom::with('promotionSector.sectorYear')
            ->findOrFail($request->classroom_id);
        $semester  = (int) $request->semester;

        $ratios       = Ratio::with('subject')
            ->where('year_id', $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->get();
        $subjects     = $ratios->pluck('subject')->unique('id')->sortBy('name')->values();
        $coefficients = $ratios->pluck('coefficient', 'subject_id');
        $recordings   = Recording::with('student')
            ->where('year_id', $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->get();
        $students = $recordings->pluck('student')->sortBy('name')->values();

        $notesData = [];
        foreach ($recordings as $recording) {
            $sid = $recording->student_id;
            foreach ($subjects as $subject) {
                $note     = Note::where('recording_id', $recording->id)
                    ->where('subject_id', $subject->id)
                    ->where('semester', $semester)->first();
                $interros = $note
                    ? (is_array($note->interros) ? $note->interros : (json_decode($note->interros, true) ?? []))
                    : [];
                $notesData[$sid][$semester][$subject->id] = $note
                    ? $this->calculateMoyenne20($interros, $note->devoir1, $note->devoir2)
                    : null;
            }
        }

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
                        ->where('semester', $sem)->first();
                    if ($note) {
                        $interros = is_array($note->interros)
                            ? $note->interros
                            : (json_decode($note->interros, true) ?? []);
                        $moy = $this->calculateMoyenne20($interros, $note->devoir1, $note->devoir2);
                        if ($moy !== null) {
                            $coef       = $coefficients[$subject->id] ?? 1;
                            $totalPond += $moy * $coef;
                            $totalCoef += $coef;
                        }
                    }
                }
                $moy = $totalCoef > 0 ? $this->trunc2($totalPond / $totalCoef) : null;
                if ($sem === 1) $moyennesS1[$sid] = $moy;
                else            $moyennesS2[$sid] = $moy;
            }
        }

        $moyennesAnnuelles = [];
        foreach ($students as $student) {
            $sid = $student->id;
            $m1  = $moyennesS1[$sid] ?? null;
            $m2  = $moyennesS2[$sid] ?? null;
            $moyennesAnnuelles[$sid] = ($m1 !== null && $m2 !== null)
                ? $this->trunc2(($m1 + $m2) / 2)
                : ($m1 ?? $m2);
        }

        $rangsS1      = $this->calculerRangs($moyennesS1);
        $rangsS2      = $this->calculerRangs($moyennesS2);
        $rangsAnnuels = $this->calculerRangs($moyennesAnnuelles);

        $subjectRanks = [];
        foreach ($subjects as $subject) {
            $moysParSubject = [];
            foreach ($recordings as $recording) {
                $note = Note::where('recording_id', $recording->id)
                    ->where('subject_id', $subject->id)
                    ->where('semester', $semester)->first();
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
            'year', 'classroom', 'semester', 'students', 'subjects', 'coefficients',
            'notesData', 'moyennesS1', 'moyennesS2', 'moyennesAnnuelles',
            'rangsS1', 'rangsS2', 'rangsAnnuels', 'subjectRanks'
        );
        $data['dateImpression'] = Carbon::now()->format('d/m/Y');

        if ($request->export_type === 'fiche_collation') {
            return Pdf::loadView('dashboard.notes.exports.fiche', $data)
                ->setPaper('a3', 'landscape')
                ->download("fiche_collation_{$classroom->name}_S{$semester}.pdf");
        }

        return Pdf::loadView('dashboard.notes.exports.bulletin', $data)
            ->setPaper('a4', 'portrait')
            ->download("bulletins_{$classroom->name}_S{$semester}.pdf");
    }

    public function getStudentNotes(Request $request)
    {
        $years = Year::orderBy('year', 'desc')->get();
        return view('dashboard.notes.student_notes', compact('years'));
    }

    // =========================================================
    // API — Dropdowns filtrés enseignant
    // =========================================================

    public function getSectorsByYear($yearId)
    {
        $user  = Auth::user();
        $query = SectorYear::with('sector')->where('year_id', $yearId);

        if ($user->isEnseignant()) {
            $classroomIds = ClassSubjectTeacher::where('user_id', $user->id)
                ->where('year_id', $yearId)->pluck('classroom_id');
            $sectorIds = PromotionClassroom::whereIn('id', $classroomIds)->pluck('sector_id');
            $query->whereIn('sector_id', $sectorIds);
        }

        return response()->json(
            $query->get()->map(fn($sy) => [
                'id'   => $sy->sector->id,
                'name' => $sy->sector->name_sector,
            ])
        );
    }

    public function getPromotionsByYearSector($yearId, $sectorId)
    {
        $user       = Auth::user();
        $sectorYear = SectorYear::where('year_id', $yearId)
            ->where('sector_id', $sectorId)->first();

        if (!$sectorYear) return response()->json([]);

        $query = PromotionSector::where('sector_year_id', $sectorYear->id);

        if ($user->isEnseignant()) {
            $classroomIds = ClassSubjectTeacher::where('user_id', $user->id)
                ->where('year_id', $yearId)->pluck('classroom_id');
            $promotionIds = PromotionClassroom::whereIn('id', $classroomIds)
                ->pluck('promotion_sector_id');
            $query->whereIn('id', $promotionIds);
        }

        return response()->json(
            $query->get()->map(fn($p) => ['id' => $p->id, 'name' => $p->promotion_sector])
        );
    }

    public function getClassesByPromotion($promotionId)
    {
        $user   = Auth::user();
        $yearId = request('year_id');
        $query  = PromotionClassroom::where('promotion_sector_id', $promotionId);

        if ($user->isEnseignant() && $yearId) {
            $classroomIds = ClassSubjectTeacher::where('user_id', $user->id)
                ->where('year_id', $yearId)->pluck('classroom_id');
            $query->whereIn('id', $classroomIds);
        }

        return response()->json(
            $query->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->name])
        );
    }

    // =========================================================
    // API — Matières (filtrées enseignant)
    // =========================================================

    public function getSubjectsByClassroom(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|integer|exists:promotion_classrooms,id',
            'year_id'      => 'required|integer|exists:years,id',
        ]);

        $user      = Auth::user();
        $classroom = PromotionClassroom::findOrFail($request->classroom_id);

        $query = Ratio::with('subject')
            ->where('year_id',             $request->year_id)
            ->where('promotion_sector_id', $classroom->promotion_sector_id)
            ->where('classroom_id',        $request->classroom_id);

        if ($user->isEnseignant()) {
            $subjectIds = ClassSubjectTeacher::where('user_id', $user->id)
                ->where('classroom_id', $request->classroom_id)
                ->where('year_id',      $request->year_id)
                ->pluck('subject_id');
            $query->whereIn('subject_id', $subjectIds);
        }

        $ratios = $query->get()->map(fn($r) => [
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
    // API — Étudiants avec notes + fields_readonly par champ
    // =========================================================

    public function getStudentsWithNotes(Request $request)
    {
        $request->validate([
            'year_id'      => 'required|integer|exists:years,id',
            'classroom_id' => 'required|integer|exists:promotion_classrooms,id',
            'ratio_id'     => 'required|integer|exists:ratios,id',
            'semester'     => 'required|integer|in:1,2',
        ]);

        $user  = Auth::user();
        $ratio = Ratio::findOrFail($request->ratio_id);

        if ($user->isEnseignant()) {
            $hasAccess = ClassSubjectTeacher::where('user_id', $user->id)
                ->where('classroom_id', $request->classroom_id)
                ->where('subject_id',   $ratio->subject_id)
                ->where('year_id',      $request->year_id)
                ->exists();

            if (!$hasAccess) {
                Log::warning('getStudentsWithNotes : accès refusé', [
                    'user_id'      => $user->id,
                    'classroom_id' => $request->classroom_id,
                    'subject_id'   => $ratio->subject_id,
                ]);
                return response()->json([
                    'success'  => false,
                    'message'  => 'Accès non autorisé à cette classe/matière.',
                    'students' => [],
                ]);
            }
        }

        $recordings = Recording::with('student')
            ->where('year_id',      $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->get();

        if ($recordings->isEmpty()) {
            return response()->json([
                'success'  => false,
                'message'  => 'Aucun étudiant inscrit dans cette classe.',
                'students' => [],
            ]);
        }

        $isLocked = Note::whereHas('recording', fn($q) => $q
                ->where('classroom_id', $request->classroom_id)
                ->where('year_id',      $request->year_id))
            ->where('subject_id', $ratio->subject_id)
            ->where('semester',   $request->semester)
            ->where('is_locked',  true)
            ->exists();

        $canModify = $this->canModifyExistingNote(); // admin/censeur/proviseur = true, enseignant = false

        $students = $recordings->map(function ($recording) use ($request, $ratio, $canModify) {
            $note = Note::where('recording_id', $recording->id)
                ->where('subject_id', $ratio->subject_id)
                ->where('ratio_id',   $request->ratio_id)
                ->where('semester',   $request->semester)
                ->first();

            $noteExists = $note !== null;

            $interros = $note
                ? (is_array($note->interros) ? $note->interros : (json_decode($note->interros, true) ?? []))
                : [];

            $devoir1 = $note?->devoir1;
            $devoir2 = $note?->devoir2;

            $moyInterros = count($interros) > 0
                ? $this->trunc2(array_sum($interros) / count($interros))
                : null;

            $moy20 = $this->calculateMoyenne20($interros, $devoir1, $devoir2);

            // ─────────────────────────────────────────────────────────
            // Verrouillage CHAMP PAR CHAMP
            // Enseignant : seuls les champs déjà enregistrés en BDD
            //              sont en readonly. Les champs vides restent éditables.
            // Admin/Censeur/Proviseur : tout toujours éditable.
            // ─────────────────────────────────────────────────────────
            $fieldsReadonly = [
                'interro_0' => false,
                'interro_1' => false,
                'interro_2' => false,
                'devoir1'   => false,
                'devoir2'   => false,
            ];

            if (!$canModify && $note) {
                $savedInterros = is_array($note->interros)
                    ? $note->interros
                    : (json_decode($note->interros, true) ?? []);

                for ($i = 0; $i < 3; $i++) {
                    $fieldsReadonly["interro_{$i}"] = isset($savedInterros[$i])
                        && $savedInterros[$i] !== null
                        && $savedInterros[$i] !== '';
                }
                $fieldsReadonly['devoir1'] = $note->devoir1 !== null && $note->devoir1 !== '';
                $fieldsReadonly['devoir2'] = $note->devoir2 !== null && $note->devoir2 !== '';
            }

            // field_readonly global = vrai seulement si TOUS les champs sont verrouillés
            $allLocked   = $noteExists && !$canModify
                && array_sum(array_values($fieldsReadonly)) === count($fieldsReadonly);

            return [
                'id'              => $recording->student->id,
                'recording_id'    => $recording->id,
                'name'            => $recording->student->name,
                'surname'         => $recording->student->surname,
                'is_disabled'     => $recording->student->aptitude === 'dispensé',
                'interros'        => $interros,
                'devoir1'         => $devoir1,
                'devoir2'         => $devoir2,
                'moy_interros'    => $moyInterros,
                'moy_20'          => $moy20,
                'note_exists'     => $noteExists,
                'field_readonly'  => $allLocked,    // compat ancienne logique
                'fields_readonly' => $fieldsReadonly, // NEW : par champ
            ];
        })->sortBy('name')->values();

        return response()->json([
            'success'     => true,
            'students'    => $students,
            'is_locked'   => $isLocked,
            'can_edit'    => !($isLocked && $user->isEnseignant()),
            'can_modify'  => $canModify,
        ]);
    }

    // =========================================================
    // API — Sauvegarde bulk
    // L'enseignant ne peut envoyer que les champs NON encore en BDD.
    // Le backend re-vérifie champ par champ et fusionne.
    // =========================================================

    public function storeBulk(Request $request)
    {
        Log::info('storeBulk — requête reçue', [
            'user_id'      => Auth::id(),
            'classroom_id' => $request->classroom_id,
            'ratio_id'     => $request->ratio_id,
            'semester'     => $request->semester,
            'nb_notes'     => is_array($request->notes) ? count($request->notes) : 'non-array',
            'content_type' => $request->header('Content-Type'),
        ]);

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

        Log::info('storeBulk — validation OK', ['nb_notes' => count($request->notes)]);

        $user  = Auth::user();
        $ratio = Ratio::findOrFail($request->ratio_id);

        if ($user->isEnseignant()) {
            $hasAccess = ClassSubjectTeacher::where('user_id', $user->id)
                ->where('classroom_id', $request->classroom_id)
                ->where('subject_id',   $ratio->subject_id)
                ->where('year_id',      $request->year_id)
                ->exists();

            if (!$hasAccess) {
                Log::warning('storeBulk — accès refusé (enseignant non affecté)', [
                    'user_id'      => $user->id,
                    'classroom_id' => $request->classroom_id,
                    'subject_id'   => $ratio->subject_id,
                ]);
                return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
            }

            $lockedNote = Note::whereHas('recording', fn($q) => $q
                    ->where('classroom_id', $request->classroom_id)
                    ->where('year_id',      $request->year_id))
                ->where('subject_id', $ratio->subject_id)
                ->where('semester',   $request->semester)
                ->where('is_locked',  true)
                ->exists();

            if ($lockedNote) {
                Log::warning('storeBulk — refusé car notes verrouillées', [
                    'user_id'    => $user->id,
                    'subject_id' => $ratio->subject_id,
                    'semester'   => $request->semester,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Ces notes sont verrouillées. Seul le censeur peut les modifier.',
                ], 403);
            }
        }

        DB::beginTransaction();
        try {
            foreach ($request->notes as $noteData) {
                $newInterros = array_values(
                    array_filter(
                        $noteData['interros'] ?? [],
                        fn($v) => $v !== null && $v !== '' && is_numeric($v)
                    )
                );

                $newD1 = isset($noteData['devoir1']) && $noteData['devoir1'] !== ''
                    ? floatval($noteData['devoir1']) : null;
                $newD2 = isset($noteData['devoir2']) && $noteData['devoir2'] !== ''
                    ? floatval($noteData['devoir2']) : null;

                // ─────────────────────────────────────────────────────
                // Pour un enseignant : on protège les champs déjà en BDD.
                // On ne remplace QUE les champs qui étaient vides.
                // ─────────────────────────────────────────────────────
                if ($user->isEnseignant()) {
                    $existing = Note::where('recording_id', $noteData['recording_id'])
                        ->where('subject_id', $ratio->subject_id)
                        ->where('ratio_id',   $request->ratio_id)
                        ->where('semester',   $request->semester)
                        ->first();

                    if ($existing) {
                        $savedInterros = is_array($existing->interros)
                            ? $existing->interros
                            : (json_decode($existing->interros, true) ?? []);

                        // Fusion : on garde les valeurs BDD pour les index déjà remplis
                        $mergedInterros = $savedInterros;
                        foreach ($newInterros as $idx => $val) {
                            // N'écrit que si la case BDD est vide à cet index
                            if (!isset($mergedInterros[$idx])
                                || $mergedInterros[$idx] === null
                                || $mergedInterros[$idx] === '') {
                                $mergedInterros[$idx] = $val;
                            }
                        }
                        // Réindexation propre
                        $mergedInterros = array_values(array_filter(
                            $mergedInterros,
                            fn($v) => $v !== null && $v !== '' && is_numeric($v)
                        ));

                        $mergedD1 = ($existing->devoir1 !== null && $existing->devoir1 !== '')
                            ? $existing->devoir1 : $newD1;
                        $mergedD2 = ($existing->devoir2 !== null && $existing->devoir2 !== '')
                            ? $existing->devoir2 : $newD2;

                        $existing->update([
                            'interros' => count($mergedInterros) > 0 ? $mergedInterros : null,
                            'devoir1'  => $mergedD1,
                            'devoir2'  => $mergedD2,
                        ]);
                        continue; // passe à l'élève suivant
                    }
                }

                // Admin/Censeur/Proviseur OU note inexistante : updateOrCreate standard
                Note::updateOrCreate(
                    [
                        'recording_id' => $noteData['recording_id'],
                        'subject_id'   => $ratio->subject_id,
                        'ratio_id'     => $request->ratio_id,
                        'semester'     => $request->semester,
                    ],
                    [
                        'interros' => count($newInterros) > 0 ? $newInterros : null,
                        'devoir1'  => $newD1,
                        'devoir2'  => $newD2,
                    ]
                );
            }

            DB::commit();

            Log::info('storeBulk — succès', [
                'user_id'  => Auth::id(),
                'nb_saved' => count($request->notes),
            ]);

            return response()->json([
                'success' => true,
                'message' => count($request->notes) . ' note(s) enregistrée(s) avec succès.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('storeBulk — erreur', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/notes/toggle-lock — Verrouiller/déverrouiller (non-enseignants seulement).
     */
    public function toggleLock(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|integer|exists:promotion_classrooms,id',
            'year_id'      => 'required|integer|exists:years,id',
            'subject_id'   => 'required|integer|exists:subjects,id',
            'semester'     => 'required|integer|in:1,2',
            'lock'         => 'required|boolean',
        ]);

        if (Auth::user()->isEnseignant()) {
            return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
        }

        $count = Note::whereHas('recording', fn($q) => $q
                ->where('classroom_id', $request->classroom_id)
                ->where('year_id',      $request->year_id))
            ->where('subject_id', $request->subject_id)
            ->where('semester',   $request->semester)
            ->update(['is_locked' => $request->lock]);

        $action = $request->lock ? 'verrouillées' : 'déverrouillées';
        return response()->json(['success' => true, 'message' => "{$count} note(s) {$action}."]);
    }

    // =========================================================
    // Helpers privés
    // =========================================================

    public function calculateMoyenne20(array $interros, $devoir1, $devoir2): ?float
    {
        $moyInterros = count($interros) > 0
            ? array_sum($interros) / count($interros)
            : null;

        $d1 = ($devoir1 !== null && $devoir1 !== '') ? floatval($devoir1) : null;
        $d2 = ($devoir2 !== null && $devoir2 !== '') ? floatval($devoir2) : null;

        $composantes = [];
        if ($moyInterros !== null) $composantes[] = $moyInterros;
        if ($d1 !== null)          $composantes[] = $d1;
        if ($d2 !== null)          $composantes[] = $d2;

        if (count($composantes) === 0) return null;
        return $this->trunc2(array_sum($composantes) / count($composantes));
    }

    private function calculerRangs(array $moyennes): array
    {
        arsort($moyennes);

        $rangs    = [];
        $rang     = 1;
        $prevMoy  = null;
        $prevRang = 1;

        foreach ($moyennes as $sid => $moy) {
            if ($moy === null) {
                $rangs[$sid] = '-';
                continue;
            }
            if ($moy === $prevMoy) {
                $rangs[$sid] = $prevRang;
            } else {
                $rangs[$sid] = $rang;
                $prevRang    = $rang;
                $prevMoy     = $moy;
            }
            $rang++;
        }

        return $rangs;
    }
}
