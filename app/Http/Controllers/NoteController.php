<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Year;
use App\Models\Ratio;
use App\Models\Recording;
use Illuminate\Http\Request;
use App\Models\PromotionClassroom;
use Illuminate\Support\Facades\DB;
use App\Exports\NotesExport;
use App\Models\Subject;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use PDF;


class NoteController extends Controller
{
    public function create()
    {
        $years =Year::where('status', true)->get();
        return view('dashboard.notes.create', compact('years'));
    }

     public function index()
    {
        $years =Year::where('status', true)->get();
        return view('dashboard.notes.list', compact('years'));
    }
    public function getcards()
    {
        $years =Year::where('status', true)->get();
        return view('dashboard.notes.card', compact('years'));
    }

    public function getStudents($classroomId, $yearId)
    {
        $recordings = Recording::with('student')
            ->where('classroom_id', $classroomId)
            ->where('year_id', $yearId)
            ->get();

        return response()->json($recordings->map(function ($rec) {
            return [
                'recording_id' => $rec->id,
                'name' => $rec->student->name,
                'surname' => $rec->student->surname,
                'aptitude' => $rec->student->aptitude,
            ];
        }));
    }


    public function getSubjectsWithRatios($classroomId, $yearId)
    {
        $ratios = Ratio::with('subject')
            ->where('classroom_id', $classroomId)
            ->where('year_id', $yearId)
            ->get();

        Log::info('Ratios chargés', ['ratios' => $ratios]);

        return response()->json($ratios);
    }

    public function getExistingNotes(Request $request)
    {
        $notes = Note::where('semester', $request->semester)
            ->where('ratio_id', $request->ratio_id)
            ->where('subject_id', $request->subject_id)
            ->whereIn('recording_id', $request->recording_ids)
            ->get();

        $type = $request->type;

        return $notes->mapWithKeys(function ($note) use ($type) {
            return [$note->recording_id => $type === 'devoir1' ? $note->devoir1 : ($type === 'devoir2' ? $note->devoir2 : null)];
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'year_id' => 'required|exists:years,id',
            'classroom_id' => 'required|exists:promotion_classrooms,id',
            'subject' => 'required|exists:ratios,id',
            'semester' => 'required|in:1,2',
            'type' => 'required|in:interro,devoir1,devoir2',
            'students' => 'required|array',
            'grades' => 'required|array',
            'students.*' => 'required|exists:recordings,id',
            'grades.*' => 'nullable|numeric|min:0|max:20'
        ]);

        try {
            DB::beginTransaction();
            $ratio = Ratio::with('subject')->findOrFail($request->subject);
            foreach ($request->students as $index => $recordingId) {
                $noteValue = $request->grades[$index];

                if (is_null($noteValue) || $noteValue === '') continue;

                $note = Note::firstOrNew([
                    'recording_id' => $recordingId,
                    'subject_id' => $ratio->subject_id,
                    'ratio_id' => $ratio->id,
                    'semester' => $request->semester,
                ]);

                if ($request->type === 'interro') {
                    $interros = is_array($note->interros) ? $note->interros : [];
                    if (count($interros) < 5) {
                        $interros[] = round($noteValue, 2);
                        $note->interros = $interros;
                    }
                } elseif ($request->type === 'devoir1') {
                    $note->devoir1 = round($noteValue, 2);
                } elseif ($request->type === 'devoir2') {
                    $note->devoir2 = round($noteValue, 2);
                }

                $note->save();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Les notes ont été enregistrées avec succès.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        $request->validate([
            'year_id' => 'required|exists:years,id',
            'classroom_id' => 'required|exists:promotion_classrooms,id',
            'semester' => 'required|in:1,2',
        ]);

        try {
            // Récupération du nom de la classe
            $classroom = PromotionClassroom::findOrFail($request->classroom_id);
            $className = str_replace(' ', '_', $classroom->name); // Optionnel : remplace les espaces par des underscores

            // Génération du nom de fichier
            $filename = "Notes_Classe_{$className}_Semestre{$request->semester}.xlsx";

            return Excel::download(
                new NotesExport($request->year_id, $request->classroom_id, $request->semester),
                $filename
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'export : ' . $e->getMessage());
        }
    }

    public function export_view()
    {
        $years = Year::where('status', true)->get();
        return view('dashboard.notes.export', compact('years'));
    }

 public function byClassAndSemester($classroom_id, $year_id, $semester)
{
    $students = Recording::with(['student', 'notes' => function ($q) use ($semester) {
        $q->where('semester', $semester)->with('subject');
    }])->where('classroom_id', $classroom_id)
      ->where('year_id', $year_id)
      ->get();

    return $students->map(function ($rec) {
        return [
            'id' => $rec->id,
            'name' => $rec->student->name,
            'surname' => $rec->student->surname,
            'matricule' => $rec->student->matricule,
            'notes' => $rec->notes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'subject' => $note->subject->name,
                    'interros' => $note->interros ?? [],
                    'devoir1' => $note->devoir1,
                    'devoir2' => $note->devoir2,
                ];
            }),
        ];
    });
}

