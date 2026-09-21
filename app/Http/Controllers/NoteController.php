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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

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
    private function canModifyExistingNote(): bool
    {
        return !Auth::user()->isEnseignant();
    }

    // =========================================================
    // HELPER — Année active (la plus récente)
    // =========================================================
    private function getActiveYear(): ?Year
    {
        // Si votre modèle Year a is_active, décommentez :
        // return Year::where('is_active', true)->first() ?? Year::orderBy('year', 'desc')->first();
        return Year::orderBy('year', 'desc')->first();
    }

    // =========================================================
    // VUES PRINCIPALES
    // =========================================================

    public function create()
    {
        $user       = Auth::user();
        $activeYear = $this->getActiveYear();

        if ($user->isEnseignant()) {
            $yearIds = ClassSubjectTeacher::where('user_id', $user->id)
                ->pluck('year_id')->unique();
            $years = Year::whereIn('id', $yearIds)->orderBy('year', 'desc')->get();
        } else {
            $years = Year::orderBy('year', 'desc')->get();
        }

        return view('dashboard.notes.create', compact('years', 'activeYear'));
    }

 public function index(Request $request)
{
    $user = Auth::user();

    // Vérification des droits pour les enseignants (doit être Professeur Principal)
    if ($user->isEnseignant()) {
        $isPP = $user->principalClasses()->exists();
        if (!$isPP) {
            return redirect()->route('note.create')
                ->with('warning', 'Accès réservé aux professeurs principaux.');
        }
    }

    $years = Year::orderBy('year', 'desc')->get();
    $activeYear = $this->getActiveYear();
    $notes = collect();
    $subjects = collect();
    $classroom = null;
    $studentsData = collect();
    $classroomsForFilter = collect();

    // 1. Chargement des classes disponibles pour le filtre
    if ($request->filled('year_id')) {
        $query = PromotionClassroom::with([
            'promotionSector.sectorYear.sector',
            'promotionSector.sectorYear.year',
        ])->whereHas('promotionSector.sectorYear', function ($q) use ($request) {
            $q->where('year_id', $request->year_id);
        });

        if ($user->isEnseignant()) {
            $ppClassroomIds = $user->principalClasses()
                ->where('year_id', $request->year_id)
                ->pluck('classroom_id')
                ->toArray();
            $query->whereIn('id', $ppClassroomIds);
        }

        $classroomsForFilter = $query->get()->map(function ($c) {
            $sector = $c->promotionSector->sectorYear->sector->name_sector ?? '';
            $promotion = $c->promotionSector->promotion_sector ?? '';
            $c->display_name = trim($promotion . ' · ' . $c->name, ' ·');
            $c->meta = $sector . ($promotion ? ' — ' . $promotion : '');
            return $c;
        })->sortBy('display_name')->values();
    }

    // 2. Chargement des notes et calcul des statistiques par élève
    if ($request->filled(['year_id', 'classroom_id', 'semester'])) {

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
                    ->where('year_id', $request->year_id)
                )
                ->where('semester', $request->semester)
                ->get();

            // Construction de la liste des matières et leurs coefficients
            foreach ($rawNotes as $note) {
                if (!$subjects->has($note->subject_id)) {
                    $subjects->put($note->subject_id, [
                        'name' => $note->subject->name,
                        'coefficient' => $note->ratio->coefficient,
                    ]);
                }
            }
            $subjects = $subjects->sortBy('name');
            $notes = $rawNotes->groupBy('recording_id');

            // 3. Traitement élève par élève
            foreach ($notes as $recordingId => $studentNotes) {
                $totalMoyPonderee = 0;
                $totalCoef = 0;
                $notesParMatiere = [];

                foreach ($subjects as $subjectId => $subjectInfo) {
                    $note = $studentNotes->where('subject_id', $subjectId)->first();
                    $moyInterros = null;
                    $moy20 = null;

                    if ($note) {
                        $interros = is_array($note->interros) ? $note->interros : (json_decode($note->interros, true) ?? []);
                        $moyInterros = count($interros) > 0 ? $this->trunc2(array_sum($interros) / count($interros)) : null;
                        $moy20 = $this->calculateMoyenne20($interros, $note->devoir1, $note->devoir2);

                        if ($moy20 !== null) {
                            $totalMoyPonderee += $moy20 * $subjectInfo['coefficient'];
                            $totalCoef += $subjectInfo['coefficient'];
                        }
                    }

                    $notesParMatiere[$subjectId] = [
                        'note' => $note,
                        'moy_interros' => $moyInterros,
                        'moy_20' => $moy20,
                    ];
                }

                $moyenneGenerale = $totalCoef > 0 ? $this->trunc2($totalMoyPonderee / $totalCoef) : null;
                $recording = $studentNotes->first()->recording;

                // 4. Calcul de la moyenne annuelle (Uniquement si Semestre 2 est sélectionné)
                $moyenneAnnuelle = null;
                if ((int)$request->semester === 2) {
                    $notesS1 = Note::with('ratio')->where('recording_id', $recordingId)->where('semester', 1)->get();
                    $totalPondereS1 = 0;
                    $totalCoefS1 = 0;

                    foreach ($notesS1 as $nS1) {
                        $interrosS1 = is_array($nS1->interros) ? $nS1->interros : (json_decode($nS1->interros, true) ?? []);
                        $moyS1 = $this->calculateMoyenne20($interrosS1, $nS1->devoir1, $nS1->devoir2);

                        if ($moyS1 !== null) {
                            $coefS1 = $nS1->ratio->coefficient ?? 1;
                            $totalPondereS1 += $moyS1 * $coefS1;
                            $totalCoefS1 += $coefS1;
                        }
                    }

                    $moyS1Calc = $totalCoefS1 > 0 ? $this->trunc2($totalPondereS1 / $totalCoefS1) : null;

                    // Formule annuelle : (MoyS2 * 2 + MoyS1) / 3
                    if ($moyS1Calc !== null && $moyenneGenerale !== null) {
                        $moyenneAnnuelle = $this->trunc2((($moyenneGenerale * 2) + $moyS1Calc) / 3);
                    } elseif ($moyenneGenerale !== null) {
                        $moyenneAnnuelle = $moyenneGenerale;
                    } elseif ($moyS1Calc !== null) {
                        $moyenneAnnuelle = $moyS1Calc;
                    }
                }

                // 5. Ajout des données à la collection (avec les nouveaux champs)
                $studentsData->push([
                    'student' => $recording->student,
                    'notes_par_matiere' => $notesParMatiere,
                    'moyenne_generale' => $moyenneGenerale,
                    'total_pondere' => $totalMoyPonderee,    // <-- NOUVEAU
                    'total_coef' => $totalCoef,              // <-- NOUVEAU
                    'moyenne_annuelle' => $moyenneAnnuelle,  // <-- NOUVEAU (pour les stats globales)
                ]);
            }

            // Tri alphabétique des élèves
            $studentsData = $studentsData->sortBy(fn($s) => $s['student']->name)->values();
        }
    }

    return view('dashboard.notes.list', compact(
        'years',
        'activeYear',
        'notes',
        'subjects',
        'classroom',
        'studentsData',
        'classroomsForFilter'
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
    // EXPORT — Un onglet par matière · Matricule forcé en texte
    // =========================================================

    public function export_view()
    {
        $user = Auth::user();
        if ($user->isEnseignant() && !$user->principalClasses()->exists()) {
            abort(403, 'Accès réservé aux professeurs principaux.');
        }
        $years      = Year::orderBy('year', 'desc')->get();
        $activeYear = $this->getActiveYear();
        return view('dashboard.notes.export', compact('years', 'activeYear'));
    }

    /**
     * Export XLSX — un onglet par matière.
     * Colonnes : Matricule | Nom | Prénom(s) | Moy. Interros | Devoir 1 | Devoir 2
     */
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
        $semester  = (int) $request->semester;

        $ratios = Ratio::with('subject')
            ->where('year_id',      $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->get()
            ->sortBy('subject.name')
            ->values();

        $recordings = Recording::with('student')
            ->where('year_id',      $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->get()
            ->sortBy('student.name')
            ->values();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach ($ratios as $sheetIndex => $ratio) {

            $subjectName = $ratio->subject->name ?? 'Matière';
            $sheetTitle  = mb_substr(
                preg_replace('/[\/\\\?\*\[\]\:]/', '', $subjectName),
                0, 31
            );

            $sheet = $spreadsheet->createSheet($sheetIndex);
            $sheet->setTitle($sheetTitle);

            // ── En-têtes document ────────────────────────────
            $sheet->setCellValue('A1', $year->year . ' — Semestre ' . $semester);
            $sheet->setCellValue('A2', $classroom->name . ' · ' . $subjectName . '  (Coeff. ' . $ratio->coefficient . ')');
            $sheet->mergeCells('A1:F1');
            $sheet->mergeCells('A2:F2');

            $sheet->getStyle('A1')->applyFromArray([
                'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1e3a5f']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'dbeafe']],
            ]);
            $sheet->getStyle('A2')->applyFromArray([
                'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '374151']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eff6ff']],
            ]);
            $sheet->getRowDimension(1)->setRowHeight(22);
            $sheet->getRowDimension(2)->setRowHeight(18);

            // ── En-têtes colonnes ────────────────────────────
            $headers = ['Matricule', 'Nom', 'Prénom(s)', 'Moy. Interros', 'Devoir 1', 'Devoir 2'];
            foreach ($headers as $col => $label) {
                $sheet->setCellValue(chr(65 + $col) . '3', $label);
            }
            $sheet->getStyle('A3:F3')->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => 'ffffff']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3b5bdb']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'c7d2fe']]],
            ]);
            $sheet->getRowDimension(3)->setRowHeight(18);

            // ── Données ──────────────────────────────────────
            $row = 4;
            foreach ($recordings as $idx => $rec) {
                $note = Note::where('recording_id', $rec->id)
                    ->where('subject_id', $ratio->subject_id)
                    ->where('semester',   $semester)
                    ->first();

                $interrosRaw = $note?->interros ?? null;
                $interrosArr = is_array($interrosRaw)
                    ? $interrosRaw
                    : (json_decode($interrosRaw ?? '[]', true) ?? []);
                $interrosArr = array_values(array_filter($interrosArr, fn($v) => $v !== null && is_numeric($v)));

                $moyInterros = count($interrosArr) > 0
                    ? $this->trunc2(array_sum($interrosArr) / count($interrosArr))
                    : null;

                $d1 = ($note && $note->devoir1 !== null) ? (float) $note->devoir1 : null;
                $d2 = ($note && $note->devoir2 !== null) ? (float) $note->devoir2 : null;

                // Matricule forcé en texte (évite la notation scientifique)
                $sheet->setCellValueExplicit('A' . $row, (string)($rec->student->matricule ?? ''), DataType::TYPE_STRING);

                $sheet->setCellValue('B' . $row, $rec->student->name    ?? '');
                $sheet->setCellValue('C' . $row, $rec->student->surname ?? '');

                if ($moyInterros !== null) $sheet->setCellValue('D' . $row, $moyInterros);
                if ($d1 !== null)          $sheet->setCellValue('E' . $row, $d1);
                if ($d2 !== null)          $sheet->setCellValue('F' . $row, $d2);

                $rowBg = ($idx % 2 === 0) ? 'f8fafc' : 'ffffff';
                $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'e2e8f0']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("D{$row}:F{$row}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$row}:F{$row}")
                    ->getNumberFormat()->setFormatCode('0.00');
                $sheet->getRowDimension($row)->setRowHeight(16);
                $row++;
            }

            // Bordure extérieure
            $lastRow = $row - 1;
            if ($lastRow >= 4) {
                $sheet->getStyle("A3:F{$lastRow}")->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '3b5bdb']]],
                ]);
            }

            $sheet->getColumnDimension('A')->setWidth(22);
            $sheet->getColumnDimension('B')->setWidth(24);
            $sheet->getColumnDimension('C')->setWidth(24);
            $sheet->getColumnDimension('D')->setWidth(16);
            $sheet->getColumnDimension('E')->setWidth(14);
            $sheet->getColumnDimension('F')->setWidth(14);
            $sheet->freezePane('A4');
            $sheet->setAutoFilter('A3:F3');
        }

        if ($spreadsheet->getSheetCount() > 0) {
            $spreadsheet->setActiveSheetIndex(0);
        }

        $spreadsheet->getProperties()
            ->setTitle('Notes ' . $year->year . ' — ' . $classroom->name . ' S' . $semester)
            ->setCreator('NotesManager v3')
            ->setDescription('Export notes — Lycée Technique de Bohicon');

        $writer   = new XlsxWriter($spreadsheet);
        $filename = 'notes_'
            . preg_replace('/[^a-z0-9]/i', '_', $year->year) . '_'
            . preg_replace('/[^a-z0-9]/i', '_', $classroom->name) . '_S'
            . $semester . '.xlsx';

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // =========================================================
    // IMPORT — CSV / XLS / XLSX
    // =========================================================

    /**
     * GET /notes/import
     * Vue du formulaire d'import.
     */
    public function import_view()
    {
        $user = Auth::user();
        if ($user->isEnseignant() && !$user->principalClasses()->exists()) {
            abort(403, 'Accès réservé aux professeurs principaux.');
        }
        $years      = Year::orderBy('year', 'desc')->get();
        $activeYear = $this->getActiveYear();
        return view('dashboard.notes.import', compact('years', 'activeYear'));
    }

    /**
     * POST /notes/import/process
     *
     * Format attendu du fichier (même structure que l'export) :
     *   - Une feuille par matière (le nom de la feuille = nom de la matière)
     *   - Pour CSV (une seule matière) : le nom de la matière est passé en paramètre
     *   - Ligne 1 : titre (ignorée)
     *   - Ligne 2 : sous-titre (ignorée)
     *   - Ligne 3 : en-têtes colonnes (ignorée)
     *   - Lignes 4+ : Matricule | Nom | Prénom(s) | Moy. Interros | Devoir 1 | Devoir 2
     *
     * Règles de fusion :
     *   - Si l'enseignant importe : seules les cellules VIDES en base sont remplies
     *   - Si admin/censeur importe : updateOrCreate complet
     */
    public function import(Request $request)
    {
        $request->validate([
            'file'         => 'required|file|mimes:csv,txt,xls,xlsx|max:5120',
            'year_id'      => 'required|integer|exists:years,id',
            'classroom_id' => 'required|integer|exists:promotion_classrooms,id',
            'semester'     => 'required|integer|in:1,2',
            // subject_id obligatoire uniquement pour CSV (une seule feuille)
            'subject_id'   => 'nullable|integer|exists:subjects,id',
        ]);

        $user      = Auth::user();
        $semester  = (int) $request->semester;
        $yearId    = (int) $request->year_id;
        $classroomId = (int) $request->classroom_id;

        // Vérification accès enseignant
        if ($user->isEnseignant()) {
            $hasAccess = $user->principalClasses()
                ->where('classroom_id', $classroomId)
                ->where('year_id',      $yearId)
                ->exists();
            if (!$hasAccess) {
                return back()->with('error', 'Accès non autorisé à cette classe.');
            }
        }

        $file      = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        // ── Lecture du fichier ───────────────────────────────
        try {
            // Pour les CSV, forcer le reader CSV de PhpSpreadsheet
            if ($extension === 'csv' || $extension === 'txt') {
                $reader = IOFactory::createReader('Csv');
                $reader->setDelimiter($this->detectCsvDelimiter($file->getRealPath()));
                $reader->setEnclosure('"');
                $reader->setSheetIndex(0);
            } else {
                $reader = IOFactory::createReaderForFile($file->getRealPath());
            }

            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());

        } catch (\Exception $e) {
            Log::error('Import notes — lecture fichier', ['error' => $e->getMessage()]);
            return back()->with('error', 'Impossible de lire le fichier : ' . $e->getMessage());
        }

        // ── Précharger les ratios de la classe ───────────────
        // Map : nom_matière_normalisé => Ratio
        $ratios = Ratio::with('subject')
            ->where('year_id',      $yearId)
            ->where('classroom_id', $classroomId)
            ->get();

        $ratiosByName = $ratios->keyBy(fn($r) => $this->normalizeString($r->subject->name ?? ''));
        $ratiosById   = $ratios->keyBy('subject_id');

        // ── Précharger les recordings de la classe ───────────
        // Map : matricule_normalisé => Recording
        $recordings = Recording::with('student')
            ->where('year_id',      $yearId)
            ->where('classroom_id', $classroomId)
            ->get()
            ->keyBy(fn($rec) => $this->normalizeString((string)($rec->student->matricule ?? '')));

        if ($recordings->isEmpty()) {
            return back()->with('error', 'Aucun élève inscrit dans cette classe pour cette année.');
        }

        $totalSaved  = 0;
        $totalErrors = [];
        $canModify   = $this->canModifyExistingNote();

        // ── Boucle sur les feuilles ──────────────────────────
        $sheetCount = $spreadsheet->getSheetCount();

        for ($sheetIdx = 0; $sheetIdx < $sheetCount; $sheetIdx++) {

            $sheet = $spreadsheet->getSheet($sheetIdx);

            // Déterminer la matière pour cette feuille
            $ratio = null;

            if ($extension === 'csv' || $extension === 'txt') {
                // CSV : matière passée explicitement en paramètre
                if ($request->filled('subject_id')) {
                    $ratio = $ratiosById->get((int) $request->subject_id);
                }
                if (!$ratio) {
                    $totalErrors[] = 'CSV : aucune matière sélectionnée ou matière introuvable dans cette classe.';
                    continue;
                }
            } else {
                // XLSX/XLS : le nom de l'onglet = nom de la matière
                $sheetName    = $sheet->getTitle();
                $normalizedSN = $this->normalizeString($sheetName);
                $ratio        = $ratiosByName->get($normalizedSN);

                if (!$ratio) {
                    // Tentative de correspondance partielle
                    $ratio = $ratiosByName->first(fn($r, $k) => str_starts_with($k, mb_substr($normalizedSN, 0, 5)));
                }

                if (!$ratio) {
                    $totalErrors[] = "Feuille « {$sheetName} » : matière introuvable dans la classe.";
                    continue;
                }
            }

            // Vérification droit enseignant sur cette matière
            if ($user->isEnseignant()) {
                $hasSubjectAccess = ClassSubjectTeacher::where('user_id',      $user->id)
                    ->where('classroom_id', $classroomId)
                    ->where('subject_id',   $ratio->subject_id)
                    ->where('year_id',      $yearId)
                    ->exists();
                if (!$hasSubjectAccess) {
                    $totalErrors[] = "Matière « {$ratio->subject->name} » : accès non autorisé.";
                    continue;
                }

                // Vérifier verrouillage
                $isLocked = Note::whereHas('recording', fn($q) => $q
                        ->where('classroom_id', $classroomId)
                        ->where('year_id',      $yearId))
                    ->where('subject_id', $ratio->subject_id)
                    ->where('semester',   $semester)
                    ->where('is_locked',  true)
                    ->exists();

                if ($isLocked) {
                    $totalErrors[] = "Matière « {$ratio->subject->name} » : notes verrouillées.";
                    continue;
                }
            }

            // ── Lecture des lignes de données ────────────────
            // Détecter la ligne de départ : chercher la ligne dont
            // la 1re colonne contient "Matricule" (en-tête) ou sauter les 3 premières lignes
            $highestRow = $sheet->getHighestDataRow();
            $dataStartRow = $this->findDataStartRow($sheet, $highestRow);

            DB::beginTransaction();
            try {
                for ($rowNum = $dataStartRow; $rowNum <= $highestRow; $rowNum++) {

                    // Lire les 6 colonnes A→F
                    $matricule   = trim((string) $sheet->getCellByColumnAndRow(1, $rowNum)->getValue());
                    $moyInterros = $this->parseNote($sheet->getCellByColumnAndRow(4, $rowNum)->getValue());
                    $devoir1     = $this->parseNote($sheet->getCellByColumnAndRow(5, $rowNum)->getValue());
                    $devoir2     = $this->parseNote($sheet->getCellByColumnAndRow(6, $rowNum)->getValue());

                    // Ignorer les lignes sans matricule ou sans aucune note
                    if ($matricule === '' || $matricule === null) continue;
                    if ($moyInterros === null && $devoir1 === null && $devoir2 === null) continue;

                    // Retrouver le recording par matricule
                    $normalizedMat = $this->normalizeString($matricule);
                    $recording     = $recordings->get($normalizedMat);

                    if (!$recording) {
                        $totalErrors[] = "Ligne {$rowNum} (matière {$ratio->subject->name}) : matricule « {$matricule} » introuvable.";
                        continue;
                    }

                    // Construire le tableau interros à partir de la moyenne
                    // Note : le fichier exporté contient la MOYENNE des interros, pas les interros individuelles.
                    // On stocke donc la moyenne comme une interro unique [moyInterros]
                    // pour que calculateMoyenne20 donne le bon résultat.
                    $interrosToSave = ($moyInterros !== null) ? [$moyInterros] : null;

                    // Logique de fusion selon le rôle
                    $existing = Note::where('recording_id', $recording->id)
                        ->where('subject_id', $ratio->subject_id)
                        ->where('ratio_id',   $ratio->id)
                        ->where('semester',   $semester)
                        ->first();

                    if ($existing && !$canModify) {
                        // Enseignant : ne remplir que les champs vides
                        $savedInterros = is_array($existing->interros)
                            ? $existing->interros
                            : (json_decode($existing->interros ?? '[]', true) ?? []);

                        $mergedInterros = (count($savedInterros) === 0 && $interrosToSave !== null)
                            ? $interrosToSave
                            : $savedInterros;

                        $mergedD1 = ($existing->devoir1 !== null) ? $existing->devoir1 : $devoir1;
                        $mergedD2 = ($existing->devoir2 !== null) ? $existing->devoir2 : $devoir2;

                        $existing->update([
                            'interros' => count($mergedInterros) > 0 ? $mergedInterros : null,
                            'devoir1'  => $mergedD1,
                            'devoir2'  => $mergedD2,
                        ]);
                    } else {
                        // Admin/censeur OU nouvelle note : updateOrCreate
                        Note::updateOrCreate(
                            [
                                'recording_id' => $recording->id,
                                'subject_id'   => $ratio->subject_id,
                                'ratio_id'     => $ratio->id,
                                'semester'     => $semester,
                            ],
                            [
                                'interros' => $interrosToSave,
                                'devoir1'  => $devoir1,
                                'devoir2'  => $devoir2,
                            ]
                        );
                    }

                    $totalSaved++;
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Import notes — sauvegarde', [
                    'sheet'   => $sheet->getTitle(),
                    'message' => $e->getMessage(),
                ]);
                $totalErrors[] = "Feuille « {$sheet->getTitle()} » : erreur de sauvegarde — " . $e->getMessage();
            }
        }

        // ── Réponse ──────────────────────────────────────────
        $msg = "{$totalSaved} note(s) importée(s) avec succès.";
        if (!empty($totalErrors)) {
            $errList = implode(' | ', array_slice($totalErrors, 0, 5));
            $more    = count($totalErrors) > 5 ? ' (…et ' . (count($totalErrors) - 5) . ' autre(s))' : '';
            return back()
                ->with('success', $msg)
                ->with('warning', 'Avertissements : ' . $errList . $more);
        }

        return back()->with('success', $msg);
    }

    // =========================================================
    // HELPER IMPORT — Détection délimiteur CSV
    // =========================================================
    private function detectCsvDelimiter(string $filePath): string
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) return ';';
        $line = fgets($handle);
        fclose($handle);
        if (!$line) return ';';

        $counts = [
            ';'  => substr_count($line, ';'),
            ','  => substr_count($line, ','),
            "\t" => substr_count($line, "\t"),
        ];
        arsort($counts);
        return array_key_first($counts) ?: ';';
    }

    // =========================================================
    // HELPER IMPORT — Trouver la première ligne de données
    // =========================================================
    /**
     * Cherche la ligne d'en-tête (contenant "Matricule") et retourne
     * la ligne suivante comme première ligne de données.
     * Fallback : ligne 4 (structure de l'export standard).
     */
    private function findDataStartRow($sheet, int $highestRow): int
    {
        $maxSearch = min($highestRow, 10);
        for ($r = 1; $r <= $maxSearch; $r++) {
            $cellVal = strtolower(trim((string) $sheet->getCellByColumnAndRow(1, $r)->getValue()));
            if ($cellVal === 'matricule') {
                return $r + 1;
            }
        }
        // Fallback : structure export standard (3 lignes d'en-tête)
        return 4;
    }

    // =========================================================
    // HELPER IMPORT — Parser une valeur de note
    // =========================================================
    private function parseNote($value): ?float
    {
        if ($value === null || $value === '') return null;
        // Remplacer la virgule décimale par un point
        $v = str_replace(',', '.', (string) $value);
        if (!is_numeric($v)) return null;
        $f = (float) $v;
        if ($f < 0 || $f > 20) return null;
        return $this->trunc2($f);
    }

    // =========================================================
    // HELPER IMPORT — Normaliser une chaîne pour comparaison
    // =========================================================
    private function normalizeString(string $str): string
    {
        // Minuscules, sans accents, sans espaces superflus
        $str = mb_strtolower(trim($str));
        $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
        $str = preg_replace('/\s+/', ' ', $str);
        return $str;
    }

    // =========================================================
    // BULLETINS / FICHE
    // =========================================================

    public function getcards(Request $request)
    {
        $user = Auth::user();
        if ($user->isEnseignant() && !$user->principalClasses()->exists()) abort(403);
        $years      = Year::orderBy('year', 'desc')->get();
        $activeYear = $this->getActiveYear();
        return view('dashboard.notes.card', compact('years', 'activeYear'));
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
    // API — Année active
    // =========================================================

    public function getActiveYearApi()
    {
        $year = $this->getActiveYear();
        if (!$year) {
            return response()->json(['success' => false, 'year' => null]);
        }
        return response()->json([
            'success' => true,
            'year'    => ['id' => $year->id, 'year' => $year->year],
        ]);
    }

    // =========================================================
    // API — Filières pour saisie/export/import
    // =========================================================

    public function getSectorsForCreate($yearId)
    {
        $user  = Auth::user();
        $query = SectorYear::with('sector')->where('year_id', $yearId);

        if ($user->isEnseignant()) {
            $classroomIds = ClassSubjectTeacher::where('user_id', $user->id)
                ->where('year_id', $yearId)
                ->pluck('classroom_id');
            $sectorYearIds = PromotionClassroom::whereIn('promotion_classrooms.id', $classroomIds)
                ->join('promotion_sectors', 'promotion_classrooms.promotion_sector_id', '=', 'promotion_sectors.id')
                ->pluck('promotion_sectors.sector_year_id')
                ->unique();
            $query->whereIn('id', $sectorYearIds);
        }

        $sectors = $query->get()->map(fn($sy) => [
            'id'   => $sy->sector->id,
            'name' => $sy->sector->name_sector,
        ])->sortBy('name')->values();

        return response()->json([
            'success' => true,
            'sectors' => $sectors,
            'auto'    => $sectors->count() === 1,
        ]);
    }

    // =========================================================
    // API — Promotions
    // =========================================================

    public function getPromotionsForCreate($yearId, $sectorId)
    {
        $user       = Auth::user();
        $sectorYear = SectorYear::where('year_id', $yearId)
            ->where('sector_id', $sectorId)->first();

        if (!$sectorYear) {
            return response()->json(['success' => false, 'promotions' => [], 'auto' => false]);
        }

        $query = PromotionSector::where('sector_year_id', $sectorYear->id);

        if ($user->isEnseignant()) {
            $classroomIds = ClassSubjectTeacher::where('user_id', $user->id)
                ->where('year_id', $yearId)->pluck('classroom_id');
            $promotionIds = PromotionClassroom::whereIn('id', $classroomIds)
                ->pluck('promotion_sector_id')->unique();
            $query->whereIn('id', $promotionIds);
        }

        $promotions = $query->get()->map(fn($p) => [
            'id'   => $p->id,
            'name' => $p->promotion_sector,
        ])->sortBy('name')->values();

        return response()->json([
            'success'    => true,
            'promotions' => $promotions,
            'auto'       => $promotions->count() === 1,
        ]);
    }

    // =========================================================
    // API — Classes
    // =========================================================

    public function getClassroomsForCreate($promotionId)
    {
        $user   = Auth::user();
        $yearId = request('year_id');
        $query  = PromotionClassroom::where('promotion_sector_id', $promotionId);

        if ($user->isEnseignant() && $yearId) {
            $classroomIds = ClassSubjectTeacher::where('user_id', $user->id)
                ->where('year_id', $yearId)->pluck('classroom_id');
            $query->whereIn('id', $classroomIds);
        }

        $classrooms = $query->get()->map(fn($c) => [
            'id'   => $c->id,
            'name' => $c->name,
        ])->sortBy('name')->values();

        return response()->json([
            'success'    => true,
            'classrooms' => $classrooms,
            'auto'       => $classrooms->count() === 1,
        ]);
    }

    // =========================================================
    // API — Classrooms by year (liste/consultation)
    // =========================================================

    public function getClassroomsByYear($yearId)
    {
        $user = Auth::user();

        $query = PromotionClassroom::with([
            'promotionSector.sectorYear.sector',
        ])->whereHas('promotionSector.sectorYear', function ($q) use ($yearId) {
            $q->where('year_id', $yearId);
        });

        if ($user->isEnseignant()) {
            $classroomIds = ClassSubjectTeacher::where('user_id', $user->id)
                ->where('year_id', $yearId)
                ->pluck('classroom_id');
            $query->whereIn('id', $classroomIds);
        }

        $classrooms = $query->get()->map(function ($c) {
            $sector    = $c->promotionSector->sectorYear->sector->name_sector ?? '';
            $promotion = $c->promotionSector->promotion_sector ?? '';
            return [
                'id'           => $c->id,
                'name'         => $c->name,
                'display_name' => trim($promotion . ' · ' . $c->name, ' · '),
                'meta'         => $sector . ($promotion ? ' — ' . $promotion : ''),
            ];
        })->sortBy('display_name')->values();

        return response()->json($classrooms);
    }

    // =========================================================
    // API — Dropdowns legacy
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
    // API — Matières filtrées
    // =========================================================

    public function getSubjectsByClassroom(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|integer|exists:promotion_classrooms,id',
            'year_id'      => 'required|integer|exists:years,id',
            'semester'     => 'nullable|integer|in:1,2',
        ]);

        $user      = Auth::user();
        $classroom = PromotionClassroom::findOrFail($request->classroom_id);
        $semester  = $request->semester ? (int) $request->semester : null;

        $query = Ratio::with('subject')
            ->where('year_id',             $request->year_id)
            ->where('promotion_sector_id', $classroom->promotion_sector_id)
            ->where('classroom_id',        $request->classroom_id);

        if ($semester !== null) {
            $query->where(function ($q) use ($semester) {
                $q->whereNull('semester')->orWhere('semester', $semester);
            });
        }

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
            'semester'     => $r->semester,
        ]);

        if ($ratios->isEmpty()) {
            return response()->json([
                'success'  => false,
                'message'  => 'Aucune matière disponible pour ce semestre.',
                'subjects' => [],
            ]);
        }

        return response()->json(['success' => true, 'subjects' => $ratios]);
    }

    // =========================================================
    // API — Étudiants avec notes
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

        $canModify = $this->canModifyExistingNote();

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

            $allLocked = $noteExists && !$canModify
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
                'field_readonly'  => $allLocked,
                'fields_readonly' => $fieldsReadonly,
            ];
        })->sortBy('name')->values();

        return response()->json([
            'success'    => true,
            'students'   => $students,
            'is_locked'  => $isLocked,
            'can_edit'   => !($isLocked && $user->isEnseignant()),
            'can_modify' => $canModify,
        ]);
    }

    // =========================================================
    // API — Sauvegarde bulk
    // =========================================================

    public function storeBulk(Request $request)
    {
        Log::info('storeBulk — requête reçue', [
            'user_id'      => Auth::id(),
            'classroom_id' => $request->classroom_id,
            'ratio_id'     => $request->ratio_id,
            'semester'     => $request->semester,
            'nb_notes'     => is_array($request->notes) ? count($request->notes) : 'non-array',
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

        $user  = Auth::user();
        $ratio = Ratio::findOrFail($request->ratio_id);

        if ($user->isEnseignant()) {
            $hasAccess = ClassSubjectTeacher::where('user_id', $user->id)
                ->where('classroom_id', $request->classroom_id)
                ->where('subject_id',   $ratio->subject_id)
                ->where('year_id',      $request->year_id)
                ->exists();

            if (!$hasAccess) {
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

                        $mergedInterros = $savedInterros;
                        foreach ($newInterros as $idx => $val) {
                            if (!isset($mergedInterros[$idx])
                                || $mergedInterros[$idx] === null
                                || $mergedInterros[$idx] === '') {
                                $mergedInterros[$idx] = $val;
                            }
                        }
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
                        continue;
                    }
                }

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

    // =========================================================
    // API — Verrouillage
    // =========================================================

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
    // Helpers privés — Calculs
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