public function update(Request $request, Note $note)
{
    $note->update([
        'interros' => $request->input('interros'),
        'devoir1' => $request->input('devoir1'),
        'devoir2' => $request->input('devoir2'),
    ]);
    return response()->json(['message' => 'Note mise à jour avec succès']);
}
public function exportcard(Request $request)
{
    $request->validate([
        'year_id' => 'required|exists:years,id',
        'classroom_id' => 'required|exists:promotion_classrooms,id',
        'semester' => 'required|in:1,2',
        'export_type' => 'required|in:fiche_collation,fiche_bulletin',
    ]);

    $yearId = $request->year_id;
    $classroomId = $request->classroom_id;
    $semester = $request->semester;
    $exportType = $request->export_type;

    $recordings = Recording::with('student')
        ->where('classroom_id', $classroomId)
        ->where('year_id', $yearId)
        ->get();

    $students = $recordings->pluck('student')->filter();
    $subjects = Subject::whereHas('ratios', fn($q) =>
        $q->where('classroom_id', $classroomId)->where('year_id', $yearId)
    )->orderBy('name')->get();

    $coefficients = [];
    foreach ($subjects as $subject) {
        $coef = Ratio::where([
            'subject_id' => $subject->id,
            'classroom_id' => $classroomId,
            'year_id' => $yearId,
        ])->value('coefficient') ?? 1;
        $coefficients[$subject->id] = $coef;
    }

    $notesData = [];
    $moyennesS1 = [];
    $moyennesS2 = [];
    $moyennesAnnuelles = [];

    foreach ($students as $student) {
        $recording = $student->recordings->first();
        if (!$recording) continue;

        $semestreData = [1 => 0, 2 => 0];
        $totalCoef = [1 => 0, 2 => 0];

        foreach ([1, 2] as $sem) {
            foreach ($subjects as $subject) {
                if (strtoupper($subject->name) === 'EPS' && $student->aptitude !== 'Apte') {
                    $notesData[$student->id][$sem][$subject->id] = 'Dispensé(e)';
                    continue;
                }

                $note = Note::where([
                    'recording_id' => $recording->id,
                    'subject_id' => $subject->id,
                    'semester' => $sem,
                ])->first();

                $interros = $note?->interros ?? [];
                $nb = count($interros);
                $moyInterros = $nb > 0 ? array_sum($interros) / $nb : null;

                $d1 = $note?->devoir1;
                $d2 = $note?->devoir2;

                $moyenne = match (true) {
                    $moyInterros === null && $d1 && $d2 => ($d1 + $d2) / 2,
                    $moyInterros === null && $d1 => $d1,
                    $moyInterros === null && $d2 => $d2,
                    $moyInterros === null => 0,
                    default => collect([$moyInterros, $d1, $d2])->filter()->avg(),
                };

                $moy = round($moyenne, 2);
                $notesData[$student->id][$sem][$subject->id] = $moy;

                $coef = $coefficients[$subject->id];
                $semestreData[$sem] += $moy * $coef;
                $totalCoef[$sem] += $coef;
            }
        }

        $m1 = $totalCoef[1] ? round($semestreData[1] / $totalCoef[1], 2) : 0;
        $m2 = $totalCoef[2] ? round($semestreData[2] / $totalCoef[2], 2) : 0;

        if ($semester == 1) {
            $moyennesS1[$student->id] = $m1;
        } else {
            $moyennesS1[$student->id] = $m1;
            $moyennesS2[$student->id] = $m2;
            $moyennesAnnuelles[$student->id] = round(($m1 + $m2) / 2, 2);
        }
    }

    // Rangs
    $rangsS1 = $this->calculerRangs($moyennesS1);
    $rangsS2 = $semester == 2 ? $this->calculerRangs($moyennesS2) : [];
    $rangsAnnuels = $semester == 2 ? $this->calculerRangs($moyennesAnnuelles) : [];

    $year = Year::findOrFail($yearId);
    $classroom = PromotionClassroom::findOrFail($classroomId);

    $view = $exportType === 'fiche_collation'
        ? 'dashboard.notes.exports.fiche'
        : 'dashboard.notes.exports.bulletin';

    // Déterminer l'orientation en fonction du type d'export
    $orientation = $exportType === 'fiche_collation' ? 'landscape' : 'portrait';

    $pdf = PDF::loadView($view, [
        'year' => $year,
        'classroom' => $classroom,
        'students' => $students,
        'subjects' => $subjects,
        'coefficients' => $coefficients,
        'notesData' => $notesData,
        'semester' => $semester,
        'moyennesS1' => $moyennesS1,
        'moyennesS2' => $moyennesS2,
        'moyennesAnnuelles' => $moyennesAnnuelles,
        'rangsS1' => $rangsS1,
        'rangsS2' => $rangsS2,
        'rangsAnnuels' => $rangsAnnuels,
    ])->setPaper('a4', $orientation); // Utilisation de la variable d'orientation

    $filename = $exportType === 'fiche_collation'
        ? "fiche_de_collation_{$semester}_{$year->year}_{$classroom->name}.pdf"
        : "bulletin_notes_{$semester}_{$year->year}_{$classroom->name}.pdf";

    return response()->streamDownload(
        fn() => print($pdf->stream()),
        $filename,
        ['Content-Type' => 'application/pdf']
    );
}
private function calculerRangs(array $moyennes): array
{
    arsort($moyennes);
    $rangs = [];
    $rang = 1;
    $prevMoy = null;
    $count = 0;

    foreach ($moyennes as $id => $moy) {
        $count++;
        if ($moy === $prevMoy) {
            $rangs[$id] = $rang;
        } else {
            $rang = $count;
            $rangs[$id] = $rang;
            $prevMoy = $moy;
        }
    }

    return $rangs;
}

}

